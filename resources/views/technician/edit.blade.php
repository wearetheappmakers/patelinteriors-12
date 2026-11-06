<?php $employee = \App\Employee::where('branch_id',$data->branch_id)->get(); ?>
<meta name="csrf-token" content="{{ csrf_token() }}" />
<div class="kt-portlet__body">
	<div class="tab-pane active" id="client" role="tabpanel">
		<div class="row">
			@if(Auth::guard('user')->check())
			@if(Auth::user()->role == 1)
			<div class="form-group col-lg-3">
				<label>Branch  </label>
				<input type="text" class="form-control" placeholder="Branch" value="{{ $data->branch->name }}" readonly>
			</div>
			@endif
			<div class="form-group col-lg-3">
				<label>Employee  </label>
				<select name="employee_id" class="form-control select2" id="employee_id"  required >
					<option value="">Select</option>
					@foreach($employee as $sub)
					<option value="{{$sub->id}}" @if($sub->id == $data->employee_id) selected @endif>{{$sub->name}}</option>
					@endforeach
				</select>
			</div>
			@endif

			@if(Auth::guard('employee')->check() && Auth::user()->role == 4)
			<div class="form-group col-lg-3">
				<label>Employee </label>
				<select name="employee_id" class="form-control select2" id="employee_id"  required >
					<option value="">Select</option>
					@foreach($employee as $sub)
					<option value="{{$sub->id}}" @if($sub->id == $data->employee_id) selected @endif>{{$sub->name}}</option>
					@endforeach
				</select>
			</div>    
			@endif

			@if(Auth::guard('user')->check())
			@if(Auth::user()->role == 1 || Auth::user()->role == 2) 
			<div class="form-group col-lg-3">
				<label>Category</label>
				<?php  $productcategory = App\ProductCategory::where('branch_id',$data->branch_id)->where('status',1)->get(); ?>
				<select class="form-control productcategory_id select2" id="productcategory_id" name="productcategory_id" onchange="getcompany(this)"  >
					<option value="">Select</option>
					@foreach($productcategory as $value)
					<option value="{{$value->id}}" @if($value->id == $data->productcategory_id) selected @endif>{{$value->name}}</option>
					@endforeach
				</select>
			</div>
			@endif
			@endif

			@if(Auth::guard('employee')->check())
			<div class="form-group col-lg-3">
				<label>Category</label>
				<?php  $productcategory = App\ProductCategory::where('branch_id',$data->branch_id)->where('status',1)->get(); ?>
				<select class="form-control productcategory_id select2" id="productcategory_id" name="productcategory_id" onchange="getcompany(this)"  >
					<option value="">Select</option>
					@foreach($productcategory as $value)
					<option value="{{$value->id}}" @if($value->id == $data->productcategory_id) selected @endif>{{$value->name}}</option>
					@endforeach
				</select>
			</div>
			@endif

			<div class="form-group col-lg-3">
				<label>Company</label>
				<select class="form-control company_id select2" id="company_id" name="company_id" required>
					<option value="">Select</option>
					@foreach($company as $value)
					<option value="{{$value->id}}" @if($value->id == $data->company_id) selected @endif >{{$value->name}}</option>
					@endforeach
				</select>
			</div>

			<div class="form-group col-lg-3">
				<label>Technician Name</label>
				<input type="text" class="form-control" placeholder="Enter Technician Name" name="technician_name" value="{{ $data->technician_name }}" required>
			</div>
		</div>
	</div>
</div>

<script type="text/javascript">
	function getemployee($this){
		var branch_id = $($this).val();
		showLoader();
		$.ajax({
			type:"POST",
			headers: {
				'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
			},
			url:"{{ route('user.technician.getemployee') }}",
			data: {
				'branch_id': branch_id
			},
			success: function(data){
				hideLoader();
				$('#employee_id').html(data.data);
				$('#client_id').html(data.data1);
				$('.productcategory_id').html(data.data2);
			}
		});
	};
</script>
<script type="text/javascript">
	function getcompany($this){
		var productcategory_id = $($this).val();
		var branch_id = $('#branch_id').val();
		showLoader();
		$.ajax({
			type:"POST",
			headers: {
				'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
			},
			url:"{{ route('user.technician.getcompany') }}",
			data: {
				'productcategory_id': productcategory_id,
				'branch_id': branch_id
			},
			success: function(data){
				$('#company_id').html(data.data);
				$('#product_id').html('<option value="">Select</option>');
				hideLoader();
			}
		});
	};
</script>