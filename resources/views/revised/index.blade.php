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

					Revised Quotation

				</h3>

			</div>

			<div class="kt-portlet__head-toolbar">

				<div class="kt-portlet__head-wrapper">

					<div class="kt-portlet__head-actions">					

						

						 <a href="{{ route('user.quotations.index') }}" class="btn btn-brand btn-elevate btn-icon-sm">

                                <i class="flaticon2-fast-back"></i>

                                Back

                            </a>

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

							<th>id</th>

							<th>Date</th>

							<th>Time</th>

							<th>Action</th>

						</tr>

					</thead>

				</table>

			</div>



			

		</div>

		@include('layouts.multiple_action', array(

		'table_name' => 'revised_quotation',

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



			ajax: "{{ route('user.revisedquotation.index',$id) }}",



			columns: [

			

			{

				orderable: false,

				searchable: true,

				data: "id"

			},

			{

				orderable: false,

				searchable: true,

				data: "date"

			},

			{

				orderable: false,

				searchable: true,

				data: "time"

			},

			{

				orderable: false,

				searchable: true,

				data: "action"

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