@extends('main')
@section('content')
<meta name="csrf-token" content="{{ csrf_token() }}" />
<div class="kt-container  kt-container--fluid  kt-grid_item kt-grid_item--fluid">
    <br>
    <div class="kt-portlet kt-portlet--mobile">
        <div class="kt-portlet__head">
            <div class="kt-portlet__head-label">
                <h3 class="kt-portlet__head-title">
                    Create Purchase Invoice
                </h3>
            </div>
            <div class="kt-portlet__head-toolbar">
                <div class="kt-portlet__head-wrapper">
                </div>
            </div>
        </div>
        <div class="kt-portlet__body">
            <form class="kt-form kt-form--label-right add_form" id="add_form" >
                @csrf
                <div class="row">
                    @if(Auth::guard('user')->check())
                    @if(Auth::user()->role == 1)  
                    <div class="form-group col-lg-3">
                        <label>Branch</label>
                        <select name="branch_id" id="branch_id" class="form-control select2" onchange="getemployee(this)" required>
                            <option value="">Select</option>
                            @foreach($branch as $sub)
                            <option value="{{$sub->id}}" >{{$sub->name}}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group col-lg-3">
                        <label>Vendor</label>
                        <a style="background: white; height: 2px !important" target="_blank" href="{{route('user.vendor.create')}}" title="Add" class="btn btn-sm btn-clean btn-icon btn-icon-md">
                            <i class="flaticon2-plus-1"></i>
                        </a>
                        <select name="vendor_id" class="form-control select2" id="vendor_id" onchange="getVendorDetails(this)" required>
                            <option value="">Select</option>
                        </select>
                    </div>
                    @endif
                    @endif
                    @if(Auth::guard('user')->check())
                    @if(Auth::user()->role == 2)
                    <?php
                    $vendor = \App\Vendor::where('branch_id',Auth::user()->id)->get();
                    ?>
                    <div class="form-group col-lg-3">
                        <label>Vendor</label>
                        <a style="background: white; height: 2px !important" target="_blank" href="{{route('user.vendor.create')}}" title="Add" class="btn btn-sm btn-clean btn-icon btn-icon-md">
                            <i class="flaticon2-plus-1"></i>
                        </a>
                        <select name="vendor_id" class="form-control select2" id="vendor_id" onchange="getVendorDetails(this)" required >
                            <option value="">Select</option>
                            @foreach($vendor as $sub)
                            <option value="{{$sub->id}}" >{{$sub->name}}</option>
                            @endforeach
                        </select>
                    </div>    
                    @endif
                    @endif
                    @if(Auth::guard('employee')->check())

                    <?php
                    $vendor = \App\Vendor::where('branch_id',Auth::user()->branch_id)->get();
                    ?>
                    <div class="form-group col-lg-3">
                        <label>Vendor</label>
                        <a style="background: white; height: 2px !important" target="_blank" href="{{route('user.vendor.create')}}" title="Add" class="btn btn-sm btn-clean btn-icon btn-icon-md">
                            <i class="flaticon2-plus-1"></i>
                        </a>
                        <select name="vendor_id" class="form-control select2" id="vendor_id" onchange="getVendorDetails(this)" required >
                            <option value="">Select</option>
                            @foreach($vendor as $sub)
                            <option value="{{$sub->id}}" >{{$sub->name}}</option>
                            @endforeach
                        </select>
                    </div>    
                    @endif
                    <div class="form-group col-lg-3">
                        <label>Order No.</label>
                        <input type="text" class="form-control" placeholder="Order No" name="order_no"  >
                    </div>
                    <div class="form-group col-lg-3">
                        <label>Invoice No.</label>
                        <input type="text" class="form-control" placeholder="Invoice No" name="invoice_no"  required>
                    </div>

                    <div class="form-group col-lg-3">
                        <label>Date</label>
                        <input type="date" class="form-control" placeholder="Date" value="{{date('Y-m-d')}}" name="date" required>
                    </div>
                    <div class="form-group col-sm-3">
                        <label>Remark</label>
                        <textarea name="remark" class="form-control" placeholder="Enter Remark"></textarea>
                    </div>
                </div>
                <input type="hidden" id="state">
                @if(Auth::guard('user')->check())
                @if(Auth::user()->role == 1)
                <div class="row">
                    <div class="form-group col-lg-3">
                        <label>Category</label>
                        <select name="category_id" class="form-control select2 category_id" id="category_id" onchange="getcompany(this)" required>
                            <option value="">Select</option>

                        </select>
                    </div>
                    <div class="form-group col-lg-3">
                        <label>Company</label>
                        <select name="company_id" class="form-control select2 company_id" id="company_id" onchange="getproduct(this)" required>
                            <option value="">Select</option>

                        </select>
                    </div>
                    <div class="form-group col-lg-3">
                        <label>Product</label>
                        <a style="background: white; height: 2px !important" target="_blank" href="{{route('user.product.create')}}" title="Add" class="btn btn-sm btn-clean btn-icon btn-icon-md">
                            <i class="flaticon2-plus-1"></i>
                        </a>
                        <select name="product_id" class="form-control select2 product_id" id="product_id" onchange="getdata(this)" required>
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
                        <select name="category_id" class="form-control select2 category_id" id="category_id" onchange="getcompany(this)" required>
                            <option value="">Select</option>
                            @foreach($category as $key => $value)
                            <option value="{{$value->id}}">{{$value->name}}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group col-lg-3">
                        <label>Company  </label>
                        <select name="company_id" class="form-control select2 company_id" id="company_id" onchange="getproduct(this)"  required>
                            <option value="">Select</option>

                        </select>
                    </div>
                    <div class="form-group col-lg-3">
                        <label>Product  </label>
                        <a style="background: white; height: 2px !important" target="_blank" href="{{route('user.product.create')}}" title="Add" class="btn btn-sm btn-clean btn-icon btn-icon-md">
                            <i class="flaticon2-plus-1"></i>
                        </a>
                        <select name="product_id" class="form-control select2 product_id" id="product_id" onchange="getdata(this)" required>
                            <option value="">Select</option>

                        </select>
                    </div>
                </div>
                @endif
                @endif
                @if(Auth::guard('employee')->check())
                @if(Auth::user()->role == 4 || Auth::user()->role == 5)
                <div class="row">
                    @php $category = App\ProductCategory::where('branch_id',Auth::user()->branch_id)->get(); @endphp
                    <div class="form-group col-lg-3">
                        <label>Category  </label>
                        <select name="category_id" class="form-control select2 category_id" id="category_id" onchange="getcompany(this)" required>
                            <option value="">Select</option>
                            @foreach($category as $key => $value)
                            <option value="{{$value->id}}">{{$value->name}}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group col-lg-3">
                        <label>Company  </label>
                        <select name="company_id" class="form-control select2 company_id" id="company_id" onchange="getproduct(this)" required>
                            <option value="">Select</option>

                        </select>
                    </div>
                    <div class="form-group col-lg-3">
                        <label>Product  </label>
                        <a style="background: white; height: 2px !important" target="_blank" href="{{route('user.product.create')}}" title="Add" class="btn btn-sm btn-clean btn-icon btn-icon-md">
                            <i class="flaticon2-plus-1"></i>
                        </a>
                        <select name="product_id" class="form-control select2 product_id" id="product_id" onchange="getdata(this)" required>
                            <option value="">Select</option>

                        </select>
                    </div>
                </div>
                @endif
                @endif
                <div class="row">
                    <div class="form-group col-sm-12 master_clone_div_container">
                        <table cellspacing="0" border="0" class="table table-bordered" id="clone_table" style="width: 100% !important;">
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
                <div class="row">
                    <div class="form-group col-lg-8">
                    </div>
                    <div class="form-group col-lg-4">
                        <div class="row">
                            <div class="form-group col-lg-6">
                                <label><strong>Labour Charges </strong></label>
                            </div>
                            <div class="form-group col-lg-6">
                                <input type="number" name="labour_charges" class="form-control labour_charges" id="labour_charges" oninput="get_calculation()">
                            </div>
                        </div>
                        <div class="row">
                            <div class="form-group col-lg-6">
                                <label><strong>Transport Charges </strong></label>
                            </div>
                            <div class="form-group col-lg-6">
                                <input type="number" name="transport_charges" class="form-control transport_charges" id="transport_charges" oninput="get_calculation()">
                            </div>
                        </div>
                        <div class="row">
                            <div class="form-group col-lg-6">
                                <label><strong>Grand Total </strong></label>
                            </div>
                            <div class="form-group col-lg-6">
                                <input type="number" name="grand_total" class="form-control grand_total" id="grand_total" readonly>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
            <div class="kt-portlet__foot">
                <div class="kt-form__actions">
                    <center>
                        <button type="button" class="btn btn-brand submit_basic" id="submit_basic" >Submit<i class="la la-spinner change_spin d-none"></i></button>
                        <a href="{{route('user.purchaseinvoice.index')}}"><button type="button" class="btn btn-outline-secondary">Cancel</button></a>
                    </center>
                </div>
            </div>

        </div>

    </div>
