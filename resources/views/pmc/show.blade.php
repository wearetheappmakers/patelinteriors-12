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
					View PMC
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
					<input type="number" name="number" placeholder="Enter Number"  value="{{$data->number}}" class="form-control" readonly>
				</div>
				<div class="form-group col-sm-2">
					<label>Email</label>
					<input type="email" name="email" placeholder="Enter Email" value="{{$data->email}}" class="form-control" readonly>
				</div>
				<div class="form-group col-sm-2">
					<label>Contact Person</label>
					<input type="text" name="contact_person" placeholder="Enter Contact Person" value="{{$data->contact_person}}" class="form-control" readonly>
				</div>
			</div>
		</div>
	</div>
</div>
@endsection
