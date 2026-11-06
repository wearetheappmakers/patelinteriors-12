<div class="tab-pane" id="edittoshow" role="tabpanel">
	<meta name="csrf-token" content="{{ csrf_token() }}" />
	<form class="kt-form kt-form--label-right form_show" method="POST">
		@csrf
		<input type="hidden" name="lead_id" value="{{ $data->id }}">
		<div class="row form-group fv-row mb-7 fv-plugins-icon-container">
			<div class="col-md-12" >
				<table cellspacing="0" border="0" class="table table-bordered" style='width: 100% !important;'>
					<thead>
						<tr>
							@if(Auth::guard('user')->check())
							<th width="10%"></th>
							@endif
							<th width="15%">Employee</th>
							<th width="20%">Category</th>
							<th width="15%">Description</th>
							<th width="15%">Status</th>
							<th width="15%">Employee Description</th>
						</tr>
					</thead>
					<tbody>
						<?php 
						$lead_show = App\LeadToshow::where('lead_id',$data->id)->get();
						?>
						@if(count($lead_show) > 0)
						@foreach($lead_show as $key => $value)
						<tr class="">
							@if(Auth::guard('user')->check())
							@if(Auth::user()->role == 1 || Auth::user()->role == 2)  
							<td>
								<div style="display: inline-flex !important;">
									<a href="{{route('user.lead.deletetoshow',$value->id)}}"><button type="button" data-id="{{$value->id}}" class="btn btn-sm  btn-icon btn-icon-md" onclick="return confirm('Are you sure you want to delete')"><i class="fa fa-minus" style="font-size:0.9rem !important"></i></button></a>
								</div>
							</td>
							@endif
							@endif
							@if(Auth::guard('employee')->check())
							<?php $emp1 = App\LeadToshow::where('id',$value->id)->where('employee_id',Auth::user()->id)->first(); 
							?>
							
							@if(isset($emp1))
							<td>
								<input type="hidden" name="child_id[]" class="child_id" value="{{$value->id}}">

								<?php  $empl = App\Employee::where('id',$value->employee_id)->value('name'); 
								$emplValue = isset($empl) ? $empl : ''; 
								?>
								<input type="hidden" name="edit_employee_id[]" class="employee_id" value="{{$value->employee_id}}">
								<input type="text"  value="{{$emplValue}}" class="form-control" readonly id="edit_employee_id_{{$value->id}}"  data-id="{{$value->id}}">

							</td>
							
							<td>
								<?php  $cate = App\ProductCategory::where('id',$value->category_id)->value('name'); 
								$cateValue = isset($cate) ? $cate : '';
								?>
								<input type="hidden" name="edit_category_id[]" class="category_id" value="{{$value->category_id}}">

								<input type="text"  value="{{$cateValue}}" class="form-control" readonly id="edit_category_id_{{$value->id}}"  data-id="{{$value->id}}"> 
							</td>
							<td>
								<textarea name="edit_description[]" class="form-control description" id="edit_description_{{$value->id}}" data-id="{{$value->id}}" @if(Auth::guard('employee')->check()) readonly @endif rows="2">{{ $value->description}}</textarea>

							</td>
							<td>
								<select name="edit_status[]" class="form-control form-select-solid status" id="edit_status_{{$value->id}}"  data-id="{{$value->id}}" >
									<option value="">Select</option>
									<option value="1" @if($value->status == 1)selected @endif>Pending</option>
									<option value="2" @if($value->status == 2)selected @endif>Complete</option>
								</select>
							</td>
							<td>
								<textarea name="edit_emp_description[]" placeholder="Employee Description" class="form-control  emp_description" id="edit_emp_description_{{$value->id}}" rows="2" data-id="{{$value->id}}" >{{$value->emp_description}}</textarea>
							</td>
							@endif
							@endif
							@if(Auth::guard('user')->check())
							<td>
								<input type="hidden" name="child_id[]" class="child_id" value="{{$value->id}}">

								<?php  $empl = App\Employee::where('id',$value->employee_id)->value('name'); 
								$emplValue = isset($empl) ? $empl : ''; 
								?>
								<input type="hidden" name="edit_employee_id[]" class="" value="{{$value->employee_id}}">
								<input type="text"  value="{{$emplValue}}" class="form-control " readonly id="edit_employee_id_{{$value->id}}"  data-id="{{$value->id}}">

							</td>
							
							<td>
								<?php  $cate = App\ProductCategory::where('id',$value->category_id)->value('name'); 
								$cateValue = isset($cate) ? $cate : '';
								?>
								<input type="hidden" name="edit_category_id[]" class="" value="{{$value->category_id}}">

								<input type="text"  value="{{$cateValue}}" class="form-control " readonly id="edit_category_id_{{$value->id}}"  data-id="{{$value->id}}"> 
							</td>
							<td>
								<textarea name="edit_description[]" class="form-control description" id="edit_description_{{$value->id}}" data-id="{{$value->id}}" rows="2" @if(Auth::guard('employee')->check()) readonly @endif>{{ $value->description}}</textarea>

							</td>
							<td>
								<select name="edit_status[]" class="form-control form-select-solid status" id="edit_status_{{$value->id}}"  data-id="{{$value->id}}" >
									<option value="">Select</option>
									<option value="1" @if($value->status == 1)selected @endif>Pending</option>
									<option value="2" @if($value->status == 2)selected @endif>Complete</option>
								</select>
							</td>
							<td>
								<textarea name="edit_emp_description[]" placeholder="Employee Description" class="form-control  emp_description" id="edit_emp_description_{{$value->id}}" rows="2" data-id="{{$value->id}}" >{{$value->emp_description}}</textarea>

							</td>
							@endif
						</tr>
						@endforeach
						@endif

						@if(Auth::guard('user')->check())
						@if(Auth::user()->role == 1 || Auth::user()->role == 2)  
						<tr class="master_clone" data-id="0">
							<td>
								<div style="display: inline-flex !important;">
									<button type="button" id="add" class="btn btn-sm btn-icon btn-icon-md btn-clean add"><i class="fa fa-plus" style="font-size:0.9rem !important"></i></button>
									&nbsp;&nbsp;&nbsp;
									<button  type="button" id="minus" class="btn btn-sm btn-icon btn-icon-md btn-clean remove "><i class="fa fa-minus" style="font-size:0.9rem !important;"></i></button>
								</div>
							</td>
							<td>
								<select name="employee_id[]" class="form-control form-select-solid employee_id select2" id="employee_id_0" data-id="0">
									<option value="">Select</option>
									<?php 
									$assignedEmployees = App\LeadAssign::where('lead_id', $data->id)->get();
									foreach ($assignedEmployees as $assignment) {
										$employeeIds = explode(',', $assignment->employee_id);
										foreach ($employeeIds as $employeeId) {
											$employeeId = trim($employeeId);
											$employeeName = App\Employee::where('id', $employeeId)->first();
											if ($employeeName) {
												echo '<option value="' . $employeeName->id . '">' . $employeeName->name . '</option>';
											}
										}
									}
									?>
								</select>
							</td>
							<td>
								<select class="form-control form-select-solid category_id select2" id="category_id_0" name="category_id[]" data-id="0">
									<option value="">Select</option>
									<?php $category = App\ProductCategory::where('branch_id',$data->branch_id)->where('status',1)->get(); ?>
									@foreach($category as $cat)
									<option value="{{$cat->id}}">{{$cat->name}}</option>
									@endforeach
								</select>

							</td>
							<td>
								
								<textarea name="description[]" id="description_0" data-id = "0" placeholder="Enter Description" class="form-control form-control-solid description" rows="1"></textarea>
							</td>
							<td>
								<select name="status[]" class="form-control form-select-solid status" id="status_0" data-id="0" >
									<option value="">Select</option>
									<option value="1">Pending</option>
									<option value="2">Complete</option>
								</select>
							</td>
							<td>
								<textarea   name="emp_description[]" placeholder="Employee Description" class="form-control  emp_description" id="emp_description_0" data-id="0" rows="1"></textarea>

							</td>
						</tr>
						@endif
						@endif
					</tbody>

				</table>
			</div>
		</div>
	</form>
	<div class="kt-portlet__foot">
		<div class="kt-form__actions">
			<center>
				<button type="button" class="btn btn-brand submit" id="submit_show">Submit</button>
				<a href="{{route('user.lead.index')}}"><button type="button" class="btn btn-outline-secondary">Cancel</button></a>
			</center>
		</div>
	</div>
