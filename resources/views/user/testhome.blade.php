@extends('main')
@section('content')
<style type="text/css">
	.kt-widget24{
		background-color: #FFFFFF;
		box-shadow:0 0 5px 0 rgba(0, 0, 0, 0.1) ;
		-webkit-box-shadow:0 0 5px 0 rgba(0, 0, 0, 0.1) ;
		position: relative;
		overflow: hidden;
	}
	.kt-widget24 .kt-widget24__icon {
		font-size: 120px;
		position: absolute;
		line-height: 120px;
		right: 0;
		color: rgba(0, 0, 0, 0.05);
		bottom: 0;
		margin-bottom: -24px;
		z-index: 0;
	}
	.kt-widget24 .kt-widget24__details {
		position: relative;
		z-index: 1; 
	}
	body {
		background: #F3F4F6;
	}
	.kt-widget24 .kt-widget24__details .kt-widget24__info .kt-widget24__title {
		color: #000000 !important;
		margin: 0;
		font-size: 16px;
	}
	.kt-widget24 .kt-widget24__details .kt-widget24__stats {
		padding-left:0;
	}
	.kt-widget24 .kt-widget24__details .view-more a {
		background-color: #000000;
		color: #ffffff;
		height: 30px;
		width: 30px;
		border-radius: 30px;
		text-align: center;
		display: flex;
		align-items: center;
		justify-content: center;
	}	
	.kt-widget24.yello-box {
		background-color:#FFF4DE;
		border: 1px solid #FFA800;
	}
	.kt-widget24.yello-box .kt-widget24__details .view-more a {
		background-color: #FFA800;
	}
	.kt-widget24.yello-box .kt-widget24__details .kt-widget24__stats {
		color:#FFA800;
	}
	.kt-widget24.lightblue-box {
		background-color:#E1F0FF;
		border: 1px solid #3699FF;
	} 
	.kt-widget24.lightblue-box .kt-widget24__details .kt-widget24__stats {
		color:#3699FF;
	}
	.kt-widget24.lightblue-box .kt-widget24__details .view-more a {
		background-color: #3699FF;
	}
	.kt-widget24.red-box {
		background-color:#FFE2E5;
		border: 1px solid #F64E60;
	}
	.kt-widget24.red-box .kt-widget24__details .kt-widget24__stats {
		color:#F64E60;
	}
	.kt-widget24.red-box .kt-widget24__details .view-more a {
		background-color: #F64E60;
	}
	.kt-widget24.lightgreen-box {
		background-color:#c9f7f5;
		border: 1px solid #1BC5BD;
	}
	.kt-widget24.lightgreen-box .kt-widget24__details .kt-widget24__stats {
		color:#1BC5BD;
	}
	.kt-widget24.lightgreen-box .kt-widget24__details .view-more a {
		background-color: #1BC5BD;
	}
	.kt-widget24.purple-box {
		background-color:#EEE5FF;
		border: 1px solid #8950FC;
	}
	.kt-widget24.purple-box .kt-widget24__details .kt-widget24__stats {
		color:#8950FC;
	}
	.kt-widget24.purple-box .kt-widget24__details .view-more a {
		background-color: #8950FC;
	}
	.kt-widget24.dark-box {
		background-color:#EBEDF3;
		border: 1px solid #181C32;
	}
	.kt-widget24.dark-box .kt-widget24__details .kt-widget24__stats {
		color:#181C32;
	}
	.kt-widget24.dark-box .kt-widget24__details .view-more a {
		background-color: #181C32;
	}
	.kt-widget24.pink-box {
		background-color: #CFF5CF;
		border: 1px solid #4B7502;
	}
	.kt-widget24.pink-box .kt-widget24__details .kt-widget24__stats {
		color:#4B7502;
	}
	.kt-widget24.pink-box .kt-widget24__details .view-more a {
		background-color: #4B7502;
	}
	.kt-widget24.gray-box {
		background-color:#d4edda;
		border: 1px solid #155724 ;
	}
	.kt-widget24.gray-box .kt-widget24__details .kt-widget24__stats {
		color:#155724;
	}
	.kt-widget24.gray-box .kt-widget24__details .view-more a {
		background-color: #155724;
	}
	.kt-portlet .kt-portlet__head .kt-portlet__head-label .kt-portlet__head-title {
		color: #000000;
		font-size: 16px;
	}
	.kt-portlet .kt-portlet__head .kt-portlet__head-label .kt-portlet__head-title i{
		color: #BC3043; 
		font-size: 20px;
		margin-right: 5px;
	}
	.table th, .table td {
		font-size: 14px;
		padding: 15px;
		font-weight: 400;
	}
	
	.eventClass
	{
		color: #000 !important;
		background-color: #d5dbeb !important;
		border: 1px solid #d5dbeb !important;
		
	}
	.eventClass i
	{
		color: black!important;
		padding: 5px !important;
		/*margin-left: 2px !important;*/
		
	}
	.eventClass1
	{
		color: #000 !important;
		background-color: #d5dbeb !important;
		border: 1px solid #d5dbeb !important;
		
	}
	.eventClass1 i
	{
		color: black!important;
		padding: 5px !important;
		/*margin-left: 2px !important;*/
		
	}
