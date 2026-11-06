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
<title>Lead </title>
</head> 
<div class="row">
    <div class="column">
        <table>
            @if(Auth::guard('user')->check())
            @if(Auth::user()->role == 1)
            <tr><th colspan="13" style="font-size: 18px !important;"><center>Lead </center></th></tr>
            @endif
            @if(Auth::user()->role == 2)
            <tr><th colspan="12" style="font-size: 18px !important;"><center>Lead</center></th></tr>
            @endif
            @endif
            @if(Auth::guard('employee')->check())
            <tr><th colspan="11" style="font-size: 18px !important;"><center>Lead</center></th></tr>
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
                <th>Client</th>
                <th>Category</th>
                <th>Company</th>
                <th>Lead Date</th>
                <th>Source</th>
                <th>Source Name</th>
                <th>Followup Description</th>
                <th>Followup Subject</th>
                <th>Followup Status</th>
                <th>Lead Status</th>
                <th>Showroom Visit</th>
            </tr>
            @foreach($data as $d)

            @php $lead = App\Lead::where('id',$d['lead_id'])->first(); @endphp
            @php $emps = App\Employee::where('id',$d['employee_id'])->value('name'); @endphp

            <?php
            if(isset($lead->branch_id))
            {
                $branch = App\User::where('id',$lead->branch_id)->value('name'); 
            }else{
                $branch = '';
            }
            if(isset($lead->client_id))
            {
                $client = App\Client::where('id',$lead->client_id)->value('member'); 
            }else{
                $client = '';
            }
            ?>

            <?php
            if(isset($lead->lead_name))
            {
                $btn = explode(',',$lead->lead_name);
                $cat = App\ProductCategory::whereIn('id',$btn)->select('name')->get();
                $arr = [];
                foreach ($cat as $key => $value) {
                    array_push($arr,$value->name);
                }
                $imp = implode(', ',$arr);
            }else{
                $imp = '';
            }

            if(isset($lead->company_name))
            {
                $exp = explode(',',$lead->company_name);
                $emp = App\Company::whereIn('id',$exp)->where('status',1)->get();
                $array = [];
                foreach ($emp as $key => $value) {
                    array_push($array,$value->name);
                }
                $company_name = implode(', ',$array);
            }else{
                $company_name = '';
            }

            if(isset($lead->source_id))
            {
                $source = App\LeadSource::where('id',$lead->source_id)->value('name');
            }else{
                $source = '';
            }
            if(isset($lead->source_name))
            {
                $source_name = App\SourceName::where('id',$lead->source_name)->value('name');
            }else{
                $source_name = '';
            }

            if(isset($lead))
            {
                $lead_assign = App\LeadAssign::where('lead_id',$lead->id)->first();
                if(isset($lead_assign))
                {
                    $exp = explode(',',$lead_assign->employee_id);
                    $emp = App\Employee::whereIn('id',$exp)->get();
                    $f_status_arr = [];
                    foreach ($emp as $keys => $values) {
                        $followup = App\LeadFollowUp::where('lead_id',$lead->id)->where('employee_id',$values->id)->first();
                        if(isset($followup))
                        {
                            if ($d['is_completed'] == 1) {
                                $f_status = 'Pending';
                            }elseif ($d['is_completed'] == 2) {
                                $f_status = 'Discussion';
                            }elseif ($d['is_completed'] == 3) {
                                $f_status = 'Rivision';
                            }elseif ($d['is_completed'] == 4) {
                                $f_status = 'Win';
                            }elseif ($d['is_completed'] == 5) {
                                $f_status = 'Lost';
                            }elseif ($d['is_completed'] == 6) {
                                $f_status = "Showroom Visit";
                            }elseif ($d['is_completed'] == 7) {
                                $f_status = "Site Measurement";
                            }elseif ($d['is_completed'] == 8) {
                                $f_status = "Designing";
                            }elseif ($d['is_completed'] == 9) {
                                $f_status = "Quotation";
                            }else{
                                $f_status = "Pending";
                            }
                        }
                        else
                        {
                            $f_status = 'Pending';
                        }
                        $combine_data = $values->name .' - ' .$f_status;
                        array_push($f_status_arr,$combine_data);
                    }
                    $f_status_implode = implode(' - ',$f_status_arr);
                }
                else
                {
                    $f_status_implode = '-';
                }
            }
            else
            {
                $f_status_implode = '-';
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
                }else
                {
                    $status = 'Pending';
                }
            }else{
                $status = '';
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

            if(isset($lead->date))
            {
                $dates = $lead->date;
            }else{
                $dates = '';
            }

            ?>

            <tr>
                @if(Auth::guard('user')->check())
                @if(Auth::user()->role == 1)
                <th>{{$branch}}</th>
                <th>{{$emps}}</th>
                @endif
                @if(Auth::user()->role == 2)
                <th>{{$emps}}</th>
                @endif
                @endif
                <th >{{$client}}</th>
                <th >{{$imp}}</th>
                <th >{{$company_name}}</th>

                <th >{{$dates}}</th>
                <th >{{$source}}</th>
                <th >{{$source_name}}</th>

                <th>{{$d['description']}}</th>
                <th>{{$d['subject']}}</th>
                <th>{{$f_status_implode}}</th>
                <th>{{$status}}</th>
                <th>{{$showroom}}</th>
            </tr>
            @endforeach
        </table>
    </div>
</div>
</html>