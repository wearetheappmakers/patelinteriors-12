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
                <h3 class="kt-portlet__head-title">View Appointment Letter </h3>
            </div>

            <div class="kt-portlet__head-toolbar">
                <div class="kt-portlet__head-actions">
                    <a href="{{ route('user.appointment_letter.index') }}" class="btn btn-brand btn-elevate btn-icon-sm">
                        <i class="flaticon2-fast-back"></i> Back
                    </a>
                </div>
            </div>
        </div>
            <meta name="csrf-token" content="{{ csrf_token() }}" />

            <div class="kt-portlet__foot">



                <div class="row">
                     @if(Auth::guard('user')->check() && Auth::user()->role == 1 || Auth::user()->role == 9)
                        <div class="form-group col-sm-3">
                            <label>Branch</label>
                            <select class="form-control select2" id="branch_id" name="branch_id" Disabled>
                                <option value="">Select</option>

                                @foreach($branch as $datas)
                                    <option value="{{ $datas->id }}"
                                        {{ $data->branch_id == $datas->id ? 'selected' : '' }}>
                                        {{ $datas->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        
                        @endif

                    <div class="form-group col-sm-2">

                        <label>Candidate Name </label>

                        <input type="text" name="employee_name" placeholder="Enter Candidate Name " value="{{$data->employee_name}}" class="form-control" required disabled>

                    </div>
                    <div class="form-group col-sm-2">

                        <label>Address</label>

                        <textarea type="text" name="address" placeholder="Enter Address" value="" class="form-control" required disabled>{{$data->address}}</textarea>

                    </div>
                    <div class="form-group col-sm-2">

                        <label>Hr/Admin Name </label>

                        <input type="text" name="hr_admin" placeholder="Enter Hr/Admin Name " value="{{$data->hr_admin}}" class="form-control" required disabled>

                    </div>

                    <div class="form-group col-sm-2">

                        <label>Offer Letter Date</label>

                        <input type="date" name="date" placeholder="Enter Date" value="{{$data->date}}" class="form-control" required disabled>

                    </div>
                    <div class="form-group col-sm-2">

                        <label>Position</label>

                        <input type="text" name="position" placeholder="Enter Position" value="{{$data->position}}" class="form-control" required disabled>

                    </div>
                    <div class="form-group col-sm-2">

                        <label>Joining Date</label>

                        <input type="date" name="date" placeholder="Enter Date" value="{{$data->joining_date}}" class="form-control" required disabled>

                    </div>
                    <div class="form-group col-sm-2">

                        <label>Salary</label>

                        <input type="number" name="amount" placeholder="Enter Amount"  value="{{$data->amount}}" class="form-control" required disabled>

                    </div>

                <!-- <table class="table table-bordered">

                <tr>
                    <th>Particulars</th>
                    <th class="text-center">Monthly</th>
                    <th class="text-center">Annually</th>
                </tr>

                
                <tr>
                    <th>Basic</th>
                    <td><input type="text" name="basic_month" value="{{$data->basic_month}}" class="form-control  ded-calc" disabled></td>
                    <td><input type="text" name="basic_annual" value="{{$data->basic_annual}}" class="form-control  ded-calc" disabled></td>
                </tr>

                <tr>
                    <th>HRA</th>
                    <td><input type="text" name="hra_month" value="{{$data->hra_month}}" class="form-control ded-calc" disabled></td>
                    <td><input type="text" name="hra_annual" value="{{$data->hra_annual}}"   class="form-control ded-calc" disabled></td>
                </tr>

                <tr>
                    <th>Conveyance allowance</th>
                    <td><input type="text" name="convenyance_allowance_month" value="{{$data->convenyance_allowance_month}}" class="form-control ded-calc" disabled></td>
                    <td><input type="text" name="convenyance_allowance_annual" value="{{$data->convenyance_allowance_annual}}" class="form-control ded-calc" disabled></td>
                </tr>

                <tr>
                    <th>Special allowance</th>
                    <td><input type="text" name="special_month" value="{{$data->special_month}}" class="form-control ded-calc" disabled></td>
                    <td><input type="text" name="special_annual" value="{{$data->special_annual}}" class="form-control ded-calc" disabled></td>
                </tr>

                
                <tr>
                    <th colspan="3"><b>Gross Salary</b></th>
                </tr>

                
                <tr>
                    <th>Deductions</th>
                    <td><input type="text" name="deductions_month" value="{{$data->deductions_month}}" class="form-control ded-calc" disabled></td>
                    <td><input type="text" name="deductions_annual" value="{{$data->deductions_annual}}" class="form-control ded-calc" disabled></td>
                </tr>

                <tr>
                    <th>PT</th>
                    <td><input type="text" name="pt_month"  value="{{$data->pt_month}}" class="form-control ded-calc" disabled></td>
                    <td><input type="text" name="pt_annual" value="{{$data->pt_annual}}"  class="form-control ded-calc" disabled></td>
                </tr>

                <tr>
                    <th>Deposit amount (Refundable)</th>
                    <td><input type="text" name="deposit_amt_month" value="{{$data->deposit_amt_month}}"  class="form-control ded-calc" disabled></td>
                    <td><input type="text" name="deposit_amt_annual" value="{{$data->deposit_amt_annual}}"  class="form-control ded-calc" disabled></td>
                </tr>

                
                <tr>
                    <th>Total Deduction</th>
                    <td><input type="text" name="total_deductions_month"  value="{{$data->total_deductions_month}}" class="form-control" disabled></td>
                    <td><input type="text" name="total_deductions_annual"  value="{{$data->total_deductions_month}}" class="form-control" disabled></td>
                </tr>

            
                <tr>
                    <th><b>Net Salary</b></th>
                    <td><input type="text" name="net_salary_month" value="{{$data->net_salary_month}}" class="form-control" disabled></td>
                    <td><input type="text" name="net_salary_annual" value="{{$data->net_salary_annual}}" class="form-control" disabled></td>
                </tr>

            </table> -->

            <!-- <div class="col-sm-12 mt-3">
                <label>Signature Upload</label>
                <input type="file" name="signature_upload" class="form-control" style="width: 600px;" disabled>
                <div class="mb-2">
                
                    <img src="{{ asset('uploads/signature/' . ($data->signature_upload ?? 'default.png')) }}"
                        alt="Signature"
                        style="width:120px; border:1px solid #ddd; padding:5px; border-radius:5px;">
                </div> 

            </div>-->
        </div>

    </div>
</div>
</div>
@endsection