@extends('main')
@section('content')
<div class="kt-container  kt-container--fluid  kt-grid_item kt-grid_item--fluid">
	<br>
	<div class="kt-portlet kt-portlet--mobile">
		<div class="kt-portlet__head">
			<div class="kt-portlet__head-label">
				<h3 class="kt-portlet__head-title">
					Employee
				</h3>
			</div>
			<div class="kt-portlet__head-toolbar">
				<div class="kt-portlet__head-wrapper">
					@if(Auth::guard('user')->check())
					@php
					$permission = App\Group::where('id',Auth::user()->group_id)->first();
					$per_exploded = explode(',',$permission->employee);
					@endphp
					@if($per_exploded[1] == 1)
					<div class="kt-portlet__head-actions">
						
						<a href="{{ route('user.employee.create') }}" class="btn btn-brand btn-elevate btn-icon-sm"><i class="la la-plus"></i>Add Employee</a>
					</div>
					@endif
					@endif

					@if(Auth::guard('employee')->check())
					@if(Auth::user()->role == 6 || Auth::user()->role == 7)
					<div class="kt-portlet__head-actions">
						<a href="{{ route('user.employee.create') }}" class="btn btn-brand btn-elevate btn-icon-sm"><i class="la la-plus"></i>Add Employee</a>
					</div>
					@endif
					@endif
				</div>
			</div>
		</div>
		<div class="kt-portlet__body">
			<div class="row">
				<div class="form-group col-lg-2">
					<label>Status </label>
					<select class="form-control changes select2" name="status" id="status" >
						<option value="2">All</option>
						<option value="1">Active</option>
						<option value="0">In Active</option>
					</select>
				</div>
				@if(Auth::guard('user')->check())
				@if(Auth::user()->role == 1)
				<div class="form-group col-lg-2">
					<label>Branch </label>
					<select name="branch_id" class="form-control changes select2" id="branch_id">
						<option value="">Select Branch</option>
						@foreach($branch as $sub)
						<option value="{{$sub->id}}">{{$sub->name}}</option>
						@endforeach
					</select>
				</div>
				@endif
				@endif
				<div class="form-group col-lg-2">
					<label>Role </label>
					<select class="form-control changes select2" name="role" id="role" >
						<option value="">Select</option>
						@foreach ($roles as $id => $name)
							<option value="{{ $id }}">{{ $name }}</option>
						@endforeach
					</select>
				</div>
			</div>
			<br/>
			<div id="kt_table_1_wrapper" class="dataTables_wrapper dt-bootstrap4">
				<table class="table table-striped table-bordered table-hover table-checkable datatable" id="datatable_rows">
					@csrf
					<thead>
						<tr>
							<!-- <th>SrNo</th> -->
							@if(Auth::guard('user')->check())
							@if(Auth::user()->role == 1)
							<th>Branch Name</th>
							
							@endif
							@endif
							<th>Name</th>
							<th>Mobile No.</th>
							<th>Email</th>
							<th>Role</th>
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
			'table_name' => 'employees',
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
					url:'{{ route("user.employee.index") }}',
					data: function(d) {
						d.status= $('#status').val()
						d.branch_id= $('#branch_id').val()
						d.role= $('#role').val()
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
						data: "branch_id"
					},
					@endif
					@endif
					{
						orderable: false,
						searchable: true,
						data: "name"
					},
					{
						orderable: false,
						searchable: true,
						data: "mobile_no"
					},
					{
						orderable: false,
						searchable: true,
						data: "email"
					},
					{
						orderable: false,
						searchable: true,
						data: "role"
					},
					{
						orderable: false,
						searchable: false,
						data: 'singlecheckbox',
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
	</script>
	@endpush