@extends('main')
@section('content')
<?php
if($resourcePath === 'lead')
{
	if(Auth::guard('user')->check())
	{
		if(Auth::user()->role == 2)
		{
			$check = App\Lead::where('id',$data->id)->first();
			if(isset($check) && !(Auth::user()->id == $check->branch_id))
			{
				dd('Access Denied');
			}
		}
	}else{
		$check = App\LeadAssign::where('lead_id',$data->id)->first();
		$exp = array();
		$path = explode(",", $check->employee_id);
		if(!in_array(Auth::user()->id,$path))
		{
			dd('Access Denied');
		}
	}
}
?>
<div class="kt-container  kt-container--fluid  kt-grid__item kt-grid__item--fluid">
	<br>
	<div class="kt-portlet">
		<div class="kt-portlet__head">
			<div class="kt-portlet__head-label">
				<h3 class="kt-portlet__head-title">
					{{ $title }}
				</h3>
			</div>
			<div class="kt-portlet__head-toolbar">
				<div class="kt-portlet__head-wrapper">
					<div class="kt-portlet__head-actions">					
						<a href="{{URL::previous()}}" class="btn btn-brand btn-elevate btn-icon-sm">
							<i class="flaticon2-fast-back"></i>
							Back
						</a>
					</div>
				</div>
			</div>
		</div>
		<div class="kt-portlet__body">
			<ul class="nav nav-pills" role="tablist" id="myTab">
				<li class="nav-item">
					<a class="nav-link active" data-toggle="tab" data-id="editbasic" href="#editbasic" >Basic</a>
				</li>
				<li class="nav-item">
					<a class="nav-link" data-toggle="tab" data-id="editassign" href="#editassign" >Lead Assign</a>
				</li>
				<li class="nav-item">
					<a class="nav-link" data-toggle="tab" data-id="editfollowup" href="#editfollowup" >Follow UP</a>
				</li>
				<li class="nav-item">
					<a class="nav-link" data-toggle="tab" data-id="edittoshow" href="#edittoshow" >To Show</a>
				</li>
		<!-- <li class="nav-item">
			<a class="nav-link" data-toggle="tab" data-id="editcommunication" href="#editcommunication">Communication</a>
		</li> -->
		
		<!-- <li class="nav-item">
			<a class="nav-link" data-toggle="tab" data-id="documents" href="#documents">Documents</a>
		</li> -->
	</ul>
	<hr>
	<div class="tab-content">
		@include('lead.showbasic')
		@include('lead.showassign')
		@include('lead.showfollowup')
		@include('lead.showtoshow')
		<!-- @include('lead.editcommunication') -->
		
		
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