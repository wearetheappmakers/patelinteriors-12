

@php

$permission = App\Group::where('id',Auth::user()->group_id)->first();

$per_exploded = explode(',',$permission->user);

@endphp

@if($per_exploded[1] == 1)

<div class="kt-portlet__body">

	<div class="row">

       <!--  <div class="form-group col-lg-3">

            <label>Group  </label>

            <select name="group_id" class="form-control" required>

                <option value="">Select Group</option>

                @foreach($group as $sub)

                <option value="{{$sub->id}}">{{$sub->name}}</option>

                @endforeach

            </select>

        </div> -->

        <div class="form-group col-lg-3">

            <label>First Name </label>

            <input type="text" class="form-control" placeholder="First Name" name="first_name" required>

        </div>

        <div class="form-group col-lg-3">

            <label>Last Name </label>

            <input type="text" class="form-control" placeholder="Last Name" name="last_name" required>

        </div>

		<div class="form-group col-lg-3">

			<label>Name </label>

			<input type="text" class="form-control" placeholder="Name" name="name" required>

		</div>

		<div class="form-group col-lg-3">

			<label>E-Mail </label>

			<input type="email" class="form-control" placeholder="Email Id" name="email" required>

		</div>

        <div class="form-group col-lg-3">

            <label>Image </label>

            <input type="file" class="form-control" placeholder="" name="image" >

        </div>

		<div class="form-group col-lg-3">

			<label>Number </label>

			<input type="tel" class="form-control" placeholder="Contact" name="number" required>

		</div>
        <div class="form-group col-lg-3">

            <label>Prefix </label>

            <input type="text" class="form-control" placeholder="Prefix" name="prefix" required>

        </div>

		<div class="form-group col-lg-3">

			<label>Password </label>

			<input type="password" class="form-control" placeholder="Password" name="password" required>

		</div>

       

		





	

	<!-- <div class="row">

		<div class="col-lg-3">

			@include('layouts.status_checkbox',array('data' => ""))

		</div>



	</div> -->

</div>

@endif

<script type="text/javascript">

	  function showDiv()

    {

        if($('#role').val() == 3)

        {

           

            $('#per_div').show();

            // $('#myp_client_code').attr("required",true);

            // $('#kyc_client_code').attr("required",true);

            // $('#bi_family').attr("required",true);

            // $('#address').attr("required",false);

            // $('#key_password').attr("required",false);

        }

    }

</script>