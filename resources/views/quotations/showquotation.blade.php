<meta name="csrf-token" content="{{ csrf_token() }}" />
<div class="tab-pane active" id="quotation" role="tabpanel">
    <form class="kt-form kt-form--label-right edit_formssss" id="edit_formssss" method="POST" >
        @method('PUT')
        @csrf
        <div class="row">
            
            @if(Auth::guard('user')->check())
            @if(Auth::user()->role == 1)  
            <div class="form-group col-lg-3">
                <label>Branch  </label>
                <input type="text" class="form-control" placeholder="Branch" value="{{ $quotation->branch ? $quotation->branch->name : '' }}" readonly>
            </div>
            @endif
            <div class="form-group col-lg-3">
                <label>Employee  </label>
                <input type="text" class="form-control" placeholder="Employee" value="{{ $quotation->employee ? $quotation->employee->name : '' }}" readonly>
            </div>
            @endif
            
            <div class="form-group col-lg-3">
                <label>Client </label>
                <input type="text" class="form-control" placeholder="Client" value="{{ $quotation->client ? $quotation->client->member : '' }}" readonly>
            </div>
            <div class="form-group col-lg-3">
                <label>Quotation Date</label>
                <input type="date" class="form-control" placeholder="Quotation Date" value="{{ $quotation->quotation_date }}" name="quotation_date" disabled>
            </div>
            <div class="form-group col-lg-3">
                <label>Source Name</label>
                <input type="text" class="form-control"  placeholder="Source Name" value="{{ $quotation->sourcename ? $quotation->sourcename->name : '' }}" readonly>
            </div>
            <div class="form-group col-lg-3">
                <label>Status</label>
                <select class="form-control select2" name="status" id="status" disabled>
                    <option value="1" @if($quotation->status == 1) selected @endif>Pending</option>
                    <option value="2" @if($quotation->status == 2) selected @endif>Discussion</option>
                    <option value="3" @if($quotation->status == 3) selected @endif>Revision</option>
                    <option value="4" @if($quotation->status == 4) selected @endif>Win</option>
                    <option value="5" @if($quotation->status == 5) selected @endif>Lost</option>
                </select>
            </div>
        </div>
        <?php $last_main_id = 0 ?>
        
        {{-- QUOTATION EDIT --}}
        @if(isset($quotation))
        @foreach($quotation_main as $key=>$value)
        <?php $last_main_id = $key + 1 ?>
        <div class="master_clone_div master_clone_div_new" data-id="{{ $key }}">
            <div class="row col-sm-12" style="padding: 15px;background: #dede;">
                <div class="col-sm-2">
                    <label>Name</label>
                    <input type="text" name="name[]" class="form-control name" id="name_{{ $key }}" value="{{ $value->name }}" data-id="{{ $key }}" readonly>
                </div>
                <div class="form-group col-lg-2">
                    <label>Employee  </label>
                    <input type="text" class="form-control"  placeholder="Employee" value="{{ $value->employees ? $value->employees->name : '' }}" readonly>
                </div>
                <div class="form-group col-lg-2">
                    <label>Symbol  </label>
                    <input type="text" class="form-control"  placeholder="Symbol" value="{{ $value->symbols ? $value->symbols->name : '' }}" readonly>
                </div>
            </div>
            <br>
            <div class="row">
                <div class=" form-group col-sm-12" style="overflow: auto !important">
                    <table cellspacing="0" border="0" class="table table-bordered">
                        <thead>
                            <tr>
                                <th style="width:200px !important">Category</th>
                                <th style="width:200px !important">Company</th>
                                <th style="width:200px !important">Product</th>
                                <th style="width:200px !important">Warehouse</th>
                                <th style="width:200px !important" width="150px">Price</th>
                                <th style="width:200px !important">Qty</th>
                                <th style="width:200px !important" width="100px"></th>
                                <th style="width:200px !important">Discount</th>
                                <th style="width:200px !important">GST</th>
                                <th style="width:200px !important">Amount</th>
                                <th style="width:200px !important">Total</th>
                                <th style="width:200px !important">Apx. Delivery Date</th>
                                <th style="width:200px !important">Image</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php 
                            $leadchild = App\QuotationChild::where('quot_main_child_id',$value->id)->get();
                            @endphp
                            @foreach($leadchild as $lbc_key => $lbc_val)
                            <tr class="master_clone" id="master_clone_{{ $key }}" data-id="{{ $lbc_key }}" data-master="{{ $key }}">
                                <td>
                                    <input type="text" style="width: 200px !important;" class="form-control" placeholder="Category" value="{{ $lbc_val->productcategory ? $lbc_val->productcategory->name : '' }}" readonly>
                                </td>
                                <td>
                                    <input type="text" style="width: 300px !important;" class="form-control" placeholder="Company" value="{{ ($lbc_val->companys) ? $lbc_val->companys->name : '' }}" readonly>
                                </td>
                                <td> 
                                    <input type="text" style="width: 300px !important;" class="form-control" placeholder="Product" value="{{ ($lbc_val->products) ? $lbc_val->products->dropdown_name : '' }}" readonly>
                                </td>
                                <td>
                                    <input type="text" style="width: 300px !important;" class="form-control" placeholder="Warehouse" value="{{ ($lbc_val->warehouses) ? $lbc_val->warehouses->name : '' }}" readonly>
                                </td>
                                <td>
                                    <input type="number" class="form-control price" id="price_{{ $key }}_{{ $lbc_key }}" data-id="{{ $lbc_key }}" style="width:250px !important" data-master="{{ $key }}" name="price[{{ $key }}][]" readonly value="{{ $lbc_val->price }}">
                                </td>
                                <td>
                                    <input type="number" class="form-control qty" id="qty_{{ $key }}_{{ $lbc_key }}" name="qty[{{ $key }}][]" data-id="{{ $lbc_key }}" style="width:250px !important" data-master="{{ $key }}" value="{{ $lbc_val->qty }}" readonly>
                                </td>
                                <td>
                                    <select class="form-control value_of_amount" name="value_of_amount[{{ $key }}][]" id="value_of_amount_{{ $key }}_{{ $lbc_key }}" style="width:250px !important" data-id="{{ $lbc_key }}" data-master="{{ $key }}" disabled>
                                        <option value="1" @if($lbc_val->value_of_amount == 1) selected @endif>Project</option>
                                        <option value="2" @if($lbc_val->value_of_amount == 2) selected @endif>Retail</option>
                                    </select>
                                </td>
                                <td>
                                    <input type="number" class="form-control discount" placeholder="Discount" id="discount_{{ $key }}_{{ $lbc_key }}" name="discount[{{ $key }}][]" value="{{ $lbc_val->discount }}" style="width:250px !important" data-id="{{ $lbc_key }}" data-master="{{ $key }}" readonly>
                                </td>
                                <td>
                                    <input type="text" style="width: 300px !important;" id="gst_id_{{ $key }}_{{ $lbc_key }}" class="form-control gst_id" data-id="{{ $lbc_key }}" data-master="{{ $key }}" placeholder="GST" value="{{ $lbc_val->gst_id }}" readonly>
                                </td>
                                <td>
                                    <input type="number" class="form-control amount" id="amount_{{ $key }}_{{ $lbc_key }}" name="amount[{{ $key }}][]" value="{{ $lbc_val->amount }}" style="width:250px !important" data-id="{{ $lbc_key }}" data-master="{{ $key }}" readonly>
                                </td>
                                <td>
                                    <input type="number" class="form-control total" id="total_{{ $key }}_{{ $lbc_key }}" name="total[{{ $key }}][]" value="{{ $lbc_val->total }}" style="width:250px !important" data-id="{{ $lbc_key }}" data-master="{{ $key }}" readonly>
                                </td>
                                <td>
                                    <input type="date" class="form-control delivery_date" id="delivery_date_{{ $key }}_{{ $lbc_key }}" style="width:250px !important" value="{{ $lbc_val->delivery_date }}" name="delivery_date[{{ $key }}][]" data-id="{{ $lbc_key }}" data-master="{{ $key }}" disabled>
                                </td>
                                <td>
                                    <?php
                                    $html2 = \App\Gallery::where('productcategory_id',$lbc_val->productcategory_id)->where('product_id',$lbc_val->product_id)->where('company_id',$lbc_val->company_id)->latest()->first();
                                    $image = $html2 ? url('gallery/'.$html2->gallery) : '';
                                    ?>
                                    <img src="{{ $image }}" height="50px" class="image" id="image_{{ $key }}_{{ $lbc_key }}" name="image[{{ $key }}][]" width="attr" data-id="{{ $lbc_key }}" data-master="{{ $key }}">
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        @endforeach
        @endif
        {{-- END QUOTATION EDIT --}}
    </form>
    {{-- <div class="kt-portlet__foot">
        <div class="kt-form__actions">
            <center>
                <a href="{{route('user.quotations.index')}}"><button type="button" class="btn btn-outline-secondary">Back</button></a>
            </center>
        </div>
    </div> --}}
</div>
<script type="text/javascript">
    setInterval(getalltotals, 1000);
    function getalltotals()
    {
        $('.master_clone').each(function(){
            var id = $(this).data('id');
            var master = $(this).data('master');
            
            var qty = $('#qty_'+master+'_'+id).val();
            var price = $('#price_'+master+'_'+id).val();
            var discount = $('#discount_'+master+'_'+id).val();
            var gst = $('#gst_id_'+master+'_'+id).val();
            

            qty = parseFloat(qty) || 0;
            price = parseFloat(price) || 0;
            discount = parseFloat(discount) || 0;
            gst = parseFloat(gst) || 0;

            amount = (price * qty);
            
            var discount_amount = (amount * discount) / 100;
            var final_amount = amount - discount_amount; 
            var gst_amt = (final_amount * gst) / 100;
            var total = final_amount + gst_amt;

            $('#price_'+master+'_'+id).val(price.toFixed(2));
            $('#amount_'+master+'_'+id).val(final_amount.toFixed(2));
            $('#total_'+master+'_'+id).val(total.toFixed(2));
        });
    }
</script>