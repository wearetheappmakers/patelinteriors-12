<div class="tab-pane" id="editfollowup" role="tabpanel">
	<form class="kt-form kt-form--label-right form_followup" method="POST">
		@csrf
		<input type="hidden" name="lead_id" value="{{ $data->id }}">
		@if(Auth::guard('user')->check())
		<?php $emp = App\LeadFollowUp::where('lead_id',$data->id)->get(); ?>
		@if(!empty($emp))
		@foreach($emp as $emps)
		<?php $empnm = App\Employee::where('id',$emps->employee_id)->value('name'); ?>
		<label style="color: black;">Employee:-&nbsp;&nbsp;{{$empnm}}</label>
		<div class="row">
			<input type="hidden" name="followup_id[]" value="{{ $emps->id }}">
			<div class="form-group col-lg-2">
				<label>Subject</label>
				<input type="text" class="form-control" placeholder="Subject" @if(isset($emps)) value="{{$emps->subject}}" @else value="" @endif  name="subject[]">  
			</div>
			<div class="form-group col-lg-2">
				<label>Description</label>
				<textarea  type="text" class="form-control" rows="1" placeholder="Description" name="description[]">@if(isset($emps)) {{$emps->description}}@endif</textarea>
			</div>
			<div class="form-group col-lg-2">
				<label>Schedule Date</label>
				<input type="date" class="form-control" @if(isset($emps)) value="{{$emps->date}}" @else value="{{date('Y-m-d')}}" @endif  name="date[]">  
			</div>
			<div class="form-group col-lg-2">
				<label>Schedule Time</label>
				<input type="time" class="form-control"  @if(isset($emps)) value="{{$emps->time}}" @else value="{{date('H:i:s')}}" @endif  name="time[]">  
			</div>
			@if(isset($emps))
			<input type="hidden" name="lead_id" value="{{ $data->id }}">
			<div class="form-group col-lg-2">
				<label>Followup Type </label>
				<select name="type[]" class="form-control"  required>
					<option value="">Select Followup Type</option>
					@foreach($type as $sub)
					<option value="{{$sub->id}}"  @if($sub->id == $emps->type) selected @endif >{{$sub->name}}</option>
					@endforeach
				</select>
			</div>
			@else
			<div class="form-group col-lg-2">
				<label>Followup Type </label>
				<select name="type[]" class="form-control"  required>
					<option value="">Select Followup Type</option>
					@foreach($type as $sub)
					<option value="{{$sub->id}}" >{{$sub->name}}</option>
					@endforeach
				</select>
			</div>
			@endif
			@if(isset($emps))
			<div class="form-group col-lg-2">
				<label>Followup Status</label>
				<select class="form-control" name="is_completed[]" id="is_completed" required>
					<option value="1" @if($emps->is_completed == 1) selected @endif >Pending</option>
					<option value="2" @if($emps->is_completed == 2) selected @endif >Discussion</option>
					<option value="3" @if($emps->is_completed == 3) selected @endif >Rivision</option>
					<option value="4" @if($emps->is_completed == 4) selected @endif >Win</option>
					<option value="5" @if($emps->is_completed == 5) selected @endif >Lost</option>
					<option value="6" @if($emps->is_completed == 6) selected @endif >Showroom Visit</option>
					<option value="7" @if($emps->is_completed == 7) selected @endif >Site Measurement</option>
					<option value="8" @if($emps->is_completed == 8) selected @endif >Designing</option>
					<option value="9" @if($emps->is_completed == 9) selected @endif >Quotation</option>
				</select>
			</div>
			@else
			<div class="form-group col-lg-2">
				<label>Followup Status</label>
				<select class="form-control" name="is_completed[]" id="is_completed" required>
					
					<option value="1">Pending</option>
					<option value="2">Discussion</option>
					<option value="3">Rivision</option>
					<option value="4">Win</option>
					<option value="5">Lost</option>
					<option value="6">Showroom Visit</option>
					<option value="7">Site Measurement</option>
					<option value="8">Designing</option>
					<option value="9">Quotation</option>
					
				</select>
			</div>
			
			@endif
			<div class="form-group col-lg-2">
				<label>FollowUp Date</label>
				<input type="date" class="form-control" @if(isset($emps)) value="{{$emps->status_date}}" @else value="{{date('Y-m-d')}}" @endif  name="status_date[]">  
			</div>
		</div>
		@endforeach
		@endif
		@else
		<?php $emp = App\LeadFollowUp::where('lead_id',$data->id)->where('employee_id',Auth::user()->id)->get(); ?>
		@if(!empty($emp))
		@foreach($emp as $emps)
		<?php $empnm = App\Employee::where('id',$emps->employee_id)->value('name'); ?>
		<label style="color: black;">Employee:-&nbsp;&nbsp;{{$empnm}}</label>
		<div class="row">
			<input type="hidden" name="followup_id[]" value="{{ $emps->id }}">
			<div class="form-group col-lg-2">
				<label>Subject</label>
				<input type="text" class="form-control" placeholder="Subject" @if(isset($emps)) value="{{$emps->subject}}" @else value="" @endif  name="subject[]">  
			</div>
			<div class="form-group col-lg-2">
				<label>Description</label>
				<textarea  type="text" class="form-control" rows="1" placeholder="Description" name="description[]">@if(isset($emps)) {{$emps->description}}@endif</textarea>
			</div>
			<div class="form-group col-lg-2">
				<label>Schedule Date</label>
				<input type="date" class="form-control" @if(isset($emps)) value="{{$emps->date}}" @else value="{{date('Y-m-d')}}" @endif  name="date[]">  
			</div>
			<div class="form-group col-lg-2">
				<label>Schedule Time</label>
				<input type="time" class="form-control"  @if(isset($emps)) value="{{$emps->time}}" @else value="{{date('H:i:s')}}" @endif  name="time[]">  
			</div>
			@if(isset($emps))
			<input type="hidden" name="lead_id" value="{{ $data->id }}">
			<div class="form-group col-lg-2">
				<label>Followup Type </label>
				<select name="type[]" class="form-control"  required>
					<option value="">Select Followup Type</option>
					@foreach($type as $sub)
					<option value="{{$sub->id}}"  @if($sub->id == $emps->type) selected @endif >{{$sub->name}}</option>
					@endforeach
				</select>
			</div>
			@else
			<div class="form-group col-lg-2">
				<label>Followup Type </label>
				<select name="type[]" class="form-control"  required>
					<option value="">Select Followup Type</option>
					@foreach($type as $sub)
					<option value="{{$sub->id}}" >{{$sub->name}}</option>
					@endforeach
				</select>
			</div>
			@endif
			@if(isset($emps))
			<div class="form-group col-lg-2">
				<label>Status</label>
				<select class="form-control" name="is_completed[]" id="is_completed" required>
					<option value="0">None</option>
					<option value="1" @if($emps->is_completed == 1) selected @endif>Pending</option>
					<option value="2" @if($emps->is_completed == 2) selected @endif>Discussion</option>
					<option value="3" @if($emps->is_completed == 3) selected @endif>Rivision</option>
					<option value="4" @if($emps->is_completed == 4) selected @endif>Win</option>
					<option value="5" @if($emps->is_completed == 5) selected @endif>Lost</option>
					<option value="6" @if($emps->is_completed == 6) selected @endif >Showroom Visit</option>
					<option value="7" @if($emps->is_completed == 7) selected @endif >Site Measurement</option>
					<option value="8" @if($emps->is_completed == 8) selected @endif >Designing</option>
					<option value="9" @if($emps->is_completed == 9) selected @endif >Quotation</option>
				</select>
			</div>
			@else
			<div class="form-group col-lg-2">
				<label>Status</label>
				<select class="form-control" name="is_completed[]" id="is_completed" required>
					<option value="1">Pending</option>
					<option value="2">Discussion</option>
					<option value="3">Rivision</option>
					<option value="4">Win</option>
					<option value="5">Lost</option>
					<option value="6">Showroom Visit</option>
					<option value="7">Site Measurement</option>
					<option value="8">Designing</option>
					<option value="9">Quotation</option>
				</select>
			</div>
			@endif
			<div class="form-group col-lg-2">
				<label>FollowUp Date</label>
				<input type="date" class="form-control" @if(isset($emps)) value="{{$emps->status_date}}" @else value="{{date('Y-m-d')}}" @endif  name="status_date[]">  
			</div>
		</div>
		@endforeach
		@endif
		@endif
	</form>
	<div class="kt-portlet__foot">
		<div class="kt-form__actions">
			<center>
				<button type="button" class="btn btn-brand submit" id="submit_followup">Submit</button>
				<a href="{{route('user.lead.index')}}"><button type="button" class="btn btn-outline-secondary">Cancel</button></a>
			</center>
		</div>
	</div>
</div>
<script type="text/javascript">
	$(document).ready(function() {
		$("#submit_followup").on("click", function(e) {
			e.preventDefault();
			console.log('followup');
			if ($(".form_followup").valid()) {

				showLoader();

				$.ajax({
					type: "POST",
					url: "{{ route('user.lead.followup') }}",
					data: new FormData($('.form_followup')[0]),
					processData: false,
					contentType: false,
					success: function(data) {
						
						hideLoader();

						if (data.status === 'success') {
							location.reload();
							toastr["success"]("Lead Updated Successfully", "Success");
						} else if (data.status === 'error') {
							location.reload();
							toastr["error"]("Something went wrong", "Error");
						}
					},
				});
			} else {
				hideLoader();
				e.preventDefault();
			}
		});
	});
</script>
