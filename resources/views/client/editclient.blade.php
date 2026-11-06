<meta name="csrf-token" content="{{ csrf_token() }}" />
<div class="tab-pane active" id="editclient" role="tabpanel">
	<form class="kt-form kt-form--label-right edit_form" id="edit_form" method="POST" >
		@method('PUT')
		@csrf
		<div class="row">
			@if(Auth::guard('user')->check())
			@if(Auth::user()->role == 1)  
			<div class="form-group col-lg-3">
				<label>Branch  </label>
				<input type="text" class="form-control" placeholder="Branch" value="{{ $data->branch->name }}" readonly>
				{{-- <select name="branch_id" class="form-control select2"  onchange="getemployee(this)" >
					<option value="">Select Branch</option>
					@foreach($branch as $sub)
					<option value="{{$sub->id}}" @if($sub->id == $data->branch_id) selected @endif>{{$sub->name}}</option>
					@endforeach
				</select> --}}
			</div>
			<?php
			$employee = \App\Employee::where('branch_id',$data->branch_id)->get();
			?>                            
			<div class="form-group col-lg-3">
				<label>Employee  </label>
				<select name="employee_id" id="employee_id" class="form-control select2" >
					<option value="">Select Employee</option>
					@foreach($employee as $sub)
					<option value="{{$sub->id}}" @if($sub->id == $data->employee_id) selected @endif>{{$sub->name}}</option>
					@endforeach
				</select>
			</div>
			@endif
			@endif
			@if(Auth::user()->role == 2)
			<div class="form-group col-lg-3"> 
				<label>Employee  </label>
				<select name="employee_id" class="form-control select2" id="employee_id">
					<option value="">Select Employee</option>
					@foreach($employee as $sub)
					<option value="{{$sub->id}}" @if($sub->id == $data->employee_id) selected @endif >{{$sub->name}}</option>
					@endforeach
				</select>
			</div>
			@endif
			<div class="form-group col-lg-3">
				<label>Type </label>
				<select class="form-control select2" name="type" >
					<option>Selecte Type</option>
					<option value="1" @if($data->type == 1) selected @endif>Client</option>
					<option value="2" @if($data->type == 2) selected @endif>Dealer</option>
					<option value="3" @if($data->type == 3) selected @endif>Company</option>
				</select>
			</div>
			<div class="form-group col-lg-3">
				<label> Name</label>
				<input type="text" class="form-control" placeholder="Member" value="{{$data->member}}" name="member" required>
			</div>
		<!-- <div class="form-group col-lg-3">
			<label>Member Code </label>
			<input type="text" class="form-control" placeholder="Member Code" value="{{$data->member_code}}" name="member_code" >
		</div> -->
		<!-- <div class="form-group col-lg-3">
			<label>Company </label>
			<input type="text" class="form-control" placeholder="Company" value="{{$data->company}}" name="company" >
		</div> -->
		<div class="form-group col-lg-3">
			<label>Address </label>
			<input type="text" class="form-control" placeholder="Address" value="{{$data->address}}" name="address" >
		</div>
		@if(Auth::guard('user')->check())
		@if(Auth::user()->role == 1)
		<?php
		$landmark = App\Landmark::where('branch_id',$data->branch_id)->get();
		?>
		<div class="form-group col-sm-3">
			<label>Landmark</label>
			<select class="form-control select2" id="landmark_id"  name="landmark_id">
				<option value="">Select</option>
				@foreach($landmark as $lan)
				<option value="{{$lan->id}}" @if($lan->id == $data->landmark_id) selected @endif>{{$lan->name}}</option>
				@endforeach
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
				<option value="{{$lan->id}}" @if($lan->id == $data->landmark_id) selected @endif>{{$lan->name}}</option>
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
				<option value="{{$lan->id}}" @if($lan->id == $data->landmark_id) selected @endif>{{$lan->name}}</option>
				@endforeach
			</select>
		</div>
		@endif
		{{-- <div class="form-group col-lg-3">
			<label>City </label>
			<input type="text" class="form-control" placeholder="City" value="{{$data->city}}" name="city" >
		</div> --}}
		<div class="form-group col-sm-3">
			<label>Country</label>
			<select class="form-control select2" id="country_id"  name="country_id" onchange="getstate(this)" required>
				<option value="">Select</option>
				@foreach($country as $val)
				<option value="{{$val->id}}" @if($data->country_id == $val->id) selected @endif>{{$val->name}}</option>
				@endforeach
			</select>
		</div>
		<div class="form-group col-sm-3">
			<label>State</label>
			<select class="form-control select2" id="state_id"  name="state_id" onchange="getcity(this)" required>
				<option value="">Select</option>
				@foreach($state as $val)
				<option value="{{$val->id}}" @if($data->state_id == $val->id) selected @endif>{{$val->name}}</option>
				@endforeach
			</select>
		</div>
		<div class="form-group col-sm-3">
			<label>City</label>
			<select class="form-control select2" id="city_id"  name="city_id" required>
				<option value="">Select</option>
				@foreach($city as $val)
				<option value="{{$val->id}}" @if($data->city_id == $val->id) selected @endif>{{$val->name}}</option>
				@endforeach
			</select>
		</div>
		<div class="form-group col-lg-3">
			<label>Mobile No. </label>
			<input type="number" class="form-control" placeholder="Mobile No." value="{{$data->mobile_no}}" name="mobile_no" pattern="\d{10}" minlength="10" maxlength="10" required>
		</div>
		<div class="form-group col-lg-3">
			<label>Other Mobile No. </label>
			<input type="number" class="form-control" placeholder="Other Mobile No." value="{{$data->other_mobile_no}}" name="other_mobile_no" >
		</div>
		<div class="form-group col-lg-3">
			<label>Email </label>
			<input type="text" class="form-control" placeholder="Email" value="{{$data->email}}" name="email" >
		</div>
		<!-- <div class="form-group col-lg-3">
			<label>Referred Type </label>
			<select name="referred_type" class="form-control select2" required >
				<option value="">Select Referred Type</option>
				@foreach($leadsource as $sub)
				<option value="{{$sub->id}}" @if($sub->id == $data->referred_type) selected @endif>{{$sub->name}}</option>
				@endforeach
			</select>
		</div> -->
		<div class="form-group col-lg-3">
			<label>Birth Date </label>
			<input type="date" class="form-control" placeholder="" value="{{$data->dob}}" name="dob" >
		</div>
		<!-- <div class="form-group  col-lg-3">
			<div class="kt-checkbox-inline">
				<label class="kt-checkbox">
					<input type="checkbox" name="is_archive" value="1" @if($data->is_archive == 1) checked @endif> Is Archive
					<span></span>
				</label>
			</div>
		</div> -->
		
	</div>
