@extends('main')
@section('content')
<style type="text/css">
	.kanban-container {
		position: relative;
		-webkit-box-sizing: border-box;
		box-sizing: border-box;
		width: auto; 
	}
	.kanban-container * {
		-webkit-box-sizing: border-box;
		box-sizing: border-box; 
	}
	.kanban-container:after {
		clear: both;
		display: block;
		content: ""; 
	}
	.kanban-board {
		position: relative;
		float: left;
		background: #e2e4e6;
		-webkit-transition: all 0.3s cubic-bezier(0.23, 1, 0.32, 1);
		transition: all 0.3s cubic-bezier(0.23, 1, 0.32, 1); 
	}
	.kanban-board.disabled-board {
		opacity: .3; 
	}
	.kanban-board.is-moving.gu-mirror {
		-webkit-transform: rotate(3deg);
		transform: rotate(3deg); 
	}
	.kanban-board.is-moving.gu-mirror .kanban-drag {
		overflow: hidden;
		padding-right: 50px; 
	}
	.kanban-board header {
		font-size: 16px;
		padding: 15px; 
	}
	.kanban-board header .kanban-title-board {
		font-weight: 700;
		margin: 0;
		padding: 0;
		display: inline; 
	}
	.kanban-board header .kanban-title-button {
		float: right; 
	}
	.kanban-board .kanban-drag {
		min-height: 200px;
		padding: 20px; 
	}
	.kanban-board:after {
		clear: both;
		display: block;
		content: ""; 
	}
	.kanban-item {
		background: #fff;
		padding: 15px;
		margin-bottom: 20px;
		-webkit-transition: all 0.3s cubic-bezier(0.23, 1, 0.32, 1);
		transition: all 0.3s cubic-bezier(0.23, 1, 0.32, 1); 
	}
	.kanban-item:hover {
		cursor: move; 
	}
	.kanban-item:last-child {
		margin: 0; 
	}
	.kanban-item.is-moving.gu-mirror {
		-webkit-transform: rotate(3deg);
		transform: rotate(3deg);
		height: auto !important; 
	}
	.gu-mirror {
		position: fixed !important;
		margin: 0 !important;
		z-index: 9999 !important; 
	}
	.gu-hide {
		display: none !important; 
	}
	.gu-unselectable {
		-webkit-user-select: none !important;
		-moz-user-select: none !important;
		-ms-user-select: none !important;
		user-select: none !important; 
	}
	.gu-transit {
		opacity: 0.2 !important;
		-webkit-transform: rotate(0) !important;
		transform: rotate(0) !important; 
	}
	.drag_handler {
		background: #fff;
		border-radius: 50%;
		width: 24px;
		height: 24px;
		position: relative;
		float: left;
		top: -3px;
		margin-right: 4px; 
	}
	.drag_handler:hover {
		cursor: move; 
	}
	.drag_handler_icon {
		position: relative;
		display: block;
		background: #000;
		width: 24px;
		height: 2px;
		top: 12px;
		-webkit-transition: .5s ease-in-out;
		transition: .5s ease-in-out; 
	}
	.drag_handler_icon:after, .drag_handler_icon:before {
		background: #000;
		content: '';
		display: block;
		width: 100%;
		height: 100%;
		position: absolute;
		-webkit-transition: .5s ease-in-out;
		transition: .5s ease-in-out; 
	}
	.drag_handler_icon:before {
		top: 6px; 
	}
	.drag_handler_icon:after {
		bottom: 6px; 
	}
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
	/* Smaller event text */
	    .fc-title {
			font-size: 11px !important;
			line-height: 1.2;
		}
		.fc-event, 
    .fc-event .fc-content {
    display: flex !important;
    flex-direction: column; /* stack title + time vertically */
    justify-content: center; /* vertical center */
    align-items: flex-start; /* optional: left-align text */
    
    font-size: 10px; /* adjust as needed */
    line-height: 1.1;
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
		background-color: #96c627 !important;
		border: 1px solid #d5dbeb !important;
		
	}
	.eventClass1 i
	{
		color: black!important;
		padding: 5px !important;
		/*margin-left: 2px !important;*/
		
	}
	.eventMeeting {
	color: #000000 !important;
	background-color: #d3d3d3 !important;
	border: 1px solid #d3d3d3 !important;
}

