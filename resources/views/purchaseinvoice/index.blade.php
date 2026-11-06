@extends('main')
@section('content')
<div class="kt-container  kt-container--fluid  kt-grid_item kt-grid_item--fluid">
	<br>
	<div class="kt-portlet kt-portlet--mobile">
		<div class="kt-portlet__head">
			<div class="kt-portlet__head-label">
				<h3 class="kt-portlet__head-title">
					Purchase Invoice
				</h3>
			</div>
			<div class="kt-portlet__head-toolbar">
				<div class="kt-portlet__head-wrapper">
					
					<div class="kt-portlet__head-actions">
						<a href="{{ route('user.purchaseinvoice.create') }}" class="btn btn-brand btn-elevate btn-icon-sm"><i class="la la-plus"></i>Add Purchase Invoice</a>
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
			{{-- <div class="row">
				<div class="col-lg-2">
					<label>From Date</label>
					<input type="date" name="from_date" class="form-control changes" id="from_date" required>
				</div>
				<div class="col-lg-2">
					<label>To Date</label>
					<input type="date" name="to_date" class="form-control changes" id="to_date" required>
				</div>
				
				<div class="form-group col-sm-3">
					<label>Vendor</label>
					<select name="vendor_id" id="vendor_id" class="form-control changes select2 vendor_id">
						<option value="">All</option>
						@foreach($vendor as $value)
						<option value="{{$value->id}}">{{$value->name}}</option>
						@endforeach
					</select>
				</div>
				<div class="form-group col-sm-3" style="margin-top: 30px !important;">
					<button class="btn btn-brand btn-elevate btn-icon-sm" onclick="location.reload()">Refresh</button>
				</div>
			</div> --}}
			<div id="kt_table_1_wrapper" class="dataTables_wrapper dt-bootstrap4">
				<table class="table table-striped table-bordered table-hover table-checkable datatable" id="datatable_rows">
					@csrf
					<thead>
						<tr>
							<th>Sr No.</th>
							@if(Auth::guard('user')->check())
							@if(Auth::user()->role == 1)  
							<th>Branch Name</th>
							@endif
							@endif
							<th>Vendor Name</th>
							<th>Invoice No</th>
							<th>Date</th>
							{{-- <th>Status</th> --}}
							<th>Action</th>
							
						</tr>
					</thead>
					<tbody>
					</tbody>
				</table>
			</div>
		</div>
		@include('layouts.multiple_action', array(
			'table_name' => 'purchase_invoice',
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
					url:'{{ route("user.purchaseinvoice.index") }}',
					data: function(d) {
						d.from_date= $('#from_date').val(),
						d.to_date= $('#to_date').val(),
						d.branch_id= $('#branch_id').val(),
						d.vendor_id= $('#vendor_id').val()
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
					data: "vendor_id"
				},
				{
					orderable: false,
					searchable: true,
					data: "invoice_no"
				},
				{
					orderable: false,
					searchable: true,
					data: "date"
				},
				// {
				// 	orderable: false,
				// 	searchable: true,
				// 	data: "status"
				// },
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
			$('#branch_id').on('change', function () {
			oTable.draw();
		});
		});
		function status($this,id)
		{
			var status = $($this).val();
			$.ajax({
				type: 'POST',
				url: '{{ route("user.purchaseinvoice.status") }}',
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
	</script>
	@endpush