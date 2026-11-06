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
		/* Smaller event text */
		.fc-event,
		.fc-event .fc-title {
			font-size: 11px !important;
			line-height: 1.2;
		}

/* Icon size smaller */
.fc-event i {
    font-size: 10px !important;
    margin-right: 4px;
}

/* Reduce event padding */
.fc-day-grid-event {
    padding: 2px 4px !important;
}

/* Reduce event height */
.fc-event {
    min-height: unset !important;
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

</style>
<?php
if(Auth::guard('employee')->check())
{
	$data = App\LeadAssign::get();
	$array = [];
	foreach($data as $dt)
	{
		$ex_arr = explode(',', $dt->employee_id);
		if(in_array(Auth::user()->id,$ex_arr))
		{
			array_push($array,$dt->lead_id);
		}
	}
	$total_lead = App\Lead::orderBy('date','DESC')->orWhereIn('id',$array)->where('active',1)->count();
	$total_lead_today = App\Lead::orWhereIn('id',$array)->where('active',1)->whereDate('date', date('Y-m-d'))->count();
	$total_lead_month = App\Lead::orWhereIn('id',$array)->where('active',1)->whereMonth('date', date('m'))->whereYear('date', date('Y'))->count();

	$total_quotation = App\Quotation::where('employee_id',Auth::user()->id)->count();
	$total_quotation_today = App\Quotation::where('employee_id',Auth::user()->id)->whereDate('quotation_date', date('Y-m-d'))->count();
	$total_quotation_month = App\Quotation::where('employee_id',Auth::user()->id)->whereMonth('quotation_date', date('m'))->whereYear('quotation_date', date('Y'))->count();

	$total_iv = App\Visits::where('employee_id',Auth::user()->id)->count();
	$total_iv_today = App\Visits::where('employee_id',Auth::user()->id)->whereDate('date', date('Y-m-d'))->count();
	$total_iv_month = App\Visits::where('employee_id',Auth::user()->id)->whereMonth('date', date('m'))->whereYear('date', date('Y'))->count();

	$total_service = App\Service::where('employee_id',Auth::user()->id)->count();
	$total_service_today = App\Service::where('employee_id',Auth::user()->id)->whereDate('register_date', date('Y-m-d'))->count();
	$total_service_month = App\Service::where('employee_id',Auth::user()->id)->whereMonth('register_date', date('m'))->whereYear('register_date', date('Y'))->count();

	$total_attendance = App\Attendance::where('employee_id',Auth::user()->id)->count();
	$total_attendance_today = App\Attendance::where('employee_id',Auth::user()->id)->whereDate('date', date('Y-m-d'))->count();
	$total_attendance_month = App\Attendance::where('employee_id',Auth::user()->id)->whereMonth('date', date('m'))->whereYear('date', date('Y'))->count();
}
?>
<link href="{{ asset('assets/plugins/custom/datatables/datatables.bundle.css')}}" rel="stylesheet" type="text/css" />
<link href="{{asset('assets/plugins/custom/fullcalendar/fullcalendar.bundle.css')}}" rel="stylesheet" type="text/css">
<div class="kt-content  kt-grid__item kt-grid__item--fluid kt-grid kt-grid--hor" id="kt_content">
	<br>
	<div class="kt-container  kt-container--fluid  kt-grid__item kt-grid__item--fluid">
		
		<div id="kanban2">
			<div class="kanban-container" style="width: 1250px;">
				@if(Auth::user()->role == 1 || Auth::user()->role == 2)
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
				@endif
				@if(Auth::user()->role == 1)
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
				@endif
				@if(Auth::user()->role == 1 || Auth::user()->role == 2)
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
				@endif
				@if(Auth::user()->role == 3)
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
				@endif
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
	<div class="kt-container kt-container--fluid kt-grid__item kt-grid__item--fluid">

    <!-- ROW 1: Calendar (12) -->
    <div class="row">
        <div class="col-md-12">
            <div class="kt-portlet kt-portlet--mobile">
                <div class="kt-portlet__head kt-portlet__head--lg">
                    <div class="kt-portlet__head-label">
                        <h3 class="kt-portlet__head-title">Calendar</h3>
                    </div>
                </div>
                <div class="kt-portlet__body">
                    <div id="calendar"></div>
                </div>
            </div>
        </div>
    </div>

    <!-- ROW 2: Meeting + Travel (6 + 6) -->
    <div class="row mt-4">

        <!-- Meeting Calendar -->
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

    </div>

</div>

        <!-- Holiday Calendar -->
        <!-- <div class="col-md-6">
            <div class="kt-portlet kt-portlet--mobile">
                <div class="kt-portlet__head kt-portlet__head--lg">
                    <div class="kt-portlet__head-label">
                        <h3 class="kt-portlet__head-title">Holiday Calendar</h3>
                    </div>
                </div>
                <div class="kt-portlet__body">
                    <div id="holidayCalendar"></div>
                </div>
            </div>
        </div> -->

    </div>
</div>

</div>
<script src="{{asset('assets/plugins/custom/fullcalendar/fullcalendar.bundle.js')}}" type="text/javascript"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/3.10.2/fullcalendar.min.js" integrity="sha512-o0rWIsZigOfRAgBxl4puyd0t6YKzeAw9em/29Ag7lhCQfaaua/mDwnpE2PVzwqJ08N7/wqrgdjc2E0mwdSY2Tg==" crossorigin="anonymous"></script>

<script type="text/javascript">
	
	$(function () {
		var event1 = <?php echo json_encode($event1);?>;
		var event2 = <?php echo json_encode($event2);?>;
		var event = new Array();
		var event_array = new Array();
		var event_array1 = new Array();
		var event_array2 = new Array();
		$.each( event1, function( val, i ) {
			$.each( i.Lead, function(value,index) {
				var Lead = '';
				if(index.lead.client)
				{
					Lead =  index.lead.client.member;
				}
				var id = index.lead.id;
				var url =  '{{ route("user.lead.edit", ":id") }}';
				url = url.replace(':id', id);
				event_array1.push({
					title: Lead,
					start: val,
					end: val,
					icon: "clock",
					url: url,
					className: 'eventClass'
				});
			})
		});
		$.each( event2, function( val, i ) {
			$.each( i.IV, function(value,index) {
				var IV = '';
				if(index.employee.name)
				{
					IV =  'IV-'+index.employee.name;
				}
				var id = index.employee.id;
				var url =  '';
				event_array2.push({
					title: IV,
					start: val,
					end: val,
					icon: "clock",
					url: url,
					className: 'eventClass1'
				});
			})
		});
		event_array.push(event_array1);
		event_array.push(event_array2);
		$.each(event_array,function(val,i){
			$.each(i,function(index,value){
				event.push(value);
			});
		});
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
			eventLimit: 2,
			eventRender: function (event, element) {
				if (event.icon) {
					element
					.find(".fc-title")
					.prepend("<i class='fa fa-" + event.icon + "'></i>");
				}
			},
  		// eventMouseover: function (event, jsEvent) {
  		// 	console.log(event);
  		// 	$(this).popover({
  		// 		title: event.start._i,
  		// 		placement: 'right',
  		// 		trigger: 'manual',
  		// 		content: event.title,
  		// 		container: '#calendar'
  		// 	}).popover('toggle');
  		// 	edit(event.start._i);
  		// },
  		// eventClick: function (event, jsEvent, view) {
  		// 	$("#modal-view-event").modal();
  		// 	edit(event.start._i);
  		// }
		});
	});
	
