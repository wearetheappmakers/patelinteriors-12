@extends('main')

@section('content')
<div class="kt-container  kt-container--fluid  kt-grid__item kt-grid__item--fluid">
    <br>
    <div class="kt-portlet kt-portlet--mobile">
        <div class="kt-portlet__head kt-portlet__head--lg">
            <div class="kt-portlet__head-label">
                <h3 class="kt-portlet__head-title">
                    Attendance Details
                </h3>
            </div>
        </div>
        <div class="kt-portlet__body">
            <div class="row" style="padding: 20px !important;">
                @if(Auth::guard('user')->check())
                @if(Auth::user()->role == 1)  
                <div class="form-group col-lg-3">
                    <label>Branch  </label>
                    <select name="branch_id" class="form-control " onchange="getemployee(this)" disabled >
                        <option value="">Select Branch</option>
                        @foreach($branch as $sub)
                        <option value="{{$sub->id}}" @if($sub->id == $attendance->branch_id) selected @endif>{{$sub->name}}</option>
                        @endforeach
                    </select>
                </div>
                <?php
                $employee = \App\Employee::where('branch_id',$attendance->branch_id)->get();
                ?>                     
                <div class="form-group col-lg-3">
                    <label>Employees  </label>
                    <select name="employee_id" id="employee_id" class="form-control select2 employee_id" disabled >
                        <option value="">Select</option>
                        @foreach($employee as $sub)
                        <option value="{{$sub->id}}" @if($sub->id == $attendance->employee_id) selected @endif>{{$sub->name}}</option>
                        @endforeach
                    </select>
                </div>
                @endif
                @endif
                <div class="form-group col-sm-3">
                    <label>Latitude</label>
                    <input type="text" class="form-control" value="{{ $attendance->latitude }}" readonly>
                </div>
                <div class="form-group col-sm-3">
                    <label>Longitude</label>
                    <input type="text" class="form-control" value="{{ $attendance->longitude }}" readonly>
                </div>

                <div class="col-lg-3">
                    <label>Captured Photo</label><br>
                    @if($attendance->photo)
                    <a href="{{ asset($attendance->photo) }}" target="_blank">
                        <img src="{{ asset($attendance->photo) }}" width="200" height="200" style="border: 1px solid #ddd; border-radius: 5px;">
                    </a>
                    @else
                    <span style="color: black !important;"><h5>No photo available.</h5></span>
                    @endif
                </div>

                <div class="form-group col-lg-2">
                    <label>Date & Time</label>
                    <input type="text" class="form-control" value="{{ date('d-m-Y', strtotime($attendance->date)) }} {{ $attendance->created_at->format('h:i A') }}" readonly><br>
                    <label>Attendance Status</label>
                    <input type="text"
                    class="form-control"
                    value="{{ ($attendance->attendance_status == 1) ? 'On Time' : (($attendance->attendance_status == 2) ? 'Late' : '-') }}"
                    style="
                    color:
                    {{ $attendance->attendance_status == 1 ? '#2675d1' : ($attendance->attendance_status == 2 ? 'red' : '#000') }};
                    font-weight:bold;
                    "
                    readonly>
                </div>
            </div>
            <div class="kt-portlet__foot">
                <div class="kt-form__actions">
                    <center>
                        <a href="{{ route('user.attendance.index') }}" class="btn btn-secondary">Back</a>
                    </center>
                </div>
            </div>
        </div>
    </div>
</div>
@stop
