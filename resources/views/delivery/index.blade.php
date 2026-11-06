@extends('main')
@section('content')

<div class="kt-container  kt-container--fluid  kt-grid__item kt-grid__item--fluid">
	<br>
	<div class="kt-portlet kt-portlet--mobile">
		<div class="kt-portlet__head kt-portlet__head--lg">
			<div class="kt-portlet__head-label">
				<span class="kt-portlet__head-icon">
					<i class="kt-font-brand flaticon2-open-box"></i>
				</span>
				<h3 class="kt-portlet__head-title">
					Delivery
				</h3>
			</div>
			<div class="kt-portlet__head-toolbar">
				<div class="kt-portlet__head-wrapper">
					<div class="kt-portlet__head-actions">					
						
						
					</div>
				</div>
			</div>
		</div>
		<form id="inventory_form" class="inventory_form" method="POST" enctype="multipart/form-data">

				
		<div class="kt-portlet__body">
			<div id="kt_table_1_wrapper" class="dataTables_wrapper dt-bootstrap4">
				<table class="table table-striped table-bordered table-hover table-checkable datatable" id="datatable_rows">
					@csrf
					<thead>
						<tr>
							<th>Product Name</th>
							<th>Qty</th>
							<th>Used</th>
							<th>Return</th>
							<th>Add Stock</th>
							<th>Return</th>
						</tr>
					</thead>
				</table>
			</div>

			<div class="kt-form__actions">
					<center>
						<button type="button" class="btn btn-brand submit" id="submit_basic" >Update Inventory</button>
						 <a href="{{route('user.quotations.index')}}"><button type="button" class="btn btn-outline-secondary">Cancel</button></a>

					</center>
				</div>
		</div>
		@include('layouts.multiple_action', array(
		'table_name' => 'quotation_child',
		'is_orderby'=>'',
		'folder_name'=>'',
		'action' => array()
		))
	</form>
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

			ajax: "{{ route('user.delivery.index',$id) }}",

			columns: [
			
			{
				orderable: false,
				searchable: true,
				data: "product_id"
			},

			{
				orderable: false,
				searchable: true,
				data: "qty"
			},

			{
				orderable: false,
				searchable: true,
				data: "used"
			},

			{
				orderable: false,
				searchable: true,
				data: "return"
			},

			{
				orderable: false,
				searchable: true,
				data: "add_inventory"
			},

			{
				orderable: false,
				searchable: true,
				data: "update_return"
			},
			
			
			]

		});

	});


		$(".submit").on("click", function (e)

		{
			e.preventDefault();

			if ($(".inventory_form").valid())

			{

				$.ajax({

					type: "POST",

					url: "{{route('user.delivery.update',$id)}}",

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

			else

			{

				e.preventDefault();

			}

		});

</script>
@endpush