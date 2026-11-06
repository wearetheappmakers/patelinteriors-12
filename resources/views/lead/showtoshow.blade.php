<div class="tab-pane" id="edittoshow" role="tabpanel">
	<meta name="csrf-token" content="{{ csrf_token() }}" />
	<form class="kt-form kt-form--label-right form_show" method="POST">
		@csrf
		<input type="hidden" name="lead_id" value="{{ $data->id }}">
		<div class="row form-group fv-row mb-7 fv-plugins-icon-container">
			<div class="col-md-12" style="overflow: auto !important">
				<table cellspacing="0" border="0" class="table table-bordered" style='width: 100% !important;'>
					<thead>
						<tr>
							
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
						@if(isset($lead_show))
						@foreach($lead_show as $key => $value)
						<tr>
							@if(Auth::guard('employee')->check())
							<?php $emp1 = App\LeadToshow::where('id',$value->id)->where('employee_id',Auth::user()->id)->first(); 
							?>
							
							@if(isset($emp1))

							<td>
								<input type="hidden" name="child_id" class="child_id" value="{{$value->id}}">
								<?php  $empl = App\Employee::where('id',$value->employee_id)->value('name'); 
								$emplValue = isset($empl) ? $empl : ''; 
								?>
								<input type="text" name="edit_employee_id[]" value="{{$emplValue}}" class="form-control employee_id" readonly id="employee_id_0" >

							</td>
							
							<td>
								<?php  $cate = App\ProductCategory::where('id',$value->category_id)->value('name'); 
								$cateValue = isset($cate) ? $cate : '';
								?>
								<input type="text" name="edit_category_id[]" value="{{$cateValue}}" class="form-control category_id" readonly id="category_id_0" >
							</td>
							<td>
								<input type="text" name="edit_description[]" placeholder="Enter Description" class="form-control description" id="description_0" data-id="0" value="{{ $value->description }}" 	 readonly>
							</td>
							<td>
								<select name="edit_status[]" class="form-control form-select-solid status" disabled id="status_0" data-id="0" >
									<option value="">Select</option>
									<option value="1" @if($value->status == 1)selected @endif>Pending</option>
									<option value="2" @if($value->status == 2)selected @endif>Complete</option>
								</select>
							</td>
							<td>
								<input type="text" name="edit_emp_description[]" placeholder="Employee Description" class="form-control  emp_description" id="emp_description_0" data-id="0"  value="{{ $value->emp_description }}" readonly>
							</td>
							@endif
							@endif
							@if(Auth::guard('user')->check())
							<td>
								<input type="hidden" name="child_id" class="child_id" value="{{$value->id}}">
								<?php  $empl = App\Employee::where('id',$value->employee_id)->value('name'); 
								$emplValue = isset($empl) ? $empl : ''; 
								?>
								<input type="text" name="edit_employee_id[]" value="{{$emplValue}}" class="form-control employee_id" readonly id="employee_id_0" >

							</td>
							
							<td>
								<?php  $cate = App\ProductCategory::where('id',$value->category_id)->value('name'); 
								$cateValue = isset($cate) ? $cate : '';
								?>
								<input type="text" name="edit_category_id[]" value="{{$cateValue}}" class="form-control category_id" readonly id="category_id_0" >
							</td>
							<td>
								<input type="text" name="edit_description[]" placeholder="Enter Description" class="form-control description" id="description_0" data-id="0" value="{{ $value->description }}" 	 readonly>
							</td>
							<td>
								<select name="edit_status[]" class="form-control form-select-solid status" disabled id="status_0" data-id="0" >
									<option value="">Select</option>
									<option value="1" @if($value->status == 1)selected @endif>Pending</option>
									<option value="2" @if($value->status == 2)selected @endif>Complete</option>
								</select>
							</td>
							<td>
								<input type="text" name="edit_emp_description[]" placeholder="Employee Description" class="form-control  emp_description" id="emp_description_0" data-id="0"  value="{{ $value->emp_description }}" readonly>
							</td>
							@endif
						</tr>
						@endforeach
						@endif

					</tbody>
				</table>
			</div>
		</div>
	</form>
	<div class="kt-portlet__foot">
		<div class="kt-form__actions">
			<center>
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
				$.ajax({
					type: "POST",
					url: "{{route('user.lead.toshow')}}",
					data: new FormData($('.form_show')[0]),
					processData: false,
					contentType: false,
					success: function(data) {
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
		
		var $row = $(this).closest('.master_clone');
		var $clone = $row.clone();
		$clone.find('.employee_id').val('');
		$clone.find('.branch_id').val('');
		$clone.find('.category_id').val('');

		$clone.find('.description').val('');
		$clone.find('.status').val('');
		$clone.find('.emp_description').val('');
		$row.after($clone);
		
	});
	$(document).on('click','.remove',function(){
		var clone = $('.master_clone').length;

		if (clone != 1) {
			var obj = $(this).closest('.master_clone');
			obj.remove();
		}
	});


	setInterval(formshow, 1000);
	function formshow() {
		
		$('.master_clone').each(function(i,val){
			$(this).find('.employee_id').attr('data-id',i).attr('id','employee_id_'+i);
			$(this).find('.branch_id').attr('data-id',i).attr('id','branch_id_'+i);
			$(this).find('.category_id').attr('data-id',i).attr('id','category_id_'+i);
			$(this).find('.description').attr('data-id',i).attr('id','description_'+i);
			$(this).find('.status').attr('data-id',i).attr('id','status_'+i);
			$(this).find('.emp_description').attr('data-id',i).attr('id','emp_description_'+i);

		});
	}
</script>



