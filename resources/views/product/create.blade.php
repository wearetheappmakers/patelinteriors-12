{{-- @php

$permission = App\Group::where('id',Auth::user()->group_id)->first();

$per_exploded = explode(',',$permission->product);

@endphp

@if($per_exploded[1] == 1) --}}



<meta name="csrf-token" content="{{ csrf_token() }}" />

<div class="kt-portlet__body">

	<div class="row">

		@if(Auth::guard('user')->check())

		@if(Auth::user()->role == 1)  

		<div class="form-group col-lg-3">

			<label>Branch  </label>

			<select name="branch_id" class="form-control select2 branch_id" id="branch_id" onchange="getcategory(this)">

				<option value="">Select Branch</option>

				@foreach($branch as $sub)

				<option value="{{$sub->id}}">{{$sub->name}}</option>

				@endforeach

			</select>

		</div>

		<div class="form-group col-lg-3">

			<label>Category </label>

			<select name="productcategory_id"  id="productcategory_id" onchange="getcompany(this)" class="form-control select2" required>

				<option value="">Select Category</option>

			</select>

		</div>

		@endif

		@endif



		@if(Auth::guard('user')->check())

		@if(!(Auth::user()->role == 1)) 

		<div class="form-group col-lg-3">

			<label>Category </label>

			<select name="productcategory_id"  id="productcategory_id" onchange="getcompany(this)" class="form-control select2" required>

				<option value="">Select Category</option>

				@foreach($productcategory as $sub)

				<option value="{{$sub->id}}">{{$sub->name}}</option>

				@endforeach

			</select>

		</div>

		@endif

		@endif

		@if(Auth::guard('employee')->check())
		<div class="form-group col-lg-3">
			<label>Category </label>
			<select name="productcategory_id"  id="productcategory_id" onchange="getcompany(this)" class="form-control select2" required>
				<option value="">Select Category</option>
				@foreach($productcategory as $sub)
				<option value="{{$sub->id}}">{{$sub->name}}</option>
				@endforeach
			</select>
		</div>
		@endif
		

		<div class="form-group col-lg-3">

			<label>Company </label>

			<select name="company_id"  id="company_id" class="form-control select2" required>

				<option value="">Select</option>



			</select>

		</div>

		<div class="form-group col-lg-3">

			<label>Product</label>

			<input type="text" class="form-control" placeholder="Product" name="product" required>

		</div>



		<div class="form-group col-lg-3">

			<label>SKU Code</label>

			<input type="text" class="form-control" placeholder="SKU Code" name="code" required>

		</div>



		<div class="form-group col-lg-3">

			<label>Product Price</label>

			<input type="number" class="form-control" placeholder="Product Price" name="price" required>

		</div>



		<div class="form-group col-lg-3">

			<label>Product Unit</label>

			<input type="text" class="form-control" placeholder="Product Unit" name="unit" required>

		</div>



		<div class="form-group col-lg-3">

			<label>Product Description</label>

			<textarea class="form-control" placeholder="Product Description" name="description"></textarea>  

		</div>



		<div class="form-group col-lg-3">

			<label>Image1</label>

			<input type="file" class="form-control" placeholder="" name="image1" >

		</div>



		{{-- <div class="form-group col-lg-3">

			<label>Image2</label>

			<input type="file" class="form-control" placeholder="" name="image2" >

		</div> --}}



		<div class="form-group col-lg-3">

			<label>Product Discount </label>

			<input type="number" class="form-control" placeholder="Product Discount" name="discount" >

		</div>



		<div class="form-group col-lg-3">

			<label>Product Discount Type</label>

			<select class="form-control" name="discount_type" id="discount_type" required>

				<option value="0">None</option>

				<option value="1">Popular</option>

				<option value="2">Premium</option>

				<option value="3">Luxury</option>

				<option value="4">Good</option>

				<option value="5">Best</option>

				<option value="6">Better</option>

				

			</select>

		</div>

		

		

	</div>

	<!-- <div class="row">

		<div class="col-lg-3">

			@include('layouts.status_checkbox',array('data' => ""))

		</div>



	</div> -->

</div>

{{-- @endif --}}



<script type="text/javascript">

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



	$('.select2').select2({

		

	});

</script>

<script type="text/javascript">

	function getcategory($this){



		var branch_id = $($this).val();

		var id = $($this).data('id');

		$.ajax({

			type:"POST",

			headers: {

				'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')

			},

			url:"{{ route('user.getcategory') }}",

			data: {

				'branch_id': branch_id

			},

			success: function(data){

				$('#productcategory_id').html(data);

			}

		});



	};

</script>