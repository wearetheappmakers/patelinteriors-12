@extends('main')
@section('content')
<meta name="csrf-token" content="{{ csrf_token() }}" />
<div class="kt-container  kt-container--fluid  kt-grid_item kt-grid_item--fluid">
    <br>
    <div class="kt-portlet kt-portlet--mobile">
        <div class="kt-portlet__head">
            <div class="kt-portlet__head-label">
                <h3 class="kt-portlet__head-title">
                    Edit Sales Invoice
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
                        <label>Branch  </label>
                        <input type="hidden" id="branch_id" value="{{ $data->branch_id }}" >
                        <input type="text" class="form-control" placeholder="Branch" value="{{ $data->branch ? $data->branch->name : '' }}" readonly >
                    </div>
                    @endif
                    <div class="form-group col-lg-3">
                        <label>Employee  </label>
                        <select name="employee_id" class="form-control select2" id="employee_id"  required >
                            <option value="">Select</option>
                            @foreach($employees as $sub)
                            <option value="{{ $sub->id }}" @if($data->employee_id == $sub->id) selected @endif>{{ $sub->name }}</option>
                            @endforeach
                        </select>
                    </div>  
                    @endif

                    @if(Auth::guard('employee')->check() && Auth::user()->role == 4)
                    <div class="form-group col-lg-3">
                        <label>Employee</label>
                        <select name="employee_id" class="form-control select2" id="employee_id"  required >
                            <option value="">Select</option>
                            @foreach($employees as $sub)
                            <option value="{{$sub->id}}" @if($data->employee_id == $sub->id) selected @endif>{{$sub->name}}</option>
                            @endforeach
                        </select>
                    </div>  
                    @endif
                    <div class="form-group col-lg-3">
                        <label>Client</label>
                        <input type="hidden" id="client_state_id" value="{{ $data->client ? $data->client->state_id : '' }}">
                        <input type="hidden" id="name" value="{{ $data->branch_id }}" >
                        <input type="text" class="form-control" placeholder="Client" value="{{ $data->client ? $data->client->member : '' }}" readonly >
                    </div>      

                    <div class="form-group col-lg-3">
                        <label>Pending SO </label>
                        <input type="text" class="form-control" placeholder="Pending SO" value="{{ $data->pendingso ? $data->pendingso->order_no : '' }}" readonly >
                    </div>
                    <div class="form-group col-lg-3">
                        <label>Invoice No. </label>
                        <input type="text" class="form-control" placeholder="Invoice No" value="{{ $data->invoice_no }}" readonly >
                    </div>
                    <div class="form-group col-lg-3">
                        <label>Date </label>
                        <input type="date" class="form-control" placeholder="Date" value="{{ $data->date }}" name="date" >
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
                                @if(count($data->child) > 0)
                                @foreach($data->child as $key => $val)
                                <tr class="master_clone">
                                    <td>
                                        @if(count($data->child) > 1)
                                        <div style="display: inline-flex !important;">
                                            <button type="button" data-value= "{{ $val->id }}" class="btn btn-sm btn-icon btn-icon-md btn-clean remove_clone"><i class="fa fa-minus" style="font-size:0.8rem !important"></i></button>
                                        </div>
                                        @endif
                                    </td>

                                    <td>
                                        <input type="hidden" name="hidden_id[]" value="{{ $val->id }}" class="hidden_id">
                                        <input type="hidden" name="salesorder_child_id[]" value="{{ $val->salesorder_child_id }}" class="salesorder_child_id">
                                        <input type="hidden" name="product_id[]" value="{{ $val->product_id }}" class="product_id">
                                        {{ $val->product ? $val->product->product : '' }}<br>
                                        {{ $val->product ? $val->product->code : '' }}
                                    </td>

                                    <td>
                                        <input type="hidden" name="warehouse_id[]" value="{{ $val->warehouse_id }}" class="warehouse_id">
                                        {{ $val->warehouse ? $val->warehouse->name : '' }}
                                    </td>

                                    <td>
                                    <?php
                                        $so_pending_qty = $val->sochilds ? $val->sochilds->pending_qty : 0;
                                        $pending_qty = $so_pending_qty + $val->qty;

                                        ?>

                                        <input type="number" name="pending_qty[]" value="{{ $pending_qty }}" placeholder="Pending Qty" class="pending_qty" style="width: 100% !important;" readonly>
                                        <input type="number" name="qty[]" value="{{ $val->qty }}" placeholder="Enter Qty" class="qty" style="width: 100% !important;">
                                        <input type="number" name="blocking_qty[]" value="{{ $val->blocking_qty }}" placeholder="Enter Blocking Qty" class="blocking_qty" style="width:100%">
                                    </td>

                                    <td><input type="number" name="rate[]" value="{{ $val->rate }}" placeholder="Enter Rate" class="rate" style="width: 100% !important;"></td>

                                    <td>
                                        <input type="number" name="discount_per[]" value="{{ $val->discount }}" placeholder="Enter Discount(%)" class="discount_per" style="width: 100% !important;">
                                        <input type="number" name="discount_amt[]" value="{{ $val->discount_amt }}" placeholder="Discount Amount" class="discount_amt" style="width: 100% !important;" >
                                    </td>

                                    <td>
                                        <input type="hidden" name="gst[]" value="{{ $val->gst }}" class="gst" style="width: 100% !important;">
                                        <select style="width: 100% !important;" class="gst_id" name="gst_id[]">
                                            <option value="">Select</option>
                                            @foreach($gst as $gst_key => $gst_val)
                                            <option value="{{ $gst_val->gst }}" @if($val->gst_id == $gst_val->id) selected @endif>{{ $gst_val->gst }}</option>
                                            @endforeach
                                        </select><br>
                                        <input type="text" placeholder="GST Amount" name="gst_amt[]" value="{{ $val->gst_amt }}" class="gst_amt" style="width: 100% !important;" readonly>
                                    </td>

                                    <td class="cgst_column" style="display: none;">
                                        <input type="text" placeholder="CGST" name="cgst[]" class="cgst" style="width: 100% !important;" readonly><br>
                                        <input type="text" placeholder="CGST Amount" name="cgst_amt[]" class="cgst_amt" style="width: 100% !important;" readonly>
                                    </td>

                                    <td class="cgst_column" style="display: none;">
                                        <input type="text" placeholder="SGST" name="sgst[]" class="sgst" style="width: 100% !important;" readonly><br>
                                        <input type="text" placeholder="SGST Amount" name="sgst_amt[]" class="sgst_amt" style="width: 100% !important;" readonly>
                                    </td>

                                    <td class="igst_column" style="display: none;">
                                        <input type="text" placeholder="IGST" name="igst[]" class="igst" style="width: 100% !important;" readonly><br>
                                        <input type="text" placeholder="IGST Amount" name="igst_amt[]" class="igst_amt" style="width: 100% !important;" readonly>
                                    </td>

                                    <td><input type="text" placeholder="Final Amount" name="amount[]" value="{{ $val->amount }}" class="amount" style="width: 100% !important;" readonly></td>

                                </tr>
                                @endforeach
                                @endif
                            </tbody>
                            <tfoot>
                                <tr>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td><input type="number" placeholder="Total Qty" name="total_qty" value="{{ $data->total_qty }}" class="total_qty" id="total_qty" readonly style="width: 100%;"></td>
                                    <td></td>
                                    <td><input type="number" placeholder="Total Discount" name="total_discount_amt" value="{{ $data->total_discount_amt }}" class="total_discount_amt" id="total_discount_amt" readonly style="width: 100%;"></td>
                                    <td><input type="number" placeholder="Total GST" name="total_gst_amt" value="{{ $data->total_gst_amt }}" class="total_gst_amt" id="total_gst_amt" readonly style="width: 100%;"></td>

                                    <td class="cgst_column" style="display: none;"><input type="number" placeholder="Total CGST" name="total_cgst_amt" class="total_cgst_amt" id="total_cgst_amt" readonly style="width: 100%;"></td>
                                    <td class="cgst_column" style="display: none;"><input type="number" placeholder="Total SGST" name="total_sgst_amt" class="total_sgst_amt" id="total_sgst_amt" readonly style="width: 100%;"></td>
                                    <td class="igst_column" style="display: none;"><input type="number" placeholder="Total IGST" name="total_igst_amt" class="total_igst_amt" id="total_igst_amt" readonly style="width: 100%;"></td> 

                                    <td><input type="number" name="total_amount" value="{{ $data->total_amount }}" class="total_amount" id="total_amount" readonly style="width: 100%;"></td>
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
                                <label>Labour Charges</label>
                            </div>
                            <div class="form-group col-lg-6">
                                <input type="number" name="labour_charges" value="{{ $data->labour_charges }}" placeholder="Enter Labour Charges" class="form-control labour_charges" id="labour_charges" >
                            </div>
                        </div>
                        <div class="row">
                            <div class="form-group col-lg-6">
                                <label>Transport Charges</label>
                            </div>
                            <div class="form-group col-lg-6">
                                <input type="number" name="transport_charges" value="{{ $data->transport_charges }}" placeholder="Enter Transport Charges" class="form-control transport_charges" id="transport_charges" >
                            </div>
                        </div>
                        <div class="row">
                            <div class="form-group col-lg-6">
                                <label>Grand Total</label>
                            </div>
                            <div class="form-group col-lg-6">
                                <input type="number" name="grand_total" value="{{ $data->grand_total }}" placeholder="Grand Total" class="form-control grand_total" id="grand_total"  readonly="">
                            </div>
                        </div>
                    </div>
                </div>
            </form>
            <div class="kt-portlet__foot">
                <div class="kt-form__actions">
                    <center>
                        <button type="button" class="btn btn-brand submit_basic" id="submit_basic">Update<i class="la la-spinner change_spin d-none"></i></button>
                        <a href="{{route('user.salesorderinvoice.index')}}"><button type="button" class="btn btn-outline-secondary">Cancel</button></a>
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
                    url: "{{ route('user.salesorderinvoice.update',$data->id) }}",
                    data: new FormData($('.add_form')[0]),
                    processData: false,
                    contentType: false,
                    success: function(data) {
                        hideLoader();
                        if (data.status === 'success') {
                            window.location="{{ route('user.salesorderinvoice.index') }}";
                            toastr["success"]("SalesOrder Invoice Updated Successfully", "Success");
                            $('.submit_basic').prop('disabled', false);
                            $('.submit_basic').find('.change_spin').addClass('d-none');
                        } else if (data.status === 'error') {
                            toastr["error"]("Something went wrong", "Error");
                            $('.submit_basic').prop('disabled', false);
                            $('.submit_basic').find('.change_spin').addClass('d-none');
                        }  else if (data.status === 'required') {
                            toastr["error"](data.message, "Error");
                            $('.submit_basic').prop('disabled', false);
                            $('.submit_basic').find('.change_spin').addClass('d-none');
                        }
                    },
                });
            } else {
                hideLoader();
                $('.submit_basic').prop('disabled', false);
                $('.submit_basic').find('.change_spin').addClass('d-none');
                e.preventDefault();
            }
        });
    });

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
            $(this).find('.qty').attr('data-id',i).attr('id','qty_'+i).on('input', get_calculation);
            $(this).find('.rate').attr('data-id',i).attr('id','rate_'+i).on('input', get_calculation);
            $(this).find('.discount_per').attr('data-id',i).attr('id','discount_per_'+i).on('input', get_calculation);
            $(this).find('.discount_amt').attr('data-id',i).attr('id','discount_amt_'+i).on('input', get_calculation);
            $(this).find('.gst').attr('data-id',i).attr('id','gst_'+i).on('input', get_calculation);
            $(this).find('.gst_id').attr('data-id',i).attr('id','gst_id_'+i).on('input', get_calculation);
            $(this).find('.gst_amt').attr('data-id',i).attr('id','gst_amt_'+i).on('input', get_calculation);
            $(this).find('.cgst').attr('data-id',i).attr('id','cgst_'+i).on('input', get_calculation);
            $(this).find('.cgst_amt').attr('data-id',i).attr('id','cgst_amt_'+i).on('input', get_calculation);
            $(this).find('.sgst').attr('data-id',i).attr('id','sgst_'+i).on('input', get_calculation);
            $(this).find('.sgst_amt').attr('data-id',i).attr('id','sgst_amt_'+i).on('input', get_calculation);
            $(this).find('.igst').attr('data-id',i).attr('id','igst_'+i).on('input', get_calculation);
            $(this).find('.igst_amt').attr('data-id',i).attr('id','igst_amt_'+i).on('input', get_calculation);
            $(this).find('.amount').attr('data-id',i).attr('id','amount_'+i).on('input', get_calculation);

            // var pending_qty = $('#pending_qty_'+i).val();
            // var qty = $('#qty_'+i).val();
            // var rate = $('#rate_'+i).val();
            // var discount_amt = $('#discount_amt_'+i).val();
            // var gst = $('#gst_'+i).val();

            // pending_qty = parseFloat(pending_qty) || 0;
            // qty = parseFloat(qty) || 0;
            // rate = parseFloat(rate) || 0;
            // discount_amt = parseFloat(discount_amt) || 0;
            // gst = parseFloat(gst) || 0;

            // var base_amt = parseFloat(qty) * parseFloat(rate);
            // if(discount_amt)
            // {
            //     var discount_per = (100*parseFloat(discount_amt))/parseFloat(base_amt);
            //     $('#discount_per_'+i).val(parseFloat(discount_per).toFixed(2));
            // }else{
            //     $('#discount_per_'+i).val(0);
            // }
            // var dis_amt = parseFloat(base_amt) - parseFloat(discount_amt);
            // var cgst = parseFloat(gst) / 2;
            // var gst_amt = (parseFloat(dis_amt) * parseFloat(gst)) / 100;
            // var cgst_amt = parseFloat(gst_amt) / 2;
            // var amount = parseFloat(dis_amt) + parseFloat(gst_amt); 

            // $('#gst_amt_'+i).val(gst_amt.toFixed(2));
            // $('#amount_'+i).val(amount.toFixed(2));


            // $('#cgst_'+i).val(cgst.toFixed(2));
            // $('#cgst_amt_'+i).val(cgst_amt.toFixed(2));
            // $('#sgst_'+i).val(cgst.toFixed(2));
            // $('#sgst_amt_'+i).val(cgst_amt.toFixed(2));
            // $('#igst_'+i).val(gst.toFixed(2));
            // $('#igst_amt_'+i).val(gst_amt.toFixed(2));


            // total_qty += qty;
            // total_discount_amt += discount_amt;
            // total_gst_amt += gst_amt;
            // total_cgst_amt += cgst_amt;
            // total_amount += amount;

            // if(qty > pending_qty) {
            //     var $toast = toastr["error"]("Qty. is greater than pending Qty.!!!", "Error");
            //     setTimeout(function(){
            //         $toast.fadeOut(500);
            //     }, 500);

            //     disableButton = true;
            // }
            get_calculation();
        });

        // grand_total = total_amount + labour_charges + transport_charges;

        // $('#total_qty').val(total_qty.toFixed(2));
        // $('#total_discount_amt').val(total_discount_amt.toFixed(2));
        // $('#total_gst_amt').val(total_gst_amt.toFixed(2));
        // $('#total_cgst_amt').val(total_cgst_amt.toFixed(2));
        // $('#total_sgst_amt').val(total_cgst_amt.toFixed(2));
        // $('#total_igst_amt').val(total_gst_amt.toFixed(2));
        // $('#total_amount').val(total_amount.toFixed(2));
        // $('#grand_total').val(grand_total.toFixed(2));


        // if(disableButton) {
        //     $('.submit_basic').prop('disabled', true);
        // } else {
        //     $('.submit_basic').prop('disabled', false);
        // }
}

