<meta name="csrf-token" content="{{ csrf_token() }}" />
<div class="kt-portlet__body">
	<div class="tab-pane active" id="client" role="tabpanel">
		<div class="row">
			@if(Auth::guard('user')->check())
			@if(Auth::user()->role == 1)  
			<div class="form-group col-lg-3">
				<label>Branch  </label>
				<input type="hidden" id="branch_id" value="{{ $data->branch_id }}">
				<input type="text" class="form-control" placeholder="Branch" value="{{ $data->branch->name }}" readonly>
			</div>                     
			<div class="form-group col-lg-3">
				<label>Employee  </label>
				<select name="employee_id" id="employee_id" class="form-control select2" required >
					<option value="">Select Employee</option>
					@foreach($employees as $sub)
					<option value="{{$sub->id}}" @if($sub->id == $data->employee_id) selected @endif>{{$sub->name}}</option>
					@endforeach
				</select>
			</div>    
			<div class="form-group col-lg-3">
				<label>Client </label>
				<select name="client_id" class="form-control select2" id="client_id" required>
					<option value="">Select</option>
					@foreach($client as $sub)
					<option value="{{$sub->id}}" @if($sub->id == $data->client_id) selected @endif  >{{$sub->member}}</option>
					@endforeach
				</select>
			</div>
			<div class="form-group col-lg-3">
				<label>Consent Person </label>
				<select name="consent_person_id" class="form-control select2" id="consent_person_id">
					<option value="">Select</option>
					@foreach($consent_person as $sub)
					<option value="{{$sub->id}}" @if($sub->id == $data->consent_person_id) selected @endif  >{{$sub->name}}</option>
					@endforeach
				</select>
			</div>
			@endif
			@endif
			@if(Auth::guard('user')->check())
			@if(Auth::user()->role == 2)
			<div class="form-group col-lg-3">
				<label>Employee  </label>
				<select name="employee_id" id="employee_id" class="form-control select2" required >
					<option value="">Select Employee</option>
					@foreach($employees as $sub)
					<option value="{{$sub->id}}" @if($sub->id == $data->employee_id) selected @endif>{{$sub->name}}</option>
					@endforeach
				</select>
			</div>
			<div class="form-group col-lg-3">
				<label>Client </label>
				<select name="client_id" class="form-control select2" required>
					<option value="">Select</option>
					@foreach($client as $sub)
					<option value="{{$sub->id}}" @if($sub->id == $data->client_id) selected @endif  >{{$sub->member}}</option>
					@endforeach
				</select>
			</div>
			<div class="form-group col-lg-3">
				<label>Consent Person </label>
				<select name="consent_person_id" class="form-control select2" id="consent_person_id">
					<option value="">Select</option>
					@foreach($consent_person as $sub)
					<option value="{{$sub->id}}" @if($sub->id == $data->consent_person_id) selected @endif  >{{$sub->name}}</option>
					@endforeach
				</select>
			</div>
			@endif
			@endif
			@if(Auth::guard('employee')->check())
			<div class="form-group col-lg-3">
				<label>Client </label>
				<select name="client_id" class="form-control select2" required>
					<option value="">Select</option>
					@foreach($client as $sub)
					<option value="{{$sub->id}}" @if($sub->id == $data->client_id) selected @endif  >{{$sub->member}}</option>
					@endforeach
				</select>
			</div>
			<div class="form-group col-lg-3">
				<label>Consent Person </label>
				<select name="consent_person_id" class="form-control select2" id="consent_person_id">
					<option value="">Select</option>
					@foreach($consent_person as $sub)
					<option value="{{$sub->id}}" @if($sub->id == $data->consent_person_id) selected @endif  >{{$sub->name}}</option>
					@endforeach
				</select>
			</div>
			@endif
			<div class="form-group col-lg-3">
				<label>Phone No.</label>
				<input type="number" class="form-control" placeholder="Enter Phone No." value="{{$data->phone_no}}" id="phone_no"  name="phone_no" readonly>
			</div>
			<div class="form-group col-lg-3">
				<label>Address</label>
				<input type="text" class="form-control" placeholder="Enter Address" id="address" value="{{$data->address}}"  name="address" readonly >
			</div>
			<div class="form-group col-lg-3">
				<label>Complain No.</label>
				<input type="text" class="form-control" placeholder="Enter Complain No." value="{{$data->complain_no}}"  name="complain_no" >
			</div>
			<div class="form-group col-lg-3">
				<label>Register Date</label>
				<input type="date" class="form-control" placeholder="" value="{{$data->register_date}}" name="register_date" >
			</div>
			<div class="form-group col-lg-3">
				<label>Remarks</label>
				<input id="remarks" name="remark" rows="1" placeholder="Enter Remarks" value="{{$data->remark}}" class="form-control"> 
			</div>
			<div class="form-group col-lg-3">
				<label>Complete Date</label>
				<input type="date" class="form-control" placeholder="" value="{{$data->completed_date}}"  name="completed_date" >
			</div>
			<div class="form-group col-lg-3">
				<label>Image</label>
				<input type="file" class="form-control" placeholder=""   name="image[]" multiple >
			</div>
			@php $datas=App\ServiceImage::where('service_id',$data->id)->get(); @endphp
			<div>
				@foreach($datas as $key => $value)
				<input type="hidden" name="hidden_id[]" value="{{$value->id}}">
				<a href="{{asset('service/'.$value->image)}}" target="_blank" download="">
					<!-- <img src="{{asset('sales/'.$value->drawing)}}" height="40" width="40" style="border:1px solid #e3001b"> -->
					<i class="fas fa-download" style="padding: 1vh;font-size: 20px;color: black;"></i>
				</a>
				<label class="kt-avatar__upload" data-toggle="kt-tooltip" title="" data-original-title="Delete">
					<a href="{{ route('user.servicedelete.image',$value['id']) }}" onclick="return confirm('Are you sure you want to remove attachemnt')"><i class="fa fa-trash" style="color: black"></i></a>
				</label>
				@endforeach
			</div>
			<div class="form-group col-lg-3">
				<label>Type</label>
				<select name="type" class="form-control">
					<option value="">Select</option>
					<option value="1" @if($data->type == 1) selected @endif >Complain</option>
					<option value="2" @if($data->type == 2) selected @endif >Demostration</option>
					<option value="3" @if($data->type == 3) selected @endif >Pre Delivery Inspection</option>
					<option value="4" @if($data->type == 4) selected @endif >Installation Guideline</option>
					<option value="5" @if($data->type == 5) selected @endif >After Installation Guideline</option>
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
					@if(isset($child))
					@foreach($child as $keys => $values)
					<tr class="master_clones" data-id="{{$values->id}}" data-master="{{$values->id}}">
						<td>
							<div style="display: inline-flex !important;">
								<a href="{{route('user.service.clone.delete',$values->id)}}" data-id='{{$values->id}}' class="btn btn-sm btn-icon btn-icon-md btn-clean" ><i class="fa fa-minus" style="font-size:0.8rem !important"></i>
								</a>
							</div>
						</td>
						<input type="hidden" name="child_id[]" value="{{$values->id}}">
						<td>
							<input type="text" class="form-control" @if(isset($values->category)) value="{{ $values->category->name }}" @endif readonly>
						</td>
						<td>
							<input type="text" class="form-control" @if(isset($values->company)) value="{{ $values->company->name }}" @endif readonly>
						</td>
						<td>
							<input type="text" class="form-control" @if(isset($values->technician)) value="{{ $values->technician->technician_name }}" @endif readonly>
						</td>
						<td>
							<input type="text" class="form-control" @if(isset($values->product)) value="{{ $values->product->dropdown_name }}" @endif readonly>
						</td>
						<td>
							<input type="number" name="edit_qty[]" class="form-control edit_qty" value="{{$values->qty}}" id="edit_qty_{{$values->id}}" data-id="{{$values->id}}">
						</td>
					</tr>
					@endforeach
					@endif
					<tr class="master_clone" data-id="0" data-master="0">
						<td>
							<div style="display: inline-flex !important;">
								<button type="button" id="add_more" class="btn btn-sm btn-clean btn-icon btn-icon-md add_more"><i class="fa fa-plus" style="font-size:0.8rem !important"></i></button>&nbsp;&nbsp;&nbsp;
								<button type="button" id="remove" data-id='0' class="btn btn-sm btn-icon btn-icon-md btn-clean remove remove" ><i class="fa fa-minus" style="font-size:0.8rem !important"></i></button>
							</div>
						</td>
						@if(Auth::guard('user')->check())
						@if(Auth::user()->role == 1) 
						<td>
							<?php  $productcategory = App\ProductCategory::where('branch_id',$data->branch_id)->where('status',1)->get(); ?>
							<input type="hidden" name="main_id[]" class="main_id">
							<select class="form-control productcategory_id select2" id="productcategory_id_0" name="productcategory_id[]" data-id="0" onchange="getcompany(this)"  >
								<option value="">Select</option>
								@foreach($productcategory as $value)
								<option value="{{$value->id}}">{{$value->name}}</option>
								@endforeach
							</select>
						</td>
						@endif
						@endif
						
						@if(!(Auth::user()->role == 1))
						<td>
							<input type="hidden" name="main_id[]" class="main_id">
							<select class="form-control productcategory_id select2" id="productcategory_id_0" name="productcategory_id[]" data-id="0" onchange="getcompany(this)"  >
								<option value="">Select</option>
								@foreach($productcategory as $value)
								<option value="{{$value->id}}">{{$value->name}}</option>
								@endforeach
							</select>
						</td>
						@endif
						<td>
							<select class="form-control company_id select2" id="company_id_0" name="company_id[]" onchange="getproduct(this)" data-id="0"  >
								<option value="">Select</option>
							</select>
						</td>
						<td>
							<select class="form-control technician_id select2" id="technician_id_0" name="technician_id[]" data-id="0">
								<option value="">Select</option>
							</select>
						</td>
						<td>
							<select class="form-control product_id select2" id="product_id_0" name="product_id[]" data-id="0">
								<option value="">Select</option>
							</select>
						</td>
						<td>
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
		$('#phone_no').val('');
		$('#address').val('');

		$('.productcategory_id').html('<option value="">Select</option>');
		$('.company_id').html('<option value="">Select</option>');
		$('.technician_id').html('<option value="">Select</option>');
		$('.product_id').html('<option value="">Select</option>');
		$('.qty').val('');

		if(branch_id != '')
		{
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
					console.log(data.data);
					$('#employee_id').html(data.data);
					$('#client_id').html(data.data1);
					$('.productcategory_id').html(data.data2);
					$('.edit_productcategory_id').html(data.data2);
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
				console.log(data.data);
				$('#phone_no').val(data.data);
				$('#address').val(data.data1);
			}
		});
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
					$('#company_id_'+id).html(data.data);
					hideLoader();
				}
			});
		}
	};
	function editgetcompany($this){
		var edit_productcategory_id = $($this).val();
		var id = $($this).data('id');
		showLoader();
		$.ajax({
			type:"POST",
			headers: {
				'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
			},
			url:"{{ route('user.service.getcompany') }}",
			data: {
				'edit_productcategory_id': edit_productcategory_id
			},
			success: function(data){
				$('#edit_company_id_'+id).html(data.data1);
				hideLoader();
			}
		});
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
					$('#product_id_'+id).html(data.data);
					$('#technician_id_'+id).html(data.data3);
					hideLoader();
				}
			});
		}
	};
	function editgetproduct($this){
		var edit_company_id = $($this).val();
		var id = $($this).data('id');
		showLoader();
		$.ajax({
			type:"POST",
			headers: {
				'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
			},
			url:"{{ route('user.service.getproduct') }}",
			data: {
				'edit_company_id': edit_company_id
			},
			success: function(data){
				$('#edit_product_id_'+id).html(data.data1);
				hideLoader();
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