.eventMeeting i {
	color: #D07090 !important;
	padding: 5px !important;
}
 
    .kt-portlet__head-label .row {
        display: flex !important;
        flex-wrap: nowrap !important;
        gap: 10px;
    }

    .kt-portlet__head-label .row > div {
        flex: 1;
    }


</style>
<?php

	if(Auth::user()->role == 2)
	{	
		$total_lead = App\Lead::where('branch_id',Auth::user()->id)->where('active',1)->count();
		$total_lead_today = App\Lead::whereDate('date',date('Y-m-d'))->where('branch_id',Auth::user()->id)->where('active',1)->count();
		$total_lead_month = App\Lead::whereMonth('date',date('m'))->whereYear('date', date('Y'))->where('branch_id',Auth::user()->id)->where('active',1)->count();

		$total_quotation = App\Quotation::where('branch_id',Auth::user()->id)->count();
		$total_quotation_today = App\Quotation::where('branch_id',Auth::user()->id)->whereDate('quotation_date', date('Y-m-d'))->count();
		$total_quotation_month = App\Quotation::where('branch_id',Auth::user()->id)->whereMonth('quotation_date', date('m'))->whereYear('quotation_date', date('Y'))->count();

		$total_service = App\Service::where('branch_id',Auth::user()->id)->count();
		$total_service_today = App\Service::where('branch_id',Auth::user()->id)->whereDate('register_date', date('Y-m-d'))->count();
		$total_service_month = App\Service::where('branch_id',Auth::user()->id)->whereMonth('register_date', date('m'))->whereYear('register_date', date('Y'))->count();

		$total_iv = App\Visits::where('branch_id',Auth::user()->id)->count();
		$total_iv_today = App\Visits::where('branch_id',Auth::user()->id)->whereDate('date', date('Y-m-d'))->count();
		$total_iv_month = App\Visits::where('branch_id',Auth::user()->id)->whereMonth('date', date('m'))->whereYear('date', date('Y'))->count();

		$total_attendance = App\Attendance::where('branch_id',Auth::user()->id)->count();
		$total_attendance_today = App\Attendance::where('branch_id',Auth::user()->id)->whereDate('date', date('Y-m-d'))->count();
		$total_attendance_month = App\Attendance::where('branch_id',Auth::user()->id)->whereMonth('date', date('m'))->whereYear('date', date('Y'))->count();

		$executive = App\Employee::where('branch_id',Auth::user()->id)->where('role',1)->where('status',1)->get();
		$source = App\LeadSource::where('branch_id',Auth::user()->id)->where('status',1)->get();
		
	}
	else
	{
		$total_lead = App\Lead::where('active',1)->count();
		$total_lead_today = App\Lead::whereDate('date', date('Y-m-d'))->where('active',1)->count();
		$total_lead_month = App\Lead::whereMonth('date', date('m'))->where('active',1)->whereYear('date', date('Y'))->count();

		$total_quotation = App\Quotation::count();
		$total_quotation_today = App\Quotation::whereDate('quotation_date', date('Y-m-d'))->count();
		$total_quotation_month = App\Quotation::whereMonth('quotation_date', date('m'))->whereYear('quotation_date', date('Y'))->count();

		$total_service = App\Service::count();
		$total_service_today = App\Service::whereDate('register_date', date('Y-m-d'))->count();
		$total_service_month = App\Service::whereMonth('register_date', date('m'))->whereYear('register_date', date('Y'))->count();

		$total_iv = App\Visits::count();
		$total_iv_today = App\Visits::whereDate('date', date('Y-m-d'))->count();
		$total_iv_month = App\Visits::whereMonth('date', date('m'))->whereYear('date', date('Y'))->count();

		$total_attendance = App\Attendance::count();
		$total_attendance_today = App\Attendance::whereDate('date', date('Y-m-d'))->count();
		$total_attendance_month = App\Attendance::whereMonth('date', date('m'))->whereYear('date', date('Y'))->count();
		
		$executive = App\Employee::where('role',1)->where('status',1)->get();
		$source = App\LeadSource::where('status',1)->get();
	}

