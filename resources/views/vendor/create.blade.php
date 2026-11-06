<meta name="csrf-token" content="{{ csrf_token() }}" />
<div class="kt-portlet__foot">
	<div class="row">
		@if(Auth::guard('user')->check())
		@if(Auth::user()->role == 1)  
		<div class="form-group col-lg-2">
			<label>Branch</label>
			<select name="branch_id" class="form-control select2" onchange="getcategory(this)" >
				<option value="">Select Branch</option>
				@foreach($branch as $sub)
				<option value="{{$sub->id}}">{{$sub->name}}</option>
				@endforeach
			</select>
		</div>
		@endif
		@endif
		<div class="form-group col-sm-2">
			<label>Name</label>
			<input type="text" name="name" placeholder="Enter Name" class="form-control" required>
		</div>
		<div class="form-group col-sm-2">
			<label>Number</label>
			<input type="number" name="number" placeholder="Enter Number" class="form-control">
		</div>
		<div class="form-group col-sm-2">
			<label>GST No</label>
			<input type="text" name="gst_no" placeholder="Enter GST No" class="form-control" required>
		</div>
		<div class="form-group col-sm-2">
			<label>Company Name</label>
			<input type="text" name="company_name" placeholder="Enter Company Name" class="form-control" >
		</div>
		<div class="form-group col-sm-2">
			<label>Company Address</label>
			<textarea name="company_address" class="form-control" placeholder="Enter Company Address"></textarea>
		</div>
		<div class="form-group col-sm-2">
			<label>Email</label>
			<input type="email" name="email" placeholder="Enter Email" class="form-control" >
		</div>
		<div class="form-group col-sm-2">
			<label>Address</label>
			<textarea name="address" class="form-control" placeholder="Enter Address"></textarea>
		</div>
		<div class="form-group col-sm-2">
			<label>Country</label>
			<select name="country_id" id="country_id" class="form-control select2" required onchange="getstate(this)">
				<option value="">Select</option>
				@foreach($country as $key => $value)
				<option value="{{$value->id}}">{{$value->name}}</option>
				@endforeach
			</select>
		</div>
		<div class="form-group col-sm-2">
			<label>State</label>
			<select name="state_id" id="state_id" class="form-control select2" required onchange="getcity(this)">
				<option value="">Select State</option>
				
			</select>
		</div>
		<div class="form-group col-sm-2">
			<label>City</label>
			<select name="city_id" id="city_id" class="form-control select2" required>
				<option value="">Select City</option>
				
			</select>
		</div>
	</div>
</div>
<script type="text/javascript">
	$('.select2').select2();
	function getcategory($this){
		var branch_id = $($this).val();
		var id = $($this).data('id');
		$.ajax({
			type:"POST",
			headers: {
				'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
			},
			url:"{{ route('user.getcategory') }}",
			data: {
				'branch_id': branch_id
			},
			success: function(data){
				$('#pro_category_id').html(data);
			}
		});
	};
	function getstate($this){
		var country_id = $($this).val();

		$('#state_id').html('<option value="">Select</option>');
		$('#city_id').html('<option value="">Select</option>');

		if(country_id != '')
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
	};
	function getcity($this){
		var state_id = $($this).val();

		$('#city_id').html('<option value="">Select</option>');

		if(state_id != '')
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
	};
</script>