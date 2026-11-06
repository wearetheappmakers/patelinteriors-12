<!DOCTYPE html>
<html>
<head>
     <style type="text/css">
          body{
/*font-size: 0px !important;*/
font-family: "verdana", sans-serif;
font-size: 12px !important;
}
td,th{
     font-family: "verdana", sans-serif;
     font-size: 12px !important;
     border-collapse: collapse;
     border: 1px solid #737373 !important;
     padding: 2px !important; 
}
.no-break {
     page-break-before: always !important;
}
ol li{
     text-align: justify;
     font-size:70% !important;
}
{
     box-sizing: border-box;
}
.row {
     margin-left:-5px;
     margin-right:-5px;
}
.column {
     width: 100%;
}
.column1 {
     float: left;
     width: 50%;
/*padding: 5px;*/
}
table {
     border-collapse: collapse;
     border-spacing: 0;
     width: 100%;
     border: 1px;
}
</style>
<title>Followup</title>
</head> 
<div class="row">
     <div class="column">
          <table>
               @if(Auth::guard('user')->check())
               @if(Auth::user()->role == 1)
               <tr><th colspan="10" style="font-size: 18px !important;"><center>Followup</center></th></tr>
               @endif
               @if(Auth::user()->role == 2)
               <tr><th colspan="9" style="font-size: 18px !important;"><center>Followup</center></th></tr>
               @endif


               @endif
               @if(Auth::guard('employee')->check())
               <tr><th colspan="8" style="font-size: 18px !important;"><center>Followup</center></th></tr>
               @endif
               <tr>
                    @if(Auth::guard('user')->check())
                    @if(Auth::user()->role == 1)
                    <th>Branch Name</th>
                    <th>Employee Name</th>
                    @endif
                    @if(Auth::user()->role == 2)
                    <th>Employee Name</th>
                    @endif
                    @endif
                    <th >Client</th>
                    <th >Category</th>
                    <th >Company</th>
                    <th >Title</th>
                    <th >Lead Date</th>
                    <th width="14%" >Source</th>
                    <th width="14%">Source Name</th>
                    <th>Followup Date</th>
               </tr>
               @foreach($data as $d)


               @php $branch = App\User::where('id',$d['branch_id'])->value('name'); @endphp

               @php $client = App\Client::where('id',$d['client_id'])->value('member'); @endphp
               <?php
               $leads = App\LeadAssign::where('lead_id',$d['id'])->first();
               $exp = explode(',',$leads->employee_id);
               $emp = App\Employee::whereIn('id',$exp)->get();
               $arr = [];
               foreach ($emp as $key => $value) {
                    array_push($arr,$value->name);
               }
               $employee = implode(', ',$arr); 

               $btn = explode(',',$d['lead_name']);
               $cat = App\ProductCategory::whereIn('id',$btn)->select('name')->get();
               $arr = [];
               foreach ($cat as $key => $value) {
                    array_push($arr,$value->name);
               }
               $imp = implode(', ',$arr);
               $exp = explode(',',$d['company_name']);
               $emp = App\Company::whereIn('id',$exp)->where('status',1)->get();
               $array = [];
               foreach ($emp as $key => $value) {
                    array_push($array,$value->name);
               }
               $company_name = implode(', ',$array);
               $source = App\LeadSource::where('id',$d['source_id'])->value('name');
               $source_name = App\SourceName::where('id',$d['source_name'])->value('name');
               if(Auth::guard('user')->check())
               {
                    $leads = App\LeadAssign::where('lead_id',$d['id'])->first();
                    $exp = explode(',',$leads->employee_id);
                    $emp = App\Employee::whereIn('id',$exp)->get();
                    $arr = [];

                    foreach ($emp as $keys => $values) {
                         $followup = App\LeadFollowUp::where('lead_id',$d['id'])->where('employee_id',$values->id)->first();
                         if(isset($followup))
                         {
                              $followup_log = App\FollowupLog::where('lead_id',$d['id'])->where('employee_id',$values->id)->latest()->first();
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
                    }
                    $btn = implode(',',$arr);
               }else
               {
                    $followup = App\LeadFollowUp::where('lead_id',$d['id'])->where('employee_id',Auth::user()->id)->first();
                    if(isset($followup))
                    {
                         $followup_log = App\FollowupLog::where('lead_id',$d['id'])->where('employee_id',Auth::user()->id)->latest()->first();
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
                    $btn = Auth::user()->name .' - ' .$status .' - ' .$date;
               }
               ?>

               <tr>
                    @if(Auth::guard('user')->check())
                    @if(Auth::user()->role == 1)
                    <th>{{$branch}}</th>
                    <th>{{$employee}}</th>
                    @endif
                    @if(Auth::user()->role == 2)
                    <th>{{$employee}}</th>
                    @endif
                    @endif

                    <th >{{$client}}</th>
                    <th >{{$imp}}</th>
                    <th >{{$company_name}}</th>
                    <th >{{$imp}}</th>
                    <th >{{date('d/m/Y', strtotime($d['date']))}}</th>
                    <th >{{$source}}</th>
                    <th >{{$source_name}}</th>
                    <th>{{$btn}}</th>
               </tr>
               @endforeach


          </table>
     </div>
</div>
</html>