@extends('main')
@section('content')

<div class="kt-container  kt-container--fluid  kt-grid_item kt-grid_item--fluid">
	<br>
	<div class="kt-portlet kt-portlet--mobile">
		<div class="kt-portlet__head">
			<div class="kt-portlet__head-label">
				<h3 class="kt-portlet__head-title">
					Complain
				</h3>
			</div>
			<div class="kt-portlet__head-toolbar">
				<div class="kt-portlet__head-wrapper">
					@php
					$permission = App\Group::where('id',Auth::user()->group_id)->first();
					$per_exploded = explode(',',$permission->complain);
					@endphp
					@if($per_exploded[1] == 1)
					<div class="kt-portlet__head-actions">
						<a href="{{ route('user.complains.create') }}" class="btn btn-brand btn-elevate btn-icon-sm"><i class="la la-plus"></i>Add Complain</a>
					</div>
					@endif
				</div>
			</div>
		</div>
		<div class="kt-portlet__body">

			<div class="row">
				<div class="col-lg-2">
					
					<select class="form-control changes" name="status" id="status" >
						<option value="2">All</option>
						<option value="1">Active</option>
						<option value="0">In Active</option>
					</select>
				</div>
			</div>
			<br/>
			<div id="kt_table_1_wrapper" class="dataTables_wrapper dt-bootstrap4">
				<table class="table table-striped table-bordered table-hover table-checkable datatable" id="datatable_rows">
					@csrf
					<thead>
						<tr>
							<th>SrNo</th>
							<th>Name</th>
							<th>Mobile</th>
							<th>Company</th>
							<th>Reference No</th>
							<th>Problem</th>
							
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
		'table_name' => 'complain',
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
				url:'{{ route("user.complains.index") }}',
				data: function(d) {
					d.status= $('#status').val()
				}
			},
			columns: [
			{
				orderable: false,
				searchable: true,
				data: "id"
			},
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
				data: "company"
			},
			{
				orderable: false,
				searchable: true,
				data: "reference_no"
			},

			{
				orderable: false,
				searchable: true,
				data: "problem"
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