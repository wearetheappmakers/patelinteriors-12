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
                <select name="branch_id" class="form-control " onchange="getemployee(this)" disabled >
                    <option value="">Select Branch</option>
                    @foreach($branch as $sub)
                    <option value="{{$sub->id}}" @if($sub->id == $data->branch_id) selected @endif>{{$sub->name}}</option>
                    @endforeach
                </select>
            </div>
            <?php
            $employee = \App\Employee::where('branch_id',$data->branch_id)->get();
            ?>                     
            <div class="form-group col-lg-3">
                <label>Employees  </label>
                <select name="employee_id" id="employee_id" class="form-control select2 employee_id" disabled >
                    <option value="">Select</option>
                    @foreach($employee as $sub)
                    <option value="{{$sub->id}}" @if($sub->id == $data->employee_id) selected @endif>{{$sub->name}}</option>
                    @endforeach
                </select>
            </div>
            <?php
            $client = \App\Client::where('branch_id',$data->branch_id)->get();
            ?>    
            <div class="form-group col-lg-3">
                <label>Client </label>

                <select name="client_id" class="form-control select2" id="client_id" disabled>
                    <option value="">Select</option>
                    @foreach($client as $sub)
                    <option value="{{$sub->id}}" @if($sub->id == $data->client_id) selected @endif  >{{$sub->member}}</option>
                    @endforeach
                </select>
            </div>
            <div class="form-group col-lg-3">
                <label>Categorys</label>
                @php 
                $updated_employee = explode(',',$data->lead_name);
                @endphp
                <select class="form-control select2" name="lead_name[]" multiple disabled>
                    <option value="">Select</option>
                    @foreach($productcategory as $emp)
                    <?php
                    $selected = '';
                    foreach($updated_employee as $ui)
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
                $updated_companys = explode(',',$data->company_name);
                @endphp
                <select class="form-control select2" name="company_name[]" multiple disabled>
                    <option value="">Select</option>
                    @foreach($company as $companys)
                    <?php
                    $selected = '';
                    foreach($updated_companys as $uis)
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
            <?php
            $project_type = App\ProjectType::where('branch_id',$data->branch_id)->get();
            ?>                     
            <div class="form-group col-lg-3">
                <label>Project Type</label>
                <select name="project_type_id" id="project_type_id" class="form-control select2 project_type_id" disabled>
                    <option value="">Select</option>
                    @foreach($project_type as $pro)
                    <option value="{{$pro->id}}" @if($pro->id == $data->project_type_id) selected @endif>{{$pro->name}}</option>
                    @endforeach
                </select>
            </div>
            <?php
            $project_name = App\ProjectName::where('branch_id',$data->branch_id)->get();
            ?>                     
            <div class="form-group col-lg-3">
                <label>Project Name</label>
                <select name="project_name_id" id="project_name_id" class="form-control select2 project_name_id" disabled>
                    <option value="">Select</option>
                    @foreach($project_name as $pn)
                    <option value="{{$pn->id}}" @if($pn->id == $data->project_name_id) selected @endif>{{$pn->name}}</option>
                    @endforeach
                </select>
            </div>
            <div class="form-group col-lg-3">
                <label>Ar/ID</label>
                <select name="arid_id" id="arid_id" class="form-control select2 arid_id" disabled>
                    <option value="">Select</option>
                    @foreach($arids as $pn)
                    <option value="{{$pn->id}}" @if($pn->id == $data->arid_id) selected @endif>{{$pn->name}}</option>
                    @endforeach
                </select>
            </div>
            @else
            <?php $productcategory = App\ProductCategory::where('branch_id',$data->branch_id)->get(); ?>
            <?php $company = App\Company::where('branch_id',$data->branch_id)->get(); ?>
            <div class="form-group col-lg-3">
                <label>Category</label>
                @php 
                $updated_employee = explode(',',$data->lead_name);
                @endphp
                <select class="form-control select2" name="lead_name[]" multiple disabled>
                    <option value="">Select</option>
                    @foreach($productcategory as $emp)
                    <?php
                    $selected = '';
                    foreach($updated_employee as $ui)
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
                <label>Company</label>
                @php 
                $updated_companys = explode(',',$data->company_name);
                @endphp
                <select class="form-control select2" name="company_name[]" multiple disabled>
                    <option value="">Select</option>
                    @foreach($company as $companys)
                    <?php
                    $selected = '';
                    foreach($updated_companys as $uis)
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
            @endif
            @endif
            @if(Auth::guard('user')->check())
            @if(Auth::user()->role == 2)
            <div class="form-group col-lg-3">
                <label>Employee  </label>
                <select name="employee_id" id="employee_id" class="form-control select2" disabled >
                    <option value="">Select</option>
                    @foreach($employees as $sub)
                    <option value="{{$sub->id}}" @if($sub->id == $data->employee_id) selected @endif>{{$sub->name}}</option>
                    @endforeach
                </select>
            </div>
            <div class="form-group col-lg-3">
                <label>Client </label>

                <select name="client_id" class="form-control select2" disabled>
                    <option value="">Select</option>
                    @foreach($client as $sub)
                    <option value="{{$sub->id}}" @if($sub->id == $data->client_id) selected @endif  >{{$sub->member}}</option>
                    @endforeach
                </select>
            </div>
            <div class="form-group col-lg-3">
                <label>Project Type</label>
                <select name="project_type_id" id="project_type_id" class="form-control select2 project_type_id" disabled>
                    <option value="">Select</option>
                    @foreach($project_type as $pro)
                    <option value="{{$pro->id}}" @if($pro->id == $data->project_type_id) selected @endif>{{$pro->name}}</option>
                    @endforeach
                </select>
            </div>                    
            <div class="form-group col-lg-3">
                <label>Project Name</label>
                <select name="project_name_id" id="project_name_id" class="form-control select2 project_name_id" disabled>
                    <option value="">Select</option>
                    @foreach($project_name as $pn)
                    <option value="{{$pn->id}}" @if($pn->id == $data->project_name_id) selected @endif>{{$pn->name}}</option>
                    @endforeach
                </select>
            </div>
            <div class="form-group col-lg-3">
                <label>Ar/ID</label>
                <select name="arid_id" id="arid_id" class="form-control select2 arid_id" disabled>
                    <option value="">Select</option>
                    @foreach($arids as $pn)
                    <option value="{{$pn->id}}" @if($pn->id == $data->arid_id) selected @endif>{{$pn->name}}</option>
                    @endforeach
                </select>
            </div>
            @endif
            @endif
            @if(Auth::guard('employee')->check())
            <div class="form-group col-lg-3">
                <label>Client </label>
                <select name="client_id" class="form-control select2" disabled>
                    <option value="">Select</option>
                    @foreach($client as $sub)
                    <option value="{{$sub->id}}" @if($sub->id == $data->client_id) selected @endif  >{{$sub->member}}</option>
                    @endforeach
                </select>
            </div>
            
           <!--  <div class="form-group col-lg-3">
                <label>Lead Title</label>
                <input type="text" class="form-control" placeholder="Lead Title" @if(isset($data)) value="{{$data->lead_name }}" @else value="" @endif name="lead_name">
            </div> -->
            <div class="form-group col-lg-3">
                <label>Category</label>
                @php 
                $updated_employee = explode(',',$data->lead_name);
                @endphp
                <select class="form-control select2" name="lead_name[]" multiple disabled>
                    <option value="">Select</option>
                    @foreach($productcategory as $emp)
                    <?php
                    $selected = '';
                    foreach($updated_employee as $ui)
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
                <label>Company</label>
                @php 
                $updated_companys = explode(',',$data->company_name);
                @endphp
                <select class="form-control select2" name="company_name[]" multiple disabled>
                    <option value="">Select</option>
                    @foreach($company as $companys)
                    <?php
                    $selected = '';
                    foreach($updated_companys as $uis)
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
                <select name="project_type_id" id="project_type_id" class="form-control select2 project_type_id" disabled>
                    <option value="">Select</option>
                    @foreach($project_type as $pro)
                    <option value="{{$pro->id}}" @if($pro->id == $data->project_type_id) selected @endif>{{$pro->name}}</option>
                    @endforeach
                </select>
            </div>                    
            <div class="form-group col-lg-3">
                <label>Project Name</label>
                <select name="project_name_id" id="project_name_id" class="form-control select2 project_name_id" disabled>
                    <option value="">Select</option>
                    @foreach($project_name as $pn)
                    <option value="{{$pn->id}}" @if($pn->id == $data->project_name_id) selected @endif>{{$pn->name}}</option>
                    @endforeach
                </select>
            </div>
            <div class="form-group col-lg-3">
                <label>Ar/ID</label>
                <select name="arid_id" id="arid_id" class="form-control select2 arid_id" disabled>
                    <option value="">Select</option>
                    @foreach($arids as $pn)
                    <option value="{{$pn->id}}" @if($pn->id == $data->arid_id) selected @endif>{{$pn->name}}</option>
                    @endforeach
                </select>
            </div>
            @endif
            <div class="form-group col-lg-3">
                <label>Lead Description</label>
                <textarea type="text" class="form-control" placeholder="Lead Description" readonly name="description">@if(isset($data)) {{$data->description}} @else  @endif</textarea> 
            </div>
            @if(isset($data))
            <div class="form-group col-lg-3">
                <label>Source </label>

                <select name="source_id" id="source_id" class="form-control select2" disabled>
                    <option value="">Select</option>
                    @foreach($source as $sub)
                    <option value="{{$sub->id}}"  @if($sub->id == $data->source_id) selected @endif>{{$sub->name}}</option>
                    @endforeach
                </select>
            </div>
            <?php
            $sourcename = \App\SourceName::where('branch_id',$data->branch_id)->get();
            ?>
            <div class="form-group col-lg-3">
                <label>Source Name </label>

                <select name="source_name" id="source_name" class="form-control select2" disabled>
                    <option value="">Select</option>
                    @foreach($sourcename as $sub)
                    <option value="{{$sub->id}}" @if($sub->id == $data->source_name) selected @endif>{{$sub->name}}</option>
                    @endforeach
                </select>
            </div>
            @else
            <div class="form-group col-lg-3">
                <label>Source </label>

                <select name="source_id" class="form-control select2" disabled>
                    <option value="">Select</option>
                    @foreach($source as $sub)
                    <option value="{{$sub->id}}">{{$sub->name}}</option>
                    @endforeach
                </select>
            </div>
            @endif
            <div class="form-group col-lg-3">
                <label>Lead Date</label>
                
                <input type="date" class="form-control" placeholder="" max="{{date('Y-m-d')}}" @if(isset($data)) value="{{$data->date}}" @else value="" @endif name="date" readonly>
            </div>
            <div class="form-group col-lg-3">
                <label>Current Project Phase</label>
                <textarea type="text" class="form-control" placeholder="Current Project Phase" readonly  name="current_project_phase"> @if(isset($data)) {{$data->current_project_phase}} @else  @endif</textarea> 
            </div>
            <div class="form-group col-lg-3">
                <label>Lead Value</label>
                <input type="number" class="form-control" placeholder="Lead Value" @if(isset($data)) value="{{$data->lead_value}}" @else value="" @endif readonly  name="value">
            </div>
            <div class="form-group col-lg-3">
                <label>Status</label>
                {{-- @if(Auth::guard('employee')->check())
                <select name="status" id="status" class="form-control select2" disabled>
                    <option value="1" selected>Pending</option>
                    <option value="2">Discussion</option>
                    <option value="3">Rivision</option>
                    <option value="4">Win</option>
                    <option value="5">Lost</option>
                </select>
                @else --}}
                <select name="status" id="status" class="form-control select2" onchange="getvalue(this)"  disabled>
                    <option value="1" @if($data->status == 1) selected @endif >Pending</option>
                    <option value="2" @if($data->status == 2) selected @endif >Discussion</option>
                    <option value="3" @if($data->status == 3) selected @endif >Rivision</option>
                    <option value="4" @if($data->status == 4) selected @endif >Win</option>
                    <option value="5" @if($data->status == 5) selected @endif >Lost</option>
                </select>
                {{-- @endif --}}
            </div>
            <div class="form-group col-lg-3">
                <label>Showroom Visit </label>

                <select name="showroom" id="showroom" class="form-control select2" disabled>
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

                            <th>Date</th>
                            <th>Design</th>
                            <th>Time</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($design as $key=>$value)
                        <tr class="fordelete">

                            <td>
                                <input type="hidden" name="hidden_id[]" value="{{$value->id}}" class="form-control ">
                                <input type="date" name="old_design_date[]" class="form-control old_design_date" value="{{$value->date}}" readonly>
                            </td>
                            <td>
                                {{-- <input type="file" id="design" name="design" class="form-control design" value="{{$value->design}}"> --}}
                                @if(isset($value->design))
                                <a href="{{url('design/',$value->design)}}" target="_blank">
                                    <img src = "{{url('design/',$value->design)}}"  height="50" width="50">
                                </a>
                                @endif
                            </td>
                            <td>
                                <input type="text" name="time" class="form-control" value="{{date('h:i:sa', strtotime($value->created_at))}}" readonly>
                            </td>
                        </tr>
                        @endforeach
                        <!--  -->
                    </tbody>
                </table>
            </div>
            <div class="form-group col-sm-6" id="from_to_time">
                <table cellspacing="0" border="0" class="table table-bordered  from">
                    <thead>
                        <tr>

                            <th>Date</th>
                            <th>Quotation</th>
                            <th>Time</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($quotationchild as $key=>$value)
                        <tr class="oldquotationclone">

                            <td>
                                <input type="hidden" name="hidden_id[]" value="{{$value->id}}" class="form-control ">
                                <input type="date" name="old_quotation_date[]" class="form-control old_quotation_date" value="{{$value->quotation_date}}" readonly>
                            </td>
                            <td>
                                {{-- <input type="file" id="design" name="design" class="form-control design" value="{{$value->design}}"> --}}
                                @if(isset($value->quotation_image))
                                <a href="{{url('lead_basic_quotation_image/',$value->quotation_image)}}" target="_blank">
                                    <img src = "{{url('lead_basic_quotation_image/',$value->quotation_image)}}"  height="50" width="50">
                                </a>
                                @endif
                            </td>
                            <td>
                                <input type="text" name="time" class="form-control" value="{{date('h:i:sa', strtotime($value->created_at))}}" readonly>
                            </td>
                        </tr>
                        @endforeach
                      <!--   <tr class="quotationclone">
                            <td width="15%">
                                <button type="button" id="addtime" class="btn btn-sm btn-icon btn-icon-md btn-clean addtime "><i class="fa fa-plus" style="font-size:0.8rem !important"></i></button>
                                <button  type="button" id="minus" class="btn btn-sm btn-icon btn-icon-md btn-clean remove "><i class="fa fa-minus" style="font-size:0.8rem !important;"></i></button>
                            </td>
                            <td>
                                <input type="date" name="quotation_date[]" id="quotation_date_0" class="form-control quotation_date" data-id = "0">
                            </td>
                            <td>
                                <input type="file" name="quotation_image[]" id="quotation_image_0" class="form-control quotation_image" data-id = "0">
                            </td>
                            <td></td>
                        </tr> -->
                    </tbody>
                </table>
            </div>
        </div>
        @if(isset($leadbasicmainchild))
        @foreach($leadbasicmainchild as $key => $value)
        <div class="edit_master_clone_div" data-id="0">
            <div class="row col-sm-12" style="padding: 15px;background: #dede;">
               <!--  <div class="col-sm-1">
                    <div style="display: inline-flex !important;">
                        <a href="{{ route('user.lead.deletemainchild',$value->id) }}" id="edit_remove_container" class="btn btn-sm btn-label-brand btn-bold expbtn btn-icon btn-icon-md edit_remove_container edit_remove_container"><i class="fa fa-minus" style=""></i></a>
                    </div>
                </div> -->
                <div class="col-sm-2">
                    <label>Name</label>
                    <input type="hidden" name="lead_basic_main_id[]" value="{{$value->id}}">
                    <input type="text" name="edit_name[]" class="form-control name" id="name" value="{{$value->name}}" data-id="0" readonly>
                </div>
                <div class="form-group col-lg-2">
                    <label>Employee  </label>
                    <select name="edit_main_employee_id[]" class="form-control employee_id" id="employee_id" disabled>
                        <option value="">Select</option>
                        @foreach($employees as $sub)
                        <option value="{{$sub->id}}" @if($value->employee_id == $sub->id) selected @endif>{{$sub->name}}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group col-lg-2">
                    <label>Symbol  </label>
                    <select name="edit_symbol[]" class="form-control symbol" id="symbol" disabled>
                        <option value="">Select Symbol</option>
                        @foreach($symbol as $sub)
                        <option value="{{$sub->id}}" @if($value->symbol == $sub->id) selected @endif >{{$sub->name}}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <br>
            <div class="row">
                <div class=" form-group col-sm-12">
                    <table cellspacing="0" border="0" class="table table-bordered">
                        <thead>
                            <tr>

                                <th>Category</th>
                                <th>Company</th>
                                <th>Product</th>
                                <th>Product Name</th>
                                <th>Warehouse</th>
                                <th>Qty</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php 
                            $leadbasicchild = App\LeadBasicChild::where('lead_basic_main_id',$value->id)->get();
                            @endphp
                            @if(isset($leadbasicchild))
                            @foreach($leadbasicchild as $keys => $values)
                            <tr class="edit_master_clone" data-id="{{$keys}}}" data-master="{{$key}}">
                             <!--    <td width="10%">
                                    <div style="display: inline-flex !important;">
                                        <a href="{{ route('user.lead.deletechild',$values->id) }}" id="edit_remove" data-id='{{$keys}}' class="btn btn-sm btn-icon btn-icon-md btn-clean edit_remove edit_remove" ><i class="fa fa-minus" style="font-size:0.8rem !important"></i></a>
                                    </div>
                                </td> -->
                                <td width="15%">
                                    <input type="hidden" name="lead_basic_child_id[]" value="{{$values->id}}">                                    
                                    <?php $procategory = App\ProductCategory::where('id',$values->productcategory_id)->value('name'); ?>
                                    <input type="text" name="edit_productcategory_id[]" class="form-control edit_productcategory_id" value="{{ $procategory }}" id="edit_productcategory_id_{{$value->id}}_{{$values->id}}" data-id="{{$values->id}}" data-master="{{$value->id}}" readonly>
                                </td>
                                <td width="15%">
                                    <?php $company = App\Company::where('id',$values->company_id)->value('name'); ?>
                                    <input type="text" name="edit_company_id[]" class="form-control edit_company_id" value="{{ $company }}" id="edit_company_id_{{$value->id}}_{{$values->id}}" data-id="{{$values->id}}" data-master="{{$value->id}}" readonly>
                                </td>
                                <td width="15%">
                                    <?php $product = App\Product::where('id',$values->product_id)->value('product'); 
                                    $productcode = App\Product::where('id',$values->product_id)->value('code');
                                    ?>
                                    <input type="text" name="edit_product_id[]" class="form-control edit_product_id" value="{{ $productcode ." - ". $product }}" id="edit_product_id_{{$value->id}}_{{$values->id}}" data-id="{{$values->id}}" data-master="{{$value->id}}" readonly>
                                </td>
                                <td width="15%">
                                    <input type="text" value="{{ ($values->product) ? $values->product->product : '' }}" class="form-control product_nm" id="product_nm_{{ $value->id }}_{{ $values->id }}" data-id="{{ $values->id }}" data-master="{{ $value->id }}" readonly>
                                </td>
                                <td width="20%">
                                    <?php
                                    $warehouse = \App\WarehouseInventory::where('product_id',$values->product_id)->with('warehouse')->get();
                                    $warehousenm = \App\Warehouse::where('branch_id',$data['branch_id'])->get();
                                    ?> 
                                    <select class="form-control edit_warehouse_id" id="edit_warehouse_id_{{$value->id}}_{{$values->id}}" disabled style="width:100% !important"  name="edit_warehouse_id[]" data-id="{{$values->id}}" data-master="{{$value->id}}"  >
                                       <option value="">Select</option>
                                       @foreach($warehousenm as $sub)
                                       <option value="{{$sub->id}}" @if($sub->id == $values->warehouse_id) selected @endif>{{$sub->name}}</option>
                                       @endforeach
                                   </select>
                               </td>
                               <td width="15%">
                                <input type="number" name="edit_qty[]" class="form-control edit_qty" value="{{$values->qty}}" id="edit_qty_{{$value->id}}_{{$values->id}}" data-id="{{$values->id}}" data-master="{{$value->id}}" readonly>
                            </td>
                        </tr>

                        @endforeach
                        @endif
                        <!--  -->
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    @endforeach
    @endif
   <!--  <div class="master_clone_div" data-id="0">
        <div class="row col-sm-12" style="padding: 15px;background: #dede;">
            <div class="col-sm-1">
                <div style="display: inline-flex !important;">
                    <button type="button" id="add_more_container" class="btn btn-sm btn-label-brand btn-bold expbtn btn-icon btn-icon-md add_more_container"><i class="fa fa-plus" style=""></i></button>
                    &nbsp;&nbsp;&nbsp;
                    <button type="button" id="remove_container" data-id='0' class="btn btn-sm btn-label-brand btn-bold expbtn btn-icon btn-icon-md remove_container remove_container" style="display:none;"><i class="fa fa-minus" style=""></i></button>
                </div>
            </div>
            <div class="col-sm-2">
                <label>Name</label>
                <input type="text" name="name[]" class="form-control name" id="name" data-id="0" >
            </div>
            <div class="form-group col-lg-2">
                <label>Employee  </label>
                <select name="main_employee_id[]" class="form-control employee_id" id="employee_id">
                    <option value="">Select</option>
                    @foreach($employees as $sub)
                    <option value="{{$sub->id}}" >{{$sub->name}}</option>
                    @endforeach
                </select>
            </div>
            <div class="form-group col-lg-2">
                <label>Symbol  </label>
                <select name="symbol[]" class="form-control symbol" id="symbol">
                    <option value="">Select Symbol</option>
                    @foreach($symbol as $sub)
                    <option value="{{$sub->id}}" >{{$sub->name}}</option>
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
                            <th>Category</th>
                            <th>Company</th>
                            <th>Product</th>
                            <th>Warehouse</th>
                            <th>Qty</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr class="master_clone" data-id="0" data-master="0">
                            <td width="10%">
                                <div style="display: inline-flex !important;">
                                    <button type="button" id="add_more" class="btn btn-sm btn-clean btn-icon btn-icon-md add_more"><i class="fa fa-plus" style="font-size:0.8rem !important"></i></button>
                                    &nbsp;&nbsp;&nbsp;
                                    <button type="button" id="remove" data-id='0' class="btn btn-sm btn-icon btn-icon-md btn-clean remove remove" ><i class="fa fa-minus" style="font-size:0.8rem !important"></i></button>
                                </div>
                            </td>
                            <td width="15%">
                                <input type="hidden" name="main_id[]" class="main_id">
                                <select class="form-control productcategory_id" id="productcategory_id_0_0" name="productcategory_id[]" data-id="0" data-master="0" style="width:100% !important" onchange="getcompany(this)"   >
                                    <option value="">Select</option>
                                    @foreach($productcategory as $value)
                                    <option value="{{$value->id}}">{{$value->name}}</option>
                                    @endforeach
                                </select>
                            </td>
                            <td width="15%">
                                <select class="form-control company_id" id="company_id_0_0" name="company_id[]" onchange="getproduct(this)" style="width:100% !important"  data-id="0" data-master="0"  >
                                    <option value="">Select</option>
                                </select>
                            </td>
                            <td width="25%">
                                <select class="form-control product_id" id="product_id_0_0" name="product_id[]" data-id="0" data-master="0" style="width:100% !important" onchange="getwarehouse(this)" >
                                    <option value="">Select</option>
                                </select>
                            </td>
                            <td width="20%">
                                <select class="form-control warehouse_id" id="warehouse_id_0_0"  onchange="calldestroy(this)" style="width:100% !important"  name="warehouse_id[]" data-id="0" data-master="0"  >
                                    <option value="">Select</option>
                                </select>
                            </td>
                            <td width="15%">
                                <input type="number" name="qty[]" class="form-control qty" id="qty_0_0" data-id="0" data-master="0">
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div> -->
</form>
<div class="kt-portlet__foot">
    <div class="kt-form__actions">
        <center>

            <a href="{{route('user.lead.index')}}"><button type="button" class="btn btn-outline-secondary">Cancel</button></a>
        </center>
    </div>
