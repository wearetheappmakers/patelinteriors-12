<div class="tab-pane" id="delivery" role="tabpanel">

	<form class="kt-form kt-form--label-right form_delivery" method="POST">

	@csrf
		<div class="row">
			<!-- <input type="hidden" name="client_id" value="{{ $data->id }}"> -->
      <input type="hidden" name="quotation_id" value="{{$data->id}}">


			
      
			<div class="form-group col-lg-3">
				<label>Client Name </label>
				<input type="text" name="client_id"  @if(isset($delivery)) value="{{$delivery->client_id}}" @else value="" @endif   class="form-control" >
			</div>
     

     

		

		<div class="form-group col-lg-3">
			<label>Delivery Date</label>
			<input type="date" class="form-control"  @if(isset($delivery)) value="{{$delivery->date}}" @else value="" @endif name="date" required>  
		</div>

   

    


      <div class="form-group col-lg-3">
      <label>Delivery Note</label>
      <textarea type="text" class="form-control" name="note"> @if(isset($delivery)) {{$delivery->note}} @else  @endif</textarea> 
    </div>

    

	</div>


	</form>
	 <div class="kt-portlet__foot">
      <div class="kt-form__actions">
        <center>
          <button type="button" class="btn btn-brand submit" id="submit_delivery" >Submit</button>
          <a href="{{ route('user.quotations.index') }}"><button type="button" class="btn btn-outline-secondary">Cancel</button></a>
        </center>
      </div>
    </div>
</div>

<script type="text/javascript">
$(document).ready(function() {
    $("#submit_delivery").on("click", function(e) {
      e.preventDefault();
      console.log('client');
      if ($(".form_delivery").valid()) {
        $.ajax({
          type: "POST",
          url: "{{route('user.quotations.delivery')}}",
          data: new FormData($('.form_delivery')[0]),
          processData: false,
          contentType: false,
          success: function(data) {
            if (data.status === 'success') {
            	
            	 window.location="{{ route('user.quotations.index') }}";
                           

              toastr["success"]("Delivery Added Successfully", "Success");
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

