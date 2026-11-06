<meta name="csrf-token" content="{{ csrf_token() }}" />
<div class="kt-portlet__foot">
	<div class="row">
		@if(Auth::guard('user')->check())
		@if(Auth::user()->role == 1)  
		<div class="form-group col-lg-2">
			<label>Branch  </label>
			<input type="hidden" id="branch_id" value="{{ $data->branch_id }}">
			<input type="text" class="form-control" placeholder="Branch" value="{{ ($data->branch_id) ? $data->branch->name : '' }}" readonly>
		</div>
		<div class="form-group col-sm-2">
			<label>Vendor</label>
			<input type="text" class="form-control" placeholder="Branch" value="{{ ($data->vendor_id) ? $data->vendor->name : '' }}" readonly>
		</div>
		@endif
		@endif
		@if(Auth::guard('user')->check())
		@if(!(Auth::user()->role == 1))
		<div class="form-group col-sm-2">
			<label>Vendor </label>
			<input type="text" class="form-control" placeholder="Branch" value="{{ ($data->vendor_id) ? $data->vendor->name : '' }}" readonly>
		</div>
		@endif
		@endif
		@if(Auth::guard('employee')->check())
		<div class="form-group col-sm-2">
			<label>Vendor </label>
			<input type="text" class="form-control" placeholder="Branch" value="{{ ($data->vendor_id) ? $data->vendor->name : '' }}" readonly>
		</div>
		@endif
		<div class="form-group col-sm-2">
			<label>Invoice</label>
			<input type="text" class="form-control" value="{{ ($data->invoice_id) ? ($data->invoices->invoice_no) : '' }}" readonly>
		</div>
		<div class="form-group col-sm-2">
			<label>Date</label>
			<input type="date" name="date" value="{{$data->date}}" placeholder="Enter Date" class="form-control" >
		</div>
		<div class="form-group col-sm-2">
			<label>Debit Order No</label>
			<input type="text" name="order_no" value="{{$data->order_no}}" placeholder="Enter Order No" class="form-control" >
		</div>
		<div class="form-group col-sm-2">
			<label>Remark</label>
			<textarea name="remark" class="form-control" placeholder="Enter Remark">{{$data->remark}}</textarea>
		</div>
		<?php $vendor_state = App\Vendor::where('id',$data->vendor_id)->value('state_id'); ?>
		<input type="hidden" id="state" value="{{$vendor_state}}">
	</div>
	<div class="row">
		<div class="form-group col-sm-12 master_clone_div_container">
			<table cellspacing="0" border="0" class="table table-bordered" id="clone_table" style="width: 100% !important;">
				<thead>
					<tr>
						<th style="font-size: 15px !important; width: 1% !important;"></th>
						<th style="font-size: 15px !important; width: 15% !important;">Product Name & Code</th>
						<th style="font-size: 15px !important; width: 10% !important;">Warehouse</th>
						<th style="font-size: 15px !important; width: 10% !important;">Rem Invoice Qty<br>Qty</th>
						<th style="font-size: 15px !important; width: 10% !important;">Rate</th>
						<th style="font-size: 15px !important; width: 10% !important;">Dis(%)<br>Dis Amt</th>
						<th style="font-size: 15px !important; width: 10% !important;">GST<br>GST Amt</th>
						<th style="font-size: 15px !important; width: 8% !important" class="cgstdiv">CGST<br>CGST Amt</th>
						<th style="font-size: 15px !important; width: 8% !important" class="cgstdiv">SGST<br>SGST Amt</th>
						<th style="font-size: 15px !important; width: 8% !important" class="igstdiv">IGST<br>IGST Amt</th>
						<th style="font-size: 15px !important; width: 10% !important;">Final Amt.</th>
					</tr>
				</thead>
				<tbody id="getdata">
					@foreach($child as $key => $value)
					<tr class = "edit_div_clone" >
						<td>
							@if(count($child) > 1)
							<div  style="display: inline-flex !important;">
								<button type="button" id="edit_remove_container_details_{{$value->id}}" data-value="{{$value->id}}" class="btn btn-sm btn-icon btn-icon-md btn-clean edit_remove_container_details" ><i class="fa fa-minus" style="font-size:0.8rem !important"></i></button>
							</div>
							@endif
						</td>
						<td style="font-size: 15px !important;">
							<input type="hidden" name="edit_main_id[]" value="{{$value->id}}">

							{{ ($value->product_id) ? $value->product->product : '' }} <br>
							{{ ($value->product_id) ? $value->product->code : '' }}

							<input type="hidden" style="width: 100% !important;" readonly class="edit_product_name" name="edit_product_name[]" id="edit_product_name_{{$value->id}}" placeholder="Enter Name" data-id="{{$value->id}}" value="{{$value->product_id}}" >
							<input type="hidden" style="width: 100% !important;" class="edit_product_code" value="{{ ($value->product_id) ? $value->product->code : '' }}" name="edit_product_code[]" id="edit_product_code_{{$value->id}}" placeholder="Enter Price" data-id="{{$value->id}}" readonly  >
						</td>
						<td style="font-size: 15px !important;">
							<select class="edit_warehouse_id" name="edit_warehouse_id[]" style="width: 100% !important;" id="edit_warehouse_id_{{$value->id}}" data-id="{{$value->id}}" >
								@foreach($warehouses as $key1 => $value1)
								<option value="{{$value1->id}}" @if($value1->id == $value->warehouse_id) selected @endif >{{$value1->name}}</option>
								@endforeach
							</select>
						</td>
						<td style="font-size: 15px !important;">
							<?php
							$pending_return_qty = App\PurchaseInvoiceChild::where("id",$value->invoice_child_id)->value('pending_return_qty');
							?>
							<input type="number" style="width: 100% !important;" class="pending_return_qty" id="pending_return_qty_{{$value->id}}" data-id="{{$value->id}}" value="{{ $pending_return_qty }}" readonly>
							<input type="number" style="width: 100% !important;" class="edit_qty" id="edit_qty_{{$value->id}}" placeholder="Enter Qty" name="edit_qty[]" value="{{$value->qty}}" data-id="{{$value->id}}"  >
						</td>
						<td style="font-size: 15px !important;">
							<input type="number" style="width: 100% !important;" class="edit_rate" id="edit_rate_{{$value->id}}" value="{{$value->rate}}" placeholder="Enter Rate"  name="edit_rate[]" data-id="{{$value->id}}">
						</td>
						<td style="font-size: 15px !important;">
							<input type="number" style="width: 100% !important;" class="edit_discount" id="edit_discount_{{$value->id}}" value="{{$value->discount}}" placeholder="Enter Discount(%)" name="edit_discount[]" data-id="{{$value->id}}" readonly><br>
							<input type="number" style="width: 100% !important;" class="edit_discount_amount" id="edit_discount_amount_{{$value->id}}" value="{{$value->discount_amount}}" placeholder="Enter Discount Amount" name="edit_discount_amount[]" data-id="{{$value->id}}" >
						</td>
						<td style="font-size: 15px !important;">
							<?php
							if(Auth::guard('user')->check())
							{
								if(Auth::user()->role == 1)
								{
									$gst = App\GST::where('branch_id',$data->branch_id)->get();
								}
								if(Auth::user()->role == 2)
								{
									$gst = App\GST::where('branch_id',Auth::user()->id)->get();
								}
							}
							else
							{
								$gst = App\GST::where('branch_id',Auth::user()->branch_id)->get();
							}
							?>
							<select style="width: 100% !important;" class="edit_gst" name="edit_gst[]" id="edit_gst_{{$value->id}}" data-id="{{$value->id}}">
								<option value="">Select</option>
								@foreach ($gst as $key1 => $value1)
								<option value="{{$value1->id}}" @if($value->gst == $value1->id) selected @endif>{{$value1->gst}}</option>
								@endforeach
							</select><br>
							<input type="number" style="width: 100% !important;" class="edit_gst_amt" id="edit_gst_amt_{{$value->id}}" value="{{$value->gst_amt}}" placeholder="Enter GST Amount" name="edit_gst_amt[]" data-id="{{$value->id}}" readonly>
						</td>
						<td style="font-size: 15px !important;" class="edit_cgstdiv" data-id="{{$value->id}}" id="edit_cgstdiv_{{$value->id}}">
							<input type="number" style="width: 100% !important;" class="edit_cgst" id="edit_cgst_{{$value->id}}" placeholder="CGST" name="edit_cgst[]" data-id="{{$value->id}}" readonly><br>
							<input type="number" style="width: 100% !important;" readonly class="edit_cgst_amt" id="edit_cgst_amt_{{$value->id}}" placeholder="CGST Amt" name="edit_cgst_amt[]" data-id="{{$value->id}}">
						</td>
						<td style="font-size: 15px !important;" class="edit_sgstdiv" data-id="{{$value->id}}" id="edit_sgstdiv_{{$value->id}}">
							<input type="number" style="width: 100% !important;" class="edit_sgst" id="edit_sgst_{{$value->id}}" placeholder="SGST" name="edit_sgst[]" data-id="{{$value->id}}" readonly><br>
							<input type="number" style="width: 100% !important;" readonly class="edit_sgst_amt" id="edit_sgst_amt_{{$value->id}}" placeholder="SGST Amt" name="edit_sgst_amt[]" data-id="{{$value->id}}">
						</td>
						<td style="font-size: 15px !important;" class="edit_igstdiv" data-id="{{$value->id}}" id="edit_igstdiv_{{$value->id}}">
							<input type="number" style="width: 100% !important;" class="edit_igst" id="edit_igst_{{$value->id}}" placeholder="IGST" name="edit_igst[]" data-id="{{$value->id}}" readonly><br>
							<input type="number" style="width: 100% !important;" readonly class="edit_igst_amt" id="edit_igst_amt_{{$value->id}}" placeholder="IGST Amt" name="edit_igst_amt[]" data-id="{{$value->id}}">
						</td>
						<td style="font-size: 15px !important;">
							<input type="number" style="width: 100% !important;" class="edit_amount" id="edit_amount_{{$value->id}}" value="{{$value->amount}}" name="edit_amount[]" data-id="{{$value->id}}" readonly  >
						</td>
					</tr>
					@endforeach
				</tbody>
				<tfoot>
					<tr>
						<td></td>
						<td></td>
						<td></td>
						<td><input type="number" name="total_qty" style="font-size: 15px !important; width: 100% !important;" class="total_qty" id="total_qty" readonly></td>
						<td></td>
						<td><input type="number" name="total_discount" style="font-size: 15px !important; width: 100% !important;" class="total_discount" id="total_discount" readonly></td>
						<td><input type="number" style="font-size: 15px !important; width: 100% !important;" name="total_gst" class="total_gst" id="total_gst" readonly></td>
						<td class="cgstdiv"><input type="number" style="font-size: 15px !important; width: 100% !important;" name="total_cgst" class="total_cgst" id="total_cgst" readonly></td>
						<td class="cgstdiv"><input type="number" style="font-size: 15px !important; width: 100% !important;" name="total_sgst" class="total_sgst" id="total_sgst" readonly></td>
						<td class="igstdiv"><input type="number" style="font-size: 15px !important; width: 100% !important;" name="total_igst" class="total_igst" id="total_igst" readonly></td>
						<td><input type="number" name="final_amount" style="font-size: 15px !important; width: 100% !important;" class="final_amount" id="final_amount" readonly></td>
					</tr>
				</tfoot>
			</table>
		</div>
	</div>
