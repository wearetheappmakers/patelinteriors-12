<meta name="csrf-token" content="{{ csrf_token() }}" />
<div class="tab-pane active" id="basic" role="tabpanel">
    <form class="kt-form kt-form--label-right add_form" id="add_form" >
        @csrf
        <div class="row">
            @if(Auth::guard('user')->check())
            @if(Auth::user()->role == 1)  
            <div class="form-group col-lg-3">
                <label>Branch  </label>
                <select name="branch_id" class="form-control select2" onchange="getemployee(this)" required >
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
                <select name="lead_name[]" id="lead_name" class="form-control select2" multiple>
                    <option value="">Select</option>
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
                <select name="lead_name[]" id="lead_name" class="form-control select2" multiple>
                    <option value="">Select</option>
                    @foreach($productcategory as $sub)
                    <option value="{{$sub->id}}" >{{$sub->name}}</option>
                    @endforeach
                </select>
            </div>
            <div class="form-group col-lg-3">
                <label>Company</label>
                <select name="company_name[]" id="company_name" class="form-control select2" multiple>
                    <option value="">Select</option>
                    @foreach($company as $sub)
                    <option value="{{$sub->id}}" >{{$sub->name}}</option>
                    @endforeach
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
                <select name="lead_name[]" id="lead_name" class="form-control select2" multiple>
                    <option value="">Select</option>
                    @foreach($productcategory as $sub)
                    <option value="{{$sub->id}}" >{{$sub->name}}</option>
                    @endforeach
                </select>
            </div>
            <div class="form-group col-lg-3">
                <label>Company</label>
                <select name="company_name[]" id="company_name" class="form-control select2" multiple>
                    <option value="">Select</option>
                    @foreach($company as $sub)
                    <option value="{{$sub->id}}" >{{$sub->name}}</option>
                    @endforeach
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
                <select name="source_id" class="form-control select2" id="source_id" required>
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
                    @if((Auth::guard('user')->check() && Auth::user()->role == 2) || Auth::guard('employee')->check())
                    @foreach($sourcename as $sub)
                    <option value="{{$sub->id}}">{{$sub->name}}</option>
                    @endforeach
                    @endif
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
                @if(Auth::guard('employee')->check())
                <select name="status" id="status" class="form-control select2" disabled>
                    <option value="1" selected>Pending</option>
                    <option value="2">Discussion</option>
                    <option value="3">Rivision</option>
                    <option value="4">Win</option>
                    <option value="5">Lost</option>
                </select>
                @else
                <select name="status" id="status" class="form-control select2" required onchange="getvalue(this)">
                    <option value="1" selected>Pending</option>
                    <option value="2">Discussion</option>
                    <option value="3">Rivision</option>
                    <option value="4">Win</option>
                    <option value="5">Lost</option>
                </select>
                @endif
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
                                <button type="button" id="addtime" class="btn btn-sm btn-icon btn-icon-md btn-clean addtime "><i class="fa fa-plus" style="font-size:0.8rem !important"></i></button>
                                <button  type="button" id="minus" class="btn btn-sm btn-icon btn-icon-md btn-clean remove "><i class="fa fa-minus" style="font-size:0.8rem !important;"></i></button>
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
                                <button type="button" id="addtime" class="btn btn-sm btn-icon btn-icon-md btn-clean addtime "><i class="fa fa-plus" style="font-size:0.8rem !important"></i></button>
                                <button  type="button" id="minus" class="btn btn-sm btn-icon btn-icon-md btn-clean remove "><i class="fa fa-minus" style="font-size:0.8rem !important;"></i></button>
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
                        <option value="">Select Employee</option>
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
                                        @if((Auth::guard('user')->check() && Auth::user()->role == 2) || Auth::guard('employee')->check())
                                        <option value="">Select</option>
                                        @foreach($productcategory as $value)
                                        <option value="{{$value->id}}">{{$value->name}}</option>
                                        @endforeach
                                        @endif
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
                        }  else if (data.status === 'duplicateclient') {
                            // location.reload();
                            toastr["error"]("Lead already exists for "+data.data+"!", "Error");
                        }else if (data.status === 'reason_error') {
                            // location.reload();
                            toastr["error"]("Reason Required", "Error");
                        } else if (data.status === 'required') {
                            toastr["error"](data.message, "Error");
                        }
                    },
                });
            } else {
                e.preventDefault();
            }
        });
    });
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

                console.log(data.data);
                $('#product_id_'+master+'_'+id).html(data.data);
            }
        });
        $($this).select2('destroy');
    };
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
                console.log(data.data);
                $('.employee_id').html(data.data);
                $('#client_id').html(data.data1);
                $('#source_id').html(data.data3);
                $('#source_name').html(data.data2);
                $('.productcategory_id').html(data.data4);
                $('#lead_name').html(data.data4);
                $('#company_name').html(data.data5);
                $('#symbol').html(data.data6);
                $('#project_type_id').html(data.data7);
                $('#project_name_id').html(data.data8);
            }
        });
    };
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
                console.log(master,id,data);
                $('#company_id_'+master+'_'+id).html(data.data);
            }
        });
        $($this).select2('destroy');
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
                $('#warehouse_id_'+master+'_'+id).html(data.data);
            }
        });
        $($this).select2('destroy');
    };
</script>
<script type="text/javascript">
    var count = 0;
    $(document).on('click','.addtime',function() {
        count++;  
        var length = $('.cloneThis').length;     
        var $row = $(this).closest('.cloneThis');
        var $clone = $row.clone();
        $clone.find('.design_date').val('').attr('data-id',count).attr('id','design_date_'+count);
        $clone.find('.design').val('').attr('data-id',count).attr('id','design_'+count);
        $row.after($clone);
    });
    $(document).on('click','#minus',function(){
        var clone = $('.cloneThis').length;
        if (clone != 1) {
            var obj = $(this).closest('.cloneThis');
            obj.remove();
        }
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
</script>
<script type="text/javascript">
    function getselected($this)
    {
        $($this).css('width','100%');
        $($this).select2();
    }
    function calldestroy($this)
    {
        $($this).select2('destroy');
    }
    $(document).ready(function(){
        $(document).on('click','.add_more_container',function() {
            var $row = $(this).closest('.master_clone_div');
            var $clone = $row.clone();
            $clone.find('.name').val('');
            $clone.find('.employee_id').html('');
            $clone.find('.symbol').html('');
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
    function updatedata()
    {
        $('.master_clone_div').each(function (i,value){
            $(this).find('.name').attr('data-id',i).attr('id','name_'+i);
            $(this).find('.employee_id').attr('data-id',i).attr('id','employee_id_'+i);
            $(this).find('.symbol').attr('data-id',i).attr('id','symbol_'+i);
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