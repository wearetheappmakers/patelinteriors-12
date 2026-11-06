<meta name="csrf-token" content="{{ csrf_token() }}" />
<div class="kt-portlet__body">
	<div class="tab-pane active" id="client" role="tabpanel">
		<div class="row">
			@if(Auth::guard('user')->check())
			@if(Auth::user()->role == 1)  
			<div class="form-group col-lg-3">
				<label>Branch</label>
				<select name="branch_id" class="form-control select2" onchange="getemployee(this)" required>
					<option value="">Select Branch</option>
					@foreach($branch as $sub)
					<option value="{{$sub->id}}">{{$sub->name}}</option>
					@endforeach
				</select>
			</div>
			<div class="form-group col-lg-3">
				<label>Employee</label>
				<select name="employee_id" class="form-control select2" id="employee_id" required>
					<option value="">Select Employee</option>
				</select>
			</div>
			<div class="form-group col-lg-3">
				<label>Client </label>
				<select name="client_id" id="client_id" class="form-control select2" onchange="getnumber(this)" required>
					<option value="">Select</option>
				</select>
			</div>
			<div class="form-group col-lg-3">
				<label>Consent Person </label>
				<select name="consent_person_id" id="consent_person_id" class="form-control select2">
					<option value="">Select</option>
				</select>
			</div>
			@endif
			@endif
			@if(Auth::guard('user')->check())
			@if(Auth::user()->role == 2)
			<div class="form-group col-lg-3">
				<label>Employee</label>
				<select name="employee_id" class="form-control select2" id="employee_id" required>
					<option value="">Select Employee</option>
					@foreach($employees as $sub)
					<option value="{{$sub->id}}">{{$sub->name}}</option>
					@endforeach
				</select>
			</div>
			<div class="form-group col-lg-3">
				<label>Client </label>
				<select name="client_id" id="client_id" class="form-control select2" onchange="getnumber(this)" required>
					<option value="">Select</option>
					@foreach($client as $sub)
					<option value="{{$sub->id}}">{{$sub->member}}</option>
					@endforeach
				</select>
			</div>
			<div class="form-group col-lg-3">
				<label>Consent Person </label>
				<select name="consent_person_id" id="consent_person_id" class="form-control select2">
					<option value="">Select</option>
					@foreach($consent_person as $sub)
					<option value="{{$sub->id}}">{{$sub->name}}</option>
					@endforeach
				</select>
			</div>
			@endif
			@endif
			@if(Auth::guard('employee')->check())
			<div class="form-group col-lg-3">
				<label>Client </label>
				<select name="client_id" id="client_id" class="form-control select2" onchange="getnumber(this)" required>
					<option value="">Select</option>
					@foreach($client as $sub)
					<option value="{{$sub->id}}">{{$sub->member}}</option>
					@endforeach
				</select>
			</div>
			<div class="form-group col-lg-3">
				<label>Consent Person </label>
				<select name="consent_person_id" id="consent_person_id" class="form-control select2">
					<option value="">Select</option>
					@foreach($consent_person as $sub)
					<option value="{{$sub->id}}">{{$sub->name}}</option>
					@endforeach
				</select>
			</div>
			@endif
			<div class="form-group col-lg-3">
				<label>Phone No.</label>
				<input type="number" class="form-control" placeholder="Enter Phone No." id="phone_no" name="phone_no" readonly>
			</div>
			<div class="form-group col-lg-3">
				<label>Address</label>
				<input type="text" class="form-control" placeholder="Enter Address" id="address" name="address" readonly>
			</div>
			<div class="form-group col-lg-3">
				<label>Complain No.</label>
				<input type="text" class="form-control" placeholder="Enter Complain No." name="complain_no">
			</div>
			<div class="form-group col-lg-3">
				<label>Register Date</label>
				<input type="date" class="form-control" placeholder="" value="{{date('Y-m-d')}}" name="register_date">
			</div>
			<div class="form-group col-lg-3">
				<label>Remarks</label>
				<input id="remarks" name="remark" rows="1" placeholder="Enter Remarks" class="form-control"> 
			</div>
			<div class="form-group col-lg-3">
				<label>Complete Date</label>
				<input type="date" class="form-control" placeholder="" name="completed_date">
			</div>
			<div class="form-group col-lg-3">
				<label>Image</label>
				<input type="file" class="form-control" placeholder="" name="image[]" multiple>
			</div>
			<div class="form-group col-lg-3">
				<label>Type</label>
				<select name="type" class="form-control">
					<option value="">Select</option>
					<option value="1">Complain</option>
					<option value="2">Demostration</option>
					<option value="3">Pre Delivery Inspection</option>
					<option value="4">Installation Guideline</option>
					<option value="5">After Installation Guideline</option>
				</select>
			</div>
		</div>
	</div>
	<div class="row">
		<div class=" form-group col-sm-12" style="overflow: auto !important">
			<table cellspacing="0" border="0" class="table table-bordered">
				<thead>
					<tr>
						<th></th>
						<th>Category</th>
						<th>Company</th>
						<th>Technician</th>
						<th>Product</th>
						<th>Qty</th>
					</tr>
				</thead>
				<tbody>
					<tr class="master_clone" data-id="0" data-master="0">
						<td>
							<div style="display: inline-flex !important;">
								<button type="button" id="add_more" class="btn btn-sm btn-clean btn-icon btn-icon-md add_more"><i class="fa fa-plus" style="font-size:0.8rem !important"></i></button>&nbsp;&nbsp;&nbsp;
								<button type="button" id="remove" data-id='0' class="btn btn-sm btn-icon btn-icon-md btn-clean remove remove"><i class="fa fa-minus" style="font-size:0.8rem !important"></i></button>
							</div>
						</td>
						@if(Auth::guard('user')->check())
						@if(Auth::user()->role == 1) 
						<td width="20%">
							<input type="hidden" name="main_id[]" class="main_id">
							<select class="form-control productcategory_id select2" id="productcategory_id_0" name="productcategory_id[]" data-id="0" onchange="getcompany(this)">
								<option value="">Select</option>
								
							</select>
						</td>
						@endif
						@endif
						@if(!(Auth::user()->role == 1))
						<td width="20%">
							<input type="hidden" name="main_id[]" class="main_id">
							<select class="form-control productcategory_id select2" id="productcategory_id_0" name="productcategory_id[]" data-id="0" onchange="getcompany(this)">
								<option value="">Select</option>
								@foreach($productcategory as $value)
								<option value="{{$value->id}}">{{$value->name}}</option>
								@endforeach
							</select>
						</td>
						@endif
						<td width="20%">
							<select class="form-control company_id select2" id="company_id_0" name="company_id[]" onchange="getproduct(this)" data-id="0">
								<option value="">Select</option>
							</select>
						</td>
						<td width="20%">
							<select class="form-control technician_id select2" id="technician_id_0" name="technician_id[]" data-id="0">
								<option value="">Select</option>
							</select>
						</td>
						<td width="20%">
							<select class="form-control product_id select2" id="product_id_0" name="product_id[]" data-id="0">
								<option value="">Select</option>
							</select>
						</td>
						<td width="20%">
							<input type="number" name="qty[]" class="form-control qty" id="qty_0" data-id="0">
						</td>
					</tr>
				</tbody>
			</table>
		</div>
	</div>
