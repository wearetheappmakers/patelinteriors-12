<div class="kt-portlet__foot">
	<div class="row">
		@if(Auth::guard('user')->check())
		@if(Auth::user()->role == 1)  
		<div class="form-group col-lg-3">
			<label>Branch  </label>
			<select name="branch_id" class="form-control select2" >
				<option value="">Select Branch</option>
				@foreach($branch as $sub)
				<option value="{{$sub->id}}" @if($sub->id == $data->branch_id) selected @endif>{{$sub->name}}</option>
				@endforeach
			</select>
		</div>
		@endif
		@endif
		<div class="form-group col-sm-2">
			<label>Name</label>
			<input type="text" name="name" placeholder="Enter Name" class="form-control" value="{{$data->name}}" required>
		</div>
	</div>
</div>