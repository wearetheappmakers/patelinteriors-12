@extends('main')
@section('content')
<meta name="csrf-token" content="{{ csrf_token() }}" />
<div class="kt-container  kt-container--fluid  kt-grid__item kt-grid__item--fluid">
	<br>
	<div class="kt-portlet kt-portlet--mobile">
		<div class="kt-portlet__head kt-portlet__head--lg">
			<div class="kt-portlet__head-label">
				<h3 class="kt-portlet__head-title">
				Meeting Entry Employee
				</h3>
			</div>
			<div class="kt-portlet__head-toolbar">
				<div class="kt-portlet__head-wrapper">
					<div class="kt-portlet__head-actions">					

						<form id="userimportform" enctype="multipart/form-data">
							@csrf
							<input type="file" style="display:none;" name="user_import" id="userimportfile" class="form-control">
						
							<a href="{{route('user.meeting_entry_employee.create')}}" class="btn btn-brand btn-elevate btn-icon-sm">
								<i class="la la-plus"></i>
								Add Meeting Entry 
							</a>
							
							
						</form>
						
					</div>
				</div>
			</div>
		</div>
		<div class="kt-portlet__body">
			
		<div class="row">

	{{-- SUPER ADMIN (user guard, role = 1) --}}
	@if(Auth::guard('user')->check() && Auth::user()->role == 1)

	<div class="form-group col-lg-2">
	<label>Branch</label>
	<select class="form-control select2 changes"
	id="branch_id"
	onchange="loadEmployees(this)">
	<option value="">Select Branch</option>
	@foreach($branch as $sub)
	<option value="{{ $sub->id }}">{{ $sub->name }}</option>
	@endforeach
	</select>
	</div>

	<div class="form-group col-lg-2">
	<label>Employee</label>
	<select class="form-control select2 changes" id="employee_id">
	<option value="">Select Employee</option>
	</select>
	</div>

	@endif


	{{-- BRANCH USER (user guard, role = 2) --}}
	@if(Auth::guard('user')->check() && Auth::user()->role == 2)

	<div class="form-group col-lg-2">
	<label>Employee</label>
	<select class="form-control select2 changes" id="employee_id">
	<option value="">Select Employee</option>
	@foreach($employee as $emp)
	<option value="{{ $emp->id }}">{{ $emp->name }}</option>
	@endforeach
	</select>
	</div>

	@endif


	{{-- HR (employee guard, role = 9) --}}

	@if(Auth::guard('employee')->check() && Auth::guard('employee')->user()->role == 9)

	<div class="form-group col-lg-2">
	<label>Employee</label>
	<select class="form-control select2 changes" id="employee_id">
	<option value="">Select Employee</option>
	@foreach($employee as $emp)
	<option value="{{ $emp->id }}">{{ $emp->name }}</option>
	@endforeach
	</select>
	</div>

	@endif


	</div>
			<div id="kt_table_1_wrapper" class="dataTables_wrapper dt-bootstrap4">
				<table class="table table-striped table-bordered table-hover table-checkable datatable" id="datatable_rows">
					@csrf
					<thead>
						<tr>
						
							@if(Auth::user()->role == 9 || Auth::user()->role == 2) 
								<th>Employee Name</th>
							@endif
							@if(Auth::user()->role == 1 ) 
								<th>Employee Name</th>
								<th>Branch Name</th>
							@endif

							<th>Name</th>
                            <th>Contact No</th>
                            <th>Purpose Of meeting</th>
							<!-- <th>Status</th> -->
							<th>Action</th>
						</tr>
					</thead>
				</table>
			</div>
		</div>
		@include('layouts.multiple_action', array(
		'table_name' => 'meeting_entry_emp',
		'is_orderby'=>'',
		'folder_name'=>'',
		'action' => array('change-status-1' => _('Active'), 'change-status-0' => _('Inactive'))
		))
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
			columnDefs: [{
				orderable: false,
				targets: -1,
			}],
			ajax: {
				url:'{{ route("user.meeting_entry_employee.index") }}',
				data: function(d) {
					d.status= $('#status').val()
					
					d.branch_id = $('#branch_id').val();
                    d.employee_id = $('#employee_id').val();
				}
			},
				// ajax: "{{ route('user.company.index') }}",
			columns: [
				 @if(Auth::user()->role == 9 || Auth::user()->role == 2)
				{
					orderable: false,
					searchable: true,
					data: "employee_name",
				},
				@endif
				@if(Auth::user()->role == 1)
				{
					orderable: false,
					searchable: true,
					data: "employee_name",
				},
				{
					orderable: false,
					searchable: true,
					data: "branch_name",
				},
				@endif
				{
					orderable: false,
					searchable: true,
					data: "client_name",
				},
                
				{
					orderable: false,
					searchable: true,
					data: "contact_no",
				},
                {
					orderable: false,
					searchable: true,
					data: "purpose_of_meeting",
				},
				// {
				// 	orderable: false,
				// 	searchable: false,
				// 	'data': 'singlecheckbox',
				// },
				{
					orderable: false,
					searchable: false,
					'data': 'action',
				},
			]
		});
		$('.changes').change(function(){
			oTable.draw();
		});
	});

	function loadEmployees(el) {
    let branch_id = $(el).val();
    let $emp = $('#employee_id');

    $emp.val(null).trigger('change');
    $emp.empty().append('<option value="">Select Employee</option>');

    if (branch_id) {
        $.ajax({
            type: "POST",
            url: "{{ route('user.meeting_entry_employee.getemployee') }}",
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            data: { branch_id: branch_id },
            success: function (res) {

                
                $emp.append(res.data1);

              
                $emp.val('').trigger('change');
            }
        });
    }

 
    $('#datatable_rows').DataTable().draw();
}

	$('.select2').select2({
	});
</script>
@endpush