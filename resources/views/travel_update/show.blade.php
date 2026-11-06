@extends('main')
@section('content')

<div class="kt-container kt-container--fluid kt-grid__item kt-grid__item--fluid">
    <br>

    <div class="kt-portlet kt-portlet--mobile">

        {{-- Header --}}
        <div class="kt-portlet__head">
            <div class="kt-portlet__head-label">
                <span class="kt-portlet__head-icon">
                    <i class="kt-font-brand"></i>
                </span>
                <h3 class="kt-portlet__head-title">View Travel </h3>
            </div>

            <div class="kt-portlet__head-toolbar">
                <div class="kt-portlet__head-actions">
                    <a href="{{ route('user.travel_update.index') }}" class="btn btn-brand btn-elevate btn-icon-sm">
                        <i class="flaticon2-fast-back"></i> Back
                    </a>
                </div>
            </div>
        </div>

        {{-- BODY --}}
        <meta name="csrf-token" content="{{ csrf_token() }}" />

        <div class="kt-portlet__foot">
            <div class="row">

                   {{-- Super Admin Access --}}
                    
		            @if(Auth::guard('user')->check() && Auth::user()->role == 1)

                    <div class="form-group col-sm-3">
                        <label>Branch</label>
                        <select disabled class="form-control select2" id="branch_id"
                                onchange="getemployee(this)" name="branch_id">
                            <option value="">Select</option>

                            @foreach($branch as $datas)
                                <option value="{{ $datas->id }}"
                                    {{ $data->branch_id == $datas->id ? 'selected' : '' }}>
                                    {{ $datas->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group col-sm-3">
                        <label>Employee</label>
                        <select class="form-control select2" id="employee_id" name="employee_id" disabled>
                            <option value="">Select</option>
                            {{-- Employees will be auto-loaded & selected via JS --}}
                        </select>
                    </div>

                @endif


                {{-- EMPLOYEE (HR only, role = 9) --}}
				@if((Auth::guard('employee')->check() && Auth::guard('employee')->user()->role == 9) ||(Auth::guard('user')->check() && Auth::user()->role == 2))
                    <div class="form-group col-sm-3">
                        <label>Employee</label>
                        <select Disabled  class="form-control select2" name="employee_id">
                            <option value="">Select</option>
                            @foreach($employees as $emp)
                                <option value="{{ $emp->id }}"
                                    {{ isset($data) && $data->employee_id == $emp->id ? 'selected' : '' }}>
                                    {{ $emp->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
               @endif

                {{-- Other fields --}}
                <div class="form-group col-sm-2">
                    <label>SCH. DATE</label>
                    <input type="date" disabled name="sch_date" value="{{ $data->sch_date }}" class="form-control">
                </div>

                <div class="form-group col-sm-2">
                    <label>SCH. Time</label>
                    <input type="time" disabled name="sch_time" value="{{ $data->sch_time }}" class="form-control">
                </div>

                <div class="form-group col-sm-2">
                    <label>Name</label>
                    <input type="text" disabled name="name" value="{{ $data->name }}" class="form-control">
                </div>

                <div class="form-group col-sm-2">
                    <label>Contact No</label>
                    <input type="text" disabled name="contact_no" value="{{ $data->contact_no }}" class="form-control">
                </div>

                <div class="form-group col-sm-2">
                    <label>Purpose Of Travel</label>
                    <textarea disabled name="purpose_of_travel" class="form-control">{{ $data->purpose_of_travel }}</textarea>
                </div>

            </div>
        </div>
    </div>
</div>

{{-- JS --}}
<script type="text/javascript">

    function getemployee($this){
        let branch_id = $($this).val();

        $("#employee_id").html('<option value="">Select</option>');

        if(branch_id){
            $.ajax({
                type: "POST",
                headers: {
                    "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content")
                },
                url: "{{ route('user.travel_update.getemployee') }}",
                data: { branch_id: branch_id },
                success: function(data){

                    $("#employee_id").html(data.data1);

                    // Preselect saved employee
                    $("#employee_id").val("{{ $data->employee_id }}").trigger("change");
                }
            });
        }
    }

    $(document).ready(function(){

        $(".select2").select2();

        let selectedBranch = "{{ $data->branch_id }}";

        if(selectedBranch){
            $("#branch_id").val(selectedBranch).trigger("change");
        }
    });

</script>

@endsection
