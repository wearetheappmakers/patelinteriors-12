@extends('main')
@section('content')
<meta name="csrf-token" content="{{ csrf_token() }}" />
<div class="kt-container  kt-container--fluid  kt-grid__item kt-grid__item--fluid">
	<br>
	<div class="kt-portlet kt-portlet--mobile">
		<div class="kt-portlet__head">
			<div class="kt-portlet__head-label">
				<span class="kt-portlet__head-icon">
					<i class="kt-font-brand"></i>
				</span>
				<h3 class="kt-portlet__head-title">
					Interior Visit
				</h3>
			</div>
		</div>
		<div class="kt-portlet__body">
			<form class="kt-form kt-form--label-right add_form" id="add_form">
				@csrf
				<div class="row">
					@if(Auth::guard('user')->check())
					@if(Auth::user()->role == 1 || Auth::user()->role == 2)
					<div class="col-lg-2">
						<label>From Date</label>
						<input type="date" name="from_date" class="form-control filter" id="from_date">
					</div>
					<div class="col-lg-2">
						<label>To Date</label>
						<input type="date" name="to_date" class="form-control filter" id="to_date">
					</div>
					@endif				
					@endif
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
							<option value="">Select</option>
						</select>
					</div>
					<div class="form-group col-lg-2">
						<label>Architecture</label>
						<select name="source_name" id="source_name" class="form-control changes select2">
							<option value="">Select</option>
						</select>
					</div>	
					@endif
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
					@if(Auth::guard('user')->check())
					@if(!(Auth::user()->role == 1))
					<div class="form-group col-lg-2">
						<label>Architecture</label>
						<select name="source_name" id="source_name" class="form-control changes select2">
							<option value="">Select</option>
							@foreach($sourcename as $sub)
							<option value="{{$sub->id}}">{{$sub->name}}</option>
							@endforeach
						</select>
					</div>	
					@endif
					@endif	
					<div class="form-group col-lg-2" style="margin-top: 30px !important;">
						<button type="button" id="interiorvisit" class="btn btn-brand btn-elevate btn-icon-sm">
							<i class="flaticon-doc"></i>
							Export
						</button>&nbsp;&nbsp;
						<button type="button" id="pdfexport" class="btn btn-brand btn-elevate btn-icon-sm">
							<i class="fas fa-print"></i>
							PDF
						</button>
					</div>	
				</div>
			</form>
			<div class="kt-portlet__body">
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
								<th>Architect</th>
								<th>Date</th>
								<th>Action</th>
							</tr>
						</thead>
					</table>
				</div>
			</div>
			@include('layouts.multiple_action', array(
				'table_name' => 'interior_visit',
				'is_orderby'=>'',
				'folder_name'=>'',
				'action' => array()
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
						url:'{{ route("user.visitreport.index") }}',
						data: function(d) {
							d.branch_id= $('#branch_id').val(),
							d.employee_id= $('#employee_id').val(),				
							d.from_date= $('#from_date').val(),				
							d.to_date= $('#to_date').val(),	
							d.source_name= $('#source_name').val()		
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
							data: "architect_id"
						},
						{
							orderable: false,
							searchable: true,
							data: "date"
						},
						{
							orderable: false,
							searchable: true,
							data: "action"
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
		</script>
		<script type="text/javascript">
			function statuschange($this,id)
			{
				var status = $($this).val();
				$.ajax({
					type: 'POST',
					url: '{{ route("user.change.status") }}',
					data: {
						'_token': '{{ csrf_token() }}',
						id: id,
						status: status,
					},
					dataType: 'json',
					success: function(data) {
						if (data.status == 'success') {
							toastr["success"]("Status Changed successfully", "Success");
							location.reload();
						} 
						else 
						{
							toastr["error"]("Something went wrong!", "Error");
							location.reload();
						}
					}
				});
			}
		</script>
		<script type="text/javascript">
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
						$('#source_name').html(data.data3);
					}
				});
			};
			$(document).ready(function() {
				$("#interiorvisit").on("click", function(e) {
					e.preventDefault();
					if ($(".add_form").valid()) {
						$('.loader-wrapper').show();
						$('.loader').show();
						$.ajax({
							type: "POST",
							url: "{{ route('user.interiorvisit.export') }}",
							data: new FormData($('#add_form')[0]),
							processData: false,
							contentType: false,
							xhrFields: {
								responseType: 'blob'
							},
							success: function(blob) {
								var link=document.createElement('a');
								link.href=window.URL.createObjectURL(blob);
								link.download = 'InteriorVisit.xlsx';
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
							url: "{{ route('user.interiorvisit.pdf') }}",
							data: new FormData($('.add_form')[0]),
							xhrFields: {
								responseType: 'blob',
							},
							processData: false,
							contentType: false,
							success: function(blob) {
								var link=document.createElement('a');
								link.href=window.URL.createObjectURL(blob);
								link.download = 'InteriorVisit.pdf'
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