</style>
@php
if(Auth::guard('employee')->check())
{
	$total_lead = App\Lead::where('employee_id',Auth::user()->id)->where('active',1)->count();
	$total_lead_today = App\Lead::whereDate('created_at', date('Y-m-d'))->where('employee_id',Auth::user()->id)->where('active',1)->count();
	$total_lead_month = App\Lead::whereMonth('created_at', date('m'))->where('employee_id',Auth::user()->id)->where('active',1)->count();
	
}
else
{
	if(Auth::user()->role == 2)
	{
		$total_lead = App\Lead::where('branch_id',Auth::user()->id)->where('active',1)->count();
		$total_lead_today = App\Lead::whereDate('date',date('Y-m-d'))->where('branch_id',Auth::user()->id)->where('active',1)->count();
		$total_lead_month = App\Lead::whereMonth('date',date('m'))->whereYear('date', date('Y'))->where('branch_id',Auth::user()->id)->where('active',1)->count();
		$executive = App\Employee::where('branch_id',Auth::user()->id)->where('role',1)->where('status',1)->get();
		$source = App\LeadSource::where('branch_id',Auth::user()->id)->where('status',1)->get();
		
	}
	else
	{
		$total_lead = App\Lead::where('active',1)->count();
		$total_lead_today = App\Lead::whereDate('date', date('Y-m-d'))->where('active',1)->count();
		$total_lead_month = App\Lead::whereMonth('date', date('m'))->where('active',1)->whereYear('date', date('Y'))->count();
		
		$executive = App\Employee::where('role',1)->where('status',1)->get();
		$source = App\LeadSource::where('status',1)->get();
	}
}
@endphp
<link href="{{ asset('assets/plugins/custom/datatables/datatables.bundle.css')}}" rel="stylesheet" type="text/css" />
<link href="{{asset('assets/plugins/custom/fullcalendar/fullcalendar.bundle.css')}}" rel="stylesheet" type="text/css">
@if(!(Auth::user()->role == 5))
<div class="kt-content  kt-grid__item kt-grid__item--fluid kt-grid kt-grid--hor" id="kt_content">
	<br>
	<div class="kt-container  kt-container--fluid  kt-grid__item kt-grid__item--fluid">
		<div class="row">
			<div class="col-lg-2">
				<a href="{{route ('user.lead.index')}}" class="kt-portlet kt-iconbox " style="background: #FFFFFF;">
					<div class="kt-portlet__body ">
						<div class="kt-iconbox__body">
							<div class="kt-iconbox__desc">
								<h3 class="kt-iconbox__title">
									<span class="kt-widget24__stats" id="total_lead">
										{{$total_lead}}
									</span> 
								</h3>
								<div class="kt-iconbox__content" >
									Total Lead
								</div>
							</div>
						</div>
					</div>
				</a>
			</div>
			<div class="col-lg-2">
				<a href="{{route ('user.lead.index')}}" class="kt-portlet kt-iconbox " style="background: #FFFFFF;">
					<div class="kt-portlet__body ">
						<div class="kt-iconbox__body">
							<div class="kt-iconbox__desc">
								<h3 class="kt-iconbox__title">
									<span class="kt-widget24__stats" id="total_lead_month">
										{{$total_lead_month}}
									</span> 
								</h3>
								<div class="kt-iconbox__content">
									Total Lead Month
								</div>
							</div>
						</div>
					</div>
				</a>
			</div>
			<div class="col-lg-2">
				<a href="{{route ('user.lead.index')}}" class="kt-portlet kt-iconbox " style="background: #FFFFFF;">
					<div class="kt-portlet__body ">
						<div class="kt-iconbox__body">
							<div class="kt-iconbox__desc">
								<h3 class="kt-iconbox__title">
									<span class="kt-widget24__stats" id="total_weekly">
										{{$weekly}}
									</span> 
								</h3>
								<div class="kt-iconbox__content">
									Total Lead Week
								</div>
							</div>
						</div>
					</div>
				</a>
			</div>
			<div class="col-lg-2">
				<a href="{{route ('user.lead.index')}}" class="kt-portlet kt-iconbox " style="background: #FFFFFF;">
					<div class="kt-portlet__body ">
						<div class="kt-iconbox__body">
							<div class="kt-iconbox__desc">
								<h3 class="kt-iconbox__title">
									<span class="kt-widget24__stats" id="total_lead_today">
										{{$total_lead_today}}
									</span> 
								</h3>
								<div class="kt-iconbox__content">
									Total Lead Today
								</div>
							</div>
						</div>
					</div>
				</a>
			</div>
			<div class="col-lg-2">
				<a href="{{route ('user.lead.index')}}" class="kt-portlet kt-iconbox " style="background: #FFFFFF;">
					<div class="kt-portlet__body ">
						<div class="kt-iconbox__body">
							<div class="kt-iconbox__desc">
								<h3 class="kt-iconbox__title">
									<span class="kt-widget24__stats" id="total_followup">
										{{$today_followup}}
									</span> 
								</h3>
								<div class="kt-iconbox__content">
									Today Followup
								</div>
							</div>
						</div>
					</div>
				</a>
			</div>
			
			
		</div>
	</div>
	@if(Auth::guard('user')->check())
	<div class="kt-container  kt-container--fluid  kt-grid__item kt-grid__item--fluid">
		<div class="row">
			@if(Auth::user()->role == 1)
			<div class="col-lg-8">
				<div class="kt-portlet__body kt-portlet kt-iconbox" style="background: #FFFFFF;">
					<div class="form-group col-lg-4">
						<label>Branch </label> 
						<select class="form-control changes select2"  id="branch_id">
							@php $branch = App\User::where('status',1)->where('role','!=',1)->get(); @endphp
							<option value="">All</option>
							@foreach($branch as $sub)
							<option value="{{$sub->id}}">{{$sub->name}}</option>
							@endforeach
						</select>
					</div>
					<div id="kt_table_1_wrapper" class="dataTables_wrapper dt-bootstrap4">
						<table class="table table-striped table-bordered table-hover table-checkable datatable" id="datatable_rows111">
							@csrf
							<thead>
								<tr>
									<th>Sales Executive</th>
									<th colspan="2"><center>Total Inquiries</center></th>
									<th >Pending</th>
									<th>Discussion</th>
									<th>Revision</th>
									<th>Win</th>
									<th>Lost</th>
								</tr>
								<tr>
									<th></th>
									<th>Yesterday</th>
									<th>MTD</th>
									<th>MTD</th>
									<th>MTD</th>
									<th>MTD</th>
									<th>MTD</th>
									<th>MTD</th>
								</tr>
							</thead>
							<tbody>
								
							</tbody>
						</table>
					</div>
				</div>
			</div>
			<div class="col-lg-4">
				<div class="kt-portlet__body kt-portlet kt-iconbox" style="background: #FFFFFF;">
					<div class="form-group col-lg-6">
						<label>Branch </label> 
						<select class="form-control changes select2"  id="branch_ids">
							@php $branch = App\User::where('status',1)->where('role','!=',1)->get(); @endphp
							<option value="">All</option>
							@foreach($branch as $sub)
							<option value="{{$sub->id}}">{{$sub->name}}</option>
							@endforeach
						</select>
					</div>
					<div id="kt_table_1_wrapper" class="dataTables_wrapper dt-bootstrap4">
						<table class="table table-striped table-bordered table-hover table-checkable datatable" id="datatable_rows">
							@csrf
							<thead>
								<tr>
									<th>Lead Source</th>
									<th>Total Leads</th>
									<th>Win</th>
								</tr>
							</thead>
							<tbody>
								
							</tbody>
						</table>
					</div>
				</div>
			</div>
			@endif
			@if(Auth::user()->role == 2)
			<div class="col-lg-8">
				<div class="kt-portlet__body kt-portlet kt-iconbox" style="background: #FFFFFF;">
					<div id="kt_table_1_wrapper" class="dataTables_wrapper dt-bootstrap4">
						<table class="table table-striped table-bordered table-hover table-checkable datatable" id="datatable_rows111">
							@csrf
							<thead>
								<tr>
									<th>Sales Executive</th>
									<th colspan="2"><center>Total Inquiries</center></th>
									<th >Pending</th>
									<th>Discussion</th>
									<th>Revision</th>
									<th>Win</th>
									<th>Lost</th>
								</tr>
								<tr>
									<th></th>
									<th>Yesterday</th>
									<th>MTD</th>
									<th>MTD</th>
									<th>MTD</th>
									<th>MTD</th>
									<th>MTD</th>
									<th>MTD</th>
								</tr>
							</thead>
							<tbody>
								@if(count($executive) > 0)
								@foreach($executive as $ex)
								<?php
								$leadactive = App\Lead::where('employee_id',$ex->id)->where('active',1)->count();
								$yesterday = date('Y-m-d', strtotime('-1 day')); 
								$leadsYesterday=App\Lead::where('employee_id',$ex->id)->where('active', 1)->where('date', $yesterday)->count();
								$leadpending = App\Lead::where('employee_id',$ex->id)->where('active', 1)->where('status',1)->count();
								$leaddiscuss = App\Lead::where('employee_id',$ex->id)->where('active', 1)->where('status',2)->count();
								$leadrevision = App\Lead::where('employee_id',$ex->id)->where('active', 1)->where('status',3)->count();
								$leadwin = App\Lead::where('employee_id',$ex->id)->where('active', 1)->where('status',4)->count();
								$leadlost = App\Lead::where('employee_id',$ex->id)->where('active', 1)->where('status',5)->count();
								?>
								<tr>
									<td> {{ $ex->name }}</td>
									<td> {{ $leadsYesterday}}</td>
									<td> {{ $leadactive }}</td>
									<td> {{ $leadpending }}</td>
									<td> {{ $leaddiscuss }}</td>
									<td> {{ $leadrevision }}</td>
									<td> {{ $leadwin }}</td>
									<td> {{ $leadlost}}</td>
								</tr>
								@endforeach
								@endif
							</tbody>
						</table>
					</div>
				</div>
			</div>
			<div class="col-lg-4">
				<div class="kt-portlet__body kt-portlet kt-iconbox" style="background: #FFFFFF;">
					
					<div id="kt_table_1_wrapper" class="dataTables_wrapper dt-bootstrap4">
						<table class="table table-striped table-bordered table-hover table-checkable datatable" id="datatable_rows">
							@csrf
							<thead>
								<tr>
									<th>Lead Source</th>
									<th>Total Leads</th>
									<th>Win</th>
								</tr>
							</thead>
							<tbody>
								@if(count($source) > 0)
								@foreach($source as $so)
								<?php
								$sourceactive = App\Lead::where('source_name',$so->id)->where('active',1)->count();
								$sourcewin = App\Lead::where('source_name',$so->id)->where('active', 1)->where('status',4)->count();
								?>
								<tr>
									<td> {{ $so->name }}</td>
									<td> {{ $sourceactive }}</td>
									<td> {{ $sourcewin}}</td>
								</tr>
								@endforeach
								@endif
							</tbody>
						</table>
					</div>
				</div>
			</div>
			@endif
		</div>
	</div>
	@endif
	<div class="kt-container  kt-container--fluid  kt-grid__item kt-grid__item--fluid">
		<div class="row">
			<div class="col-sm-12">
				<div class="kt-portlet kt-portlet--mobile">
					<div class="kt-portlet__head kt-portlet__head--lg">
						<div class="kt-portlet__head-label">
							<span class="kt-portlet__head-icon">
								<i class="kt-font-brand flaticon2-line-chart"></i>
							</span>
							<h3 class="kt-portlet__head-title">
								Calender
							</h3>
							<br/>
							<br/>
							<div class="col-sm-8">
								@if(Auth::user()->role == 1)
								@php $user = App\Employee::where('status',1)->get(); @endphp
								@php $lsource = App\LeadSource::where('status',1)->get(); @endphp
								@else
								@php $user = App\Employee::where('branch_id',Auth::user()->id)->where('status',1)->get(); @endphp
								@php $lsource = App\LeadSource::where('branch_id',Auth::user()->id)->where('status',1)->get(); @endphp
								@endif
								<select class="form-control" id="employee" onchange="getdatas(this)">
									<option value="">Select Employee</option>
									@foreach($user as $key => $value)
									<option value="{{$value->id}}">{{$value->name}}</option>
									@endforeach
								</select>
							</div>
							<div class="col-sm-8">
							<select class="form-control" id="lesource" onchange="getdatas(this)">
								<option value="">Select LeadSource</option>
								@foreach($lsource as $key => $value)
								<option value="{{$value->id}}">{{$value->name}}</option>
								@endforeach
							</select>
						</div>
						</div>
					</div>
					<div class="kt-portlet__body">
						<div id="calendar"></div>
						<div id="modal-view-event" class="modal modal-top fade calendar-modal">
							<div class="modal-dialog modal-dialog-centered">
								<div class="modal-content">
									<div class="modal-body">
										<h4 class="modal-title"><span class="event-icon"></span><span class="event-title"></span></h4>
										<div class="event-body">
											<h6 id='lead_name'></h6>
											<h6 id='lead_date'>Lead Date</h6>
											<h6 id='lead_time'></h6>
											
										</div>
									</div>
									<div class="modal-footer">
										<button type="button" class="btn btn-primary" data-dismiss="modal">Close</button>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
