@extends('main')
@section('content')
<div class="kt-container  kt-container--fluid  kt-grid_item kt-grid_item--fluid">
    <br>
    <div class="kt-portlet kt-portlet--mobile">
        <div class="kt-portlet__head">
            <div class="kt-portlet__head-label">
                <h3 class="kt-portlet__head-title">
                    View Purchase Invoice
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
                    <div class="form-group col-lg-2">
                        <label>Branch  </label>
                        <input type="hidden" name="branch_id" value="{{$data->branch_id}}">
                        <input type="text" class="form-control" placeholder="Branch" value="{{ ($data->branch_id) ? $data->branch->name : '' }}" readonly>
                    </div>
                    @endif
                    @endif
                    <div class="form-group col-lg-2">
                        <label>Vendor  </label>
                        <input type="hidden" name="vendor_id" value="{{$data->vendor_id}}">
                        <input type="text" class="form-control" placeholder="Branch" value="{{ ($data->vendor_id) ? $data->vendor->name : '' }}" readonly>
                    </div>
                    <div class="form-group col-lg-2">
                        <label>Invoice No  </label>
                        <input type="text"  class="form-control" placeholder="Enter Invoice No" value="{{$data->invoice_no}}" name="invoice_no" readonly>
                    </div>
                    <div class="form-group col-lg-2">
                        <label>Order No  </label>
                        <input type="text"  class="form-control" placeholder="Enter Order No" value="{{$data->order_no}}" name="order_no" readonly>
                    </div>
                    <div class="form-group col-lg-2">
                        <label>Date  </label>
                        <input type="date"  class="form-control" value="{{$data->date}}" name="date" readonly>
                    </div>
                    <div class="form-group col-sm-2">
                        <label>Remark</label>
                        <textarea name="remark" class="form-control" placeholder="Enter Remark" readonly>{{ $data->remark }}</textarea>
                    </div>
                </div>
                <?php $vendor_state = App\Vendor::where('id',$data->vendor_id)->value('state_id'); ?>
                <input type="hidden" id="state" value="{{$vendor_state}}">
                <div class="row">
                    <div class="form-group col-sm-12 master_clone_div_container">
                        <table cellspacing="0" border="0" class="table table-bordered" id="clone_table" style="width: 100%;">
                            <thead>
                                <tr>
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
                                @foreach($child as $key => $value)
                                <tr class="edit_div_clone">
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
                                        <input type="number" style="width: 100% !important;" class="edit_qty" id="edit_qty_{{$value->id}}" placeholder="Enter Qty" name="edit_qty[]" value="{{$value->qty}}" data-id="{{$value->id}}" readonly >
                                    </td>
                                    <td style="font-size: 15px !important;">
                                        <input type="number" style="width: 100% !important;" class="edit_rate" id="edit_rate_{{$value->id}}" value="{{$value->rate}}" placeholder="Enter Rate"  name="edit_rate[]" data-id="{{$value->id}}" readonly  >
                                    </td>
                                    <td style="font-size: 15px !important;">
                                        <input type="number" style="width: 100% !important;" class="edit_discount" id="edit_discount_{{$value->id}}" value="{{$value->discount}}" placeholder="Enter Discount(%)" name="edit_discount[]" data-id="{{$value->id}}" readonly><br>
                                        <input type="number" style="width: 100% !important;" class="edit_discount_amount" id="edit_discount_amount_{{$value->id}}" value="{{$value->discount_amount}}" placeholder="Enter Discount Amount" name="edit_discount_amount[]" data-id="{{$value->id}}" readonly>
                                    </td>
                                    <td style="font-size: 15px !important;">
                                        <input type="text" class="edit_gst" value="{{ ($value->gst) ? $value->pro_gst->gst : '' }}" name="edit_gst[]" style="width: 100% !important;" id="edit_gst_{{$value->id}}" placeholder="Enter GST " data-id="{{$value->id}}" readonly  > <br>
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
                    <div class="form-group col-lg-8"></div>
                    <div class="form-group col-lg-4">
                        {{-- <div class="row">
                            <div class="form-group col-lg-4">
                                <label><strong>Flat Discount </strong></label>
                            </div>
                            <div class="form-group col-lg-4">
                                <input type="number" name="flat_discount" class="form-control flat_discount" id="flat_discount" value="{{$data->flat_discount}}" style="height: calc(0.5em + 1.3rem + 2px);" readonly>
                            </div>
                        </div> --}}
                        <div class="row">
                            <div class="form-group col-lg-4">
                                <label><strong>Labour Charges </strong></label>
                            </div>
                            <div class="form-group col-lg-4">
                                <input type="number" name="labour_charges" class="form-control labour_charges" id="labour_charges" value="{{$data->labour_charges}}" style="height: calc(0.5em + 1.3rem + 2px);" readonly>
                            </div>
                        </div>
                        <div class="row">
                            <div class="form-group col-lg-4">
                                <label><strong>Transport Charges </strong></label>
                            </div>
                            <div class="form-group col-lg-4">
                                <input type="number" name="transport_charges" class="form-control transport_charges" id="transport_charges" value="{{$data->transport_charges}}" style="height: calc(0.5em + 1.3rem + 2px);" readonly>
                            </div>
                        </div>
                        <div class="row">
                            <div class="form-group col-lg-4">
                                <label><strong>Grand Total </strong></label>
                            </div>
                            <div class="form-group col-lg-4">
                                <input type="number" name="grand_total" class="form-control grand_total" id="grand_total" style="height: calc(0.5em + 1.3rem + 2px);" readonly>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
            <div class="kt-portlet__foot">
                <div class="kt-form__actions">
                    <center>
                        <a href="{{route('user.purchaseinvoice.index',$id)}}"><button type="button" class="btn btn-outline-secondary">Cancel</button></a>
                    </center>
                </div>
            </div>
        </div>
    </div>
</div>
@stop
@push('scripts')
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

        $('.edit_qty').each(function(){
            var edit_id = $(this).data('id');
            var edit_qty = $('#edit_qty_'+edit_id).val();
            var edit_rate = $('#edit_rate_'+edit_id).val();
            var edit_discount_amount = $('#edit_discount_amount_'+edit_id).val();
            var edit_gst = $('#edit_gst_'+edit_id).val();

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
        });

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

        var flat_discount = $('#flat_discount').val();
        var labour_charges = $('#labour_charges').val();
        var transport_charges = $('#transport_charges').val();

        flat_discount = parseFloat(flat_discount) || 0;
        labour_charges = parseFloat(labour_charges) || 0;
        transport_charges = parseFloat(transport_charges) || 0;

        var edit_final_amt = parseFloat(edit_final_amount+final_amount).toFixed(2);
        final_amt = parseFloat(edit_final_amt) - parseFloat(flat_discount) + parseFloat(labour_charges) + parseFloat(transport_charges);

        $('#grand_total').val(parseFloat(final_amt).toFixed(2));
    }
</script>
@endpush