@extends('main')

@section('content')





<div class="kt-container  kt-container--fluid  kt-grid__item kt-grid__item--fluid">

	<br>

	<div class="kt-portlet">

		<div class="kt-portlet__head">

			<div class="kt-portlet__head-label">

				<h3 class="kt-portlet__head-title">

					{{ $title }}

				</h3>

			</div>

		</div>

		<div class="kt-portlet__body">



			<ul class="nav nav-pills" role="tablist" id="myTab">

				<li class="nav-item">

					<a class="nav-link active" data-toggle="tab" data-id="client" href="#client" >Client </a>

				</li>

				

				<!-- <li class="nav-item">

					<a class="nav-link" data-toggle="tab" data-id="assign" href="#assign" >Lead Assign</a>

				</li>

				<li class="nav-item">

					<a class="nav-link" data-toggle="tab" data-id="followup" href="#followup" >Follow UP</a>

				</li>

				<li class="nav-item">

					<a class="nav-link" data-toggle="tab" data-id="communication" href="#communication">Communication</a>

				</li> -->

				

		<!-- <li class="nav-item">

			<a class="nav-link" data-toggle="tab" data-id="documents" href="#documents">Documents</a>

		</li> -->

	</ul>

	<hr>

	<div class="tab-content">

		@include('client.client')

		

		

		

		

	</div>

</div>



</div>

</div>

@endsection









