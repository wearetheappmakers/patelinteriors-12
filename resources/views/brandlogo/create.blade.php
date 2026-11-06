<div class="kt-portlet__foot">
	<div class="row">
		@if(Auth::guard('user')->check())
		@if(Auth::user()->role == 1)  
		<div class="form-group col-lg-2">
			<label>Branch  </label>
			<select name="branch_id" class="form-control select2" >
				<option value="">Select Branch</option>
				@foreach($branch as $sub)
				<option value="{{$sub->id}}">{{$sub->name}}</option>
				@endforeach
			</select>
		</div>
		@endif
		@endif
		<div class="form-group col-sm-3">
			<label>Name</label>
			<input type="text" id="name" name="name" placeholder="Enter Name" class="form-control name" required>
		</div>
		<div class="form-group col-sm-3">
			<label>Brand Logo</label>
			<input type="file" id="brand_logo" name="brand_logo" class="form-control brand_logo" required>
		</div>

	</div>
</div>