</div>
</div>
@php
$update = route('user.lead.update',$data->id);
@endphp
<script type="text/javascript">
    $(document).ready(function() {
        $("#submit").on("click", function(e) {
            console.log('basic');
            e.preventDefault();
            if ($(".edit_form").valid()) {
                $.ajax({
                    type: "POST",
                    url: "{{ $update }}",
                    data: new FormData($('.edit_form')[0]),
                    processData: false,
                    contentType: false,
                    success: function(data) {
                        if (data.status === 'success') {
                            location.reload();
                            toastr["success"]("Lead Added Successfully", "Success");
                        } else if (data.status === 'error') {
                            location.reload();
                            toastr["error"]("Something went wrong", "Error");
                        } else if (data.status === 'duplicateclient') {
                            location.reload();
                            toastr["error"]("Lead already exists for "+data.data+"!", "Error");
                        }else if (data.status === 'reason_error') {
                            location.reload();
                            toastr["error"]("Reason Required", "Error");
                        }
                    },
                });
            } else {
                e.preventDefault();
            }
        });
    });
</script>
<script type="text/javascript">
    function getproduct($this){
        var company_id = $($this).val();
        var id = $($this).data('id');
        var master = $($this).data('master');
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
                $('#product_id_'+master+'_'+id).html('');
                $('#product_id_'+master+'_'+id).html(data.data);
            }
        })
    };
    function getnewproduct($this){
        var new_company_id = $($this).val();
        var id = $($this).data('id');
        var master = $($this).data('master');
        $.ajax({
            type:"POST",
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            url:"{{ route('user.lead.getproduct') }}",
            data: {
                'new_company_id': new_company_id
            },
            success: function(data){
                $('#new_product_id_'+master+'_'+id).html('');
                $('#new_product_id_'+master+'_'+id).html(data.data1);
            }
        })
    };
    function editgetproduct($this){
        var edit_company_id = $($this).val();
        var id = $($this).data('id');
        var master = $($this).data('master');
        $.ajax({
            type:"POST",
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            url:"{{ route('user.lead.getproduct') }}",
            data: {
                'company_id': edit_company_id
            },
            success: function(data){
                $('#edit_product_id_'+master+'_'+id).html('');
                $('#edit_product_id_'+master+'_'+id).html(data);
            }
        })
    }
    function getemployee($this){
        var branch_id = $($this).val();
        var id = $($this).data('id');
        $.ajax({
            type:"POST",
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            url:"{{ route('user.lead.getemployee') }}",
            data: {
                'branch_id': branch_id
            },
            success: function(data){
                // $('#employee_id').html(data.data);
                $('.employee_id').html(data.data);
                $('#client_id').html(data.data1);
                $('#source_id').html(data.data3);
                $('#source_name').html(data.data2);
                $('.productcategory_id').html(data.data4);
                $('#company_name').html(data.data5);
                $('.symbol').html(data.data6);
            }
        });
    };
    $('.select2').select2({
    });
    function getcompany($this){
        var productcategory_id = $($this).val();

        var id = $($this).data('id');
        var master = $($this).data('master');
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
                $('#company_id_'+master+'_'+id).html('');
                $('#company_id_'+master+'_'+id).html(data.data);
                
            }
        });
    };
    function getnewcompany($this){
        var new_productcategory_id = $($this).val();

        var id = $($this).data('id');
        var master = $($this).data('master');
        $.ajax({
            type:"POST",
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            url:"{{ route('user.lead.getcompany') }}",
            data: {
                'new_productcategory_id': new_productcategory_id
                
            },
            success: function(data){
                $('#new_company_id_'+master+'_'+id).html('');
                $('#new_company_id_'+master+'_'+id).html(data.data1);
                
            }
        });
    };
    function editgetcompany($this){
        var edit_productcategory_id = $($this).val();
        var id = $($this).data('id');
        var master = $($this).data('master');
        $.ajax({
            type:"POST",
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            url:"{{ route('user.lead.getcompany') }}",
            data: {
                'productcategory_id': edit_productcategory_id
            },
            success: function(data){
                $('#edit_company_id_'+master+'_'+id).html('');
                $('#edit_company_id_'+master+'_'+id).html(data);
            }
        });
    };
    function getwarehouse($this){
        var product_id = $($this).val();
        var id = $($this).data('id');
        var master = $($this).data('master');
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
                $('#warehouse_id_'+master+'_'+id).html('');
                $('#warehouse_id_'+master+'_'+id).html(data.data);
            }
        });
    };
    function getnewwarehouse($this){
        var new_product_id = $($this).val();
        var id = $($this).data('id');
        var master = $($this).data('master');
        $.ajax({
            type:"POST",
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            url:"{{ route('user.lead.getwarehouse') }}",
            data: {
                'new_product_id': new_product_id
            },
            success: function(data){
                $('#new_warehouse_id_'+master+'_'+id).html('');
                $('#new_warehouse_id_'+master+'_'+id).html(data.data1);
            }
        });
    };
    function editgetwarehouse($this){
        var edit_product_id = $($this).val();
        var id = $($this).data('id');
        var master = $($this).data('master');
        $.ajax({
            type:"POST",
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            url:"{{ route('user.lead.getwarehouse') }}",
            data: {
                'product_id': edit_product_id
            },
            success: function(data){
                $('#edit_warehouse_id_'+master+'_'+id).html('');
                $('#edit_warehouse_id_'+master+'_'+id).html(data);
            }
        });
    };
