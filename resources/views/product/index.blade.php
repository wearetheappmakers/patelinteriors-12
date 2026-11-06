@extends('main')
@section('content')
<meta name="csrf-token" content="{{ csrf_token() }}" />
<div class="kt-container  kt-container--fluid  kt-grid_item kt-grid_item--fluid">
	<br>
	<div class="kt-portlet kt-portlet--mobile">
		<div class="kt-portlet__head">
			<div class="kt-portlet__head-label">
				<h3 class="kt-portlet__head-title">
					Product
				</h3>
			</div>
			<div class="kt-portlet__head-toolbar">
				<div class="kt-portlet__head-wrapper">
					@if(Auth::guard('user')->check())
					@php
					$permission = App\Group::where('id',Auth::user()->group_id)->first();
					$per_exploded = explode(',',$permission->product);
					@endphp
					@if($per_exploded[1] == 1)
					<div class="kt-portlet__head-actions">
						<form method="POST" id="productimportform" enctype="multipart/form-data">
							@csrf	
							<input type="file" style="display:none;" name="product_excel_import" id="productinputfile">
							<input type="file" style="display:none;" name="product_excel" id="productinputmrpfile">
						</form>
						<button class="btn btn-brand btn-elevate btn-icon-sm productimportbtn">
							<i class="la la-upload"></i>
							Import Product
							<i class="la la-spinner la-spin d-none"></i>
						</button>
						<a href="{{route('user.product.product_export')}}" class="btn btn-brand btn-elevate btn-icon-sm">
							<i class="la la-download"></i>
							Sample Product
						</a>
						<button class="btn btn-brand btn-elevate btn-icon-sm productimportmrpbtn">
							<i class="la la-upload"></i>
							Import Mrp
							<i class="la la-spinner la-spin d-none"></i>
						</button>
						<a href="{{route('user.product.product_export_mrp')}}" class="btn btn-brand btn-elevate btn-icon-sm">
							<i class="la la-download"></i>
							Export MRP
						</a>
						<button type="button" class="btn btn-brand btn-elevate btn-icon-sm downloads">
							<i class="la la-download"></i>
							In Downloads
						</button>
						@endif
						<a href="{{ route('user.product.create') }}" class="btn btn-brand btn-elevate btn-icon-sm"><i class="la la-plus"></i>Add Product</a>
					</div>
					@endif
				</div>
			</div>
		</div>
		<div class="kt-portlet__body">
			<form class="kt-form" action="" method="POST">
				@csrf
				<div class="row">
					<!-- <div class="form-group col-lg-2">
						<label>Sataus </label>
						<select class="form-control changes select2" name="status" id="status" >
							<option value="2">All</option>
							<option value="1">Active</option>
							<option value="0">In Active</option>
						</select>
					</div> -->
					<div class="form-group col-lg-2">
						<label>Category </label>
						<select name="productcategory_id" class="form-control changes select2" onchange="getcompany(this)" id="productcategory_id">
							<option value="">Select Category</option>
							@foreach($productcategory as $sub)
							<option value="{{$sub->id}}">{{$sub->name}}</option>
							@endforeach
						</select>
					</div>
					<div class="form-group col-lg-2">
						<label>Company </label>
						<select name="company_id" class="form-control changes select2" id="company_id">
							<option value="">Select Company</option>
						</select>
					</div>
				</div>
			</form>
			<div id="kt_table_1_wrapper" class="dataTables_wrapper dt-bootstrap4">
				<table class="table table-striped table-bordered table-hover table-checkable datatable" id="datatable_rows">
					@csrf
					<thead>
						<tr>
							<th>Category</th>
							<th>Company</th>
							<th>Product</th>
							<th>SKU Code</th>
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
		'table_name' => 'product',
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
		$(document).on("click", ".downloads", function ()
		{
			toastr["success"]("Check in Download After Sometime.", "Success");
			$.ajax({
				type: "POST",
				url: "{{ route('user.product.cron.productexportmrp') }}",
				data: new FormData($('.kt-form')[0]),
				processData: false,
				contentType: false,
				success: function (data)
				{
					location.reload();
					toastr["success"]("Check in Download After Sometime.", "Success");
				}
			});
		});
	});
	$(document).ready(function() {
		$('.productimportbtn').on('click', function() {
			$('#productinputfile').trigger('click');
		});
		$('#productinputfile').change(function(e) {
			$('.product_upload').prop('disabled', true);
			$('.product_upload').find('.la-spin').removeClass('d-none');
			if($("#productimportform").valid()){
				$.ajax({
					type:"POST",
					url:"{{ route('user.product.product_import') }}",
					data: new FormData($('#productimportform')[0]),
					processData: false,
					contentType:false,
					success: function(data){
						if(data.status==='success'){
							alert('Import successfull.')
							window.location.reload();
						}else if(data.status==='error'){
							toastr["error"](data.data,data.status);
							setTimeout(function() {
								location.reload();
							}, 5000);
						}
					}
				});
			}else{
				e.preventDefault();
			}
		});
		$('.productimportmrpbtn').on('click', function() {
			$('#productinputmrpfile').trigger('click');
		});
		$('#productinputmrpfile').change(function(e) {
			$('.product_upload').prop('disabled', true);
			$('.product_upload').find('.la-spin').removeClass('d-none');
			if($("#productimportform").valid()){
				$.ajax({
					type:"POST",
					url:"{{ route('user.product.product_import_mrp') }}",
					data: new FormData($('#productimportform')[0]),
					processData: false,
					contentType:false,
					success: function(data){
						if(data.status==='success'){
							alert('Import successfull.')
							window.location.reload();
						}else if(data.status==='error'){
							// toastr["error"]("Some error!", "Error");
							// location.reload();
							toastr["error"](data.data,data.status);
							setTimeout(function() {
								location.reload();
							}, 5000);
						}
					}
				});
			}else{
				e.preventDefault();
			}
		});
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
				url:'{{ route("user.product.index") }}',
				data: function(d) {
					d.status= $('#status').val()
					d.company_id= $('#company_id').val()
					d.productcategory_id = $('#productcategory_id').val()
				}
			},
			columns: [
			{
				orderable: false,
				searchable: true,
				data: "productcategory_id"
			},
			{
				orderable: false,
				searchable: true,
				data: "company_id"
			},
			{
				orderable: false,
				searchable: true,
				data: "product"
			},
			{
				orderable: false,
				searchable: true,
				data: "code"
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
	$('.select2').select2({
	});
	function getcompany($this){
		var productcategory_id = $($this).val();
		var id = $($this).data('id');
		$.ajax({
			type:"POST",
			headers: {
				'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
			},
			url:"{{ route('user.product.getcompany') }}",
			data: {
				'productcategory_id': productcategory_id
			},
			success: function(data){
				$('#company_id').html(data);
			}
		});
	};
</script>
@endpush