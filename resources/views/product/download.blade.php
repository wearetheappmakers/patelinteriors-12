@extends('main')
@section('content')
<div class="kt-container  kt-container--fluid  kt-grid__item kt-grid__item--fluid">
	<br>
	<div class="kt-portlet kt-portlet--mobile">
		<div class="kt-portlet__head kt-portlet__head--lg">
			<div class="kt-portlet__head-label">
				<span class="kt-portlet__head-icon">
					<i class="kt-font-brand"></i>
				</span>
				<h3 class="kt-portlet__head-title">
					Download History
				</h3>
			</div>
		</div>
		<div class="kt-portlet__body">
			<div id="kt_table_1_wrapper" class="dataTables_wrapper dt-bootstrap4">
				
				<table class="table table-striped table-bordered table-hover table-checkable datatable" id="datatable_rowss">
					@csrf
					<thead>
						<tr>
							<th>#</th>
							<th>Date Time</th>
							<th>File</th>
							<th>Action</th>
						</tr>
					</thead>
					<tbody>
					</tbody>
				</table>
			</div>
		</div>
	</div>
</div>
@stop
@push('scripts')
<script>
	$(document).ready(function() {
		var oTable = $('#datatable_rowss').DataTable({
			processing: true,
			serverSide: true,
			searchable: true,
			scrollX: true,
			stateSave: true,
			columnDefs: [{
				orderable: false,
				targets: -1,
			}],
			order: [0,false],
			ajax: {
				url:'{{ route("user.product.download") }}',
				data: function(d) {
					d.vendor_id= $('#vendor_id').val()
				}
			},
			columns: [
			{
				orderable: true,
				searchable: true,
				data: "id"
			},
			{
				orderable: true,
				searchable: true,
				data: "dates"
			},
			{
				orderable: true,
				searchable: true,
				data: "files"
			},
			{
				orderable: true,
				searchable: true,
				data: "action"
			},
			]
		});
		$('.changes').change(function(){
			
			oTable.draw();
			
		});
	});
</script>
@endpush