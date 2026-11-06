<style type="text/css">
  .select2
  {
    opacity: 1 !important;
    width: 100% !important;
  }
</style>
<div class="tab-pane" id="clientdocument" role="tabpanel">
  <form class="kt-form kt-form--label-right form_clientdocument" id="form_clientdocument" >
    @csrf
    <div class="row">
      <input type="hidden" name="client_id" value="{{ $data->id }}">
      <div class="form-group col-lg-3">
        <label>Client Name </label>
        <input type="text" value="{{$data->member}}" class="form-control" readonly>
      </div>
      <div class="form-group col-lg-3">
       <label> Date</label>
       <input type="date" class="form-control"  @if(isset($clientdocument)) value="{{$clientdocument->date}}" @else value="{{date('Y-m-d')}}" @endif name="date"readonly>  
     </div>
     {{-- <div class="form-group col-lg-3">
      <label>Tracking Number</label>
      <input type="text" class="form-control"  @if(isset($clientdocument)) value="{{$clientdocument->tracking_no}}" @else value="" @endif name="tracking_no" placeholder="Tracking Number" readonly>  
    </div>
    <div class="form-group col-lg-3">
      <label>Tracking Link</label>
      <input type="text" class="form-control"  @if(isset($clientdocument)) value="{{$clientdocument->tracking_link}}" @else value="" @endif name="tracking_link" placeholder="Tracking Link" readonly>  
    </div>
    <div class="form-group col-lg-3">
      <label>Distributor ID</label>
      <input type="text" class="form-control"  @if(isset($clientdocument)) value="{{$clientdocument->distributor_id}}" @else value="" @endif name="distributor_id" placeholder="Distributor ID" readonly>  
    </div>
    @if(isset($clientdocument))
    <div class="form-group col-lg-3">
      <label>Status</label>
      <select class="form-control select2" name="status" id="status" disabled>
        <option value="1" @if($clientdocument->status == 1) selected @endif>Pending</option>
        <option value="2" @if($clientdocument->status == 2) selected @endif>AMC</option>
        <option value="3" @if($clientdocument->status == 3) selected @endif>Cancelled</option>
      </select>
    </div>
    @else
    <div class="form-group col-lg-3">
      <label>Status</label>
      <select class="form-control select2" name="status" id="status" disabled>
        <option value="1">Pending</option>
        <option value="2">AMC</option>
        <option value="3">Cancelled</option>
      </select>
    </div>
    @endif --}}
    <div class="form-group col-lg-3">
      <label>Remark</label>
      <textarea type="text" class="form-control" readonly name="remark"> @if(isset($clientdocument)) {{$clientdocument->remark}} @else  @endif</textarea > 
    </div>
    @if(isset($clientdocument))
    <div class="form-group  col-lg-3">
      <div class="kt-checkbox-inline">
        <label class="kt-checkbox">
          <input type="checkbox" name="aadhar_card" value="1" disabled @if($clientdocument->aadhar_card == 1) checked @endif> Aadhar Card
          <span></span>
        </label>
      </div>
      @if(!empty($clientdocument->aadhar_img))
      <a href="{{ asset('/aadharimage/'.$clientdocument->aadhar_img) }}" target="_blank">
        <img src="{{ asset('/aadharimage/'.$clientdocument->aadhar_img) }}" height="50px" width="50px">
      </a>
      @endif
    </div>
    @else
    <div class="form-group  col-lg-3">
      <div class="kt-checkbox-inline">
        <label class="kt-checkbox">
          <input type="checkbox" name="aadhar_card" value="1" disabled> Aadhar Card
          <span></span>
        </label>
      </div>
      @if(!empty($clientdocument->aadhar_img))
      <a href="{{ asset('/aadharimage/'.$clientdocument->aadhar_img) }}" target="_blank">
        <img src="{{ asset('/aadharimage/'.$clientdocument->aadhar_img) }}" height="50px" width="50px">
      </a>
      @endif
    </div>
    @endif
    @if(isset($clientdocument))
    <div class="form-group  col-lg-3">
      <div class="kt-checkbox-inline">
        <label class="kt-checkbox">
          <input type="checkbox" name="pan_card" disabled value="1" @if($clientdocument->pan_card == 1) checked @endif > Pan Card
          <span></span>
        </label>
      </div>
      @if(!empty($clientdocument->pan_img))
      <a href="{{ asset('/panimage/'.$clientdocument->pan_img) }}" target="_blank">
        <img src="{{ asset('/panimage/'.$clientdocument->pan_img) }}" height="50px" width="50px">
      </a>
      @endif
    </div>
    @else
    <div class="form-group  col-lg-3">
      <div class="kt-checkbox-inline">
        <label class="kt-checkbox">
          <input type="checkbox" name="pan_card" disabled value="1"> Pan Card
          <span></span>
        </label>
      </div>
      @if(!empty($clientdocument->pan_img))
      <a href="{{ asset('/panimage/'.$clientdocument->pan_img) }}" target="_blank">
        <img src="{{ asset('/panimage/'.$clientdocument->pan_img) }}" height="50px" width="50px">
      </a>
      @endif
    </div>
    @endif
    {{-- @if(isset($clientdocument))
    <div class="form-group  col-lg-3">
      <div class="kt-checkbox-inline">
        <label class="kt-checkbox">
          <input type="checkbox" name="cancelled_cheque" disabled value="1" @if($clientdocument->cancelled_cheque == 1) checked @endif > Cancelled Cheque
          <span></span>
        </label>
      </div>
      @if(!empty($clientdocument->cancelledcheque_img))
      <a href="{{ asset('/cancelledchequeimage/'.$clientdocument->cancelledcheque_img) }}" target="_blank">
        <img src="{{ asset('/cancelledchequeimage/'.$clientdocument->cancelledcheque_img) }}" height="50px" width="50px">
      </a>
      @endif
    </div>
    @else
    <div class="form-group  col-lg-3">
      <div class="kt-checkbox-inline">
        <label class="kt-checkbox">
          <input type="checkbox" name="cancelled_cheque" disabled value="1"> Cancelled Cheque
          <span></span>
        </label>
      </div>
      @if(!empty($clientdocument->cancelledcheque_img))
      <a href="{{ asset('/cancelledchequeimage/'.$clientdocument->cancelledcheque_img) }}" target="_blank">
        <img src="{{ asset('/cancelledchequeimage/'.$clientdocument->cancelledcheque_img) }}" height="50px" width="50px">
      </a>
      @endif
    </div>
    @endif
    @if(isset($clientdocument))
    <div class="form-group  col-lg-3">
      <div class="kt-checkbox-inline">
        <label class="kt-checkbox">
          <input type="checkbox" name="rtgs_details" value="1" disabled @if($clientdocument->rtgs_details == 1) checked @endif> RTGS Detail
          <span></span>
        </label>
      </div>
      @if(!empty($clientdocument->rtgs_img))
      <a href="{{ asset('/rtgsimage/'.$clientdocument->rtgs_img) }}" target="_blank">
        <img src="{{ asset('/rtgsimage/'.$clientdocument->rtgs_img) }}" height="50px" width="50px">
      </a>
      @endif
    </div>
    @else
    <div class="form-group  col-lg-3">
      <div class="kt-checkbox-inline">
        <label class="kt-checkbox">
          <input type="checkbox" name="rtgs_details" disabled value="1"> RTGS Detail
          <span></span>
        </label>
      </div>
      @if(!empty($clientdocument->rtgs_img))
      <a href="{{ asset('/rtgsimage/'.$clientdocument->rtgs_img) }}" target="_blank">
        <img src="{{ asset('/rtgsimage/'.$clientdocument->rtgs_img) }}" height="50px" width="50px">
      </a>
      @endif
    </div>
    @endif
    @if(isset($clientdocument))
    <div class="form-group  col-lg-3">
      <div class="kt-checkbox-inline">
        <label class="kt-checkbox">
          <input type="checkbox" name="form_submission" disabled value="1" @if($clientdocument->form_submission == 1) checked @endif> Form Submission
          <span></span>
        </label>
      </div>
      @if(!empty($clientdocument->form_submission_img))
      <a href="{{ asset('/formsubmissionimage/'.$clientdocument->form_submission_img) }}" target="_blank">
        <img src="{{ asset('/formsubmissionimage/'.$clientdocument->form_submission_img) }}" height="50px" width="50px">
      </a>
      @endif
    </div>
    @else
    <div class="form-group  col-lg-3">
      <div class="kt-checkbox-inline">
        <label class="kt-checkbox">
          <input type="checkbox" name="form_submission" disabled value="1"> Form Submission
          <span></span>
        </label>
      </div>
      @if(!empty($clientdocument->form_submission_img))
      <a href="{{ asset('/formsubmissionimage/'.$clientdocument->form_submission_img) }}" target="_blank">
        <img src="{{ asset('/formsubmissionimage/'.$clientdocument->form_submission_img) }}" height="50px" width="50px">
      </a>
      @endif
    </div>
    @endif
    @if(isset($clientdocument))
    <div class="form-group  col-lg-3">
      <div class="kt-checkbox-inline">
        <label class="kt-checkbox">
          <input type="checkbox" name="machine_delivered" disabled value="1" @if($clientdocument->machine_delivered == 1) checked @endif> Machine Delivered
          <span></span>
        </label>
      </div>
      @if(!empty($clientdocument->machine_delivered_img))
      <a href="{{ asset('/machinedeliveredimage/'.$clientdocument->machine_delivered_img) }}" target="_blank">
        <img src="{{ asset('/machinedeliveredimage/'.$clientdocument->machine_delivered_img) }}" height="50px" width="50px">
      </a>
      @endif
    </div>
    @else
    <div class="form-group  col-lg-3">
      <div class="kt-checkbox-inline">
        <label class="kt-checkbox">
          <input type="checkbox" name="machine_delivered" disabled value="1"> Machine Delivered
          <span></span>
        </label>
      </div>
      @if(!empty($clientdocument->machine_delivered_img))
      <a href="{{ asset('/machinedeliveredimage/'.$clientdocument->machine_delivered_img) }}" target="_blank">
        <img src="{{ asset('/machinedeliveredimage/'.$clientdocument->machine_delivered_img) }}" height="50px" width="50px">
      </a>
      @endif
    </div>
    @endif --}}
    @if(isset($clientdocument))
    <div class="form-group  col-lg-3">
      <div class="kt-checkbox-inline">
        <label class="kt-checkbox">
          <input type="checkbox" name="gst" value="1" disabled @if($clientdocument->gst == 1) checked @endif> GST
          <span></span>
        </label>
      </div>
      @if(!empty($clientdocument->gst_img))
      <a href="{{ asset('/gstimage/'.$clientdocument->gst_img) }}" target="_blank">
        <img src="{{ asset('/gstimage/'.$clientdocument->gst_img) }}" height="50px" width="50px">
      </a>
      @endif
    </div>
    @else
    <div class="form-group  col-lg-3">
      <div class="kt-checkbox-inline">
        <label class="kt-checkbox">
          <input type="checkbox" name="gst" disabled value="1"> GST
          <span></span>
        </label>
      </div>
      @if(!empty($clientdocument->gst_img))
      <a href="{{ asset('/gstimage/'.$clientdocument->gst_img) }}" target="_blank">
        <img src="{{ asset('/gstimage/'.$clientdocument->gst_img) }}" height="50px" width="50px">
      </a>
      @endif
    </div>
    @endif
  </div>
</form>
<div class="kt-portlet__foot">
  <div class="kt-form__actions">
    
  </div>
</div>
</div>
<script type="text/javascript">
  $(document).ready(function() {
    $("#submit_clientdocument").on("click", function(e) {
      e.preventDefault();
      console.log('client');
      if ($(".form_clientdocument").valid()) {
        $.ajax({
          type: "POST",
          url: "{{ route('user.client.clientdocument') }}",
          data: new FormData($('.form_clientdocument')[0]),
          processData: false,
          contentType: false,
          success: function(data) {
            if (data.status === 'success') {
              window.location="{{ route('user.client.index') }}";
              toastr["success"]("Client Document Added Successfully", "Success");
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
  $('.select2').select2({
  });
</script>
