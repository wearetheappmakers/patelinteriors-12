<style type="text/css">
	.select2
	{
		opacity: 1 !important;
		width: 100% !important;
	}
</style>
<div class="tab-pane" id="editassign" role="tabpanel">
	
		<form class="kt-form kt-form--label-right form_assign" method="POST">
			@csrf
			<div class="row">
				<input type="hidden" name="lead_id" value="{{ $data->id }}">
				@if(isset($assign))
				<div class="form-group col-lg-3">
					<label>Stages </label>
					<select name="stage_id" class="form-control select2" disabled>
						<option value="">Select Stage</option>
						@foreach($stage as $sub)
						<option value="{{$sub->id}}" @if($sub->id == $assign->stage_id) selected @endif  >{{$sub->name}}</option>
						@endforeach
					</select>
				</div>
				@else
				<div class="form-group col-lg-3">
					<label>Stage </label>
					<select name="stage_id" class="form-control select2" disabled>
						<option value="">Select Stage</option>
						@foreach($stage as $sub)
						<option value="{{$sub->id}}" >{{$sub->name}}</option>
						@endforeach
					</select>
				</div>
				@endif
			<!-- @if(isset($assign))
			<div class="form-group col-lg-3">
				<label>Section </label>
				<select name="section_id" class="form-control select2" >
					<option value="">Select Section</option>
					@foreach($section as $sub)
					<option value="{{$sub->id}}"  @if($sub->id == $assign->section_id) selected @endif >{{$sub->name}}</option>
					@endforeach
				</select>
			</div>
			@else
			<div class="form-group col-lg-3">
				<label>Section </label>
				<select name="section_id" class="form-control select2" >
					<option value="">Select Section</option>
					@foreach($section as $sub)
					<option value="{{$sub->id}}">{{$sub->name}}</option>
					@endforeach
				</select>
			</div>
			@endif -->

			@if(isset($assign))

			<div class="form-group col-lg-3">
				<label>Employee </label>
				{{-- <select name="employee_id" class="form-control select2" disabled>
					<option value="">Select Employee</option>
					@foreach($employees as $sub)
					<option value="{{$sub->id}}" @if($sub->id == $assign->employee_id) selected @endif >{{$sub->name}}</option>
					@endforeach
				</select> --}}
				@php 
				$updated_employee = explode(',',$assign->employee_id);
				@endphp
				<select class="form-control select2" name="employee_id[]" multiple disabled> 
					<option value="">Select</option>
					@foreach($employees as $emp)
					<?php
					$selected = '';
					foreach($updated_employee as $ui)
					{
						if($ui == $emp->id)
						{
							$selected = 'selected';
						}
					}
					?>
					<option value="{{ $emp->id }}" {{$selected}}>{{ $emp->name }}</option>
					@endforeach
				</select>
			</div>
			@else
			<div class="form-group col-lg-3">
				<label>Employee </label>
				<select name="employee_id[]" class="form-control select2" disabled multiple>
					<option value="">Select Employee</option>
					@foreach($employees as $sub)
					<option value="{{$sub->id}}">{{$sub->name}}</option>
					@endforeach
				</select>
			</div>
			@endif


			<div class="form-group col-lg-3">
				<label>Lead Note</label>
				<textarea  type="text" class="form-control" placeholder="Lead Note" readonly name="note">@if(isset($assign)) {{$assign->note}} @else  @endif</textarea>
			</div>

		</div>
	</form>
	<div class="kt-portlet__foot">
		<div class="kt-form__actions">
			<center>

				<a href="{{route('user.lead.index')}}"><button type="button" class="btn btn-outline-secondary">Cancel</button></a>
			</center>
		</div>
	</div>
</div>
<script type="text/javascript">
	$('.select2').select2();
	$(document).ready(function() {
		$("#submit_assign").on("click", function(e) {
			e.preventDefault();
			console.log('assign');
			if ($(".form_assign").valid()) {
				$.ajax({
					type: "POST",
					url: "{{ route('user.lead.assign') }}",
					data: new FormData($('.form_assign')[0]),
					processData: false,
					contentType: false,
					success: function(data) {
						if (data.status === 'success') {

							location.reload();

							toastr["success"]("Lead Added Successfully", "Success");
						} else if (data.status === 'error') {
							location.reload();
							toastr["error"]("Something went wrong", "Error");
						}
					},
				});
			} else {
				e.preventDefault();
			}
		});
	});
</script>