@endif
<script src="{{asset('assets/plugins/custom/fullcalendar/fullcalendar.bundle.js')}}" type="text/javascript"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/3.10.2/fullcalendar.min.js" integrity="sha512-o0rWIsZigOfRAgBxl4puyd0t6YKzeAw9em/29Ag7lhCQfaaua/mDwnpE2PVzwqJ08N7/wqrgdjc2E0mwdSY2Tg==" crossorigin="anonymous"></script>
<script type="text/javascript">
	$(document).ready(function() {
		
		var oTable = $('#datatable_rows111').DataTable({
			processing: true,
			serverSide: true,
			searchable: true,
			scrollX: true,
			stateSave: true,
			columnDefs: [{
				orderable: false,
				targets: -1,
			}],
			ajax: {
				url:'{{ route("user.sales.report") }}',
				data: function(d) {
					d.branch_id = $('#branch_id').val()
				}
			},
			columns: [
			{
				orderable: true,
				searchable: true,
				data: "name"
			},
			{
				orderable: true,
				searchable: true,
				data: "leadsYesterday"
			},
			{
				orderable: true,
				searchable: true,
				data: "leadactive"
			},
			{
				orderable: true,
				searchable: true,
				data: "leadpending"
			},
			{
				orderable: true,
				searchable: true,
				data: "leaddiscuss"
			},
			{
				orderable: true,
				searchable: true,
				data: "leadrevision"
			},
			{
				orderable: true,
				searchable: true,
				data: "leadwin"
			},
			{
				orderable: true,
				searchable: true,
				data: "leadlost"
			},
			]
		});
		$('.changes').change(function(){
			
			oTable.draw();
			
		});
	});
