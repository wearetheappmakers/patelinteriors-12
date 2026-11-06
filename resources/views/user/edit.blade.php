@php

$permission = App\Group::where('id',Auth::user()->group_id)->first();

$per_exploded = explode(',',$permission->user);

@endphp

@if($per_exploded[2] == 1)

<div class="kt-portlet__body">

	<div class="row">

		<!--  <div class="form-group col-lg-3">

            <label>Group  </label>

            <select name="group_id" class="form-control" required>

                <option value="">Select Group</option>

                @foreach($group as $sub)

                <option value="{{$sub->id}}" @if($sub->id == $data->group_id) selected @endif>{{$sub->name}}</option>

                @endforeach

            </select>

        </div> -->

         <div class="form-group col-lg-3">

            <label>First Name </label>

            <input type="text" class="form-control" placeholder="First Name" value="{{$data->first_name}}" name="first_name" required>

        </div>

        <div class="form-group col-lg-3">

            <label>Last Name </label>

            <input type="text" class="form-control" placeholder="Last Name" value="{{$data->last_name}}" name="last_name"  required>

        </div>



		<div class="form-group col-lg-3">

			<label>Name </label>

			<input type="text" class="form-control" placeholder="Name" name="name" required value="{{$data->name}}">

		</div>

		<div class="form-group col-lg-3">

			<label>E-Mail </label>

			<input type="email" class="form-control" placeholder="Email Id" name="email" required value="{{$data->email}}">

		</div>

         <div class="form-group col-lg-3">

            <label>Image </label>

            <input type="file" class="form-control" placeholder="" value="{{$data->image}}" name="image" >

              @if($data->image)

               <img src="{{ asset('/user/'.$data->image) }}" height="70px" width="70px">



               @endif

        </div>



		<div class="form-group col-lg-3">

			<label>Contact </label>

			<input type="tel" class="form-control" placeholder="Contact" name="number" required value="{{$data->number}}">

		</div>

		 <div class="form-group col-lg-3">

            <label>Prefix </label>

            <input type="text" class="form-control" value="{{$data->prefix}}" placeholder="Prefix" name="prefix" >

        </div>

		<div class="form-group col-lg-3">

			<label>Password </label>

			<input type="text" class="form-control" placeholder="Password" readonly value="{{$data->show_password}}">

		</div>

		<div class="form-group col-lg-3">

			<label>Change Password </label>

			<input type="password" class="form-control" placeholder="Password" value="{{$data->show_password}}" name="password">

			<span>If you want to change password else keep it as blank.</span>

		</div>

		 

		

                                </div>

</div>

@endif

