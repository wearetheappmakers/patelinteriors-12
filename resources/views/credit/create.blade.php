<meta name="csrf-token" content="{{ csrf_token() }}" />
<div class="kt-portlet__foot">
	<div class="row">
		@if(Auth::guard('user')->check() && Auth::user()->role == 1)
		<div class="form-group col-lg-2">
			<label>Branch  </label>
			<select name="branch_id" id="branch_id" class="form-control select2" onchange="getclient(this)" required>
				<option value="">Select Branch</option>
				@foreach($branch as $sub)
				<option value="{{$sub->id}}">{{$sub->name}}</option>
				@endforeach
			</select>
		</div>
		@endif

		<div class="form-group col-sm-2">
			<label>Client Name</label>
			<input type="hidden" id="client_state_id">
			<select class="form-control client_id select2" id="client_id"  name="client_id" onchange="getinvoice(this)" required>
				<option value="">Select</option>
				@foreach($client as $datas)
				<option value="{{$datas->id}}">{{$datas->member}}</option>
				@endforeach
			</select>
		</div>
		<div class="form-group col-sm-2">
			<label>Date</label>
			<input type="date" name="date" value="{{ date('Y-m-d') }}" class="form-control" >
		</div>
		<div class="form-group col-sm-2">
			<label>Credit Order No</label>
			<input type="text" name="order_no" placeholder="Enter Order No" class="form-control" required>
		</div>
		
		<div class="form-group col-sm-4">
			<label>Remark</label>
			<textarea name="remark" class="form-control" placeholder="Enter Remark"></textarea>
		</div>
	</div>
	<div class="row">
		<div class="form-group col-sm-2">
			<label>SalesOrder Invoice</label>
			<select class="form-control invoice_id select2" id="invoice_id"  name="invoice_id" onchange="pending_so_invoice_product(this)" required>
				<option value="">Select</option>
			</select>
		</div>
		<div class="form-group col-sm-2">
			<label>Product</label>
			<select class="form-control select2" id="product_id"  onchange="getdata(this)" required>
				<option value="">Select</option>
			</select>
		</div>
	</div>
	
	<div class="row">
		<div class="form-group col-sm-12 master_clone_div_container" style="overflow: auto !important">
			<table cellspacing="0" border="0" class="table table-bordered" id="clone_table">
				<thead>
					<tr>
						<th width="1%"></th>
						<th width="15%">Product Name <br>Code</th>
						<th width="10%">Warehouse</th>
						<th width="10%">Pending Qty <br>Qty</th>
						<th width="10%">Rate</th>
						<th width="10%">Dis(%)<br>Dis Amt</th>
						<th width="10%">GST<br>GST Amt</th>

						<th class="cgst_column" width="8%" style="display: none;">CGST<br>CGST Amt</th>
						<th class="cgst_column" width="8%" style="display: none;">SGST<br>SGST Amt</th>
						<th class="igst_column" width="8%" style="display: none;">IGST<br>IGST Amt</th>

						<th width="10%">Final Amt</th>
					</tr>
				</thead>
				<tbody id="getdata">
				</tbody>
				<tfoot>
					<tr>
						<td></td>
						<td></td>
						<td></td>
						<td><input type="number" placeholder="Total Qty" name="total_qty" class="total_qty" id="total_qty" readonly style="width: 100%;"></td>
						<td></td>
						<td><input type="number" placeholder="Total Discount" name="total_discount_amt" class="total_discount_amt" id="total_discount_amt" readonly style="width: 100%;"></td>
						<td><input type="number" placeholder="Total GST" name="total_gst_amt" class="total_gst_amt" id="total_gst_amt" readonly style="width: 100%;"></td>

						<td class="cgst_column" style="display: none;"><input type="number" placeholder="Total CGST" name="total_cgst_amt" class="total_cgst_amt" id="total_cgst_amt" readonly style="width: 100%;"></td>
						<td class="cgst_column" style="display: none;"><input type="number" placeholder="Total SGST" name="total_sgst_amt" class="total_sgst_amt" id="total_sgst_amt" readonly style="width: 100%;"></td>
						<td class="igst_column" style="display: none;"><input type="number" placeholder="Total IGST" name="total_igst_amt" class="total_igst_amt" id="total_igst_amt" readonly style="width: 100%;"></td> 

						<td><input type="number" name="total_amount" class="total_amount" id="total_amount" readonly style="width: 100%;"></td>
					</tr>
				</tfoot>
			</table>
		</div>
	</div>
