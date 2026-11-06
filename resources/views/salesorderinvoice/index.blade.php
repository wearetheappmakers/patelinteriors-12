@extends('main')
@section('content')
<div class="kt-container  kt-container--fluid  kt-grid_item kt-grid_item--fluid">
	<br>
	<div class="kt-portlet kt-portlet--mobile">
		<div class="kt-portlet__head">
			<div class="kt-portlet__head-label">
				<h3 class="kt-portlet__head-title">
					Sales Invoice
				</h3>
			</div>
			<div class="kt-portlet__head-toolbar">
				<div class="kt-portlet__head-wrapper">
					<div class="kt-portlet__head-actions">
						<form id="siimportform" enctype="multipart/form-data">
							@csrf   
							<input type="file" style="display:none;" name="file1" id="siinputfile">
						</form>
						<button class="btn btn-brand btn-elevate btn-icon-sm siimportbtn">
							<i class="fa fa-file-import fs-2"> &nbsp;</i>Import
						</button>

						<a href="{{route('user.salesorder.invoice.sample.export')}}" class="btn btn-brand btn-elevate btn-icon-sm">
							<i class="fa fa-file-excel fs-2"> &nbsp;</i>Sample 
						</a>

						<a href="{{ route('user.salesorderinvoice.create') }}" class="btn btn-brand btn-elevate btn-icon-sm"><i class="la la-plus"></i>Add Sales Invoice</a>
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
						<select class="form-control changes select2" name="branch_id" id="branch_id" >
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
							<th>Sr no.</th>
							@if(Auth::guard('user')->check())
							@if(Auth::user()->role == 1)  
							<th>Branch Name</th>
							@endif
							<th>Employee Name</th>
							@endif
							<th>Client</th>
							<th>Date</th>
							<th>Action</th>
						</tr>
					</thead>
					<tbody>
					</tbody>
				</table>
			</div>
		</div>
		@include('layouts.multiple_action', array(
		'table_name' => 'salesorder_invoice',
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
				url:'{{ route("user.salesorderinvoice.index") }}',
				data: function(d) {
					d.status= $('#status').val();
					d.branch_id = $('#branch_id').val();
				}
			},
			columns: [
				{
					orderable: false,
					searchable: true,
					data: "ids"
				},
				@if(Auth::guard('user')->check())

				@if(Auth::user()->role == 1)  
				{ orderable: false,	searchable: true, data: "branch.name" },
				@endif

				{ orderable: false,	searchable: true, data: "employees.name" },

				@endif

				{ orderable: false,	searchable: true, data: "client.member" },

				{ orderable: false,	searchable: true, data: "date" },	

				{ orderable: false,	searchable: true, data: "action" },
			]
		});
		$('.changes').change(function(){
			oTable.draw();	
		});
	});
	function status($this,id)
	{
		var status = $($this).val();
		$.ajax({
			type: 'POST',
			url: '{{ route("user.salesorderinvoice.status") }}',
			data: {
				'_token': '{{ csrf_token() }}',
				id: id,
				status: status,
			},
			dataType: 'json',
			success: function(data) {
				if (data.status == 'success') {
					toastr["success"]("status Changed successfully", "Success");
					location.reload();
				} else if(data.status == 'qty')
				{
					toastr["error"](" Qty Check", "Error");
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
	$('.siimportbtn').on('click', function() {
		$('#siinputfile').trigger('click');
	});

	$('#siinputfile').change(function(e) {
		e.preventDefault();
		$('.loader-wrapper').show();
		$('.loader').show();
		$.ajax({
			type: "POST",
			url: "{{ route('user.salesorder.invoice.import') }}",
			data: new FormData($('#siimportform')[0]),
			processData: false,
			contentType: false,
			success: function(data) {
				if (data.status === 'success') {
					alert('Data Import Successful.');
					window.location.reload();
				} else if (data.status === 'error') {
					if (data.hasOwnProperty('gst_error')) {
						toastr["error"](data.gst_error);
					} else {
						toastr["error"](data.data,data.status);
					}
					setTimeout(function() {
						location.reload();
					}, 5000);
				}
			},
		});
		return false;
	});
</script>
@endpush