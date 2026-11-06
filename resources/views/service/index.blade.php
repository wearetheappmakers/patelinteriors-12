@extends('main')
@section('content')
<meta name="csrf-token" content="{{ csrf_token() }}" />
<div class="kt-container  kt-container--fluid  kt-grid_item kt-grid_item--fluid">
	<br>
	<div class="kt-portlet kt-portlet--mobile">
		<div class="kt-portlet__head">
			<div class="kt-portlet__head-label">
				<h3 class="kt-portlet__head-title">
					Service
				</h3>
			</div>
			<div class="kt-portlet__head-toolbar">
				<div class="kt-portlet__head-wrapper">
					<div class="kt-portlet__head-actions">
						<a href="{{ route('user.service.create') }}" class="btn btn-brand btn-elevate btn-icon-sm"><i class="la la-plus"></i>Add Service</a>
					</div>
				</div>
			</div>
		</div>
		<div class="kt-portlet__body">
			<div class="row">
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
						<!-- @foreach($employee as $sub)
						<option value="{{$sub->id}}">{{$sub->name}}</option>
						@endforeach -->
					</select>
				</div>
				@endif
				@if(Auth::user()->role == 2)
				<div class="form-group col-lg-2">
					<label>Employee </label>
					<select name="employee_id" class="form-control changes select2" id="employee_id">
						<option value="">Select Employee</option>
						@foreach($employee as $sub)
						<option value="{{$sub->id}}">{{$sub->name}}</option>
						@endforeach
					</select>
				</div>
				@endif
				@endif
				<div class="form-group col-lg-2">
					<label>Status </label>
					<select name="status" class="form-control changes select2" id="status">
						<option value="">All</option>
						<option value="1">Open</option>
						<option value="2">Pending</option>
						<option value="3">Completed</option>
						
					</select>
				</div>
			</div>
			<div id="kt_table_1_wrapper" class="dataTables_wrapper dt-bootstrap4">
				<table class="table table-striped table-bordered table-hover table-checkable datatable" id="datatable_rows">
					@csrf
					<thead>
						<tr>
							<!-- <th>SrNo</th> -->
							@if(Auth::guard('user')->check())
							@if(Auth::user()->role == 1)
							<th>Branch</th>
							<th>Employee</th>
							@endif
							@if(Auth::user()->role == 2)
							<th>Employee</th>
							@endif
							
							@endif
							
							<th>Registered Date</th>
							<th>Client Name</th>
							<th style="width: 25% !important;">Company</th>
							<th>Status</th>							
							<th>Action</th>
						</tr>
					</thead>
					<tbody>
					</tbody>
				</table>
			</div>
		</div>
		@include('layouts.multiple_action', array(
		'table_name' => 'service',
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
				url:'{{ route("user.service.index") }}',
				data: function(d) {
					d.branch_id= $('#branch_id').val()
					d.employee_id= $('#employee_id').val()
					d.status= $('#status').val()
					
				}
			},
			
			columns: [
			// {
			// 	orderable: false,
			// 	searchable: true,
			// 	data: "id"
			// },
				@if(Auth::guard('user')->check())
				@if(Auth::user()->role == 1)
				{
					orderable: false,
					searchable: true,
					data: "branch.name"
				},
				{
					orderable: false,
					searchable: true,
					data: "employee.name",
				},
				@endif
				@if(Auth::user()->role == 2)
				{
					orderable: false,
					searchable: true,
					data: "employee.name",
				},
				
				@endif
				@endif
				{
					orderable: false,
					searchable: true,
					data: "register_date"
				},
				{
					orderable: false,
					searchable: true,
					data: "client.member"
				},
				{
					orderable: false,
					searchable: true,
					data: "company",
				},
				{
					orderable: false,
					searchable: true,
					data: "status"
				},
				{
					orderable: false,
					searchable: false,
					'data': 'action',
				},
			]
		});
		$('.changes').change(function(){
			
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
			url:"{{ route('user.employee.name') }}",
			data: {
				'branch_id': branch_id
			},
			success: function(data){
				$('#employee_id').html(data);
			}
		});
	};
	function status($this,id)
	{
		var status = $($this).val();
		$('.loader-wrapper').show();
		$('.loader').show();
		$.ajax({
			type: 'POST',
			url: '{{ route("user.service.status") }}',
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
				$('.loader-wrapper').hide();
				$('.loader').hide();
			}
		});
	}
</script>
@endpush