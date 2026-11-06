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
				<select name="stage_id" class="form-control select2" required>
					<option value="">Select Stage</option>
					@foreach($stage as $sub)
					<option value="{{$sub->id}}" @if($sub->id == $assign->stage_id) selected @endif  >{{$sub->name}}</option>
					@endforeach
				</select>
			</div>
			@else
			<div class="form-group col-lg-3">
				<label>Stage </label>
				<select name="stage_id" class="form-control select2" required>
					<option value="">Select Stage</option>
					@foreach($stage as $sub)
					<option value="{{$sub->id}}" >{{$sub->name}}</option>
					@endforeach
				</select>
			</div>
			@endif
			
			@if(isset($assign))
			   
			<div class="form-group col-lg-3">
				<label>Employee </label>
				@php 
            $updated_employee = explode(',',$assign->employee_id);
            @endphp
            <select class="form-control select2" name="employee_id[]" multiple>
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
				<select name="employee_id[]" class="form-control select2" required multiple>
					<option value="">Select Employee</option>
					@foreach($employees as $sub)
					<option value="{{$sub->id}}">{{$sub->name}}</option>
					@endforeach
				</select>
			</div>
			@endif
		
		
		<div class="form-group col-lg-3">
			<label>Lead Note</label>
			<textarea  type="text" class="form-control" placeholder="Lead Note"  name="note">{{ $assign->note }}</textarea>
		</div>
		
	</div>
	</form>
	 <div class="kt-portlet__foot">
      <div class="kt-form__actions">
        <center>
          <button type="button" class="btn btn-brand submit" id="submit_assign" >Submit</button>
          <a href="{{route('user.lead.index')}}"><button type="button" class="btn btn-outline-secondary">Cancel</button></a>
        </center>
      </div>
    </div>
</div>

<script type="text/javascript">
	// $('.select2').select2();
$(document).ready(function() {
    $("#submit_assign").on("click", function(e) {
      e.preventDefault();
      console.log('assign');
      if ($(".form_assign").valid()) {
      	
      	showLoader();

        $.ajax({
          type: "POST",
          url: "{{ route('user.lead.assign') }}",
          data: new FormData($('.form_assign')[0]),
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
