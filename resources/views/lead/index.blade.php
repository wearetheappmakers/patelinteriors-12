@extends('main')
@section('content')
<meta name="csrf-token" content="{{ csrf_token() }}" />
<div class="kt-container  kt-container--fluid  kt-grid_item kt-grid_item--fluid">
	<br>
	<div class="kt-portlet kt-portlet--mobile">
		<div class="kt-portlet__head">
			<div class="kt-portlet__head-label">
				<h3 class="kt-portlet__head-title">
					Lead
				</h3>
			</div>
			<div class="kt-portlet__head-toolbar">
				<div class="kt-portlet__head-wrapper">					
					<div class="kt-portlet__head-actions">
						<a href="{{ route('user.lead.create') }}" class="btn btn-brand btn-elevate btn-icon-sm"><i class="la la-plus"></i>Add Lead</a>&nbsp;&nbsp;&nbsp;
					</div>	
					@if(Auth::guard('user')->check())
					<a href="{{ route('user.lead.activeindex') }}" class="btn btn-brand btn-elevate btn-icon-sm">InActive Lead</a>
					@endif				
				</div>
			</div>
		</div>
		<div class="kt-portlet__body">
			<div class="row">				
				@if(Auth::guard('user')->check())
				@if(Auth::user()->role == 1)
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
						<option value="">Select Employee</option>
					</select>
				</div>
				<div class="form-group col-lg-2">
					<label>Source </label>
					<select name="source_id" class="form-control changes select2" id="source_id">
						<option value="">Select Source</option>
					</select>
				</div>
				<div class="form-group col-lg-2">
					<label>Source Name </label>
					<select name="source_name" class="form-control changes select2" id="source_name">
						<option value="">Select Source Name</option>
					</select>
				</div>
				<div class="form-group col-lg-2">
					<label>Ar/Id </label>
					<select name="arid" class="form-control changes select2" id="arid">
						<option value="">Select Ar/Id</option>
					</select>
				</div>
				<div class="form-group col-lg-2">
					<label>Client </label>
					<select name="client_id" class="form-control changes select2" id="client_id">
						<option value="">Select Client</option>
					</select>
				</div>
				<div class="form-group col-lg-2">
					<label>Category</label>
					<select name="lead_name" id="lead_name" class="form-control changes select2" >
						<option value="">Select Category</option>
					</select>
				</div>
				<div class="form-group col-lg-2">
					<label>Company</label>
					<select name="company_name" id="company_name" class="form-control changes select2" >
						<option value="">Select Company</option>
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
				@if(Auth::guard('employee')->check())
				<div class="form-group col-lg-2">
					<label>Source </label>
					<select name="source_id" class="form-control changes select2" id="source_id">
						<option value="">Select Source</option>
						@foreach($source as $sub)
						<option value="{{$sub->id}}">{{$sub->name}}</option>
						@endforeach
					</select>
				</div>
				<div class="form-group col-lg-2">
					<label>Source Name </label>
					<select name="source_name" class="form-control changes select2" id="source_name">
						<option value="">Select Source Name</option>
						@foreach($sourcename as $sub)
						<option value="{{$sub->id}}">{{$sub->name}}</option>
						@endforeach
					</select>
				</div>
				<div class="form-group col-lg-2">
					<label>Ar/Id </label>
					<select name="arid" class="form-control changes select2" id="arid">
						<option value="">Select Ar/Id</option>
						@foreach($arids as $sub)
						<option value="{{$sub->id}}">{{$sub->name}}</option>
						@endforeach
					</select>
				</div>
				<div class="form-group col-lg-2">
					<label>Client </label>
					<select name="client_id" class="form-control changes select2" id="client_id">
						<option value="">Select Client</option>
						@foreach($client as $sub)
						<option value="{{$sub->id}}">{{$sub->member}}</option>
						@endforeach
					</select>
				</div>
				<div class="form-group col-lg-2">
					<label>Category</label>
					<select name="lead_name" id="lead_name" class="form-control changes select2" >
						<option value="">Select Category</option>
						@foreach($productcategory as $sub)
						<option value="{{$sub->id}}" >{{$sub->name}}</option>
						@endforeach
					</select>
				</div>
				<div class="form-group col-lg-2">
					<label>Company</label>
					<select name="company_name" id="company_name" class="form-control changes select2" >
						<option value="">Select Company</option>
						@foreach($company as $sub)
						<option value="{{$sub->id}}" >{{$sub->name}}</option>
						@endforeach
					</select>
				</div>
				@endif	
				@if(Auth::guard('user')->check())
				@if(Auth::user()->role == 2)
				<div class="form-group col-lg-2">
					<label>Source </label>
					<select name="source_id" class="form-control changes select2" id="source_id">
						<option value="">Select Source</option>
						@foreach($source as $sub)
						<option value="{{$sub->id}}">{{$sub->name}}</option>
						@endforeach
					</select>
				</div>
				<div class="form-group col-lg-2">
					<label>Source Name </label>
					<select name="source_name" class="form-control changes select2" id="source_name">
						<option value="">Select Source Name</option>
						@foreach($sourcename as $sub)
						<option value="{{$sub->id}}">{{$sub->name}}</option>
						@endforeach
					</select>
				</div>
				<div class="form-group col-lg-2">
					<label>Ar/Id</label>
					<select name="arid" class="form-control changes select2" id="arid">
						<option value="">Select Ar/Id</option>
						@foreach($arids as $sub)
						<option value="{{$sub->id}}">{{$sub->name}}</option>
						@endforeach
					</select>
				</div>
				<div class="form-group col-lg-2">
					<label>Client </label>
					<select name="client_id" class="form-control changes select2" id="client_id">
						<option value="">Select Client</option>
						@foreach($client as $sub)
						<option value="{{$sub->id}}">{{$sub->member}}</option>
						@endforeach
					</select>
				</div>
				<div class="form-group col-lg-2">
					<label>Category</label>
					<select name="lead_name" id="lead_name" class="form-control changes select2" >
						<option value="">Select Category</option>
						@foreach($productcategory as $sub)
						<option value="{{$sub->id}}" >{{$sub->name}}</option>
						@endforeach
					</select>
				</div>
				<div class="form-group col-lg-2">
					<label>Company</label>
					<select name="company_name" id="company_name" class="form-control changes select2" >
						<option value="">Select Company</option>
						@foreach($company as $sub)
						<option value="{{$sub->id}}" >{{$sub->name}}</option>
						@endforeach
					</select>
				</div>
				@endif
				@endif
				<div class="form-group col-lg-2">
					<label>From Date </label>
					<input type="date" name="from_date" id='from_date' class="form-control changes">
				</div>
				<div class="form-group col-lg-2">
					<label>To Date </label>
					<input type="date" name="to_date" id='to_date' class="form-control changes">
				</div>
				<div class="form-group col-lg-2">
					<label>Status</label>
					<select name="status" id="status" class="form-control changes select2">
						<option value="">All</option>
						<option value="1">Pending</option>
						<option value="2">Discussion</option>
						<option value="3">Rivision</option>
						<option value="4">Win</option>
						<option value="5">Lost</option>
					</select>
				</div>	
			</div>
			<div id="kt_table_1_wrapper" class="dataTables_wrapper dt-bootstrap4">
				<table class="table table-striped table-bordered table-hover table-checkable datatable" id="datatable_rows">
					@csrf
					<thead>
						<tr>
							@if(Auth::guard('user')->check())
							@if(Auth::user()->role == 1)
							<th>Branch</th>
							<th>Client</th>	
							<th width="20%">Employee</th>
							@endif
							@if(Auth::user()->role == 2)
							<th>Employee</th>							
							@endif							
							@endif
							<th>Source</th>
							<th>Source Name</th>
							<th>Ar/Id</th>
							@if(Auth::guard('user')->check())
							@if(!(Auth::user()->role == 1))
							<th>Client</th>	
							@endif
							@endif	
							@if(Auth::guard('employee')->check())
							<th>Client</th>	
							@endif						
							<th>Category</th>
							<th>Company</th>					
							<th>Lead Date</th>							
							<th>Status</th>
							<th>Followup Status</th>
							<th>Action</th>
						</tr>
					</thead>
					<tbody>
					</tbody>
				</table>
			</div>
		</div>
		@include('layouts.multiple_action', array(
		'table_name' => 'lead',
		'is_orderby'=>'',
		'folder_name'=>'',
		'action' => array('change-status-1' => __('Active'), 'change-status-0' => __('Inactive'))
		))
	</div>
	<div class="row col-sm-12">
		<div class="modal fade" id="edit_modals" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
			<div class="modal-dialog modal-lg" role="document">
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
				url:'{{ route("user.lead.index") }}',
				data: function(d) {
					d.branch_id= $('#branch_id').val()
					d.employee_id= $('#employee_id').val()					
					d.status= $('#status').val()					
					d.client_id= $('#client_id').val()					
					d.source_name= $('#source_name').val()					
					d.arid= $('#arid').val()					
					d.source_id= $('#source_id').val()					
					d.from_date= $('#from_date').val()					
					d.to_date= $('#to_date').val()	
					d.lead_name= $('#lead_name').val()					
					d.company_name= $('#company_name').val()					
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
					searchable: false,
					data: "branch_id"
				},
				{
					orderable: false,
					searchable: false,
					data: "client.member"
				},
				{
					orderable: false,
					searchable: false,
					data: "employee_id",
				},
				@endif
				@if(Auth::user()->role == 2)
				{
					orderable: false,
					searchable: false,
					data: "employee_id",
				},
				@endif
				@endif
				{
					orderable: false,
					searchable: false,
					data: "source.name"
				},
				{
					orderable: false,
					searchable: false,
					data: "source_name"
				},
				{
					orderable: false,
					searchable: false,
					data: "arid_id"
				},
				@if(Auth::guard('user')->check())
				@if(!(Auth::user()->role == 1))
				{
					orderable: false,
					searchable: false,
					data: "client.member"
				},
				@endif
				@endif
				@if(Auth::guard('employee')->check())
				{
					orderable: false,
					searchable: false,
					data: "client.member"
				},
				@endif
				{
					orderable: false,
					searchable: false,
					data: "lead_name"
				},
				{
					orderable: true,
					searchable: true,
					data: "company_name"
				},
				{
					orderable: true,
					searchable: true,
					data: "date"
				},
				{
					orderable: true,
					searchable: true,
					data: "status"
				},
				{
					orderable: true,
					searchable: true,
					data: "followup_status"
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
	$('.select2').select2({ 
	});
	$('#lead_name').on('select2:select', function(){
		getdata(this);
	});	
	function getdata($this){

		var branch_id = $($this).val();
		var id = $($this).data('id');

		$.ajax({
			type:"POST",
			headers: {
				'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
			},
			url:"{{ route('user.report.getdata') }}",
			data: {
				'branch_id': branch_id
			},
			success: function(data){

				$('#employee_id').html(data.data);        
				$('#source_id').html(data.data2);        
				$('#client_id').html(data.data1);        
				$('#source_name').html(data.data3);  
				$('#lead_name').html(data.data4);

				$('#company_name').html(data.data5);
				$('#arid').html(data.data8);
			}
		});
	}
	function model(id)
	{
		$.ajax({
			type: 'POST',
			url: '{{ route("user.lead.model") }}',
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