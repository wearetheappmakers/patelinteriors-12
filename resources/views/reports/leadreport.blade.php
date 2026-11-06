@extends('main')

@section('content')



<div class="kt-container  kt-container--fluid  kt-grid_item kt-grid_item--fluid">

	<br>

	<div class="kt-portlet kt-portlet--mobile">

		<div class="kt-portlet__head">

			<div class="kt-portlet__head-label">

				<h3 class="kt-portlet__head-title">

					Lead Report

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

					<label>Product </label>

					<select name="product_id" class="form-control changes" id="product_id">

						<option value="">Select Product</option>

						@foreach($product as $sub)

						<option value="{{$sub->id}}">{{$sub->product}}</option>

						@endforeach

					</select>

				</div>



				<div class="form-group col-lg-2">

					<label>Stage </label>

					<select name="stage_id" class="form-control changes" id="stage_id">

						<option value="">Select Stage</option>

						@foreach($stage as $sub)

						<option value="{{$sub->id}}">{{$sub->name}}</option>

						@endforeach

					</select>

				</div>



				<div class="form-group col-lg-2">

					<label>Employee </label>

					<select name="employee_id" class="form-control changes" id="employee_id">

						<option value="">Select Employee</option>

						@foreach($employee as $sub)

						<option value="{{$sub->id}}">{{$sub->name}}</option>

						@endforeach

					</select>

				</div>



				<div class="form-group col-lg-2">

					<label>Source </label>

					<select name="source_id" class="form-control changes" id="source_id">

						<option value="">Select Source</option>

						@foreach($source as $sub)

						<option value="{{$sub->id}}">{{$sub->name}}</option>

						@endforeach

					</select>

				</div>

			</div>

			<div class="row">

				<div class="form-group col-lg-2">

				<label>Total:</label>       {{$count}}



					

				</div>

			</div>

			<div id="kt_table_1_wrapper" class="dataTables_wrapper dt-bootstrap4">

				<table class="table table-striped table-bordered table-hover table-checkable datatable" id="datatable_rows">

					@csrf

					<thead>

						<tr>

							<th>SrNo</th>

							<th>Client Name</th>

							<th>Phone</th>

							<th>Source</th>

							<th>Title</th>

							<th>Description</th>

							<th>Stage</th>

							<th>Assign To</th>

							<th>Next Followup Date</th>

							<th>Product</th>

							<th>No. Of Machines</th>

							<th>Lead Value</th>

							<th>Section</th>

							<th> Date</th>

							<th>Current Project Phase</th>

							<th>Subject</th>

							<th>Followup Description</th>

							<th>Schedule Date</th>

							<th>Schedule Time</th>

							<th>Followup Type</th>

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



			dom: "Blfrtip",

			 buttons : [{

            extend : 'pdf',

            text : 'Export to PDF',

            title : 'Patel',

            className: "btn btn-primary export",

            exportOptions : {

                columns: [0,1,2,3,4,5,6]

            }

        	}],

			order:[[0, "desc"]],



			ajax: {

				url:'{{ route("user.report.leadreport") }}',

				data: function(d) {

					d.product_id= $('#product_id').val()

					d.employee_id= $('#employee_id').val()

					d.stage_id= $('#stage_id').val()

					d.source_id= $('#source_id').val()

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

				data: 'client.mobile_no'

			},

			{

				orderable: false,

				searchable: true,

				data: 'source_id'

			},

			{

				orderable: false,

				searchable: true,

				data: "lead_name"

			},

			{

				orderable: false,

				searchable: true,

				data: "description"

			},

			{

				orderable: false,

				searchable: true,

				data: "lead_assign.stage.name"

			},

			{

				orderable: false,

				searchable: true,

				data: "lead_assign.employee.name"

			},

			{

				orderable: false,

				searchable: true,

				data: "lead_assign.date"

			},



			{

				orderable: false,

				searchable: true,

				data: "product_id"

			},



			{

				orderable: false,

				searchable: true,

				data: "no_of_machines"

			},

			{

				orderable: false,

				searchable: true,

				data: "lead_value"

			},

			{

				orderable: false,

				searchable: true,

				data: "lead_assign.section.name"

			},

			{

				orderable: false,

				searchable: true,

				data: "date"

			},



			{

				orderable: false,

				searchable: true,

				data: "current_project_phase"

			},



			{

				orderable: false,

				searchable: true,

				data: "lead_followup.subject"

			},



			{

				orderable: false,

				searchable: true,

				data: "lead_followup.description"

			},



			{

				orderable: false,

				searchable: true,

				data: "lead_followup.date"

			},

			{

				orderable: false,

				searchable: true,

				data: "lead_followup.time"

			},

			{

				orderable: false,

				searchable: true,

				data: "lead_followup.type"

			},





			

			

			

			]

		});



		$('.changes').change(function(){

			

			oTable.draw();

			

		});



	});



</script>







@endpush