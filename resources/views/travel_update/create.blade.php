<meta name="csrf-token" content="{{ csrf_token() }}" />

<div class="kt-portlet__foot">



	<div class="row">
       {{-- Super Admin --}}

		@if(Auth::guard('user')->check() && Auth::user()->role == 1)

			<div class="form-group col-sm-3">
				<label>Branch</label>
				<select class="form-control select2" id="branch_id"
						onchange="getemployee(this)" name="branch_id">
					<option value="">Select</option>
					@foreach($branch as $datas)
						<option value="{{ $datas->id }}">{{ $datas->name }}</option>
					@endforeach
				</select>
			</div>

			<div class="form-group col-sm-3">
				<label>Employee</label>
				<select class="form-control select2" id="employee_id" name="employee_id">
					<option value="">Select</option>
				</select>
			</div>
		@endif


		{{-- EMPLOYEE (HR only, role = 9) --}}
				@if(
			(Auth::guard('employee')->check() && Auth::guard('employee')->user()->role == 9) ||
			(Auth::guard('user')->check() && Auth::user()->role == 2)
	     	)
			<div class="form-group col-sm-3">
				<label>Employee</label>
				<select class="form-control select2" name="employee_id">
					<option value="">Select</option>
					@foreach($employees as $emp)
						<option value="{{ $emp->id }}"
							{{ isset($data) && $data->employee_id == $emp->id ? 'selected' : '' }}>
							{{ $emp->name }}
						</option>
					@endforeach
				</select>
			</div>
		@endif



		<div class="form-group col-sm-2">

			<label>SCH. DATE</label>

			<input type="date" name="sch_date" placeholder="Enter Date" class="form-control" required>

		</div>
        <div class="form-group col-sm-2">

			<label>SCH. Time</label>

			<input type="time" name="sch_time" placeholder="Enter Time" class="form-control" required>

		</div>

        <div class="form-group col-sm-2">

			<label>Name</label>

			<input type="text" name="name" placeholder="Enter Name" class="form-control" required>

		</div>
        <div class="form-group col-sm-2">

			<label>Contact No</label>

			<input type="text" name="contact_no" placeholder="Enter Contact" class="form-control" required>

		</div>
        <div class="form-group col-sm-2">

			<label>Purpose Of Travel</label>

			<textarea type="text" name="purpose_of_travel" placeholder="Enter Purpose Of Travel" class="form-control" required></textarea>

		</div>



	</div>

</div>


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
				url:"{{ route('user.travel_update.getemployee') }}",
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

	

	 $('.select2').select2({
 
});
</script>