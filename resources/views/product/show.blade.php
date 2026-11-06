 
{{-- @php
$permission = App\Group::where('id',Auth::user()->group_id)->first();
$per_exploded = explode(',',$permission->product);
@endphp
@if($per_exploded[2] == 1) --}}
@extends('main')
@section('content')
<div class="kt-container  kt-container--fluid  kt-grid__item kt-grid__item--fluid">
	<br>
	<div class="kt-portlet kt-portlet--mobile">
		<div class="kt-portlet__head">
			<div class="kt-portlet__head-label">
				<span class="kt-portlet__head-icon">
					<i class="kt-font-brand"></i>
				</span>
				<h3 class="kt-portlet__head-title">
					View Product
				</h3>
			</div>
			<div class="kt-portlet__head-toolbar">
				<div class="kt-portlet__head-wrapper">
					<div class="kt-portlet__head-actions">					
						<a href="{{URL::previous()}}" class="btn btn-brand btn-elevate btn-icon-sm">
							<i class="flaticon2-fast-back"></i>
							Back
						</a>
					</div>
				</div>
			</div>
		</div>
		<div class="kt-portlet__body">
			<div class="row">
				@if(Auth::guard('user')->check())
				@if(Auth::user()->role == 1)  
				<div class="form-group col-lg-3">
					<label>Branch  </label>
					<input  type="text" class="form-control"  value="{{$branchname}}" name="branch_id" readonly>

				<!-- 	<select name="branch_id" class="form-control select2 branch_id" id="branch_id" onchange="getcategory(this)" disabled>
						<option value="">Select Branch</option>
						@foreach($branch as $sub)
						<option value="{{$sub->id}}" @if($sub->id == $data->branch_id) selected @endif>{{$sub->name}}</option>
						@endforeach
					</select> -->
				</div>
				<?php $productcategorys = \App\ProductCategory::where('branch_id',$data->branch_id)->where('status',1)->get(); ?>
				<div class="form-group col-lg-3">
					<label>Category </label>
					<input  type="text" class="form-control"  value="{{$category}}" name="productcategory_id" readonly>


				<!-- 	<select name="productcategory_id"  id="productcategory_id" onchange="getcompany(this)" class="form-control select2" disabled>
						<option value="">Select Category</option>
						@foreach($productcategorys as $sub)
						<option value="{{$sub->id}}" @if($sub->id == $data->productcategory_id) selected @endif>{{$sub->name}}</option>
						@endforeach
					</select> -->
				</div>
				@endif
				@endif
				@if(Auth::guard('user')->check())
				@if(!(Auth::user()->role == 1)) 

				<div class="form-group col-lg-3">
					<label>Category </label>
					<input  type="text" class="form-control"  value="{{$category}}" name="productcategory_id" readonly>
					<!-- <select name="productcategory_id"  id="productcategory_id" onchange="getcompany(this)" class="form-control select2" disabled>
						<option value="">Select Category</option>
						@foreach($productcategory as $sub)
						<option value="{{$sub->id}}" @if($sub->id == $data->productcategory_id) selected @endif>{{$sub->name}}</option>
						@endforeach
					</select> -->
				</div>
				@endif
				@endif

				@if(Auth::guard('employee')->check())
				<div class="form-group col-lg-3">
					<label>Category </label>
					<input  type="text" class="form-control"  value="{{$category}}" name="productcategory_id" readonly>
				</div>
				@endif

				<?php
				$company = \App\Company::where('pro_category_id',$data->productcategory_id)->get();
				?>      
				<div class="form-group col-lg-3">
					<label>Company </label>
					<input  type="text" class="form-control"  value="{{$companys}}" name="company_id" readonly>
				<!-- 	<select name="company_id" id="company_id" class="form-control select2" disabled>
						<option value="">Select Company</option>
						@foreach($company as $sub)
						<option value="{{$sub->id}}" @if($sub->id == $data->company_id) selected @endif>{{$sub->name}}</option>
						@endforeach
					</select> -->
				</div>
				<div class="form-group col-lg-3">
					<label>Product</label>
					<input type="text" class="form-control" placeholder="Product" value="{{$data->product}}" name="product" readonly>
				</div>
				<div class="form-group col-lg-3">
					<label>SKU Code</label>
					<input type="text" class="form-control" placeholder="SKU Code" value="{{$data->code}}" name="code" readonly>
				</div>
				<div class="form-group col-lg-3">
					<label>Product Price</label>
					<input type="text" class="form-control" placeholder="Product Price" value="{{$data->price}}" name="price" readonly>
				</div>
				<div class="form-group col-lg-3">
					<label>Product Unit</label>
					<input type="text" class="form-control" placeholder="Product Unit" value="{{$data->unit}}" name="unit" readonly>
				</div>
				<div class="form-group col-lg-3">
					<label>Product Description</label>
					<textarea class="form-control" placeholder="Product Description" readonly name="description">{{$data->description}}</textarea>  
				</div>
				<div class="form-group col-lg-3">
					<label>Image1</label>
					
					@if($data->image1)
					<img src="{{ asset('/product/'.$data->image1) }}" height="70px" width="70px">
					@endif
				</div>
				{{-- <div class="form-group col-lg-3">
					<label>Image2</label>

					@if($data->image2)
					<img src="{{ asset('/product/'.$data->image2) }}" height="70px" width="70px" >
					@endif
				</div> --}}
				<div class="form-group col-lg-3">
					<label>Product Discount </label>
					<input type="number" class="form-control" value="{{$data->discount}}" placeholder="Product Discount" name="discount" readonly>
				</div>
				<div class="form-group col-lg-3">
					<label>Product Discount Type</label>
					<select class="form-control" name="discount_type" id="discount_type" disabled>
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
		</div>
	</div>
</div>
@endsection
{{-- @endif --}}
