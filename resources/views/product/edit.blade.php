{{-- @php
$permission = App\Group::where('id',Auth::user()->group_id)->first();
$per_exploded = explode(',',$permission->product);
@endphp
@if($per_exploded[2] == 1) --}}
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
				<option value="{{$sub->id}}" @if($sub->id == $data->branch_id) selected @endif>{{$sub->name}}</option>
				@endforeach
			</select>
		</div>
			<?php $productcategorys = \App\ProductCategory::where('branch_id',$data->branch_id)->where('status',1)->get(); ?>
		<div class="form-group col-lg-3">
			<label>Category </label>
			<select name="productcategory_id"  id="productcategory_id" onchange="getcompany(this)" class="form-control select2" required>
				<option value="">Select Category</option>
				@foreach($productcategorys as $sub)
				<option value="{{$sub->id}}" @if($sub->id == $data->productcategory_id) selected @endif>{{$sub->name}}</option>
				@endforeach
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
				<option value="{{$sub->id}}" @if($sub->id == $data->productcategory_id) selected @endif>{{$sub->name}}</option>
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
				<option value="{{$sub->id}}" @if($sub->id == $data->productcategory_id) selected @endif>{{$sub->name}}</option>
				@endforeach
			</select>
		</div>
		@endif
		
		<?php
		$company = \App\Company::where('pro_category_id',$data->productcategory_id)->get();
		?>      
		<div class="form-group col-lg-3">
			<label>Company </label>
			<select name="company_id" id="company_id" class="form-control select2" required>
				<option value="">Select Company</option>
				@foreach($company as $sub)
				<option value="{{$sub->id}}" @if($sub->id == $data->company_id) selected @endif>{{$sub->name}}</option>
				@endforeach
			</select>
		</div>
		<div class="form-group col-lg-3">
			<label>Product</label>
			<input type="text" class="form-control" placeholder="Product" value="{{$data->product}}" name="product" required>
		</div>
		<div class="form-group col-lg-3">
			<label>SKU Code</label>
			<input type="text" class="form-control" placeholder="SKU Code" value="{{$data->code}}" name="code" required>
		</div>
		<div class="form-group col-lg-3">
			<label>Product Price</label>
			<input type="text" class="form-control" placeholder="Product Price" value="{{$data->price}}" name="price" required>
		</div>
		<div class="form-group col-lg-3">
			<label>Product Unit</label>
			<input type="text" class="form-control" placeholder="Product Unit" value="{{$data->unit}}" name="unit" required>
		</div>
		<div class="form-group col-lg-3">
			<label>Product Description</label>
			<textarea class="form-control" placeholder="Product Description" name="description">{{$data->description}}</textarea>  
		</div>
		<div class="form-group col-lg-3">
			<label>Image1</label>
			<input type="file" name="image1" id="image1" value="{{ $data->image1 }}"  class="form-control">
			@if($data->image1)
			<img src="{{ asset('/product/'.$data->image1) }}" height="70px" width="70px">
			@endif
		</div>
		{{-- <div class="form-group col-lg-3">
			<label>Image2</label>
			<input type="file" name="image2" id="image2" value="{{ $data->image2 }}"  class="form-control">
			@if($data->image2)
			<img src="{{ asset('/product/'.$data->image2) }}" height="70px" width="70px">
			@endif
		</div> --}}
		<div class="form-group col-lg-3">
			<label>Product Discount </label>
			<input type="number" class="form-control" value="{{$data->discount}}" placeholder="Product Discount" name="discount" >
		</div>
		<div class="form-group col-lg-3">
			<label>Product Discount Type</label>
			<select class="form-control" name="discount_type" id="discount_type" required>
				<option value="0" @if($data->discount_type == 0) selected @endif>None</option>
				<option value="1" @if($data->discount_type == 1) selected @endif>Popular</option>
				<option value="2" @if($data->discount_type == 2) selected @endif>Premium</option>
				<option value="3" @if($data->discount_type == 3) selected @endif>Luxury</option>
				<option value="4" @if($data->discount_type == 4) selected @endif>Good</option>
				<option value="5" @if($data->discount_type == 5) selected @endif>Best</option>
				<option value="6" @if($data->discount_type == 6) selected @endif>Better</option>
				
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