@extends('main')
@section('content')
<style>
	#stockTransferModal .modal-content{
		border-radius:12px;
		border:none;
	}
	#stockTransferModal .modal-header{
		padding:16px 20px;
	}
	#stockTransferModal .modal-body{
		background:#f8f9fc;
	}
	#stockTransferModal .card{
		border-radius:10px;
	}
	#stockTransferModal label{
		font-weight:600;
		margin-bottom:6px;
	}
	#stockTransferModal .form-control{
		height:42px;
		border-radius:6px;
	}
	#stockTransferModal .btn{
		min-width:130px;
	}
</style>

<meta name="csrf-token" content="{{ csrf_token() }}" />
<div class="kt-container  kt-container--fluid  kt-grid__item kt-grid__item--fluid">
	<br>
	<div class="kt-portlet kt-portlet--mobile">
		<div class="kt-portlet__head kt-portlet__head--lg">
			<div class="kt-portlet__head-label">
				<h3 class="kt-portlet__head-title">
					Inventory
				</h3>
			</div>
		</div>
		<form id="inventory_form" class="inventory_form" method="POST" enctype="multipart/form-data">
			@csrf
			<div class="kt-portlet__body">
				<div class="row">
					@if(Auth::guard('user')->check())
					@if(Auth::user()->role == 1)
					<div class="form-group col-lg-3">
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
					<div class="form-group col-lg-2">
						<label>Warehouse </label>
						<select class="form-control changes select2" name="warehouse_id" id="warehouse_id"  >
							<option value="">Select</option>
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
					<div class="form-group col-lg-2">
						<label>Warehouse </label>
						<select class="form-control changes select2" name="warehouse_id" id="warehouse_id" >
							<option value="">Select</option>
							@foreach($warehouse as $sub)
							<option value="{{$sub->id}}">{{$sub->name}}</option>
							@endforeach
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
					<div class="form-group col-lg-2">
						<label>Warehouse </label>
						<select class="form-control changes select2" name="warehouse_id" id="warehouse_id" >
							<option value="">Select</option>
							@foreach($warehouse as $sub)
							<option value="{{$sub->id}}">{{$sub->name}}</option>
							@endforeach
						</select>
					</div>
					@endif
					<div class="kt-portlet__head-actions mt-5">
					<a href="javascript:void(0)" id="inventory_export" class="btn btn-brand btn-elevate btn-icon-sm"  style="margin-top: -10px;">
						<i class="fa fa-file-excel"></i> Export
					</a>
                    </div>
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
								<th>Warehouse Name</th>
								<th>Category</th>
								<th>Company</th>
								<th>Product Name</th>
								<th>Product Code</th>
								<th>Opening Stock</th>
								<th>Purchase</th>
								<th>Sale</th>
								<th>SI Blocking Qty</th> 
								<th>Closing Stock</th>
								@if(
									(Auth::guard('user')->check() && Auth::guard('user')->user()->role != 1)
									||
									(
										Auth::guard('employee')->check()
										&& !in_array(Auth::guard('employee')->user()->role, [2, 3])
									)
								)
									<th>Action</th>
								@endif
							</tr>
						</thead>
					</table>
				</div>
				<div class="kt-form__actions">
					<center>
						<!-- <button type="button" class="btn btn-brand submit" id="submit_basic" >Update Inventory</button> -->
					</center>
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

	<!-- stock transfer model-->
	<div class="modal fade" id="stockTransferModal" tabindex="-1" role="dialog">
		<div class="modal-dialog modal-lg" role="document">
			<form id="stockTransferForm">
				@csrf
				<input type="hidden" id="inventory_id" name="inventory_id">
				<div class="modal-content">
					<div class="modal-header">
						<h5 class="modal-title">
							<i class="fa fa-exchange"></i>
							Stock Transfer
						</h5>
						<button type="button" class="close text-white" data-dismiss="modal">
							<span>&times;</span>
						</button>
					</div>
					<div class="modal-body">
						<div class="card border-0 shadow-sm mb-3">
							<div class="card-header bg-light">
								<strong>
									<i class="fa fa-info-circle text-primary"></i>
									Transfer Information
								</strong>
							</div>
							<div class="card-body">
								<div class="form-row">
									<div class="form-group col-md-5">
										<label>
											<i class="fa fa-calendar"></i>
											Date
										</label>
										<input type="date" class="form-control" name="transfer_date" value="{{ date('Y-m-d') }}">
									</div>
									<div class="form-group col-md-5">
										<label>
											<i class="fa fa-building"></i>
											Branch
										</label>
										<!-- <input type="text" class="form-control bg-light" value="{{ Auth::guard('user')->check() ? Auth::user()->name : '' }}" readonly> -->
										 @php
											$branchName = '';

											if (Auth::guard('user')->check()) {
												$branchName = Auth::guard('user')->user()->name;
											} elseif (Auth::guard('employee')->check()) {
												$branchName = App\User::find(Auth::guard('employee')->user()->branch_id)->name ?? '';
											}
										@endphp

										<input type="text"
											class="form-control bg-light"
											value="{{ $branchName }}"
											readonly>
									</div>
								</div>
							</div>
						</div>
						<div class="card border-0 shadow-sm">
							<div class="card-header bg-light">
								<strong>
									<i class="fa fa-exchange"></i>
									Warehouse Details
								</strong>
							</div>
							<div class="card-body">
								<div class="form-row align-items-end">
									<div class="form-group col-md-4">
										<label>From Warehouse</label>
										<input type="text" id="from_warehouse" class="form-control bg-light" readonly>
										<input type="hidden" id="from_warehouse_id" name="from_warehouse_id">
									</div>
									<div class="col-md-1 text-center mb-3">
										<i class="fa fa-long-arrow-right fa-2x text-primary"></i>
									</div>
									<div class="form-group col-md-4">
										<label>To Warehouse</label>
										<select class="form-control select2" id="to_warehouse_id" name="to_warehouse_id">
											<option value="">Select Warehouse</option>
										</select>
									</div>
									<div class="form-group col-md-3">
										<label>
											<i class="fa fa-cubes"></i>
											Transfer Qty
										</label>
										<input type="number" class="form-control" name="qty" min="1" placeholder="Enter Qty">
									</div>
									<div class="form-group col-md-6">
										<label>Remark</label>
										<textarea class="form-control" name="remark" placeholder="Enter Remark"></textarea>
									</div>
								</div>
							</div>
						</div>
					</div>
					<div class="modal-footer">
						<button class="btn btn-light" data-dismiss="modal">
							<i class="fa fa-times"></i>
							Cancel
						</button>
						<button type="button" class="btn btn-success" id="saveTransfer">
							<i class="fa fa-check-circle"></i>
							Transfer Stock
						</button>
					</div>
				</div>
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
					url:'{{ route("user.warehouse.inventory") }}',
					data: function(d) {
						d.branch_id= $('#branch_id').val()
						d.productcategory_id= $('#productcategory_id').val()
						d.company_id= $('#company_id').val()
						d.warehouse_id= $('#warehouse_id').val()
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
						orderable: true,
						searchable: true,
						data: "warehouse_id",
					},
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
						'data': 'product_nm',
					},
					{
						orderable: false,
						searchable: true,
						'data': 'productcode',
					},
					{
						orderable: false,
						searchable: false,
						'data': 'opening_stock',
					},
					{
						orderable: false,
						searchable: false,
						'data': 'stock',
					},
					{
						orderable: false,
						searchable: false,
						'data': 'used',
					},
					{
						orderable: false,
						searchable: false,
						'data': 'si_blocking_qty', 
					},
					{
						orderable: false,
						searchable: false,
						'data': 'actual_inventory',
					},
					@if(
						(Auth::guard('user')->check() && Auth::guard('user')->user()->role != 1)
						||
						(
							Auth::guard('employee')->check()
							&& !in_array(Auth::guard('employee')->user()->role, [2, 3])
						)
					)
					{
						orderable: false,
						searchable: false,
						data: 'action',
					},
					@endif
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
	<script>
		$(document).on('click','.stock-transfer',function(){
			let inventory_id=$(this).data('id');
			$.ajax({
				url:"{{ route('user.get.transfer.warehouse') }}",
				type:"POST",
				headers:{
					'X-CSRF-TOKEN':$('meta[name="csrf-token"]').attr('content')
				},
				data:{
					inventory_id:inventory_id
				},
				success:function(res){
					$('#inventory_id').val(inventory_id);
					$('#from_warehouse').val(res.from_name);
					$('#from_warehouse_id').val(res.from_id);
					$('#to_warehouse_id').empty();
					$('#to_warehouse_id').append(
						'<option value="">Select Warehouse</option>'
						);
					$.each(res.warehouses,function(i,item){
						$('#to_warehouse_id').append(
							'<option value="'+item.id+'">'+item.name+'</option>'
							);
					});
					$('#stockTransferModal').modal('show');
				}
			});
		});
	</script>
	<script>
		$('#saveTransfer').click(function(){
			let qty = $('input[name="qty"]').val();
			let toWarehouse = $('#to_warehouse_id').val();
			if(qty == '' || qty <= 0){
				toastr.error('Please enter transfer quantity');
				return false;
			}
			if(toWarehouse == ''){
				toastr.error('Please select To Warehouse');
				return false;
			}
			$.ajax({
				url:"{{ route('user.transfer.stock') }}",
				type:"POST",
				data:$('#stockTransferForm').serialize(),
				headers:{
					'X-CSRF-TOKEN':
					$('meta[name="csrf-token"]').attr('content')
				},
				success:function(res){
					if(res.status == 'success'){
						toastr.success(res.message);
						$('#stockTransferModal').modal('hide');
						$('#stockTransferForm')[0].reset();
						$('#datatable_rows').DataTable().ajax.reload();
					}
				},
				error:function(xhr){
					toastr.error(
						xhr.responseJSON.message
						);
				}
			});
		});
		$('#inventory_export').click(function () {

			var branch_id = $('#branch_id').val();

			if (branch_id == '') {
				toastr.error('Please select a branch before exporting.');
				return false;
			}

			var url = "{{ route('user.inventory.export') }}";

			url += '?branch_id=' + branch_id;
			url += '&productcategory_id=' + $('#productcategory_id').val();
			url += '&company_id=' + $('#company_id').val();
			url += '&warehouse_id=' + $('#warehouse_id').val();
			url += '&product_id=' + $('#product_id').val();

			window.location.href = url;
		});
	</script>
	@endpush