?>
<link href="{{ asset('assets/plugins/custom/datatables/datatables.bundle.css')}}" rel="stylesheet" type="text/css" />
<link href="{{asset('assets/plugins/custom/fullcalendar/fullcalendar.bundle.css')}}" rel="stylesheet" type="text/css">
@if(!(Auth::user()->role == 5))
<div class="kt-content  kt-grid__item kt-grid__item--fluid kt-grid kt-grid--hor" id="kt_content">
	<br>
	<div class="kt-container  kt-container--fluid  kt-grid__item kt-grid__item--fluid">

		<div id="kanban2">
			<div class="kanban-container" style="width: 1250px;">
				<div data-id="_todo" data-order="1" class="kanban-board" style="width: 250px; margin-left: 0px; margin-right: 0px;">
					<header class="kanban-board-header brand">
						<div class="kanban-title-board">Total Lead <span style="float: right !important;">{{$total_lead}}</span>
						</div>
					</header>
					<main class="kanban-drag">
						<div class="kanban-item" data-class="info">Today <span style="float: right !important;">{{$total_lead_today}}</span>
						</div>
						<div class="kanban-item" data-class="info">Week <span style="float: right !important;">{{$weekly}}</span>
						</div>
						<div class="kanban-item" data-class="info">Month <span style="float: right !important;">{{$total_lead_month}}</span>
						</div>
						<!-- <div class="kanban-item" data-class="info">Followup <span style="float: right !important;">{{$today_followup}}</span>
						</div> -->
					</main>
					<footer>
					</footer>
				</div>
				<div data-id="_todo" data-order="1" class="kanban-board" style="width: 250px; margin-left: 0px; margin-right: 0px;">
					<header class="kanban-board-header brand">
						<div class="kanban-title-board">Total Quotation <span style="float: right !important;">{{$total_quotation}}</span>
						</div>
					</header>
					<main class="kanban-drag">
						<div class="kanban-item" data-class="info">Today <span style="float: right !important;">{{$total_quotation_today}}</span>
						</div>
						<div class="kanban-item" data-class="info">Week <span style="float: right !important;">{{$total_quotation_weekly}}</span>
						</div>
						<div class="kanban-item" data-class="info">Month <span style="float: right !important;">{{$total_quotation_month}}</span>
						</div>
					</main>
					<footer>
					</footer>
				</div>
				<div data-id="_todo" data-order="1" class="kanban-board" style="width: 250px; margin-left: 0px; margin-right: 0px;">
					<header class="kanban-board-header brand">
						<div class="kanban-title-board">Total Service <span style="float: right !important;">{{$total_service}}</span>
						</div>
					</header>
					<main class="kanban-drag">
						<div class="kanban-item" data-class="info">Today <span style="float: right !important;">{{$total_service_today}}</span>
						</div>
						<div class="kanban-item" data-class="info">Week <span style="float: right !important;">{{$total_service_weekly}}</span>
						</div>
						<div class="kanban-item" data-class="info">Month <span style="float: right !important;">{{$total_service_month}}</span>
						</div>
					</main>
					<footer>
					</footer>
				</div>
				<div data-id="_todo" data-order="1" class="kanban-board" style="width: 250px; margin-left: 0px; margin-right: 0px;">
					<header class="kanban-board-header brand">
						<div class="kanban-title-board">Total InteriorVisit <span style="float: right !important;">{{$total_iv}}</span>
						</div>
					</header>
					<main class="kanban-drag">
						<div class="kanban-item" data-class="info">Today <span style="float: right !important;">{{$total_iv_today}}</span>
						</div>
						<div class="kanban-item" data-class="info">Week <span style="float: right !important;">{{$total_iv_weekly}}</span>
						</div>
						<div class="kanban-item" data-class="info">Month <span style="float: right !important;">{{$total_iv_month}}</span>
						</div>
					</main>
					<footer>
					</footer>
				</div>
				<div data-id="_todo" data-order="1" class="kanban-board" style="width: 250px; margin-left: 0px; margin-right: 0px;">
					<header class="kanban-board-header brand">
						<div class="kanban-title-board">Attendance</div>
					</header>
					<main class="kanban-drag">
						<div class="kanban-item" data-class="info">Today <span style="float: right !important;">{{$total_attendance_today}}</span>
						</div>
						<div class="kanban-item" data-class="info">Week <span style="float: right !important;">{{$total_attendance_weekly}}</span>
						</div>
						<div class="kanban-item" data-class="info">Month <span style="float: right !important;">{{$total_attendance_month}}</span>
						</div>
					</main>
					<footer>
					</footer>
				</div>
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
								
							</tbody>
						</table>
					</div>
				</div>
			</div>
			
			@endif
		</div>
	</div>
	@endif
	<div class="kt-container kt-container--fluid kt-grid__item kt-grid__item--fluid">

    <!-- ================= ROW 1 ================= -->
    <div class="row">

        <!-- Lead Calendar -->
        <div class="col-md-12">
            <div class="kt-portlet kt-portlet--mobile">
                <div class="kt-portlet__head kt-portlet__head--lg">
                    <div class="kt-portlet__head-label">
                        <span class="kt-portlet__head-icon">
                            <i class="kt-font-brand flaticon2-line-chart"></i>
                        </span>
                        <h3 class="kt-portlet__head-title">Calendar</h3>&nbsp;&nbsp;&nbsp;&nbsp;

                        <div class="row mt-3">
                            <div class="col-sm-6">
                                @if(Auth::user()->role == 1)
                                    @php 
                                        $user = App\Employee::where('status',1)->get();
                                        $lsource = App\LeadSource::where('status',1)->get();
                                    @endphp
                                @else
                                    @php 
                                        $user = App\Employee::where('branch_id',Auth::user()->id)->where('status',1)->get();
                                        $lsource = App\LeadSource::where('branch_id',Auth::user()->id)->where('status',1)->get();
                                    @endphp
                                @endif

                                <select class="form-control select2" id="employee_data" onchange="getdata()">
                                    <option value="">Select Employee</option>
                                    @foreach($user as $value)
                                        <option value="{{ $value->id }}">{{ $value->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-6">
                                <select class="form-control select2" id="lesource" onchange="getdata()">
                                    <option value="">Select Lead Source</option>
                                    @foreach($lsource as $value)
                                        <option value="{{ $value->id }}">{{ $value->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                    </div>
                </div>

                <div class="kt-portlet__body">
                    <div id="calendar"></div>
                </div>
            </div>
        </div>
      
	</div>
	<div class="row mt-4">
        <div class="col-md-6">
            <div class="kt-portlet kt-portlet--mobile">
                <div class="kt-portlet__head kt-portlet__head--lg">
                    <div class="kt-portlet__head-label">
                        <h3 class="kt-portlet__head-title">Meeting Schedule</h3>
                    </div>
                </div>
                <div class="kt-portlet__body">
                    <div id="meetingCalendar"></div>
                </div>
            </div>
        </div>
        <!-- Travel Calendar -->
       <div class="col-md-6">
            <div class="kt-portlet kt-portlet--mobile">
                <div class="kt-portlet__head kt-portlet__head--lg">
                    <div class="kt-portlet__head-label">
                        <h3 class="kt-portlet__head-title">Travel Update</h3>
                    </div>
                </div>
                <div class="kt-portlet__body">
                    <div id="travelupdateCalendar"></div>
                </div>
            </div>
        </div>

        <!-- Empty column (future use) -->
        <div class="col-md-6"></div>

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
		console.log('========>',event)
		
		
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
		var employee = $('#employee_data').val();
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
				$('#calendar').fullCalendar( 'addEventSource', event);
				// $('#total_followup').html(data.data.today_followup);
				// $('#total_lead').html(data.data.total_lead);
				// $('#total_lead_today').html(data.data.total_lead_today);
				// $('#total_lead_month').html(data.data.total_lead_month);
				// $('#total_weekly').html(data.data.weekly);
				// $('#calendar').fullCalendar( 'events', '');
			}
		});
	}