$(document).on('input', '.discount_per', function () {
    var id = $(this).data('id');
    get_calculation(id, 'discount_per');
});

$(document).on('input', '.discount_amt', function () {
    var id = $(this).data('id');
    get_calculation(id, 'discount_amt');
});

function get_calculation(changed_id = null, changed_field = null)
{
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

    $('.qty').each(function(i, val){

        var pending_qty = $('#pending_qty_'+i).val();
        var qty = $('#qty_'+i).val();
        var rate = $('#rate_'+i).val();
        var discount_amt = $('#discount_amt_'+i).val();
        var gst = $('#gst_'+i).val();

        pending_qty = parseFloat(pending_qty) || 0;
        qty = parseFloat(qty) || 0;
        rate = parseFloat(rate) || 0;
        var discount = parseFloat($('#discount_per_'+i).val()) || 0;
        discount_amt = parseFloat(discount_amt) || 0;
        gst = parseFloat(gst) || 0;

        var base_amt = parseFloat(qty) * parseFloat(rate);

        if (changed_id == i && changed_field == 'discount_per') {
            discount_amt = (base_amt * discount) / 100;
            $('#discount_amt_' + i).val(discount_amt.toFixed(2));
        }

        if (changed_id == i && changed_field == 'discount_amt') {
            discount = (100 * discount_amt) / base_amt;
            $('#discount_per_' + i).val(discount.toFixed(2));
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
                $toast.fadeOut(5000);
            }, 5000);

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
        $('.submit_basic').prop('disabled', true);
    } else {
        $('.submit_basic').prop('disabled', false);
    }
}

$(document).ready(function() {
    $(".remove_clone").on("click", function(e)
    {
        var master_clone = $('.master_clone').length;
        if (master_clone != 1) 
        {
            if (confirm("Are you sure want to delete this item?"))
            {
                var id = $(this).data('value');
                var url = "{{ route('user.salesorderinvoice.deletechild',":rowid") }}";
                url = url.replace(':rowid',id);
                e.preventDefault();
                showLoader();
                $.ajax({
                    type: "GET",
                    url: url,
                    processData: false,
                    contentType: false,
                    success: function(data) {
                        if (data.status === 'success') {
                            location.reload();
                        } else if (data.status === 'error') {
                            location.reload();
                        } else if (data.status === 'required') {
                            toastr["error"](data.message, "Error");
                        }
                        hideLoader();
                    }
                });
            }
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
@endpush