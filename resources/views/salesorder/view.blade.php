@extends('main')
@section('content')
<div class="kt-content  kt-grid__item kt-grid__item--fluid kt-grid kt-grid--hor" id="kt_content">
    <br>
    <div class="kt-container  kt-container--fluid  kt-grid__item kt-grid__item--fluid">        
        <link href="{{ asset('assets/plugins/custom/datatables/datatables.bundle.css')}}" rel="stylesheet" type="text/css" />
        <div class="kt-portlet kt-portlet--mobile">
            <div class="kt-portlet__head kt-portlet__head--lg">
                <div class="kt-portlet__head-label">
                    <h3 class="kt-portlet__head-title">
                        View Sales Order
                    </h3>
                </div>
                <div class="kt-portlet__head-toolbar">
                    <div class="kt-portlet__head-wrapper">
                        <div class="kt-portlet__head-actions">
                            <a href="{{ route('user.salesorder.index')}}" class="btn btn-brand btn-elevate btn-icon-sm">
                                <i class="flaticon2-fast-back"></i>
                                Back
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            <meta name="csrf-token" content="{{ csrf_token() }}" />
            <div class="kt-portlet__body">
                <div class="row">
                    @if(Auth::guard('user')->check())
                    @if(Auth::user()->role == 1)
                    <div class="form-group col-lg-3">
                        <label>Branch  </label>
                        <input type="hidden" id="branch_id" value="{{ $data->branch_id }}">
                        <input type="text" class="form-control" placeholder="Branch" value="{{ $data->branch ? $data->branch->name : '' }}" readonly>
                    </div>
                    @endif
                    <div class="form-group col-lg-3">
                        <label>Employee  </label>
                        <input type="text" class="form-control" placeholder="Employee" value="{{ $data->employee ? $data->employee->name : '' }}" readonly>
                    </div>
                    @endif

                    @if(Auth::guard('employee')->check() && Auth::user()->role == 4)
                    <div class="form-group col-lg-3">
                        <label>Employee </label>
                        <input type="text" class="form-control" placeholder="Employee" value="{{ $data->employee ? $data->employee->name : '' }}" readonly>
                    </div>    
                    @endif

                    <div class="form-group col-lg-3">
                        <label>Client</label>
                        <input type="hidden" id="client_state_id" value="{{ $data->client ? $data->client->state_id : '' }}">
                        <input type="text" class="form-control" value="{{ $data->client ? $data->client->member : '' }}" readonly>
                    </div>    
                    <div class="form-group col-lg-3">
                        <label>Order No. </label>
                        <input type="text" class="form-control" placeholder="Order No" value="{{ $data->order_no }}" readonly >
                    </div>
                    <div class="form-group col-lg-3">
                        <label>Date </label>
                        <input type="date" class="form-control" placeholder="Date" value="{{ $data->date }}" name="date" disabled>
                    </div>
                </div>
                <div class="row">
                    <div class="form-group col-sm-12 master_clone_div_container" style="overflow: auto !important">
                        <table cellspacing="0" border="0" class="table table-bordered" id="clone_table">
                            <thead>
                                <tr>
                                    {{-- <th width="1%"></th> --}}
                                    <th width="15%">Product Name <br>Code</th>
                                    <th width="10%">Warehouse</th>
                                    <th width="10%">Qty</th>
                                    <th width="10%">Rate</th>
                                    <th width="10%">Dis<br>Dis Amt</th>
                                    <th width="10%">GST<br>GST Amt</th>

                                    <th class="cgst_column" width="8%" style="display: none;">CGST<br>CGST Amt</th>
                                    <th class="cgst_column" width="8%" style="display: none;">SGST<br>SGST Amt</th>
                                    <th class="igst_column" width="8%" style="display: none;">IGST<br>IGST Amt</th>

                                    <th width="10%">Final Amt</th>
                                </tr>
                            </thead>
                            <tbody id="getdata">
                                @if(count($data->salesorderchild) > 0)
                                @foreach ($data->salesorderchild as $key => $val)
                                <tr class="master_clone">
                                    <td>
                                        <input type="hidden" name="hidden_id[]" value="{{ $val->id }}" class="hidden_id">
                                        <input type="hidden" name="product_id[]" value="{{ $val->product_id }}" class="product_id">
                                        {{ $val->product ? $val->product->product : '' }}<br>
                                        {{ $val->product ? $val->product->code : '' }}
                                    </td>

                                    <td>
                                        {{ $val->warehouse ? $val->warehouse->name : '' }}
                                    </td>

                                    <td><input type="number" name="qty[]" value="{{ $val->qty }}" placeholder="Qty" class="qty" style="width: 100% !important;" readonly></td>

                                    <td><input type="number" name="rate[]" value="{{ $val->rate }}" placeholder="Rate" class="rate" style="width: 100% !important;" readonly></td>

                                    <td>
                                        <input type="number" name="discount_per[]" value="{{ $val->discount_per }}" placeholder="Discount(%)" class="discount_per" style="width: 100% !important;" readonly>
                                        <input type="number" name="discount_amt[]" value="{{ $val->discount_amt }}" placeholder="Discount Amount" class="discount_amt" style="width: 100% !important;" readonly>
                                    </td>

                                    <td>
                                        <input type="text" name="gst[]" value="{{ $val->gst }}" class="gst" style="width: 100% !important;">
                                        <br>
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
                                    {{-- <td></td> --}}
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
            </div>
        </div>
    </div>
    @stop
    @push('scripts')
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

          $('.master_clone').each(function(i,val){

               $(this).attr('data-id',i).attr('id','master_clone_'+i);
               $(this).find('.hidden_id').attr('data-id',i).attr('id','hidden_id_'+i);
               $(this).find('.product_id').attr('data-id',i).attr('id','product_id_'+i);
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
               
               var qty = $('#qty_'+i).val();
               var rate = $('#rate_'+i).val();
               var discount_amt = $('#discount_amt_'+i).val();
               var gst = $('#gst_'+i).val();

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
          });
          $('#total_qty').val(total_qty.toFixed(2));
          $('#total_discount_amt').val(total_discount_amt.toFixed(2));
          $('#total_gst_amt').val(total_gst_amt.toFixed(2));
          $('#total_cgst_amt').val(total_cgst_amt.toFixed(2));
          $('#total_sgst_amt').val(total_cgst_amt.toFixed(2));
          $('#total_igst_amt').val(total_gst_amt.toFixed(2));
          $('#total_amount').val(total_amount.toFixed(2));
     }
    </script>