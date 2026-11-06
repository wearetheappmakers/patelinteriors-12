@extends('main')
@section('content')
<meta name="csrf-token" content="{{ csrf_token() }}" />
<div class="kt-container  kt-container--fluid  kt-grid__item kt-grid__item--fluid">
	<br>
	<div class="kt-portlet kt-portlet--mobile">
		<div class="kt-portlet__head">
			<div class="kt-portlet__head-label">
				<span class="kt-portlet__head-icon">
					<i class="kt-font-brand"></i>
				</span>
				<h3 class="kt-portlet__head-title">
					Interior Visit
				</h3>
			</div>
			<div class="kt-portlet__head-toolbar">
				<div class="kt-portlet__head-wrapper">
					<div class="kt-portlet__head-actions">					
						<a href="{{route('user.visits.create')}}" class="btn btn-brand btn-elevate btn-icon-sm">
							<i class="la la-plus"></i>
							Add Visit
						</a>
					</div>
				</div>
			</div>
		</div>
		<div class="kt-portlet__body">
			<div class="row">
				@if(Auth::guard('user')->check())
				@if(Auth::user()->role == 1)
				@php $branch = App\User::where('role',2)->where('status',1)->get(); @endphp
				<div class="form-group col-lg-2">
					<label>Branch </label> 
					<select name="branch_id" class="form-control changes select2" onchange="getdata(this)" id="branch_id">
						<option value="">Select Branch</option>
						@foreach($branch as $sub)
						<option value="{{$sub->id}}">{{$sub->name}}</option>
						@endforeach
					</select>
				</div>        
				<div class="form-group col-lg-2">
					<label>Employee </label>
					<select name="employee_id" class="form-control changes select2" id="employee_id">
						<option value="">Select</option>
						
					</select>
				</div>
				<div class="form-group col-lg-2">
					<label>Ar/ID</label>
					<select name="arid" id="arid" class="form-control changes select2" required>
						<option value="">Select</option>
						
					</select>
				</div>	
				@endif
				@endif	

				<?php
				$employees = App\Employee::where('branch_id',Auth::user()->id)->get();
				$arid = App\Arid::where('branch_id',Auth::user()->id)->get();
				?>
				@if(Auth::guard('user')->check())
				@if(Auth::user()->role == 2)
				<div class="form-group col-lg-2">
					<label>Employee </label>
					<select name="employee_id" class="form-control changes select2" id="employee_id">
						<option value="">Select Employee</option>
						@foreach($employees as $sub)
						<option value="{{$sub->id}}">{{$sub->name}}</option>
						@endforeach
					</select>
				</div>
				<div class="form-group col-lg-2">
					<label>Ar/ID</label>
					<select name="arid" id="arid" class="form-control changes select2" required>
						<option value="">Select</option>
						@foreach($arid as $ar)
						<option value="{{$ar->id}}">{{$ar->name}}</option>
						@endforeach
					</select>
				</div>	
				@endif
				@endif	

				@if(Auth::guard('employee')->check())
				<?php
				$arid = App\Arid::where('branch_id',Auth::user()->branch_id)->get();
				?>
				<div class="form-group col-lg-2">
					<label>Ar/ID</label>
					<select name="arid" id="arid" class="form-control changes select2" required>
						<option value="">Select</option>
						@foreach($arid as $ar)
						<option value="{{$ar->id}}">{{$ar->name}}</option>
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
							<th>Ar/ID</th>
							<th>Firm Name</th>
							<th>Showroom Visit</th>
							<th>Remarks</th>
							<th>Date</th>
							<th>Company Person</th>
							<th>Status</th>
							<th>Action</th>
						</tr>
					</thead>
				</table>
			</div>
		</div>
		@include('layouts.multiple_action', array(
		'table_name' => 'interior_visit',
		'is_orderby'=>'',
		'folder_name'=>'',
		'action' => array()
		))
	</div>
</div>	
<div class="modal fade" id="edit_modals" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
	<div class="modal-dialog modal-xl" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title" id="exampleModalLabel">Followup</h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close"></button>
			</div>
			<form class="edit_form" method="POST" id="edit_form">
				<div class="modal-body">
					@csrf 
					@method('PUT') 
					<div class="table_followup" id="table_followup">
					</div>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
				</div>
			</div>
		</form>
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
				url:'{{ route("user.visits.index") }}',
				data: function(d) {
					d.branch_id= $('#branch_id').val(),
					d.employee_id= $('#employee_id').val(),				
					d.arid= $('#arid').val(),				
					d.from_date= $('#from_date').val(),				
					d.to_date= $('#to_date').val(),				
					d.client_id= $('#client_id').val(),		
					d.source_id= $('#source_id').val(),		
					d.source_name= $('#source_name').val()
				}
			},		
			
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
					data: "arids.name"
				},
				{
					orderable: false,
					searchable: true,
					data: "firm_name"
				},
				{
					orderable: false,
					searchable: true,
					data: "showroomvisit"
				},
				{
					orderable: false,
					searchable: true,
					data: "remarks"
				},
				{
					orderable: false,
					searchable: true,
					data: "date"
				},
				{
					orderable: false,
					searchable: true,
					data: "company_person"
				},
				{
					orderable: false,
					searchable: true,
					data: "status"
				},
				{
					orderable: false,
					searchable: false,
					'data': 'action'
				},
			]
		});
		$('.changes').change(function(){
			oTable.draw();
		});
		$('.filter').change(function(){
			oTable.draw();
		});
	});
	$('.changes').select2({
	});
</script>
<script type="text/javascript">
	function statuschange($this,id)
	{
		var status = $($this).val();
		$.ajax({
			type: 'POST',
			url: '{{ route("user.change.status") }}',
			data: {
				'_token': '{{ csrf_token() }}',
				id: id,
				status: status,
			},
			dataType: 'json',
			success: function(data) {
				if (data.status == 'success') {
					toastr["success"]("Status Changed successfully", "Success");
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
</script>
<script type="text/javascript">
	function getdata($this){
		var branch_id = $($this).val();
		var id = $($this).data('id');
		
		$('#employee_id').html('<option value="">Select</option>');
		$('#arid').html('<option value="">Select</option>');
		$('#email').val('');
		$('#number').val('');

		if(branch_id != '')
		{
			showLoader();
			$.ajax({
				type:"POST",
				headers: {
					'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
				},
				url:"{{ route('user.visit.getemployee') }}",
				data: {
					'branch_id': branch_id
				},
				success: function(data){
					hideLoader();
					$('#employee_id').html(data.data);
					$('#arid').html(data.data3);
				}
			});
		}
	};
	function model(id)
	{
		$.ajax({
			type: 'POST',
			url: '{{ route("user.iv.followup.model") }}',
			data: {
				'_token': '{{ csrf_token() }}',
				id: id,
			},
			dataType: 'json',
			success: function(data) {
				$('#edit_modals').modal('show');
				$('#table_followup').html(data.data);
			}
		});
	}
</script>
@endpush