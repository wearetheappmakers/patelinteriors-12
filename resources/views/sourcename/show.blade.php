  
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
					View Source Name
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
				<div class="form-group col-lg-2">
					<label>Branch  </label>
					<select name="branch_id" class="form-control select2" onchange="getleadsource(this)" disabled>
						<option value="">Select Branch</option>
						@foreach($branch as $sub)
						<option value="{{$sub->id}}" @if($sub->id == $data->branch_id) selected @endif>{{$sub->name}}</option>
						@endforeach
					</select>
				</div>
				<?php $sources = App\LeadSource::where('branch_id',$data->branch_id)->get(); ?>
				<div class="form-group col-lg-2">
					<label>Source </label>
					<select name="lead_sourceid" id="lead_sourceid" class="form-control select2" disabled>
						<option value="">Select</option>
						@foreach($sources as $sub)
						<option value="{{$sub->id}}" @if($data->lead_sourceid == $sub->id) selected @endif >{{$sub->name}}</option>
						@endforeach
					</select>
				</div>
				@else
				<?php $sources = App\LeadSource::where('branch_id',Auth::user()->id)->get(); ?>
				<div class="form-group col-lg-2">
					<label>Source </label>
					<select name="lead_sourceid" class="form-control select2" disabled>
						<option value="">Select</option>
						@foreach($sources as $sub)
						<option value="{{$sub->id}}" @if($data->lead_sourceid == $sub->id) selected @endif >{{$sub->name}}</option>
						@endforeach
					</select>
				</div>
				@endif
				@else
				<?php $sources = App\LeadSource::where('branch_id',Auth::user()->branch_id)->get(); ?>
				<div class="form-group col-lg-2">
					<label>Source </label>
					<select name="lead_sourceid" class="form-control select2" disabled>
						<option value="">Select</option>
						@foreach($sources as $sub)
						<option value="{{$sub->id}}" @if($data->lead_sourceid == $sub->id) selected @endif >{{$sub->name}}</option>
						@endforeach
					</select>
				</div>
				@endif
				<div class="form-group col-sm-2">
					<label>Name</label>
					<input type="text" name="name" placeholder="Enter Name" class="form-control" value="{{$data->name}}" readonly>
				</div>
				<div class="form-group col-sm-2">
					<label>Address</label>
					<input type="text" name="address" placeholder="Enter Address" class="form-control" value="{{$data->address}}" readonly>
				</div>
				<div class="form-group col-sm-2">
					<label>Phone No.</label>
					<input type="number" name="phoneno" placeholder="Enter Phone Number" class="form-control" value="{{$data->phoneno}}" readonly>
				</div>
				<div class="form-group col-sm-2">
					<label>Email</label>
					<input type="text" name="email" placeholder="Enter Email" class="form-control" value="{{$data->email}}" readonly>
				</div>
				<div class="form-group col-sm-2">
					<label>Birth Date</label>
					<input type="date" name="birth_date" class="form-control" value="{{$data->birth_date}}" readonly>
				</div>
				<div class="form-group col-sm-2">
					<label>Anniversary Date</label>
					<input type="date" name="anniversary_date" class="form-control" value="{{$data->anniversary_date}}" readonly>
				</div>
			</div>
		</div>
	</div>
</div>
@endsection