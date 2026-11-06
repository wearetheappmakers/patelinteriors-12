<meta name="csrf-token" content="{{ csrf_token() }}" />
<div class="tab-pane active" id="quotation" role="tabpanel">
    <form class="kt-form kt-form--label-right edit_formssss" id="edit_formssss" method="POST" >
        @method('PUT')
        @csrf
        <div class="row">
            
            @if(isset($quotation))
            <input type="hidden" name="form_type" value="edit">
            <input type="hidden" name="quotation_id" value="{{ $quotation->id }}">
            @endif

            @if(Auth::guard('user')->check())
            @if(Auth::user()->role == 1)  
            <div class="form-group col-lg-3">
                <label>Branch  </label>
                <input type="hidden" name="branch_id" value="{{ isset($quotation) ? $quotation->branch_id : $data->branch_id }}" id="branch_id_tx">
                <input type="text" class="form-control" placeholder="Branch" value="{{ isset($quotation) ? ($quotation->branch ? $quotation->branch->name : '') : ($data->branch ? $data->branch->name : '') }}" readonly>
            </div>
            @endif
            <div class="form-group col-lg-3">
                <label>Employee  </label>
                <input type="hidden" name="employee_id" value="{{ isset($quotation) ? $quotation->employee_id : $data->employee_id }}" id="employee_id_tx">
                <input type="text" class="form-control" placeholder="Employee" value="{{ isset($quotation) ? ($quotation->employee ? $quotation->employee->name : '') : ($data->employee ? $data->employee->name : '') }}" readonly>
            </div>
            @endif
            
            <div class="form-group col-lg-3">
                <label>Client </label>
                <input type="hidden" name="client_id" value="{{ isset($quotation) ? $quotation->client_id : $data->client_id }}" id="client_id_tx">
                <input type="text" class="form-control" placeholder="Client" value="{{ isset($quotation) ? ($quotation->client ? $quotation->client->member : '') : ($data->client ? $data->client->member : '') }}" readonly>
            </div>
            <div class="form-group col-lg-3">
                <label>Quotation Date</label>
                <input type="date" class="form-control" placeholder="Quotation Date" value="{{ isset($quotation) ? $quotation->quotation_date : date('Y-m-d') }}" name="quotation_date" required>
            </div>
            <div class="form-group col-lg-3">
                <label>Source Name</label>
                <input type="hidden" name="source_name" value="{{ isset($quotation) ? $quotation->source_name : $data->source_name }}">
                <input type="text" class="form-control"  placeholder="Source Name" value="{{ isset($quotation) ? ($quotation->sourcename ? $quotation->sourcename->name : '') : ($data->sourcename ? $data->sourcename->name : '') }}" readonly>
            </div>
            <div class="form-group col-lg-3">
                <label>Status</label>
                <select class="form-control select2" name="status" id="status">
                    @php $quote_status = isset($quotation) ? $quotation->status : $data->status; @endphp
                    <option value="1" @if($quote_status == 1) selected @endif>Pending</option>
                    <option value="2" @if($quote_status == 2) selected @endif>Discussion</option>
                    <option value="3" @if($quote_status == 3) selected @endif>Revision</option>
                    <option value="4" @if($quote_status == 4) selected @endif>Win</option>
                    <option value="5" @if($quote_status == 5) selected @endif>Lost</option>
                </select>
            </div>

            @if(isset($quotation))
            <div class="form-group col-lg-3">
                <label>Reason</label>
                <input type="text" class="form-control"  placeholder="Revised"  name="revised" required>
            </div>
            @endif
        </div>
        <?php $last_main_id = 0 ?>
        
        {{-- QUOTATION EDIT --}}
        @if(isset($quotation))
        @foreach($quotation_main as $key=>$value)
        <?php $last_main_id = $key + 1 ?>
        <div class="master_clone_div master_clone_div_new" data-id="{{ $key }}">
            <div class="row col-sm-12" style="padding: 15px;background: #dede;">
                <div class="col-sm-1">
                    <div style="display: inline-flex !important;">
                        <a href="{{ route('user.quotations.deletemainchild',$value->id) }}" onclick="return confirm(`Are You Sure?`)" class="btn btn-sm btn-label-brand btn-bold expbtn btn-icon btn-icon-md"><i class="fa fa-minus"></i></a>
                    </div>
                </div>
                <div class="col-sm-2">
                    <label>Name</label>
                    <input type="hidden" name="hidden_main[]" class="hidden_main" value="{{ $value->id }}" id="hidden_main_{{ $key }}" data-id="{{ $key }}">
                    <input type="text" name="name[]" class="form-control name" id="name_{{ $key }}" value="{{ $value->name }}" data-id="{{ $key }}" >
                </div>
                <div class="form-group col-lg-2">
                    <label>Employee  </label>
                    <input type="hidden" name="main_employee_id[]" value="{{ $value->employee_id }}" id="employee_id_{{ $key }}" data-id="{{ $key }}">
                    <select id="employee_id_{{$value->id}}" data-id="{{ $key }}" class="form-control employee_id select2" disabled>
                        <option value="">Select Employee</option>
                        @foreach($employee as $sub)
                        <option value="{{$sub->id}}" @if($sub->id == $value->employee_id) selected @endif>{{$sub->name}}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group col-lg-2">
                    <label>Symbol  </label>
                    <input type="hidden" name="symbol[]" value="{{ $value->symbol }}" id="symbol_{{ $key }}" data-id="{{ $key }}">
                    <select id="symbol_{{ $key }}" data-id="{{ $key }}" class="form-control symbol select2" disabled>
                        <option value="">Select Symbol</option>
                        @foreach($symbol as $sub)
                        <option value="{{$sub->id}}" @if($sub->id == $value->symbol) selected @endif>{{$sub->name}}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <br>
            <div class="row">
                <div class=" form-group col-sm-12" style="overflow: auto !important">
                    <table cellspacing="0" border="0" class="table table-bordered">
                        <thead>
                            <tr>
                                <th></th>
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
                                    <div style="display: inline-flex !important;">
                                        <a href="{{ route('user.quotations.deletechild',$lbc_val->id) }}" onclick="return confirm(`Are You Sure?`)" class="btn btn-sm btn-icon btn-icon-md btn-clean" ><i class="fa fa-minus" style="font-size:0.8rem !important"></i></a>
                                    </div>
                                </td>
                                <td>
                                    <input type="hidden" class="hidden_child" name="hidden_child[{{ $key }}][]" value="{{ $lbc_val->id }}" id="hidden_child_{{ $key }}_{{ $lbc_key }}" data-id="{{ $lbc_key }}" data-master="{{$key}}">

                                    <input type="hidden" name="productcategory_id[{{ $key }}][]" value="{{ $lbc_val->productcategory_id }}" id="productcategory_id_{{ $key }}_{{ $lbc_key }}" data-id="{{ $lbc_key }}" data-master="{{ $key }}">
                                    <input type="text" style="width: 200px !important;" class="form-control" placeholder="Category" value="{{ $lbc_val->productcategory->name }}" readonly>
                                </td>
                                <td>
                                    <input type="hidden" name="company_id[{{ $key }}][]" value="{{ $lbc_val->company_id }}" id="company_id_{{ $key }}_{{ $lbc_key }}" data-id="{{ $lbc_key }}" data-master="{{ $key }}">
                                    <input type="text" style="width: 300px !important;" class="form-control" placeholder="Company" value="{{ ($lbc_val->companys) ? $lbc_val->companys->name : '' }}" readonly>
                                </td>
                                <td> 
                                    <input type="hidden" name="product_id[{{ $key }}][]" value="{{ $lbc_val->product_id }}" id="product_id_{{ $key }}_{{ $lbc_key }}" data-id="{{ $lbc_key }}" data-master="{{ $key }}">
                                    <input type="text" style="width: 300px !important;" class="form-control" placeholder="Product" value="{{ ($lbc_val->products) ? $lbc_val->products->dropdown_name : '' }}" readonly>
                                </td>
                                <td>
                                    <input type="hidden" name="warehouse_id[{{ $key }}][]" value="{{ $lbc_val->warehouse_id }}" id="warehouse_id_{{ $key }}_{{ $lbc_key }}" data-id="{{ $lbc_key }}" data-master="{{ $key }}">
                                    <input type="text" style="width: 300px !important;" class="form-control" placeholder="Warehouse" value="{{ ($lbc_val->warehouses) ? $lbc_val->warehouses->name : '' }}" readonly>
                                </td>
                                <td>
                                    @php
                                    $price = App\Product::where('id',$lbc_val->product_id)->value('price');
                                    @endphp
                                    <input type="number" class="form-control price" id="price_{{ $key }}_{{ $lbc_key }}" data-id="{{ $lbc_key }}" style="width:250px !important" data-master="{{ $key }}" name="price[{{ $key }}][]" readonly value="{{ $price }}">
                                </td>
                                <td>
                                    <input type="number" class="form-control qty" id="qty_{{ $key }}_{{ $lbc_key }}" name="qty[{{ $key }}][]" data-id="{{ $lbc_key }}" style="width:250px !important" data-master="{{ $key }}" value="{{ $lbc_val->qty }}">
                                </td>
                                <td>
                                    <select class="form-control value_of_amount" name="value_of_amount[{{ $key }}][]" id="value_of_amount_{{ $key }}_{{ $lbc_key }}" style="width:250px !important" data-id="{{ $lbc_key }}" data-master="{{ $key }}">
                                        <option value="1" @if($lbc_val->value_of_amount == 1) selected @endif>Project</option>
                                        <option value="2" @if($lbc_val->value_of_amount == 2) selected @endif>Retail</option>
                                    </select>
                                </td>
                                <td>
                                    <input type="number" class="form-control discount" id="discount_{{ $key }}_{{ $lbc_key }}" name="discount[{{ $key }}][]" value="{{ $lbc_val->discount }}" style="width:250px !important" data-id="{{ $lbc_key }}" data-master="{{ $key }}">
                                </td>
                                <td>
                                    <select name="gst_id[{{ $key }}][]" id="gst_id_{{ $key }}_{{ $lbc_key }}" class="form-control gst_id" style="width:250px !important" data-id="{{ $lbc_key }}" data-master="{{ $key }}">
                                        <option value="">Select GST</option>
                                        @foreach($gst as $sub)
                                        <option value="{{$sub->gst}}" @if($lbc_val->gst_id == $sub->gst) selected @endif>{{$sub->gst}}</option>
                                        @endforeach
                                    </select>
                                </td>
                                <td>
                                    <input type="number" class="form-control amount" id="amount_{{ $key }}_{{ $lbc_key }}" name="amount[{{ $key }}][]" value="{{ $lbc_val->amount }}" style="width:250px !important" data-id="{{ $lbc_key }}" data-master="{{ $key }}" readonly>
                                </td>
                                <td>
                                    <input type="number" class="form-control total" id="total_{{ $key }}_{{ $lbc_key }}" name="total[{{ $key }}][]" value="{{ $lbc_val->total }}" style="width:250px !important" data-id="{{ $lbc_key }}" data-master="{{ $key }}" readonly>
                                </td>
                                <td>
                                    <input type="date" class="form-control delivery_date" id="delivery_date_{{ $key }}_{{ $lbc_key }}" style="width:250px !important" value="{{ $lbc_val->delivery_date }}" name="delivery_date[{{ $key }}][]" data-id="{{ $lbc_key }}" data-master="{{ $key }}">
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
        {{-- END QUOTATION EDIT --}}
        
        {{-- CONVERT QUOTATION FROM LEAD --}}
        @elseif($leadmain)
        @foreach($leadmain as $key=>$value)
        <?php $last_main_id = $last_main_id + 1 ?>
        <div class="master_clone_div master_clone_div_new" data-id="{{ $key }}">
            <div class="row col-sm-12" style="padding: 15px;background: #dede;">
                <div class="col-sm-1">
                    <div style="display: inline-flex !important;">
                        <button type="button" id="remove_container" data-id='{{ $key }}' class="btn btn-sm btn-label-brand btn-bold expbtn btn-icon btn-icon-md remove_container"><i class="fa fa-minus" style=""></i></button>
                    </div>
                </div>
                <div class="col-sm-2">
                    <label>Name</label>
                    <input type="hidden" name="hidden_main[]" class="hidden_main" id="hidden_main_{{ $key }}" data-id="{{ $key }}">
                    <input type="text" name="name[]" class="form-control name" id="name_{{ $key }}" value="{{ $value->name }}" data-id="{{ $key }}" >
                </div>
                <div class="form-group col-lg-2">
                    <label>Employee  </label>
                    <input type="hidden" name="main_employee_id[]" value="{{ $value->employee_id }}" id="employee_id_{{ $key }}" data-id="{{ $key }}">
                    <select id="employee_id_{{$value->id}}" data-id="{{ $key }}" class="form-control employee_id select2" disabled>
                        <option value="">Select Employee</option>
                        @foreach($employee as $sub)
                        <option value="{{$sub->id}}" @if($sub->id == $value->employee_id) selected @endif>{{$sub->name}}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group col-lg-2">
                    <label>Symbol  </label>
                    <input type="hidden" name="symbol[]" value="{{ $value->symbol }}" id="symbol_{{ $key }}" data-id="{{ $key }}">
                    <select id="symbol_{{ $key }}" data-id="{{ $key }}" class="form-control symbol select2" disabled>
                        <option value="">Select Symbol</option>
                        @foreach($symbol as $sub)
                        <option value="{{$sub->id}}" @if($sub->id == $value->symbol) selected @endif>{{$sub->name}}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <br>
            <div class="row">
                <div class=" form-group col-sm-12" style="overflow: auto !important">
                    <table cellspacing="0" border="0" class="table table-bordered">
                        <thead>
                            <tr>
                                <th></th>
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
                            $leadchild = App\LeadBasicChild::where('lead_basic_main_id',$value->id)->get();
                            @endphp
                            @foreach($leadchild as $lbc_key => $lbc_val)
                            <tr class="master_clone master_clone_new" id="master_clone_{{ $key }}" data-id="{{ $lbc_key }}" data-master="{{ $key }}">
                                <td>
                                    <div style="display: inline-flex !important;">
                                        <button type="button" id="remove_2d_clone" data-id='{{ $lbc_key }}' class="btn btn-sm btn-icon btn-icon-md btn-clean remove remove_2d_clone" ><i class="fa fa-minus" style="font-size:0.8rem !important"></i></button>
                                    </div>
                                </td>
                                <td>
                                    <input type="hidden" class="hidden_child" name="hidden_child[{{ $key }}][]" id="hidden_child_{{ $key }}_{{ $lbc_key }}" data-id="{{ $lbc_key }}" data-master="{{$key}}">

                                    <input type="hidden" name="productcategory_id[{{ $key }}][]" value="{{ $lbc_val->productcategory_id }}" id="productcategory_id_{{ $key }}_{{ $lbc_key }}" data-id="{{ $lbc_key }}" data-master="{{ $key }}">
                                    <input type="text" style="width: 200px !important;" class="form-control" placeholder="Category" value="{{ $lbc_val->productcategory->name }}" readonly>
                                </td>
                                <td>
                                    <input type="hidden" name="company_id[{{ $key }}][]" value="{{ $lbc_val->company_id }}" id="company_id_{{ $key }}_{{ $lbc_key }}" data-id="{{ $lbc_key }}" data-master="{{ $key }}">
                                    <input type="text" style="width: 300px !important;" class="form-control" placeholder="Company" value="{{ ($lbc_val->company) ? $lbc_val->company->name : '' }}" readonly>
                                </td>
                                <td> 
                                    <input type="hidden" name="product_id[{{ $key }}][]" value="{{ $lbc_val->product_id }}" id="product_id_{{ $key }}_{{ $lbc_key }}" data-id="{{ $lbc_key }}" data-master="{{ $key }}">
                                    <input type="text" style="width: 300px !important;" class="form-control" placeholder="Product" value="{{ ($lbc_val->product) ? $lbc_val->product->dropdown_name : '' }}" readonly>
                                </td>
                                <td>
                                    <?php $warehouse = App\Warehouse::where('id',$lbc_val->warehouse_id)->value('name') ?>
                                    <input type="hidden" name="warehouse_id[{{ $key }}][]" value="{{ $lbc_val->warehouse_id }}" id="warehouse_id_{{ $key }}_{{ $lbc_key }}" data-id="{{ $lbc_key }}" data-master="{{ $key }}">
                                    <input type="text" style="width: 300px !important;" class="form-control" placeholder="Warehouse" value="{{ $warehouse }}" readonly>
                                </td>
                                <td>
                                    @php
                                    $price = App\Product::where('id',$lbc_val->product_id)->value('price');
                                    @endphp
                                    <input type="number" class="form-control price" id="price_{{ $key }}_{{ $lbc_key }}" data-id="{{ $lbc_key }}" style="width:250px !important" data-master="{{ $key }}" name="price[{{ $key }}][]" readonly value="{{ $price }}">
                                </td>
                                <td>
                                    <input type="number" class="form-control qty" id="qty_{{ $key }}_{{ $lbc_key }}" name="qty[{{ $key }}][]" value="{{ $lbc_val->qty }}" data-id="{{ $lbc_key }}" style="width:250px !important" data-master="{{ $key }}">
                                </td>
                                <td>
                                    <select class="form-control value_of_amount" name="value_of_amount[{{ $key }}][]" id="value_of_amount_{{ $key }}_{{ $lbc_key }}" style="width:250px !important" data-id="{{ $lbc_key }}" data-master="{{ $key }}">
                                        <option value="1">Project</option>
                                        <option value="2">Retail</option>
                                    </select>
                                </td>
                                <td>
                                    <input type="number" class="form-control discount" id="discount_{{ $key }}_{{ $lbc_key }}" name="discount[{{ $key }}][]" style="width:250px !important" data-id="{{ $lbc_key }}" data-master="{{ $key }}">
                                </td>
                                <td>
                                    <select name="gst_id[{{ $key }}][]" id="gst_id_{{ $key }}_{{ $lbc_key }}" class="form-control gst_id" style="width:250px !important" data-id="{{ $lbc_key }}" data-master="{{ $key }}">
                                        <option value="">Select GST</option>
                                        @foreach($gst as $sub)
                                        <option value="{{$sub->gst}}">{{$sub->gst}}</option>
                                        @endforeach
                                    </select>
                                </td>
                                <td>
                                    <input type="number" class="form-control amount" id="amount_{{ $key }}_{{ $lbc_key }}" name="amount[{{ $key }}][]" style="width:250px !important" data-id="{{ $lbc_key }}" data-master="{{ $key }}" readonly>
                                </td>
                                <td>
                                    <input type="number" class="form-control total" id="total_{{ $key }}_{{ $lbc_key }}" name="total[{{ $key }}][]" style="width:250px !important" data-id="{{ $lbc_key }}" data-master="{{ $key }}" readonly>
                                </td>
                                <td>
                                    <input type="date" class="form-control delivery_date" id="delivery_date_{{ $key }}_{{ $lbc_key }}" style="width:250px !important" value="{{date('Y-m-d')}}" name="delivery_date[{{ $key }}][]" data-id="{{ $lbc_key }}" data-master="{{ $key }}">
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
        {{-- END CONVERT QUOTATION FROM LEAD --}}
        

        {{-- NEW CLONE --}}
        <div class="master_clone_div master_clone_div_new" data-id="0">
            <div class="row col-sm-12" style="padding: 15px;background: #dede;">
                <div class="col-sm-1">
                    <div style="display: inline-flex !important;">
                        <button type="button" id="add_more_container" class="btn btn-sm btn-label-brand btn-bold expbtn btn-icon btn-icon-md add_more_container"><i class="fa fa-plus" style=""></i></button>
                        &nbsp;&nbsp;&nbsp;
                        <button type="button" id="remove_container" data-id='0' class="btn btn-sm btn-label-brand btn-bold expbtn btn-icon btn-icon-md remove_container"><i class="fa fa-minus" style=""></i></button>
                    </div>
                </div>
                <div class="col-sm-2">
                    <label>Name</label>
                    <input type="hidden" name="hidden_main[]" class="hidden_main" id="hidden_main_{{$last_main_id}}" data-id="{{$last_main_id}}">
                    <input type="text" name="name[]" class="form-control name" id="name_{{$last_main_id}}" data-id="{{$last_main_id}}">
                </div>
                <div class="form-group col-lg-2">
                    <label>Employee</label>
                    <select name="main_employee_id[]" id="employee_id_{{$last_main_id}}" data-id="{{$last_main_id}}" class="form-control employee_id select2">
                        <option value="">Select Employee</option>
                        @foreach($employee as $sub)
                        <option value="{{$sub->id}}">{{$sub->name}}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group col-lg-2">
                    <label>Symbol</label>
                    <select name="symbol[]" id="symbol_{{$last_main_id}}" data-id="{{$last_main_id}}" class="form-control symbol select2">
                        <option value="">Select Symbol</option>
                        @foreach($symbol as $sub)
                        <option value="{{$sub->id}}">{{$sub->name}}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <br>
            <div class="row">
                <div class=" form-group col-sm-12" style="overflow: auto !important">
                    <table cellspacing="0" border="0" class="table table-bordered">
                        <thead>
                            <tr>
                                <th></th>
                                <th style="width:200px !important" style="width:200px !important">Category</th>
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
                            <tr class="master_clone master_clone_new" id="master_clone_{{ $last_main_id }}" data-id="0" data-master="{{ $last_main_id }}">
                                <td>
                                    <div style="display: inline-flex !important;">
                                        <button type="button" id="add_more" class="btn btn-sm btn-clean btn-icon btn-icon-md add_more"><i class="fa fa-plus" style="font-size:0.8rem !important"></i></button>
                                        &nbsp;&nbsp;&nbsp;
                                        <button type="button" id="remove_2d_clone" data-id='0' class="btn btn-sm btn-icon btn-icon-md btn-clean remove_2d_clone remove_2d_clone" ><i class="fa fa-minus" style="font-size:0.8rem !important"></i></button>
                                    </div>
                                </td>
                                <td>
                                    <input type="hidden" class="hidden_child" name="hidden_child[{{ $last_main_id }}][]" id="hidden_child_{{ $last_main_id }}_0" data-id="0" data-master="{{ $last_main_id }}">
                                    <select class="form-control productcategory_id select2" id="productcategory_id_{{ $last_main_id }}_0" name="productcategory_id[{{ $last_main_id }}][]" data-id="0" data-master="{{ $last_main_id }}" style="width:200px !important" onchange="getcompany(this)">
                                        <option value="">Select</option>
                                        @foreach($productcategory as $value)
                                        <option value="{{ $value->id }}">{{ $value->name }}</option>
                                        @endforeach
                                    </select>
                                </td>
                                <td>
                                    <select class="form-control select2 company_id" id="company_id_{{ $last_main_id }}_0" name="company_id[{{ $last_main_id }}][]" style="width:200px !important" data-id="0" data-master="{{ $last_main_id }}" onchange="getproduct(this)">
                                        <option value="">Select</option>
                                    </select>
                                </td>
                                <td>
                                    <select class="form-control select2 product_id" id="product_id_{{ $last_main_id }}_0" name="product_id[{{ $last_main_id }}][]" data-id="0" data-master="{{ $last_main_id }}" style="width:200px !important" onchange="getwarehouse(this)">
                                        <option value="">Select</option>
                                    </select>
                                </td>
                                <td>
                                    <select class="form-control select2 warehouse_id" id="warehouse_id_{{ $last_main_id }}_0" style="width:200px !important"  name="warehouse_id[{{ $last_main_id }}][]" data-id="0" data-master="{{ $last_main_id }}">
                                        <option value="">Select</option>
                                    </select>
                                </td>
                                <td>
                                    <input type="number" class="form-control price" id="price_{{ $last_main_id }}_0" name="price[{{ $last_main_id }}][]" readonly data-id="0" data-master="{{ $last_main_id }}" style="width:250px !important">
                                </td>
                                <td>
                                    <input type="number" name="qty[{{ $last_main_id }}][]" class="form-control qty" id="qty_{{ $last_main_id }}_0" data-id="0" style="width: 250px !important;" data-master="{{ $last_main_id }}">
                                </td>
                                <td>
                                    <select class="form-control value_of_amount" name="value_of_amount[{{ $last_main_id }}][]" id="value_of_amount_{{ $last_main_id }}_0" data-id="0" data-master="{{ $last_main_id }}" style="width:250px !important">
                                        <option value="1">Project</option>
                                        <option value="2">Retail</option>
                                    </select>
                                </td>
                                <td>
                                    <input type="number" class="form-control discount" id="discount_{{ $last_main_id }}_0" name="discount[{{ $last_main_id }}][]" data-id="0" data-master="{{ $last_main_id }}" style="width:250px !important">
                                </td>
                                <td>
                                    <select name="gst_id[{{ $last_main_id }}][]" id="gst_id_{{ $last_main_id }}_0" class="form-control gst_id" data-id="0" data-master="{{ $last_main_id }}" style="width:200px !important">
                                        <option value="">Select GST</option>
                                        @foreach($gst as $sub)
                                        <option value="{{$sub->gst}}">{{$sub->gst}}</option>
                                        @endforeach
                                    </select>
                                </td>
                                <td>
                                    <input type="number" class="form-control amount" id="amount_{{ $last_main_id }}_0" name="amount[{{ $last_main_id }}][]" data-id="0" data-master="{{ $last_main_id }}" readonly  style="width:250px !important">
                                </td>
                                <td>
                                    <input type="number" class="form-control total" id="total_{{ $last_main_id }}_0" name="total[{{ $last_main_id }}][]" data-id="0" data-master="{{ $last_main_id }}" readonly style="width:250px !important">
                                </td>
                                <td>
                                    <input type="date" class="form-control delivery_date" id="delivery_date_{{ $last_main_id }}_0" value="{{date('Y-m-d')}}" name="delivery_date[{{ $last_main_id }}][]" data-id="0" data-master="{{ $last_main_id }}" style="width:250px !important" >
                                </td>
                                <td>
                                    <img height="50px" class="image" id="image_{{ $last_main_id }}_0" name="image[{{ $last_main_id }}][]" data-id="0" data-master="{{ $last_main_id }}" width="attr">
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        {{-- END NEW CLONE --}}
    </form>
    <div class="kt-portlet__foot">
        <div class="kt-form__actions">
            <center>
                <button type="button" class="btn btn-brand submit" id="submit" >Submit</button>
                <a href="{{route('user.quotations.index')}}"><button type="button" class="btn btn-outline-secondary">Cancel</button></a>
            </center>
        </div>
    </div>
