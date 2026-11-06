<meta name="csrf-token" content="{{ csrf_token() }}" />
<div class="kt-portlet__foot">
	<div class="row">
		@if(Auth::guard('user')->check())
		@if(Auth::user()->role == 1)  
		<div class="form-group col-lg-2">
			<label>Branch  </label>
			@php $branch = App\User::where('status',1)->where('role',2)->get(); @endphp
			<select name="branch_id" id="branch_id" class="form-control select2" onchange="getclient(this)" required>
				<option value="">Select Branch</option>
				@foreach($branch as $sub)
				<option value="{{$sub->id}}">{{$sub->name}}</option>
				@endforeach
			</select>
		</div>
		<div class="form-group col-sm-2">
			<label>Vendor </label>
			<select class="form-control vendor_id select2" id="vendor_id" name="vendor_id" onchange="getVendorDetails(this)" required>
				<option value="">Select</option>
			</select>
		</div>
		@endif
		@endif
		@if(Auth::guard('user')->check())
		@if(!(Auth::user()->role == 1))
		<div class="form-group col-sm-2">
			@php $vendor = App\Vendor::where('branch_id',Auth::user()->id)->get(); @endphp
			<label>Vendor Name</label>
			<select class="form-control vendor_id select2" id="vendor_id" name="vendor_id" onchange="getVendorDetails(this)" required>
				<option value="">Select</option>
				@foreach($vendor as $datas)
				<option value="{{$datas->id}}">{{$datas->name}}</option>
				@endforeach
			</select>
		</div>
		@endif
		@endif
		@if(Auth::guard('employee')->check())
		<div class="form-group col-sm-2">
			@php $vendor = App\Vendor::where('branch_id',Auth::user()->branch_id)->get(); @endphp
			<label>Vendor Name</label>
			<select class="form-control vendor_id select2" id="vendor_id" name="vendor_id" onchange="getVendorDetails(this)" required>
				<option value="">Select</option>
				@foreach($vendor as $datas)
				<option value="{{$datas->id}}">{{$datas->name}}</option>
				@endforeach
			</select>
		</div>
		@endif
		<div class="form-group col-sm-2">
			<label>Date</label>
			<input type="date" name="date" value="{{date('Y-m-d')}}" placeholder="Enter Date" class="form-control" required>
		</div>
		<div class="form-group col-sm-2">
			<label>Debit Order No</label>
			<input type="text" name="order_no" placeholder="Enter Order No" class="form-control" required>
		</div>
		<div class="form-group col-sm-4">
			<label>Remark</label>
			<textarea name="remark" class="form-control" placeholder="Enter Remark"></textarea>
		</div>
	</div>
	<div class="row">
		<div class="form-group col-sm-3">
			<label>Invoice</label>
			<select class="form-control invoice_id select2" id="invoice_id" name="invoice_id" onchange="getInvoicedata(this)">
				<option value="">Select</option>
			</select>
		</div>
		<div class="form-group col-sm-3">
			<label>Product</label>
			<select class="form-control main_product_id select2" id="main_product_id" onchange="getproductdetailfrominvoice(this)">
				<option value="">Select</option>
			</select>
		</div>
	</div>
	<input type="hidden" id="state">
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
						<th style="font-size: 15px !important; width: 10% !important;">Final Amt</th>
					</tr>
				</thead>
				<tbody id="getdata">
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
	
	function getclient($this)
	{
		var branch_id = $($this).val();

		$('#vendor_id').html('<option value="">Select</option>');
		$('#category_id').html('<option value="">Select</option>');
		$('#company_id').html('<option value="">Select</option>');
		$('#product_id').html('<option value="">Select</option>');
		$('#getdata').html('');

		if(branch_id != '')
		{
			showLoader();

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

					hideLoader();
				}
			});
		}
	}

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
		var product_id = $($this).val();
		var branch_id = $('#branch_id').val();
		var vendor_id = $('#vendor_id').val();
		if(vendor_id)
		{
			if(product_id)
			{
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
			}
		}
		else
		{
			toastr["error"]("Vendor not selected!!", "Error");
		}
	};
	function getVendorDetails($this)
	{
		var vendor = $($this).val();

		$('#state').val('');

		if(vendor)
		{
			$.ajax({
				type: 'POST',
				url: '{{ route("user.get.vendor.data.debit") }}',
				data: {
					'_token': '{{ csrf_token() }}',
					id: vendor
				},
				dataType: 'json',
				success: function(data) {
					if(data.status === 'success')
					{
						$('#state').val(data.data.state_id);
						$('#invoice_id').html(data.html);
					}
					else if(data.status === 'state_error')
					{
						$('#vendor_id').val('').change().select2();
						toastr["error"]("Add State In Vendor !!", "Error");
					}
				}
			});
		}
	}
	function getInvoicedata($this)
	{
		var invoiceid = $($this).val();

		if(invoiceid)
		{
			showLoader();
			$.ajax({
				type: 'POST',
				url: '{{ route("user.get.vendor.invoicedata.debit") }}',
				data: {
					'_token': '{{ csrf_token() }}',
					id: invoiceid
				},
				dataType: 'json',
				success: function(data) {
					hideLoader();
					if(data.status === 'success')
					{
						$('#main_product_id').html(data.html);
					}
					else
					{
						toastr["error"]("No Data Found", "Error");
					}
				}
			});
		}
	}
	function getproductdetailfrominvoice($this)
	{
		var invoicechildid = $($this).val();
		var branch_id = $('#branch_id').val();

		if(invoicechildid)
		{
			var exists = $('.invoice_child_id').filter(function() {
				return $(this).val() == invoicechildid;
			}).length > 0;
			
			if (exists) {
				toastr.error("Duplicate Product !!!", "Error");
				return;
			}else{
				showLoader();
				$.ajax({
					type: 'POST',
					url: '{{ route("user.get.vendor.invoicechilddata.debit") }}',
					data: {
						'_token': '{{ csrf_token() }}',
						id: invoicechildid,
						branch_id: branch_id
					},
					dataType: 'json',
					success: function(data) {
						hideLoader();
						if(data.status === 'success')
						{
							$('#getdata').prepend(data.data);
						}
						else
						{
							toastr["error"]("No Data Found", "Error");
						}
					}
				});
			}
		}
	}
