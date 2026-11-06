<meta name="csrf-token" content="{{ csrf_token() }}" />

<div class="kt-portlet__foot">
    <div class="row">

        {{-- SUPER ADMIN --}}
        @if(Auth::guard('user')->check() && Auth::user()->role == 1)

            <div class="form-group col-sm-3">
                <label>Branch</label>
                <select class="form-control select2"
                        id="branch_id"
                        onchange="getemployee(this)"
                        name="branch_id">
                    <option value="">Select</option>
                    @foreach($branch as $datas)
                        <option value="{{ $datas->id }}">{{ $datas->name }}</option>
                    @endforeach
                </select>
            </div>

            <div class="form-group col-sm-3">
                <label>Employee</label>
                <select class="form-control select2" id="employee_id" name="employee_id">
                    <option value="">Select</option>
                </select>
            </div>

        @endif


        {{-- HR (employee role = 9) OR Branch User (role = 2) --}}
        @if(
            (Auth::guard('employee')->check() && Auth::guard('employee')->user()->role == 9) ||
            (Auth::guard('user')->check() && Auth::user()->role == 2)
        )
            <div class="form-group col-sm-3">
                <label>Employee</label>
                <select class="form-control select2" name="employee_id">
                    <option value="">Select</option>
                    @foreach($employees as $emp)
                        <option value="{{ $emp->id }}">{{ $emp->name }}</option>
                    @endforeach
                </select>
            </div>
        @endif


        <div class="form-group col-sm-3">
            <label>From Date</label>
            <input type="date" class="form-control" name="from_date" value="{{ date('Y-m-d') }}">
        </div>

        <div class="form-group col-sm-3">
            <label>To Date</label>
            <input type="date" class="form-control" name="to_date" value="{{ date('Y-m-d') }}">
        </div>

        <div class="form-group col-sm-3">
            <label>Department</label>
            <input type="text" class="form-control" name="department" placeholder="Enter Department">
        </div>

        <div class="form-group col-sm-3">
            <label>Reason Of Leave</label>
            <input type="text" class="form-control" name="reason" placeholder="Enter Reason">
        </div>

    </div>
</div>
<script>
$(document).ready(function () {

    $('.select2').select2({
        placeholder: "Select",
        allowClear: true,
        width: '100%'
    });

});

function getemployee(el) {
    let branch_id = $(el).val();
    loadEmployees(branch_id);
}

function loadEmployees(branch_id) {

    let $emp = $('#employee_id');

    $emp.val(null).trigger('change');
    $emp.html('<option value="">Select</option>');

    if (!branch_id) return;

    $.ajax({
        type: "POST",
        url: "{{ route('user.employee.name') }}",
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        data: { branch_id: branch_id },
        success: function (res) {
            $emp.append(res.data1);
            $emp.val('').trigger('change.select2');
        }
    });
}
</script>