</script>
<script type="text/javascript">
    $(document).on('click','.old_remove',function()
    {
        var clone = $('.fordelete').length;
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
    $(document).on('click','.addtime',function() { 
        var length = $('.cloneThis').length;
        var $row = $(this).closest('.cloneThis');
        var $clone = $row.clone();
        $clone.find('.design_date').val('');
        $clone.find('.design').val('');
        $row.after($clone);
    });
    $(document).on('click','.quotation_remove',function()
    {
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
    $(document).on('click','.addtime',function() {
        count++;  
        var length = $('.quotationclone').length; 
        var $row = $(this).closest('.quotationclone');
        var $clone = $row.clone();
        $clone.find('.quotation_date').val('').attr('data-id',count).attr('id','quotation_date_'+count);
        $clone.find('.quotation_image').val('').attr('data-id',count).attr('id','quotation_image_'+count);
        $row.after($clone);
    });
    $(document).on('click','#minus',function(){
        var clone = $('.quotationclone').length;
        if (clone != 1) {
            var obj = $(this).closest('.quotationclone');
            obj.remove();
        }
    });
    $(document).on('click','#minus',function(){
        var clone = $('.cloneThis').length;
        if (clone != 1) {
            var obj = $(this).closest('.cloneThis');
            obj.remove();
        }
    });
</script>
<script type="text/javascript">
    // function getselected($this)
    // {
    //     $($this).css('width','100%');
    //     $($this).select2();
    // }
    // function calldestroy($this)
    // {
    //     $($this).select2('destroy');
    // }
    $(document).ready(function(){
        $(document).on('click','.add_more_container',function() {
            var $row = $(this).closest('.master_clone_div');
            var $clone = $row.clone();
            $clone.find('.name').val('');
            $clone.find('.productcategory_id').val('');
            $clone.find('.company_id').val('').html('');
            $clone.find('.product_id').html('');
            $clone.find('.warehouse_id').html('');
            $clone.find('.qty').html('');
            $clone.find('#remove_container').css('display','block');
            $row.after($clone);
        });
        $(document).on('click','.remove_container',function(){
            var num_of_master_clone_div = $('.master_clone_div').length;
            if (num_of_master_clone_div != 1) {
                var obj = $(this).closest('.master_clone_div');
                obj.remove();
            }
        });
    });
    $(document).ready(function(){
        $(document).on('click','.add_more',function() {
            var $row = $(this).closest('.master_clone');
            var $clone = $row.clone();
            $clone.find('.productcategory_id').val('');
            $clone.find('.company_id').val('').html('');
            $clone.find('.product_id').html('');
            $clone.find('.warehouse_id').html('');
            $clone.find('.qty').val('');
            $clone.find('.remove').css('display','block');
            $row.after($clone);
        });
        $(document).on('click','.remove',function(){
            var num_of_master_clone = $('.master_clone').length;
            if (num_of_master_clone != 1) {
                var obj = $(this).closest('.master_clone');
                obj.remove();
            }
        });
        updatedata();
        setInterval(updatedata,1000);
    });
    $(document).ready(function(){
        $(document).on('click','.new_add_more',function() {
            var $row = $(this).closest('.edit_master_clone1');
            var $clone = $row.clone();
            $clone.find('.new_productcategory_id').val('');
            $clone.find('.new_company_id').val('').html('');
            $clone.find('.new_product_id').html('');
            $clone.find('.new_warehouse_id').html('');
            $clone.find('.new_qty').val('');
            $clone.find('.new_remove').css('display','block');
            $row.after($clone);
        });
        $(document).on('click','.new_remove',function(){
            var num_of_master_clone = $('.edit_master_clone1').length;
            if (num_of_master_clone != 1) {
                var obj = $(this).closest('.edit_master_clone1');
                obj.remove();
            }
        });
        updatedata();
        setInterval(updatedata,1000);
    });
    function updatedata()
    {
        $('.master_clone_div').each(function (i,value){
            $(this).find('.name').attr('data-id',i).attr('id','name_'+i);
            $(this).attr('data-id',i);
            $(this).find('.master_clone').each(function(index,val)
            {
                $(this).find('.main_id').val(i);
                $(this).attr('data-id',index).attr('data-master',i);
                $(this).find('.productcategory_id').attr('data-id',index).attr('data-master',i).attr('id','productcategory_id_'+i+'_'+index);
                $(this).find('.company_id').attr('data-id',index).attr('data-master',i).attr('id','company_id_'+i+'_'+index);
                $(this).find('.product_id').attr('data-id',index).attr('data-master',i).attr('id','product_id_'+i+'_'+index);
                $(this).find('.warehouse_id').attr('data-id',index).attr('data-master',i).attr('id','warehouse_id_'+i+'_'+index);
                $(this).find('.qty').attr('data-id',index).attr('data-master',i).attr('id','qty_'+i+'_'+index);
            });
        });
    }
    function getvalue($this)
    {
        var value = $($this).val();
        if(value == 5)
        {
            $('#reason').show();
        }else
        {
            $('#reason').hide();
        }
    }
</script>