</div>
<script type="text/javascript">
    function getcompany($this){
        var productcategory_id = $($this).val();
        var id = $($this).data('id');
        var master = $($this).data('master');

        $('#company_id_'+master+'_'+id).html('<option value="">Select</option>');
        $('#product_id_'+master+'_'+id).html('<option value="">Select</option>');
        $('#warehouse_id_'+master+'_'+id).html('<option value="">Select</option>');
        $('#price_'+master+'_'+id).val('');
        $('#image_'+master+'_'+id).attr('src', '');

        if(productcategory_id != '')
        {  
            showLoader();

            $.ajax({
                type:"POST",
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                url:"{{ route('user.lead.getcompany') }}",
                data: {
                    'productcategory_id': productcategory_id
                },
                success: function(data){
                    hideLoader();
                    $('#company_id_'+master+'_'+id).html(data.data);
                }
            }); 
        }
    };

    function getwarehouse($this){
        var product_id = $($this).val();
        var id = $($this).data('id');
        var master = $($this).data('master');
        var company_id = $('#company_id_'+master+'_'+id).val();
        var productcategory_id = $('#productcategory_id_'+master+'_'+id).val();

        @if(Auth::guard('user')->check() && Auth::user()->role == 1)
        var branch_id = $('#branch_id_tx').val();
        @else
        var branch_id = '';
        @endif

        $('#warehouse_id_'+master+'_'+id).html('<option value="">Select</option>');
        $('#price_'+master+'_'+id).val('');
        $('#image_'+master+'_'+id).attr('src', '');

        if(product_id != '')
        {
            showLoader();

            $.ajax({
                type:"POST",
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                url:"{{ route('user.quotation.getwarehouse.data') }}",
                data: {
                    'product_id': product_id,
                    'productcategory_id': productcategory_id,
                    'company_id': company_id,
                    'branch_id': branch_id
                },
                success: function(data){
                    hideLoader();

                    $('#warehouse_id_'+master+'_'+id).html(data.data);
                    $('#price_'+master+'_'+id).val(data.data1);
                    $('#image_'+master+'_'+id).attr('src',data.data2);
                }
            });
        }
    };

    function getproduct($this){
        var company_id = $($this).val();
        var id = $($this).data('id');
        var master = $($this).data('master');

        $('#product_id_'+master+'_'+id).html('<option value="">Select</option>');
        $('#warehouse_id_'+master+'_'+id).html('<option value="">Select</option>');
        $('#price_'+master+'_'+id).val('');
        $('#image_'+master+'_'+id).attr('src', '');

        if(company_id != '')
        {
            showLoader();

            $.ajax({
                type:"POST",
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                url:"{{ route('user.lead.getproduct') }}",
                data: {
                    'company_id': company_id
                },
                success: function(data){
                    hideLoader();

                    $('#product_id_'+master+'_'+id).html(data.data);
                }
            });
        } 
    };

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
@php
if(isset($quotation))
{
    $update = route('user.quotations.update',$quotation->id);
}
else
{
    $update = route('user.quotations.update',$data->id);
}
@endphp
<script type="text/javascript">
    $(document).ready(function() {
        $("#submit").on("click", function(e) {
            e.preventDefault();
            if ($(".edit_formssss").valid()) {
                showLoader();

                $.ajax({
                    type: "POST",
                    url: "{{ $update }}",
                    data: new FormData($('.edit_formssss')[0]),
                    processData: false,
                    contentType: false,
                    success: function(data) {
                        
                        hideLoader();

                        if (data.status === 'success') {
                            window.location="{{ route('user.quotations.index') }}";
                            toastr["success"]("Quotation Added Successfully", "Success");
                        } else if (data.status === 'error') {
                            toastr["error"]("Something went wrong", "Error");
                        } else if (data.status === 'required') {
                            toastr["error"](data.message, "Error");
                        }
                    },
                });
            } else {
                hideLoader();
                e.preventDefault();
            }
        });
    });
