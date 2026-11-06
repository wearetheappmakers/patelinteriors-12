<meta name="csrf-token" content="{{ csrf_token() }}" />
<div class="tab-pane active" id="basic" role="tabpanel">
    <form class="kt-form kt-form--label-right add_form" id="add_form" >
        @csrf
        <div class="row">
            @if(Auth::guard('user')->check())
            @if(Auth::user()->role == 1)  
            <div class="form-group col-lg-3">
                <label>Branch  </label>
                <select name="branch_id" id="branch_id" class="form-control select2" onchange="getemployee(this)" required >
                    <option value="">Select Branch</option>
                    @foreach($branch as $sub)
                    <option value="{{$sub->id}}" >{{$sub->name}}</option>
                    @endforeach
                </select>
            </div>
            <div class="form-group col-lg-3">
                <label>Employee  </label>
                <select name="employee_id" class="form-control select2 employee_id" id="employee_id"  required>
                    <option value="">Select Employee</option>
                </select>
            </div>
            <div class="form-group col-lg-3">
                <label>Client </label>
                <a style="background: white; height: 2px !important" target="_blank" href="{{route('user.client.create')}}" title="Add" class="btn btn-sm btn-clean btn-icon btn-icon-md">
                    <i class="flaticon2-plus-1"></i>
                </a>
                <select name="client_id" id="client_id" class="form-control select2" required>
                    <option value="">Select Client Name</option>
                </select>
            </div>
            <div class="form-group col-lg-3">
                <label>Category</label>
                <select name="lead_name[]" id="lead_name" class="form-control select2" multiple onchange="getcompanyfromcategory(this)">
                </select>
            </div>
            <div class="form-group col-lg-3">
                <label>Company</label>
                <select name="company_name[]" id="company_name" class="form-control select2" multiple>
                    <option value="">Select</option>
                </select>
            </div>
            <div class="form-group col-lg-3">
                <label>Project Type</label>
                <select name="project_type_id" class="form-control select2 project_type_id" id="project_type_id">
                    <option value="">Select ProjectType</option>
                </select>
            </div>
            <div class="form-group col-lg-3">
                <label>Project Name</label>
                <select name="project_name_id" class="form-control select2 project_name_id" id="project_name_id">
                    <option value="">Select ProjectName</option>
                </select>
            </div>
            <div class="form-group col-lg-3">
                <label>Ar/ID</label>
                <select name="arid_id" class="form-control select2 arid_id" id="arid_id">
                    <option value="">Select Ar/ID</option>
                </select>
            </div>
            @endif
            @endif
            @if(Auth::guard('user')->check())
            @if(Auth::user()->role == 2)
            <div class="form-group col-lg-3">
                <label>Employee  </label>
                <select name="employee_id" class="form-control select2" id="employee_id"  required >
                    <option value="">Select Employee</option>
                    @foreach($employees as $sub)
                    <option value="{{$sub->id}}" >{{$sub->name}}</option>
                    @endforeach
                </select>
            </div>
            <div class="form-group col-lg-3">
                <label>Client </label>
                <a style="background: white; height: 2px !important" target="_blank" href="{{route('user.client.create')}}" title="Add" class="btn btn-sm btn-clean btn-icon btn-icon-md">
                    <i class="flaticon2-plus-1"></i>
                </a>
                <select name="client_id" id="client_id" class="form-control select2" required>
                    <option value="">Select Client Name</option>
                    @foreach($client as $sub)
                    <option value="{{$sub->id}}" >{{$sub->member}}</option>
                    @endforeach
                </select>
            </div>
            <div class="form-group col-lg-3">
                <label>Category</label>
                <select name="lead_name[]" id="lead_name" class="form-control select2" multiple onchange="getcompanyfromcategory(this)">
                    @foreach($productcategory as $sub)
                    <option value="{{$sub->id}}" >{{$sub->name}}</option>
                    @endforeach
                </select>
            </div>
            <div class="form-group col-lg-3">
                <label>Company</label>
                <select name="company_name[]" id="company_name" class="form-control select2" multiple>
                    <option value="">Select</option>
                    
                </select>
            </div>
            <div class="form-group col-lg-3">
                <label>Project Type</label>
                <select name="project_type_id" class="form-control select2" id="project_type_id">
                    <option value="">Select ProjectType</option>
                    @foreach($project_type as $type)
                    <option value="{{$type->id}}" >{{$type->name}}</option>
                    @endforeach
                </select>
            </div>
            <div class="form-group col-lg-3">
                <label>Project Name</label>
                <select name="project_name_id" class="form-control select2" id="project_name_id">
                    <option value="">Select ProjectName</option>
                    @foreach($project_name as $pn)
                    <option value="{{$pn->id}}" >{{$pn->name}}</option>
                    @endforeach
                </select>
            </div>
            <div class="form-group col-lg-3">
                <label>Ar/ID</label>
                <select name="arid_id" class="form-control select2" id="arid_id">
                    <option value="">Select Ar/ID</option>
                    @foreach($arids as $pn)
                    <option value="{{$pn->id}}" >{{$pn->name}}</option>
                    @endforeach
                </select>
            </div>
            @endif
            @endif
            @if(Auth::guard('employee')->check())
            <div class="form-group col-lg-3">
                <label>Client </label>
                <a style="background: white; height: 2px !important" target="_blank" href="{{route('user.client.create')}}" title="Add" class="btn btn-sm btn-clean btn-icon btn-icon-md">
                    <i class="flaticon2-plus-1"></i>
                </a>
                <select name="client_id" id="client_id" class="form-control select2" required>
                    <option value="">Select Client Name</option>
                    @foreach($client as $sub)
                    <option value="{{$sub->id}}" >{{$sub->member}}</option>
                    @endforeach
                </select>
            </div>
            <div class="form-group col-lg-3">
                <label>Category</label>
                <select name="lead_name[]" id="lead_name" class="form-control select2" multiple onchange="getcompanyfromcategory(this)">
                    @foreach($productcategory as $sub)
                    <option value="{{$sub->id}}" >{{$sub->name}}</option>
                    @endforeach
                </select>
            </div>
            <div class="form-group col-lg-3">
                <label>Company</label>
                <select name="company_name[]" id="company_name" class="form-control select2" multiple>
                    <option value="">Select</option>
                    
                </select>
            </div>
            <div class="form-group col-lg-3">
                <label>Project Type</label>
                <select name="project_type_id" id="project_type_id" class="form-control select2">
                    <option value="">Select</option>
                    @foreach($project_type as $type)
                    <option value="{{$type->id}}" >{{$type->name}}</option>
                    @endforeach
                </select>
            </div>
            <div class="form-group col-lg-3">
                <label>Project Name</label>
                <select name="project_name_id" id="project_name_id" class="form-control select2">
                    <option value="">Select</option>
                    @foreach($project_name as $pn)
                    <option value="{{$pn->id}}" >{{$pn->name}}</option>
                    @endforeach
                </select>
            </div>
            <div class="form-group col-lg-3">
                <label>Ar/ID</label>
                <select name="arid_id" class="form-control select2" id="arid_id">
                    <option value="">Select Ar/ID</option>
                    @foreach($arids as $pn)
                    <option value="{{$pn->id}}" >{{$pn->name}}</option>
                    @endforeach
                </select>
            </div>
            @endif
            <!-- @php $category = App\ProductCategory::where('status',1)->get();  @endphp -->
            <div class="form-group col-lg-3">
                <label>Lead Description</label>
                <textarea type="text" class="form-control" placeholder="Lead Description" name="description"></textarea> 
            </div>
            <div class="form-group col-lg-3">
                <label>Source </label>
                <a style="background: white; height: 2px !important" target="_blank" href="{{route('user.leadsource.create')}}" title="Add" class="btn btn-sm btn-clean btn-icon btn-icon-md">
                    <i class="flaticon2-plus-1"></i>
                </a>
                <select name="source_id" class="form-control select2" id="source_id" onchange="getsourcename(this)" required>
                    <option value="">Select Source</option>
                    @if((Auth::guard('user')->check() && Auth::user()->role == 2) || Auth::guard('employee')->check())
                    @foreach($source as $sub)
                    <option value="{{$sub->id}}">{{$sub->name}}</option>
                    @endforeach
                    @endif
                </select>
            </div>
            <div class="form-group col-lg-3">
                <label>Source Name </label>
                <a style="background: white; height: 2px !important" target="_blank" href="{{route('user.sourcename.create')}}" title="Add" class="btn btn-sm btn-clean btn-icon btn-icon-md">
                    <i class="flaticon2-plus-1"></i>
                </a>
                <select name="source_name" id="source_name" class="form-control select2" required>
                    <option value="">Select Source Name</option>
                    
                </select>
            </div>
            <div class="form-group col-lg-3">
                <label>Lead Date</label>
                <input type="date" class="form-control" placeholder="" max="{{date('Y-m-d')}}"  value="{{date('Y-m-d')}}" name="date">
            </div>
            <div class="form-group col-lg-3">
                <label>Current Project Phase</label>
                <textarea type="text" class="form-control" placeholder="Current Project Phase"  name="current_project_phase"></textarea> 
            </div>
            <div class="form-group col-lg-3">
                <label>Lead Value</label>
                <input type="number" class="form-control" placeholder="Lead Value"   name="value" >
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
                <select name="status" id="status" class="form-control select2" required onchange="getvalue(this)">
                    <option value="1" selected>Pending</option>
                    <option value="2">Discussion</option>
                    <option value="3">Rivision</option>
                    <option value="4">Win</option>
                    <option value="5">Lost</option>
                </select>
                {{-- @endif --}}
            </div>
            <div class="form-group col-lg-3">
                <label>Showroom Visit </label>

                <select name="showroom" id="showroom" class="form-control select2" >
                    <option value="1" >Yes</option>
                    <option value="2" selected>No</option>

                </select>

            </div>
            <div class="form-group col-lg-3" id="reason" style="display: none;" >
                <label>Reason</label>
                <input type="text" class="form-control" placeholder="Reason"    name="reason" >
            </div>
        </div>
        <div class="row">
            <div class="form-group col-lg-6">
                <table cellspacing="0" border="0" class="table table-bordered  from">
                    <thead>
                        <tr>
                            <th></th>
                            <th>Date</th>
                            <th>Design</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr class="cloneThis">
                            <td width="15%">
                                <button type="button" id="add_design" class="btn btn-sm btn-icon btn-icon-md btn-clean add_design "><i class="fa fa-plus" style="font-size:0.8rem !important"></i></button>
                                <button  type="button" id="remove_design" class="btn btn-sm btn-icon btn-icon-md btn-clean remove_design "><i class="fa fa-minus" style="font-size:0.8rem !important;"></i></button>
                            </td>
                            <td>
                                <input type="date" name="design_date[]" id="design_date_0" class="form-control design_date" data-id = "0">
                            </td>
                            <td>
                                <input type="file" name="design[]" id="design_0" class="form-control design" data-id = "0">
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div class="form-group col-lg-6">
                <table cellspacing="0" border="0" class="table table-bordered  from">
                    <thead>
                        <tr>
                            <th></th>
                            <th>Date</th>
                            <th>Quotation</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr class="quotationclone">
                            <td width="15%">
                                <button type="button" id="add_quotation" class="btn btn-sm btn-icon btn-icon-md btn-clean add_quotation "><i class="fa fa-plus" style="font-size:0.8rem !important"></i></button>
                                <button  type="button" id="remove_quotation" class="btn btn-sm btn-icon btn-icon-md btn-clean remove_quotation"><i class="fa fa-minus" style="font-size:0.8rem !important;"></i></button>
                            </td>
                            <td>
                                <input type="date" name="quotation_date[]" id="quotation_date_0" class="form-control quotation_date" data-id = "0">
                            </td>
                            <td>
                                <input type="file" name="quotation_image[]" id="quotation_image_0" class="form-control quotation_image" data-id = "0">
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
        <div class="master_clone_div" data-id="0">
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
                    <input type="text" name="name[]" class="form-control name" id="name_0" data-id="0" >
                </div>
                <div class="form-group col-lg-2">
                    <label>Employee  </label>
                    <select name="main_employee_id[]" class="form-control employee_id select2" id="employee_id_0" data-id="0">
                        <option value="">Select Employee</option>
                        @if((Auth::guard('user')->check() && Auth::user()->role == 2) || Auth::guard('employee')->check())
                        @foreach($employees as $sub)
                        <option value="{{$sub->id}}" >{{$sub->name}}</option>
                        @endforeach
                        @endif
                    </select>
                </div>
                <div class="form-group col-lg-2">
                    <label>Symbol  </label>
                    <select name="symbol[]" class="form-control symbol select2" id="symbol_0" data-id="0">
                        <option value="">Select Symbol</option>
                        @if(!(Auth::guard('user')->check() && Auth::user()->role == 1))
                        @foreach($symbol as $sub)
                        <option value="{{$sub->id}}" >{{$sub->name}}</option>
                        @endforeach
                        @endif
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
                            <tr class="master_clone" id="master_clone_0" data-id="0" data-master="0">
                                <td width="5%">
                                    <div style="display: inline-flex !important;">
                                        <button type="button" id="add_more" class="btn btn-sm btn-clean btn-icon btn-icon-md add_more"><i class="fa fa-plus" style="font-size:0.8rem !important"></i></button>
                                        &nbsp;&nbsp;&nbsp;
                                        <button type="button" id="remove" data-id='0' class="btn btn-sm btn-icon btn-icon-md btn-clean remove remove" ><i class="fa fa-minus" style="font-size:0.8rem !important"></i></button>
                                    </div>
                                </td>
                                <td width="15%">
                                    <select class="form-control productcategory_id select2" id="productcategory_id_0_0" name="productcategory_id[0][]" data-id="0" data-master="0" style="width:100% !important" >
                                        <option value="">Select</option>
                                        @if((Auth::guard('user')->check() && Auth::user()->role == 2) || Auth::guard('employee')->check())
                                        @foreach($productcategory as $value)
                                        <option value="{{$value->id}}">{{$value->name}}</option>
                                        @endforeach
                                        @endif
                                    </select>
                                </td>
                                <td width="15%">
                                    <select class="form-control select2 company_id" id="company_id_0_0" name="company_id[0][]" style="width:100% !important"  data-id="0" data-master="0">
                                        <option value="">Select</option>
                                    </select>
                                </td>
                                <td width="15%">
                                    <select class="form-control select2 product_id" id="product_id_0_0" name="product_id[0][]" data-id="0" data-master="0" style="width:100% !important">
                                        <option value="">Select</option>
                                    </select>
                                </td>
                                <td width="15%">
                                    <input type="text" class="form-control product_nm" id="product_nm_0_0" data-id="0" data-master="0" readonly>
                                </td>
                                <td width="20%">
                                    <select class="form-control select2 warehouse_id" id="warehouse_id_0_0" style="width:100% !important"  name="warehouse_id[0][]" data-id="0" data-master="0"  >
                                        <option value="">Select</option>
                                    </select>
                                </td>
                                <td width="15%">
                                    <input type="number" name="qty[0][]" class="form-control qty" id="qty_0_0" data-id="0" data-master="0">
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
                <button type="button" class="btn btn-brand submit_basic" id="submit_basic" >Submit</button>
                <a href="{{route('user.lead.index')}}"><button type="button" class="btn btn-outline-secondary">Cancel</button></a>
            </center>
        </div>
    </div>
