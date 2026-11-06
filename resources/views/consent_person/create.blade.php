<div class="kt-portlet__foot">
	<div class="row">
		@if(Auth::guard('user')->check())
		@if(Auth::user()->role == 1)  
		<div class="form-group col-sm-3">
			<label>Branch</label>
			<select class="form-control select2" id="branch_id" name="branch_id">
				<option value="">Select</option>
				@foreach($branch as $datas)
				<option value="{{$datas->id}}">{{$datas->name}}</option>
				@endforeach
			</select>
		</div>
		@endif
		@endif
		<div class="form-group col-lg-3">
			<label>Name </label>
			<input type="text" class="form-control" placeholder="Name" name="name" required>
		</div>
		<div class="form-group col-lg-3">
			<label>Number </label>
			<input type="number" class="form-control" placeholder="Number" name="number" pattern="\d{10}" minlength="10" maxlength="10" required>
		</div>
	</div>
</div>

<script type="text/javascript">
	$('.select2').select2();
</script>