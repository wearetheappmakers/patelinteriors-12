@extends('main')
@section('content')
<meta name="csrf-token" content="{{ csrf_token() }}" />
<div class="kt-container  kt-container--fluid  kt-grid_item kt-grid_item--fluid">
	<br>
	<div class="kt-portlet kt-portlet--mobile">
		<div class="kt-portlet__head">
			<div class="kt-portlet__head-label">
				<h3 class="kt-portlet__head-title">
					Add Interior Visit
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
							<form class="kt-form" class="addform" id="addform">
								@csrf
								<div class="row">
									@if(Auth::guard('user')->check())
									@if(Auth::user()->role == 1)  
									<div class="form-group col-sm-2">
										<label>Branch</label>
										<select class="form-control select2" id="branch_id" onchange="getemployee(this)"  name="branch_id" required>
											<option value="">Select</option>
											@foreach($branch as $datas)
											<option value="{{$datas->id}}">{{$datas->name}}</option>
											@endforeach
										</select>
									</div>
									<div class="form-group col-sm-2">
										<label>Employee</label>
										<select class="form-control select2" id="employee_id"  name="employee_id" required>
											<option value="">Select</option>
										</select>
									</div>
									<div class="form-group col-sm-2">
										<label>Ar/ID</label>
										<select class="form-control select2" id="arid" onchange="getemaildata(this)" name="arid" required>
											<option value="">Select</option>
										</select>
									</div>
									@endif
									@endif
									@if(Auth::guard('user')->check())
									@if(Auth::user()->role == 2)
									<div class="form-group col-sm-2">
										<label>Employee</label>
										<select class="form-control select2" id="employee_id"  name="employee_id" required>
											<option value="">Select</option>
											@foreach($employee as $datas)
											<option value="{{$datas->id}}">{{$datas->name}}</option>
											@endforeach
										</select>
									</div>
									<div class="form-group col-sm-2">
										<label>Ar/ID</label>
										<select class="form-control select2" id="arid" onchange="getemaildata(this)" name="arid" required>
											<option value="">Select</option>
											@foreach($arid as $datas)
											<option value="{{$datas->id}}">{{$datas->name}}</option>
											@endforeach
										</select>
									</div>
									@endif
									@endif
									@if(Auth::guard('employee')->check())
									<div class="form-group col-sm-2">
										<label>Ar/ID</label>
										<select class="form-control select2" id="arid" onchange="getemaildata(this)" name="arid" required>
											<option value="">Select</option>
											@foreach($arid as $datas)
											<option value="{{$datas->id}}">{{$datas->name}}</option>
											@endforeach
										</select>
									</div>
									@endif
									<div class="form-group col-lg-2">
										<label>Email</label>
										<input type="text" class="form-control" placeholder="Enter Email" id="email" name="email" readonly>
									</div>
									<div class="form-group col-lg-2">
										<label>Number</label>
										<input type="number" class="form-control" placeholder="Enter Number" id="number" name="number" readonly>
									</div>
									<div class="form-group col-lg-2">
										<label>Firm Name</label>
										<input type="text" class="form-control" placeholder="Enter Firm Name" id="firm_name" name="firm_name" readonly>
									</div>
									<div class="form-group col-lg-2">
										<label>Assistant</label>
										<input type="text" class="form-control" placeholder="Enter Assistant"  name="assistant" >
									</div>
									<div class="form-group col-lg-2">
										<label>Date</label>
										<input type="date" class="form-control" placeholder="" value="{{date('Y-m-d')}}" name="date" >
									</div>
									<div class="form-group col-lg-2">
										<label>Time</label>
										<input type="time" class="form-control" name="time">
									</div>
									<div class="form-group col-lg-2">
										<label>Company Person</label>
										<input type="text" class="form-control" placeholder="Enter Company Person" name="company_person" >
									</div>
									<div class="form-group col-lg-2">
										<label>Remarks</label>
										<textarea id="remarks" name="remarks" rows="1" placeholder="Enter Remarks" class="form-control"></textarea>
									</div>
									<div class="form-group col-lg-2">
										<label>Showroom Visit</label>
										<select class="form-control" name="showroom_visit">
											<option value="">Select</option>
											<option value="1">Yes</option>
											<option value="2">No</option>
										</select>
									</div>
								</div>
								<div class="row">
									<div class=" form-group col-sm-12" style="overflow: auto !important">
										<table cellspacing="0" border="0" class="table table-bordered">
											<thead>
												<tr>
													<th width="10%"></th>
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
														<select class="form-control child_branch_id select2" id="child_branch_id_0" data-id="0" onchange="getcategory(this)"  name="child_branch_id[]" style="width: 100%;">
															<option value="">Select</option>
															@foreach($branch as $datas)
															<option value="{{$datas->id}}">{{$datas->name}}</option>
															@endforeach
														</select>
													</td>
													<td>
														<input type="hidden" name="main_id[]" class="main_id">
														<select class="form-control productcategory_id select2" id="productcategory_id_0" name="productcategory_id[]" data-id="0" onchange="getcompany(this)"  style="width: 100%;">
															<option value="">Select</option>
														</select>
													</td>
													@endif
													@endif
													@if(Auth::guard('user')->check())
													@if(!(Auth::user()->role == 1))  
													<td>
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
													<td>
														<input type="hidden" name="main_id[]" class="main_id">
														<select class="form-control productcategory_id select2" id="productcategory_id_0" name="productcategory_id[]" data-id="0" onchange="getcompany(this)"  style="width: 100%;">
															<option value="">Select</option>
															@foreach($productcategory as $value)
															<option value="{{$value->id}}">{{$value->name}}</option>
															@endforeach
														</select>
													</td>
													@endif
													<td>
														<select class="form-control company_id select2" id="company_id_0" name="company_id[]" onchange="getproduct(this)" data-id="0" style="width: 100%;" >
															<option value="">Select</option>
														</select>
													</td>
													<td>
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
										<button type="submit" class="btn btn-brand submit" id="submit">Submit</button>
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
					<div id="collapseTwo1" class="collapse" aria-labelledby="headingTwo1" data-parent="#accordionExample1">
						<div class="card-body">
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
	$(document).ready(function(){
		$("#submit").on("click",function(e){
			e.preventDefault();
			if($("#addform").valid()){

				showLoader();

				$.ajax({
					type:"POST",
					url:"{{ route('user.visits.store') }}",
					data: new FormData($('#addform')[0]),
					processData: false,
					contentType:false,
					success: function(data){

						hideLoader();

						if(data.status==='success'){
							console.log(data.id);
							toastr["success"]("Visit Added Successfully.", "Success");
							var url = '{{ route("user.visits.edit", ":id") }}';
							url = url.replace(':id', data.id);
							window.location=url;
						}else if(data.status==='error'){
							toastr["error"]("Unsuccessfull", "Error");
							location.reload();
						}
						else if(data.status==='password'){
							toastr["error"]("Password Not Match", "Error");
							location.reload();
						}
						else if (data.status === 'email_exists') {
							toastr["error"]("Duplicate Email !!!", "Error");
							$('.change_button').prop('disabled', false);
							$('.change_button').find('.change_spin').addClass('d-none');
						}
					}
				});
			}else{
				hideLoader();
				e.preventDefault();
			}
		});
	});
	// $('.select2').select2({
	// });
