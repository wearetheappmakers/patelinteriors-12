@extends('main')
@section('content')

<div class="kt-container  kt-container--fluid  kt-grid_item kt-grid_item--fluid">
	<br>
	<div class="kt-portlet kt-portlet--mobile">
		<div class="kt-portlet__head">
			<div class="kt-portlet__head-label">
				<h3 class="kt-portlet__head-title">
					Quotation Report
				</h3>
			</div>
			<!-- <div class="kt-portlet__head-toolbar">
				<div class="kt-portlet__head-wrapper">
					<div class="kt-portlet__head-actions">
						<a href="{{ route('report.leadreport') }}" class="btn btn-brand btn-elevate btn-icon-sm"><i class="la la-plus"></i>Add Lead</a>
					</div>
				</div>
			</div> -->
		</div>
		<div class="kt-portlet__body">

			<div class="row">
				

				

				

				<div class="form-group col-lg-2">
					<label>Client </label>
					<select name="client_id" class="form-control changes" id="client_id">
						<option value="">Select Source</option>
						@foreach($client as $sub)
						<option value="{{$sub->id}}">{{$sub->member}}</option>
						@endforeach
					</select>
				</div>
			</div>
			<div class="row">
				<div class="form-group col-lg-6">
				<label>Total&nbsp;&nbsp;:</label> {{$count}} &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
				
				<label>Total Amount&nbsp;&nbsp;:</label> {{$amc_amount}}   

					
				</div>
			</div>
			
			<div id="kt_table_1_wrapper" class="dataTables_wrapper dt-bootstrap4">
				<table class="table table-striped table-bordered table-hover table-checkable datatable" id="datatable_rows">
					@csrf
					<thead>
						<tr>
							<th>SrNo</th>
							<th>Client Name</th>
							<th>Mobile</th>
							<th>Email</th>
							<th>Date</th>
							<th>Amount</th>
							<th>Status</th>
							
						</tr>
					</thead>
					<tbody>
					</tbody>
				</table>
			</div>
		</div>
		@include('layouts.multiple_action', array(
		'table_name' => 'quotation',
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
				url:'{{ route("user.report.quotationreport") }}',
				data: function(d) {
					
					d.client_id= $('#client_id').val()
					
				}
				
			},

			

		

			columns: [
			{
				orderable: false,
				searchable: true,
				data: "id"
			},
			{
				orderable: false,
				searchable: true,
				data: "client_id"
			},
			
			{
				orderable: false,
				searchable: true,
				data: "product_id"
			},

			{
				orderable: false,
				searchable: true,
				data: "sales_date"
			},

			{
				orderable: false,
				searchable: true,
				data: "sales_amount"
			},
			

			{
				orderable: false,
				searchable: true,
				data: "amc_date"
			},
			

			{
				orderable: false,
				searchable: true,
				data: "amc_amount"
			},
			

			
			
			
			]
		});

		$('.changes').change(function(){
			
			oTable.draw();
			
		});

	});

</script>



@endpush