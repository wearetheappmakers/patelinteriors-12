@extends('main')
@section('content')

<div class="kt-container  kt-container--fluid  kt-grid__item kt-grid__item--fluid">
    <br>
    <div class="kt-portlet kt-portlet--mobile">
        <div class="kt-portlet__head kt-portlet__head--lg">
            <div class="kt-portlet__head-label">
                <h3 class="kt-portlet__head-title">
                Company Policy
                </h3>
            </div>
            <div class="kt-portlet__head-toolbar">
                <div class="kt-portlet__head-wrapper">
                    <div class="kt-portlet__head-actions">

                        <a href="{{ route('user.company_policy.create') }}" 
                        class="btn btn-brand btn-elevate btn-icon-sm">
                        <i class="la la-plus"></i>
                        Add Company Policy
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
                        <th>Sr no </th>
                        @if(Auth::guard('user')->check() && Auth::guard('user')->user()->role == 1)
                        <th>Branch</th>
                        <th>Created By</th>
                        @endif
                        <th>Action</th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>

    @include('layouts.multiple_action', [
        'table_name' => 'company_policy',
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
                url: '{{ route("user.company_policy.index") }}',
                data: function(d) {
                    d.branch_id = $('#branch_id').val();
                    d.employee_id = $('#employee_id').val();
                }
            },
            columns: [
                
                { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
                @if(Auth::guard('user')->check() && Auth::guard('user')->user()->role == 1)
                { data: 'branch_id',orderable: false, searchable: true },
                { data: 'created_by_name',orderable: false, searchable: true },
                @endif
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
