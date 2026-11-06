<meta name="csrf-token" content="{{ csrf_token() }}" />
<div class="tab-pane active" id="client" role="tabpanel">
	<form class="kt-form kt-form--label-right add_form" id="add_form" >
		@csrf
		<div class="row">
			@if(Auth::guard('user')->check())
			@if(Auth::user()->role == 1)  
			<div class="form-group col-sm-3">
				<label>Branch</label>
				<select class="form-control select2" id="branch_id" onchange="getemployee(this)"  name="branch_id">
					<option value="">Select</option>
					@foreach($branch as $datas)
					<option value="{{$datas->id}}">{{$datas->name}}</option>
					@endforeach
				</select>
			</div>
			<div class="form-group col-sm-3">
				<label>Employee</label>
				<select class="form-control select2" id="employee_id"  name="employee_id">
					<option value="">Select</option>
				</select>
			</div>
			@endif
			@endif
			@if(Auth::guard('user')->check())
			@if(Auth::user()->role == 2)
			<div class="form-group col-sm-3">
				<label>Employee</label>
				<select class="form-control select2" id="employee_id"  name="employee_id">
					<option value="">Select</option>
					@foreach($employee as $datas)
					<option value="{{$datas->id}}">{{$datas->name}}</option>
					@endforeach
				</select>
			</div>
			@endif
			@endif
			<div class="form-group col-lg-3">
				<label>Type </label>
				<select class="form-control select2" name="type" >
					<option>Selecte Type</option>
					<option value="1">Client</option>
					<option value="2">Dealer</option>
					<option value="3">Company</option>
				</select>
			</div>
			<div class="form-group col-lg-3">
				<label> Name </label>
				<input type="text" class="form-control" placeholder=" Name" name="member" required>
			</div>
			<!-- <div class="form-group col-lg-3">
				<label>Client Code </label>
				<input type="text" class="form-control" placeholder="Client Code" name="member_code" >
			</div> -->
			<!-- <div class="form-group col-lg-3">
				<label>Company </label>
				<input type="text" class="form-control" placeholder="Company" name="company" >
			</div> -->
			<div class="form-group col-lg-3">
				<label>Address </label>
				<input type="text" class="form-control" placeholder="Address" name="address" >
			</div>
			@if(Auth::guard('user')->check())
			@if(Auth::user()->role == 1)
			<div class="form-group col-sm-3">
				<label>Landmark</label>
				<select class="form-control select2" id="landmark_id"  name="landmark_id">
					<option value="">Select</option>
				</select>
			</div>
			@endif
			@endif
			@if(Auth::guard('user')->check())
			@if(Auth::user()->role == 2)
			<div class="form-group col-sm-3">
				<label>Landmark</label>
				<select class="form-control select2" id="landmark_id"  name="landmark_id">
					<option value="">Select</option>
					@foreach($landmark as $lan)
					<option value="{{$lan->id}}">{{$lan->name}}</option>
					@endforeach
				</select>
			</div>
			@endif
			@endif
			@if(Auth::guard('employee')->check())
			<div class="form-group col-sm-3">
				<label>Landmark</label>
				<select class="form-control select2" id="landmark_id"  name="landmark_id">
					<option value="">Select</option>
					@foreach($landmark as $lan)
					<option value="{{$lan->id}}">{{$lan->name}}</option>
					@endforeach
				</select>
			</div>
			@endif
			{{-- <div class="form-group col-lg-3">
				<label>City </label>
				<input type="text" class="form-control" placeholder="City" name="city" >
			</div> --}}
			<div class="form-group col-sm-3">
				<label>Country</label>
				<select class="form-control select2" id="country_id"  name="country_id" onchange="getstate(this)" required>
					<option value="">Select</option>
					@foreach($country as $val)
					<option value="{{$val->id}}">{{$val->name}}</option>
					@endforeach
				</select>
			</div>
			<div class="form-group col-sm-3">
				<label>State</label>
				<select class="form-control select2" id="state_id"  name="state_id" onchange="getcity(this)" required>
					<option value="">Select</option>
				</select>
			</div>
			<div class="form-group col-sm-3">
				<label>City</label>
				<select class="form-control select2" id="city_id"  name="city_id" required>
					<option value="">Select</option>
				</select>
			</div>
			<div class="form-group col-lg-3">
				<label>Mobile No. </label>
				<input type="number" class="form-control" placeholder="Mobile No." name="mobile_no" pattern="\d{10}" minlength="10" maxlength="10" required >
			</div>
			<div class="form-group col-lg-3">
				<label>Other Mobile No. </label>
				<input type="number" class="form-control" placeholder="Other Mobile No." name="other_mobile_no" >
			</div>
			<div class="form-group col-lg-3">
				<label>Email </label>
				<input type="text" class="form-control" placeholder="Email" name="email" >
			</div>
			<!-- <div class="form-group col-lg-3">
				<label>Referred Type </label>
				<select name="referred_type" class="form-control select2" required>
					<option value="">Select Referred Type</option>
					@foreach($leadsource as $sub)
					<option value="{{$sub->id}}">{{$sub->name}}</option>
					@endforeach
				</select>
			</div> -->
			<div class="form-group col-lg-3">
				<label>Birth Date </label>
				<input type="date" class="form-control" placeholder="" value="{{date('Y-m-d')}}" name="dob" >
			</div>
			<!-- <div class="form-group  col-lg-3">
				<div class="kt-checkbox-inline">
					<label class="kt-checkbox">
						<input type="checkbox" name="is_archive" value="1"> Is Archive
						<span></span>
					</label>
				</div>
			</div> -->
		</div>
	</form>
	<div class="kt-portlet__foot">
		<div class="kt-form__actions">
			<center>
				<button type="button" class="btn btn-brand submit_client" id="submit_client"  >Submit</button>
				<a href="{{ route('user.client.index') }}"><button type="button" class="btn btn-outline-secondary">Cancel</button></a>
			</center>
		</div>
	</div>
	
