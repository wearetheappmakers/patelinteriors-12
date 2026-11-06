@extends('main')
@section('content')
@php
$update = route('user.'.$resourcePath.'.update',$data->id);
$index = route('user.visits.index');
@endphp
<meta name="csrf-token" content="{{ csrf_token() }}" />
<div class="kt-container  kt-container--fluid  kt-grid_item kt-grid_item--fluid">
	<br>
	<div class="kt-portlet kt-portlet--mobile">
		<div class="kt-portlet__head">
			<div class="kt-portlet__head-label">
				<h3 class="kt-portlet__head-title">
					Edit Interior Visit
				</h3>
			</div>
			<div class="kt-portlet__head-toolbar">
				<div class="kt-portlet__head-wrapper">					
				</div>
			</div>
		</div>
		<div class="kt-portlet__body">
			<div class="accordion" id="accordionExample1">
				<div class="card">
					<div class="card-header" id="headingOne">
						<div class="card-title" data-toggle="collapse" data-target="#collapseOne1" aria-expanded="true" aria-controls="collapseOne1">
							Basic
						</div>
					</div>
					<div id="collapseOne1" class="collapse show" aria-labelledby="headingOne" data-parent="#accordionExample1">
						<div class="card-body">
							<form class="kt-form" method="POST" class="addform" id="addform">
								@csrf
								@method('PUT')
								<div class="row">
									@if(Auth::guard('user')->check())
									@if(Auth::user()->role == 1)  
									<div class="form-group col-lg-2">
										<label>Branch  </label>
										<input type="hidden" name="branch_id" id="branch_id" value="{{ $data->branch_id }}">
										<input type="text" class="form-control" placeholder="Branch" value="{{ $data->branch->name }}" readonly>
									</div>                        
									<div class="form-group col-lg-2">
										<label>Employee  </label>
										<input type="hidden" name="employee_id" id="employee_id" value="{{ $data->employee_id }}">
										<input type="text" class="form-control" placeholder="Employee" value="{{ $data->employee->name }}" readonly>
									</div>
									<div class="form-group col-lg-2">
										<label>Ar/ID  </label>
										<input type="hidden" name="arid" id="arid" value="{{ $data->arid }}">
										<input type="text" class="form-control" placeholder="Ar/ID" value="{{ $data->arids->name }}" readonly>
									</div>
									@endif			
									@endif
									@if(Auth::guard('user')->check())
									@if(Auth::user()->role == 2)
									<div class="form-group col-lg-2"> 
										<label>Employee  </label>
										<select name="employee_id" class="form-control select2" id="employee_id">
											<option value="">Select Employee</option>
											@foreach($employee as $sub)
											<option value="{{$sub->id}}" @if($sub->id == $data->employee_id) selected @endif >{{$sub->name}}</option>
											@endforeach
										</select>
									</div>
									<div class="form-group col-lg-2"> 
										<label>Ar/ID  </label>
										<select name="arid" class="form-control select2" onchange="getemaildata(this)" id="arid">
											<option value="">Select Ar/ID</option>
											@foreach($arid as $ar)
											<option value="{{$ar->id}}" @if($ar->id == $data->arid) selected @endif >{{$ar->name}}</option>
											@endforeach
										</select>
									</div>
									@endif
									@endif
									@if(Auth::guard('employee')->check())
									<div class="form-group col-lg-2"> 
										<label>Ar/ID  </label>
										<select name="arid" class="form-control select2" onchange="getemaildata(this)" id="arid">
											<option value="">Select Ar/ID</option>
											@foreach($arid as $ar)
											<option value="{{$ar->id}}" @if($ar->id == $data->arid) selected @endif >{{$ar->name}}</option>
											@endforeach
										</select>
									</div>
									@endif
									<div class="form-group col-lg-2">
										<label>Email</label>
										<input type="text" class="form-control" placeholder="Enter Email" value="{{$data->email}}" readonly id="email" name="email" >
									</div>
									<div class="form-group col-lg-2">
										<label>Number</label>
										<input type="number" class="form-control" placeholder="Enter Number" value="{{$data->number}}" readonly id="number" name="number" >
									</div>
									<div class="form-group col-lg-2">
										<label>Firm Name</label>
										<input type="text" class="form-control" placeholder="Enter Firm Name" id="firm_name" value="{{$data->firm_name}}" name="firm_name" readonly>
									</div>
									<div class="form-group col-lg-2">
										<label>Assistant</label>
										<input type="text" class="form-control" placeholder="Enter Assistant"  value="{{$data->assistant}}" name="assistant" >
									</div>
									<div class="form-group col-lg-2">
										<label>Date</label>
										<input type="date" class="form-control" placeholder="" value="{{$data->date}}" name="date" >
									</div>
									<div class="form-group col-lg-2">
										<label>Time</label>
										<input type="time" class="form-control" placeholder="" value="{{$data->time}}" name="time" >
									</div>
									<div class="form-group col-lg-2">
										<label>Company Person</label>
										<input type="text" class="form-control" value="{{$data->company_person}}" placeholder="Enter Company Person" name="company_person" >
									</div>
									<div class="form-group col-lg-2">
										<label>Remarks</label>
										<textarea id="remarks" name="remarks" rows="1" placeholder="Enter Remarks" class="form-control">{{$data->remarks}}</textarea>
									</div>
									<div class="form-group col-lg-2">
										<label>Showroom Visit</label>
										<select class="form-control" name="showroom_visit">
											<option value="">Select</option>
											<option value="1" @if($data->showroom_visit == 1) selected @endif>Yes</option>
											<option value="2" @if($data->showroom_visit == 2) selected @endif>No</option>
										</select>
									</div>
								</div>
								<div class="row">
									<div class=" form-group col-sm-12" style="overflow: auto !important">
										<table cellspacing="0" border="0" class="table table-bordered">
											<thead>
												<tr>
													<th></th>
													@if(Auth::guard('user')->check())
													@if(Auth::user()->role == 1)  
													<th>Branch</th>
													@endif
													@endif
													<th>Category</th>
													<th>Company</th>
													<th>Product</th>
												</tr>
											</thead>
											<tbody>
												@if(!empty($clone))
												@foreach($clone as $values)
												<tr class="master_clones" data-id="{{$values->id}}" data-master="{{$values->id}}">
													<td>
														<div style="display: inline-flex !important;">
															<a href="{{route('user.visit.clone.delete',$values->id)}}" data-id='{{$values->id}}' class="btn btn-sm btn-icon btn-icon-md btn-clean" ><i class="fa fa-minus" style="font-size:0.8rem !important"></i>
															</a>
														</div>
													</td>
													@if(Auth::guard('user')->check())
													@if(Auth::user()->role == 1)  
													<td >
														<select class="form-control edit_branch_id select2" id="edit_child_branch_id_{{$values->id}}" data-id="{{$values->id}}" onchange="editgetcategory(this)"  name="edit_child_branch_id[]" style="width: 100%;">
															<option value="">Select</option>
															@foreach($branch as $datas)
															<option value="{{$datas->id}}"  @if($datas->id == $values->branch_id) selected @endif >{{$datas->name}}</option>
															@endforeach
														</select>
													</td>
													<td >
														<?php $productcategory = App\ProductCategory::where('branch_id',$values->branch_id)->get(); ?>
														<input type="hidden" name="child_id[]" value="{{$values->id}}">
														<select class="form-control edit_productcategory_id select2" id="edit_productcategory_id_{{$values->id}}" name="edit_productcategory_id[]" data-id="{{$values->id}}" onchange="editgetcompany(this)"  style="width: 100%;">
															<option value="">Select</option>
															@foreach($productcategory as $value)
															<option value="{{$value->id}}" @if($value->id == $values->category_id) selected @endif >{{$value->name}}</option>
															@endforeach
														</select>
													</td>
													@endif
													@endif
													@if(Auth::guard('user')->check())
													@if(Auth::user()->role == 2)  
													<td >
														<input type="hidden" name="child_id[]" value="{{$values->id}}">
														<select class="form-control edit_productcategory_id select2" id="edit_productcategory_id_{{$values->id}}" name="edit_productcategory_id[]" data-id="{{$values->id}}" onchange="editgetcompany(this)"  style="width: 100%;">
															<option value="">Select</option>
															@foreach($productcategory as $value)
															<option value="{{$value->id}}" @if($value->id == $values->category_id) selected @endif >{{$value->name}}</option>
															@endforeach
														</select>
													</td>
													@endif
													@endif
													@if(Auth::guard('employee')->check())
													<td >
														<input type="hidden" name="child_id[]" value="{{$values->id}}">
														<select class="form-control edit_productcategory_id select2" id="edit_productcategory_id_{{$values->id}}" name="edit_productcategory_id[]" data-id="{{$values->id}}" onchange="editgetcompany(this)"  style="width: 100%;">
															<option value="">Select</option>
															@foreach($productcategory as $value)
															<option value="{{$value->id}}" @if($value->id == $values->category_id) selected @endif >{{$value->name}}</option>
															@endforeach
														</select>
													</td>
													@endif
													<td >
														<?php $company = App\Company::where('pro_category_id',$values->category_id)->get(); ?>
														<select class="form-control edit_company_id select2" id="edit_company_id_{{$values->id}}" name="edit_company_id[]" onchange="editgetproduct(this)" data-id="{{$values->id}}"  style="width: 100%;">
															<option value="">Select</option>
															@foreach($company as $value)
															<option value="{{$value->id}}" @if($value->id == $values->company_id) selected @endif >{{$value->name}}</option>
															@endforeach
														</select>
													</td>
													<td >
														<?php $product = App\Product::where('company_id',$values->company_id)->get(); ?>
														<select class="form-control edit_product_id select2" id="edit_product_id_{{$values->id}}" name="edit_product_id[]" data-id="{{$values->id}}" style="width: 100%;">
															<option value="">Select</option>
															@foreach($product as $value)
															<option value="{{$value->id}}" @if($value->id == $values->product_id) selected @endif >{{$value->dropdown_name}}</option>
															@endforeach
														</select>
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
													<td >
														<select class="form-control child_branch_id select2" id="child_branch_id_0" data-id="0" onchange="getcategory(this)"  name="child_branch_id[]" style="width: 100%;">
															<option value="">Select</option>
															@foreach($branch as $datas)
															<option value="{{$datas->id}}">{{$datas->name}}</option>
															@endforeach
														</select>
													</td>
													<td >
														<input type="hidden" name="main_id[]" class="main_id">
														<select class="form-control productcategory_id select2" id="productcategory_id_0" name="productcategory_id[]" data-id="0" onchange="getcompany(this)"  style="width: 100%;">
															<option value="">Select</option>
														</select>
													</td>
													@endif
													@endif
													@if(Auth::guard('user')->check())
													@if(Auth::user()->role == 2)  
													<td >
														<input type="hidden" name="main_id[]" class="main_id">
														<select class="form-control productcategory_id select2" id="productcategory_id_0" name="productcategory_id[]" data-id="0" onchange="getcompany(this)"  style="width: 100%;">
															<option value="">Select</option>
															@foreach($productcategory as $value)
															<option value="{{$value->id}}">{{$value->name}}</option>
															@endforeach
														</select>
													</td>
													@endif
													@endif
													@if(Auth::guard('employee')->check())
													<td >
														<input type="hidden" name="main_id[]" class="main_id">
														<select class="form-control productcategory_id select2" id="productcategory_id_0" name="productcategory_id[]" data-id="0" onchange="getcompany(this)"  style="width: 100%;">
															<option value="">Select</option>
															@foreach($productcategory as $value)
															<option value="{{$value->id}}">{{$value->name}}</option>
															@endforeach
														</select>
													</td>
													@endif
													<td >
														<select class="form-control company_id select2" id="company_id_0" name="company_id[]" onchange="getproduct(this)" data-id="0"  style="width: 100%;">
															<option value="">Select</option>
														</select>
													</td>
													<td >
														<select class="form-control product_id select2" id="product_id_0" name="product_id[]" data-id="0" style="width: 100%;">
															<option value="">Select</option>
														</select>
													</td>
												</tr>
											</tbody>
										</table>
									</div>
								</div>
							</form>
							<div class="kt-portlet__foot">
								<div class="kt-form__actions">
									<center>
										<button type="submit" class="btn btn-brand submit" id="update">Update</button>
										<a href="{{$index}}"><button type="button" class="btn btn-outline-secondary">Cancel</button></a>
									</center>
								</div>
							</div>
						</div>
					</div>
				</div>
				<div class="card">
					<div class="card-header" id="headingTwo">
						<div class="card-title collapsed"  data-toggle="collapse" data-target="#collapseTwo1" aria-expanded="false" aria-controls="collapseTwo1">
							FollowUp
						</div>
					</div>
					@php $followup = App\IVFollowup::where('iv_id',$data->id)->first(); @endphp
					<div id="collapseTwo1" class="collapse" aria-labelledby="headingTwo1" data-parent="#accordionExample1">
						<div class="card-body">
							<form class="kt-form addform_followup" id="addform_followup">
								@csrf
								<div class="row">
									<div class="form-group col-sm-3">
										<input type="hidden" name="iv_id" value="{{$data->id}}">
										<label>Employee</label>
										<input type="text" class="form-control" readonly value="{{App\Employee::where('id',$data->employee_id)->value('name')}}">
									</div>
									<div class="form-group col-sm-3">
										<label>Subject</label>
										<input type="text" class="form-control" name="subject" @if(isset($followup)) value="{{$followup->subject}}" @endif>
									</div>
									<div class="form-group col-sm-3">
										<label>Description</label>
										<input type="text" class="form-control" name="description" @if(isset($followup)) value="{{$followup->description}}" @endif>
									</div>
									<div class="form-group col-sm-3">
										<label>Schedule Date</label>
										<input type="date" class="form-control" name="iv_date" @if(isset($followup)) value="{{$followup->date}}" @endif>
									</div>
								</div>
								<div class="row">
									<div class="form-group col-sm-3">
										<input type="hidden" name="iv_id" value="{{$data->id}}">
										<label>Schedule Time</label>
										<input type="time" class="form-control" name="iv_time" @if(isset($followup)) value="{{$followup->time}}" @endif>
									</div>
									<div class="form-group col-sm-3">
										<label>Followup Type</label>
										@php $type = App\Type::where('branch_id',$data->branch_id)->get(); @endphp
										<select name="type" class="form-control"  required>
											<option value="">Select Followup Type</option>
											@foreach($type as $sub)
											<option value="{{$sub->id}}" @if(isset($followup)) @if($sub->id == $followup->type) selected @endif @endif>{{$sub->name}}</option>
											@endforeach
										</select>
									</div>
									<div class="form-group col-sm-3">
										<label>Followup Status</label>
										<select class="form-control" name="is_completed" id="is_completed" required>
											<option value="1" @if(isset($followup) && $followup->is_completed == 1) selected @endif>Pending</option>
											<option value="2" @if(isset($followup) && $followup->is_completed == 2) selected @endif>Discussion</option>
											<option value="3" @if(isset($followup) && $followup->is_completed == 3) selected @endif>Rivision</option>
											<option value="4" @if(isset($followup) && $followup->is_completed == 4) selected @endif>Win</option>
											<option value="5" @if(isset($followup) && $followup->is_completed == 5) selected @endif>Lost</option>
											<option value="6" @if(isset($followup) && $followup->is_completed == 6) selected @endif>Showroom Visit</option>
											<option value="7" @if(isset($followup) && $followup->is_completed == 7) selected @endif>Site Measurement</option>
											<option value="8" @if(isset($followup) && $followup->is_completed == 8) selected @endif>Designing</option>
											<option value="9" @if(isset($followup) && $followup->is_completed == 9) selected @endif>Quotation</option>
										</select>
									</div>
									<div class="form-group col-sm-3">
										<label>FollowUp Date</label>
										<input type="date" class="form-control" name="status_date" @if(isset($followup)) value="{{$followup->status_date}}" @endif>
									</div>
								</div>
							</form>
							<div class="kt-portlet__foot">
								<div class="kt-form__actions">
									<center>
										<button type="submit" class="btn btn-brand update_followup" id="update_followup">Update</button>
										<a href="{{$index}}"><button type="button" class="btn btn-outline-secondary">Cancel</button></a>
									</center>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