</div>
@stop
@push('scripts')
<script type="text/javascript">
    $(document).ready(function() {
        $("#submit_basic").on("click", function(e) {
            e.preventDefault();
            if ($(".add_form").valid()) {
                showLoader();
                $('.submit_basic').find('.change_spin').removeClass('d-none');
                $('.submit_basic').prop('disabled', true);
                $.ajax({
                    type: "POST",
                    url: "{{ route('user.purchaseinvoice.store') }}",
                    data: new FormData($('.add_form')[0]),
                    processData: false,
                    contentType: false,
                    success: function(data) {
                        hideLoader();
                        if (data.status === 'success') {
                            window.location="{{ route('user.purchaseinvoice.index') }}";
                            toastr["success"]("Purchase Invoice Added Successfully", "Success");
                        } else if (data.status === 'error') {
                            $('.submit_basic').prop('disabled', false);
                            $('.submit_basic').find('.change_spin').addClass('d-none');
                            toastr["error"]("Something went wrong", "Error");
                        }else if(data.status === 'required'){
                            toastr["error"](data.message, "Error");
                            $('.submit_basic').prop('disabled', false);
                            $('.submit_basic').find('.change_spin').addClass('d-none');
                        }
                    },
                });
            } else {
                $('.submit_basic').prop('disabled', false);
                $('.submit_basic').find('.change_spin').addClass('d-none');
                e.preventDefault();
            }
        });
    });
    function getpo($this)
    {
        var purchase_id = $($this).val();
        if(purchase_id)
        {
            $.ajax({
                type: 'POST',
                url: '{{route("user.purchaseinvoice.getpo")}}',
                data: {
                    id: purchase_id,
                    _token: '{{ csrf_token()}}',
                },
                cache: false,
                success: function (data)
                {
                    $('#branch_id').val('');
                    $('#branch_id').val(data.branch);
                    $('#vendor_id').val(data.vendor);

                    $('#tableData').html('');
                    $('#tableData').html(data.data);
                }
            });
        }else{
            $('#tableData').html('');
            $('#branch_id').val('');

        }
    }
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
        $(document).on('click','.remove_container_details',function(){
            var num_of_master_clone_div_payment = $('.div_clone').length;
            if (num_of_master_clone_div_payment != 1) {
                var obj = $(this).closest('.div_clone');
                obj.remove();
            }
        });
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

    function clearBranchData()
    {
        $('#vendor_id').html('<option value="">Select</option>');
        $('.category_id').html('<option value="">Select</option>');
        $('.company_id').html('<option value="">Select</option>');
        $('.product_id').html('<option value="">Select</option>');
        $('#getdata').html('');
        $('#labour_charges').val('');
        $('#transport_charges').val('');
    }

    function getemployee($this){
        var branch_id = $($this).val();
        var id = $($this).data('id');

        clearBranchData();

        if(branch_id != '')
        {   
            showLoader();

            $.ajax({
                type:"POST",
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                url:"{{ route('user.po.getemployee') }}",
                data: {
                    'branch_id': branch_id
                },
                success: function(data){
                    $('#vendor_id').html(data.data);
                    $('#category_id').html(data.data4);

                    hideLoader();
                }
            });
        }
    };

    function getdata($this){
        var product_id =$($this).val();
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
                    url:"{{route('user.pi.getdata')}}",
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
        var customer = $($this).val();

        $('#state').val('');

        if(customer)
        {
            $.ajax({
                type: 'POST',
                url: '{{ route("user.get.vendor.data") }}',
                data: {
                    '_token': '{{ csrf_token() }}',
                    id: customer
                },
                dataType: 'json',
                success: function(data) {
                    if(data.status === 'success')
                    {
                        $('#state').val(data.data.state_id);
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
        }
        else
        {
            $(".cgstdiv").hide();
            $(".sgstdiv").hide();
            $(".igstdiv").show();
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

    function get_calculation(changed_id = null, changed_field = null)
    {
        var total_qty = 0;
        var total_discount = 0;
        var total_gst = 0;
        var total_cgst = 0;
        var total_sgst = 0;
        var total_igst = 0;
        var final_amount = 0;

        $('.qty').each(function(){
            var id = $(this).data('id');
            var qty = $('#qty_'+id).val();
            var rate = $('#rate_'+id).val();
            var discount = parseFloat($('#discount_'+id).val()) || 0;
            var discount_amount = $('#discount_amount_'+id).val();
            var gst = $('#gst_' + id + ' option:selected').text();

            qty = parseFloat(qty) || 0;
            rate = parseFloat(rate) || 0;
            discount_amount = parseFloat(discount_amount) || 0;
            gst = parseFloat(gst) || 0;
            var amount = parseFloat(qty) * parseFloat(rate); 

            if (changed_id == id && changed_field == 'discount') {
                discount_amount = (amount * discount) / 100;
                $('#discount_amount_' + id).val(discount_amount.toFixed(2));
            }

            if (changed_id == id && changed_field == 'discount_amount') {
                discount = (100 * discount_amount) / amount;
                $('#discount_' + id).val(discount.toFixed(2));
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

        var labour_charges = $('#labour_charges').val();
        var transport_charges = $('#transport_charges').val();

        labour_charges = parseFloat(labour_charges) || 0;
        transport_charges = parseFloat(transport_charges) || 0;

        final_amt = parseFloat(final_amount) + parseFloat(labour_charges) + parseFloat(transport_charges);

        $('#grand_total').val(parseFloat(final_amt).toFixed(2));
    }
</script>
@endpush