</script>

<script type="text/javascript">
	$(document).ready(function() {
		
		var oTable = $('#datatable_rows').DataTable({
			processing: true,
			serverSide: true,
			searchable: true,
			scrollX: true,
			stateSave: true,
			columnDefs: [{
				orderable: false,
				targets: -1,
			}],
			ajax: {
				url:'{{ route("user.leadsource.report") }}',
				data: function(d) {
					d.branch_ids = $('#branch_ids').val()
				}
			},
			columns: [
			{
				orderable: true,
				searchable: true,
				data: "name"
			},
			{
				orderable: true,
				searchable: true,
				data: "sourceactive"
			},
			{
				orderable: true,
				searchable: true,
				data: "sourcewin"
			},
			
			]
		});
		$('.changes').change(function(){
			
			oTable.draw();
			
		});
	});
</script>

<script type="text/javascript">
	$(function () {
		var event = <?php echo json_encode($dt_event);?>;
		// console.log('========>',event)
		
		
		$("#calendar").fullCalendar({
			themeSystem: "bootstrap4",
			businessHours: false,
			defaultView: "month",
			editable: true,
			header: {
				left: "title",
				right: "",
				center: "today prev,next"
			},
			events: event,
			eventLimit: true,
			eventRender: function (event, element) {
				if (event.icon) {
					element
					.find(".fc-title")
					.prepend("<i class='fa fa-" + event.icon + "'></i>");
				}
			},
		});
	});
	function edit(date){
		$.ajax({
			type: "post",
			url: "{{ route('user.lead.event') }}",
			data: {
				'_token': $('input[name="_token"]').val(),
				'date': date
			},
			success: function (data)
			{
				$('#lead_date').html('');
				$('#lead_date').html(data.data.date);
			}
		});
	}
	// getdata();
	function getdata()
	{
		var employee = $('#employee').val();
		var lesource = $('#lesource').val();
		console.log(lesource);
		$.ajax({
			type: "post",
			url: "{{ route('user.dashboard.event') }}",
			data: {
				'_token': $('input[name="_token"]').val(),
				'employee': employee,
				'lesource' : lesource
			},
			dataType:'JSON',
			success: function (data)
			{
				console.log('testsss=======',data.data)
				var event = data.data.dt_event;
				$('.fc-event').remove();
				$('#calendar').fullCalendar( 'removeEvents');
				$('#calendar').fullCalendar('removeEventSources'); 
				$('#total_followup').html(data.data.today_followup);
				$('#total_lead').html(data.data.total_lead);
				$('#total_lead_today').html(data.data.total_lead_today);
				$('#total_lead_month').html(data.data.total_lead_month);
				$('#total_weekly').html(data.data.weekly);
				// $('#calendar').fullCalendar( 'events', '');
				$('#calendar').fullCalendar( 'addEventSource', event);
			}
		});
		
	}

	function getdatas()
	{
		var employee = $('#employee').val();
		var lesource = $('#lesource').val();
		console.log(lesource);
		$.ajax({
			type: "post",
			url: "{{ route('user.dashboardtest.eventtest') }}",
			data: {
				'_token': $('input[name="_token"]').val(),
				'employee': employee,
				'lesource' : lesource
			},
			dataType:'JSON',
			success: function (data)
			{
				console.log('testsss=======',data.data)
				var event = data.data.dt_event;
				$('.fc-event').remove();
				$('#calendar').fullCalendar( 'removeEvents');
				$('#calendar').fullCalendar('removeEventSources'); 
				$('#total_followup').html(data.data.today_followup);
				$('#total_lead').html(data.data.total_lead);
				$('#total_lead_today').html(data.data.total_lead_today);
				$('#total_lead_month').html(data.data.total_lead_month);
				$('#total_weekly').html(data.data.weekly);
				// $('#calendar').fullCalendar( 'events', '');
				$('#calendar').fullCalendar( 'addEventSource', event);
			}
		});
		
	}
</script>
@endsection