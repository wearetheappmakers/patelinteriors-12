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
                <select name="branch_id" class="form-control select2" id="branch_id">
                    <option value="">Select Branch</option>
                    @foreach($branch as $sub)
                        <option value="{{ $sub->id }}">{{ $sub->name }}</option>
                    @endforeach
                </select>
            </div>
        @endif


        @if($employeeGuard)
            <input type="hidden" name="branch_id" value="{{ $branchId }}">
            <input type="hidden" name="employee_id" value="{{ $employeeId }}">
        @endif


        @if(Auth::guard('user')->check() && in_array(Auth::user()->role, [1,2]))
            <div class="form-group col-lg-3">
                <label>General Manager</label>
                <select name="general_manager" class="form-control select2" id="general_manager">
                    <option value="">Select General Manager</option>
                    @foreach($generalmanager as $gm)
                        @if(!$employeeGuard || ($employeeRole == 6 && $gm->id == $employeeId))
                            <option value="{{ $gm->id }}" {{ ($employeeRole == 6 && $gm->id == $employeeId) ? 'selected' : '' }}>
                                {{ $gm->name }}
                            </option>
                        @elseif(!$employeeRole)
                            <option value="{{ $gm->id }}">{{ $gm->name }}</option>
                        @endif
                    @endforeach
                </select>
            </div>
        @endif

 
        @if(Auth::guard('user')->check() && in_array(Auth::user()->role, [1,2]) || ($employeeGuard && $employeeRole == 6))
            <div class="form-group col-lg-3">
                <label>Manager</label>
                <select name="manager" class="form-control select2" id="manager">
                    <option value="">Select Manager</option>
                    @foreach($manager as $m)
                        @if(!$employeeGuard || ($branchId && $m->branch_id == $branchId))
                            <option value="{{ $m->id }}">{{ $m->name }}</option>
                        @endif
                    @endforeach
                </select>
            </div>
        @endif


        @if(Auth::guard('user')->check() && in_array(Auth::user()->role, [1,2]) || ($employeeGuard && in_array($employeeRole, [6,7])))
            <div class="form-group col-lg-3">
                <label>Sales Area Manager</label>
                <select name="sales_area_manager" class="form-control select2" id="sales_area_manager">
                    <option value="">Select Sales Area Manager</option>
                    @foreach($salesareamanager as $sam)
                        @if(!$employeeGuard || ($branchId && $sam->branch_id == $branchId))
                            <option value="{{ $sam->id }}">{{ $sam->name }}</option>
                        @endif
                    @endforeach
                </select>
            </div>
        @endif
        <div class="form-group col-lg-3">
            <label>First Name</label>
            <input type="text" class="form-control" name="first_name" placeholder="First Name" required>
        </div>

        <div class="form-group col-lg-3">
            <label>Last Name</label>
            <input type="text" class="form-control" name="last_name" placeholder="Last Name" required>
        </div>

        <div class="form-group col-lg-3">
            <label>User Name</label>
            <input type="text" class="form-control" name="name" placeholder="User Name" required>
        </div>

        <div class="form-group col-lg-3">
            <label>Gender</label>
            <div class="kt-radio-inline">
                <label class="kt-radio">
                    <input type="radio" name="gender" value="1"> Male
                    <span></span>
                </label>
                <label class="kt-radio">
                    <input type="radio" name="gender" value="2"> Female
                    <span></span>
                </label>
            </div>
        </div>

        <div class="form-group col-lg-3">
            <label>Email</label>
            <input type="email" class="form-control" name="email" placeholder="Email">
        </div>

        <div class="form-group col-lg-3">
            <label>Mobile No.</label>
            <input type="number" class="form-control" name="mobile_no" placeholder="Mobile No.">
        </div>

        <div class="form-group col-lg-3">
            <label>Other Mobile No.</label>
            <input type="number" class="form-control" name="other_mobile_no" placeholder="Other Mobile No.">
        </div>

        <div class="form-group col-lg-3">
            <label>Address</label>
            <textarea class="form-control" name="address" placeholder="Address"></textarea>
        </div>

        <div class="form-group col-lg-3">
            <label>Password</label>
            <input type="password" class="form-control" name="password" placeholder="Password" required>
        </div>

        <div class="form-group col-lg-3">
            <label>Confirm Password</label>
            <input type="password" class="form-control" name="confirm_password" placeholder="Confirm Password" required>
        </div>

  
        @if(Auth::guard('user')->check() && Auth::user()->role == 1)
            <div class="form-group col-lg-3">
                <label>Office Start Time</label>
                <input type="time" class="form-control" name="office_start_time">
            </div>

            <div class="form-group col-lg-3">
                <label>Buffer Time (Minutes)</label>
                <input type="number" class="form-control" name="buffer_time" placeholder="Enter Buffer Time (Minutes)">
            </div>
        @endif

        <div class="form-group col-lg-3">
            <label>Role</label>
            <select class="form-control select2" name="role" id="role">
                <option value="">Select Role</option>
                @foreach ($roles as $role)
                    <option value="{{ $role->id }}">{{ $role->name }}</option>
                @endforeach
            </select>
        </div>

        <div class="form-group col-lg-3">
            <label>Birth Date</label>
            <input type="date" class="form-control" name="dob" value="{{ date('Y-m-d') }}">
        </div>

        <div class="form-group col-lg-3">
            <label>Join Date</label>
            <input type="date" class="form-control" name="join_date" value="{{ date('Y-m-d') }}">
        </div>

        <div class="form-group col-lg-3">
            <label>Image</label>
            <input type="file" class="form-control" name="image">
        </div>

    </div>
</div>


<script>
@if(Auth::user()->role == 1)
    $(document).on('change', '#branch_id', function () {
        let branch_id = $(this).val();

        $('#general_manager').html('<option>Loading...</option>');
        $('#manager').html('<option>Loading...</option>');
        $('#sales_area_manager').html('<option>Loading...</option>');

        $.ajax({
            url: "{{ url('user/employee/get_employees_by_branch') }}/" + branch_id,
            type: "GET",
            success: function(response) {
                $('#general_manager').empty().append('<option value="">Select General Manager</option>');
                $('#manager').empty().append('<option value="">Select Manager</option>');
                $('#sales_area_manager').empty().append('<option value="">Select Sales Area Manager</option>');

                $.each(response.generalmanager, function(i, gm){
                    $('#general_manager').append('<option value="'+gm.id+'">'+gm.name+'</option>');
                });

                $.each(response.manager, function(i, m){
                    $('#manager').append('<option value="'+m.id+'">'+m.name+'</option>');
                });

                $.each(response.salesareamanager, function(i, sam){
                    $('#sales_area_manager').append('<option value="'+sam.id+'">'+sam.name+'</option>');
                });
            }
        });
    });
@endif
</script>

<script>
$('.select2').select2();
</script>