</div>
<script type="text/javascript">
	function getclient($this)
	{
		var branch_id = $($this).val();

		$('#client_id').html('<option value="">Select</option>');
		$('#invoice_id').html('<option value="">Select</option>');
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
					$('#client_id').html(data.data);

					hideLoader();
				}
			});
		}
	}


	function getinvoice($this)
	{
		var client_id = $($this).val();
		
		$('#invoice_id').html('<option value="">Select</option>');
		$('#getdata').html('');
		$('#client_state_id').val('');

		if(client_id != '')
		{
			showLoader();

			$.ajax({
				type:"POST",
				headers: {
					'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
				},
				url:"{{ route('user.credit.getinvoice') }}",
				data: {
					'client_id': client_id
				},
				success: function(data){
					$('#invoice_id').html(data.data);
					$('#client_state_id').val(data.client.state_id);
					hideLoader();
				}
			});
		}
	}

	function pending_so_invoice_product($this)
	{
		var invoice_id =$($this).val();
		
		$('#product_id').html('<option value="">Select</option>');

		if(invoice_id != '')
		{
			showLoader();

			$.ajax({
				type:"POST",
				headers:{
					'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
				},
				url:"{{route('user.credit.pendingso.invoice.product')}}",
				data:{
					'invoice_id':invoice_id
				},
				success: function(data){
					$('#product_id').html(data.data);
					hideLoader();
				}
			});
		}
	}

	function getdata($this)
	{
		var sales_invoice_child_id =$($this).val();
		
		if(sales_invoice_child_id != '')
		{

			var exists = $('.sales_invoice_child_id').filter(function() {
				return $(this).val() == sales_invoice_child_id;
			}).length > 0;

			if (exists) {
				toastr.error("This Product is Already used", "Error");
				return; 
			}

			showLoader();

			$.ajax({
				type:"POST",
				headers:{
					'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
				},
				url:"{{route('user.credit.getdata')}}",
				data:{
					'sales_invoice_child_id':sales_invoice_child_id,
					'branch_id': $('#branch_id').val()
				},
				success: function(data){
					$('#getdata').append(data.data);
					hideLoader();
				}
			});
		}
	}
