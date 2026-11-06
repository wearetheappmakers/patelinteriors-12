@extends('main')
@section('content')
<meta name="csrf-token" content="{{ csrf_token() }}" />
<div class="kt-container  kt-container--fluid  kt-grid_item kt-grid_item--fluid">
	<br>
	<div class="kt-portlet kt-portlet--mobile">
		<div class="kt-portlet__head">
			<div class="kt-portlet__head-label">
				<h3 class="kt-portlet__head-title">
					Client
				</h3>
			</div>
			<div class="kt-portlet__head-toolbar">
				<div class="kt-portlet__head-wrapper">
					
					<div class="kt-portlet__head-actions">
						<a href="{{ route('user.client.create') }}" class="btn btn-brand btn-elevate btn-icon-sm"><i class="la la-plus"></i>Add Client</a>
					</div>
					
				</div>
			</div>
		</div>
		<div class="kt-portlet__body">
			<div class="row">
				<div class="form-group col-lg-2">
					<label>Status </label> 
					<select class="form-control changes select2" name="is_archive" id="is_archive" >
						<option value="2">All</option>
						<option value="1"> Archive</option>
						<option value="0">Not Archive</option>
					</select>
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
					<label>Landmark</label>
					<select name="landmark_id" class="form-control changes select2" id="landmark_id">
						<option value="">Select Landmark</option>
						
					</select>
				</div>
				<!-- <div class="form-group col-lg-2">
					<label>Employee </label>
					<select name="employee_id" class="form-control changes select2" id="employee_id">
						<option value="">Select Employee</option>
						{{-- @foreach($employee as $sub)
						<option value="{{$sub->id}}">{{$sub->name}}</option>
						@endforeach --}}
					</select>
				</div> -->
				@endif
				@if(Auth::user()->role == 2)
				<div class="form-group col-lg-2">
					<label>Landmark</label>
					<select name="landmark_id" class="form-control changes select2" id="landmark_id">
						<option value="">Select Landmark</option>
						@foreach($landmark as $lan)
						<option value="{{$lan->id}}">{{$lan->name}}</option>
						@endforeach
					</select>
				</div>
				<!-- <div class="form-group col-lg-2">
					<label>Employee </label>
					<select name="employee_id" class="form-control changes select2" id="employee_id">
						<option value="">Select Employee</option>
						@foreach($employee as $sub)
						<option value="{{$sub->id}}">{{$sub->name}}</option>
						@endforeach
					</select>
				</div> -->
				@endif
				@endif
				@if(Auth::guard('employee')->check())
				<?php
				$emp = App\Employee::where('id',Auth::user()->id)->value('branch_id');
				$landmark = App\Landmark::where('branch_id',$emp)->get();
				?>
				<div class="form-group col-lg-2">
					<label>Landmark</label>
					<select name="landmark_id" class="form-control changes select2" id="landmark_id">
						<option value="">Select Landmark</option>
						@foreach($landmark as $lan)
						<option value="{{$lan->id}}">{{$lan->name}}</option>
						@endforeach
					</select>
				</div>
				@endif
			</div>
			<br/>
			<div id="kt_table_1_wrapper" class="dataTables_wrapper dt-bootstrap4">
				<table class="table table-striped table-bordered table-hover table-checkable datatable" id="datatable_rows">
					@csrf
					<thead>
						<tr>
							<!-- <th>SrNo</th> -->
							@if(Auth::guard('user')->check())
							@if(Auth::user()->role == 1)
							<th>Branch</th>
							@endif
							@if(Auth::user()->role == 2)
							<!-- <th>Employee</th> -->
							@endif
							
							@endif					
							<th>Client</th>
							
							<th>Mobile</th>
							<th>Email</th>
							<th>Landmark</th>
							<th>Action</th>
						</tr>
					</thead>
					<tbody>
					</tbody>
				</table>
			</div>
		</div>
		@include('layouts.multiple_action', array(
		'table_name' => 'client',
		'is_orderby'=>'',
		'folder_name'=>'',
		'action' => array('change-status-1' => __('Active'), 'change-status-0' => __('Inactive'))
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
				url:'{{ route("user.client.index") }}',
				data: function(d) {
					d.is_archive= $('#is_archive').val()
					d.branch_id= $('#branch_id').val()
					d.employee_id = $('#employee_id').val()
					d.landmark_id = $('#landmark_id').val()
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
				data: "branch_id"
			},
				// {
				// 	orderable: false,
				// 	searchable: true,
				// 	data: "employee_id",
				// },
				@endif
				@if(Auth::user()->role == 2)
				// {
				// 	orderable: false,
				// 	searchable: true,
				// 	data: "employee_id",
				// },
				
				@endif
				@endif
				{
					orderable: false,
					searchable: true,
					data: "member"
				},

				{
					orderable: false,
					searchable: true,
					data: "mobile_no"
				},
				{
					orderable: false,
					searchable: true,
					data: "email"
				},
				{
					orderable: false,
					searchable: true,
					data: "landmark_id"
				},

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
	function getdata($this){
		var branch_id = $($this).val();
		var id = $($this).data('id');
		$.ajax({
			type:"POST",
			headers: {
				'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
			},
			url:"{{ route('user.client.getemployee') }}",
			data: {
				'branch_id': branch_id
			},
			success: function(data){
				$('#employee_id').html(data.data1);
				$('#landmark_id').html(data.data2);
			}
		});
	};
</script>
@endpush