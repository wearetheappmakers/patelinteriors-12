@extends('main')
@section('content')
<meta name="csrf-token" content="{{ csrf_token() }}" />
<div class="kt-container  kt-container--fluid  kt-grid__item kt-grid__item--fluid">
	<br>
	<div class="kt-portlet kt-portlet--mobile">
		<div class="kt-portlet__head kt-portlet__head--lg">
			<div class="kt-portlet__head-label">
				<h3 class="kt-portlet__head-title">
					Closing Stock
				</h3>
			</div>
		</div>
		<form id="inventory_form" class="inventory_form" method="POST" enctype="multipart/form-data">
			@csrf
			<div class="kt-portlet__body">
				<div class="row">
					@if(Auth::guard('user')->check())
					@if(Auth::user()->role == 1)
					<div class="form-group col-lg-2">
						<label>Branch </label>
						<select class="form-control changes select2" name="branch_id" onchange="getdata(this)" id="branch_id" >
							<option value="">Select Branch</option>
							@foreach($branch as $sub)
							<option value="{{$sub->id}}">{{$sub->name}}</option>
							@endforeach
						</select>
					</div>
					<div class="form-group col-lg-2">
						<label>Category </label>
						<select class="form-control changes select2" name="productcategory_id" id="productcategory_id" onchange="getcompany(this)">
							<option value="">Select Category</option>

						</select>
					</div>
					<div class="form-group col-lg-2">
						<label>Company </label>
						<select class="form-control changes select2" name="company_id" id="company_id" onchange="getproduct(this)">
							<option value="">Select Company</option>

						</select>
					</div>
					
					@endif
					@if(Auth::user()->role == 2)
					<div class="form-group col-lg-2">
						<label>Category </label>
						@php
						$category = App\ProductCategory::where('branch_id',Auth::user()->id)->get();
						$warehouse = App\Warehouse::where('branch_id',Auth::user()->id)->get();
						@endphp
						<select class="form-control changes select2" name="productcategory_id" id="productcategory_id" onchange="getcompany(this)">
							<option value="">Select Category</option>
							@foreach($category as $sub)
							<option value="{{$sub->id}}">{{$sub->name}}</option>
							@endforeach
						</select>
					</div>
					<div class="form-group col-lg-2">
						<label>Company </label>
						<select class="form-control changes select2" name="company_id" id="company_id" onchange="getproduct(this)">
							<option value="">Select Company</option>

						</select>
					</div>
					@endif
					@endif
					@if(Auth::guard('employee')->check())
					<div class="form-group col-lg-2">
						<label>Category </label>
						@php
						$category = App\ProductCategory::where('branch_id',Auth::user()->branch_id)->get();
						$warehouse = App\Warehouse::where('branch_id',Auth::user()->branch_id)->get();
						@endphp
						<select class="form-control changes select2" name="productcategory_id" id="productcategory_id" onchange="getcompany(this)">
							<option value="">Select Category</option>
							@foreach($category as $sub)
							<option value="{{$sub->id}}">{{$sub->name}}</option>
							@endforeach
						</select>
					</div>
					<div class="form-group col-lg-2">
						<label>Company </label>
						<select class="form-control changes select2" name="company_id" id="company_id" onchange="getproduct(this)">
							<option value="">Select Company</option>

						</select>
					</div>
					@endif
				</div>
				<div id="kt_table_1_wrapper" class="dataTables_wrapper dt-bootstrap4">
					<table class="table table-striped table-bordered table-hover table-checkable datatable" id="datatable_rows">
						@csrf
						<thead>
							<tr>
								<!-- <th>#</th> -->
								@if(Auth::guard('user')->check())
								@if(Auth::user()->role == 1)
								<th>Branch</th>
								@endif
								@endif
								<th>Category</th>
								<th>Company</th>
								<th>Product Name</th>
								<th>Product Code</th>
								<th>Closing Stock</th>

							</tr>
						</thead>
					</table>
				</div>
			</div>
			@include('layouts.multiple_action', array(
			'table_name' => 'warehouse_inventories',
			'is_orderby'=>'',
			'folder_name'=>'',
			'action' => array('change-status-1' => _('Active'), 'change-status-0' => _('Inactive'))
			))
		</form>
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
				url:'{{ route("user.closing.stock") }}',
				data: function(d) {
					d.branch_id= $('#branch_id').val()
					d.productcategory_id= $('#productcategory_id').val()
					d.company_id= $('#company_id').val()
					d.product_id= $('#product_id').val()
				}
			},
			columns: [
			// {
			// 	orderable: true,
			// 	searchable: true,
			// 	data: "id"
			// },
			@if(Auth::guard('user')->check())
			@if(Auth::user()->role == 1)
			{
				orderable: true,
				searchable: true,
				data: "branch_id"
			},
			@endif
			@endif
			{
				orderable: false,
				searchable: true,
				'data': 'category_ids',
			},
			{
				orderable: false,
				searchable: true,
				'data': 'company_ids',
			},
			{
				orderable: false,
				searchable: true,
				'data': 'product.product',
			},
			{
				orderable: false,
				searchable: true,
				'data': 'product.code',
			},
			{
				orderable: false,
				searchable: false,
				'data': 'actual_inventory',
			},
			
			]
		});
		$('.changes').change(function(){
			oTable.draw();
		});
	});
	function stockUpdate(type)
	{
		if ($(".inventory_form").valid())
		{
			$.ajax({
				type: "POST",
				url: "{{route('user.inventory.update')}}",
				data: new FormData($('.inventory_form')[0]),
				processData: false,
				contentType: false,
				success: function (data)
				{
					if (data.status === 'success') {
						toastr["success"]("Inventory Updates Successfully", "Success");
						location.reload();
					} else if (data.status === 'error') {
						location.reload();
						toastr["error"]("Something went wrong", "Error");
					}
				}
			});
		}
	}
	function getdata($this){
		var branch_id = $($this).val();
		var id = $($this).data('id');
		$.ajax({
			type:"POST",
			headers: {
				'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
			},
			url:"{{ route('user.report.getdata') }}",
			data: {
				'branch_id': branch_id
			},
			success: function(data){
				// console.log(data.data);

				$('#productcategory_id').html(data.data4);
				$('#warehouse_id').html(data.data7);

			}
		});
	};
	function getcompany($this){
		var productcategory_id = $($this).val();
		var branch_id = $('#branch_id').val();

		var id = $($this).data('id');
		$.ajax({
			type:"POST",
			headers: {
				'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
			},
			url:"{{ route('user.product.getcompany') }}",
			data: {
				'productcategory_id': productcategory_id,
				'branch_id': branch_id
			},
			success: function(data){
				$('#company_id').html(data);
			}
		});
	};

	function getproduct($this){
		var company_id = $($this).val();
		var id = $($this).data('id');
		$.ajax({
			type:"POST",
			headers: {
				'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
			},
			url:"{{ route('user.po.getproduct') }}",
			data: {
				'company_id': company_id
			},
			success: function(data){
				$('#product_id').html(data.data);
			}
		});
	};
</script>
@endpush