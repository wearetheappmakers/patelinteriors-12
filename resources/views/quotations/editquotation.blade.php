<meta name="csrf-token" content="{{ csrf_token() }}" />

<div class="tab-pane active" id="editquotation" role="tabpanel">

    <form class="kt-form kt-form--label-right edit_form" id="edit_form" method="POST" >

        @method('PUT')

        @csrf



        <div class="row">



            <div class="form-group col-sm-3 col-lg-3">

                <label>Lead</label>

                <select class="form-control" id="lead_id" onchange="getdata(this)" disabled name="lead_id">

                    <option value="">Select</option>

                    @foreach($lead as $datas)

                    <option value="{{$datas->id}}" @if($datas->id == $data->lead_id) selected @endif>{{$datas->lead_name}}</option>

                    @endforeach

                </select>

            </div>

            @if(Auth::guard('user')->check())

            @if(Auth::user()->role == 1)  

            <div class="form-group col-lg-3">

                <label>Branch  </label>

                <input type="hidden" name="branch_id" value="{{$data->branch_id}}" id="branch_id_tx">

                <select name="branch_id" class="form-control select2" disabled  onchange="getemployee(this)" required  >

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

                <label>Employee  </label>

                <input type="hidden" name="employee_id" value="{{$data->employee_id}}" id="employee_id_tx">

                <select name="employee_id" id="employee_id" class="form-control select2" disabled required >

                    <option value="">Select Employee</option>

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

                <input type="hidden" name="client_id" id="client_id_tx" value="{{$data->client_id}}">

                <select name="client_id" class="form-control select2" id="client_id" disabled required>

                    <option value="">Select Client Name</option>

                    @foreach($client as $sub)

                    <option value="{{$sub->id}}" @if($sub->id == $data->client_id) selected @endif  >{{$sub->member}}</option>

                    @endforeach

                </select>

            </div>

            @endif

            @endif



            @if(Auth::guard('user')->check())

            @if(Auth::user()->role == 2)

            <div class="form-group col-lg-3">

                <label>Employee  </label>

                <input type="hidden" name="employee_id" value="{{$data->employee_id}}" id="employee_id_tx">

                <select name="employee_id" id="employee_id" class="form-control select2" disabled required >

                    <option value="">Select Employee</option>

                    @foreach($employee as $sub)

                    <option value="{{$sub->id}}" @if($sub->id == $data->employee_id) selected @endif>{{$sub->name}}</option>

                    @endforeach

                </select>

            </div>



            <div class="form-group col-lg-3">

                <label>Client </label>

                <input type="hidden" name="client_id" id="client_id_tx" value="{{$data->client_id}}">

                <select name="client_id" class="form-control select2" id="client_id" disabled required>

                    <option value="">Select Client Name</option>

                    @foreach($client as $sub)

                    <option value="{{$sub->id}}" @if($sub->id == $data->client_id) selected @endif  >{{$sub->member}}</option>

                    @endforeach

                </select>

            </div>

            @endif

            @endif



            @if(Auth::guard('employee')->check())

            <div class="form-group col-lg-3">

                <label>Client </label>

                <input type="hidden" name="client_id" id="client_id_tx" value="{{$data->client_id}}">

                <select name="client_id" class="form-control select2" id="client_id" disabled required>

                    <option value="">Select Client Name</option>

                    @foreach($client as $sub)

                    <option value="{{$sub->id}}" @if($sub->id == $data->client_id) selected @endif  >{{$sub->member}}</option>

                    @endforeach

                </select>

            </div>

            @endif





            <div class="form-group col-lg-3">

                <label>Quotation Date</label>

                <input type="date" class="form-control"  placeholder="Quotation Date" value="{{$data->quotation_date}}" name="quotation_date" required>

            </div>





            <div class="form-group col-lg-3">

                <label>Status</label>

                <select class="form-control" name="status" id="status" >

                    <option value="">Select Status</option>

                    <option value="1" @if($data->status == 1) selected @endif>Pending</option>

                    <option value="2" @if($data->status == 2) selected @endif>Discussion</option>

                    <option value="3" @if($data->status == 3) selected @endif>Rivision</option>

                    <option value="4" @if($data->status == 4) selected @endif>Win</option>        

                    <option value="5" @if($data->status == 5) selected @endif>Lost</option>				

                </select>

            </div>









        </div>



        <div class="row">

            <div class=" form-group col-sm-12" style="overflow: auto !important">

                <table cellspacing="0" border="0" class="table table-bordered">

                    <thead>

                        <tr>

                            <th></th>





                            <th>Product Category</th>

                            <th>Company</th>

                            <th>Product</th>

                            <th>Warehouse</th>

                            <th>Price</th>

                            <th>Qty</th>

                            <th></th>

                            <th>Discount</th>

                            <th>GST</th>

                            <th>Amount</th>

                            <th>Total</th>

                            <th>Apx. Delivery Date</th>

                            <th>Image</th>



                        </tr>

                    </thead>

                    <tbody>



                        @if($quotationchild)

                        @foreach($quotationchild as $key => $value)



                        <tr class="edit_master_clone" data-id="{{$value->id}}" data-master="0">

                            <td>

                                <div style="display: inline-flex !important;">



                                    &nbsp;&nbsp;&nbsp;

                                    <button type="button" id="edit_remove_{{$value->id}}" data-value='{{$value->id}}' class="btn btn-sm btn-icon btn-icon-md btn-clean edit_remove edit_remove" ><i class="fa fa-minus" style="font-size:0.8rem !important"></i></button>

                                </div>

                            </td>



                            <td>



                                <input type="hidden" name="edit_main_id[]" value="{{$value->id}}" class="">

                                <select class="form-control edit_productcategory_id" id="edit_productcategory_id_{{$value->id}}" name="edit_productcategory_id[]" data-id="{{$value->id}}" onchange="editgetproduct(this)" disabled >

                                    <option value="">Select</option>

                                    @foreach($productcategory as $pc)

                                    <option value="{{$pc->id}}" @if($pc->id == $value->productcategory_id) selected @endif>{{$pc->name}}</option>

                                    @endforeach

                                </select>



                            </td>

                            <td>

                                <select class="form-control edit_company_id" id="company_id_{{$value->id}}" name="edit_company_id[]" data-id="{{$value->id}}" disabled >

                                    <option value="">Select</option>

                                    @foreach($company as $pc)

                                    <option value="{{$pc->id}}" @if($pc->id == $value->company_id) selected @endif>{{$pc->name}}</option>

                                    @endforeach

                                </select>

                            </td>

                            <td>









                                <select class="form-control edit_product_id" id="edit_product_id_{{$value->id}}" name="edit_product_id[]" data-id="{{$value->id}}" disabled onchange="editgetprice(this)">

                                    <option value="">Select</option>

                                    @foreach($product as $sub)

                                    <option value="{{$sub->id}}" @if($sub->id == $value->product_id) selected @endif>{{$sub->product}}</option>

                                    @endforeach

                                </select>



                            </td>

                            <td>

                                <select class="form-control edit_warehouse_id" id="edit_warehouse_id_{{$value->id}}" name="edit_warehouse_id[]" data-id="{{$value->id}}" disabled>

                                    <option value="">Select</option>

                                    @foreach($warehouse as $sub)

                                    <option value="{{$sub->id}}" @if($sub->id == $value->warehouse_id) selected @endif>{{$sub->name}}</option>

                                    @endforeach

                                </select>

                            </td>

                            <td>

                                <input type="number" class="form-control edit_price" id="edit_price_{{$value->id}}" data-id="{{$value->id}}"  value="{{$value->price}}" name="edit_price[]" readonly   >

                            </td>





                            <td>

                                <input type="number" class="form-control edit_qty" id="edit_qty_{{$value->id}}" name="edit_qty[]" value="{{$value->qty}}" data-id="{{$value->id}}"  >

                            </td>

                            <td style="width:7%">

                                <select class="form-control edit_value_of_amount" name="edit_value_of_amount[]" id="edit_value_of_amount_{{$value->id}}" value="{{$value->value_of_amount}}" data-id="{{$value->id}}" >

                                    <option value="">Select </option>

                                    <option value="1" @if($value->value_of_amount == 1) selected @endif>Project</option>

                                    <option value="2" @if($value->value_of_amount == 2) selected @endif>Retail</option>







                                </select>

                            </td>



                            <td>

                                <input type="number" class="form-control edit_discount" id="edit_discount_{{$value->id}}" value="{{$value->discount}}" name="edit_discount[]" data-id="{{$value->discount}}"  >

                            </td>

                            <td>

                                <select name="edit_gst_id[]" id="edit_gst_id_{{$value->id}}" class="form-control select2" data-id="{{$value->id}}">

                                    <option value="">Select GST</option>

                                    @foreach($gst as $sub)

                                    <option value="{{$sub->id}}" @if($sub->id == $value->gst_id) selected @endif>{{$sub->gst}}</option>

                                    @endforeach

                                </select>

                            </td>

                            <td>

                                <input type="number" class="form-control edit_amount" id="edit_amount_{{$value->id}}" name="edit_amount[]" data-id="{{$value->id}}" readonly  >

                            </td>

                            <td>

                                <input type="number" class="form-control edit_total" id="edit_total_{{$value->id}}" name="edit_total[]" data-id="{{$value->id}}"  readonly >

                            </td>

                            <td>

                                <input type="date" class="form-control edit_delivery_date" id="edit_delivery_date_{{$value->id}}" name="edit_delivery_date[]" data-id="{{$value->id}}" value="{{$value->delivery_date}}"  >

                            </td>

                            <td>

                                <input type="file" class="form-control edit_image" id="edit_image_{{$value->id}}" name="edit_image[]" data-id="{{$value->id}}" value="{{$value->image}}"  >

                                @if($value->image)

                                <img src="{{ asset('/quotation/'.$value->image) }}" height="50px" width="50px">



                                @endif

                            </td>





                        </tr>

                        @endforeach

                        @endif

                        <tr class="master_clone" data-id="0" data-master="0">

                            <td>

                                <div style="display: inline-flex !important;">

                                    <button type="button" id="add_more" class="btn btn-sm btn-clean btn-icon btn-icon-md add_more"><i class="fa fa-plus" style="font-size:0.8rem !important"></i></button>

                                    &nbsp;&nbsp;&nbsp;

                                    <button type="button" id="remove" data-id='0' class="btn btn-sm btn-icon btn-icon-md btn-clean remove remove" ><i class="fa fa-minus" style="font-size:0.8rem !important"></i></button>

                                </div>

                            </td>

                            {{-- <td>



                                <input type="text" class="form-control area" id="area_0" name="area[]" data-id="0"  >



                            </td> --}}



                            <td>



                                <input type="hidden" name="main_id[]" class="main_id">

                                <select class="form-control productcategory_id" id="productcategory_id_0" name="productcategory_id[]" data-id="0" onchange="getproduct(this)" >

                                    <option value="">Select</option>

                                    @foreach($productcategory as $value)

                                    <option value="{{$value->id}}">{{$value->name}}</option>

                                    @endforeach

                                </select>



                            </td>

                            <td>

                                <select class="form-control company_id" id="company_id_0" name="company_id[]" data-id="0">

                                    <option value="">Select</option>

                                </select>

                            </td>

                            <td>



                                <select class="form-control product_id" id="product_id_0" name="product_id[]" data-id="0" onchange="getprice(this)">

                                    <option value="">Select</option>

                                    @foreach($product as $value)

                                    <option value="{{$value->id}}">{{$value->product}}</option>

                                    @endforeach

                                </select>



                            </td>

                            <td>

                                <select class="form-control warehouse_id" id="warehouse_id_0" name="warehouse_id[]" data-id="0">

                                    <option value="">Select</option>

                                </select>

                            </td>





                            <td>

                                <input type="number" class="form-control price" id="price_0" name="price[]" readonly   >

                            </td>



                            <td>

                                <input type="number" class="form-control qty" id="qty_0" name="qty[]" data-id="0"  >

                            </td>

                            <td>

                                <select class="form-control value_of_amount" name="value_of_amount[]" id="value_of_amount_0" data-id="0" >



                                    <option value="1">Project</option>

                                    <option value="2">Retail</option>





                                </select>

                            </td>



                            <td>

                                <input type="number" class="form-control discount" id="discount_0" name="discount[]" data-id="0"  >

                            </td>

                            <td>

                                <select name="gst_id[]" id="gst_id_0" class="form-control select2" data-id="0">

                                    <option value="">Select GST</option>

                                    @foreach($gst as $sub)

                                    <option value="{{$sub->id}}">{{$sub->gst}}</option>

                                    @endforeach

                                </select>

                            </td>



                            <td>

                                <input type="number" class="form-control amount" id="amount_0" name="amount[]" data-id="0" readonly  >

                            </td>

                            <td>

                                <input type="number" class="form-control total" id="total_0" name="total[]" data-id="0" readonly >

                            </td>

                            <td>

                                <input type="date" class="form-control delivery_date" id="delivery_date_0" name="delivery_date[]" data-id="0"  >

                            </td>

                            <td>

                                <input type="file" class="form-control image" id="image_0" name="image[]" data-id="0"  >

                            </td>





                        </tr>



                    </tbody>

                </table>

            </div>

        </div>

