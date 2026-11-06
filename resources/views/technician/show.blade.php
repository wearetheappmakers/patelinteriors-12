@extends('main')
@section('content')
<?php $employee = \App\Employee::where('branch_id',$data->branch_id)->get(); ?>
<meta name="csrf-token" content="{{ csrf_token() }}" />
<div class="kt-container  kt-container--fluid  kt-grid_item kt-grid_item--fluid">
	<br>
	<div class="kt-portlet kt-portlet--mobile">
		<div class="kt-portlet__head">
			<div class="kt-portlet__head-label">
				<h3 class="kt-portlet__head-title">
					Show Technician
				</h3>
			</div>
		</div>
		<div class="kt-portlet__body">
			<div class="row">
				@if(Auth::guard('user')->check())
				@if(Auth::user()->role == 1)
				<div class="form-group col-lg-3">
					<label>Branch  </label>
					<input type="text" class="form-control" placeholder="Branch" value="{{ $data->branch->name }}" disabled>
				</div>
				@endif
				<div class="form-group col-lg-3">
					<label>Employee  </label>
					<select disabled name="employee_id" class="form-control select2" id="employee_id"  required >
						<option value="">Select</option>
						@foreach($employee as $sub)
						<option value="{{$sub->id}}" @if($sub->id == $data->employee_id) selected @endif>{{$sub->name}}</option>
						@endforeach
					</select>
				</div>
				@endif

				@if(Auth::guard('employee')->check() && Auth::user()->role == 4)
				<div class="form-group col-lg-3">
					<label>Employee </label>
					<select disabled name="employee_id" class="form-control select2" id="employee_id"  required >
						<option value="">Select</option>
						@foreach($employee as $sub)
						<option value="{{$sub->id}}" @if($sub->id == $data->employee_id) selected @endif>{{$sub->name}}</option>
						@endforeach
					</select>
				</div>    
				@endif

				@if(Auth::guard('user')->check())
				@if(Auth::user()->role == 1 || Auth::user()->role == 2) 
				<div class="form-group col-lg-3">
					<label>Category</label>
					<?php  $productcategory = App\ProductCategory::where('branch_id',$data->branch_id)->where('status',1)->get(); ?>
					<select class="form-control productcategory_id select2" id="productcategory_id" disabled name="productcategory_id" onchange="getcompany(this)"  >
						<option value="">Select</option>
						@foreach($productcategory as $value)
						<option value="{{$value->id}}" @if($value->id == $data->productcategory_id) selected @endif>{{$value->name}}</option>
						@endforeach
					</select>
				</div>
				@endif
				@endif

				@if(Auth::guard('employee')->check())
				<div class="form-group col-lg-3">
					<label>Category</label>
					<?php  $productcategory = App\ProductCategory::where('branch_id',$data->branch_id)->where('status',1)->get(); ?>
					<select class="form-control productcategory_id select2" id="productcategory_id" disabled name="productcategory_id" onchange="getcompany(this)"  >
						<option value="">Select</option>
						@foreach($productcategory as $value)
						<option value="{{$value->id}}" @if($value->id == $data->productcategory_id) selected @endif>{{$value->name}}</option>
						@endforeach
					</select>
				</div>
				@endif

				<div class="form-group col-lg-3">
					<label>Company</label>
					<select class="form-control company_id select2" id="company_id" disabled name="company_id" onchange="getproduct(this)" required>
						<option value="">Select</option>
						@foreach($company as $value)
						<option value="{{$value->id}}" @if($value->id == $data->company_id) selected @endif >{{$value->name}}</option>
						@endforeach
					</select>
				</div>

				<div class="form-group col-lg-3">
					<label>Technician Name</label>
					<input type="text" class="form-control" placeholder="Enter Technician Name" disabled name="technician_name" value="{{ $data->technician_name }}" required>
				</div>
			</div>
		</div>
		<div class="kt-portlet__foot">
			<div class="kt-form__actions">
				<center>
					<a href="{{ route('user.technician.index') }}"><button type="button" class="btn btn-outline-secondary">Cancel</button></a>
				</center>
			</div>
		</div>
	</div>
</div>
@stop