</script>
<script type="text/javascript">
	setInterval(gettotal, 1000);
	function gettotal()
	{
		$('.div_clone').each(function(i,val){
			$(this).find('.invoice_child_id').attr('id','invoice_child_id_'+i).attr('data-id',i).data('id',i);
			$(this).find('.new_product_id').attr('id','new_product_id_'+i).attr('data-id',i).data('id',i);
			$(this).find('.product_name').attr('id','product_name_'+i).attr('data-id',i).data('id',i);
			$(this).find('.product_code').attr('id','product_code_'+i).attr('data-id',i).data('id',i);
			$(this).find('.warehouse_id').attr('id','warehouse_id_'+i).attr('data-id',i).data('id',i);
			$(this).find('.qty').attr('id','qty_'+i).attr('data-id',i).data('id',i);
			$(this).find('.pending_return_qty').attr('id','pending_return_qty_'+i).attr('data-id',i).data('id',i);
			$(this).find('.rate').attr('id','rate_'+i).attr('data-id',i).data('id',i);
			$(this).find('.discount').attr('id','discount_'+i).attr('data-id',i).data('id',i);
			$(this).find('.discount_amount').attr('id','discount_amount_'+i).attr('data-id',i).data('id',i);
			$(this).find('.cgstdiv').attr('id','cgstdiv_'+i).attr('data-id',i).data('id',i);
			$(this).find('.sgstdiv').attr('id','sgstdiv_'+i).attr('data-id',i).data('id',i);
			$(this).find('.igstdiv').attr('id','igstdiv_'+i).attr('data-id',i).data('id',i);
			$(this).find('.cgst').attr('id','cgst_'+i).attr('data-id',i).data('id',i);
			$(this).find('.cgst_amt').attr('id','cgst_amt_'+i).attr('data-id',i).data('id',i);
			$(this).find('.sgst').attr('id','sgst_'+i).attr('data-id',i).data('id',i);
			$(this).find('.sgst_amt').attr('id','sgst_amt_'+i).attr('data-id',i).data('id',i);
			$(this).find('.igst').attr('id','igst_'+i).attr('data-id',i).data('id',i);
			$(this).find('.igst_amt').attr('id','igst_amt_'+i).attr('data-id',i).data('id',i);
			$(this).find('.gst').attr('id','gst_'+i).attr('data-id',i).data('id',i);
			$(this).find('.gst_amt').attr('id','gst_amt_'+i).attr('data-id',i).data('id',i);
			$(this).find('.amount').attr('id','amount_'+i).attr('data-id',i).data('id',i);
		})

		var state = $('#state').val();
		if (state == 12)
		{
			$(".cgstdiv").show();
			$(".sgstdiv").show();
			$(".igstdiv").hide();
		}
		else
		{
			$(".cgstdiv").hide();
			$(".sgstdiv").hide();
			$(".igstdiv").show();
		}

		var total_qty = 0;
		var total_discount = 0;
		var total_gst = 0;
		var total_cgst = 0;
		var total_sgst = 0;
		var total_igst = 0;
		var final_amount = 0;
		var entries = [];
		var hasDuplicate = false;
		var flag_qty = false;
		
		$('.qty').each(function(){
			var id = $(this).data('id');
			var new_product_id = $('#new_product_id_'+id).val();

			if(entries.includes(new_product_id)) {
				hasDuplicate = true;
			}
			entries.push(new_product_id);

			var qty = $('#qty_'+id).val();
			var pending_return_qty = $('#pending_return_qty_'+id).val();
			var rate = $('#rate_'+id).val();
			var discount_amount = $('#discount_amount_'+id).val();
			var gst = $('#gst_' + id + ' option:selected').text();

			qty = parseFloat(qty) || 0;
			pending_return_qty = parseFloat(pending_return_qty) || 0;
			rate = parseFloat(rate) || 0;
			discount_amount = parseFloat(discount_amount) || 0;
			gst = parseFloat(gst) || 0;

			var amount = parseFloat(qty) * parseFloat(rate); 
			if(discount_amount)
            {
                var discount = (100*parseFloat(discount_amount))/parseFloat(amount);
                $('#discount_'+id).val(parseFloat(discount).toFixed(2));
            }else{
                $('#discount_'+id).val(0);
            }
			var dis_amt = parseFloat(amount) - parseFloat(discount_amount);
			var gst_amt = parseFloat(dis_amt) * gst /100;
			var amount1 = parseFloat(dis_amt) + parseFloat(gst_amt);

			$('#cgst_'+id).val((gst/2));
			$('#sgst_'+id).val((gst/2));
			$('#igst_'+id).val((gst));
			$('#cgst_amt_'+id).val(parseFloat(gst_amt/2).toFixed(2));
			$('#sgst_amt_'+id).val(parseFloat(gst_amt/2).toFixed(2));
			$('#igst_amt_'+id).val(parseFloat(gst_amt).toFixed(2));

			$('#amount_'+id).val(parseFloat(((amount+gst_amt)-discount_amount)).toFixed(2));
			$('#gst_amt_'+id).val(parseFloat(gst_amt).toFixed(2));

			if(parseFloat(qty) > parseFloat(pending_return_qty))
			{
				flag_qty = true;
			}
		});

		$('.qty').each(function(){
			var val = $(this).val();
			if (val == '') { val = 0; }

			total_qty += parseFloat(val,2);
		});

		$('.discount_amount').each(function(){
			var val = $(this).val();
			if (val == '') { val = 0; }

			total_discount += parseFloat(val,2);
		});

		$('.gst_amt').each(function(){
			var val = $(this).val();
			if (val == '') { val = 0; }
			total_gst += parseFloat(val,2);
		});

		$('.cgst_amt').each(function(){
			var val = $(this).val();
			if (val == '') { val = 0; }
			total_cgst += parseFloat(val,2);
		});

		$('.sgst_amt').each(function(){
			var val = $(this).val();
			if (val == '') { val = 0; }
			total_sgst += parseFloat(val,2);
		});

		$('.igst_amt').each(function(){
			var val = $(this).val();
			if (val == '') { val = 0; }
			total_igst += parseFloat(val,2);
		});

		$('.amount').each(function(){
			var val = $(this).val();
			if (val == '') { val = 0; }

			final_amount += parseFloat(val,2);
		});

		$('#total_qty').val(parseFloat(total_qty).toFixed(2));
		$('#total_discount').val(parseFloat(total_discount).toFixed(2));
		$('#total_gst').val(parseFloat(total_gst).toFixed(2));
		$('#total_cgst').val(parseFloat(total_cgst).toFixed(2));
		$('#total_sgst').val(parseFloat(total_sgst).toFixed(2));
		$('#total_igst').val(parseFloat(total_igst).toFixed(2));
		$('#final_amount').val(parseFloat(final_amount).toFixed(2));

		if(hasDuplicate) {
			var toasts = toastr["error"]("Duplicate Product is selected !", "Error");
			setTimeout(function(){
				toasts.fadeOut(500);
			}, 500);
		}
		if(flag_qty) {
			var toasts = toastr["error"]("Cannot be greater than Invoice Qty !", "Error");
			setTimeout(function(){
				toasts.fadeOut(500);
			}, 500);
		}
		$('.submit').attr('disabled',false);
		if(hasDuplicate || flag_qty) {
			$('.submit').attr('disabled',true);
		}
	}
</script>