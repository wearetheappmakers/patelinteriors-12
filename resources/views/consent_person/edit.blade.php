<div class="kt-portlet__foot">
	<div class="row">
		@if(Auth::guard('user')->check())
		@if(Auth::user()->role == 1)  
		<div class="form-group col-sm-3">
			<label>Branch</label>
			<input type="text" class="form-control" placeholder="Branch" value="{{ $data->branch->name }}" readonly>
		</div>
		@endif
		@endif
		<div class="form-group col-lg-3">
			<label>Name </label>
			<input type="text" class="form-control" placeholder="Name" value="{{ $data->name }}" name="name" required>
		</div>
		<div class="form-group col-lg-3">
			<label>Number </label>
			<input type="number" class="form-control" placeholder="Number" value="{{ $data->number }}" name="number" pattern="\d{10}" minlength="10" maxlength="10" required>
		</div>
	</div>
</div>