<!-- <div class="row">

<div class="col-lg-3">

@include('layouts.status_checkbox',array('data' => ""))

</div>



</div> -->





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

    $(document).ready(function() {



        var count = 0;

        $(document).on('click','.add_more',function() {

            count++;

            var $row = $(this).closest('.master_clone');



            var $clone = $row.clone();



            $clone.find('.area').val('').attr('id','area_'+count);

            $clone.find('.productcategory_id').val('').attr('data-id',count).attr('id','productcategory_id_'+count);

            $clone.find('.product_id').val('').attr('data-id',count).attr('id','product_id_'+count);

            $clone.find('.company_id').val('').attr('data-id',count).attr('id','company_id_'+count);

            $clone.find('.warehouse_id').val('').attr('data-id',count).attr('id','warehouse_id_'+count);

            $clone.find('.price').val('').attr('data-id',count).attr('id','price_'+count);

            $clone.find('.qty').val('').attr('data-id',count).attr('id','qty_'+count);

            $clone.find('.value_of_amount').attr('data-id',count).val('').attr('id','value_of_amount_'+count);

            $clone.find('.discount').val('').attr('data-id',count).attr('id','discount_'+count);

            $clone.find('.amount').val('').attr('data-id',count).attr('id','amount_'+count);

            $clone.find('.total').val('').attr('data-id',count).attr('id','total_'+count);

            $clone.find('.delivery_date').attr('data-id',count).val('').attr('id','delivery_date_'+count);

            $clone.find('.image').val('').attr('data-id',count).attr('id','image_'+count);



            $row.after($clone);

        });



        $(document).on('click','.remove',function(){



            var num_of_master_clone_div_payment = $('.master_clone').length;

            if (num_of_master_clone_div_payment != 1) {

                var obj = $(this).closest('.master_clone');

                obj.remove();

            }

        });

    });



    function getprice($this){



        var product_id = $($this).val();

        var id = $($this).data('id');

        console.log(id);

        $.ajax({

            type:"POST",

            headers: {

                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')

            },

            url:"{{ route('user.quotations.getprice') }}",

            data: {

                'product_id': product_id

            },



            success: function(data){

// console.log(id);

                $('#price_'+id).val(data.data);

            }

        });



    };



    function getproduct($this){



        var productcategory_id = $($this).val();

        var id = $($this).data('id');

        console.log(id);

        $.ajax({

            type:"POST",

            headers: {

                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')

            },

            url:"{{ route('user.quotations.getproduct') }}",

            data: {

                'productcategory_id': productcategory_id

            },



            success: function(data){

// console.log(id);

                $('#product_id_'+id).html(data);

            }

        });



    };



    function editgetprice($this){



        var edit_product_id = $($this).val();

        var id = $($this).data('id');

        console.log(id);

        $.ajax({

            type:"POST",

            headers: {

                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')

            },

            url:"{{ route('user.quotations.getprice') }}",

            data: {

                'product_id': edit_product_id

            },



            success: function(data){

// console.log(id);

                $('#edit_price_'+id).val(data.data);

            }

        });



    };



    function editgetproduct($this){



        var edit_productcategory_id = $($this).val();

        var id = $($this).data('id');

        console.log(id);

        $.ajax({

            type:"POST",

            headers: {

                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')

            },

            url:"{{ route('user.quotations.getproduct') }}",

            data: {

                'productcategory_id': edit_productcategory_id

            },



            success: function(data){

// console.log(id);

                $('#edit_product_id_'+id).html(data);

            }

        });



    };



    setInterval(getalltotals, 1000);

    function getalltotals()

    {

        var grandtotals = 0;



        $('.qty').each(function(){

            var id = $(this).data('id');

            var qty = $(this).val();

            var price = $('#price_'+id).val();

            var discount = $('#discount_'+id).val();



            if(qty == '' || qty == ''){qty = 0;}

            if(price == 0 || price == '') {price = 0;}

            var amount = qty*price;



            var discount_amount = amount * discount /100;

            var total_discount = amount - discount_amount;



            var price_discount = price * discount /100;

            var total_price = price - price_discount;

            $('#total_'+id).val(total_discount);

            $('#amount_'+id).val(total_price);

        });





    }



    setInterval(getalltotal, 1000);

    function getalltotal()

    {

        var grandtotals = 0;



        $('.edit_qty').each(function(){

            var id = $(this).data('id');

            var qty = $(this).val();

            var price = $('#edit_price_'+id).val();

            var discount = $('#edit_discount_'+id).val();



            if(qty == '' || qty == ''){qty = 0;}

            if(price == 0 || price == '') {price = 0;}

            var amount = qty*price;



            var discount_amount = amount * discount /100;

            var total_discount = amount - discount_amount;



            var price_discount = price * discount /100;

            var total_price = price - price_discount;

            $('#edit_total_'+id).val(total_discount);

            $('#edit_amount_'+id).val(total_price);

        });





    }



    $(".edit_remove").on("click", function(e)

    {

        var id = $(this).data('value');

        var url = "{{ route('user.quotations.deletechild',":rowid") }}";

        url = url.replace(':rowid',id);

        var num_of_master_clone_div_payment = $('.edit_master_clone').length;

        if (num_of_master_clone_div_payment != 1) 



            e.preventDefault();







        $.ajax({



            type: "GET",

            url: url,



            processData: false,

            contentType: false,



            success: function(data) {



                if (data.status === 'success') {



                    console.log('done');

                    location.reload();



                } else if (data.status === 'error') {

                    location.reload();

                }

            }

        });

    });

