@extends('main')

@section('content')

<meta name="csrf-token" content="{{ csrf_token() }}" />



<div class="kt-container  kt-container--fluid  kt-grid_item kt-grid_item--fluid">

	<br>

	<div class="kt-portlet kt-portlet--mobile">

		<div class="kt-portlet__head">

			<div class="kt-portlet__head-label">

				<h3 class="kt-portlet__head-title">

					PDF

				</h3>

			</div>

			<div class="kt-portlet__head-toolbar">

				<div class="kt-portlet__head-wrapper">

					

					<div class="kt-portlet__head-actions">

						<a href="{{ route('user.quotations.index') }}" class="btn btn-brand btn-elevate btn-icon-sm"><i class="flaticon2-fast-back"></i>Back</a>

					</div>

					

				</div>

			</div>

		</div>

		<div class="kt-portlet__body">





			

			<div id="kt_table_1_wrapper" class="dataTables_wrapper dt-bootstrap4">

				<form class="kt-form kt-form--label-right form_price"  method="POST">

					@csrf

					

					<table class="table table-striped table-bordered table-hover table-checkable datatable" id="datatable_rows">



						<thead>

							<tr>

								<th width="10%">Name</th>

								<th width="10%">Amount</th>

								<th width="10%">Action</th>

							</tr>

						</thead>

						<tbody>
							<?php $quotation = App\Quotation::where('id',$id)->first(); ?>
							<tr>
								 @if(Auth::guard('user')->check())
								 @if(Auth::user()->role == 1)
									<td>Patel Interior</td>
								@elseif(Auth::user()->role == 2)
									<td>{{Auth::user()->name}}</td>
								 @endif
								 @endif

								 @if(Auth::guard('employee')->check())
								 @php $name = App\User::where('id',Auth::user()->branch_id)->value('name'); @endphp
								 	<td>{{$name}}</td>
								 @endif




								<td>

									
										<div class="kt-checkbox-inline">
											<label class="kt-checkbox" cheched="">
												<input type="checkbox" name="image" value="1" @if($quotation->image == 1) checked @endif  > Image
												<span></span>
											</label>
											<label class="kt-checkbox">
												<input type="checkbox" name="code" id="code" value="1" @if($quotation->code == 1) checked @endif > Code
												<span></span>
											</label>
											<label class="kt-checkbox">
												<input type="checkbox" name="item_name"  value="1" @if($quotation->item_name == 1) checked @endif> Item Name
												<span></span>
											</label>
											<label class="kt-checkbox">
												<input type="checkbox" name="description" value="1" @if($quotation->description == 1) checked @endif> Description
												<span></span>
											</label>
											<label class="kt-checkbox">
												<input type="checkbox" name="qty" value="1" @if($quotation->qty == 1) checked @endif> Qty
												<span></span>
											</label>
											<label class="kt-checkbox">
												<input type="checkbox" name="mrp" value="1" @if($quotation->mrp == 1) checked @endif> MRP
												<span></span>
											</label><label class="kt-checkbox">
												<input type="checkbox" name="discount" value="1" @if($quotation->discount == 1) checked @endif> Discount
												<span></span>
											</label>
											<label class="kt-checkbox">
												<input type="checkbox" name="discount_price" value="1" @if($quotation->discount_price == 1) checked @endif> Discount Price
												<span></span>
											</label>
											<label class="kt-checkbox">
												<input type="checkbox" name="sp_price" value="1" @if($quotation->sp_price == 1) checked @endif> SP. Price
												<span></span>
											</label>
											<label class="kt-checkbox">
												<input type="checkbox" name="gst" value="1" @if($quotation->gst == 1) checked @endif> Gst
												<span></span>
											</label>
											<label class="kt-checkbox">
												<input type="checkbox" name="gst_amt" value="1" @if($quotation->gst_amt == 1) checked @endif> Gst Amt.
												<span></span>
											</label>
											<label class="kt-checkbox">
												<input type="checkbox" name="sp_price_total" value="1" @if($quotation->sp_price_total == 1) checked @endif> Sp. Price Total
												<span></span>
											</label>

										</div>
										
									

								</td>

								<td>

									 <button type="button" style="background-color:#2675d1;"  class="btn btn-primary submit_price" id="submit_price">Save</button> 

									<button style="background: white;" title="PATEL" onclick="getpdf('{{$id}}')" id="pdf_{{$id}}" class="btn btn-sm btn-clean btn-icon btn-icon-md pdf">

										<i style="color: black;" class="fas fa-print"></i>

									</button>&nbsp&nbsp&nbsp

								</td>

							</tr>

							<tr>

								<td>Kuche</td>

								<?php $quotation = App\Quotation::where('id',$id)->first(); ?>



								<td> 

									<input type="number" class="form-control q_price2" name="q_price2" value="{{$quotation->q_price2}}">

								</td>

								<td>

									<button type="button" style="background-color:#2675d1;" class="btn btn-primary submit_price" id="submit_price">Save</button>

									<button style="background: white;" title="KUCHE" onclick="getpdf2('{{$id}}')" id="pdf2_{{$id}}" class="btn btn-sm btn-clean btn-icon btn-icon-md pdf2">

										<i style="color: black;" class="fas fa-print"></i>

									</button>&nbsp&nbsp&nbsp

								</td>

							</tr>

							<tr>

								<td>Elementra</td>

								<td> 

									<input type="number" class="form-control q_price3" name="q_price3" value="{{$quotation->q_price3}}">

								</td>

								<td>

									<button type="button" style="background-color:#2675d1;" class="btn btn-primary submit_price" id="submit_price">Save</button>

									<button style="background: white;" title="ELEMENTRA" onclick="getpdf3('{{$id}}')" id="pdf3_{{$id}}" class="btn btn-sm btn-clean btn-icon btn-icon-md pdf3">

										<i style="color: black;" class="fas fa-print"></i>

									</button>

								</td>

							</tr>

							<tr>

								<td>Nolte</td>

								<td> 

									<input type="number" class="form-control q_price4" name="q_price4"value="{{$quotation->q_price4}}" >

								</td>

								<td>

									<button type="button" style="background-color:#2675d1;" class="btn btn-primary submit_price" id="submit_price">Save</button>

									<button style="background: white;" title="NOLTE" onclick="getpdf4('{{$id}}')" id="pdf4_{{$id}}" class="btn btn-sm btn-clean btn-icon btn-icon-md pdf4">

										<i style="color: black;" class="fas fa-print"></i>

									</button>

								</td>

							</tr>

						</tbody>

					</table>

				</form>

			</div>

		</div>



	</div>