</script>
<script type="text/javascript">
    $(document).ready(function(){

        var count_2d_clone = {{ $last_main_id }};
        $(document).on('click','.add_more_container',function() {
            count_2d_clone++;
            var $row = $(this).closest('.master_clone_div');
            var $clone = $row.clone();
            $clone.attr('data-id',count_2d_clone);
            $clone.find('.name').val('').attr('data-id',count_2d_clone).attr('id','name_'+count_2d_clone);
            $clone.find('.hidden_main').val('').attr('data-id',count_2d_clone).attr('id','hidden_main_'+count_2d_clone);
            
            // 2d clone child
            $clone.find('.master_clone').attr('id','master_clone_'+count_2d_clone).attr('data-id',0).attr('data-master',count_2d_clone);
            $clone.find('.qty').val('').attr('data-id',0).attr('data-master',count_2d_clone).attr('id','qty_'+count_2d_clone+'_'+0).attr('name','qty['+count_2d_clone+'][]');
            $clone.find('.price').val('').attr('data-id',0).attr('data-master',count_2d_clone).attr('id','price_'+count_2d_clone+'_'+0).attr('name','price['+count_2d_clone+'][]');
            $clone.find('.value_of_amount').attr('data-id',0).attr('data-master',count_2d_clone).attr('id','value_of_amount_'+count_2d_clone+'_'+0).attr('name','value_of_amount['+count_2d_clone+'][]');
            $clone.find('.discount').val('').attr('data-id',0).attr('data-master',count_2d_clone).attr('id','discount_'+count_2d_clone+'_'+0).attr('name','discount['+count_2d_clone+'][]');
            $clone.find('.gst_id').val('').attr('data-id',0).attr('data-master',count_2d_clone).attr('id','gst_id_'+count_2d_clone+'_'+0).attr('name','gst_id['+count_2d_clone+'][]');
            $clone.find('.amount').val('').attr('data-id',0).attr('data-master',count_2d_clone).attr('id','amount_'+count_2d_clone+'_'+0).attr('name','amount['+count_2d_clone+'][]');
            $clone.find('.total').val('').attr('data-id',0).attr('data-master',count_2d_clone).attr('id','total_'+count_2d_clone+'_'+0).attr('name','total['+count_2d_clone+'][]');
            var currentDate = new Date().toISOString().split('T')[0];
            $clone.find('.delivery_date').val(currentDate).attr('data-id',0).attr('data-master',count_2d_clone).attr('id','delivery_date_'+count_2d_clone+'_'+0).attr('name','delivery_date['+count_2d_clone+'][]');
            $clone.find('.image').attr('src', '').attr('data-id',0).attr('data-master',count_2d_clone).attr('id','image_'+count_2d_clone+'_'+0).attr('name','image['+count_2d_clone+'][]');

            $clone.find('.hidden_child').val('').attr('data-id',0).attr('data-master',count_2d_clone).attr('id','hidden_child_'+count_2d_clone+'_'+0).attr('name','hidden_child['+count_2d_clone+'][]');
            $clone.find('.master_clone').not(':first').remove();
            
            var $clone1 = $row.clone();
            $clone1.find('.employee_id, .symbol, .productcategory_id, .company_id, .product_id, .warehouse_id').select2();

            $clone.find('.employee_id').val('').css('width','100%').attr('id', 'employee_id_' + count_2d_clone).attr('data-id', count_2d_clone).removeClass('select2-hidden-accessible').next('.select2-container').remove();
            $clone.find('.symbol').val('').css('width','100%').attr('id', 'symbol_' + count_2d_clone).attr('data-id', count_2d_clone).removeClass('select2-hidden-accessible').next('.select2-container').remove();

            // 2d clone child
            $clone.find('.productcategory_id').val('').css('width','100%').attr('data-id',0).attr('data-master',count_2d_clone).attr('id','productcategory_id_'+count_2d_clone+'_'+0).attr('name','productcategory_id['+count_2d_clone+'][]').removeClass('select2-hidden-accessible').next('.select2-container').remove();
            $clone.find('.company_id').html('<option value="">Select</option>').css('width','100%').attr('data-id',0).attr('data-master',count_2d_clone).attr('id','company_id_'+count_2d_clone+'_'+0).attr('name','company_id['+count_2d_clone+'][]').removeClass('select2-hidden-accessible').next('.select2-container').remove();
            $clone.find('.product_id').html('<option value="">Select</option>').css('width','100%').attr('data-id',0).attr('data-master',count_2d_clone).attr('id','product_id_'+count_2d_clone+'_'+0).attr('name','product_id['+count_2d_clone+'][]').removeClass('select2-hidden-accessible').next('.select2-container').remove();
            $clone.find('.warehouse_id').html('<option value="">Select</option>').css('width','100%').attr('data-id',0).attr('data-master',count_2d_clone).attr('id','warehouse_id_'+count_2d_clone+'_'+0).attr('name','warehouse_id['+count_2d_clone+'][]').removeClass('select2-hidden-accessible').next('.select2-container').remove();

            $clone.find('.employee_id, .symbol, .productcategory_id, .company_id, .product_id, .warehouse_id').select2();
            $clone.find('.employee_id, .symbol, .productcategory_id, .company_id, .product_id, .warehouse_id').select2();
            $('.employee_id, .symbol, .productcategory_id, .company_id, .product_id, .warehouse_id').select2();

            $row.after($clone);

            $clone.find('.add_more').show();
        });
        $(document).on('click','.remove_container',function(){
            var num_of_master_clone_div = $('.master_clone_div_new').length;
            if (num_of_master_clone_div != 1) {
                var obj = $(this).closest('.master_clone_div_new');
                obj.remove();
            }
        });

        $(document).on('click','.add_more',function() {
            var $row = $(this).closest('.master_clone');
            var $clone = $row.clone();
            var dataMasterId = $row.data('master');
            var count_2d_clone_child = parseInt($row.data('id')) + 1;

            var $parentCloneDiv = $(this).closest('.master_clone_div');
            var $lastRow = $parentCloneDiv.find('.master_clone:last');

            $clone.attr('data-id',count_2d_clone_child);
            $clone.find('.qty').val('').attr('data-id',count_2d_clone_child).attr('data-master',dataMasterId).attr('id','qty_'+dataMasterId+'_'+count_2d_clone_child).attr('name','qty['+dataMasterId+'][]');
            $clone.find('.price').val('').attr('data-id',count_2d_clone_child).attr('data-master',dataMasterId).attr('id','price_'+dataMasterId+'_'+count_2d_clone_child).attr('name','price['+dataMasterId+'][]');
            $clone.find('.value_of_amount').attr('data-id',count_2d_clone_child).attr('data-master',dataMasterId).attr('id','value_of_amount_'+dataMasterId+'_'+count_2d_clone_child).attr('name','value_of_amount['+dataMasterId+'][]');
            $clone.find('.discount').val('').attr('data-id',count_2d_clone_child).attr('data-master',dataMasterId).attr('id','discount_'+dataMasterId+'_'+count_2d_clone_child).attr('name','discount['+dataMasterId+'][]');
            $clone.find('.gst_id').val('').attr('data-id',count_2d_clone_child).attr('data-master',dataMasterId).attr('id','gst_id_'+dataMasterId+'_'+count_2d_clone_child).attr('name','gst_id['+dataMasterId+'][]');
            $clone.find('.amount').val('').attr('data-id',count_2d_clone_child).attr('data-master',dataMasterId).attr('id','amount_'+dataMasterId+'_'+count_2d_clone_child).attr('name','amount['+dataMasterId+'][]');
            $clone.find('.total').val('').attr('data-id',count_2d_clone_child).attr('data-master',dataMasterId).attr('id','total_'+dataMasterId+'_'+count_2d_clone_child).attr('name','total['+dataMasterId+'][]');
            var currentDate = new Date().toISOString().split('T')[0];
            $clone.find('.delivery_date').val(currentDate).attr('data-id',count_2d_clone_child).attr('data-master',dataMasterId).attr('id','delivery_date_'+dataMasterId+'_'+count_2d_clone_child).attr('name','delivery_date['+dataMasterId+'][]');
            $clone.find('.image').attr('src', '').attr('data-id',count_2d_clone_child).attr('data-master',dataMasterId).attr('id','image_'+dataMasterId+'_'+count_2d_clone_child).attr('name','image['+dataMasterId+'][]');

            $clone.find('.hidden_child').val('').attr('data-id',count_2d_clone_child).attr('data-master',dataMasterId).attr('id','hidden_child_'+dataMasterId+'_'+count_2d_clone_child).attr('name','hidden_child['+dataMasterId+'][]');


            var $clone1 = $row.clone();
            $clone1.find('.productcategory_id, .company_id, .product_id, .warehouse_id').select2();

            $clone.find('.productcategory_id').val('').css('width','100%').attr('data-id',count_2d_clone_child).attr('data-master',dataMasterId).attr('id','productcategory_id_'+dataMasterId+'_'+count_2d_clone_child).attr('name','productcategory_id['+dataMasterId+'][]').removeClass('select2-hidden-accessible').next('.select2-container').remove();
            $clone.find('.company_id').html('<option value="">Select</option>').css('width','100%').attr('data-id',count_2d_clone_child).attr('data-master',dataMasterId).attr('id','company_id_'+dataMasterId+'_'+count_2d_clone_child).attr('name','company_id['+dataMasterId+'][]').removeClass('select2-hidden-accessible').next('.select2-container').remove();
            $clone.find('.product_id').html('<option value="">Select</option>').css('width','100%').attr('data-id',count_2d_clone_child).attr('data-master',dataMasterId).attr('id','product_id_'+dataMasterId+'_'+count_2d_clone_child).attr('name','product_id['+dataMasterId+'][]').removeClass('select2-hidden-accessible').next('.select2-container').remove();
            $clone.find('.warehouse_id').html('<option value="">Select</option>').css('width','100%').attr('data-id',count_2d_clone_child).attr('data-master',dataMasterId).attr('id','warehouse_id_'+dataMasterId+'_'+count_2d_clone_child).attr('name','warehouse_id['+dataMasterId+'][]').removeClass('select2-hidden-accessible').next('.select2-container').remove();

            $clone.find('.productcategory_id, .company_id, .product_id, .warehouse_id').select2();
            $clone.find('.productcategory_id, .company_id, .product_id, .warehouse_id').select2();
            $('.productcategory_id, .company_id, .product_id, .warehouse_id').select2();


            $parentCloneDiv.find('.master_clone .add_more').hide();

            $row.after($clone);

            $parentCloneDiv.find('.master_clone .remove_2d_clone').show();
            $parentCloneDiv.find('.master_clone').last().find('.remove_2d_clone').hide();
        });

        $(document).on('click','.remove_2d_clone',function(){
            var $parentCloneDiv = $(this).closest('.master_clone_div');
            var num_of_master_clone = $parentCloneDiv.find('.master_clone_new').length;

            if (num_of_master_clone != 1) {
                var obj = $(this).closest('.master_clone_new');
                obj.remove();
            }
        });
    });
</script>