</div>
<script type="text/javascript">
	$(document).on('click','.remove_container_details',function(){
		var num_of_master_clone_div_payment = $('.div_clone').length;
		if (num_of_master_clone_div_payment != 1) {
			var obj = $(this).closest('.div_clone');
			obj.remove();
		}
	});

	function getclient($this){
		var branch_id = $($this).val();
		var id = $($this).data('id');
		$.ajax({
			type:"POST",
			headers: {
				'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
			},
			url:"{{ route('user.credit.getclient') }}",
			data: {
				'branch_id': branch_id
			},
			success: function(data){
				$('#vendor_id').html(data.data2);
				$('#category_id').html(data.data1);
			}
		});
	};
	$(".edit_remove_container_details").on("click", function(e)
	{
		var num_of_master_clone_div_payment = $('.edit_div_clone').length;
		if (num_of_master_clone_div_payment != 1)
		{
			if (confirm("Are you sure want to delete this item??")) {
				var id = $(this).data('value');
				var url = "{{ route('user.debit.deletechild',":rowid") }}";
				url = url.replace(':rowid',id);
				showLoader();
				$.ajax({
					type: "GET",
					url: url,
					processData: false,
					contentType: false,
					success: function(data) {
						hideLoader();
						if (data.status === 'success') {
							location.reload();
						} else if (data.status === 'error') {
							location.reload();
						}
					}
				});
			}
		}
	});
	function getcompany($this)
	{
		var category_id = $($this).val();

		$('#company_id').html('<option value="">Select</option>');
		$('#product_id').html('<option value="">Select</option>');

		if(category_id != '')
		{
			showLoader();

			$.ajax({
				type:"POST",
				headers: {
					'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
				},
				url:"{{ route('user.po.getcompany') }}",
				data: {
					'category_id': category_id
				},
				success: function(data){
					$('#company_id').html(data.data);

					hideLoader();
				}
			});
		}
	}
	function getwarehouse($this){
		var product_id = $($this).val();
		var id = $($this).data('id');
		$.ajax({
			type:"POST",
			headers: {
				'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
			},
			url:"{{ route('user.lead.getwarehouse') }}",
			data: {
				'product_id': product_id
			},
			success: function(data){
				$('#warehouse_id_'+id).html(data.data);
			}
		});
	};
	function getproduct($this)
	{
		var company_id = $($this).val();
		
		$('#product_id').html('<option value="">Select</option>');

		if(company_id != '')
		{
			showLoader();

			$.ajax({
				type:"POST",
				headers: {
					'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
				},
				url:"{{ route('user.po.getproduct') }}",
				data: {
					'company_id': company_id
				},
				success: function(data){
					$('#product_id').html(data.data);

					hideLoader();
				}
			});
		}
	}
	function getdata($this){
		var product_id =$($this).val();
		var branch_id = $('#branch_id').val();
		if(product_id){
			$.ajax({
				type:"POST",
				headers:{
					'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
				},
				url:"{{route('user.debit.getdata')}}",
				data:{
					'product_id':product_id,
					'branch_id':branch_id
				},
				success: function(data){
					$('#getdata').prepend(data.data);
				}
			});
		}else{
			$('#getdata').html('');
		}
	};
