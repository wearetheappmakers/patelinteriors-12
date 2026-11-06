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

    <form class="kt-form" class="addform" id="addform">

      @csrf

      <?php

      if($resourcePath === 'payment')

      {

        $index = route($resourcePath.'.index',$redirect_id);

      }

      else

      {

        $index = route('user.'.$resourcePath.'.index');

      }

      $store = route('user.'.$resourcePath.'.store');

      ?>

      @include($resourcePath.'.create')

    </form>

    @if(!($resourcePath === 'lead'))

    <div class="kt-portlet__foot">

      <div class="kt-form__actions">

        <center>

          <button type="submit" class="btn btn-brand submit" id="submit">Submit</button>

          <a href="{{ $index }}"><button type="button" class="btn btn-outline-secondary">Cancel</button></a>

        </center>

      </div>

    </div>

    @endif

  </div>

</div>

@endsection



@push('scripts')

<script type="text/javascript">



  $(document).ready(function(){

    $("#submit").on("click",function(e){

      e.preventDefault();

      @if($resourcePath === "quotationfooter")



      for ( instance in CKEDITOR.instances ) {

        CKEDITOR.instances.template.updateElement();

      }

      @endif



      @if($resourcePath === "template")



      for ( instance in CKEDITOR.instances ) {

        CKEDITOR.instances.template.updateElement();

      }

      @endif



      @if($resourcePath === "systemautoresponder")



      for ( instance in CKEDITOR.instances ) {

        CKEDITOR.instances.message.updateElement();

      }

      @endif

      @if($resourcePath === "autoresponder")



      for ( instance in CKEDITOR.instances ) {

        CKEDITOR.instances.message.updateElement();

      }

      @endif

      @if($resourcePath === "property")

      for ( instance in CKEDITOR.instances ) {

        CKEDITOR.instances.description.updateElement();

      }

      @endif

      @if($resourcePath === "task")

      for ( instance in CKEDITOR.instances ) {

        CKEDITOR.instances.description.updateElement();

      }

      @endif
      
      @if($resourcePath === "company_policy")

      for (instance in CKEDITOR.instances) {
          CKEDITOR.instances[instance].updateElement();
      }

      @endif

      if($("#addform").valid()){

        $('.loader-wrapper').show();
        $('.loader').show();

        $.ajax({

          type:"POST",

          url:"{{ $store }}",

          data: new FormData($('#addform')[0]),

          processData: false,

          contentType:false,



          success: function(data){

            $('.loader').hide();
            $('.loader-wrapper').hide();

            if(data.status==='success'){

              toastr["success"]("{{$module}} Added Successfully.", "Success");

              window.location="{{ $index }}";

            }else if(data.status==='error'){

              toastr["error"]("Unsuccessfull", "Error");

              location.reload();



            }

            else if(data.status==='password'){

              toastr["error"]("Password Not Match", "Error");

              location.reload();

            }
            else if (data.status === 'email_exists') {
              toastr["error"]("Duplicate Email !!!", "Error");
              $('.change_button').prop('disabled', false);
              $('.change_button').find('.change_spin').addClass('d-none');
            }
            else if (data.status === 'required') {
              toastr["error"](data.message, "Error");
              $('.change_button').prop('disabled', false);
              $('.change_button').find('.change_spin').addClass('d-none');
            }

          }

        });

      }else{

        $('.loader').hide();
        $('.loader-wrapper').hide();
        e.preventDefault();

      }

    });

  });

</script>

@endpush