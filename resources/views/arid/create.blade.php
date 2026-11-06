<meta name="csrf-token" content="{{ csrf_token() }}" />
<div class="kt-portlet__foot">
	<div class="row">
		@if(Auth::guard('user')->check())
		@if(Auth::user()->role == 1)  
		<div class="form-group col-sm-3">
			<label>Branch</label>
			<select class="form-control select2" id="branch_id" onchange="getemployee(this)" required name="branch_id">
				<option value="">Select</option>
				@foreach($branch as $datas)
				<option value="{{$datas->id}}">{{$datas->name}}</option>
				@endforeach
			</select>
		</div>
		<div class="form-group col-sm-3">
			<label>Employee</label>
			<select class="form-control select2" id="employee_id" required name="employee_id">
				<option value="">Select</option>
			</select>
		</div>
		@endif
		@endif
		@if(Auth::guard('user')->check())
		@if(Auth::user()->role == 2)
		<div class="form-group col-sm-3">
			<label>Employee</label>
			<select class="form-control select2" id="employee_id" required name="employee_id">
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
			<select class="form-control" name="type" required>
				<option value="1">Ar</option>
				<option value="2">ID</option>
			</select>
		</div>
		<div class="form-group col-lg-3">
			<label>Name </label>
			<input type="text" class="form-control" placeholder="Name" name="name" required>
		</div>
		<div class="form-group col-lg-3">
			<label>Personal Number </label>
			<input type="number" class="form-control" placeholder="Personal Number" name="number" pattern="\d{10}" minlength="10" maxlength="10" required >
		</div>
		<div class="form-group col-lg-3">
			<label>Office Number </label>
			<input type="number" class="form-control" placeholder="Office Number" name="office_no" >
		</div>
		<div class="form-group col-lg-3">
			<label>Firm Name </label>
			<input type="text" class="form-control" placeholder="Firm Name" name="firm_name">
		</div>
		<div class="form-group col-lg-3">
			<label>Firm Address </label>
			<input type="text" class="form-control" placeholder="Firm Address" name="firm_address">
		</div>
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
			<label>Email </label>
			<input type="text" class="form-control" placeholder="Email" name="email" id="email">
			<small id="emailError" style="color: red; display: none;">Please enter a valid email.</small>
		</div>
		<div class="form-group col-lg-3">
			<label>Birth Date </label>
			<input type="date" class="form-control" name="dob" >
		</div>
		<div class="form-group col-lg-3">
			<label>Website</label>
			<input type="text" name="website" class="form-control" placeholder="Website">
		</div>
	</div>
</div>
<script type="text/javascript">
	document.addEventListener("DOMContentLoaded", function () {
		const emailInput = document.getElementById("email");
		const emailError = document.getElementById("emailError");
		const submitBtn = document.getElementById("submit");

		function validateEmail() {
			const emailValue = emailInput.value.trim();
			const emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

			if (emailValue !== "") {
				if (!emailPattern.test(emailValue)) {
					emailError.style.display = "block";
					emailInput.classList.add("is-invalid");
					submitBtn.disabled = true;
				} else {
					emailError.style.display = "none";
					emailInput.classList.remove("is-invalid");
					submitBtn.disabled = false;
				}
			} else {
				emailError.style.display = "none";
				emailInput.classList.remove("is-invalid");
				submitBtn.disabled = false;
			}
		}

		emailInput.addEventListener("blur", validateEmail);
		emailInput.addEventListener("input", validateEmail);
	});
</script>
<script type="text/javascript">
	function getemployee($this){
		var branch_id = $($this).val();
		
		$('#employee_id').html('<option value="">Select</option>');

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

	$('.select2').select2();
</script>