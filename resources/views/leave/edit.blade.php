<meta name="csrf-token" content="{{ csrf_token() }}" />

<div class="kt-portlet__foot">

	<div class="row">
		@if(Auth::guard('user')->check() && Auth::user()->role == 1)

            <div class="form-group col-sm-3">
                <label>Branch</label>
                <select class="form-control select2" id="branch_id"
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
                <select class="form-control select2" id="employee_id" name="employee_id">
                   
                    {{-- Employees will be auto-loaded & selected via JS --}}
                </select>
            </div>

        @endif


        {{-- HR (employee guard, role = 9) --}}
      
				@if((Auth::guard('employee')->check() && Auth::guard('employee')->user()->role == 9) ||(Auth::guard('user')->check() && Auth::user()->role == 2)
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



		<div class="form-group col-sm-3">

			<label>From Date</label>

			<input type="date" class="form-control" name="from_date" id="from_date" value="{{$data->from_date}}">

		</div>



		<div class="form-group col-sm-3">

			<label>To Date</label>

			<input type="date" class="form-control" name="to_date" id="to_date" value="{{$data->to_date}}">

		</div>


		<div class="form-group col-sm-3">

			<label>Department</label>

			<input type="text" class="form-control" name="department" id="department" placeholder="Enter Department" value="{{$data->department}}">

		</div>



		<div class="form-group col-sm-3">

			<label>Reason</label>

			<input type="text" class="form-control" name="reason" id="reason" placeholder="Enter Reason" value="{{$data->reason}}">

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
            url: "{{ route('user.employee.name') }}",
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

