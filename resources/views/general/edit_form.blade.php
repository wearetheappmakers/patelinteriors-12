@extends('main')
@section('content')
<?php
if($resourcePath === 'task')
{
    if(Auth::guard('user')->check())
    {
        if(Auth::user()->role == 2)
        {
            $check = App\Task::where('id',$data->id)->first();
            if(isset($check) && !(Auth::user()->id == $check->branch_id))
            {
                dd('Access Denied');
            }
        }
    }
}
?>
<div class="kt-container  kt-container--fluid  kt-grid__item kt-grid__item--fluid">
    <br>
    <div class="kt-portlet">
        <div class="kt-portlet__head">
            <div class="kt-portlet__head-label">
                <h3 class="kt-portlet__head-title">
                    {{$title}}
                </h3>
            </div>
        </div>
        <form class="kt-form" class="addform" id="addform" method="POST">
            @method('put')
            @csrf
            <?php 
            if($resourcePath === 'payment')
            {
                $redirect_id = $redirect_id;
                $index = route($resourcePath.'.index',$redirect_id);
            }
            else
            {
                $index = route('user.'.$resourcePath.'.index');
            }
            $update = route('user.'.$resourcePath.'.update',$data->id);
            ?>
            @include($resourcePath.'.edit')
        </form>
        <div class="kt-portlet__foot">
            <div class="kt-form__actions">
                <center>
                    <button type="submit" class="btn btn-brand submit" id="submit">Update</button>
                   
                     @if($resourcePath === 'expense')
                        <!-- For expense, stay on the same edit page -->
                        <a href="{{ url()->current() }}" class="btn btn-outline-secondary" style="display:none;">Cancel</a>
                    @else
                        <!-- For other modules, use the general $index -->
                        <a href="{{ $index }}" class="btn btn-outline-secondary">Cancel</a>
                    @endif
                </center>
            </div>
        </div>
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
                    url:"{{$update}}",
                    data: new FormData($('#addform')[0]),
                    processData: false,
                    contentType:false,
                    success: function (data) {
                        $('.loader').hide();
                        $('.loader-wrapper').hide();
                        if (data.status === 'success') {
                            toastr["success"]("{{$module}} Updated Successfully.", "Success");
                                if (data.redirect) {
                                    window.location.href = data.redirect;
                                } else{
                                window.location = "{{ $index }}";
                                }

                        } else if (data.status === 'error') {
                            toastr["error"]("Unsuccessfull", "Error");
                            location.reload();
                        }
                        else if(data.status==='password'){
                            toastr["error"]("Password Not Match", "Error");
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
                $('.loader').hide();
                $('.loader-wrapper').hide();
                e.preventDefault();
            }
        });
    });
</script>
@endpush