</div>
<script type="text/javascript">
    $(document).ready(function() {
        $("#submit_basic").on("click", function(e) {
            console.log('basic');
            e.preventDefault();
            if ($(".add_form").valid()) {

                showLoader();

                $.ajax({
                    type: "POST",
                    url: "{{ route('user.lead.basic') }}",
                    data: new FormData($('.add_form')[0]),
                    processData: false,
                    contentType: false,
                    success: function(data) {

                        if (data.status === 'success') {
                            var id = data.id;
                            var url = "{{route('user.lead.edit',":id") }}";
                            url = url.replace(':id', id);
                            window.location = url;
                            toastr["success"]("Lead Added Successfully", "Success");
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
        var branch_id = $('#branch_id').val();
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
                    'company_name': company_name,
                    'branch_id': branch_id
                },
                success: function(data){
                    $('#company_name').html(data);
                    hideLoader();
                }
            });
        }
    } 
    function clearBranchData()
    {
        $('#client_id').html('<option value="">Select Client Name</option>');
        $('#lead_name').html('');
        $('#company_name').html('');
        $('#project_type_id').html('<option value="">Select ProjectType</option>');
        $('#project_name_id').html('<option value="">Select ProjectName</option>');
        $('#source_id').html('<option value="">Select Source</option>');
        $('#source_name').html('<option value="">Select Source Name</option>');
        
        $('.name').val('');
        $('.employee_id').html('<option value="">Select Employee</option>');
        $('.symbol').html('<option value="">Select Symbol</option>');
        $('.master_clone_div').not(':first').remove();

        $('.productcategory_id').html('<option value="">Select</option>');
        $('.company_id').html('<option value="">Select</option>');
        $('.product_id').html('<option value="">Select</option>');
        $('.product_nm').val('');
        $('.warehouse_id').html('<option value="">Select</option>');
        $('.qty').val('');
        $('.master_clone').not(':first').remove();
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
                url:"{{ route('user.lead.getemployee') }}",
                data: {
                    'branch_id': branch_id
                },
                success: function(data){
                    $('.employee_id').html(data.data);
                    $('#client_id').html(data.data1);
                    $('#source_id').html(data.data3);
                    {{-- $('#source_name').html(data.data2); --}}
                    $('.productcategory_id').html(data.data4);
                    $('#lead_name').html(data.data4);
                    {{-- $('#company_name').html(data.data5); --}}
                    $('.symbol').html(data.data6);
                    $('#project_type_id').html(data.data7);
                    $('#project_name_id').html(data.data8);
                    $('#arid_id').html(data.data9);

                    hideLoader();
                }
            });
        }
    };

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

    $(document).ready(function(){

        var count = 0;
        $(document).on('click','.add_design',function() {
            count++;  
            var length = $('.cloneThis').length;     
            var $row = $(this).closest('.cloneThis');
            var $clone = $row.clone();
            $clone.find('.design_date').val('').attr('data-id',count).attr('id','design_date_'+count);
            $clone.find('.design').val('').attr('data-id',count).attr('id','design_'+count);
            $row.after($clone);
        });
        $(document).on('click','#remove_design',function(){
            var clone = $('.cloneThis').length;
            if (clone != 1) {
                var obj = $(this).closest('.cloneThis');
                obj.remove();
            }
        });

        var quote_count = 0;
        $(document).on('click','.add_quotation',function() {
            quote_count++;  
            var length = $('.quotationclone').length; 
            var $row = $(this).closest('.quotationclone');
            var $clone = $row.clone();
            $clone.find('.quotation_date').val('').attr('data-id',quote_count).attr('id','quotation_date_'+quote_count);
            $clone.find('.quotation_image').val('').attr('data-id',quote_count).attr('id','quotation_image_'+quote_count);
            $row.after($clone);
        });
        $(document).on('click','#remove_quotation',function(){
            var clone = $('.quotationclone').length;
            if (clone != 1) {
                var obj = $(this).closest('.quotationclone');
                obj.remove();
            }
        });
        

        var count_2d_clone = 0;
        $(document).on('click','.add_more_container',function() {
            count_2d_clone++;
            var $row = $(this).closest('.master_clone_div');
            var $clone = $row.clone();
            $clone.attr('data-id',count_2d_clone);
            $clone.find('.name').val('').attr('data-id',count_2d_clone).attr('id','name_'+count_2d_clone);
            
            // 2d clone child
            $clone.find('.master_clone').attr('id','master_clone_'+count_2d_clone).attr('data-id',0).attr('data-master',count_2d_clone);
            $clone.find('.qty').val('').attr('data-id',0).attr('data-master',count_2d_clone).attr('id','qty_'+count_2d_clone+'_'+0).attr('name','qty['+count_2d_clone+'][]');
            $clone.find('.product_nm').val('').attr('data-id',0).attr('data-master',count_2d_clone).attr('id','product_nm_'+count_2d_clone+'_'+0).attr('name','product_nm['+count_2d_clone+'][]');
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
            var num_of_master_clone_div = $('.master_clone_div').length;
            if (num_of_master_clone_div != 1) {
                var obj = $(this).closest('.master_clone_div');
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

            $parentCloneDiv.find('.master_clone .remove').show();
            $parentCloneDiv.find('.master_clone').last().find('.remove').hide();
        });
        
        $(document).on('click','.remove',function(){
            var $parentCloneDiv = $(this).closest('.master_clone_div');
            var num_of_master_clone = $parentCloneDiv.find('.master_clone').length;

            if (num_of_master_clone != 1) {
                var obj = $(this).closest('.master_clone');
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
