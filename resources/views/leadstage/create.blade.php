

<div class="kt-portlet__body">

	<div class="row">

		@if(Auth::guard('user')->check())

		@if(Auth::user()->role == 1)  

		<div class="form-group col-lg-2">

			<label>Branch  </label>

			<select name="branch_id" class="form-control" >

				<option value="">Select Branch</option>

				@foreach($branch as $sub)

				<option value="{{$sub->id}}" >{{$sub->name}}</option>

				@endforeach

			</select>

		</div>

		@endif       

		@endif

		<div class="form-group col-lg-2">

			<label>Lead Stage Name </label>

			<input type="text" class="form-control" placeholder="Lead Stage Name" name="name" required>

		</div>



		<div class="form-group col-lg-2">

			<label>Lead Stage Color </label>

			<input type="color" class="form-control" placeholder="Lead Stage Color" name="color" required>

		</div>



		



		<div class="form-group  col-lg-2">

			<!-- <label>Is Completed</label> -->

			<div class="kt-checkbox-inline">

				<label class="kt-checkbox">

					<input type="checkbox" name="is_completed" value="1"> Is Completed

					<span></span>

				</label>



			</div>



		</div>



		<div class="form-group  col-lg-2">

			<!-- <label>Is Completed</label> -->

			<div class="kt-checkbox-inline">

				<label class="kt-checkbox">

					<input type="checkbox" name="is_canceled" value="1"> Is Canceled

					<span></span>

				</label>

				

			</div>

			

		</div>

		

	</div>

	<!-- <div class="row">

		<div class="col-lg-3">

			@include('layouts.status_checkbox',array('data' => ""))

		</div>



	</div> -->

</div>



