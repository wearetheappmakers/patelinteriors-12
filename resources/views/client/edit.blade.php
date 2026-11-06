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
			<a class="nav-link active" data-toggle="tab" data-id="editclient" href="#editclient" >Client</a>
		</li>
		
		<li class="nav-item">
			<a class="nav-link" data-toggle="tab" data-id="clientdocument" href="#clientdocument" >Client Document</a>
		</li>
		<!-- <li class="nav-item">
			<a class="nav-link" data-toggle="tab" data-id="documents" href="#documents">Documents</a>
		</li> -->
	</ul>
	<hr>
	<div class="tab-content">
		@include('client.editclient')
		@include('client.clientdocument')
		
		
	</div>
</div>
  </div>
</div>
@endsection
@push('scripts')
<script type="text/javascript">
	// $(document).ready(function(){
	// 	$('.nav-link').each(function()
	// 	{
	// 		var href = $(this).attr('href');
	// 		$(this).removeClass('active');
	// 		getval= '#'+getValue();
	// 		if(href == getval)
	// 		{
	// 			$(this).addClass('active');
	// 		}
	// 	})
	// 	$('.tab-pane').each(function()
	// 	{
	// 		$(this).removeClass('active');
	// 	})
	// 	$('#'+getValue()).addClass('active');
	// })
// store the currently selected tab in the hash value
</script>
@endpush
