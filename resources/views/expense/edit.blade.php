@php
    $role = auth()->user()->role; // 2 = employee, 9 = HR
@endphp

<meta name="csrf-token" content="{{ csrf_token() }}" />
<div class="kt-portlet__foot">
    <div class="row">
        <div class="form-group col-sm-12" style="overflow: auto !important;">
            <table class="table table-bordered" cellspacing="0" border="0">
                <thead>
                    <tr>
                        <th width="10%"></th>
                        {{-- Branch & Employee header --}}
                        @if(Auth::guard('user')->check() && Auth::user()->role == 1)
                            <th>Branch</th>
                            <th>Employee</th>
                        @elseif(
                            (Auth::guard('employee')->check() && Auth::guard('employee')->user()->role == 9) ||
                            (Auth::guard('user')->check() && Auth::user()->role == 2)
                        )
                            <th colspan="2">Employee</th>
                        @endif
                        <th>Date</th>
                        <th>Amount</th>
                        <th>Expense Name</th>
                        <th>Bill Upload</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody id="expense_wrapper">

                    {{-- Existing Rows --}}
                    @foreach($data->children as $index => $child)
                        <tr class="master_clone existing_row" data-id="{{ $index }}">
                            <input type="hidden" class="child_real_id" value="{{ $child->id }}">
                            <td>
                                <div style="display:inline-flex !important;">
                                    <button type="button" class="btn btn-sm btn-clean btn-icon remove" {{ $role == 9 ? 'disabled' : '' }}>
                                        <i class="fa fa-minus" style="color:red; font-size:0.8rem !important"></i>
                                    </button>
                                </div>
                            </td>

                            {{-- Branch & Employee fields --}}
                            @if(Auth::guard('user')->check() && Auth::user()->role == 1)
                                <td>
                                    <select class="form-control select2 branch_id" onchange="getemployee(this)" name="branch_id[]">
                                        <option value="">Select</option>
                                        @foreach($branch as $datas)
                                            <option value="{{ $datas->id }}" {{ $child->branch_id == $datas->id ? 'selected' : '' }}>
                                                {{ $datas->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </td>
                                <td>
                                    <select class="form-control select2 employee_id" name="employee_id[]" data-selected="{{ $child->employee_id }}">
                                        <option value="">Select</option>
                                        @foreach($employees as $emp)
                                            <option value="{{ $emp->id }}">{{ $emp->name }}</option>
                                        @endforeach
                                    </select>
                                </td>
                            @elseif(
                                (Auth::guard('employee')->check() && Auth::guard('employee')->user()->role == 9) ||
                                (Auth::guard('user')->check() && Auth::user()->role == 2)
                            )
                                <td colspan="2">
                                    <select class="form-control select2 employee_id" name="employee_id[]">
                                        <option value="">Select</option>
                                        @foreach($employees as $emp)
                                            <option value="{{ $emp->id }}" {{ $child->employee_id == $emp->id ? 'selected' : '' }}>
                                                {{ $emp->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </td>
                            @endif

                            <td><input type="date" name="date[]" value="{{ $child->date }}" class="form-control" {{ $role == 9 ? 'disabled' : '' }}></td>
                            <td><input type="number" name="amount[]" value="{{ $child->amount }}" class="form-control" {{ $role == 9 ? 'disabled' : '' }}></td>
                            <td><input type="text" name="name[]" value="{{ $child->name }}" class="form-control" {{ $role == 9 ? 'disabled' : '' }}></td>
                            <td>
                                <input type="file" name="bill_upload[]" class="form-control" {{ $role == 9 ? 'disabled' : '' }}>
                                <input type="hidden" name="bill_upload_old[]" value="{{ $child->bill_upload ?? '' }}">
                                @if($child->bill_upload)
                                    <small><a href="{{ asset('uploads/expenses/'.$child->bill_upload) }}" target="_blank">View File</a></small>
                                @endif
                            </td>
                            <td>
                                <select name="status_select[]" class="form-control" {{ $role != 9 ? 'disabled' : '' }}>
                                    <option value="0" {{ $child->status=='0' ? 'selected':'' }}>PENDING</option>
                                    <option value="1" {{ $child->status=='1' ? 'selected':'' }}>HALF PAYMENT</option>
                                    <option value="2" {{ $child->status=='2' ? 'selected':'' }}>FULL PAYMENT</option>
                                </select>
                                <input type="hidden" name="status[]" class="status-hidden" value="{{ $child->status }}">
                            </td>
                        </tr>
                    @endforeach

                </tbody>
            </table>
        </div>
    </div>
</div>

{{-- HIDDEN CLONE TEMPLATE --}}
<table class="d-none">
    <tbody id="expense_clone_template">
        <tr class="master_clone new_row">
            <td>
                <div style="display:inline-flex !important;">
                    <button type="button" class="btn btn-sm btn-clean btn-icon add_more" {{ $role == 9 ? 'disabled' : '' }}>
                        <i class="fa fa-plus" style="font-size:0.8rem !important"></i>
                    </button>&nbsp;&nbsp;&nbsp;
                    <button type="button" class="btn btn-sm btn-clean btn-icon remove" {{ $role == 9 ? 'disabled' : '' }}>
                        <i class="fa fa-minus" style="font-size:0.8rem !important"></i>
                    </button>
                </div>
            </td>

            {{-- Branch & Employee fields --}}
            @if(Auth::guard('user')->check() && Auth::user()->role == 1)
                <td>
                    <select class="form-control select2 branch_id" onchange="getemployee(this)" name="branch_id[]">
                        <option value="">Select</option>
                        @foreach($branch as $datas)
                            <option value="{{ $datas->id }}">{{ $datas->name }}</option>
                        @endforeach
                    </select>
                </td>
                <td>
                    <select class="form-control select2 employee_id" name="employee_id[]">
                        <option value="">Select</option>
                    </select>
                </td>
            @elseif(
                (Auth::guard('employee')->check() && Auth::guard('employee')->user()->role == 9) ||
                (Auth::guard('user')->check() && Auth::user()->role == 2)
            )
                <td colspan="2">
                    <select class="form-control select2 employee_id" name="employee_id[]">
                        <option value="">Select</option>
                        @foreach($employees as $emp)
                            <option value="{{ $emp->id }}">{{ $emp->name }}</option>
                        @endforeach
                    </select>
                </td>
            @endif

            <td><input type="date" name="date[]" class="form-control" {{ $role == 9 ? 'disabled' : '' }}></td>
            <td><input type="number" name="amount[]" class="form-control" {{ $role == 9 ? 'disabled' : '' }}></td>
            <td><input type="text" name="name[]" class="form-control" {{ $role == 9 ? 'disabled' : '' }}></td>
            <td>
                <input type="file" name="bill_upload[]" class="form-control" {{ $role == 9 ? 'disabled' : '' }}>
                <input type="hidden" name="bill_upload_old[]" value="">
            </td>
            <td>
                <select name="status_select[]" class="form-control" {{ $role != 9 ? 'disabled' : '' }}>
                    <option value="0">PENDING</option>
                    <option value="1">HALF PAYMENT</option>
                    <option value="2">FULL PAYMENT</option>
                </select>
                <input type="hidden" name="status[]" class="status-hidden" value="0">
            </td>
        </tr>
    </tbody>
</table>

<script>
function getemployee(element){
    var row = $(element).closest('tr');
    var branch_id = $(element).val();
    var employeeSelect = row.find('.employee_id');

    // Clear old options
    employeeSelect.empty().append('<option value="">Select</option>');

    if(branch_id) {
        showLoader();
        $.ajax({
            type: "POST",
            headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
            url: "{{ route('user.expense.getemployee') }}",
            data: { branch_id: branch_id },
            success: function(data){
                // Directly insert HTML returned by backend
                employeeSelect.html(data.data1);

                // Refresh Select2 to show new options
                employeeSelect.val('').trigger('change.select2');

                hideLoader();
            }
        });
    }
}



function initSelect2(element){
    element.find('.select2').select2({ width: '100%' });
}

$(document).ready(function(){

    // Initialize existing rows
    initSelect2($('#expense_wrapper'));

    // Prepend a new row if none exists
    if($('#expense_wrapper .new_row').length === 0){
        let newRow = $($('#expense_clone_template').html());
        $('#expense_wrapper').prepend(newRow);
        initSelect2(newRow);
    }

    // Add new row
    $(document).on('click', '.add_more', function(){
        let newRow = $($('#expense_clone_template').html());
        $('#expense_wrapper').prepend(newRow);
        initSelect2(newRow);
    });

    // Remove row
    $(document).on('click', '.remove', function(){
        let row = $(this).closest('tr');
        let childId = row.find('.child_real_id').val();

        if(!childId){
            row.remove();
            if($('#expense_wrapper .new_row').length === 0){
                let newRow = $($('#expense_clone_template').html());
                $('#expense_wrapper').prepend(newRow);
                initSelect2(newRow);
            }
            return;
        }

        if(confirm("Are you sure you want to delete this item?")){
            $.ajax({
                url: "/testerp/user/expense_child/delete/" + childId,
                method: "POST",
                data: { _method: "DELETE" },
                headers: { "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr('content') },
                success: function(res){
                    alert(res.message);
                    row.remove();
                    if($('#expense_wrapper .new_row').length === 0){
                        let newRow = $($('#expense_clone_template').html());
                        $('#expense_wrapper').prepend(newRow);
                        initSelect2(newRow);
                    }
                }
            });
        }
    });

    // Update hidden status when HR changes select
    $(document).on('change', "select[name='status_select[]']", function(){
        $(this).closest("td").find(".status-hidden").val($(this).val());
    });

    // Load employee list for existing rows on page load
    $('#expense_wrapper tr.existing_row').each(function(){
        var row = $(this);
        var branchSelect = row.find('.branch_id');
        var employeeSelect = row.find('.employee_id');
        var selectedEmployee = employeeSelect.data('selected');

        var branch_id = branchSelect.val();
        if(branch_id){
            $.ajax({
                type: "POST",
                headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                url: "{{ route('user.travel_update.getemployee') }}",
                data: { branch_id: branch_id },
                success: function(data){
                    employeeSelect.html(data.data1);
                    employeeSelect.val(selectedEmployee).trigger('change');
                }
            });
        }
    });

});
</script>
