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
use App\User;
use App\Client;
use App\Company;
use App\FollowupLog;
use App\Type;
use Auth;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class YesterdayFollowupExport implements FromCollection,WithHeadings
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
                    'employee',
                    'client',
                    'source',
                    'source_name',
                    'lead_date',
                    'subjects',
                    'descriptions',
                    'times',
                    'types',
                    'status'
                ];
            }else
            {
                return [
                    'branch',
                    'employee',
                    'client',
                    'source',
                    'source_name',
                    'lead_date',
                    'subjects',
                    'descriptions',
                    'times',
                    'types',
                    'status'
                ];
            }
        }
        else
        {
            return [
                'client',
                'source',
                'source_name',
                'lead_date',
                'subjects',
                'descriptions',
                'times',
                'types',
                'status'
            ];
        }
    }
    public function collection()
    {
        $yesterday = date("Y-m-d", strtotime("- 1 day"));

        if(Auth::guard('user')->check())
        {
            if(Auth::user()->role == 1)
            {
                $data = FollowupLog::leftjoin('lead as ld','ld.id','followup_log.lead_id')->where('ld.active',1)->whereDate('followup_log.date',$yesterday)->select('followup_log.*');
                if($this->param['branch_id'])
                {
                    $data = FollowupLog::leftjoin('lead as ld','ld.id','followup_log.lead_id')->where('followup_log.date',$yesterday)->where('ld.active',1)->where('ld.branch_id',$this->param['branch_id'])->select('followup_log.*');
                }
            }

            else if(Auth::user()->role == 2)
            {
                $data = FollowupLog::leftjoin('lead as ld','ld.id','followup_log.lead_id')->where('ld.branch_id',Auth::user()->id)->where('ld.active',1)->whereDate('followup_log.date',$yesterday)->select('followup_log.*');
            }

            if($this->param['employee_id'])
            {

                $data = FollowupLog::leftjoin('lead as ld','ld.id','followup_log.lead_id')->where('followup_log.date',$yesterday)->where('ld.active',1)->where('followup_log.employee_id',$this->param['employee_id'])->select('followup_log.*');
            }
        }
        if(Auth::guard('employee')->check())
        {
            $data = FollowupLog::leftjoin('lead as ld','ld.id','followup_log.lead_id')->where('ld.employee_id',Auth::user()->id)->where('ld.active',1)->whereDate('followup_log.date',$yesterday)->select('followup_log.*');
        }


        if($this->param['client_id'])
        {
            $data = FollowupLog::leftjoin('lead as ld','ld.id','followup_log.lead_id')->where('followup_log.date',$yesterday)->where('ld.active',1)->where('ld.client_id',$this->param['client_id'])->select('followup_log.*');
        }

        if($this->param['source_id'])
        {
            $data = FollowupLog::leftjoin('lead as ld','ld.id','followup_log.lead_id')->where('followup_log.date',$yesterday)->where('ld.active',1)->where('ld.source_id',$this->param['source_id'])->select('followup_log.*');
        }
        if($this->param['source_name'])
        {
            $data = FollowupLog::leftjoin('lead as ld','ld.id','followup_log.lead_id')->where('followup_log.date',$yesterday)->where('ld.active',1)->where('ld.source_name',$this->param['source_name'])->select('followup_log.*');
        }
        $query = $data->get();

        foreach($query as $key=>$value)
        {
            $lead = Lead::where('id',$value->lead_id)->where('active',1)->first();

            if(Auth::guard('user')->check())
            {
                if(Auth::user()->role == 1)
                {
                    $query[$key]['branch_id'] = User::where('id',$lead->branch_id)->value('name');
                    $query[$key]['employee'] = Employee::where('id',$lead->employee_id)->value('name');
                }elseif (Auth::user()->role == 2) {
                    $query[$key]['employee'] = Employee::where('id',$lead->employee_id)->value('name');
                    unset($query[$key]['branch_id']);

                }
            }
            if(Auth::guard('employee')->check())
            {
                unset($query[$key]['branch_id'],$query[$key]['employee_id']);
            }
            $query[$key]['client'] = Client::where('id',$lead->client_id)->value('member');
            $query[$key]['source'] = LeadSource::where('id',$lead->source_id)->value('name');

            $query[$key]['source_name'] = SourceName::where('id',$lead->source_name)->value('name');
            $query[$key]['lead_date'] = date('d/m/Y', strtotime($lead->date));
            $query[$key]['subjects'] = $value->subject;
            $query[$key]['descriptions'] = $value->description;
            $query[$key]['times'] = date('H:i A',strtotime($value->time));
            $query[$key]['types'] = Type::where('id',$value->type)->value('name');
            $status = $value->is_completed;
            if($status == 1){
                $btn = "Pending"; 
            }
            elseif($status == 2){
                $btn = "Discussion"; 
            }
            elseif($status == 3){
                $btn = "Rivision"; 
            }
            elseif($status == 4){
                $btn = "Win"; 
            }
            elseif($status == 5){
                $btn = "Lost"; 
            } else {
                $btn = '';
            }
            $query[$key]['status'] = $btn;

            unset($query[$key]['id'],$query[$key]['created_at'],$query[$key]['updated_at'],$query[$key]['lead_id'],$query[$key]['date'],$query[$key]['subject'],$query[$key]['description'],$query[$key]['time'],$query[$key]['type'],$query[$key]['employee_id'],$query[$key]['is_completed']);
        }

        return $query;
    }
}