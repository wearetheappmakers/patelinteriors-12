<meta name="csrf-token" content="{{ csrf_token() }}" />
<div class="kt-portlet__body">
    <div class="row">
        @if(Auth::guard('user')->check())
        @if(Auth::user()->role == 1)  
        <div class="form-group col-lg-3">
            <label>Branch  </label>
            <input type="hidden" id="branch_id" value="{{ $data->branch_id }}">
            <input type="text" class="form-control" placeholder="Branch" value="{{ ($data->branch_id) ? $data->branch->name : '' }}" readonly>
        </div>
        <div class="form-group col-lg-3">
            <label>Vendor  </label>
            <input type="text" class="form-control" placeholder="Branch" value="{{ ($data->vendor_id) ? $data->vendor->name : '' }}" readonly>
        </div>
        @endif
        @endif
        @if(Auth::guard('user')->check())
        @if(Auth::user()->role == 2)
        <div class="form-group col-lg-3">
            <label>Vendor  </label>
            <input type="text" class="form-control" placeholder="Branch" value="{{ ($data->vendor_id) ? $data->vendor->name : '' }}" readonly>
        </div>    
        @endif
        @endif
        @if(Auth::guard('employee')->check())
        <div class="form-group col-lg-3">
            <label>Vendor  </label>
            <input type="text" class="form-control" placeholder="Branch" value="{{ ($data->vendor_id) ? $data->vendor->name : '' }}" readonly>
        </div>    
        @endif
        <div class="form-group col-lg-3">
            <label>Order No. </label>
            <input type="text" class="form-control" placeholder="Order No" value="{{$data->order_no}}" name="order_no" required >
        </div>
        <div class="form-group col-lg-3">
            <label>Date </label>
            <input type="date" class="form-control" placeholder="Date" value="{{$data->date}}" name="date" >
        </div>
    </div>
    <?php $vendor_state = App\Vendor::where('id',$data->vendor_id)->value('state_id'); ?>
    <input type="hidden" id="state" value="{{$vendor_state}}">
    @if(Auth::guard('user')->check())
    @if(Auth::user()->role == 1)  
    <div class="row">
        @php $category = App\ProductCategory::where('branch_id',$data->branch_id)->get(); @endphp
        <div class="form-group col-lg-3">
            <label>Category  </label>
            <select name="category_id" class="form-control select2" id="category_id" onchange="getcompany(this)">
                <option value="">Select</option>
                @foreach($category as $key => $value)
                <option value="{{$value->id}}">{{$value->name}}</option>
                @endforeach
            </select>
        </div>
        <div class="form-group col-lg-3">
            <label>Company  </label>
            <select name="company_id" class="form-control select2" id="company_id" onchange="getproduct(this)">
                <option value="">Select</option>

            </select>
        </div>
        <div class="form-group col-lg-3">
            <label>Product  </label>
            <select name="product_id" class="form-control select2" id="product_id" onchange="getdata(this)">
                <option value="">Select</option>

            </select>
        </div>
    </div>
    @endif
    @endif
    @if(Auth::guard('user')->check())
    @if(Auth::user()->role == 2)
    <div class="row">
        @php $category = App\ProductCategory::where('branch_id',Auth::user()->id)->get(); @endphp
        <div class="form-group col-lg-3">
            <label>Category  </label>
            <select name="category_id" class="form-control select2" id="category_id" onchange="getcompany(this)">
                <option value="">Select</option>
                @foreach($category as $key => $value)
                <option value="{{$value->id}}">{{$value->name}}</option>
                @endforeach
            </select>
        </div>
        <div class="form-group col-lg-3">
            <label>Company  </label>
            <select name="company_id" class="form-control select2" id="company_id" onchange="getproduct(this)">
                <option value="">Select</option>

            </select>
        </div>
        <div class="form-group col-lg-3">
            <label>Product  </label>
            <select name="product_id" class="form-control select2" id="product_id" onchange="getdata(this)">
                <option value="">Select</option>

            </select>
        </div>
    </div>
    @endif
    @endif
    @if(Auth::guard('employee')->check())
    <div class="row">
        @php $category = App\ProductCategory::where('branch_id',Auth::user()->branch_id)->get(); @endphp
        <div class="form-group col-lg-3">
            <label>Category  </label>
            <select name="category_id" class="form-control select2" id="category_id" onchange="getcompany(this)">
                <option value="">Select</option>
                @foreach($category as $key => $value)
                <option value="{{$value->id}}">{{$value->name}}</option>
                @endforeach
            </select>
        </div>
        <div class="form-group col-lg-3">
            <label>Company  </label>
            <select name="company_id" class="form-control select2" id="company_id" onchange="getproduct(this)">
                <option value="">Select</option>

            </select>
        </div>
        <div class="form-group col-lg-3">
            <label>Product  </label>
            <select name="product_id" class="form-control select2" id="product_id" onchange="getdata(this)">
                <option value="">Select</option>

            </select>
        </div>
    </div>
    @endif
    <div class="row">
        <div class="form-group col-sm-12 master_clone_div_container">
            <table cellspacing="0" border="0" class="table table-bordered" id="clone_table" style="width: 100%;">
                <thead>
                    <tr>
                        <th style="font-size: 15px !important; width: 1% !important;"></th>
                        <th style="font-size: 15px !important; width: 15% !important;">Product Name & Code</th>
                        <th style="font-size: 15px !important; width: 10% !important;">Warehouse</th>
                        <th style="font-size: 15px !important; width: 10% !important;">Qty</th>
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
                    @foreach($purchaseorderchild as $key => $value)
                    <tr class = "edit_div_clone" >
                        <td>
                            @if(count($purchaseorderchild) > 1)
                            <div style="display: inline-flex !important;">
                                <button type="button" id="edit_remove_container_details_{{$value->id}}" data-value="{{$value->id}}" class="btn btn-sm btn-icon btn-icon-md btn-clean edit_remove_container_details" ><i class="fa fa-minus" style="font-size:0.8rem !important"></i></button>
                            </div>
                            @endif
                        </td>
                        <td style="font-size: 15px !important;">
                            <input type="hidden" name="edit_main_id[]" value="{{$value->id}}">
                            <input type="hidden" style="width: 100% !important;" readonly class="edit_product_name" name="edit_product_name[]" id="edit_product_name_{{$value->id}}" placeholder="Enter Name" data-id="{{$value->id}}" value="{{$value->product_id}}" >
                            <input type="hidden" style="width: 100% !important;" class="edit_product_code" value="{{ ($value->product_id) ? $value->product->code : '' }}" name="edit_product_code[]" id="edit_product_code_{{$value->id}}" placeholder="Enter Price" data-id="{{$value->id}}" readonly  >

                            {{ ($value->product_id) ? $value->product->product : '' }} <br>
                            {{ ($value->product_id) ? $value->product->code : '' }}
                        </td>
                        <td style="font-size: 15px !important;">
                            <input type="hidden" class="edit_warehouse_id" value="{{$value->warehouse_id}}" name="edit_warehouse_id[]" style="width: 100% !important;" id="edit_warehouse_id_{{$value->id}}" placeholder="Enter Warehouse " data-id="{{$value->id}}" readonly  >

                            {{ ($value->warehouse_id) ? $value->warehouse->name : '' }}
                        </td>
                        <td style="font-size: 15px !important;">
                            <input type="number" style="width: 100% !important;" class="edit_qty" id="edit_qty_{{$value->id}}" placeholder="Enter Qty" oninput="get_calculation()" name="edit_qty[]" value="{{$value->qty}}" data-id="{{$value->id}}"  >
                        </td>
                        <td style="font-size: 15px !important;">
                            <input type="number" style="width: 100% !important;" class="edit_rate" id="edit_rate_{{$value->id}}" value="{{$value->rate}}" placeholder="Enter Rate"  oninput="get_calculation()" name="edit_rate[]" data-id="{{$value->id}}">
                        </td>
                        <td style="font-size: 15px !important;">
                            <input type="number" style="width: 100% !important;" class="edit_discount" id="edit_discount_{{$value->id}}" value="{{$value->discount}}" placeholder="Enter Discount(%)" oninput="get_calculation()" name="edit_discount[]" data-id="{{$value->id}}"><br>
                            <input type="number" style="width: 100% !important;" class="edit_discount_amount" id="edit_discount_amount_{{$value->id}}" value="{{$value->discount_amount}}" placeholder="Enter Discount Amount" oninput="get_calculation()" name="edit_discount_amount[]" data-id="{{$value->id}}">
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
                            <select style="width: 100% !important;" class="edit_gst" oninput="get_calculation()" name="edit_gst[]" id="edit_gst_{{$value->id}}" data-id="{{$value->id}}">
                                <option value="">Select</option>
                                @foreach ($gst as $key1 => $value1)
                                <option value="{{$value1->id}}" @if($value->gst == $value1->id) selected @endif>{{$value1->gst}}</option>
                                @endforeach
                            </select><br>
                            <input type="number" style="width: 100% !important;" class="edit_gst_amt" id="edit_gst_amt_{{$value->id}}" value="{{$value->gst_amt}}" placeholder="Enter GST Amount" oninput="get_calculation()" name="edit_gst_amt[]" data-id="{{$value->id}}" readonly>
                        </td>
                        <td style="font-size: 15px !important;" class="edit_cgstdiv" data-id="{{$value->id}}" id="edit_cgstdiv_{{$value->id}}">
                            <input type="number" style="width: 100% !important;" class="edit_cgst" id="edit_cgst_{{$value->id}}" placeholder="CGST" oninput="get_calculation()" name="edit_cgst[]" data-id="{{$value->id}}" readonly><br>
                            <input type="number" style="width: 100% !important;" readonly class="edit_cgst_amt" id="edit_cgst_amt_{{$value->id}}" placeholder="CGST Amt" oninput="get_calculation()" name="edit_cgst_amt[]" data-id="{{$value->id}}">
                        </td>
                        <td style="font-size: 15px !important;" class="edit_sgstdiv" data-id="{{$value->id}}" id="edit_sgstdiv_{{$value->id}}">
                            <input type="number" style="width: 100% !important;" class="edit_sgst" id="edit_sgst_{{$value->id}}" placeholder="SGST" oninput="get_calculation()" name="edit_sgst[]" data-id="{{$value->id}}" readonly><br>
                            <input type="number" style="width: 100% !important;" readonly class="edit_sgst_amt" id="edit_sgst_amt_{{$value->id}}" placeholder="SGST Amt" oninput="get_calculation()" name="edit_sgst_amt[]" data-id="{{$value->id}}">
                        </td>
                        <td style="font-size: 15px !important;" class="edit_igstdiv" data-id="{{$value->id}}" id="edit_igstdiv_{{$value->id}}">
                            <input type="number" style="width: 100% !important;" class="edit_igst" id="edit_igst_{{$value->id}}" placeholder="IGST" oninput="get_calculation()" name="edit_igst[]" data-id="{{$value->id}}" readonly><br>
                            <input type="number" style="width: 100% !important;" readonly class="edit_igst_amt" id="edit_igst_amt_{{$value->id}}" placeholder="IGST Amt" oninput="get_calculation()" name="edit_igst_amt[]" data-id="{{$value->id}}">
                        </td>
                        <td style="font-size: 15px !important;">
                            <input type="number" style="width: 100% !important;" class="edit_amount" id="edit_amount_{{$value->id}}" value="{{$value->amount}}" oninput="get_calculation()" name="edit_amount[]" data-id="{{$value->id}}" readonly  >
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
    $(document).ready(function() {
        var count = 0;
        $(document).on('click','.add_more',function() {
            count++;
            var $row = $(this).closest('.master_clone');
            var $clone = $row.clone();
            $clone.find('.productcategory_id').val('').attr('data-id',count).attr('id','productcategory_id_'+count);
            $clone.find('.product_id').val('').attr('data-id',count).attr('id','product_id_'+count);
            $clone.find('.company_id').val('').attr('data-id',count).attr('id','company_id_'+count);
            $clone.find('.warehouse_id').val('').attr('data-id',count).attr('id','warehouse_id_'+count);
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
        $(document).on('click','.remove_container_details',function(){
            var num_of_master_clone_div_payment = $('.div_clone').length;
            if (num_of_master_clone_div_payment != 1) {
                var obj = $(this).closest('.div_clone');
                obj.remove();
            }
        });
    });
    $(".edit_remove_container_details").on("click", function(e)
    {
        var num_of_master_clone_div_payment = $('.edit_div_clone').length;
        if (num_of_master_clone_div_payment != 1)
        {
            if (confirm("Are you sure want to delete this item??")) {
                var id = $(this).data('value');
                var url = "{{ route('user.purchaseorder.deletechild',":rowid") }}";
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
    function getcompany($this){
        showLoader();
        var category_id = $($this).val();
        var branch_id = $('#branch_id').val();
        var id = $($this).data('id');
        $.ajax({
            type:"POST",
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            url:"{{ route('user.po.getcompany') }}",
            data: {
                'category_id': category_id,
                'branch_id': branch_id
            },
            success: function(data){
                hideLoader();
                $('#company_id').html(data.data);
                $('#product_id').html('<option value="">Select</option>');
            }
        });     
    };
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
    function getproduct($this){
        showLoader();
        var company_id = $($this).val();
        var id = $($this).data('id');
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
                hideLoader();
                $('#product_id').html(data.data);
            }
        });
    };

    function getdata($this){
        var product_id =$($this).val();
        var branch_id = $('#branch_id').val();
        if(product_id){
            $.ajax({
                type:"POST",
                headers:{
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                url:"{{route('user.po.getdata')}}",
                data:{
                    'product_id':product_id,
                    'branch_id': branch_id
                },
                success: function(data){
                    $('#getdata').prepend(data.data);
                }
            });
        }
    };
    setInterval(gettotal, 1000);
    function gettotal()
    {
        $('.div_clone').each(function(i,val){
            $(this).find('.new_product_id').attr('id','new_product_id_'+i).attr('data-id',i).data('id',i);
            $(this).find('.product_name').attr('id','product_name_'+i).attr('data-id',i).data('id',i);
            $(this).find('.product_code').attr('id','product_code_'+i).attr('data-id',i).data('id',i);
            $(this).find('.warehouse_id').attr('id','warehouse_id_'+i).attr('data-id',i).data('id',i);
            $(this).find('.qty').attr('id','qty_'+i).attr('data-id',i).data('id',i).on('input', get_calculation);
            $(this).find('.rate').attr('id','rate_'+i).attr('data-id',i).data('id',i).on('input', get_calculation);
            $(this).find('.discount').attr('id','discount_'+i).attr('data-id',i).data('id',i).on('input', get_calculation);
            $(this).find('.discount_amount').attr('id','discount_amount_'+i).attr('data-id',i).data('id',i).on('input', get_calculation);
            $(this).find('.cgstdiv').attr('id','cgstdiv_'+i).attr('data-id',i).data('id',i).on('input', get_calculation);
            $(this).find('.sgstdiv').attr('id','sgstdiv_'+i).attr('data-id',i).data('id',i).on('input', get_calculation);
            $(this).find('.igstdiv').attr('id','igstdiv_'+i).attr('data-id',i).data('id',i).on('input', get_calculation);
            $(this).find('.cgst').attr('id','cgst_'+i).attr('data-id',i).data('id',i).on('input', get_calculation);
            $(this).find('.cgst_amt').attr('id','cgst_amt_'+i).attr('data-id',i).data('id',i).on('input', get_calculation);
            $(this).find('.sgst').attr('id','sgst_'+i).attr('data-id',i).data('id',i).on('input', get_calculation);
            $(this).find('.sgst_amt').attr('id','sgst_amt_'+i).attr('data-id',i).data('id',i).on('input', get_calculation);
            $(this).find('.igst').attr('id','igst_'+i).attr('data-id',i).data('id',i).on('input', get_calculation);
            $(this).find('.igst_amt').attr('id','igst_amt_'+i).attr('data-id',i).data('id',i).on('input', get_calculation);
            $(this).find('.gst').attr('id','gst_'+i).attr('data-id',i).data('id',i).on('input', get_calculation);
            $(this).find('.gst_amt').attr('id','gst_amt_'+i).attr('data-id',i).data('id',i).on('input', get_calculation);
            $(this).find('.amount').attr('id','amount_'+i).attr('data-id',i).data('id',i).on('input', get_calculation);
        })

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
    }

    $(document).on('input', '.discount', function () {
        var id = $(this).data('id');
        get_calculation(id, 'discount');
    });

    $(document).on('input', '.discount_amount', function () {
        var id = $(this).data('id');
        get_calculation(id, 'discount_amount');
    });

    $(document).on('input', '.edit_discount', function () {
        var id = $(this).data('id');
        get_calculation(id, 'edit_discount');
    });

    $(document).on('input', '.edit_discount_amount', function () {
        var id = $(this).data('id');
        get_calculation(id, 'edit_discount_amount');
    });

    get_calculation();
    function get_calculation(changed_id = null, changed_field = null)
    {
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

        $('.qty').each(function(){
            var id = $(this).data('id');
            var qty = parseFloat($('#qty_'+id).val()) || 0;
            var rate = parseFloat($('#rate_'+id).val()) || 0;
            var discount = parseFloat($('#discount_'+id).val()) || 0;
            var discount_amount = parseFloat($('#discount_amount_'+id).val()) || 0;
            var gst = parseFloat($('#gst_' + id + ' option:selected').text()) || 0;

            var amount = qty * rate;

            if (changed_id == id && changed_field == 'discount') {
                discount_amount = (amount * discount) / 100;
                $('#discount_amount_' + id).val(discount_amount.toFixed(2));
            }

            if (changed_id == id && changed_field == 'discount_amount') {
                discount = (100 * discount_amount) / amount;
                $('#discount_' + id).val(discount.toFixed(2));
            }

            var dis_amt = amount - discount_amount;
            var gst_amt = dis_amt * gst / 100;
            var final_amt = dis_amt + gst_amt;

            $('#cgst_'+id).val((gst/2));
            $('#sgst_'+id).val((gst/2));
            $('#igst_'+id).val((gst));
            $('#cgst_amt_'+id).val((gst_amt/2).toFixed(2));
            $('#sgst_amt_'+id).val((gst_amt/2).toFixed(2));
            $('#igst_amt_'+id).val(gst_amt.toFixed(2));
            $('#amount_'+id).val(final_amt.toFixed(2));
            $('#gst_amt_'+id).val(gst_amt.toFixed(2));
        });

        $('.edit_qty').each(function(){
            var edit_id = $(this).data('id');
            var edit_qty = parseFloat($('#edit_qty_'+edit_id).val()) || 0;
            var edit_rate = parseFloat($('#edit_rate_'+edit_id).val()) || 0;
            var edit_discount = parseFloat($('#edit_discount_'+edit_id).val()) || 0;
            var edit_discount_amount = parseFloat($('#edit_discount_amount_'+edit_id).val()) || 0;
            var edit_gst = parseFloat($('#edit_gst_' + edit_id + ' option:selected').text()) || 0;

            var edit_amount = parseFloat(edit_qty) * parseFloat(edit_rate);

            if (changed_id == edit_id && changed_field == 'edit_discount') {
                edit_discount_amount = (edit_amount * edit_discount) / 100;
                $('#edit_discount_amount_' + edit_id).val(edit_discount_amount.toFixed(2));
            }

            if (changed_id == edit_id && changed_field == 'edit_discount_amount') {
                edit_discount = (100 * edit_discount_amount) / edit_amount;
                $('#edit_discount_' + edit_id).val(edit_discount.toFixed(2));
            }

            var edit_dis_amt = parseFloat(edit_amount) - parseFloat(edit_discount_amount);
            var edit_gst_amt = parseFloat(edit_dis_amt) * edit_gst /100;
            var edit_amount1 = parseFloat(edit_dis_amt) + parseFloat(edit_gst_amt);

            $('#edit_cgst_'+edit_id).val((edit_gst/2));
            $('#edit_sgst_'+edit_id).val((edit_gst/2));
            $('#edit_igst_'+edit_id).val((edit_gst));
            $('#edit_cgst_amt_'+edit_id).val(parseFloat(edit_gst_amt/2).toFixed(2));
            $('#edit_sgst_amt_'+edit_id).val(parseFloat(edit_gst_amt/2).toFixed(2));
            $('#edit_igst_amt_'+edit_id).val(parseFloat(edit_gst_amt).toFixed(2));

            $('#edit_amount_'+edit_id).val(parseFloat(((edit_amount+edit_gst_amt)-edit_discount_amount)).toFixed(2));
            $('#edit_gst_amt_'+edit_id).val(parseFloat(edit_gst_amt).toFixed(2));
        });

        {{-- $('.edit_qty').each(function(){
            var edit_id = $(this).data('id');
            var edit_qty = $('#edit_qty_'+edit_id).val();
            var edit_rate = $('#edit_rate_'+edit_id).val();
            var edit_discount_amount = $('#edit_discount_amount_'+edit_id).val();
            var edit_gst = $('#edit_gst_' + edit_id + ' option:selected').text();

            edit_qty = parseFloat(edit_qty) || 0;
            edit_rate = parseFloat(edit_rate) || 0;
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

            $('#edit_cgst_'+edit_id).val((edit_gst/2));
            $('#edit_sgst_'+edit_id).val((edit_gst/2));
            $('#edit_igst_'+edit_id).val((edit_gst));
            $('#edit_cgst_amt_'+edit_id).val(parseFloat(edit_gst_amt/2).toFixed(2));
            $('#edit_sgst_amt_'+edit_id).val(parseFloat(edit_gst_amt/2).toFixed(2));
            $('#edit_igst_amt_'+edit_id).val(parseFloat(edit_gst_amt).toFixed(2));

            $('#edit_amount_'+edit_id).val(parseFloat(((edit_amount+edit_gst_amt)-edit_discount_amount)).toFixed(2));
            $('#edit_gst_amt_'+edit_id).val(parseFloat(edit_gst_amt).toFixed(2));
        }); --}}

        $('.qty').each(function(){
            var val = $(this).val();
            if (val == '') { val = 0; }

            total_qty += parseFloat(val,2);
        });

        $('.edit_qty').each(function(){
            var val = $(this).val();
            if (val == '') { val = 0; }
            edit_total_qty += parseFloat(val,2);
        });

        $('.discount_amount').each(function(){
            var val = $(this).val();
            if (val == '') { val = 0; }

            total_discount += parseFloat(val,2);
        });

        $('.edit_discount_amount').each(function(){
            var val = $(this).val();
            if (val == '') { val = 0; }

            edit_total_discount += parseFloat(val,2);
        });

        $('.gst_amt').each(function(){
            var val = $(this).val();
            if (val == '') { val = 0; }
            total_gst += parseFloat(val,2);
        });

        $('.edit_gst_amt').each(function(){
            var val = $(this).val();
            if (val == '') { val = 0; }
            edit_total_gst += parseFloat(val,2);
        });

        $('.cgst_amt').each(function(){
            var val = $(this).val();
            if (val == '') { val = 0; }
            total_cgst += parseFloat(val,2);
        });

        $('.edit_cgst_amt').each(function(){
            var val = $(this).val();
            if (val == '') { val = 0; }
            edit_total_cgst += parseFloat(val,2);
        });

        $('.sgst_amt').each(function(){
            var val = $(this).val();
            if (val == '') { val = 0; }
            total_sgst += parseFloat(val,2);
        });

        $('.edit_sgst_amt').each(function(){
            var val = $(this).val();
            if (val == '') { val = 0; }
            edit_total_sgst += parseFloat(val,2);
        });

        $('.igst_amt').each(function(){
            var val = $(this).val();
            if (val == '') { val = 0; }
            total_igst += parseFloat(val,2);
        });

        $('.edit_igst_amt').each(function(){
            var val = $(this).val();
            if (val == '') { val = 0; }
            edit_total_igst += parseFloat(val,2);
        });

        $('.amount').each(function(){
            var val = $(this).val();
            if (val == '') { val = 0; }

            final_amount += parseFloat(val,2);
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
    }
</script>