</form>
<div class="kt-portlet__foot">
	<div class="kt-form__actions">
		<center>
			<button type="button" class="btn btn-brand submit" id="submit" >Submit</button>
			<a href="{{ route('user.client.index') }}"><button type="button" class="btn btn-outline-secondary">Cancel</button></a>
		</center>
	</div>
</div>
</div>
	<!-- <div class="row">
		<div class="col-lg-3">
			@include('layouts.status_checkbox',array('data' => ""))
		</div>
	</div> -->
	@php
	$update = route('user.client.update',$data->id);
	@endphp
	<script type="text/javascript">
		$(document).ready(function() {
			$("#submit").on("click", function(e) {
				console.log('basic');
				e.preventDefault();
				if ($(".edit_form").valid()) {
					$.ajax({
						type: "POST",
						url: "{{ $update }}",
						data: new FormData($('.edit_form')[0]),
						processData: false,
						contentType: false,
						success: function(data) {
							if (data.status === 'success') {
								
								location.reload();
								toastr["success"]("Client Added Successfully", "Success");
							} else if (data.status === 'error') {
								location.reload();
								toastr["error"]("Something went wrong", "Error");
							}else if (data.status === 'duplicate') {
								location.reload();
								toastr["error"]("Duplicate mobile number!", "Error");
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
		// function getemployee($this){
		// 	var branch_id = $($this).val();
		// 	var id = $($this).data('id');
		// 	$.ajax({
		// 		type:"POST",
		// 		headers: {
		// 			'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
		// 		},
		// 		url:"{{ route('user.client.getemployee') }}",
		// 		data: {
		// 			'branch_id': branch_id
		// 		},
		// 		success: function(data){
		// 			$('#employee_id').html(data);
		// 		}
		// 	});
		// };

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
