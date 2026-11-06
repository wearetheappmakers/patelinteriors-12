<meta name="csrf-token" content="{{ csrf_token() }}" />
<div class="tab-pane active" id="editbasic" role="tabpanel">
    <form class="kt-form kt-form--label-right edit_form" id="edit_form" method="POST" >
        @method('PUT')
        @csrf
        <div class="row">
            @if(Auth::guard('user')->check())
            
            @if(Auth::user()->role == 1)  
            <div class="form-group col-lg-3">
                <label>Branch  </label>
                <input type="hidden" id="branch_id" value="{{ $data->branch_id }}">
                <input type="text" class="form-control" placeholder="Branch" value="{{ $data->branch->name }}" readonly>
            </div>
            @endif
            
            @if(Auth::user()->role == 1 || Auth::user()->role == 2)
            <div class="form-group col-lg-3">
                <label>Employees  </label>
                <select name="employee_id" id="employee_id" class="form-control select2 employee_id" required >
                    <option value="">Select</option>
                    @foreach($employees as $sub)
                    <option value="{{$sub->id}}" @if($sub->id == $data->employee_id) selected @endif>{{$sub->name}}</option>
                    @endforeach
                </select>
            </div>
            @endif
            
            @endif
            
            <div class="form-group col-lg-3">
                <label>Client </label>
                <input type="text" class="form-control" placeholder="Client" value="{{ $data->client->member }}" readonly>
            </div>
            <div class="form-group col-lg-3">
                <label>Categorys</label>
                @php 
                $selected_category = explode(',',$data->lead_name);
                @endphp
                <select class="form-control select2" id="lead_name" name="lead_name[]" multiple onchange="getcompanyfromcategory(this)">
                    <option value="">Select</option>
                    @foreach($productcategory as $emp)
                    <?php
                    $selected = '';
                    foreach($selected_category as $ui)
                    {
                        if($ui == $emp->id)
                        {
                            $selected = 'selected';
                        }
                    }
                    ?>
                    <option value="{{ $emp->id }}" {{$selected}}>{{ $emp->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="form-group col-lg-3">
                <label>Companys</label>
                @php 
                $selected_company = explode(',',$data->company_name);
                @endphp
                <select class="form-control select2" id="company_name" name="company_name[]" multiple>
                    <option value="">Select</option>
                    @foreach($company as $companys)
                    <?php
                    $selected = '';
                    foreach($selected_company as $uis)
                    {
                        if($uis == $companys->id)
                        {
                            $selected = 'selected';
                        }
                    }
                    ?>
                    <option value="{{ $companys->id }}" {{$selected}}>{{ $companys->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="form-group col-lg-3">
                <label>Project Type</label>
                <select name="project_type_id" id="project_type_id" class="form-control select2 project_type_id">
                    <option value="">Select</option>
                    @foreach($project_type as $pro)
                    <option value="{{$pro->id}}" @if($pro->id == $data->project_type_id) selected @endif>{{$pro->name}}</option>
                    @endforeach
                </select>
            </div>
            <div class="form-group col-lg-3">
                <label>Project Name</label>
                <select name="project_name_id" id="project_name_id" class="form-control select2 project_name_id">
                    <option value="">Select</option>
                    @foreach($project_name as $pn)
                    <option value="{{$pn->id}}" @if($pn->id == $data->project_name_id) selected @endif>{{$pn->name}}</option>
                    @endforeach
                </select>
            </div>
            <div class="form-group col-lg-3">
                <label>Ar/ID</label>
                <select name="arid_id" id="arid_id" class="form-control select2 arid_id">
                    <option value="">Select</option>
                    @foreach($arids as $pn)
                    <option value="{{$pn->id}}" @if($pn->id == $data->arid_id) selected @endif>{{$pn->name}}</option>
                    @endforeach
                </select>
            </div>
            <div class="form-group col-lg-3">
                <label>Lead Description</label>
                <textarea type="text" class="form-control" placeholder="Lead Description" name="description">@if(isset($data)) {{$data->description}} @else  @endif</textarea> 
            </div>
            @if(isset($data))
            <div class="form-group col-lg-3">
                <label>Source </label>
                <a style="background: white; height: 2px !important" target="_blank" href="{{route('user.leadsource.create')}}" title="Add" class="btn btn-sm btn-clean btn-icon btn-icon-md">
                    <i class="flaticon2-plus-1"></i>
                </a>
                <select name="source_id" id="source_id" class="form-control select2" required onchange="getsourcename(this)">
                    <option value="">Select</option>
                    @foreach($source as $sub)
                    <option value="{{$sub->id}}"  @if($sub->id == $data->source_id) selected @endif>{{$sub->name}}</option>
                    @endforeach
                </select>
            </div>
            <div class="form-group col-lg-3">
                <label>Source Name </label>
                <a style="background: white; height: 2px !important" target="_blank" href="{{route('user.sourcename.create')}}" title="Add" class="btn btn-sm btn-clean btn-icon btn-icon-md">
                    <i class="flaticon2-plus-1"></i>
                </a>
                <select name="source_name" id="source_name" class="form-control select2" required>
                    <option value="">Select</option>
                    @foreach($sourcename as $sub)
                    <option value="{{$sub->id}}" @if($sub->id == $data->source_name) selected @endif>{{$sub->name}}</option>
                    @endforeach
                </select>
            </div>
            @else
            <div class="form-group col-lg-3">
                <label>Source </label>
                <a style="background: white; height: 2px !important" target="_blank" href="{{route('user.leadsource.create')}}" title="Add" class="btn btn-sm btn-clean btn-icon btn-icon-md">
                    <i class="flaticon2-plus-1"></i>
                </a>
                <select name="source_id" class="form-control select2" required>
                    <option value="">Select</option>
                    @foreach($source as $sub)
                    <option value="{{$sub->id}}">{{$sub->name}}</option>
                    @endforeach
                </select>
            </div>
            @endif
            <div class="form-group col-lg-3">
                <label>Lead Date</label>
                
                <input type="date" class="form-control" placeholder="" max="{{date('Y-m-d')}}" @if(isset($data)) value="{{$data->date}}" @else value="" @endif name="date">
            </div>
            <div class="form-group col-lg-3">
                <label>Current Project Phase</label>
                <textarea type="text" class="form-control" placeholder="Current Project Phase"  name="current_project_phase"> @if(isset($data)) {{$data->current_project_phase}} @else  @endif</textarea> 
            </div>
            <div class="form-group col-lg-3">
                <label>Lead Value</label>
                <input type="number" class="form-control" placeholder="Lead Value" @if(isset($data)) value="{{$data->lead_value}}" @else value="" @endif  name="value">
            </div>
            <div class="form-group col-lg-3">
                <label>Status</label>
                <?php $disabled = ''; 
                // if(Auth::guard('employee')->check())
                // {
                //     $disabled = 'disabled';
                // }
                ?>
                <select name="status" id="status" class="form-control select2" onchange="getvalue(this)"  {{ $disabled }}>
                    <option value="1" @if($data->status == 1) selected @endif >Pending</option>
                    <option value="2" @if($data->status == 2) selected @endif >Discussion</option>
                    <option value="3" @if($data->status == 3) selected @endif >Rivision</option>
                    <option value="4" @if($data->status == 4) selected @endif >Win</option>
                    <option value="5" @if($data->status == 5) selected @endif >Lost</option>
                </select>

            </div>
            <div class="form-group col-lg-3">
                <label>Showroom Visit </label>

                <select name="showroom" id="showroom" class="form-control select2" >
                    <option value="1" @if($data->showroom == 1) selected @endif >Yes</option>
                    <option value="2" @if($data->showroom == 2) selected @endif >No</option>

                </select>

            </div>
            <div class="form-group col-lg-3" id="reason" @if($data->status == 5) style="display: block;" @else style="display:none;"@endif >
                <label>Reason</label>
                <input type="text" class="form-control" placeholder="Reason"    name="reason" >
            </div>
        </div>
        <div class="row">
            <div class="form-group col-sm-6" id="from_to_time">
                <table cellspacing="0" border="0" class="table table-bordered  from">
                    <thead>
                        <tr>
                            <th></th>
                            <th>Date</th>
                            <th>Design</th>
                            <th>Time</th>
                        </tr>
                    </thead>
                    <tbody>
                        @if(count($design) > 0)
                        @foreach($design as $key=>$value)
                        <tr class="design_fordelete">
                            <td>
                                <button  type="button" id="delete_design" data-value="{{$value->id}}" class="btn btn-sm btn-icon btn-icon-md btn-clean delete_design "><i class="fa fa-minus" style="font-size:0.8rem !important;"></i></button>
                            </td>
                            <td>
                                <input type="date" class="form-control old_design_date" value="{{$value->date}}" readonly>
                            </td>
                            <td>
                                @if(isset($value->design))
                                <a href="{{url('design/',$value->design)}}" target="_blank">
                                    <img src = "{{url('design/',$value->design)}}"  height="50" width="50">
                                </a>
                                @endif
                            </td>
                            <td>
                                <input type="text" class="form-control" value="{{date('h:i:sa', strtotime($value->created_at))}}" readonly>
                            </td>
                        </tr>
                        @endforeach
                        @endif
                        <tr class="design_clone">
                            <td>
                                <button type="button" id="add_design" class="btn btn-sm btn-icon btn-icon-md btn-clean add_design "><i class="fa fa-plus" style="font-size:0.8rem !important"></i></button>
                                <button  type="button" id="remove_design" class="btn btn-sm btn-icon btn-icon-md btn-clean "><i class="fa fa-minus" style="font-size:0.8rem !important;"></i></button>
                            </td>
                            <td>
                                <input type="date" name="design_date[]" id="design_date_0" class="form-control design_date" data-id = "0">
                            </td>
                            <td>
                                <input type="file" name="design[]" id="design_0" class="form-control design" data-id = "0">
                            </td>
                            <td></td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div class="form-group col-sm-6" id="from_to_time">
                <table cellspacing="0" border="0" class="table table-bordered  from">
                    <thead>
                        <tr>
                            <th></th>
                            <th>Date</th>
                            <th>Quotation</th>
                            <th>Time</th>
                        </tr>
                    </thead>
                    <tbody>
                        @if(count($quotationchild) > 0)
                        @foreach($quotationchild as $key=>$value)
                        <tr class="oldquotationclone">
                            <td>
                                <button  type="button" id="quotation_remove" data-value="{{$value->id}}" class="btn btn-sm btn-icon btn-icon-md btn-clean quotation_remove "><i class="fa fa-minus" style="font-size:0.8rem !important;"></i></button>
                            </td>
                            <td>
                                <input type="date" class="form-control old_quotation_date" value="{{$value->quotation_date}}" readonly>
                            </td>
                            <td>
                                @if(isset($value->quotation_image))
                                <a href="{{url('lead_basic_quotation_image/',$value->quotation_image)}}" target="_blank">
                                    <img src = "{{url('lead_basic_quotation_image/',$value->quotation_image)}}"  height="50" width="50">
                                </a>
                                @endif
                            </td>
                            <td>
                                <input type="text" class="form-control" value="{{date('h:i:sa', strtotime($value->created_at))}}" readonly>
                            </td>
                        </tr>
                        @endforeach
                        @endif
                        <tr class="quotationclone">
                            <td width="15%">
                                <button type="button" id="add_quotation" class="btn btn-sm btn-icon btn-icon-md btn-clean add_quotation "><i class="fa fa-plus" style="font-size:0.8rem !important"></i></button>
                                <button  type="button" id="remove_quotation" class="btn btn-sm btn-icon btn-icon-md btn-clean"><i class="fa fa-minus" style="font-size:0.8rem !important;"></i></button>
                            </td>
                            <td>
                                <input type="date" name="quotation_date[]" id="quotation_date_0" class="form-control quotation_date" data-id = "0">
                            </td>
                            <td>
                                <input type="file" name="quotation_image[]" id="quotation_image_0" class="form-control quotation_image" data-id = "0">
                            </td>
                            <td></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
        
        <?php 
        $last_main_id = 0; 
        $last_child_id = 0; 
        ?>

        @if(count($leadbasicmainchild) > 0)
        @foreach($leadbasicmainchild as $key => $val)
        <?php $last_main_id = $key + 1 ?>
        <div class="master_clone_div" data-id="{{ $val->id }}">
            <div class="row col-sm-12" style="padding: 15px;background: #dede;">
                <div class="col-sm-1">
                    <div style="display: inline-flex !important;">
                        <a onclick="return confirm(`Are You Sure?`)" href="{{ route('user.lead.deletemainchild',$val->id) }}" class="btn btn-sm btn-label-brand btn-bold expbtn btn-icon btn-icon-md"><i class="fa fa-minus" style=""></i></a>
                    </div>
                </div>
                <div class="col-sm-2">
                    <label>Name</label>
                    <input type="hidden" name="hidden_main[]" class="hidden_main" value="{{ $val->id }}" id="hidden_main_{{ $key }}" data-id="{{ $key }}">
                    <input type="text" name="name[]" value="{{ $val->name }}" class="form-control name" id="name_{{ $key }}" data-id="{{ $key }}">
                </div>
                <div class="form-group col-lg-2">
                    <label>Employee  </label>
                    <select name="main_employee_id[]" class="form-control employee_id select2" id="employee_id_{{ $key }}" data-id="{{ $key }}">
                        <option value="">Select Employee</option>
                        @foreach($employees as $sub)
                        <option value="{{ $sub->id }}" @if($val->employee_id == $sub->id) selected @endif>{{ $sub->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group col-lg-2">
                    <label>Symbol  </label>
                    <select name="symbol[]" class="form-control symbol select2" id="symbol_{{ $key }}" data-id="{{ $key }}">
                        <option value="">Select Symbol</option>
                        @foreach($symbol as $sub)
                        <option value="{{ $sub->id }}" @if($val->symbol == $sub->id) selected @endif>{{ $sub->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <br>
            
            
            <div class="row">
                <div class=" form-group col-sm-12">
                    <table cellspacing="0" border="0" class="table table-bordered" >
                        <thead>
                            <tr>
                                <th></th>
                                <th>Category</th>
                                <th>Company</th>
                                <th>Product</th>
                                <th>Product Name</th>
                                <th>Warehouse</th>
                                <th>Qty</th>
                            </tr>
                        </thead>
                        <tbody>
                            @if(count($val->leadbasicchild) > 0)
                            @foreach($val->leadbasicchild as $lbc_key => $lbc_val)
                            <?php $last_child_id = $lbc_key + 1 ?>
                            <tr class="master_clone" id="master_clone_{{ $key }}" data-id="{{ $lbc_key }}" data-master="{{ $key }}">
                                <td width="5%">
                                    <div style="display: inline-flex !important;">
                                        <a onclick="return confirm(`Are You Sure?`)" href="{{ route('user.lead.deletechild',$lbc_val->id) }}" class="btn btn-sm btn-icon btn-icon-md btn-clean" ><i class="fa fa-minus" style="font-size:0.8rem !important"></i></a>
                                    </div>
                                </td>
                                <td width="15%">
                                    <input type="hidden" class="hidden_child" name="hidden_child[{{ $key }}][]" value="{{ $lbc_val->id }}" id="hidden_child_{{ $key }}_{{ $lbc_key }}" data-id="{{ $lbc_key }}" data-master="{{ $key }}">

                                    <input type="hidden" name="productcategory_id[{{ $key }}][]" value="{{ $lbc_val->productcategory_id }}" id="productcategory_id_{{ $key }}_{{ $lbc_key }}" data-id="{{ $lbc_key }}" data-master="{{ $key }}">
                                    <input type="text" class="form-control" placeholder="Category" value="{{ $lbc_val->productcategory->name }}" readonly>
                                </td>
                                <td width="15%">
                                    <input type="hidden" name="company_id[{{ $key }}][]" value="{{ $lbc_val->company_id }}" id="company_id_{{ $key }}_{{ $lbc_key }}" data-id="{{ $lbc_key }}" data-master="{{ $key }}">
                                    <input type="text" class="form-control" placeholder="Company" value="{{ ($lbc_val->company) ? $lbc_val->company->name : '' }}" readonly>
                                </td>
                                <td width="15%">
                                    <input type="hidden" name="product_id[{{ $key }}][]" value="{{ $lbc_val->product_id }}" id="product_id_{{ $key }}_{{ $lbc_key }}" data-id="{{ $lbc_key }}" data-master="{{ $key }}">
                                    <input type="text" class="form-control" placeholder="Product" value="{{ ($lbc_val->product) ? $lbc_val->product->dropdown_name : '' }}" readonly>
                                </td>
                                <td width="15%">
                                    <input type="text" value="{{ ($lbc_val->product) ? $lbc_val->product->product : '' }}" class="form-control product_nm" id="product_nm_{{ $key }}_{{ $lbc_key }}" data-id="{{ $lbc_key }}" data-master="{{ $key }}" readonly>
                                </td>
                                <td width="20%">
                                    <select class="form-control select2 warehouse_id" id="warehouse_id_{{ $key }}_{{ $lbc_key }}" style="width:100% !important"  name="warehouse_id[{{ $key }}][]" data-id="{{ $lbc_key }}" data-master="{{ $key }}">
                                        <option value="">Select</option>
                                        @foreach($warehouse as $value)
                                        <option value="{{ $value->id }}" @if($lbc_val->warehouse_id == $value->id) selected @endif>{{ $value->name }}</option>
                                        @endforeach
                                    </select>
                                </td>
                                <td width="15%">
                                    <input type="number" name="qty[{{ $key }}][]" value="{{ $lbc_val->qty }}" class="form-control qty" id="qty_{{ $key }}_{{ $lbc_key }}" data-id="{{ $lbc_key }}" data-master="{{ $key }}">
                                </td>
                            </tr>
                            @endforeach
                            @endif
                            <tr class="master_clone master_clone_new" id="master_clone_{{ $key }}" data-id="{{ $last_child_id }}" data-master="{{ $key }}">
                                <td width="5%">
                                    <div style="display: inline-flex !important;">
                                        <button type="button" id="add_more" class="btn btn-sm btn-clean btn-icon btn-icon-md add_more"><i class="fa fa-plus" style="font-size:0.8rem !important"></i></button>
                                        &nbsp;&nbsp;&nbsp;
                                        <button type="button" id="remove_2d_clone" class="btn btn-sm btn-icon btn-icon-md btn-clean remove_2d_clone remove_2d_clone" ><i class="fa fa-minus" style="font-size:0.8rem !important"></i></button>
                                    </div>
                                </td>
                                <td width="15%">
                                    <input type="hidden" class="hidden_child" name="hidden_child[{{ $key }}][]" id="hidden_child_{{ $key }}_{{ $last_child_id }}" data-id="{{ $last_child_id }}" data-master="{{ $key }}">
                                    <select class="form-control productcategory_id select2" id="productcategory_id_{{ $key }}_{{ $last_child_id }}" name="productcategory_id[{{ $key }}][]" data-id="{{ $last_child_id }}" data-master="{{ $key }}" style="width:100% !important" >
                                        <option value="">Select</option>
                                        @foreach($productcategory as $value)
                                        <option value="{{$value->id}}">{{$value->name}}</option>
                                        @endforeach
                                    </select>
                                </td>
                                <td width="15%">
                                    <select class="form-control select2 company_id" id="company_id_{{ $key }}_{{ $last_child_id }}" name="company_id[{{ $key }}][]" style="width:100% !important"  data-id="{{ $last_child_id }}" data-master="{{ $key }}">
                                        <option value="">Select</option>
                                    </select>
                                </td>
                                <td width="15%">
                                    <select class="form-control select2 product_id" id="product_id_{{ $key }}_{{ $last_child_id }}" name="product_id[{{ $key }}][]" data-id="{{ $last_child_id }}" data-master="{{ $key }}" style="width:100% !important">
                                        <option value="">Select</option>
                                    </select>
                                </td>
                                <td width="15%">
                                    <input type="text" class="form-control product_nm" id="product_nm_{{ $key }}_{{ $last_child_id }}" data-id="{{ $last_child_id }}" data-master="{{ $key }}" readonly>
                                </td>
                                <td width="20%">
                                    <select class="form-control select2 warehouse_id" id="warehouse_id_{{ $key }}_{{ $last_child_id }}" style="width:100% !important"  name="warehouse_id[{{ $key }}][]" data-id="{{ $last_child_id }}" data-master="{{ $key }}"  >
                                        <option value="">Select</option>
                                    </select>
                                </td>
                                <td width="15%">
                                    <input type="number" name="qty[{{ $key }}][]" class="form-control qty" id="qty_{{ $key }}_{{ $last_child_id }}" data-id="{{ $last_child_id }}" data-master="{{ $key }}">
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
            
        </div>
        @endforeach
        @endif

        <div class="master_clone_div master_clone_div_new" data-id="{{ $last_main_id }}">
            <div class="row col-sm-12" style="padding: 15px;background: #dede;">
                <div class="col-sm-1">
                    <div style="display: inline-flex !important;">
                        <button type="button" id="add_more_container" class="btn btn-sm btn-label-brand btn-bold expbtn btn-icon btn-icon-md add_more_container"><i class="fa fa-plus" style=""></i></button>
                        &nbsp;&nbsp;&nbsp;
                        <button type="button" id="remove_container" data-id='{{ $last_main_id }}' class="btn btn-sm btn-label-brand btn-bold expbtn btn-icon btn-icon-md remove_container"><i class="fa fa-minus" style=""></i></button>
                    </div>
                </div>
                <div class="col-sm-2">
                    <label>Name</label>
                    <input type="hidden" name="hidden_main[]" class="hidden_main" id="hidden_main_{{ $last_main_id }}" data-id="{{ $last_main_id }}">
                    <input type="text" name="name[]" class="form-control name" id="name_{{ $last_main_id }}" data-id="{{ $last_main_id }}" >
                </div>
                <div class="form-group col-lg-2">
                    <label>Employee  </label>
                    <select name="main_employee_id[]" class="form-control employee_id select2" id="employee_id_{{ $last_main_id }}" data-id="{{ $last_main_id }}">
                        <option value="">Select Employee</option>
                        @foreach($employees as $sub)
                        <option value="{{$sub->id}}" >{{$sub->name}}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group col-lg-2">
                    <label>Symbol  </label>
                    <select name="symbol[]" class="form-control symbol select2" id="symbol_{{ $last_main_id }}" data-id="{{ $last_main_id }}">
                        <option value="">Select Symbol</option>
                        @foreach($symbol as $sub)
                        <option value="{{$sub->id}}" >{{$sub->name}}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <br>
            <div class="row">
                <div class=" form-group col-sm-12">
                    <table cellspacing="0" border="0" class="table table-bordered" >
                        <thead>
                            <tr>
                                <th></th>
                                <th>Category</th>
                                <th>Company</th>
                                <th>Product</th>
                                <th>Product Name</th>
                                <th>Warehouse</th>
                                <th>Qty</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr class="master_clone master_clone_new" id="master_clone_{{ $last_main_id }}" data-id="0" data-master="{{ $last_main_id }}">
                                <td width="5%">
                                    <div style="display: inline-flex !important;">
                                        <button type="button" id="add_more" class="btn btn-sm btn-clean btn-icon btn-icon-md add_more"><i class="fa fa-plus" style="font-size:0.8rem !important"></i></button>
                                        &nbsp;&nbsp;&nbsp;
                                        <button type="button" id="remove_2d_clone" data-id='0' class="btn btn-sm btn-icon btn-icon-md btn-clean remove_2d_clone remove_2d_clone" ><i class="fa fa-minus" style="font-size:0.8rem !important"></i></button>
                                    </div>
                                </td>
                                <td width="15%">
                                    <input type="hidden" class="hidden_child" name="hidden_child[{{ $last_main_id }}][]" id="hidden_child_{{ $last_main_id }}_0" data-id="0" data-master="{{ $last_main_id }}">
                                    <select class="form-control productcategory_id select2" id="productcategory_id_{{ $last_main_id }}_0" name="productcategory_id[{{ $last_main_id }}][]" data-id="0" data-master="{{ $last_main_id }}" style="width:100% !important" >
                                        <option value="">Select</option>
                                        @foreach($productcategory as $value)
                                        <option value="{{$value->id}}">{{$value->name}}</option>
                                        @endforeach
                                    </select>
                                </td>
                                <td width="15%">
                                    <select class="form-control select2 company_id" id="company_id_{{ $last_main_id }}_0" name="company_id[{{ $last_main_id }}][]" style="width:100% !important"  data-id="0" data-master="{{ $last_main_id }}">
                                        <option value="">Select</option>
                                    </select>
                                </td>
                                <td width="15%">
                                    <select class="form-control select2 product_id" id="product_id_{{ $last_main_id }}_0" name="product_id[{{ $last_main_id }}][]" data-id="0" data-master="{{ $last_main_id }}" style="width:100% !important">
                                        <option value="">Select</option>
                                    </select>
                                </td>
                                <td width="15%">
                                    <input type="text" class="form-control product_nm" id="product_nm_0_0" data-id="0" data-master="0" readonly>
                                </td>
                                <td width="20%">
                                    <select class="form-control select2 warehouse_id" id="warehouse_id_{{ $last_main_id }}_0" style="width:100% !important"  name="warehouse_id[{{ $last_main_id }}][]" data-id="0" data-master="{{ $last_main_id }}"  >
                                        <option value="">Select</option>
                                    </select>
                                </td>
                                <td width="15%">
                                    <input type="number" name="qty[{{ $last_main_id }}][]" class="form-control qty" id="qty_{{ $last_main_id }}_0" data-id="0" data-master="{{ $last_main_id }}">
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

    </form>
    <div class="kt-portlet__foot">
        <div class="kt-form__actions">
            <center>
                <button type="button" class="btn btn-brand submit" id="submit" >Submit</button>
                <a href="{{route('user.lead.index')}}"><button type="button" class="btn btn-outline-secondary">Cancel</button></a>
            </center>
        </div>
    </div>
</div>
@php
$update = route('user.lead.update',$data->id);
@endphp
<script type="text/javascript">
    function getsourcename($this)
    {
        var source_id = $($this).val();
        $('#source_name').html('<option value="">Select</option>');
        if(source_id)
        {
            showLoader();
            $.ajax({
                type:"POST",
                url:"{{ route('user.lead.getsourcename') }}",
                data: {
                    '_token': "{{ csrf_token() }}",
                    'source_id': source_id
                },
                success: function(data){
                    $('#source_name').html(data);
                    hideLoader();
                }
            });
        }
    } 
    function getcompanyfromcategory($this)
    {
        var lead_name = $($this).val();
        var company_name = $('#company_name').val();
        {{-- $('#company_name').html('<option value="">Select</option>'); --}}
        if(lead_name)
        {
            showLoader();
            $.ajax({
                type:"POST",
                url:"{{ route('user.lead.getcompanyfromcategory') }}",
                data: {
                    '_token': "{{ csrf_token() }}",
                    'lead_name': lead_name,
                    'company_name': company_name
                },
                success: function(data){
                    $('#company_name').html(data);
                    hideLoader();
                }
            });
        }
    } 
    $(document).ready(function() {
        $("#submit").on("click", function(e) {
            e.preventDefault();
            if ($(".edit_form").valid()) {

                showLoader();

                $.ajax({
                    type: "POST",
                    url: "{{ $update }}",
                    data: new FormData($('.edit_form')[0]),
                    processData: false,
                    contentType: false,
                    success: function(data) {

                        $('.loader').hide();
                        $('.loader-wrapper').hide();

                        if (data.status === 'success') {
                            location.reload();
                            toastr["success"]("Lead Updated Successfully", "Success");
                        } else if (data.status === 'error') {
                            location.reload();
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
    $(document).on('click','.delete_design',function(){
        var clone = $('.design_fordelete').length;
        var id = $(this).data('value');
        var url = "{{ route('user.delete.design',":rowid") }}";
        url = url.replace(':rowid',id);
        var obj = $(this);
        $.ajax({
            type: "GET",
            url: url,
            processData: false,
            contentType: false,
            success: function(data)
            {
                if (data.status === 'success')
                {
                    location.reload();
                } else if (data.status === 'error')
                {
                    location.reload();
                }
            }
        });
    });

    $(document).on('click','.add_design',function() { 
        var length = $('.design_clone').length;
        var $row = $(this).closest('.design_clone');
        var $clone = $row.clone();
        $clone.find('.design_date').val('');
        $clone.find('.design').val('');
        $row.after($clone);
    });

    $(document).on('click','#remove_design',function(){
        var clone = $('.design_clone').length;
        if (clone != 1) {
            var obj = $(this).closest('.design_clone');
            obj.remove();
        }
    });



    $(document).on('click','.quotation_remove',function(){
        var clone = $('.oldquotationclone').length;
        var id = $(this).data('value');
        var url = "{{ route('user.delete.quotation',":rowid") }}";
        url = url.replace(':rowid',id);
        var obj = $(this);
        $.ajax({
            type: "GET",
            url: url,
            processData: false,
            contentType: false,
            success: function(data)
            {
                if (data.status === 'success')
                {
                    location.reload();
                } else if (data.status === 'error')
                {
                    location.reload();
                }
            }
        });
    });

    var count = 0;
    $(document).on('click','.add_quotation',function() {
        count++;  
        var length = $('.quotationclone').length; 
        var $row = $(this).closest('.quotationclone');
        var $clone = $row.clone();
        $clone.find('.quotation_date').val('').attr('data-id',count).attr('id','quotation_date_'+count);
        $clone.find('.quotation_image').val('').attr('data-id',count).attr('id','quotation_image_'+count);
        $row.after($clone);
    });

    $(document).on('click','#remove_quotation',function(){
        var clone = $('.quotationclone').length;
        if (clone != 1) {
            var obj = $(this).closest('.quotationclone');
            obj.remove();
        }
    });

    
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
            $clone.find('.product_nm').val('').attr('data-id',0).attr('data-master',count_2d_clone).attr('id','product_nm_'+count_2d_clone+'_'+0).attr('name','product_nm['+count_2d_clone+'][]');
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
            $clone.find('.product_nm').val('').attr('data-id',count_2d_clone_child).attr('data-master',dataMasterId).attr('id','product_nm_'+dataMasterId+'_'+count_2d_clone_child).attr('name','product_nm['+dataMasterId+'][]');
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



        $(document).on('change','.productcategory_id ',function(){
            var productcategory_id = $(this).val();
            var id = $(this).data('id');
            var dataMaster = $(this).data('master');

            $('#company_id_'+dataMaster+'_'+id).html('<option value="">Select</option>');
            $('#product_id_'+dataMaster+'_'+id).html('<option value="">Select</option>');
            $('#warehouse_id_'+dataMaster+'_'+id).html('<option value="">Select</option>');

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
                        
                        $('#company_id_'+dataMaster+'_'+id).html(data.data);

                        hideLoader();
                    }
                });
            }
        });

        $(document).on('change','.company_id ',function(){
            var company_id = $(this).val();
            var id = $(this).data('id');
            var dataMaster = $(this).data('master');

            $('#product_id_'+dataMaster+'_'+id).html('<option value="">Select</option>');
            $('#warehouse_id_'+dataMaster+'_'+id).html('<option value="">Select</option>');

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

                        $('#product_id_'+dataMaster+'_'+id).html(data.data);

                        hideLoader();
                    }
                });
            }
        });

        $(document).on('change','.product_id ',function(){
            var product_id = $(this).val();
            var id = $(this).data('id');
            var dataMaster = $(this).data('master');

            @if(Auth::guard('user')->check() && Auth::user()->role == 1)
            var branch_id = $('#branch_id').val();
            @else
            var branch_id = '';
            @endif

            $('#warehouse_id_'+dataMaster+'_'+id).html('<option value="">Select</option>');
            $('#product_nm_'+dataMaster+'_'+id).val('');

            if(product_id != '')
            {
                showLoader();

                $.ajax({
                    type:"POST",
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    url:"{{ route('user.lead.getwarehouse.from.product') }}",
                    data: {
                        'product_id': product_id,
                        'branch_id': branch_id
                    },
                    success: function(data){
                        $('#warehouse_id_'+dataMaster+'_'+id).html(data.data);
                        $('#product_nm_'+dataMaster+'_'+id).val(data.data1);

                        hideLoader();
                    }
                });
            }
        });

    });

</script>