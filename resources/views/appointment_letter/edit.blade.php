<meta name="csrf-token" content="{{ csrf_token() }}" />

<div class="kt-portlet__foot">



	<div class="row">
         @if(Auth::guard('user')->check() && Auth::user()->role == 1 )
			 <div class="form-group col-sm-3">
                <label>Branch</label>
                <select class="form-control select2" id="branch_id" name="branch_id" required>
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

			<input type="text" name="employee_name" placeholder="Enter Candidate Name " value="{{$data->employee_name}}" class="form-control" required>

		</div>
        <div class="form-group col-sm-2">

			<label>Address</label>

			<textarea type="text" name="address" placeholder="Enter Address" value="" class="form-control" required>{{$data->address}}</textarea>

		</div>
        <div class="form-group col-sm-2">

                        <label>Hr/Admin Name </label>

                        <input type="text" name="hr_admin" placeholder="Enter Hr/Admin Name " value="{{$data->hr_admin}}" class="form-control" required >

        </div>

        <div class="form-group col-sm-2">

			<label>Offer Letter Date</label>

			<input type="date" name="date" placeholder="Enter Date" value="{{$data->date}}" class="form-control" required>

		</div>
        <div class="form-group col-sm-2">

			<label>Position</label>

			<input type="text" name="position" placeholder="Enter Position" value="{{$data->position}}" class="form-control" required>

		</div>
        <div class="form-group col-sm-2">

                        <label>Joining Date</label>

                        <input type="date" name="joining_date" placeholder="Enter Date" value="{{$data->joining_date}}" class="form-control" required >

        </div>
        <div class="form-group col-sm-2">

			<label>Salary</label>

			<input type="number" name="amount" placeholder="Enter Amount"  value="{{$data->amount}}" class="form-control" required>

		</div>
        <!-- <div class="form-group col-sm-2">
            <label>Signature Upload</label>
            <input type="file" name="signature_upload" class="form-control" style="width: 600px;">

            <div class="mb-2">
                <img src="{{ asset('uploads/signature/' . ($data->signature_upload ?? 'default.png')) }}"
                    alt="Signature"
                    style="width:120px; border:1px solid #ddd; padding:5px; border-radius:5px;">
            </div>
        </div> -->

         <!-- <table class="table table-bordered">

    <tr>
        <th>Particulars</th>
        <th class="text-center">Monthly</th>
        <th class="text-center">Annually</th>
    </tr>

    <tr>
        <th>Basic</th>
        <td><input type="text" name="basic_month" value="{{$data->basic_month}}" class="form-control  ded-calc"></td>
        <td><input type="text" name="basic_annual" value="{{$data->basic_annual}}" class="form-control  ded-calc"></td>
    </tr>

    <tr>
        <th>HRA</th>
        <td><input type="text" name="hra_month" value="{{$data->hra_month}}" class="form-control ded-calc"></td>
        <td><input type="text" name="hra_annual" value="{{$data->hra_annual}}"   class="form-control ded-calc"></td>
    </tr>

    <tr>
        <th>Conveyance allowance</th>
        <td><input type="text" name="convenyance_allowance_month" value="{{$data->convenyance_allowance_month}}" class="form-control ded-calc"></td>
        <td><input type="text" name="convenyance_allowance_annual" value="{{$data->convenyance_allowance_annual}}" class="form-control ded-calc"></td>
    </tr>

    <tr>
        <th>Special allowance</th>
        <td><input type="text" name="special_month" value="{{$data->special_month}}" class="form-control ded-calc"></td>
        <td><input type="text" name="special_annual" value="{{$data->special_annual}}" class="form-control ded-calc"></td>
    </tr>


    <tr>
        <th colspan="3"><b>Gross Salary</b></th>
    </tr>

    
    <tr>
        <th>Deductions</th>
        <td><input type="text" name="deductions_month" value="{{$data->deductions_month}}" class="form-control ded-calc" ></td>
        <td><input type="text" name="deductions_annual" value="{{$data->deductions_annual}}" class="form-control ded-calc"></td>
    </tr>

    <tr>
        <th>PT</th>
        <td><input type="text" name="pt_month"  value="{{$data->pt_month}}" class="form-control ded-calc"></td>
        <td><input type="text" name="pt_annual" value="{{$data->pt_annual}}"  class="form-control ded-calc"></td>
    </tr>

    <tr>
        <th>Deposit amount (Refundable)</th>
        <td><input type="text" name="deposit_amt_month" value="{{$data->deposit_amt_month}}"  class="form-control ded-calc"></td>
        <td><input type="text" name="deposit_amt_annual" value="{{$data->deposit_amt_annual}}"  class="form-control ded-calc"></td>
    </tr>

    
    <tr>
        <th>Total Deduction</th>
        <td><input type="text" name="total_deductions_month"  value="{{$data->total_deductions_month}}" class="form-control"></td>
        <td><input type="text" name="total_deductions_annual"  value="{{$data->total_deductions_month}}" class="form-control"></td>
    </tr>


    <tr>
        <th><b>Net Salary</b></th>
        <td><input type="text" name="net_salary_month" value="{{$data->net_salary_month}}" class="form-control"></td>
        <td><input type="text" name="net_salary_annual" value="{{$data->net_salary_annual}}" class="form-control"></td>
    </tr>

</table> -->





	</div>

</div>
<script>
function calcTotals() {

    // ================= MONTHLY =================
    let basic_m  = parseFloat($("input[name='basic_month']").val()) || 0;
    let hra_m    = parseFloat($("input[name='hra_month']").val()) || 0;
    let conv_m   = parseFloat($("input[name='convenyance_allowance_month']").val()) || 0;
    let spec_m   = parseFloat($("input[name='special_month']").val()) || 0;
    let ded_m    = parseFloat($("input[name='deductions_month']").val()) || 0;
    let pt_m     = parseFloat($("input[name='pt_month']").val()) || 0;
    let depo_m   = parseFloat($("input[name='deposit_amt_month']").val()) || 0;

    let total_month = basic_m + hra_m + conv_m + spec_m + ded_m + pt_m + depo_m;
    $("input[name='total_deductions_month']").val(total_month.toFixed(2));

    // ================= ANNUAL ==================
    let basic_a  = parseFloat($("input[name='basic_annual']").val()) || 0;
    let hra_a    = parseFloat($("input[name='hra_annual']").val()) || 0;
    let conv_a   = parseFloat($("input[name='convenyance_allowance_annual']").val()) || 0;
    let spec_a   = parseFloat($("input[name='special_annual']").val()) || 0;
    let ded_a    = parseFloat($("input[name='deductions_annual']").val()) || 0;
    let pt_a     = parseFloat($("input[name='pt_annual']").val()) || 0;
    let depo_a   = parseFloat($("input[name='deposit_amt_annual']").val()) || 0;

    let total_annual = basic_a + hra_a + conv_a + spec_a + ded_a + pt_a + depo_a;
    $("input[name='total_deductions_annual']").val(total_annual.toFixed(2));
}

// Trigger calculation when typing
$(".ded-calc").on("keyup change", function () {
    calcTotals();
});

// Trigger calculation on page load
$(document).ready(function () {
    calcTotals();
});
</script>
