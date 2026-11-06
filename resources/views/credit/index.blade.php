@extends('main')
@section('content')
<div class="kt-container  kt-container--fluid  kt-grid__item kt-grid__item--fluid">
	<br>
	<div class="kt-portlet kt-portlet--mobile">
		<div class="kt-portlet__head kt-portlet__head--lg">
			<div class="kt-portlet__head-label">
				<h3 class="kt-portlet__head-title">
					Credit
				</h3>
			</div>
			<div class="kt-portlet__head-toolbar">
				<div class="kt-portlet__head-wrapper">
					<div class="kt-portlet__head-actions">					
						<form id="userimportform" enctype="multipart/form-data">
							@csrf
							<input type="file" style="display:none;" name="user_import" id="userimportfile" class="form-control">
							<a href="{{route('user.credit.create')}}" class="btn btn-brand btn-elevate btn-icon-sm">
								<i class="la la-plus"></i>
								Add Credit
							</a>
						</form>
						
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
							@if(Auth::guard('user')->check())
							@if(Auth::user()->role == 1)  
							<th>Branch</th>
							@endif
							@endif
							<th>Date</th>
							<th>Client</th>
							<th>Debit Order No</th>
							<th>SalesOrder Invoice</th>
							<th>Status</th>
							<th>Action</th>
						</tr>
					</thead>
				</table>
			</div>
		</div>
		@include('layouts.multiple_action', array(
		'table_name' => 'credit',
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
				url:'{{ route("user.credit.index") }}',
				data: function(d) {
					d.status= $('#status').val();
						d.branch_id = $('#branch_id').val();
					
					d.productcategory_id = $('#productcategory_id').val()
				}
			},
				// ajax: "{{ route('user.company.index') }}",
				columns: [
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
					data: "date",
				},
				{
					orderable: false,
					searchable: true,
					data: "client_id",
				},
				{
					orderable: true,
					searchable: true,
					data: "order_no",
				},
				{
					orderable: false,
					searchable: true,
					data: "invoice_id",
				},
				{
					orderable: false,
					searchable: false,
					'data': 'singlecheckbox',
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
	$('.select2').select2({
	});
	function status($this,id)
	{
		var status = $($this).val();
		$.ajax({
			type: 'POST',
			url: '{{ route("user.credit.status") }}',
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