<?php

if (Auth::user()->role == 2) {
    $meeting_data = App\MeetingEntryEmp::where('branch_id', Auth::user()->id)->get();
} else {
    $meeting_data = App\MeetingEntryEmp::all();
}

$meeting_events = [];

foreach ($meeting_data as $mt) {

    // Ensure sch_time is valid
    $sch_time = $mt->sch_time ?: '09:00:00';
    $time_12hr = date("h:i A", strtotime($sch_time));
    $end_time = date("H:i:s", strtotime($sch_time . ' +30 minutes'));

    $meeting_events[] = [
        "title" => $mt->client_name,
        "time"  => $time_12hr,
        "start" => $mt->sch_date . ' ' . $sch_time,
        "end"   => $mt->sch_date . ' ' . $end_time,
        "className" => "eventMeeting"
    ];
}

// Convert to JSON for JS
$meeting_events_json = json_encode($meeting_events);
?>
var meetingEvents = <?php echo $meeting_events_json; ?>;

$('#meetingCalendar').fullCalendar({
    themeSystem: "bootstrap4",
    defaultView: "month",

    height: 650,
    editable: false,

    eventLimit: true,          // enable "+X more"
    eventLimitClick: 'popover',
    displayEventTime: false,   // hide default time

    header: {
        left: "title",
        center: "today prev,next",
        right: ""
    },

    events: meetingEvents,

    eventRender: function(event, element) {
       
        if (event.time) {
            element.find('.fc-title')
                .append("<br><span style='font-size:5px;'>" + event.time + "</span>");
        }
    }
});