<?php

if (Auth::user()->role == 9) {
   
    $meeting_data = App\MeetingEntryEmp::all();
   
} else {
	 $meeting_data = App\MeetingEntryEmp::where('employee_id', Auth::user()->id)->get();
    
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

    height: 700,
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
        // Append the 12-hour time below event title
        if (event.time) {
            element.find('.fc-title')
                .append("<br><span style='font-size:5px;'>" + event.time + "</span>");
        }
    }
});



<?php
if (Auth::user()->role == 9) {
	 $travelupdate_data = App\TravelUpdate::all();
   
} else {
    $travelupdate_data = App\TravelUpdate::where('employee_id', Auth::user()->id)->get();
	
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
		height: 750,
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

<?php

    $holiday_data = App\Holiday::get();


$holiday_events = [];

foreach ($holiday_data as $holiday) {
    $holiday_events[] = [
        "title" => "<div style='font-weight:bold;'>" . $holiday->name . "</div>",
        "start" => $holiday->from_date,
        "end"   => date('Y-m-d', strtotime($holiday->to_date . ' +1 day')), // FullCalendar excludes the end date, so add 1 day
        "icon"  => "calendar",
        "className" => "eventMeeting"
    ];
}
?>


    var holidayEvents = @json($holiday_events);

    $('#holidayCalendar').fullCalendar({
        themeSystem: "bootstrap4",
        businessHours: false,
        defaultView: "month",
        editable: false,
        header: {
            left: "title",
            center: "today prev,next",
            right: ""
        },
        events: holidayEvents,
        eventLimit: true,
        displayEventTime: false,
        eventRender: function (event, element) {
            if (event.icon) {
                element.find(".fc-title").html("<i class='fa fa-" + event.icon + "'></i> " + element.find(".fc-title").html());
            }
            element.find(".fc-title").html(event.title);
        }
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
	
</script>
@endsection