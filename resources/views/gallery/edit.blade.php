@extends('main')

@section('content')

<meta name="csrf-token" content="{{ csrf_token() }}" />

<div class="kt-content  kt-grid__item kt-grid__item--fluid kt-grid kt-grid--hor" id="kt_content">

	<br>

	<div class="kt-container  kt-container--fluid  kt-grid__item kt-grid__item--fluid">        

		<link href="{{ asset('assets/plugins/custom/datatables/datatables.bundle.css')}}" rel="stylesheet" type="text/css" />

		<div class="kt-portlet kt-portlet--mobile">

			<div class="kt-portlet__head kt-portlet__head--lg">

				<div class="kt-portlet__head-label">

					<h3 class="kt-portlet__head-title">

						Gallery Assign

					</h3>

				</div>

				<div class="kt-portlet__head-toolbar">

					<div class="kt-portlet__head-wrapper">

						<div class="kt-portlet__head-actions">



						</div>

					</div>

				</div>

			</div>



			<div class="kt-portlet__foot">

				<form class="kt-form kt-form--label-right form_assign" id="form_assign" >

					@csrf

					<div class="row">

						@if(Auth::guard('user')->check())

						@if(Auth::user()->role == 1)  

						<div class="form-group col-lg-2">

							<label>Branch  </label>

							<select name="branch_id" class="form-control select2" >

								<option value="">Select Branch</option>

								@foreach($branch as $sub)

								<option value="{{$sub->id}}" @if($sub->id == $gallery->branch_id) selected @endif>{{$sub->name}}</option>

								@endforeach

							</select>

						</div>

						@endif

						@endif

						<div class="form-group col-sm-2">

							<label>Category</label>

							<select class="form-control productcategory_id select2" id="productcategory_id" name="productcategory_id"  onchange="getcompany(this)"   >

								<option value="">Select</option>

								@foreach($productcategory as $value)

								<option value="{{$value->id}}"  @if($value->id == $gallery->productcategory_id) selected @endif>{{$value->name}}</option>

								@endforeach

							</select>

						</div>

						<?php

						$company = \App\Company::where('pro_category_id',$gallery->productcategory_id)->get();

						?> 

						<div class="form-group col-sm-2">

							<label>Company</label>

							<select class="form-control company_id select2" id="company_id" name="company_id" onchange="getproduct(this)" >

								<option value="">Select</option>

								@foreach($company as $sub)

								<option value="{{$sub->id}}" @if($sub->id == $gallery->company_id) selected @endif>{{$sub->name}}</option>

								@endforeach

							</select>

						</div>

						<?php

						$product = \App\Product::where('company_id',$gallery->company_id)->get();

						?>  

						<div class="form-group col-sm-2">

							<label>Product</label>

							<select class="form-control product_id select2" id="product_id" name="product_id"  >

								<option value="">Select</option>

								@foreach($product as $sub)

								<option value="{{$sub->id}}" @if($sub->id == $gallery->product_id) selected @endif>{{$sub->product}}</option>

								@endforeach

							</select>

						</div>



						<div class="form-group col-sm-2">

							<label>Image</label>



							@if($gallery->gallery)

							<img src="{{ asset('/gallery/'.$gallery->gallery) }}" height="70px" width="70px">



							@endif

						</div>

					</div>

				</form>

				<div class="kt-portlet__foot">

					<div class="kt-form__actions">

						<center>

							<button type="button" class="btn btn-brand submit_assign" id="submit_assign" >Submit</button>

							<a href="{{route('user.gallery.index')}}"><button type="button" class="btn btn-outline-secondary">Cancel</button></a>

						</center>

					</div>

				</div>



			</div>

		</div>

	</div>

</div>

@stop

@push('scripts')

<script type="text/javascript">



	$(document).ready(function() {

		$("#submit_assign").on("click", function(e) {

			console.log('basic');

			e.preventDefault();



			if ($(".form_assign").valid()) {

				$.ajax({

					type: "POST",

					url: "{{ route('user.gallery.assign.store',$id) }}",

					data: new FormData($('.form_assign')[0]),

					processData: false,

					contentType: false,

					success: function(data) {

						if (data.status === 'success') {

							window.location="{{ route('user.gallery.index') }}";

							toastr["success"]("Gallery Assign Updated Successfully", "Success");

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

	function getcompany($this){



		var productcategory_id = $($this).val();

		var id = $($this).data('id');

		$.ajax({

			type:"POST",

			headers: {

				'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')

			},

			url:"{{ route('user.lead.getcompany') }}",

			data: {

				'productcategory_id': productcategory_id

			},

			success: function(data){
				console.log(data);
				$('#company_id').html(data.data);

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

			url:"{{ route('user.lead.getproduct') }}",

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