</div>
<script type="text/javascript">
	function getemployee($this){
		var branch_id = $($this).val();
		var id = $($this).data('id');

		$('#employee_id').html('<option value="">Select Employee</option>');
		$('#client_id').html('<option value="">Select</option>');
		$('#consent_person_id').html('<option value="">Select</option>')
		$('#phone_no').val('');
		$('#address').val('');

		$('.productcategory_id').html('<option value="">Select</option>');
		$('.company_id').html('<option value="">Select</option>');
		$('.technician_id').html('<option value="">Select</option>');
		$('.product_id').html('<option value="">Select</option>');
		$('.qty').val('');

		if(branch_id != '')
		{
			showLoader();
			$.ajax({
				type:"POST",
				headers: {
					'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
				},
				url:"{{ route('user.service.getemployee') }}",
				data: {
					'branch_id': branch_id
				},
				success: function(data){
					hideLoader();
					$('#employee_id').html(data.data);
					$('#client_id').html(data.data1);
					$('#consent_person_id').html(data.data3);
					$('.productcategory_id').html(data.data2);
				}
			});
		}
	};
	function getcompany($this){
		var productcategory_id = $($this).val();
		var id = $($this).data('id');

		$('#company_id_'+id).html('<option value="">Select</option>');

		if(productcategory_id != '')
		{
			showLoader();
			$.ajax({
				type:"POST",
				headers: {
					'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
				},
				url:"{{ route('user.service.getcompany') }}",
				data: {
					'productcategory_id': productcategory_id
				},
				success: function(data){
					hideLoader();
					$('#company_id_'+id).html(data.data);
				}
			});
		}
	};
	function getproduct($this){
		var company_id = $($this).val();
		var id = $($this).data('id');

		$('#product_id_'+id).html('<option value="">Select</option>');
		$('#technician_id_'+id).html('<option value="">Select</option>');

		if(company_id != '')
		{
			showLoader();
			$.ajax({
				type:"POST",
				headers: {
					'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
				},
				url:"{{ route('user.service.getproduct') }}",
				data: {
					'company_id': company_id
				},
				success: function(data){
					hideLoader();
					$('#product_id_'+id).html(data.data);
					$('#technician_id_'+id).html(data.data3);
				}
			});
		}
	};
	function getnumber($this){
		var client_id = $($this).val();
		var id = $($this).data('id');
		$.ajax({
			type:"POST",
			headers: {
				'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
			},
			url:"{{ route('user.service.getnumber') }}",
			data: {
				'client_id': client_id
			},
			success: function(data){
				$('#phone_no').val(data.data);
				$('#address').val(data.data1);
			}
		});
	};
