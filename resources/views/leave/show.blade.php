@extends('main')

@section('content')



<div class="kt-content  kt-grid__item kt-grid__item--fluid kt-grid kt-grid--hor" id="kt_content">

    <br>

    <div class="kt-container  kt-container--fluid  kt-grid__item kt-grid__item--fluid">        

        <link href="{{ asset('assets/plugins/custom/datatables/datatables.bundle.css')}}" rel="stylesheet" type="text/css" />

        <div class="kt-portlet kt-portlet--mobile">

            <div class="kt-portlet__head kt-portlet__head--lg">

                <div class="kt-portlet__head-label">

                    <h3 class="kt-portlet__head-title">

                        View Leave

                    </h3>

                </div>

                <div class="kt-portlet__head-toolbar">

                    <div class="kt-portlet__head-wrapper">

                        <div class="kt-portlet__head-actions">

                            <a href="{{ route('user.leave.index') }}" class="btn btn-brand btn-elevate btn-icon-sm">

                                <i class="flaticon2-fast-back"></i>

                                Back

                            </a>

                        </div>

                    </div>

                </div>

            </div>
            
        {{-- BODY --}}
        <meta name="csrf-token" content="{{ csrf_token() }}" />

            <div class="kt-portlet__body">

                <div id="kt_table_1_wrapper" class="dataTables_wrapper dt-bootstrap4">

                    <div class="row">
                             
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

                      

                        <div class="form-group col-sm-3">

                            <label>From Date</label>

                            <input type="date" class="form-control" name="from_date" id="from_date" value="{{$data->from_date}}" readonly>

                        </div>



                        <div class="form-group col-sm-3">

                            <label>To Date</label>

                            <input type="date" class="form-control" name="to_date" id="to_date" value="{{$data->to_date}}" readonly>

                        </div>

                        <div class="form-group col-sm-3">

                            <label>Department</label>

                            <input type="text" class="form-control" name="department" id="department" placeholder="Enter department" value="{{$data->department}}" readonly>

                        </div>



                        <div class="form-group col-sm-3">

                            <label>Reason of leave </label>

                            <input type="text" class="form-control" name="reason" id="reason" placeholder="Enter Reason" value="{{$data->reason}}" readonly>

                        </div>

                    </div>

                </div>

            </div>

            

        </div>

    </div>

</div>
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

@stop

@push('scripts')