</div>
<script type="text/javascript">
	$(document).ready(function() {
		$("#submit_show").on("click", function(e) {
			e.preventDefault();
			if ($(".form_show").valid()) {
				
				showLoader();

				$.ajax({
					type: "POST",
					url: "{{route('user.lead.toshow')}}",
					data: new FormData($('.form_show')[0]),
					processData: false,
					contentType: false,
					success: function(data) {
						
						hideLoader();

						if (data.status === 'success') {
							location.reload();
							toastr["success"]("Lead Added Successfully", "Success");
						} else if (data.status === 'error') {
							location.reload();
							toastr["error"]("Something went wrong", "Error");
						}
					},
				});
			} else {
				hideLoader();
				e.preventDefault();
			}
		});
	});
	
</script>
<script type="text/javascript">
	var count = 0;
	$(document).on('click','.add',function() {
		count++;	
		var length = $('.master_clone').length;
		console.log(length);

		var $row = $(this).closest('.master_clone');
		var $clone = $row.clone();
		// $clone.find('.employee_id').val('');
		// $clone.find('.category_id').val('');

		$clone.find('.branch_id').val('').attr('data-id',count).attr('id','branch_id_'+count);
		$clone.find('.description').val('').attr('data-id',count).attr('id','description_'+count);
		$clone.find('.status').val('').attr('data-id',count).attr('id','status_'+count);
		$clone.find('.emp_description').val('').attr('data-id',count).attr('id','emp_description_'+count);

		var $clone1 = $row.clone();
		$clone1.find('.employee_id, .category_id').select2();

		$clone.find('.employee_id').val('').css('width','100%').attr('id', 'employee_id_' + count).attr('data-id', count).removeClass('select2-hidden-accessible').next('.select2-container').remove();
		$clone.find('.category_id').val('').css('width','100%').attr('id', 'category_id_' + count).attr('data-id', count).removeClass('select2-hidden-accessible').next('.select2-container').remove();

		$clone.find('.employee_id, .category_id').select2();
		$clone.find('.employee_id, .category_id').select2();
		$('.employee_id, .category_id').select2();

		$row.after($clone);
		
	});
	$(document).on('click','.remove',function(){
		var clone = $('.master_clone').length;
		console.log(clone);

		if (clone != 1) {
			var obj = $(this).closest('.master_clone');
			obj.remove();
		}
	});


	// setInterval(formshow, 1000);
	// function formshow() {
		
	// 	$('.master_clone').each(function(i,val){
	// 		$(this).find('.employee_id').attr('data-id',i).attr('id','employee_id_'+i);
	// 		$(this).find('.branch_id').attr('data-id',i).attr('id','branch_id_'+i);
	// 		$(this).find('.category_id').attr('data-id',i).attr('id','category_id_'+i);
	// 		$(this).find('.description').attr('data-id',i).attr('id','description_'+i);
	// 		$(this).find('.status').attr('data-id',i).attr('id','status_'+i);
	// 		$(this).find('.emp_description').attr('data-id',i).attr('id','emp_description_'+i);

	// 	});
	// }
</script>