</script>
<script type="text/javascript">
	setInterval(gettotal, 1000);
	function gettotal()
	{
		var client_state_id = $('#client_state_id').val();
		if(client_state_id)
		{
			if(client_state_id == 12)
			{
				$('.cgst_column').show();
				$('.igst_column').hide();
			}
			else
			{
				$('.cgst_column').hide();
				$('.igst_column').show();
			}
		} 
		else
		{
			$('.cgst_column').hide();
			$('.igst_column').hide();
		}

		var total_qty = parseFloat(0);
		var total_discount_amt = parseFloat(0);
		var total_gst_amt = parseFloat(0);
		var total_cgst_amt = parseFloat(0);
		var total_amount = parseFloat(0);
		var grand_total = parseFloat(0);
		var labour_charges = $('#labour_charges').val();
		var transport_charges = $('#transport_charges').val();

		labour_charges = parseFloat(labour_charges) || 0;
		transport_charges = parseFloat(transport_charges) || 0;

		var disableButton = false;

		$('.master_clone').each(function(i,val){

			$(this).attr('data-id',i).attr('id','master_clone_'+i);
			$(this).find('.hidden_id').attr('data-id',i).attr('id','hidden_id_'+i);
			$(this).find('.salesorder_child_id').attr('data-id',i).attr('id','salesorder_child_id_'+i);
			$(this).find('.product_id').attr('data-id',i).attr('id','product_id_'+i);
			$(this).find('.warehouse_id').attr('data-id',i).attr('id','warehouse_id_'+i);
			$(this).find('.pending_qty').attr('data-id',i).attr('id','pending_qty_'+i);
			$(this).find('.qty').attr('data-id',i).attr('id','qty_'+i);
			$(this).find('.rate').attr('data-id',i).attr('id','rate_'+i);
			$(this).find('.discount_per').attr('data-id',i).attr('id','discount_per_'+i);
			$(this).find('.discount_amt').attr('data-id',i).attr('id','discount_amt_'+i);
			$(this).find('.gst').attr('data-id',i).attr('id','gst_'+i);
			$(this).find('.gst_id').attr('data-id',i).attr('id','gst_id_'+i);
			$(this).find('.gst_amt').attr('data-id',i).attr('id','gst_amt_'+i);
			$(this).find('.cgst').attr('data-id',i).attr('id','cgst_'+i);
			$(this).find('.cgst_amt').attr('data-id',i).attr('id','cgst_amt_'+i);
			$(this).find('.sgst').attr('data-id',i).attr('id','sgst_'+i);
			$(this).find('.sgst_amt').attr('data-id',i).attr('id','sgst_amt_'+i);
			$(this).find('.igst').attr('data-id',i).attr('id','igst_'+i);
			$(this).find('.igst_amt').attr('data-id',i).attr('id','igst_amt_'+i);
			$(this).find('.amount').attr('data-id',i).attr('id','amount_'+i);

			var pending_qty = $('#pending_qty_'+i).val();
			var qty = $('#qty_'+i).val();
			var rate = $('#rate_'+i).val();
			var discount_amt = $('#discount_amt_'+i).val();
			var gst = $('#gst_'+i).val();

			pending_qty = parseFloat(pending_qty) || 0;
			qty = parseFloat(qty) || 0;
			rate = parseFloat(rate) || 0;
			discount_amt = parseFloat(discount_amt) || 0;
			gst = parseFloat(gst) || 0;

			var base_amt = parseFloat(qty) * parseFloat(rate);
            if(discount_amt)
            {
                var discount_per = (100*parseFloat(discount_amt))/parseFloat(base_amt);
                $('#discount_per_'+i).val(parseFloat(discount_per).toFixed(2));
            }else{
                $('#discount_per_'+i).val(0);
            }
            var dis_amt = parseFloat(base_amt) - parseFloat(discount_amt);
            var cgst = parseFloat(gst) / 2;
            var gst_amt = (parseFloat(dis_amt) * parseFloat(gst)) / 100;
            var cgst_amt = parseFloat(gst_amt) / 2;
            var amount = parseFloat(dis_amt) + parseFloat(gst_amt); 

			$('#gst_amt_'+i).val(gst_amt.toFixed(2));
			$('#amount_'+i).val(amount.toFixed(2));


			$('#cgst_'+i).val(cgst.toFixed(2));
			$('#cgst_amt_'+i).val(cgst_amt.toFixed(2));
			$('#sgst_'+i).val(cgst.toFixed(2));
			$('#sgst_amt_'+i).val(cgst_amt.toFixed(2));
			$('#igst_'+i).val(gst.toFixed(2));
			$('#igst_amt_'+i).val(gst_amt.toFixed(2));


			total_qty += qty;
			total_discount_amt += discount_amt;
			total_gst_amt += gst_amt;
			total_cgst_amt += cgst_amt;
			total_amount += amount;

			if(qty > pending_qty) {
				var $toast = toastr["error"]("Qty. is greater than pending Qty.!!!", "Error");
				setTimeout(function(){
					$toast.fadeOut(500);
				}, 500);

				disableButton = true;
			}
		});

		grand_total = total_amount + labour_charges + transport_charges;

		$('#total_qty').val(total_qty.toFixed(2));
		$('#total_discount_amt').val(total_discount_amt.toFixed(2));
		$('#total_gst_amt').val(total_gst_amt.toFixed(2));
		$('#total_cgst_amt').val(total_cgst_amt.toFixed(2));
		$('#total_sgst_amt').val(total_cgst_amt.toFixed(2));
		$('#total_igst_amt').val(total_gst_amt.toFixed(2));
		$('#total_amount').val(total_amount.toFixed(2));
		$('#grand_total').val(grand_total.toFixed(2));


		if(disableButton) {
			$('.submit').prop('disabled', true);
		} else {
			$('.submit').prop('disabled', false);
		}
	}


	$(document).ready(function() {
		$(document).on('click','#remove',function(){
			var master_clone = $('.master_clone').length;
			if (master_clone != 1) {
				var obj = $(this).closest('.master_clone');
				obj.remove();
			}
		});

		$(document).on('change','.gst_id',function(){
			var gst = $(this).val();
			var dataId = $(this).data('id');
			var gstText = $("#gst_id_" + dataId + " option:selected").text();

			$('#gst_'+dataId).val('');

			if(gst)
			{
				$('#gst_'+dataId).val(gstText);
			}
		});
	});
</script>