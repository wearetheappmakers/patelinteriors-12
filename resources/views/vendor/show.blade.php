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
					View Vendor
				</h3>
			</div>
			<div class="kt-portlet__head-toolbar">
				<div class="kt-portlet__head-wrapper">
					<div class="kt-portlet__head-actions">					
						<a href="{{ route('user.vendor.index') }}" class="btn btn-brand btn-elevate btn-icon-sm">
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
				<div class="form-group col-lg-2">
					<label>Branch  </label>
					<select name="branch_id" class="form-control select2" onchange="getcategory(this)" disabled>
						<option value="">Select Branch</option>
						@foreach($branch as $sub)
						<option value="{{$sub->id}}" @if($sub->id == $data->branch_id) selected @endif>{{$sub->name}}</option>
						@endforeach
					</select>
				</div>
				@endif
				@endif
				<div class="form-group col-sm-2">
					<label>Name</label>
					<input type="text" name="name" placeholder="Enter Name" value="{{$data->name}}" class="form-control" readonly>
				</div>
				<div class="form-group col-sm-2">
					<label>Number</label>
					<input type="number" name="number" placeholder="Enter Number" value="{{$data->number}}" class="form-control" readonly>
				</div>
				<div class="form-group col-sm-2">
					<label>GST No</label>
					<input type="text" name="gst_no" placeholder="Enter GST No" value="{{$data->gst_no}}" class="form-control" readonly>
				</div>
				<div class="form-group col-sm-2">
					<label>Company Name</label>
					<input type="text" name="company_name" placeholder="Enter Company Name" value="{{$data->company_name}}" class="form-control" readonly>
				</div>
				<div class="form-group col-sm-2">
					<label>Company Address</label>
					<textarea name="company_address" class="form-control" placeholder="Enter Company Address" readonly>{{$data->company_address}}</textarea>
				</div>
				<div class="form-group col-sm-2">
					<label>Email</label>
					<input type="email" name="email" placeholder="Enter Email" value="{{$data->email}}" class="form-control" readonly>
				</div>
				<div class="form-group col-sm-2">
					<label>Address</label>
					<textarea name="address" class="form-control" placeholder="Enter Address" readonly>{{$data->address}}</textarea>
				</div>
				<div class="form-group col-sm-2">
					<label>Country</label>
					<input type="text" class="form-control" placeholder="Country" value="{{ ($data->country_id) ? $data->country->name : '' }}" readonly>
				</div>
				<div class="form-group col-sm-2">
					<label>State</label>
					<input type="text" class="form-control" placeholder="State" value="{{ ($data->state_id) ? $data->state->name : '' }}" readonly>
				</div>
				<div class="form-group col-sm-2">
					<label>City</label>
					<input type="text" class="form-control" placeholder="City" value="{{ ($data->city_id) ? $data->city->name : '' }}" readonly>
				</div>
			</div>
		</div>
	</div>
</div>
@endsection