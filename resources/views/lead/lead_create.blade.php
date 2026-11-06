<?php
$exp = explode(',',$lead->lead_name);
$emp = App\ProductCategory::whereIn('id',$exp)->where('status',1)->get();
$arr = [];
foreach ($emp as $key => $value) {
    array_push($arr,$value->name);
}
$lead_name = implode(', ',$arr);
?>
<?php   
if($lead->status == 1){
    $statuss = 'Pending';
}elseif($lead->status == 2){
    $statuss = 'Discussion';
}elseif($lead->status == 3){
    $statuss = 'Rivision';
}elseif($lead->status == 4){
    $statuss = 'Win';
}elseif($lead->status == 5){
    $statuss = 'Lost';
}else{
    $statuss = '';
}
?>
<h4>Lead Basic</h4>
<p>Lead No. - {{$lead->id}}</p>
<p>Client Name - {{$lead->client->member}}</p>
<p>Category - {{$lead_name}}</p>
<p>Lead Description - {{$lead->description}}</p>
<p>Source - {{$lead->source->name}}</p>
<p>Source Name - {{$lead->sourcename->name}}</p>
<p>Lead Date - {{date('d-m-Y', strtotime($lead->date))}}</p>
<p>Current Project Phase - {{$lead->current_project_phase}}</p>
<p>Lead Value - {{$lead->lead_value}}</p>
<p>Lead Status - {{$statuss}}</p>
<?php
$design = App\LeadBasicDesignChild::where('lead_id',$lead->id)->get();
$leadmain = App\LeadBasicMainChild::where('lead_id',$lead->id)->get();
$lead_assign = App\LeadAssign::where('lead_id',$lead->id)->first();
$lead_stage = App\LeadStage::where('id',$lead_assign->stage_id)->value('name');
$lead_followup = App\LeadFollowUp::where('lead_id',$lead->id)->get();
?>

<br>
<table cellspacing="0" border="0">
    <thead>
        <tr>
            <th>Date</th>
            <th>Design</th>
        </tr>
    </thead>
    <tbody>
        @foreach($design as $key=>$value)
        <tr class="fordelete">
            <td>
                {{date('d-m-Y', strtotime($value->date))}}

            </td>
            <td>
                @if(isset($value->design))           
                <a href="{{url('design/',$value->design)}}" target="_blank">
                    <img src = "{{url('design/',$value->design)}}"  height="50" width="50">
                </a>
                @endif
            </td>
        </tr>
        @endforeach

    </tbody>
</table>
<br>
@foreach($leadmain as $keys => $values)
<label>Name:</label>
{{$values->name}}
<br/>
<table cellspacing="0" border="1">
    <thead>
        <tr>
            <th>Category</th>
            <th>Company</th>
            <th>Product</th>
            <th>Warehouse</th>
            <th>Qty</th>

        </tr>
    </thead>
    <tbody>
        <?php 
        $leadbasicchild = App\LeadBasicChild::where('lead_basic_main_id',$values->id)->get();
        ?>
        @if(isset($leadbasicchild))
        @foreach($leadbasicchild as $keyss => $valuess)
        <?php
        $procat = App\ProductCategory::where('id',$valuess->productcategory_id)->value('name');
        $company = App\Company::where('id',$valuess->company_id)->value('name');
        $pro = App\Product::where('id',$valuess->product_id)->value('product');
        $warehouse = App\Warehouse::where('id',$valuess->warehouse_id)->value('name');
        ?>
        <tr class="edit_master_clone">

            <td width="15%">
                {{$procat}}

            </td>

            <td width="15%">
                {{$company}}
            </td>

            <td width="25%">
                {{$pro}}

            </td>

            <td width="20%">
                {{$warehouse}}
            </td>

            <td width="15%">
                {{$valuess->qty}}

            </td>


        </tr>
        @endforeach
        @endif

    </tbody>
</table>
@endforeach
<br>
@if(isset($lead_assign))
<h4>Lead Assign</h4>
<p>Stage - {{$lead_stage}}</p>
<p>Lead Note - {{$lead_assign->note}}</p>
@endif
<br>
@if(isset($lead_followup))
<h4>Lead Followup</h4>
@foreach($lead_followup as $value)
<?php $empnm = App\Employee::where('id',$value->employee_id)->value('name'); ?>
<h4>Employee - {{$empnm}}</h4>
<p>Subject - {{$value->subject}}</p>
<p>Description - {{$value->description}}</p>
<p>Schedule Date - {{date('d-m-Y', strtotime($value->date))}}</p>
<p>Schedule Time - {{date('h:i A', strtotime($value->time))}}</p>
<?php
if($value->type == 1){
    $types = 'SITE VISIT';
}elseif($value->type == 2){
    $types = 'PHONE CALL';
}else{
    $types = '';
}
if($value->is_completed == 0){
    $iscompleted = 'None';
}elseif($value->is_completed == 1){
    $iscompleted = 'Pending';
}elseif($value->is_completed == 2){
    $iscompleted = 'Completed';
}else{
    $iscompleted = '';
}
?>
<p>Followup Type - {{$types}}</p>
<p>Status - {{$iscompleted}}</p>
@endforeach
@endif