</script>



@php

$update = route('user.quotations.update',$data->id);

@endphp

<script type="text/javascript">

    $(document).ready(function() {

        $("#submit").on("click", function(e) {

            console.log('quotations');

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



                            window.location="{{ route('user.quotations.index') }}";

                            toastr["success"]("Quotation Added Successfully", "Success");

                        } else if (data.status === 'error') {



                            toastr["error"]("Something went wrong", "Error");

                        }

                    },

                });

            } else {

                e.preventDefault();

            }

        });

    });



    function getemployee($this){



        var branch_id = $($this).val();

        var id = $($this).data('id');

        $.ajax({

            type:"POST",

            headers: {

                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')

            },

            url:"{{ route('user.quotations.getemployee') }}",

            data: {

                'branch_id': branch_id

            },

            success: function(data){

                $('#employee_id').html(data.data);

                $('#client_id').html(data.data1);

            }

        });



    };



    function getdata($this){



        var lead_id = $($this).val();

        var id = $($this).data('id');

        $.ajax({

            type:"POST",

            headers: {

                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')

            },

            url:"{{ route('user.quotations.getdata') }}",

            data: {

                'lead_id': lead_id

            },

            success: function(data){

// console.log(data.data);

                $('#branch_id').val(data.data);

                $('#branch_id_tx').val(data.data);

                $('#employee_id_tx').val(data.data1);

                $('#employee_id').val(data.data1);

                $('#client_id').val(data.data2);

                $('#client_id_tx').val(data.data2);

                $('#edit_master_clone').html(data.data3);

                console.log(data.data3);





            }

        });



    };



    $('.select2').select2({



    });

</script>