<?php
if (Auth::user()->role == 2) {
    $travelupdate_data = App\TravelUpdate::where('branch_id', Auth::user()->id)->get();
} else {
   
	 $travelupdate_data = App\TravelUpdate::all();
}
$travel_events = [];

foreach ($travelupdate_data as $tu) {
  
    $sch_time = $mt->sch_time ?: '09:00:00';

    // Format time in 12-hour format for display
    $time_12hr = date("h:i A", strtotime($sch_time));

    // Add 30 minutes to make end time > start
    $end_time = date("H:i:s", strtotime($sch_time . ' +30 minutes'));


    $travel_events[] = [
        // Use HTML to add a line break between time and client name
        
		
		"title_plain" => $tu->name, // plain text for FullCalendar
        "title" => $tu->name,       // can keep same for safety
		"time"        => $time_12hr,  
        "start" => $tu->sch_date . ' ' . $tu->sch_time,
        "end"   => $tu->sch_date . ' ' . $tu->sch_time,
        "icon"  => "users",
        "className" => "eventMeeting"
    ];
}
$travel_events_json = json_encode($travel_events);
?>
    var travelupdateEvents = @json($travel_events);

    $('#travelupdateCalendar').fullCalendar({
        themeSystem: "bootstrap4",

        businessHours: false,
        defaultView: "month",
        editable: false,
		height: 650,
        header: {
            left: "title",
            center: "today prev,next",
            right: ""
        },
         events: travelupdateEvents,
         eventLimit: true,          // enable "+X more"
         eventLimitClick: 'popover',
        displayEventTime: false,
        eventRender: function(event, element) {
        // Append the 12-hour time below event title
        if (event.time) {
            element.find('.fc-title')
                .append("<br><span style='font-size:5px;'>" + event.time + "</span>");
        }
    }
    });
</script>

@endsection