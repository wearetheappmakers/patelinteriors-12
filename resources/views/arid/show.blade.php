@extends('main')
@section('content')
<div class="kt-container  kt-container--fluid  kt-grid__item kt-grid__item--fluid">
	<br>
	<div class="kt-portlet">
		<div class="kt-portlet__head">
			<div class="kt-portlet__head-label">
				<h3 class="kt-portlet__head-title">
					Show Ar/ID
				</h3>
			</div>
		</div>
		<form class="kt-form" class="addform" id="addform" method="POST">
			@method('put')
			@csrf
			<?php
			$index = route('user.'.$resourcePath.'.index');
			?>
			<meta name="csrf-token" content="{{ csrf_token() }}" />
			<div class="kt-portlet__foot">
				<div class="row">
					@if(Auth::guard('user')->check())
					@if(Auth::user()->role == 1)  
					<div class="form-group col-sm-3">
						<label>Branch</label>
						<input type="text" class="form-control" placeholder="Branch" value="{{ $data->branch->name }}" readonly>
					</div>
					<div class="form-group col-sm-3">
						<label>Employee</label>
						<input type="text" class="form-control" placeholder="Branch" value="{{ $data->employee->name }}" readonly>
					</div>
					@endif
					@endif
					@if(Auth::guard('user')->check())
					@if(Auth::user()->role == 2)
					<div class="form-group col-sm-3">
						<label>Employee</label>
						<input type="text" class="form-control" placeholder="Branch" value="{{ $data->employee->name }}" readonly>
					</div>
					@endif
					@endif
					<div class="form-group col-lg-3">
						<label>Type </label>
						<select class="form-control" name="type" disabled>
							<option value="1" @if($data->type == '1') selected @endif>Ar</option>
							<option value="2" @if($data->type == '2') selected @endif>ID</option>
						</select>
					</div>
					<div class="form-group col-lg-3">
						<label>Name </label>
						<input type="text" class="form-control" placeholder="Name" name="name" value="{{ $data->name }}" readonly>
					</div>
					<div class="form-group col-lg-3">
						<label>Personal Number </label>
						<input type="number" class="form-control" placeholder="Personal Number" name="number" pattern="\d{10}" minlength="10" maxlength="10" value="{{ $data->number }}" readonly >
					</div>
					<div class="form-group col-lg-3">
						<label>Office Number </label>
						<input type="number" class="form-control" placeholder="Office Number" name="office_no" value="{{ $data->office_no }}" readonly>
					</div>
					<div class="form-group col-lg-3">
						<label>Firm Name </label>
						<input type="text" class="form-control" placeholder="Firm Name" name="firm_name" value="{{ $data->firm_name }}" readonly>
					</div>
					<div class="form-group col-lg-3">
						<label>Firm Address </label>
						<input type="text" class="form-control" placeholder="Firm Address" name="firm_address" value="{{ $data->firm_address }}" readonly>
					</div>
					<div class="form-group col-sm-3">
						<label>Country</label>
						<input type="text" class="form-control" value="{{ $data->countrys->name }}" readonly>
					</div>
					<div class="form-group col-sm-3">
						<label>State</label>
						<input type="text" class="form-control" value="{{ $data->states->name }}" readonly>
					</div>
					<div class="form-group col-sm-3">
						<label>City</label>
						<input type="text" class="form-control" value="{{ $data->citys->name }}" readonly>
					</div>
					<div class="form-group col-lg-3">
						<label>Email </label>
						<input type="text" class="form-control" placeholder="Email" name="email" value="{{ $data->name }}" readonly>
					</div>
					<div class="form-group col-lg-3">
						<label>Birth Date </label>
						<input type="date" class="form-control" value="{{ $data->dob }}" name="dob" readonly>
					</div>
					<div class="form-group col-lg-3">
						<label>Website</label>
						<input type="text" name="website" class="form-control" value="{{ $data->website }}" readonly placeholder="Website">
					</div>
				</div>
			</div>
		</form>
		<div class="kt-portlet__foot">
			<div class="kt-form__actions">
				<center>
					<a href="{{$index}}"><button type="button" class="btn btn-outline-secondary">Cancel</button></a>
				</center>
			</div>
		</div>
	</div>
</div>
@endsection