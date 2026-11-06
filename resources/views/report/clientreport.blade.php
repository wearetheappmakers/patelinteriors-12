@extends('main')
@section('content')
<meta name="csrf-token" content="{{ csrf_token() }}" />
<div class="kt-container  kt-container--fluid  kt-grid_item kt-grid_item--fluid">
	<br>
	<div class="kt-portlet kt-portlet--mobile">
		<div class="kt-portlet__head">
			<div class="kt-portlet__head-label">
				<h3 class="kt-portlet__head-title">
					Client Report
				</h3>
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
						<label>Landmark</label>
						<select name="landmark_id" class="form-control changes select2" id="landmark_id">
							<option value="">Select Landmark</option>

						</select>
					</div>
					@else
					@php $employee = App\Employee::where('branch_id',Auth::user()->id)->get(); @endphp
					<div class="form-group col-lg-2">
						<label>Employee </label>
						<select name="employee_id" class="form-control changes select2" id="employee_id">
							<option value="">Select Employee</option>
							@foreach($employee as $key => $value)
							<option value="{{$value->id}}">{{$value->name}}</option>
							@endforeach
						</select>
					</div>
					@php $landmark = App\Landmark::where('branch_id',Auth::user()->id)->get(); @endphp
					<div class="form-group col-lg-2">
						<label>Landmark</label>
						<select name="landmark_id" class="form-control changes select2" id="landmark_id">
							<option value="">Select Landmark</option>
							@foreach($landmark as $lan)
							<option value="{{$lan->id}}">{{$lan->name}}</option>
							@endforeach
						</select>
					</div>
					@endif
					@endif	
					@if(Auth::guard('employee')->check())
					<?php
					$emp = App\Employee::where('id',Auth::user()->id)->value('branch_id');
					$landmark = App\Landmark::where('branch_id',$emp)->get();
					?>
					<div class="form-group col-lg-2">
						<label>Landmark</label>
						<select name="landmark_id" class="form-control changes select2" id="landmark_id">
							<option value="">Select Landmark</option>
							@foreach($landmark as $lan)
							<option value="{{$lan->id}}">{{$lan->name}}</option>
							@endforeach
						</select>
					</div>
					@endif
					<div class="form-group col-lg-12" style="margin-top: 30px !important; text-align: center;">
						<button type="button" id="clientreport_excelexport" title="Export Excel" class="btn btn-brand btn-elevate btn-icon-sm">
							<i class="flaticon-doc" ></i>Export
						</button> &nbsp;&nbsp;
						<button type="button" class="btn btn-brand btn-elevate btn-icon-sm" onclick="location.reload()"><i class="flaticon2-refresh"></i>Refresh</button>
					</div>
				</div>
			</form>			
			<div id="kt_table_1_wrapper" class="dataTables_wrapper dt-bootstrap4">
				<table class="table table-striped table-bordered table-hover table-checkable datatable" id="datatable_rows">
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
							<th>Mobile</th>
							<th>Email</th>				
							<th>Landmark</th>				
						</tr>
					</thead>
					<tbody>
					</tbody>
				</table>
			</div>
		</div>
		@include('layouts.multiple_action', array(
			'table_name' => 'client',
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
					url:'{{ route("user.client.report") }}',
					data: function(d) {
						d.branch_id= $('#branch_id').val(),
						d.employee_id= $('#employee_id').val(),				
						d.landmark_id= $('#landmark_id').val(),				
						d.from_date= $('#from_date').val(),				
						d.to_date= $('#to_date').val()			
					}
				},		
				columns: [

					@if(Auth::guard('user')->check())
					@if(Auth::user()->role == 1)
					{
						orderable: false,
						searchable: true,
						data: "branch_id"
					},
					{
						orderable: false,
						searchable: true,
						data: "employee_id",
					},
					@endif
					@if(Auth::user()->role == 2)
					{
						orderable: false,
						searchable: true,
						data: "employee_id",
					},
					@endif
					@endif
					{
						orderable: false,
						searchable: true,
						data: "member"
					},
					{
						orderable: true,
						searchable: true,
						data: "mobile_no"
					},
					{
						orderable: true,
						searchable: true,
						data: "email"
					},
					{
						orderable: true,
						searchable: true,
						data: "landmark_id"
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
					$('#landmark_id').html(data.data6); 
				}
			});
		};
		$(document).ready(function() {
			$("#excelexport").on("click", function(e) {
				e.preventDefault();
				if ($(".add_form").valid()) {
					$.ajax({
						type: "POST",
						url: "{{ route('user.leadreport.export') }}",
						data: new FormData($('.add_form')[0]),
						xhrFields: {
							responseType: 'blob',
						},
						processData: false,
						contentType: false,
						success: function(blob) {
							var link=document.createElement('a');
							link.href=window.URL.createObjectURL(blob);
							link.download = 'LeadReport.xlsx'
							link.click();
						},
					});
				} else {
					e.preventDefault();
				}
			});
		});
		$(document).ready(function() {
			$("#pdfexport").on("click", function(e) {
				e.preventDefault();
				if ($(".add_form").valid()) {
					$.ajax({
						type: "POST",
						url: "{{ route('user.leadreport.pdf') }}",
						data: new FormData($('.add_form')[0]),
						xhrFields: {
							responseType: 'blob',
						},
						processData: false,
						contentType: false,
						success: function(blob) {
							var link=document.createElement('a');
							link.href=window.URL.createObjectURL(blob);
							link.download = 'LeadReport.pdf'
							link.click();
						},
					});
				} else {
					e.preventDefault();
				}
			});

			$("#clientreport_excelexport").on("click", function(e) {
				e.preventDefault();
				var from_date = $('#from_date').val();
				var to_date = $('#to_date').val();
				var branch_id = $('#branch_id').val();
				var employee_id = $('#employee_id').val();
				var landmark_id = $('#landmark_id').val();
				if ($(".add_form").valid()) {
					showLoader();
					$.ajax({
						type: "POST",
						url: "{{ route('user.client.report.export') }}",
						data: new FormData($('.add_form')[0]),
						xhrFields: {
							responseType: 'blob',
						},
						processData: false,
						contentType: false,
						success: function(blob) {
							hideLoader(); 
							var link=document.createElement('a');
							link.href=window.URL.createObjectURL(blob);
							link.download = 'client_report_export.xlsx'
							link.click();
						},
					});
				} else {
					e.preventDefault();
				}
			});
		});
	</script>
	@endpush