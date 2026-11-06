<div class="kt-portlet__foot">
	<div class="row">
		@if(Auth::guard('user')->check())
		@if(Auth::user()->role == 1)  
		<div class="form-group col-lg-2">
			<label>Branch  </label>
			<select name="branch_id" class="form-control select2" onchange="getleadsource(this)">
				<option value="">Select Branch</option>
				@foreach($branch as $sub)
				<option value="{{$sub->id}}" @if($sub->id == $data->branch_id) selected @endif>{{$sub->name}}</option>
				@endforeach
			</select>
		</div>
		<?php $sources = App\LeadSource::where('branch_id',$data->branch_id)->get(); ?>
		<div class="form-group col-lg-2">
			<label>Source </label>
			<select name="lead_sourceid" id="lead_sourceid" class="form-control select2" required>
				<option value="">Select</option>
				@foreach($sources as $sub)
				<option value="{{$sub->id}}" @if($data->lead_sourceid == $sub->id) selected @endif >{{$sub->name}}</option>
				@endforeach
			</select>
		</div>
		@else
		<?php $sources = App\LeadSource::where('branch_id',Auth::user()->id)->get(); ?>
		<div class="form-group col-lg-2">
			<label>Source </label>
			<select name="lead_sourceid" class="form-control select2" required>
				<option value="">Select</option>
				@foreach($sources as $sub)
				<option value="{{$sub->id}}" @if($data->lead_sourceid == $sub->id) selected @endif >{{$sub->name}}</option>
				@endforeach
			</select>
		</div>
		@endif
		@else
		<?php $sources = App\LeadSource::where('branch_id',Auth::user()->branch_id)->get(); ?>
		<div class="form-group col-lg-2">
			<label>Source </label>
			<select name="lead_sourceid" class="form-control select2" required>
				<option value="">Select</option>
				@foreach($sources as $sub)
				<option value="{{$sub->id}}" @if($data->lead_sourceid == $sub->id) selected @endif >{{$sub->name}}</option>
				@endforeach
			</select>
		</div>
		@endif
		<div class="form-group col-sm-2">
			<label>Name</label>
			<input type="text" name="name" placeholder="Enter Name" class="form-control" value="{{$data->name}}" required>
		</div>
		<div class="form-group col-sm-2">
			<label>Address</label>
			<input type="text" name="address" placeholder="Enter Address" class="form-control" value="{{$data->address}}">
		</div>
		<div class="form-group col-sm-2">
			<label>Phone No.</label>
			<input type="number" name="phoneno" placeholder="Enter Phone Number" class="form-control" value="{{$data->phoneno}}">
		</div>
		<div class="form-group col-sm-2">
			<label>Email</label>
			<input type="text" name="email" placeholder="Enter Email" class="form-control" value="{{$data->email}}">
		</div>
		<div class="form-group col-sm-2">
			<label>Birth Date</label>
			<input type="date" name="birth_date" class="form-control" value="{{$data->birth_date}}">
		</div>
		<div class="form-group col-sm-2">
			<label>Anniversary Date</label>
			<input type="date" name="anniversary_date" class="form-control" value="{{$data->anniversary_date}}">
		</div>
	</div>
</div>
<script type="text/javascript">
	function getleadsource($this){
		var branch_id = $($this).val();
		$('#lead_sourceid').html('<option value="">Select</option>');
		if(branch_id)
		{
			showLoader();
			$.ajax({
				type:"POST",
				url:"{{ route('user.getleadsource') }}",
				data: {
					'_token': "{{ csrf_token() }}",
					'branch_id': branch_id
				},
				success: function(data){
					$('#lead_sourceid').html(data.data);
					hideLoader();
				}
			});
		}
	};
</script>