</script>
<script type="text/javascript">
	setInterval(gettotal, 1000);
	function gettotal()
	{
		var state = $('#state').val();
		if (state == 12)
		{
			$(".cgstdiv").show();
			$(".sgstdiv").show();
			$(".igstdiv").hide();

			$(".edit_cgstdiv").show();
			$(".edit_sgstdiv").show();
			$(".edit_igstdiv").hide();
		}
		else{
			$(".cgstdiv").hide();
			$(".sgstdiv").hide();
			$(".igstdiv").show();

			$(".edit_cgstdiv").hide();
			$(".edit_sgstdiv").hide();
			$(".edit_igstdiv").show();
		}

		var total_qty = 0;
		var edit_total_qty = 0;
		var total_discount = 0;
		var edit_total_discount = 0;
		var total_gst = 0;
		var edit_total_gst = 0;
		var total_cgst = 0;
		var edit_total_cgst = 0;
		var total_sgst = 0;
		var edit_total_sgst = 0;
		var total_igst = 0;
		var edit_total_igst = 0;
		var final_amount = 0;
		var edit_final_amount = 0;
		var flag_qty = false;

		$('.edit_qty').each(function(){
			var edit_id = $(this).data('id');
			var edit_qty = $('#edit_qty_'+edit_id).val();
			var edit_rate = $('#edit_rate_'+edit_id).val();
			var edit_discount_amount = $('#edit_discount_amount_'+edit_id).val();
			var pending_return_qty = $('#pending_return_qty_'+edit_id).val();
			var edit_gst = $('#edit_gst_' + edit_id + ' option:selected').text();

			edit_qty = parseFloat(edit_qty) || 0;
			edit_rate = parseFloat(edit_rate) || 0;
			pending_return_qty = parseFloat(pending_return_qty) || 0;
			edit_discount_amount = parseFloat(edit_discount_amount) || 0;
			edit_gst = parseFloat(edit_gst) || 0;

			var edit_amount = parseFloat(edit_qty) * parseFloat(edit_rate); 
			if(edit_discount_amount)
            {
                var edit_discount = (100*parseFloat(edit_discount_amount))/parseFloat(edit_amount);
                $('#edit_discount_'+edit_id).val(parseFloat(edit_discount).toFixed(2));
            }else{
                $('#edit_discount_'+edit_id).val(0);
            }
			var edit_dis_amt = parseFloat(edit_amount) - parseFloat(edit_discount_amount);
			var edit_gst_amt = parseFloat(edit_dis_amt) * edit_gst /100;
			var edit_amount1 = parseFloat(edit_dis_amt) + parseFloat(edit_gst_amt);

			if(parseFloat(edit_qty) > parseFloat(pending_return_qty))
			{
				flag_qty = true;
			}

			$('#edit_cgst_'+edit_id).val((edit_gst/2));
			$('#edit_sgst_'+edit_id).val((edit_gst/2));
			$('#edit_igst_'+edit_id).val((edit_gst));
			$('#edit_cgst_amt_'+edit_id).val(parseFloat(edit_gst_amt/2).toFixed(2));
			$('#edit_sgst_amt_'+edit_id).val(parseFloat(edit_gst_amt/2).toFixed(2));
			$('#edit_igst_amt_'+edit_id).val(parseFloat(edit_gst_amt).toFixed(2));

			$('#edit_amount_'+edit_id).val(parseFloat(((edit_amount+edit_gst_amt)-edit_discount_amount)).toFixed(2));
			$('#edit_gst_amt_'+edit_id).val(parseFloat(edit_gst_amt).toFixed(2));
		});

		$('.edit_qty').each(function(){
			var val = $(this).val();
			if (val == '') { val = 0; }
			edit_total_qty += parseFloat(val,2);
		});

		$('.edit_discount_amount').each(function(){
			var val = $(this).val();
			if (val == '') { val = 0; }

			edit_total_discount += parseFloat(val,2);
		});


		$('.edit_gst_amt').each(function(){
			var val = $(this).val();
			if (val == '') { val = 0; }
			edit_total_gst += parseFloat(val,2);
		});


		$('.edit_cgst_amt').each(function(){
			var val = $(this).val();
			if (val == '') { val = 0; }
			edit_total_cgst += parseFloat(val,2);
		});

		$('.edit_sgst_amt').each(function(){
			var val = $(this).val();
			if (val == '') { val = 0; }
			edit_total_sgst += parseFloat(val,2);
		});

		$('.edit_igst_amt').each(function(){
			var val = $(this).val();
			if (val == '') { val = 0; }
			edit_total_igst += parseFloat(val,2);
		});

		$('.edit_amount').each(function(){
			var val = $(this).val();
			if (val == '') { val = 0; }

			edit_final_amount += parseFloat(val,2);
		});

		$('#total_qty').val(parseFloat(edit_total_qty+total_qty).toFixed(2));
		$('#total_discount').val(parseFloat(edit_total_discount+total_discount).toFixed(2));
		$('#total_gst').val(parseFloat(edit_total_gst+total_gst).toFixed(2));
		$('#total_cgst').val(parseFloat(edit_total_cgst+total_cgst).toFixed(2));
		$('#total_sgst').val(parseFloat(edit_total_sgst+total_sgst).toFixed(2));
		$('#total_igst').val(parseFloat(edit_total_igst+total_igst).toFixed(2));
		$('#final_amount').val(parseFloat(edit_final_amount+final_amount).toFixed(2));

		$('.submit').attr('disabled',false);
		if(flag_qty) {
			$('.submit').attr('disabled',true);
			var toasts = toastr["error"]("Cannot be greater than Invoice Qty !", "Error");
			setTimeout(function(){
				toasts.fadeOut(500);
			}, 500);
		}
	}
</script>