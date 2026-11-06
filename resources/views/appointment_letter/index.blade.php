@extends('main')
@section('content')

<div class="kt-container  kt-container--fluid  kt-grid__item kt-grid__item--fluid">
    <br>
    <div class="kt-portlet kt-portlet--mobile">
        <div class="kt-portlet__head kt-portlet__head--lg">
            <div class="kt-portlet__head-label">
                <h3 class="kt-portlet__head-title">
                    Appointment Letter
                </h3>
            </div>
            <div class="kt-portlet__head-toolbar">
                <div class="kt-portlet__head-wrapper">
                    <div class="kt-portlet__head-actions">

                        <a href="{{ route('user.appointment_letter.create') }}" 
                        class="btn btn-brand btn-elevate btn-icon-sm">
                        <i class="la la-plus"></i>
                        Add Appointment Letter
                    </a>

                </div>
            </div>
        </div>
    </div>

    <div class="kt-portlet__body">
        <div class="row">
            @if(Auth::guard('user')->check())
            @if(Auth::user()->role == 1)  
            <div class="form-group col-lg-2">
                <label>Branch </label>
                @php $branch = App\User::where('status',1)->where('role',2)->get(); @endphp
                <select name="branch_id" class="form-control changes select2" id="branch_id">
                    <option value="">Select Branch</option>
                    @foreach($branch as $sub)
                    <option value="{{$sub->id}}">{{$sub->name}}</option>
                    @endforeach
                </select>
            </div>
            @endif
            @endif
        </div>

        <div id="kt_table_1_wrapper" class="dataTables_wrapper dt-bootstrap4">
            <table class="table table-striped table-bordered table-hover table-checkable datatable" id="datatable_rows">
                @csrf
                <thead>
                    <tr>
                        @if(Auth::guard('user')->check() && Auth::guard('user')->user()->role == 1)
                        <th>Branch</th>
                        <th>Created By</th>
                        @endif
                        <th>Candidate Name</th>
                        <th>Position</th>
                        <th>Offer Letter Date</th>
                        <th>Salary</th>
                        <th>Action</th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>

    @include('layouts.multiple_action', [
        'table_name' => 'appointment_letter',
        'is_orderby' => '',
        'folder_name' => '',
        'action' => [
            'change-status-1' => _('Active'),
            'change-status-0' => _('Inactive')
        ]
        ])
    </div>
</div>

@stop

@push('scripts')
<script>
    $(document).ready(function() {

        var oTable = $('#datatable_rows').DataTable({
            processing: true,
            serverSide: true,
            searchable: true,
            scrollX: true,
            stateSave: true,
            columnDefs: [{ orderable: false, targets: -1 }],
            ajax: {
                url: '{{ route("user.appointment_letter.index") }}',
                data: function(d) {
                    d.branch_id = $('#branch_id').val();
                    d.employee_id = $('#employee_id').val();
                }
            },
            columns: [
                @if(Auth::guard('user')->check() && Auth::guard('user')->user()->role == 1)
                { data: 'branch_id',orderable: false, searchable: true },
                { data: 'created_by_name',orderable: false, searchable: true },
                @endif
                { data: "employee_name", orderable: false, searchable: true },
                { data: "position", orderable: false, searchable: true },
                { data: "date", orderable: false, searchable: true },
                { data: "amount", orderable: false, searchable: true },
                { data: "action", orderable: false, searchable: false }
            ]
        });

        $('.changes').change(function() {
            oTable.draw();
        });
    });

    $('.select2').select2();
</script>
@endpush
