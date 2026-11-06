@extends('main')
@section('content')
<meta name="csrf-token" content="{{ csrf_token() }}" />
<div class="kt-container  kt-container--fluid  kt-grid__item kt-grid__item--fluid">
	<br>
	<div class="kt-portlet kt-portlet--mobile">
		<div class="kt-portlet__head kt-portlet__head--lg">
			<div class="kt-portlet__head-label">
				<h3 class="kt-portlet__head-title">
					PDF
				</h3>
			</div>
			<div class="kt-portlet__head-toolbar">
				<div class="kt-portlet__head-wrapper">
					<div class="kt-portlet__head-actions">					
						<form id="userimportform" enctype="multipart/form-data">
							@csrf
							<a href="{{route('user.pdf.create')}}" class="btn btn-brand btn-elevate btn-icon-sm">
								<i class="la la-plus"></i>
								Add PDF
							</a>
						</form>
						
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
						<!-- @foreach($employee as $sub)
						<option value="{{$sub->id}}">{{$sub->name}}</option>
						@endforeach -->
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
							<th>#</th>
							@if(Auth::guard('user')->check())
							@if(Auth::user()->role == 1)
							<th>Branch</th>
							<th>Employee</th>
							@endif
							@if(Auth::user()->role == 2)
							<th>Employee</th>
							@endif
							
							@endif
							<th>Pdf Name</th>
							<th>Action</th>
						</tr>
					</thead>
				</table>
			</div>
		</div>
		@include('layouts.multiple_action', array(
			'table_name' => 'pdfs',
			'is_orderby'=>'',
			'folder_name'=>'',
			'action' => array('change-status-1' => _('Active'), 'change-status-0' => _('Inactive'))
			))
		</div>
	</div>	
	@stop
	@push('scripts')
	<script>

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
				url:'{{ route('user.pdf.index') }}',
				data: function(d) {
					d.branch_id= $('#branch_id').val()
					
					d.employee_id = $('#employee_id').val()
				}
			},
			// ajax: "{{ route('user.pdf.index') }}",

			columns: [
			{
				orderable: false,
				searchable: true,
				data: "id"
			},
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
				searchable: false,
				'data': 'pdf_name',
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