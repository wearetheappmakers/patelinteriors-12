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
					View Brand Logo
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
					<select name="branch_id" class="form-control select2" disabled>
						<option value="">Select Branch</option>
						@foreach($branch as $sub)
						<option value="{{$sub->id}}" @if($sub->id == $data->branch_id) selected @endif>{{$sub->name}}</option>
						@endforeach
					</select>
				</div>
				@endif
				@endif
				<div class="form-group col-sm-3">
					<label>Name</label>
					<input type="text" id="name" name="name" placeholder="Enter Name" value="{{$data->name}}" class="form-control name" readonly>
				</div>
				<div class="form-group col-sm-3">
					<label>Brand Logo</label>
					<br>
					@if(isset($data->brand_logo))			
					<a href="{{url('brand_logo/',$data->brand_logo)}}" target="_blank">
						<img src = "{{url('brand_logo/',$data->brand_logo)}}"  height="50" width="50">
					</a>
					@endif
				</div>
			</div>
		</div>
	</div>
</div>
@endsection
