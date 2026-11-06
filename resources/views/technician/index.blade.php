@extends('main')
@section('content')
<meta name="csrf-token" content="{{ csrf_token() }}" />
<div class="kt-container  kt-container--fluid  kt-grid_item kt-grid_item--fluid">
	<br>
	<div class="kt-portlet kt-portlet--mobile">
		<div class="kt-portlet__head">
			<div class="kt-portlet__head-label">
				<h3 class="kt-portlet__head-title">
					Technician
				</h3>
			</div>
			<div class="kt-portlet__head-toolbar">
				<div class="kt-portlet__head-wrapper">
					<div class="kt-portlet__head-actions">
						<a href="{{ route('user.technician.create') }}" class="btn btn-brand btn-elevate btn-icon-sm"><i class="la la-plus"></i>Add Technician</a>
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
					<select name="branch_id" class="form-control changes select2" id="branch_id">
						<option value="">Select Branch</option>
						@foreach($branch as $sub)
						<option value="{{$sub->id}}">{{$sub->name}}</option>
						@endforeach
					</select>
				</div>
				@endif
				@endif
			</div>
			<div id="kt_table_1_wrapper" class="dataTables_wrapper dt-bootstrap4">
				<table class="table table-striped table-bordered table-hover table-checkable datatable" id="datatable_rows">
					@csrf
					<thead>
						<tr>
							@if(Auth::guard('user')->check())
							@if(Auth::user()->role == 1)
							<th>Branch</th>
							@endif
							@endif
							<th>Category</th>						
							<th>Company</th>						
							<th>Technician Name</th>
							<th>Action</th>
						</tr>
					</thead>
					<tbody>
					</tbody>
				</table>
			</div>
		</div>
		@include('layouts.multiple_action', array(
		'table_name' => 'technician',
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
				url:'{{ route("user.technician.index") }}',
				data: function(d) {
					d.branch_id= $('#branch_id').val()
					d.employee_id= $('#employee_id').val()
					d.status= $('#status').val()
					
				}
			},
			
			columns: [
				@if(Auth::guard('user')->check())
				@if(Auth::user()->role == 1)
				{
					orderable: false,
					searchable: true,
					data: "branch.name"
				},
				@endif
				@endif
				{
					orderable: false,
					searchable: true,
					data: "category.name",
				},
				{
					orderable: false,
					searchable: true,
					data: "company.name",
				},
				{
					orderable: false,
					searchable: true,
					data: "technician_name"
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