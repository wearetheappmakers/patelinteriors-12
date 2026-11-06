@extends('main')
@section('content')
<meta name="csrf-token" content="{{ csrf_token() }}" />
<div class="kt-container  kt-container--fluid  kt-grid_item kt-grid_item--fluid">
	<br>
	<div class="kt-portlet kt-portlet--mobile">
		<div class="kt-portlet__head">
			<div class="kt-portlet__head-label">
				<h3 class="kt-portlet__head-title">
					Yesterday Followup Report
				</h3>
			</div>
			<div class="kt-portlet__head-toolbar">
				<div class="kt-portlet__head-wrapper">

				</div>
			</div>
		</div>
		<div class="kt-portlet__body">
			<form class="kt-form kt-form--label-right add_form" id="add_form" >
				@csrf
				<div class="row">
					<div class="col-lg-2">
						<label>From Date</label>
						<input type="date" name="from_date" class="form-control filter" id="from_date">
					</div>
					<div class="col-lg-2">
						<label>To Date</label>
						<input type="date" name="to_date" class="form-control filter" id="to_date">
					</div>
					@if(Auth::guard('user')->check())
					@if(Auth::user()->role == 1)
					<div class="form-group col-lg-2">
						<label>Branch </label> 
						<select name="branch_id" class="form-control changes select2" onchange="getdata(this)" id="branch_id">
							<option value="">Select Branch</option>
							@foreach($branch as $sub)
							<option value="{{$sub->id}}">{{$sub->name}}</option>
							@endforeach
						</select>
					</div>        
					<div class="form-group col-lg-2">
						<label>Employee </label>
						<select name="employee_id" class="form-control changes select2" id="employee_id">
							<option value="">Select Employee</option>
						</select>
					</div>
					<div class="form-group col-lg-2">
						<label>Client</label> 
						<select name="client_id" class="form-control changes select2" id="client_id">
							<option value="">Select</option>
						</select>
					</div>
					<div class="form-group col-lg-2">
						<label>Source </label>
						<select name="source_id" id="source_id" class="form-control changes select2" >
							<option value="">Select</option>
						</select>
					</div>  
					<div class="form-group col-lg-2">
						<label>Source Name</label>
						<select name="source_name" id="source_name" class="form-control changes select2" >
							<option value="">Select</option>
						</select>
					</div>
					@endif
					@endif	
					@if(Auth::guard('user')->check())
					@if(Auth::user()->role == 2)
					<div class="form-group col-lg-2">
						<label>Client</label> 
						<select name="client_id" class="form-control changes select2" id="client_id">
							<option value="">Select</option>
							@foreach($client as $sub)
							<option value="{{$sub->id}}">{{$sub->member}}</option>
							@endforeach
						</select>
					</div>
					@endif
					@endif
					@if(Auth::guard('employee')->check())
					<div class="form-group col-lg-2">
						<label>Client</label> 
						<select name="client_id" class="form-control changes select2" id="client_id">
							<option value="">Select</option>
							@foreach($client as $sub)
							<option value="{{$sub->id}}">{{$sub->member}}</option>
							@endforeach
						</select>
					</div>
					@endif
					@if(Auth::guard('user')->check())
					@if(Auth::user()->role == 2)
					<div class="form-group col-lg-2">
						<label>Source </label>
						<select name="source_id" id="source_id" class="form-control changes select2" >
							<option value="">Select</option>
							@foreach($source as $sub)
							<option value="{{$sub->id}}">{{$sub->name}}</option>
							@endforeach
						</select>
					</div>        
					<div class="form-group col-lg-2">
						<label>Source Name</label>
						<select name="source_name" id="source_name" class="form-control changes select2" >
							<option value="">Select</option>
							@foreach($sourcename as $sub)
							<option value="{{$sub->id}}">{{$sub->name}}</option>
							@endforeach
						</select>
					</div>
					@endif
					<?php $employees = App\Employee::where('branch_id',Auth::user()->id)->get(); ?>
					@if(Auth::user()->role == 2)
					<div class="form-group col-lg-2">
						<label>Employee </label>
						<select name="employee_id" class="form-control changes select2" id="employee_id">
							<option value="">Select Employee</option>
							@foreach($employees as $sub)
							<option value="{{$sub->id}}">{{$sub->name}}</option>
							@endforeach
						</select>
					</div>
					@endif
					@endif
					@if(Auth::guard('employee')->check())

					<div class="form-group col-lg-2">
						<label>Source </label>
						<select name="source_id" id="source_id" class="form-control changes select2" >
							<option value="">Select</option>
							@foreach($source as $sub)
							<option value="{{$sub->id}}">{{$sub->name}}</option>
							@endforeach
						</select>
					</div>        
					<div class="form-group col-lg-2">
						<label>Source Name</label>
						<select name="source_name" id="source_name" class="form-control changes select2" >
							<option value="">Select</option>
							@foreach($sourcename as $sub)
							<option value="{{$sub->id}}">{{$sub->name}}</option>
							@endforeach
						</select>
					</div>
					@endif

					@if(Auth::user()->role == 2)
					<div class="form-group col-lg-12">
						<center>
							<button type="button" id="excelexport" title="Export Excel" class="btn btn-brand btn-elevate btn-icon-sm">
								<i class="flaticon-doc" ></i>Export
							</button> &nbsp;&nbsp;
							<button type="button" id="pdfexport"  title="Export PDF" class="btn btn-brand btn-elevate btn-icon-sm">
								<i class="fas fa-print"></i>Pdf
							</button>
						</center>
					</div>
					@else
					<div class="form-group col-lg-2" style="margin-top: 30px !important;">
						<button type="button" id="excelexport" title="Export Excel" class="btn btn-brand btn-elevate btn-icon-sm">
							<i class="flaticon-doc" ></i>Export
						</button> &nbsp;&nbsp;
						<button type="button" id="pdfexport"  title="Export PDF" class="btn btn-brand btn-elevate btn-icon-sm">
							<i class="fas fa-print"></i>Pdf
						</button>
					</div>
					@endif
				</div>
			</form>
			<div id="kt_table_1_wrapper" class="dataTables_wrapper dt-bootstrap4">
				<table class="table table-striped table-bordered table-hover table-checkable datatable"  id="datatable_rows">
					@csrf
					<thead>
						<tr>
							@if(Auth::guard('user')->check())
							@if(Auth::user()->role == 1)
							<th>Branch</th>
							<th>Employee</th>
							@endif
							@if(Auth::user()->role == 2)
							<th>Employee</th>
							@endif
							@endif
							<th>Client</th>
							<th>Source</th>
							<th>Source Name</th>
							<th>Lead Date</th>
							<th>Subject</th>
							<th>Description</th>
							<th>Time</th>
							<th>Type</th>
							<th>Status</th>
						</tr>
					</thead>
					<tbody>
					</tbody>
				</table>
			</div>
		</div>
		@include('layouts.multiple_action', array(
			'table_name' => 'gst',
			'is_orderby'=>'',
			'folder_name'=>'',
			'action' => array('change-status-1' => __('Active'), 'change-status-0' => __('Inactive'))
			))
		</div>
	</div>
	@stop
	@push('scripts')
	<script>
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
					url:'{{ route('user.yesterday.followup.report') }}',
					data: function(d) {
						d.branch_id= $('#branch_id').val(),
						d.employee_id= $('#employee_id').val(),				
						d.from_date= $('#from_date').val(),				
						d.to_date= $('#to_date').val(),				
						d.client_id= $('#client_id').val(),		
						d.source_id= $('#source_id').val(),		
						d.source_name= $('#source_name').val()
						d.lead_name= $('#lead_name').val()					
						d.company_name= $('#company_name').val()		
					}
				},		

				columns: [
					@if(Auth::guard('user')->check())
					@if(Auth::user()->role == 1)
					{
						orderable: false,
						searchable: true,
						data: "branch"
					},
					{
						orderable: false,
						searchable: true,
						data: "employee",
					},
					@endif
					@if(Auth::user()->role == 2)
					{
						orderable: false,
						searchable: true,
						data: "employee",
					},
					@endif
					@endif
					{
						orderable: true,
						searchable: true,
						data: "client"
					},
					{
						orderable: true,
						searchable: true,
						data: "source"
					},
					{
						orderable: true,
						searchable: true,
						data: "sourcename"
					},
					{
						orderable: true,
						searchable: true,
						data: "leaddate"
					},
					{
						orderable: true,
						searchable: true,
						data: "subject"
					},
					{
						orderable: true,
						searchable: true,
						data: "description"
					},
					{
						orderable: true,
						searchable: true,
						data: "time"
					},
					{
						orderable: true,
						searchable: true,
						data: "type"
					},
					{
						orderable: true,
						searchable: true,
						data: "status"
					},
				]
			});
			$('.changes').change(function(){

				oTable.draw();

			});
			$('.filter').change(function(){

				oTable.draw();

			});
		});
		$('.changes').select2({
		});
		function getdata($this){
			var branch_id = $($this).val();
			var id = $($this).data('id');
			$.ajax({
				type:"POST",
				headers: {
					'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
				},
				url:"{{ route('user.report.getdata') }}",
				data: {
					'branch_id': branch_id
				},
				success: function(data){
					$('#employee_id').html(data.data);
					$('#client_id').html(data.data1);
					$('#source_id').html(data.data2);
					$('#source_name').html(data.data3);
					$('#lead_name').html(data.data4);
					$('#company_name').html(data.data5);  
				}
			});
		};
		$(document).ready(function() {
			$("#excelexport").on("click", function(e) {
				e.preventDefault();
				if ($(".add_form").valid()) {
					$('.loader-wrapper').show();
					$('.loader').show();
					$.ajax({
						type: "POST",
						url: "{{ route('user.yesterdayreport.export') }}",
						data: new FormData($('.add_form')[0]),
						xhrFields: {
							responseType: 'blob',
						},
						processData: false,
						contentType: false,
						success: function(blob) {
							var link=document.createElement('a');
							link.href=window.URL.createObjectURL(blob);
							link.download = 'YesterdayFollowupReport.xlsx'
							link.click();
							$('.loader-wrapper').hide();
							$('.loader').hide();
						},
					});
				} else {
					e.preventDefault();
					$('.loader-wrapper').hide();
					$('.loader').hide();
				}
			});
		});
		$(document).ready(function() {
			$("#pdfexport").on("click", function(e) {
				e.preventDefault();
				if ($(".add_form").valid()) {
					$('.loader-wrapper').show();
					$('.loader').show();
					$.ajax({
						type: "POST",
						url: "{{ route('user.yesterdayreport.pdf') }}",
						data: new FormData($('.add_form')[0]),
						xhrFields: {
							responseType: 'blob',
						},
						processData: false,
						contentType: false,
						success: function(blob) {
							var link=document.createElement('a');
							link.href=window.URL.createObjectURL(blob);
							link.download = 'YesterdayFollowupReport.pdf'
							link.click();
							$('.loader-wrapper').hide();
							$('.loader').hide();
						},
					});
				} else {
					e.preventDefault();
					$('.loader-wrapper').hide();
					$('.loader').hide();
				}
			});
		});
	</script>
	@endpush