<div class="tab-pane" id="followup" role="tabpanel">

	<form class="kt-form kt-form--label-right form_followup" method="POST">
		@csrf

		<div class="row">
		<div class="form-group col-lg-3">
			<label>Subject</label>
			<input type="text" class="form-control" placeholder="Subject" @if(isset($followup)) value="{{$followup->subject}}" @else value="" @endif  name="subject">  
		</div>

		<div class="form-group col-lg-3">
			<label>Description</label>
			<textarea  type="text" class="form-control" placeholder="Description" name="description">@if(isset($followup)) {{$followup->description}} @else  @endif </textarea>
		</div>

		<div class="form-group col-lg-3">
			<label>Schedule Date</label>
			<input type="date" class="form-control" @if(isset($followup)) value="{{$followup->date}}" @else value="" @endif  name="date">  
		</div>

		<div class="form-group col-lg-3">
			<label>Schedule Time</label>
			<input type="time" class="form-control" @if(isset($followup)) value="{{$followup->time}}" @else value="" @endif  name="time">  
		</div>

		

		@if(isset($followup))
			<input type="hidden" name="lead_id" value="{{ $data->id }}">
			<div class="form-group col-lg-3">
			<label>Type </label>
			<select name="type" class="form-control select2"  required>
				<option value="">Select Type</option>
				@foreach($type as $sub)
				<option value="{{$sub->id}}"  @if($sub->id == $followup->type) selected @endif >{{$sub->name}}</option>
				@endforeach
			</select>
		</div>
		@else
		<div class="form-group col-lg-3">
			<label>Type </label>
			<select name="type" class="form-control select2"  required>
				<option value="">Select Type</option>
				@foreach($type as $sub)
				<option value="{{$sub->id}}" >{{$sub->name}}</option>
				@endforeach
			</select>
		</div>
		@endif

		@if(isset($followup))
		
		
		<div class="form-group col-lg-3">
			<label>Status</label>
			<select class="form-control" name="is_completed" id="is_completed" required>
				<option value="">None</option>
				<option value="1" @if($followup->is_completed == 1) selected @endif>Pending</option>
				<option value="2" @if($followup->is_completed == 2) selected @endif>Completed</option>
				
				
			</select>
		</div>
		@else
		
		
		<div class="form-group col-lg-3">
			<label>Status</label>
			<select class="form-control" name="is_completed" id="is_completed" required>
				<option value="">None</option>
				<option value="1">Pending</option>
				<option value="2">Complated</option>
				
				
			</select>
		</div>
		@endif

		

	</div>


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
        $.ajax({
          type: "POST",
          url: "{{ route('user.lead.followup') }}",
          data: new FormData($('.form_followup')[0]),
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
