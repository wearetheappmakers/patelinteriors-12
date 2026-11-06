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
                        View Revised Quotation
                    </h3>
                </div>
                <div class="kt-portlet__head-toolbar">
                    <div class="kt-portlet__head-wrapper">
                        <div class="kt-portlet__head-actions">
                            <a href="{{ route('user.revisedquotation.index', $revised->quotation_id)}}" class="btn btn-brand btn-elevate btn-icon-sm">
                                <i class="flaticon2-fast-back"></i>
                                Back
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            <div class="kt-portlet__body">
                <div id="kt_table_1_wrapper" class="dataTables_wrapper dt-bootstrap4">
                    <div class="row">
                        @if(isset($revised))
                        <input type="hidden" name="form_type" value="edit">
                        @endif
                        @if(Auth::guard('user')->check())
                        @if(Auth::user()->role == 1)  
                        <div class="form-group col-lg-3">
                            <label>Branch  </label>
                            <input type="text" class="form-control" placeholder="Branch" value="{{ $revised->branch ? $revised->branch->name : '' }}" readonly>
                        </div>
                        @endif
                        <div class="form-group col-lg-3">
                            <label>Employee  </label>
                            <input type="text" class="form-control" placeholder="Employee" value="{{ $revised->employee ? $revised->employee->name : '' }}" readonly>
                        </div>
                        @endif
                        <div class="form-group col-lg-3">
                            <label>Client </label>
                            <input type="text" class="form-control" placeholder="Client" value="{{ $revised->client ? $revised->client->member : '' }}" readonly>
                        </div>
                        <div class="form-group col-lg-3">
                            <label>Quotation Date</label>
                            <input type="date" class="form-control"  placeholder="Quotation Date" @if($revised) value="{{ $revised->quotation_date }}" @endif  name="revised_date" readonly>
                        </div>
                        <div class="form-group col-lg-3">
                            <label>Status</label>
                            <select class="form-control select2" name="status" id="status" disabled>
                                <option value="1" @if($revised->status == 1) selected @endif>Pending</option>
                                <option value="2" @if($revised->status == 2) selected @endif>Cancelled</option>
                                <option value="3" @if($revised->status == 3) selected @endif>Completed</option>
                                <option value="4" @if($revised->status == 4) selected @endif>On Hold</option>        
                            </select>
                        </div>
                        <div class="form-group col-lg-3">
                            <label>Reason</label>
                            <input type="text" class="form-control"  placeholder="Revised"  name="revised" readonly @if($revised) value="{{ $revised->revised }}"  @endif>
                        </div>
                    </div>

                    @foreach($revisedchild as $key=>$value)
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
                                        $quotationchild = App\RevisedChild::where('revised_main_child_id',$value->id)->get();
                                        @endphp
                                        @foreach($quotationchild as $lbc_key => $lbc_val)
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
                
                </div>
            </div>
        </div>
    </div>
</div>
@stop
@push('scripts')