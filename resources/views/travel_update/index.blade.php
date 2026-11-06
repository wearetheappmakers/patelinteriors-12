@extends('main')
@section('content')
<meta name="csrf-token" content="{{ csrf_token() }}" />
<div class="kt-container  kt-container--fluid  kt-grid__item kt-grid__item--fluid">
	<br>
	<div class="kt-portlet kt-portlet--mobile">
		<div class="kt-portlet__head kt-portlet__head--lg">
			<div class="kt-portlet__head-label">
				<h3 class="kt-portlet__head-title">
					Travel
				</h3>
				
			</div>
			
			<div class="kt-portlet__head-toolbar">
				<div class="kt-portlet__head-wrapper">
					<div class="kt-portlet__head-actions">					

						<form id="userimportform" enctype="multipart/form-data">
							@csrf
							<input type="file" style="display:none;" name="user_import" id="userimportfile" class="form-control">
						
							<a href="{{route('user.travel_update.create')}}" class="btn btn-brand btn-elevate btn-icon-sm">
								<i class="la la-plus"></i>
								Add Travel
							</a>
						
						</form>
						
					</div>
				</div>
			</div>
			
		</div>
		<div class="kt-portlet__body">
		
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
                    <select class="form-control select2 " id="employee_id" name="employee_id">
                        <option value="">Select </option>
                    </select>
                </div>
            @endif
			
		{{-- EMPLOYEE (HR only, role = 9) --}}
				@if(
			(Auth::guard('employee')->check() && Auth::guard('employee')->user()->role == 9) ||
			(Auth::guard('user')->check() && Auth::user()->role == 2)
	     	)
			<div class="form-group col-sm-3">
				<label>Employee</label>
				<select class="form-control select2" id="employee_id" name="employee_id">
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
				<!-- <div class="form-group col-lg-2">
					<label>Sataus </label>
					<select class="form-control changes select2" name="status" id="status" >
						<option value="2">All</option>
						<option value="1">Active</option>
						<option value="0">In Active</option>
					</select>
				</div> -->
				
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
                            <th>Purpose Of Travel</th>
							<!-- <th>Status</th> -->
							<th>Action</th>
						</tr>
					</thead>
				</table>
			</div>
		</div>
		@include('layouts.multiple_action', array(
		'table_name' => 'travel_update',
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
				url:'{{ route("user.travel_update.index") }}',
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
					data: "name",
				},
                
				{
					orderable: false,
					searchable: true,
					data: "contact_no",
				},
                {
					orderable: false,
					searchable: true,
					data: "purpose_of_travel",
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

		$('#branch_id, #employee_id').change(function () {
    oTable.draw();
       });
	});
	$('.select2').select2({
	});
</script>

<script type="text/javascript">
	function getemployee($this){
		var branch_id = $($this).val();
		
		$('#employee_id').html('<option value="">Select</option>');
		
		if(branch_id)
		{
			showLoader();

			$.ajax({
				type:"POST",
				headers: {
					'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
				},
				url:"{{ route('user.travel_update.getemployee') }}",
				data: {
					'branch_id': branch_id,
					 with_select: 1 
					
				},
				success: function(data){
					$('#employee_id').html(data.data1);
					
					hideLoader();
				}
			});
		}
	};

	

	 $('.select2').select2({
 
});
</script>
@endpush