</div>
<script type="text/javascript">
	$(document).ready(function() {
		$("#submit_client").on("click", function(e) {
			// $('#submit_client').hide();
			e.preventDefault();
			if ($(".add_form").valid()) {
				$.ajax({
					type: "POST",
					url: "{{ route('user.client.store') }}",
					data: new FormData($('.add_form')[0]),
					processData: false,
					contentType: false,
					success: function(data) {
						if (data.status === 'success') {
							var id = data.data.id;
							console.log(id);
							var url = "{{route('user.client.edit',":id") }}";
							url = url.replace(':id', id);
							window.location = url;
							toastr["success"]("Client Added Successfully", "Success");
						} else if (data.status === 'error') {
							location.reload();
							toastr["error"]("Something went wrong", "Error");
						}
						else if (data.status === 'duplicate') {
							// location.reload();
							toastr["warning"]("Duplicate mobile number!", "Warning");
						}
					},
				});
			} else {
				e.preventDefault();
			}
		});
	});
</script>
<script type="text/javascript">
	function getemployee($this){
		var branch_id = $($this).val();
		
		$('#employee_id').html('<option value="">Select</option>');
		$('#landmark_id').html('<option value="">Select</option>');

		if(branch_id)
		{
			showLoader();

			$.ajax({
				type:"POST",
				headers: {
					'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
				},
				url:"{{ route('user.client.getemployee') }}",
				data: {
					'branch_id': branch_id
				},
				success: function(data){
					$('#employee_id').html(data.data1);
					$('#landmark_id').html(data.data2);

					hideLoader();
				}
			});
		}
	};

	function getstate($this){
		var country_id = $($this).val();
		$('#state_id').html('<option value="">Select</option>');
		$('#city_id').html('<option value="">Select</option>');

		if(country_id)
		{
			showLoader();

			$.ajax({
				type:"POST",
				headers: {
					'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
				},
				url:"{{ route('user.getstate') }}",
				data: {
					'country_id': country_id
				},
				success: function(data){
					$('#state_id').html(data.data);  
					hideLoader();                          
				}
			});
		}
	}

	function getcity($this){
		var state_id = $($this).val();
		$('#city_id').html('<option value="">Select</option>');

		if(state_id)
		{
			showLoader();

			$.ajax({
				type:"POST",
				headers: {
					'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
				},
				url:"{{ route('user.getcity') }}",
				data: {
					'state_id': state_id
				},
				success: function(data){
					$('#city_id').html(data.data);
					hideLoader();                            
				}
			});
		}
	}

	 $('.select2').select2({
 
});
</script>