@stop
@push('scripts')
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
	// $('.select2').select2({
	// });
</script>
<script type="text/javascript">
	$(document).ready(function(){
		$("#update_followup").on("click",function(e){
			e.preventDefault();
			
			if($("#addform_followup").valid()){
				$.ajax({
					type:"POST",
					url:"{{route('user.iv.followup.store')}}",
					data: new FormData($('#addform_followup')[0]),
					processData: false,
					contentType:false,
					success: function (data) {
						if (data.status === 'success') {
							toastr["success"]("Followup Updated Successfully.", "Success");
							window.location = "{{$index}}"
						} 
						else{
							toastr["error"]("Something Wen Wrong", "Error");
							location.reload();
						}
					}
				});
			}else{
				e.preventDefault();
			}
		});
		$("#update").on("click",function(e){
			e.preventDefault();
			
			if($("#addform").valid()){

				showLoader();

				$.ajax({
					type:"POST",
					url:"{{$update}}",
					data: new FormData($('#addform')[0]),
					processData: false,
					contentType:false,
					success: function (data) {
						hideLoader();
						if (data.status === 'success') {
							toastr["success"]("Visit Updated Successfully.", "Success");
							window.location.reload()
						} 
						else{
							toastr["error"]("Something Wen Wrong", "Error");
							location.reload();
						}
					}
				});
			}else{
				hideLoader();
				e.preventDefault();
			}
		});
	});
	$(document).ready(function() {
		var count = 0;
		$(document).on('click','.add_more',function() {
			count++;
			var $row = $(this).closest('.master_clone');
			var $clone = $row.clone();

			// $clone.find('.productcategory_id').val('').attr('data-id',count).attr('id','productcategory_id_'+count);
			// $clone.find('.product_id').val('').attr('data-id',count).attr('id','product_id_'+count);
			// $clone.find('.company_id').val('').attr('data-id',count).attr('id','company_id_'+count);
			// $clone.find('.warehouse_id').val('').attr('data-id',count).attr('id','warehouse_id_'+count);

			var $clone1 = $row.clone();
			$clone1.find('.child_branch_id, .productcategory_id, .company_id, .product_id').select2();

			$clone.find('.child_branch_id').val('').css('width','100%').attr('id', 'child_branch_id_' + count).attr('data-id', count).removeClass('select2-hidden-accessible').next('.select2-container').remove();
			$clone.find('.productcategory_id').val('').css('width','100%').attr('id', 'productcategory_id_' + count).attr('data-id', count).removeClass('select2-hidden-accessible').next('.select2-container').remove();
			$clone.find('.company_id').val('').css('width','100%').attr('id', 'company_id_' + count).attr('data-id', count).removeClass('select2-hidden-accessible').next('.select2-container').remove();
			$clone.find('.product_id').val('').css('width','100%').attr('id', 'product_id_' + count).attr('data-id', count).removeClass('select2-hidden-accessible').next('.select2-container').remove();

			$clone.find('.child_branch_id, .productcategory_id, .company_id, .product_id').select2();
			$clone.find('.child_branch_id, .productcategory_id, .company_id, .product_id').select2();
			$('.child_branch_id, .productcategory_id, .company_id, .product_id').select2();


			$clone.find('.qty').val('').attr('data-id',count).attr('id','qty_'+count);
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

	function editgetcategory($this)
	{
		var edit_child_branch_id = $($this).val();
		var id = $($this).data('id');

		$('#edit_productcategory_id_'+id).html('<option value="">Select</option>');
		$('#edit_company_id_'+id).html('<option value="">Select</option>');
		$('#edit_product_id_'+id).html('<option value="">Select</option>');

		if(edit_child_branch_id != '')
		{
			showLoader();
			$.ajax({
				type:"POST",
				headers: {
					'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
				},
				url:"{{ route('user.visit.getcategory') }}",
				data: {
					'edit_child_branch_id': edit_child_branch_id
				},
				success: function(data){
					$('#edit_productcategory_id_'+id).html(data.data1);
					hideLoader();
				}
			});
		}
	}

	function editgetcompany($this)
	{
		var productcategory_id = $($this).val();
		var id = $($this).data('id');

		$('#edit_company_id_'+id).html('<option value="">Select</option>');
		$('#edit_product_id_'+id).html('<option value="">Select</option>');

		if(productcategory_id != '')
		{
			showLoader();	
			$.ajax({
				type:"POST",
				headers: {
					'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
				},
				url:"{{ route('user.lead.getcompany') }}",
				data: {
					'edit_productcategory_id': productcategory_id
				},
				success: function(data){
					$('#edit_company_id_'+id).html(data.data2);
					hideLoader();
				}
			});
		}
	}

	function editgetproduct($this)
	{
		var company_id = $($this).val();
		var id = $($this).data('id');

		$('#edit_product_id_'+id).html('<option value="">Select</option>');

		if(company_id != '')
		{
			showLoader();

			$.ajax({
				type:"POST",
				headers: {
					'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
				},
				url:"{{ route('user.lead.getproduct') }}",
				data: {
					'edit_company_id': company_id
				},
				success: function(data){
					$('#edit_product_id_'+id).html(data.data2);
					hideLoader();
				}
			});
		}
	}


	function getcategory($this){
		var child_branch_id = $($this).val();
		var id = $($this).data('id');

		$('#productcategory_id_'+id).html('<option value="">Select</option>');
		$('#company_id_'+id).html('<option value="">Select</option>');
		$('#product_id_'+id).html('<option value="">Select</option>');

		if(child_branch_id != '')
		{
			showLoader();

			$.ajax({
				type:"POST",
				headers: {
					'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
				},
				url:"{{ route('user.visit.getcategory') }}",
				data: {
					'child_branch_id': child_branch_id
				},
				success: function(data){
					$('#productcategory_id_'+id).html(data.data);
					hideLoader();
				}
			});
		}
	};

	function getcompany($this){
		var productcategory_id = $($this).val();
		var id = $($this).data('id');

		$('#company_id_'+id).html('<option value="">Select</option>');
		$('#product_id_'+id).html('<option value="">Select</option>');

		if(productcategory_id != '')
		{
			showLoader();

			$.ajax({
				type:"POST",
				headers: {
					'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
				},
				url:"{{ route('user.lead.getcompany') }}",
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
	function getproduct($this){
		var company_id = $($this).val();
		var id = $($this).data('id');

		$('#product_id_'+id).html('<option value="">Select</option>');

		if(company_id != '')
		{
			showLoader();

			$.ajax({
				type:"POST",
				headers: {
					'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
				},
				url:"{{ route('user.lead.getproduct') }}",
				data: {
					'company_id': company_id
				},
				success: function(data){
					$('#product_id_'+id).html(data.data);
					hideLoader();
				}
			});
		}
	};
	function getemaildata($this){
		var arid = $($this).val();
		var id = $($this).data('id');
		$.ajax({
			type:"POST",
			headers: {
				'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
			},
			url:"{{ route('user.interiorvisit.getemaildata') }}",
			data: {
				'arid': arid
			},
			success: function(data){
				$('#email').val(data.data);
				$('#number').val(data.data1);
				$('#firm_name').val(data.data2);
			}
		});
	};
</script>
@endpush