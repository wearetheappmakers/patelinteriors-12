@extends('main')
@section('content')

<div class="kt-container  kt-container--fluid  kt-grid_item kt-grid_item--fluid">
	<br>
	<div class="kt-portlet kt-portlet--mobile">
		<div class="kt-portlet__head">
			<div class="kt-portlet__head-label">
				<h3 class="kt-portlet__head-title">
					Branch
				</h3>
			</div>
			<div class="kt-portlet__head-toolbar">
				<div class="kt-portlet__head-wrapper">
					
					<div class="kt-portlet__head-actions">
						<a href="{{ route('user.user.create') }}" class="btn btn-brand btn-elevate btn-icon-sm"><i class="la la-plus"></i>Add Branch</a>
					</div>
				</div>
			</div>
		</div>
		<div class="kt-portlet__body">
			<div id="kt_table_1_wrapper" class="dataTables_wrapper dt-bootstrap4">
				<table class="table table-striped table-bordered table-hover table-checkable datatable" id="datatable_rows">
					@csrf
					<thead>
						<tr>
							<!-- <th>SrNo</th> -->
							<th>Name</th>
							<th>E-mail</th>
							<th>Number</th>
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
		'table_name' => 'users',
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

		$('#datatable_rows').DataTable({

			processing: true,
			serverSide: true,
			searchable: true,
			scrollX: true,
			stateSave: true,
			columnDefs: [{
				orderable: false,
				targets: -1,
			}],

			ajax: "{{ route('user.user.index') }}",

			columns: [
			// {
			// 	orderable: false,
			// 	searchable: true,
			// 	data: "id"
			// },
			{
				orderable: false,
				searchable: true,
				data: "name"
			},
			{
				orderable: false,
				searchable: false,
				data: 'email',	

			},
			{
				orderable: false,
				searchable: false,
				data: 'number',
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

	});

</script>



@endpush