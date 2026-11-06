<div class="tab-pane" id="communication" role="tabpanel">

	<form class="kt-form kt-form--label-right form_communication"  method="POST">
		@csrf

		<div class="row">

			@if(isset($communication))
			<input type="hidden" name="lead_id" value="{{ $data->id }}">
			<div class="form-group col-lg-3">
				<label>Type</label>
				<select class="form-control" name="type" id="type" >
					<option value="">Select</option>
					<option value="1" @if($communication->type == 1) selected @endif >Email</option>

				</select>
			</div>
			@else
			<div class="form-group col-lg-3">
				<label>Type</label>
				<select class="form-control" name="type" id="type" >
					<option value="">Select</option>
					<option value="1">Email</option>

				</select>
			</div>
			@endif
		

		<div class="form-group col-lg-3">
			<label>Email</label>
			<input type="email" class="form-control" placeholder="Enter Email"  @if(isset($communication)) value="{{$communication->email}}" @else value="" @endif name="email">  
		</div>

		
		<div class="form-group col-lg-3">
			<label>Subject</label>
			<input type="text" class="form-control" placeholder="Enter Subject"  @if(isset($communication)) value="{{$communication->subject}}" @else value="" @endif name="subject">  
		</div>

		<div class="form-group">
			<label><strong>Message</strong></label>
			<textarea class="message form-control" name="message"  id="message"> @if(isset($communication)) {{$communication->message}} @else  @endif</textarea>
		</div>
		<script type="text/javascript">
			CKEDITOR.replace('message', {
				filebrowserUploadMethod: 'form'
			});
		</script>

	</div>


	</form>

	 <div class="kt-portlet__foot">
      <div class="kt-form__actions">
        <center>
          <button type="button" class="btn btn-brand submit" id="submit_communication" >Submit</button>
          <a href="{{route('user.lead.index')}}"><button type="button" class="btn btn-outline-secondary">Cancel</button></a>
        </center>
      </div>
    </div>
</div>


<script src="//cdn.ckeditor.com/4.14.0/standard/ckeditor.js"></script>
<script type="text/javascript">
	$(document).ready(function() {
		$('.message').ckeditor();
	});

	$(".submit").click(function (event) {
		event.preventDefault();
		for ( instance in CKEDITOR.instances ) {
			CKEDITOR.instances.message.updateElement();
		}  
	})
</script>


<script type="text/javascript">
$(document).ready(function() {
    $("#submit_communication").on("click", function(e) {
      e.preventDefault();
      console.log('djn');
      if ($(".form_communication").valid()) {
        $.ajax({
          type: "POST",
          url: "{{ route('user.lead.communication') }}",
          data: new FormData($('.form_communication')[0]),
          processData: false,
          contentType: false,
          success: function(data) {
            if (data.status === 'success') {
            	
            	 window.location="{{ route('user.lead.index') }}";
                           

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
