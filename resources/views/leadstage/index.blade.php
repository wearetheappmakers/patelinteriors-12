@extends('main')
@section('content')
<div class="kt-container  kt-container--fluid  kt-grid_item kt-grid_item--fluid">
	<br>
	<div class="kt-portlet kt-portlet--mobile">
		<div class="kt-portlet__head">
			<div class="kt-portlet__head-label">
				<h3 class="kt-portlet__head-title">
					Lead Stage
				</h3>
			</div>
			<div class="kt-portlet__head-toolbar">
				<div class="kt-portlet__head-wrapper">
					<div class="kt-portlet__head-actions">
						<a href="{{ route('user.leadstage.create') }}" 
						class="btn btn-brand btn-elevate btn-icon-sm">
						<i class="la la-plus"></i>Add Lead Stage
						</a>
					</div>
				</div>
			</div>
		</div>
		<div class="kt-portlet__body">
			<div class="row">
				<!-- <div class="col-lg-2">
					<select class="form-control changes select2" name="status" id="status" >
						<option value="2">All</option>
						<option value="1">Active</option>
						<option value="0">In Active</option>
					</select>
				</div> -->
			</div>
			<br/>
			<div id="kt_table_1_wrapper" class="dataTables_wrapper dt-bootstrap4">
				<table class="table table-striped table-bordered table-hover table-checkable datatable" id="datatable_rows">
					@csrf
					<thead>
						<tr>
							<th>Name</th>
							<th>Color</th>
							<!-- <th>Status</th> -->
							<th>Action</th>
						</tr>
					</thead>
					<tbody>
					</tbody>
				</table>
			</div>
		</div>
		@include('layouts.multiple_action', array(
		'table_name' => 'lead_stage',
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
				url:'{{ route("user.leadstage.index") }}',
				data: function(d) {
					d.status= $('#status').val()
				}
			},
			columns: [
				{
					orderable: false,
					searchable: true,
					data: "name"
				},
				{
					orderable: false,
					searchable: true,
					data: "color"
				},
				// {
				// 	orderable: false,
				// 	searchable: false,
				// 	data: 'singlecheckbox',
				// },
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