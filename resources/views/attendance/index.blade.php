@extends('main')
@section('content')
<meta name="csrf-token" content="{{ csrf_token() }}" />
<div class="kt-container  kt-container--fluid  kt-grid__item kt-grid__item--fluid">
	<br>
	<div class="kt-portlet kt-portlet--mobile">
		<div class="kt-portlet__head kt-portlet__head--lg">
			<div class="kt-portlet__head-label">
				
				<h3 class="kt-portlet__head-title">
					Attendance
				</h3>
			</div>
			<div class="kt-portlet__head-toolbar">
				<div class="kt-portlet__head-wrapper">
					<div class="kt-portlet__head-actions">
						@if(Auth::guard('employee')->check())
						<a href="{{ route('user.attendance.create') }}" class="btn btn-brand btn-elevate btn-icon-sm"><i class="la la-plus"></i>Add Attendance</a>
						@endif
						<form id="userimportform" enctype="multipart/form-data">
							@csrf
							<input type="file" style="display:none;" name="user_import" id="userimportfile" class="form-control">
						</form>
						
					</div>
				</div>
			</div>
		</div>
		<div class="kt-portlet__body">
			<div class="row">
				<div class="form-group col-lg-2">
					<label>From Date</label>
					<input type="date" id="from_date" class="form-control changes">
				</div>
				<div class="form-group col-lg-2">
					<label>To Date</label>
					<input type="date" id="to_date" class="form-control changes">
				</div>
				@if(Auth::guard('user')->check())
				@if(Auth::user()->role == 1)
				<div class="form-group col-lg-2">
					<label>Branch </label>
					<select class="form-control changes select2" name="branch_id" onchange="getdata(this)" id="branch_id" >
						<option value="">Select Branch</option>
						@foreach($branch as $sub)
						<option value="{{$sub->id}}">{{$sub->name}}</option>
						@endforeach
					</select>
				</div>
				<div class="form-group col-lg-2">
					<label>Employee </label>
					<select name="employee_id" class="form-control changes select2" id="employee_id">
						<option value="">Select Employee</option>
						{{-- @foreach($employee as $sub)
						<option value="{{$sub->id}}">{{$sub->name}}</option>
						@endforeach --}}
					</select>
				</div>
				@endif
				@if(Auth::user()->role == 2)
				<div class="form-group col-lg-2">
					<label>Employee </label>
					<select name="employee_id" class="form-control changes select2" id="employee_id">
						<option value="">Select Employee</option>
						@foreach($employee as $sub)
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
							<!-- <th>#</th> -->
							@if(Auth::guard('user')->check())
							@if(Auth::user()->role == 1)
							<th>Branch</th>
							<th>Employee</th>
							@endif
							@if(Auth::user()->role == 2)
							<th>Employee</th>
							@endif
							@endif
							<th>Date</th>
							<th>Attendance Status</th>
							<th>Action</th>
						</tr>
					</thead>
				</table>
			</div>
		</div>
		@include('layouts.multiple_action', array(
			'table_name' => 'attendance',
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
					url:'{{ route("user.attendance.index") }}',
					data: function(d) {
						d.branch_id= $('#branch_id').val(),
						d.employee_id = $('#employee_id').val(),
						d.from_date= $('#from_date').val(),
						d.to_date= $('#to_date').val()
					}
				},
				// ajax: "{{ route('user.attendance.index') }}",
				columns: [
				// {
				// 	orderable: false,
				// 	searchable: true,
				// 	data: "id"
				// },
					@if(Auth::guard('user')->check())
					@if(Auth::user()->role == 1)
					{
						orderable: false,
						searchable: true,
						data: "branch.name"
					},
					{
						orderable: false,
						searchable: true,
						data: "employee.name",
					},
					@endif
					@if(Auth::user()->role == 2)
					{
						orderable: false,
						searchable: true,
						data: "employee.name",
					},

					@endif
					@endif
					{
						orderable: false,
						searchable: true,
						data: "date",
					},
					{
						orderable: true,
						searchable: true,
						data: "attendance",
					},	
					{
						orderable: true,
						searchable: true,
						data: "action",
					},				
				]
			});
			$('.changes').change(function(){
				
				oTable.draw();
			});
		});
		function attendance($this,id)
		{
			var attendance = $($this).val();
			$.ajax({
				type: 'POST',
				url: '{{ route("user.attendance.status") }}',
				data: {
					'_token': '{{ csrf_token() }}',
					id: id,
					attendance: attendance,
				},
				dataType: 'json',
				success: function(data) {
					if (data.status == 'success') {
						toastr["success"]("Attendance Changed successfully", "Success");
						location.reload();
					} 
					else 
					{
						toastr["error"]("Something went wrong!", "Error");
						location.reload();
					}
				}
			});
		}
		function getdata($this){
			var branch_id = $($this).val();
			var id = $($this).data('id');
			$.ajax({
				type:"POST",
				headers: {
					'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
				},
				url:"{{ route('user.employee.name') }}",
				data: {
					'branch_id': branch_id
				},
				success: function(data){
					console.log(data.data);
					$('#employee_id').html(data);
					
				}
			});
		};
	</script>
	@endpush