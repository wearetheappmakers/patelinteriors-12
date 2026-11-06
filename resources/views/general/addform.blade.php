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

    <form class="kt-form" class="add_form" id="add_form">

      @csrf

      <?php

      if($resourcePath === 'payment')

      {

        $index = route($resourcePath.'.index',$redirect_id);

      }

      else

      {

        $index = route($resourcePath.'.index');

      }

      $store = route($resourcePath.'.store');

      ?>

      @include($resourcePath.'.create')

    </form>



  </div>

</div>

@endsection



@push('scripts')

<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.5/jquery.validate.min.js"></script>

<script type="text/javascript">



  $(document).ready(function(){

    $(".submit").on("click",function(e){



      var form_value = $(this).data('value');

      if (form_value === 'basic') {

        var form = $('.add_form')[0]; 

      }

      else if(form_value === 'assign')

      {

        var form = $('.add_form')[1];

      }

      else if(form_value === 'followup')

      {

        var form = $('.add_form')[2];

      }

      else if(form_value === 'communication')

      {

        var form = $('.add_form')[3];

      }

      e.preventDefault();

      if($(form).valid()){





        if (form_value === 'basic') {



          var fd = new FormData($('.add_form')[0]);    

        }

        else if(form_value === 'assign')

        {

          var fd = new FormData($('.add_form')[1]);

        }

        else if(form_value === 'followup')

        {

          var fd = new FormData($('.add_form')[2]);

        }

        else 

        {

          var fd = new FormData($('.add_form')[3]);

        }

        fd.append('form_value', form_value );

        $.ajax({

          type:"POST",

          url:"{{ route('lead.store') }}",

          data: fd,

          processData: false,

          contentType:false,



          success: function(data){

            if(data.status==='success'){

              toastr["success"]("{{$module}} Added Successfully.", "Success");

              window.location="{{ $index }}";

            }else if(data.status==='error'){

              toastr["error"]("Unsuccessfull", "Error");

              location.reload();

            }
            else if (data.status === 'required') {
              toastr["error"](data.message, "Error");
              $('.change_button').prop('disabled', false);
              $('.change_button').find('.change_spin').addClass('d-none');
            }

          }

        });

      }else{

        e.preventDefault();

      }

    });

  });

</script>

@endpush