</div>



@stop

@push('scripts')



<script type="text/javascript">







	function getpdf(id,name,invoiceno)

	{

		var parent = $('#pdf_'+id);           

		var image = $('#image').val();


		parent.attr('disabled',true);



		parent.html('<i class="fa fa-spinner fa-spin"></i>');



		$.ajax({

			type: "POST",

			url: "{{ route('user.quotation.pdf') }}",

			data: {

				'_token': $('input[name="_token"]').val(),

				'id': id

			},

			xhrFields: {

				responseType: 'blob'

			},

			success: function(blob) {

				parent.attr('disabled',false);



				parent.html('<i style="color: black;" class="fas fa-print"></i >');



				var link=document.createElement('a');

				link.href=window.URL.createObjectURL(blob);

				link.download= id+'-Quotation.pdf';

				link.click();

			}

		});

	}





	function getpdf2(id,name,invoiceno)

	{

		var parent = $('#pdf2_'+id);           



		parent.attr('disabled',true);



		parent.html('<i class="fa fa-spinner fa-spin"></i>');



		$.ajax({

			type: "POST",

			url: "{{ route('user.quotation2.pdf') }}",

			data: {

				'_token': $('input[name="_token"]').val(),

				'id': id

			},

			xhrFields: {

				responseType: 'blob'

			},

			success: function(blob) {

				parent.attr('disabled',false);



				parent.html('<i style="color: black;" class="fas fa-print"></i >');



				var link=document.createElement('a');

				link.href=window.URL.createObjectURL(blob);

				link.download= id+'-Quotation.pdf';

				link.click();

			}

		});

	}





	function getpdf3(id,name,invoiceno)

	{

		var parent = $('#pdf3_'+id);           



		parent.attr('disabled',true);



		parent.html('<i class="fa fa-spinner fa-spin"></i>');



		$.ajax({

			type: "POST",

			url: "{{ route('user.quotation3.pdf') }}",

			data: {

				'_token': $('input[name="_token"]').val(),

				'id': id

			},

			xhrFields: {

				responseType: 'blob'

			},

			success: function(blob) {

				parent.attr('disabled',false);



				parent.html('<i style="color: black;" class="fas fa-print"></i >');



				var link=document.createElement('a');

				link.href=window.URL.createObjectURL(blob);

				link.download= id+'-Quotation.pdf';

				link.click();

			}

		});

	}





	function getpdf4(id,name,invoiceno)

	{

		var parent = $('#pdf4_'+id);           



		parent.attr('disabled',true);



		parent.html('<i class="fa fa-spinner fa-spin"></i>');



		$.ajax({

			type: "POST",

			url: "{{ route('user.quotation4.pdf') }}",

			data: {

				'_token': $('input[name="_token"]').val(),

				'id': id

			},

			xhrFields: {

				responseType: 'blob'

			},

			success: function(blob) {

				parent.attr('disabled',false);



				parent.html('<i style="color: black;" class="fas fa-print"></i >');



				var link=document.createElement('a');

				link.href=window.URL.createObjectURL(blob);

				link.download= id+'-Quotation.pdf';

				link.click();

			}

		});

	}



</script>



<script type="text/javascript">

	$(document).ready(function() {

		$(".submit_price").on("click", function(e) {

			e.preventDefault();

			if ($(".form_price").valid()) {

				console.log('djn');

				$.ajax({

					type: "POST",

					url: "{{ route('user.quotation.price',$id) }}",

					data: new FormData($('.form_price')[0]),

					processData: false,

					contentType: false,

					success: function(data) {

						if (data.status === 'success') {



							location.reload();





							toastr["success"]("Update Price Successfully", "Success");

						} else if (data.status === 'error') {

							location.reload();

							toastr["error"]("Something went wrong", "Error");

						}

					},

				});

			} else {

				e.preventDefault();

			}

		});

	});



</script>

















@endpush