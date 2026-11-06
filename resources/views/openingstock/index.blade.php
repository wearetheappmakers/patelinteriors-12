@extends('main')
@section('content')
<meta name="csrf-token" content="{{ csrf_token() }}" />
<div class="kt-container  kt-container--fluid  kt-grid_item kt-grid_item--fluid">
	<br>
	<div class="kt-portlet kt-portlet--mobile">
		<div class="kt-portlet__head">
			<div class="kt-portlet__head-label">
				<h3 class="kt-portlet__head-title">
					Opening Stock
				</h3>
			</div>
			<div class="kt-portlet__head-toolbar">
				<div class="kt-portlet__head-wrapper">
					
					@if(Auth::guard('user')->check())
					@if(Auth::user()->role == 2 ||Auth::user()->role == 1)
					@php
					$date=date('Y-m-d');
					if (date('m') >= 4) {
						$financial_year = (date('Y')) . '-' . (date('y')+1);
					} else {
						$financial_year = (date('Y')-1) . '-' . date('y');
					}
					$check = App\OpeningStock::where('fy_year',$financial_year)->where('branch_id',Auth::user()->id)->count();
					@endphp
					@if($check == 0)
					<div class="kt-portlet__head-actions">
						<a href="{{ route('user.opening.stock.sample') }}" class="btn btn-brand btn-elevate btn-icon-sm"><i class="la la-download"></i>Sample</a>
					</div>
					&nbsp;&nbsp;
					<form method="POST" id="import_form" enctype="multipart/form-data">
						@csrf	
						<input type="file" style="display:none;" name="importfile" id="importfile">
					</form>
					<div class="kt-portlet__head-actions">
						<button type="button" class="btn btn-brand btn-elevate btn-icon-sm importbtn"><i class="fa fa-file-import"></i>Financial year Import</button>
					</div>
					@else
					<form method="POST" id="childimport_form" enctype="multipart/form-data">
						@csrf	
						<input type="file" style="display:none;" name="childimportfile" id="childimportfile">
					</form>
					<div class="kt-portlet__head-actions">
						
						<button type="button" class="btn btn-brand btn-elevate btn-icon-sm childbutton" id="childbutton"><i class="fa fa-file-import"></i>Update Old Import</button>
					</div>
					@endif
					@endif
					@endif
					
					
				</div>
			</div>
		</div>
		<div class="kt-portlet__body">
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
							@endif					
							<th>Date</th>
							
							<th>Financial Year</th>
							<th>Action</th>
						</tr>
					</thead>
					<tbody>
					</tbody>
				</table>
			</div>
		</div>
		@include('layouts.multiple_action', array(
			'table_name' => 'opening_stock',
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
			$('.childbutton').on('click', function() {
				$('#childimportfile').trigger('click');
			})

			$('.importbtn').on('click', function() {
				$('#importfile').trigger('click');
			});

			$('#importfile').change(function(e) {
				$('.product_upload').prop('disabled', true);
				$('.product_upload').find('.la-spin').removeClass('d-none');

				if($("#import_form").valid()){
					$.ajax({
						type:"POST",
						url:"{{ route('user.opening.stock.import') }}",
						data: new FormData($('#import_form')[0]),
						processData: false,
						contentType:false,
						success: function(data){
							if(data.status==='success'){
								alert('Import successfull.')
								window.location.reload();
							}else if(data.status==='error'){
								toastr["error"](data.data, "Error");
								setTimeout(function() {
									location.reload();
								}, 5000);
							}
						}
					});
				}else{
					e.preventDefault();
				}
			});
			@if($id)
			$('#childimportfile').change(function(e) {
				if($("#childimport_form").valid()){
					$.ajax({
						type:"POST",
						url:"{{ route('user.opening.stock.childimport',$id) }}",
						data: new FormData($('#childimport_form')[0]),
						processData: false,
						contentType:false,
						success: function(data){
							if(data.status==='success'){
								alert('Import successfull.')
								window.location.reload();
							}else if(data.status==='error'){
								toastr["error"](data.data, "Error");
								setTimeout(function() {
									location.reload();
								}, 5000);
							}
						}
					});
				}else{
					e.preventDefault();
				}
			});
			@endif
		});
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
					url:'{{ route("user.opening.stock") }}',
					data: function(d) {
						d.is_archive= $('#is_archive').val()
						d.branch_id= $('#branch_id').val()
						d.employee_id = $('#employee_id').val()
					}
				},
				columns: [
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

					@endif
					{
						orderable: false,
						searchable: true,
						data: "date"
					},
					{
						orderable: false,
						searchable: true,
						data: "fy_year"
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

	</script>
	@endpush