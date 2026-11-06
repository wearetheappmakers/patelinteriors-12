@extends('main')
@section('content')
<div class="kt-container  kt-container--fluid  kt-grid__item kt-grid__item--fluid">
	<br>
	<div class="kt-portlet">
		<div class="kt-portlet__head">
			<div class="kt-portlet__head-label">
				<h3 class="kt-portlet__head-title">
					Show Consent Person
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
					@endif
					@endif
					<div class="form-group col-lg-3">
						<label>Name </label>
						<input type="text" class="form-control" placeholder="Name" value="{{ $data->name }}" name="name" readonly>
					</div>
					<div class="form-group col-lg-3">
						<label>Number </label>
						<input type="number" class="form-control" placeholder="Number" value="{{ $data->number }}" name="number" pattern="\d{10}" minlength="10" maxlength="10" readonly>
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