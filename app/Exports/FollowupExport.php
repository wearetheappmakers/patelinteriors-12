<?php
namespace App\Exports;
use App\StockChild;
use App\Lead;
use App\Category;
use App\Employee;
use App\ProductCategory;
use App\LeadSource;
use App\LeadAssign;
use App\SourceName;
use App\LeadFollowUp;
use App\FollowupLog;
use App\User;
use App\Client;
use App\Company;
use Auth;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
class FollowupExport implements FromCollection,WithHeadings
{
    private $param;
    public function __construct($param)
    {
        $this->param = $param;
    }
    public function headings(): array
    {
        if(Auth::guard('user')->check())
        {
            if(Auth::user()->role == 2)
            {
                return [
                    'client',
                    'employee',
                    'category',
                    'company',
                    'title',
                    'lead_date',
                    'source',
                    'sourcename',
                    'followup_date'
                ];
            }else
            {
                return [
                    'client',
                    'branch',
                    'employee',
                    'category',
                    'company',
                    'title',
                    'lead_date',
                    'source',
                    'sourcename',
                    'followup_date'
                ];
            }
        }
        else
        {
            return [
                'client',
                'category',
                'company',
                'title',
                'lead_date',
                'source',
                'sourcename',
                'followup_date'
            ];
        }
    }
    public function collection()
    {

        if(Auth::guard('employee')->check())
        {
            $data = LeadAssign::get();
            $array = [];
            foreach($data as $dt)
            {
                $ex_arr = explode(',', $dt->employee_id);
                if(in_array(Auth::user()->id,$ex_arr))
                {
                    array_push($array,$dt->lead_id);
                }
            }
            $data = Lead::leftjoin('lead_followup as f','f.lead_id','lead.id')->where('lead.active',1)->whereIn('lead.id',$array);
        }

        else
        {
            $data = Lead::leftjoin('lead_followup as f','f.lead_id','lead.id')->where('lead.active',1);

            if(Auth::user()->role == 2)
            {
                $data = Lead::leftjoin('lead_followup as f','f.lead_id','lead.id')->where('lead.active',1)->where('lead.branch_id',Auth::user()->id);

            }
        }

        if(Auth::guard('user')->check())
        {
            if(Auth::user()->role == 1)
            {
                if($this->param['branch_id'])
                {
                    $data->where('lead.branch_id',$this->param['branch_id']);
                }
                if($this->param['employee_id'])
                {
                    $array = [];

                    $lead = LeadAssign::get();
                    $arr = [];
                    foreach($lead as $dt)
                    {
                        $ex_arr = explode(',', $dt->employee_id);
                        if(in_array($this->param['employee_id'],$ex_arr))
                        {
                            array_push($array,$dt->lead_id);
                        }
                    }
                    $data->whereIn('lead.id',$array);
                }
            }else
            {

                if($this->param['employee_id'])
                {
                    $array = [];

                    $lead = LeadAssign::get();
                    $arr = [];
                    foreach($lead as $dt)
                    {
                        $ex_arr = explode(',', $dt->employee_id);
                        if(in_array($this->param['employee_id'],$ex_arr))
                        {
                            array_push($array,$dt->lead_id);
                        }
                    }
                    $data->whereIn('lead.id',$array);
                }
            }
        }
        if($this->param['client_id'])
        {
            $data->where('lead.client_id',$this->param['client_id']);
        }
        if($this->param['lead_name'])
        {

            $query = $data->select('lead.*')->get();
            $array_id = [];
            foreach($query as $dt)
            {
                $co_co = explode(',', $dt['lead_name']);
                foreach($co_co as $co)
                {
                    if($co == $this->param['lead_name'])
                    {
                        array_push($array_id, $dt->id);
                    }
                }
            }
            $data->whereIn('lead.id',$array_id);
        }
        if($this->param['company_name'])
        {
            $query = $data->select('lead.*')->get();
            $array_id = [];
            foreach($query as $dt)
            {
                $co_co = explode(',', $dt['company_name']);
                foreach($co_co as $co)
                {
                    if($co == $this->param['company_name'])
                    {
                        array_push($array_id, $dt->id);
                    }
                }
            }
            $data->whereIn('lead.id',$array_id);
        }
        if($this->param['from_date'] && $this->param['to_date'])
        {
            $data->where('lead.date','>=',$this->param['from_date'])->where('lead.date','<=',$this->param['to_date']);
        }
        if($this->param['source_id'])
        {
            $data->where('lead.source_id',$this->param['source_id']);
        }
        if($this->param['source_name'])
        {
            $data->where('lead.source_name',$this->param['source_name']);
        }

        $query = $data->select('lead.*')->get();
        foreach($query as $key=>$value)
        {
            if(Auth::guard('user')->check())
            {
                if(Auth::user()->role == 1)
                {
                    $query[$key]['client'] = Client::where('id',$value->client_id)->value('member');
                    $query[$key]['branch'] = User::where('id',$value->branch_id)->value('name');
                    $leads = LeadAssign::where('lead_id',$value->id)->first();
                    if($leads)
                    {
                        $exp = explode(',',$leads->employee_id);
                        $emp = Employee::whereIn('id',$exp)->get();
                        $arr1 = [];
                        foreach ($emp as $keys => $values) {
                            array_push($arr1,$values->name);
                        }
                        $btn = implode(', ',$arr1);
                    }
                    $query[$key]['employee'] = $btn;
                }elseif (Auth::user()->role == 2) {
                    $leads = LeadAssign::where('lead_id',$value->id)->first();
                    if($leads)
                    {
                        $exp = explode(',',$leads->employee_id);
                        $emp = Employee::whereIn('id',$exp)->get();
                        $arr1 = [];
                        foreach ($emp as $keys => $values) {
                            array_push($arr1,$values->name);
                        }
                        $btn = implode(', ',$arr1);
                    }
                    $query[$key]['client'] = Client::where('id',$value->client_id)->value('member');
                    $query[$key]['employee'] = $btn;
                    unset($query[$key]['branch_id']);

                }
            }
            if(Auth::guard('employee')->check())
            {
                unset($query[$key]['branch_id'],$query[$key]['employee']);
            }
            $query[$key]['client'] = Client::where('id',$value->client_id)->value('member');
            $category = explode(',', $value->lead_name);
            $arr = [];
            foreach ($category as $keys => $values) {
                $ca = ProductCategory::where('id',$values)->value('name');
                array_push($arr, $ca);
            }  
            $c = implode(',', $arr);
            $query[$key]['category'] = $c;
            $array = [];
            $company = explode(',', $value->company_name);
            foreach ($company as $keys => $values) {
                $com = Company::where('id',$values)->value('name');
                array_push($array, $com);
            }
            $compa = implode(',', $array);
            $query[$key]['company'] = $compa;
            $query[$key]['title'] = $c;
            $query[$key]['lead_date'] = date('d/m/Y', strtotime($value->date));
            $query[$key]['source'] = LeadSource::where('id',$value->source_id)->value('name');
            $query[$key]['sourcename'] = SourceName::where('id',$value->source_name)->value('name');
            if(Auth::guard('user')->check())
            {
                $leads = LeadAssign::where('lead_id',$value->id)->first();
                $exp = explode(',',$leads->employee_id);
                $emp = Employee::whereIn('id',$exp)->get();
                $arr = [];

                foreach ($emp as $keys => $values) {
                    $followup = LeadFollowUp::where('lead_id',$value->id)->where('employee_id',$values->id)->first();
                    if(isset($followup))
                    {
                        $followup_log = FollowupLog::where('lead_id',$value->id)->where('employee_id',$values->id)->latest()->first();
                        if($followup_log)
                        {
                            $date = date('d/m/Y', strtotime($followup_log->date));
                        }
                        else
                        {
                            $date = '';
                        }

                        $status = '';
                        if ($followup->is_completed == 1) {
                            $status = 'Pending';
                        }elseif ($followup->is_completed == 2) {
                            $status = 'Discussion';
                        }elseif ($followup->is_completed == 3) {
                            $status = 'Rivision';
                        }elseif ($followup->is_completed == 4) {
                            $status = 'Win';
                        }elseif ($followup->is_completed == 5) {
                            $status = 'Lost';
                        }
                    }
                    else
                    {
                        $date = '-';
                        $status = '-';
                    }
                    $data1 = $values->name .' - ' .$status .' - ' .$date;
                    array_push($arr,$data1);
                    $btn = implode(',',$arr);
                    $query[$key]['followup_date'] = $btn;
                }
            }else
            {
                $followup = LeadFollowUp::where('lead_id',$value->id)->where('employee_id',Auth::user()->id)->first();
                if($followup)
                {
                    $date = date('d/m/Y', strtotime($followup->date));
                    $status = '';
                    if ($followup->is_completed == 1) {
                        $status = 'Pending';
                    }elseif ($followup->is_completed == 2) {
                        $status = 'Discussion';
                    }elseif ($followup->is_completed == 3) {
                        $status = 'Rivision';
                    }elseif ($followup->is_completed == 4) {
                        $status = 'Win';
                    }elseif ($followup->is_completed == 5) {
                        $status = 'Lost';
                    }
                }
                else
                {
                    $date = '-';
                    $status = '-';
                }
                $data = Auth::user()->name .' - ' .$status;
                $query[$key]['followup_date'] = $data;
            }
            unset($query[$key]['id'],$query[$key]['created_at'],$query[$key]['updated_at'],$query[$key]['description'],$query[$key]['productcategory_id'],$query[$key]['product_id'],$query[$key]['no_of_machines'],$query[$key]['current_project_phase'],$query[$key]['lead_value'],$query[$key]['app_link'],$query[$key]['employee_app_link'],$query[$key]['status'],$query[$key]['lead_name'],$query[$key]['company_name'],$query[$key]['date'],$query[$key]['source_id'],$query[$key]['source_name'],$query[$key]['reason'],$query[$key]['active'],$query[$key]['deleted_at'],$query[$key]['employee_id'],$query[$key]['branch_id'],$query[$key]['project_type_id'],$query[$key]['showroom'],$query[$key]['client_id'],$query[$key]['project_name_id']);

        }

        return $query;
    }
}