</script>
<script type="text/javascript">
	$(document).ready(function() {
		var count = 0;
		$(document).on('click','.add_more',function() {
			count++;
			var $row = $(this).closest('.master_clone');
			var $clone = $row.clone();
			$clone.find('.qty').val('').attr('data-id',count).attr('id','qty_'+count);

			var $clone1 = $row.clone();
			$clone1.find('.productcategory_id, .company_id, .technician_id, .product_id').select2();

			$clone.find('.productcategory_id').val('').css('width','100%').attr('data-id',count).attr('id','productcategory_id_'+count).attr('name','productcategory_id[]').removeClass('select2-hidden-accessible').next('.select2-container').remove();
			$clone.find('.company_id').html('<option value="">Select</option>').css('width','100%').attr('data-id',count).attr('id','company_id_'+count).attr('name','company_id[]').removeClass('select2-hidden-accessible').next('.select2-container').remove();
			$clone.find('.technician_id').html('<option value="">Select</option>').css('width','100%').attr('data-id',count).attr('id','technician_id_'+count).attr('name','technician_id[]').removeClass('select2-hidden-accessible').next('.select2-container').remove();
			$clone.find('.product_id').html('<option value="">Select</option>').css('width','100%').attr('data-id',count).attr('id','product_id_'+count).attr('name','product_id[]').removeClass('select2-hidden-accessible').next('.select2-container').remove();

			$clone.find('.productcategory_id, .company_id, .technician_id, .product_id, .warehouse_id').select2();
			$clone.find('.productcategory_id, .company_id, .technician_id, .product_id, .warehouse_id').select2();
			$('.productcategory_id, .company_id, .technician_id, .product_id, .warehouse_id').select2();

			$row.after($clone);
		});
		$(document).on('click','.remove',function(){
			var num_of_master_clone_div_payment = $('.master_clone').length;
			if (num_of_master_clone_div_payment != 1) {
				var obj = $(this).closest('.master_clone');
				obj.remove();
			}
		});
	});
</script>