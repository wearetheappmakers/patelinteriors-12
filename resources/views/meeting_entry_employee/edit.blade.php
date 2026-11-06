<meta name="csrf-token" content="{{ csrf_token() }}" />

<div class="kt-portlet__foot">
    <div class="row">

            
            @if(Auth::guard('user')->check() && Auth::user()->role == 1)
                <div class="form-group col-sm-3">
                    <label>Branch</label>
                    <select class="form-control select2" id="branch_id" onchange="getemployee(this)" name="branch_id">
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

            {{-- HR (employee guard, role = 9) OR Branch User (user guard, role = 2) --}}
            @if(
                (Auth::guard('employee')->check() && Auth::guard('employee')->user()->role == 9) ||
                (Auth::guard('user')->check() && Auth::user()->role == 2)
            )
                <div class="form-group col-sm-3">
                    <label>Employee</label>
                    <select class="form-control select2" name="employee_id">
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

        {{-- COMMON FIELDS --}}
        <div class="form-group col-sm-2">
            <label>SCH. DATE</label>
            <input type="date" name="sch_date" value="{{ $data->sch_date }}" class="form-control" required>
        </div>

        <div class="form-group col-sm-2">
            <label>SCH. Time</label>
            <input type="time" name="sch_time" value="{{ $data->sch_time }}" class="form-control" required>
        </div>

        <div class="form-group col-sm-2">
            <label>Client Name</label>
            <input type="text" name="client_name" value="{{ $data->client_name }}" class="form-control" required>
        </div>

        <div class="form-group col-sm-2">
            <label>Contact No</label>
            <input type="text" name="contact_no" value="{{ $data->contact_no }}" class="form-control" required>
        </div>

        <div class="form-group col-sm-2">
            <label>Purpose Of Meeting</label>
            <textarea name="purpose_of_meeting" class="form-control" required>{{ $data->purpose_of_meeting }}</textarea>
        </div>

    </div>
</div>
<script>
    $(document).ready(function () {
        $('.select2').select2({
            width: '100%'
        });

        let branchId   = "{{ $data->branch_id }}";
        let employeeId = "{{ $data->employee_id }}";

 
        if (branchId) {
            $('#branch_id').val(branchId).trigger('change.select2');

            loadEmployees(branchId, employeeId);
        }
    });

    function getemployee(el) {
        let branch_id = $(el).val();
        loadEmployees(branch_id, null);
    }

    function loadEmployees(branch_id, selectedEmployee = null) {

        let $emp = $('#employee_id');


        $emp.val(null).trigger('change');
        $emp.empty().append('<option value="">Select</option>');

        if (!branch_id) return;

        $.ajax({
            type: "POST",
            url: "{{ route('user.meeting_entry_employee.getemployee') }}",
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            data: { branch_id: branch_id },
            success: function (res) {

                $emp.append(res.data1);

                if (selectedEmployee) {
                    $emp.val(selectedEmployee).trigger('change');
                }
            }
        });
    }
</script>

