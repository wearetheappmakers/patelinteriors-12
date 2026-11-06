@php
$permission = App\Group::where('id',Auth::user()->group_id)->first();
$per_exploded = explode(',',$permission->user);
@endphp
@if($per_exploded[2] == 1)
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
					View Branch
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
		<!--  <div class="form-group col-lg-3">
            <label>Group  </label>
            <select name="group_id" class="form-control" readonly>
                <option value="">Select Group</option>
                @foreach($group as $sub)
                <option value="{{$sub->id}}" @if($sub->id == $data->group_id) selected @endif>{{$sub->name}}</option>
                @endforeach
            </select>
        </div> -->
        <div class="form-group col-lg-3">
        	<label>First Name </label>
        	<input type="text" class="form-control" placeholder="First Name" value="{{$data->first_name}}" name="first_name" readonly>
        </div>
        <div class="form-group col-lg-3">
        	<label>Last Name </label>
        	<input type="text" class="form-control" placeholder="Last Name" value="{{$data->last_name}}" name="last_name"  readonly>
        </div>
        <div class="form-group col-lg-3">
        	<label>Name </label>
        	<input type="text" class="form-control" placeholder="Name" name="name" readonly value="{{$data->name}}">
        </div>
        <div class="form-group col-lg-3">
        	<label>E-Mail </label>
        	<input type="email" class="form-control" placeholder="Email Id" name="email" readonly value="{{$data->email}}">
        </div>
        <div class="form-group col-lg-3">
        	<label>Image </label>
        	<!-- <input type="file" class="form-control" placeholder="" value="{{$data->image}}" name="image" > -->
            <br>
        	@if($data->image)
        	<img src="{{ asset('/user/'.$data->image) }}" height="70px" width="70px">
        	@endif
        </div>
        <div class="form-group col-lg-3">
        	<label>Contact </label>
        	<input type="tel" class="form-control" placeholder="Contact" name="number" readonly value="{{$data->number}}">
        </div>
        <div class="form-group col-lg-3">
        	<label>Prefix </label>
        	<input type="text" class="form-control" value="{{$data->prefix}}" placeholder="Prefix" name="prefix" readonly>
        </div>
        <div class="form-group col-lg-3">
        	<label>Password </label>
        	<input type="text" class="form-control" placeholder="Password" readonly value="{{$data->show_password}}">
        </div>
        
    </div>
</div>
</div>
</div>
@endsection
@endif