</script>
<script type="text/javascript">
	$(document).ready(function() {
		var count = 0;
		$(document).on('click','.add_more',function() {
			count++;
			var $row = $(this).closest('.master_clone');
			var $clone = $row.clone();
			// $clone.find('.productcategory_id').val('').attr('data-id',count).attr('id','productcategory_id_'+count);
			// $clone.find('.child_branch_id').val('').attr('data-id',count).attr('id','child_branch_id_'+count);
			// $clone.find('.product_id').val('').attr('data-id',count).attr('id','product_id_'+count);
			// $clone.find('.company_id').val('').attr('data-id',count).attr('id','company_id_'+count);
			// $clone.find('.warehouse_id').val('').attr('data-id',count).attr('id','warehouse_id_'+count);
			$clone.find('.qty').val('').attr('data-id',count).attr('id','qty_'+count);

			var $clone1 = $row.clone();
			$clone1.find('.child_branch_id, .productcategory_id, .company_id, .product_id').select2();

			$clone.find('.child_branch_id').val('').css('width','100%').attr('id', 'child_branch_id_' + count).attr('data-id', count).removeClass('select2-hidden-accessible').next('.select2-container').remove();
			$clone.find('.productcategory_id').val('').css('width','100%').attr('id', 'productcategory_id_' + count).attr('data-id', count).removeClass('select2-hidden-accessible').next('.select2-container').remove();
			$clone.find('.company_id').val('').css('width','100%').attr('id', 'company_id_' + count).attr('data-id', count).removeClass('select2-hidden-accessible').next('.select2-container').remove();
			$clone.find('.product_id').val('').css('width','100%').attr('id', 'product_id_' + count).attr('data-id', count).removeClass('select2-hidden-accessible').next('.select2-container').remove();

			$clone.find('.child_branch_id, .productcategory_id, .company_id, .product_id').select2();
			$clone.find('.child_branch_id, .productcategory_id, .company_id, .product_id').select2();
			$('.child_branch_id, .productcategory_id, .company_id, .product_id').select2();

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
	function getemployee($this){
		var branch_id = $($this).val();
		var id = $($this).val();

		$('#employee_id').html('<option value="">Select</option>');
		$('#arid').html('<option value="">Select</option>');
		$('#email').val('');
		$('#number').val('');

		if(branch_id != '')
		{
			showLoader();
			$.ajax({
				type:"POST",
				headers: {
					'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
				},
				url:"{{ route('user.visit.getemployee') }}",
				data: {
					'branch_id': branch_id
				},
				success: function(data){
					hideLoader();
					$('#employee_id').html(data.data);
					$('#arid').html(data.data3);
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