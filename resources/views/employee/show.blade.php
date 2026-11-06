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
					View Employee
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

	@php
    $employeeGuard = Auth::guard('employee')->check() ? Auth::guard('employee')->user() : null;
    $employeeRole = $employeeGuard->role ?? null;
    $employeeId = $employeeGuard->id ?? null;
    $branchId = $employeeGuard->branch_id ?? null;
	@endphp


		@if(Auth::guard('user')->check() && Auth::user()->role == 1)
		<div class="form-group col-lg-3">
			<label>Branch</label>
			<input type="text" class="form-control" value="{{ 
				Auth::guard('user')->check() && Auth::user()->role == 1 
				? ($branch->firstWhere('id', $data->branch_id)->name ?? '-') 
				: ($branch->firstWhere('id', $branchId)->name ?? '-') 
			}}" readonly>
		</div>
		@endif


		@if(Auth::guard('user')->check() && in_array(Auth::user()->role, [1,2]))
			<div class="form-group col-lg-3">
				<label>General Manager</label>
				<input type="text" class="form-control" value="{{ $generalmanager->firstWhere('id', $data->general_manager)->name ?? '-' }}" readonly>
			</div>
		@endif


		@if(Auth::guard('user')->check() && in_array(Auth::user()->role, [1,2]) || ($employeeGuard && $employeeRole == 6))
			<div class="form-group col-lg-3">
				<label>Manager</label>
				<input type="text" class="form-control" value="{{ $manager->firstWhere('id', $data->manager)->name ?? '-' }}" readonly>
			</div>
		@endif


		@if(Auth::guard('user')->check() && in_array(Auth::user()->role, [1,2]) || ($employeeGuard && in_array($employeeRole, [6,7])))
			<div class="form-group col-lg-3">
				<label>Sales Area Manager</label>
				<input type="text" class="form-control" value="{{ $salesareamanager->firstWhere('id', $data->sales_area_manager)->name ?? '-' }}" readonly>
			</div>
		@endif

     
        <div class="form-group col-lg-3">
            <label>First Name</label>
            <input type="text" class="form-control" value="{{ $data->first_name }}" readonly>
        </div>

        <div class="form-group col-lg-3">
            <label>Last Name</label>
            <input type="text" class="form-control" value="{{ $data->last_name }}" readonly>
        </div>

        <div class="form-group col-lg-3">
            <label>User Name</label>
            <input type="text" class="form-control" value="{{ $data->name }}" readonly>
        </div>

        <div class="form-group col-lg-3">
            <label>Gender</label>
            <input type="text" class="form-control" value="{{ $data->gender == 1 ? 'Male' : 'Female' }}" readonly>
        </div>

        <div class="form-group col-lg-3">
            <label>Email</label>
            <input type="email" class="form-control" value="{{ $data->email }}" readonly>
        </div>

        <div class="form-group col-lg-3">
            <label>Mobile No.</label>
            <input type="text" class="form-control" value="{{ $data->mobile_no }}" readonly>
        </div>

        <div class="form-group col-lg-3">
            <label>Other Mobile No.</label>
            <input type="text" class="form-control" value="{{ $data->other_mobile_no }}" readonly>
        </div>

        <div class="form-group col-lg-3">
            <label>Address</label>
            <textarea class="form-control" readonly>{{ $data->address }}</textarea>
        </div>

   
        <div class="form-group col-lg-3">
            <label>Password</label>
            <input type="text" class="form-control" value="{{ $data->confirm_password }}" readonly>
        </div>

        @if(Auth::guard('user')->check() && Auth::user()->role == 1)
            <div class="form-group col-lg-3">
                <label>Office Start Time</label>
                <input type="time" class="form-control" value="{{ $data->office_start_time }}" readonly>
            </div>

            <div class="form-group col-lg-3">
                <label>Buffer Time (Minutes)</label>
                <input type="text" class="form-control" value="{{ $data->buffer_time }}" readonly>
            </div>
        @endif

        <div class="form-group col-lg-3">
            <label>Role</label>
            <select class="form-control select2" disabled>
                <option value="">Select Role</option>
                @foreach ($roles as $role)
                <option value="{{ $role->id }}" @if($data->role == $role->id) selected @endif> {{ $role->name }}
                </option>
                @endforeach
            </select>
        </div>

        <div class="form-group col-lg-3">
            <label>Birth Date</label>
            <input type="date" class="form-control" value="{{ $data->dob }}" readonly>
        </div>

        <div class="form-group col-lg-3">
            <label>Join Date</label>
            <input type="date" class="form-control" value="{{ $data->join_date }}" readonly>
        </div>

 
        <div class="form-group col-lg-3">
            <label>Image</label>
            <br>
            @if($data->image)
                <img src="{{ asset('employee/'.$data->image) }}" height="70" width="70">
            @endif
        </div>

    </div>
</div>

</div>
</div>
@endsection