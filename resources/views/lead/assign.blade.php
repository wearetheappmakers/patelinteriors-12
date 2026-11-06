<style type="text/css">
	.select2
	{
		opacity: 1 !important;
		width: 100% !important;
	}
</style>
<div class="tab-pane" id="assign" role="tabpanel">

	<form class="kt-form kt-form--label-right form_assign" method="POST">

		@csrf
		<div class="row">
			<div class="form-group col-sm-3">
				@if(isset($assign))
				<input type="hidden" name="lead_id" value="{{ $data->id }}">
				<label>Stage </label><br>
				<select name="stage_id" class="form-control select2" required>
					<option value="">Select Stage</option>
					@foreach($stage as $sub)
					<option value="{{$sub->id}}" @if($sub->id == $assign->stage_id) selected @endif  >{{$sub->name}}</option>
					@endforeach
				</select>
				@else
				<label>Stage </label><br>
				<select name="stage_id" class="form-control select2" required>
					<option value="">Select Stage</option>
					@foreach($stage as $sub)
					<option value="{{$sub->id}}" >{{$sub->name}}</option>
					@endforeach
				</select>
				@endif
			</div>

			<div class="form-group col-sm-3">
				@if(isset($assign))
				<label>Section </label><br>
				<select name="section_id" class="form-control select2" >
					<option value="">Select Section</option>
					@foreach($section as $sub)
					<option value="{{$sub->id}}"  @if($sub->id == $assign->section_id) selected @endif >{{$sub->name}}</option>
					@endforeach
				</select>
				@else
				<label>Section </label><br>
				<select name="section_id" class="form-control select2" >
					<option value="">Select Section</option>
					@foreach($section as $sub)
					<option value="{{$sub->id}}">{{$sub->name}}</option>
					@endforeach
				</select>
				@endif
			</div>

			<div class="form-group col-sm-3">
				@if(isset($assign))
				<label>Employee </label><br>
				<select name="employee_id" class="form-control select2" required>
					<option value="">Select Employee</option>
					@foreach($employees as $sub)
					<option value="{{$sub->id}}" @if($sub->id == $assign->employee_id) selected @endif >{{$sub->name}}</option>
					@endforeach
				</select>
				@else
				<label>Employee </label><br>
				<select name="employee_id" class="form-control select2" required>
					<option value="">Select Employee</option>
					@foreach($employees as $sub)
					<option value="{{$sub->id}}">{{$sub->name}}</option>
					@endforeach
				</select>
				@endif
			</div>





			<div class="form-group col-sm-3">
				<label>Lead Note</label><br>
				<textarea  type="text" class="form-control" placeholder="Lead Note"  name="note">@if(isset($assign)) {{$assign->note}} @else  @endif</textarea>
			</div>

			

		</div>


	</form>
	<div class="kt-portlet__foot">
		<div class="kt-form__actions">
			<center>
				<button type="button" class="btn btn-brand submit" id="submit_assign" >Submit</button>
				<a href="{{route('user.lead.index')}}" class="btn btn-outline-secondary">Cancel</a>
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
					url: "{{ route('lead.assign') }}",
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

