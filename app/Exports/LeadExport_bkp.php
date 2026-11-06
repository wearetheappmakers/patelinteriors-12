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
use App\User;
use App\Type;
use App\Client;
use App\LeadFollowUp;
use App\Company;
use Auth;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
class LeadExport implements FromCollection,WithHeadings
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
          'category',
          'company',
          'lead_date',
          'source',
          'source_name',
          'followup_description',
          'followup_subject',
          'followup_type',
          'followup_status',
          'lead_status',
          'showroom'
        ];
      }else
      {
        return [
          'branch',
          'employee',
          'client',
          'category',
          'company',
          'lead_date',
          'source',
          'sourcename',
          'followup_description',
          'followup_subject',
          'followup_type',
          'followup_status',
          'lead_status',
          'showroom'
          
        ];
      }
    }
    else
    {
      return [
        'client',
        'category',
        'company',
        
        'lead_date',
        'source',
        'sourcename',
        'followup_description',
        'followup_subject',
        'followup_type',
        'followup_status',
        'lead_status',
        'showroom'
      ];
    }
  }
  public function collection()
  {
    if(Auth::guard('employee')->check())
    {
      // $data = LeadFollowUp::where('active',1)->orderBy('date','DESC')->get();
      // $array = [];
      // foreach($data as $d)
      // {
      //  $explode = explode(',',$d->employee_id);
      //  if(in_array(Auth::user()->id,$explode))
      //  {
      //    array_push($array,$d->id);
      //  }
      // }
      // $data = LeadAssign::get();
      // $arr = [];
      // foreach($data as $dt)
      // {
      //  $ex_arr = explode(',', $dt->employee_id);
      //  if(in_array(Auth::user()->id,$ex_arr))
      //  {
      //    array_push($array,$dt->lead_id);
      //  }
      // }
      $query = LeadFollowUp::leftJoin('lead as l','l.id','lead_followup.lead_id')->where('l.active',1)->where('lead_followup.employee_id',Auth::user()->id);

      // $query = Lead::orderBy('date','DESC')->where('active',1)->orWhereIn('id',$array);
    }
    else
    {
      $query = LeadFollowUp::leftJoin('lead as l','l.id','lead_followup.lead_id')->where('l.active',1);
      // $query = Lead::where('active',1)->get();
      if(Auth::user()->role == 2)
      {
        $query = LeadFollowUp::leftjoin('lead as l','l.id','lead_followup.lead_id')->where('l.active',1)->where('l.branch_id',Auth::user()->id);

      }
    }

    if(Auth::guard('user')->check())
    { 
      if(Auth::user()->role == 1)
      {
        if($this->param['branch_id'])
        {
          $query->where('l.branch_id',$this->param['branch_id']);
          

        }
        if($this->param['employee_id'])
        {
          $query->where('lead_followup.employee_id',$this->param['employee_id']);
      // $query->where('employee_id',$this->param['employee_id']);
        }

      }else
      {
        if($this->param['employee_id'])
        {
          $query->where('lead_followup.employee_id',$this->param['employee_id']);
      // $query->where('employee_id',$this->param['employee_id']);
        }
      }
    }

    if($this->param['client_id'])
    {
      $query->where('l.client_id',$this->param['client_id']);
    }

    if($this->param['from_date'])
    {
      $query->where('l.date','>=',$this->param['from_date']);
      
    }
    if($this->param['to_date'])
    {
      $query->where('l.date','<=',$this->param['to_date']);
      // $query = $query->where('date','<=',$this->param['to_date']);
    }
    if($this->param['source_id'])
    {
      $query->where('l.source_id',$this->param['source_id']);
      // $query = $query->where('source_id',$this->param['source_id']);
    }
    if($this->param['source_name'])
    {
      $query->where('l.source_name',$this->param['source_name']);
      // $query = $query->where('source_name',$this->param['source_name']);
    }

    

    if($this->param['lead_name'])
    {
                    // $data = $query;
      $data = $query->select('lead_followup.*')->get();
      $array_id = [];
      foreach($data as $dt)
      {
        $lead = Lead::where('id',$dt->lead_id)->first();
        $co_co = explode(',', $lead->lead_name);
        foreach($co_co as $co)
        {
          if($co == $this->param['lead_name'])
          {
            array_push($array_id, $dt->id);
          }
        }
      }
      $query->whereIn('lead_followup.id',$array_id);
    } 

    if($this->param['company_name'])
    {

      $data = $query->select('lead_followup.*')->get();
      $array_id = [];
      foreach($data as $dt)
      {
        $lead = Lead::where('id',$dt->lead_id)->first();
        $co_co = explode(',', $lead->company_name);
        foreach($co_co as $co)
        {
          if($co == $this->param['company_name'])
          {
            array_push($array_id, $dt->id);
          }
        }
      }
      $query->whereIn('lead_followup.id',$array_id);
    }

    $query = $query->select('lead_followup.*')->get();









    foreach($query as $key=>$value)
    {

      if(Auth::guard('user')->check())
      {
        if(Auth::user()->role == 2)
        {
          $employee = Employee::where('id',$value->employee_id)->value('name');
          $lead = Lead::where('id',$value->lead_id)->first();
          // $leads = LeadAssign::where('lead_id', $value->lead_id)->first();
          // $exp = explode(',',$leads->employee_id);
          // $emp = Employee::whereIn('id',$exp)->get();
          // $arr = [];
          // foreach ($emp as $key => $value) {
          //    // dd($emp);
          //  array_push($arr,$value->name);
          // }
          // $btn = implode(', ',$arr);

          $query[$key]['employee'] = $employee;

          unset($query[$key]['branch']);

        }
        else
        {
          $lead = Lead::where('id',$value->lead_id)->first();
          if(isset($lead->branch_id))
          {
            $branch = User::where('id',$lead->branch_id)->value('name');
            $query[$key]['branch'] = $branch;
          }else{
            $query[$key]['branch'] = ' ';
          }
          $employee = Employee::where('id',$value->employee_id)->value('name');
            // $leads = LeadAssign::where('lead_id', $value->id)->first();
            // $exp = explode(',',$leads->employee_id);
            // $emp = Employee::whereIn('id',$exp)->get();
            // $arr = [];
            // foreach ($emp as $keys => $values) {
            //  array_push($arr,$values->name);
            // }
            // $btn = implode(', ',$arr);
          $query[$key]['employee'] = $employee;
        }
      }

      if(Auth::guard('employee')->check())
      {
        $lead = Lead::where('id',$value->lead_id)->first();
        unset($query[$key]['branch'],$query[$key]['employee']);
      }
      if(isset($lead->client_id))
      {
        $ccc = Client::where('id',$lead->client_id)->value('member');
        $query[$key]['client_id']  = $ccc;
      }else{
        $query[$key]['client_id']  = ' ';

      }
      // dd($query[$key]['client_id']);

      // $query[$key]['client_id'] = Client::where('id',$lead->client_id)->value('member');
      if(isset($lead->lead_name))
      {
        $category = explode(',', $lead->lead_name);
        $arr = [];
        foreach ($category as $keys => $values) {
          $ca = ProductCategory::where('id',$values)->value('name');
          array_push($arr, $ca);
        }  
        $c = implode(',', $arr);
        $query[$key]['category'] = $c;
      }
      else{
        $query[$key]['category'] = ' ';
      }
      if(isset($lead->company_name))
      {
        $array = [];
        $company = explode(',', $lead->company_name);
        foreach ($company as $keys => $values) {
          $com = Company::where('id',$values)->value('name');
          array_push($array, $com);
        }
        $compa = implode(',', $array);
        $query[$key]['company'] = $compa;
      }else{
        $query[$key]['company'] = '';
      }
      if(isset($lead->date))
      {
        $query[$key]['lead_date'] = $lead->date;
      }else{
        $query[$key]['lead_date'] = '';
      }
      if(isset($lead->source_id))
      {
        $query[$key]['source'] = LeadSource::where('id',$lead->source_id)->value('name');
      }else{
        $query[$key]['source'] = '';
      }
      if(isset($lead->source_name))
      {
        $query[$key]['sourcename'] = SourceName::where('id',$lead->source_name)->value('name');
      }else{
        $query[$key]['sourcename'] = '';
      }
      $query[$key]['followup_description'] = $value->description;
      $query[$key]['followup_subject'] = $value->subject;
      if($value->type)
      {
        $type = Type::where('id',$value->type)->value('name');
      }else
      {
        $type = '';
      }

      if($value->is_completed == 1)
      {
        $followup_status = 'Pending';
      }elseif ($value->is_completed == 2) {
        $followup_status = 'Discussion';
      }elseif ($value->is_completed == 3) {
        $followup_status = 'Rivision';
      }elseif ($value->is_completed == 4) {
        $followup_status = 'Win';
      }elseif ($value->is_completed == 5) {
        $followup_status = 'Lost';
      }elseif ($value->is_completed == 6) {
        $followup_status = "Showroom Visit";
      }elseif ($value->is_completed == 7) {
        $followup_status = "Site Measurement";
      }elseif ($value->is_completed == 8) {
        $followup_status = "Designing";
      }elseif ($value->is_completed == 9) {
        $followup_status = "Quotation";
      } 
      else
      {
        $followup_status = 'Pending';
      }

      if(isset($lead->status))
      {

        if($lead->status == 1)
        {
          $status = 'Pending';
        }elseif ($lead->status == 2) {
          $status = 'Discussion';
        }elseif ($lead->status == 3) {
          $status = 'Rivision';
        }elseif ($lead->status == 4) {
          $status = 'Win';
        }elseif ($lead->status == 5) {
          $status = 'Lost';
        }
      }else{
       $status = 'Pending';
     }

     if(isset($lead->showroom))
     {
      if($lead->showroom == 1)
      {
        $showroom = 'Yes';
      }else{
        $showroom = 'No';
      }
    }else{
      $showroom = '';
    }

    $query[$key]['followup_type'] = $type;
    $query[$key]['followup_status'] = $followup_status;
    $query[$key]['lead_status'] = $status;
    $query[$key]['showroom'] = $showroom;


    unset($query[$key]['id'],$query[$key]['created_at'],$query[$key]['updated_at'],$query[$key]['branch_id'],$query[$key]['lead_id'],$query[$key]['employee_id'],$query[$key]['subject'],$query[$key]['description'],$query[$key]['date'],$query[$key]['time'],$query[$key]['type'],$query[$key]['is_completed'],$query[$key]['status_date']);

  }

  return $query;
}
}