@extends('main')
@section('content')
<div class="kt-container  kt-container--fluid  kt-grid__item kt-grid__item--fluid">
	<br>
	<div class="kt-portlet kt-portlet--mobile">
		<div class="kt-portlet__head kt-portlet__head--lg">
			<div class="kt-portlet__head-label">
				<h3 class="kt-portlet__head-title">
                Holiday
				</h3>
			</div>
			<div class="kt-portlet__head-toolbar">
				<div class="kt-portlet__head-wrapper">
					<div class="kt-portlet__head-actions">					

						<form id="userimportform" enctype="multipart/form-data">
							@csrf
							<input type="file" style="display:none;" name="user_import" id="userimportfile" class="form-control">
							<a href="{{route('user.holiday.create')}}" class="btn btn-brand btn-elevate btn-icon-sm">
								<i class="la la-plus"></i>
								Add Holiday
							</a>
						</form>
					</div>
				</div>
			</div>
		</div>
		<div class="kt-portlet__body">
			<div class="row">
				<!-- <div class="form-group col-lg-2">
					<label>Sataus </label>
					<select class="form-control changes select2" name="status" id="status" >
						<option value="2">All</option>
						<option value="1">Active</option>
						<option value="0">In Active</option>
					</select>
				</div> -->
				
			</div>
			<div id="kt_table_1_wrapper" class="dataTables_wrapper dt-bootstrap4">
				<table class="table table-striped table-bordered table-hover table-checkable datatable" id="datatable_rows">
					@csrf
					<thead>
						<tr>
						    <th>From Date</th>
                            <th>To Date</th>
							<th>Name</th>
                            
							<!-- <th>Status</th> -->
							<th>Action</th>
						</tr>
					</thead>
				</table>
			</div>
		</div>
		@include('layouts.multiple_action', array(
		'table_name' => 'holiday',
		'is_orderby'=>'',
		'folder_name'=>'',
		'action' => array('change-status-1' => _('Active'), 'change-status-0' => _('Inactive'))
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
				url:'{{ route("user.holiday.index") }}',
				data: function(d) {
					d.status= $('#status').val()
				}
			},
				// ajax: "{{ route('user.company.index') }}",
			columns: [
                {
					orderable: false,
					searchable: true,
					data: "from_date",
				},
                {
					orderable: false,
					searchable: true,
					data: "to_date",
				},
				
				{
					orderable: false,
					searchable: true,
					data: "name",
				},
                
				// {
				// 	orderable: false,
				// 	searchable: false,
				// 	'data': 'singlecheckbox',
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
	$('.select2').select2({
	});
</script>
@endpush