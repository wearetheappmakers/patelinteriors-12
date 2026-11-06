<!DOCTYPE html>
<html>
<head>
    <style type="text/css">
        body{
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
        }
        table {
            border-collapse: collapse;
            border-spacing: 0;
            width: 100%;
            border: 1px;
        }
    </style>
    <title>Today Followup </title>
</head> 
<div class="row">
    <div class="column">
        <table>
            @if(Auth::guard('user')->check())
            @if(Auth::user()->role == 1)
            <tr><th colspan="11" style="font-size: 18px !important;"><center>Today Followup </center></th></tr>
            @endif
            @if(Auth::user()->role == 2)
            <tr><th colspan="10" style="font-size: 18px !important;"><center>Today Followup</center></th></tr>
            @endif
            @endif
            @if(Auth::guard('employee')->check())
            <tr><th colspan="9" style="font-size: 18px !important;"><center>Today Followup</center></th></tr>
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
                <th >Source</th>
                <th >Source Name</th>
                <th >Lead Date</th>
                <th >Subject</th>
                <th >Description</th>
                <th >Time</th>
                <th >Type</th>
                <th >Status</th>
            </tr>
            @foreach($query as $d)

            @php $lead = App\Lead::where('id',$d->lead_id)->where('active',1)->first(); @endphp
            @php $branch = App\User::where('id',$lead->branch_id)->value('name'); @endphp
            @php $client = App\Client::where('id',$lead->client_id)->value('member'); @endphp
            @php $employee = App\Employee::where('id',$lead->employee_id)->value('name'); @endphp
            @php $source = App\LeadSource::where('id',$lead->source_id)->value('name');@endphp
            @php $type = App\Type::where('id',$d->type)->value('name');@endphp
            @php $source_name = App\SourceName::where('id',$lead->source_name)->value('name');@endphp

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
                <?php
                $status = $d->is_completed;
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
                ?>
                <th >{{$client}}</th>
                <th >{{$source}}</th>
                <th >{{$source_name}}</th>
                <th >{{date('d/m/Y', strtotime($lead->date))}}</th>
                <th >{{$d->subject}}</th>
                <th >{{$d->description}}</th>
                <th >{{date('H:i A',strtotime($d->time))}}</th>
                <th >{{$type}}</th>
                <th >{{$btn}}</th>
            </tr>
            @endforeach
        </table>
    </div>
</div>
</html>