  

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
					View Lead Stage
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
					<input  type="text" class="form-control"  value="{{$branchname}}" name="branch_id" readonly>
				<!-- 	<select name="branch_id" class="form-control select2" >
						<option value="">Select Branch</option>
						@foreach($branch as $sub)
						<option value="{{$sub->id}}" @if($sub->id == $data->branch_id) selected @endif>{{$sub->name}}</option>
						@endforeach
					</select> -->
				</div>
				@endif
				@endif
				<div class="form-group col-lg-2">
					<label>Lead Stage Name </label>
					<input type="text" class="form-control" placeholder="Lead Stage Name" value="{{$data->name}}" name="name" readonly>
				</div>
				<div class="form-group col-lg-2">
					<label>Lead Stage Color </label>
					<input type="color" class="form-control" placeholder="Lead Stage Color" value="{{$data->color}}" name="color" readonly>
				</div>

				<div class="form-group  col-lg-2">
					<!-- <label>Is Completed</label> -->
					<div class="kt-checkbox-inline">
						<label class="kt-checkbox">
							<input type="checkbox" name="is_completed" value="1" disabled @if($data->is_completed == 1) checked @endif> Is Completed
							<span></span>
						</label>
					</div>
				</div>
				<div class="form-group  col-lg-2">
					<!-- <label>Is Completed</label> -->
					<div class="kt-checkbox-inline">
						<label class="kt-checkbox">
							<input type="checkbox" name="is_canceled" value="1" disabled @if($data->is_canceled == 1) checked @endif> Is Canceled
							<span></span>
						</label>

					</div>

				</div>

			</div>
		</div>
	</div>
</div>
@endsection


