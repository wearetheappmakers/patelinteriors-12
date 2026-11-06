<!DOCTYPE html>

<html>

<head>

    <style type="text/css">

        body{

/*font-size: 0px !important;*/

font-family: "verdana", sans-serif;

font-size: 12px !important;

}

td,th{

    font-family: "verdana", sans-serif;

    font-size: 12px !important;

    border-collapse: collapse;

    border: 1px solid #737373 !important;

    padding: 2px !important; 

}

.no-break {

    page-break-before: always !important;

}

ol li{

    text-align: justify;

    font-size:70% !important;

}



{

    box-sizing: border-box;

}



.row {

    margin-left:-5px;

    margin-right:-5px;

}



.column {

    float: left;

    width: 50%;

/*padding: 5px;*/

}







table {

    border-collapse: collapse;

    border-spacing: 0;

    width: 100%;

    border: 1px;

}



th, td {

    text-align: left;

    padding: 16px;

}

</style>

<title>Patel Interior</title>

<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css">

<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>

</head> 



<?php $data = App\Quotation::where('id',request()->id)->first(); ?>

<?php $clones = App\QuotationChild::where('quotation_id',$data->id)->first(); ?>
@if(isset($clones))
<?php $clones2 = App\QuotationMainChild::where('is_hide',1)->where('quot_id',$clones->id)->first(); ?>
@endif
@php $clones2 = ''; @endphp

<?php $delivery = App\Delivery::where('quotation_id',$data->id)->first(); ?>

<?php $branch = App\User::where('role',2)->where('id',$data->branch_id)->value('name'); ?>

<?php $cname = App\Client::where('id',$data->client_id)->value('member'); ?>

<?php $cnum = App\Client::where('id',$data->client_id)->value('mobile_no'); ?>

<?php $caddress = App\Client::where('id',$data->client_id)->value('address'); ?>

<?php $gst = App\GST::where('id',$data->gst_id)->value('gst'); ?>

<?php $lead = App\Lead::where('id',$data->lead_id)->first(); ?>



<div class="row">

    @php $numbers=1; $s_total = 0; @endphp    

    <?php $clones2 = App\QuotationMainChild::where('is_hide',1)->where('quot_id',$data->id)->get(); ?>



    @if(!empty($clones2))

    @foreach($clones2 as $key=>$quot2)



    <table @if($key != 0) class="no-break" @endif>

        <tr>

            <th colspan="5" rowspan="3">

                <img src="{{asset('assets/media/logos/patellogo.jpg')}}" width="200" height="50" >&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                @if(isset($quot2->symbol))
                @php $image = App\Symbol::where('id',$quot2->symbol)->value('image'); @endphp
                 <img src="{{asset('symbol/'.$image)}}" width="50" height="50" >
                @endif

            </th>

            <th colspan="4"><center>Date</center></th>

            <th colspan="4"><center>{{ date('d-m-Y',strtotime($data->quotation_date)) }}</center></th>

        </tr>

        <tr>

            <th colspan="4"><center>CLIENT NAME</center></th>

            <th colspan="4"><center>{{$cname}}</center></th>

        </tr>

        <tr>

            <th colspan="4"><center>CONTACT</center></th>

            <th colspan="4"><center>{{$cnum}}</center></th>

        </tr>

        <tr>

            <th colspan="5" rowspan="2">

                <center>Opp. Masoom School, Opp. Selenium Sky Apartment,<br>

                    Off.Kalawad road, Mota Mauva, Rajkot - 360005,<br>

                Contact: 7575066600</center>

            </th>

            <th colspan="4"><center>ADDRESS</center></th>

            <th colspan="4"><center>{{$caddress}}</center></th>

        </tr>

        <tr>

            <th colspan="4"><center>ARCHITECT NAME</center></th>
           @php $name = App\SourceName::where('id',$data->source_name)->value('name'); @endphp
          

            <th colspan="4"><center>{{$name}}</center></th>

        </tr>

        <tr>

            <th height="12px" style="text-align: center !important;" bgcolor="#92d14e;" colspan="13">{{$quot2->name}}</th>

        </tr>
    </table>
    <table width="100%">
        <tr >  
            <th><center>SR.NO</center></th>
            @if($data->image == 1)

            <th><center>IMAGE</center></th> 
            @endif 
             @if($data->code == 1)
            <th><center>CODE</center></th>  
            @endif
            @if($data->item_name == 1)
            <th><center>ITEM NAME</center></th>  
            @endif
            @if($data->description == 1)
            <th><center>DESCRIPTION</center></th>  
            @endif
            @if($data->qty == 1)
            <th><center>QTY</center></th>
            @endif  
            @if($data->mrp == 1)
            <th><center>MRP</center></th>
            @endif
            @if($data->discount == 1)
            <th><center>DISCOUNT(%)</center></th>  
            @endif
            @if($data->discount_price == 1)
            <th><center>DISCOUNT PRICE</center></th>  
            @endif
            @if($data->sp_price == 1)
            <th><center>SP. PRICE</center></th>  
            @endif
            @if($data->gst == 1)
            <th><center>GST(%)</center></th>  
            @endif
            @if($data->gst_amt == 1)
            <th><center>GST Amt.</center></th>  
            @endif
            @if($data->sp_price_total == 1)
            <th><center>SP. PRICE TOTAL</center></th>  
            @endif
        </tr>

        <?php 

        $clone = App\QuotationChild::where('quot_main_child_id',$quot2->id)->get(); 

        $final_total = 0; 

        $total_mrp = 0;

        ?>

        @if(!empty($clone))

        @foreach($clone as $keys=>$quot)

        <?php $image = App\Product::where('id',$quot->product_id)->value('image1'); ?>

        <?php $products = App\Product::where('id',$quot->product_id)->value('product'); ?>

        <?php $codes = App\Product::where('id',$quot->product_id)->value('code'); ?>

        <?php $des = App\Product::where('id',$quot->product_id)->value('description'); ?>

        <?php $emp = App\Employee::where('id',$data->employee_id)->first(); ?>

        <?php $gst = $quot->gst_id ?>

        <tr style="page-break-after: tr;">

            <td style="text-align: center !important;">{{$keys+1}}</td>
             @if($data->image == 1)

            <td style="text-align: center !important;">

                @if($image)

                <img src="{{ asset('/gallery/'.$image) }}" width="100" height="70" >

                @endif

            </td>
            @endif

            <?php $totgst = $quot->price - $quot->price * $quot->discount / 100 ?>

            <?php $disprice = $quot->price - $quot->price * $quot->discount / 100 ?>


             @if($data->code == 1)
            <td style="text-align: center !important;">{{$codes}}</td>
            @endif
             @if($data->item_name == 1)
            <td style="text-align: center !important;">{{$products}}</td>
            @endif
            @if($data->description == 1)
            <td style="text-align: center !important;">{{$des}}</td>
            @endif
            @if($data->qty == 1)
            <td style="text-align: center !important;">{{$quot->qty}}</td>
            @endif
            @if($data->mrp == 1)
            <td style="text-align: center !important;">{{$quot->price}}</td>
            @endif
            @if($data->discount == 1)
            <td style="text-align: center !important;">{{$quot->discount}}</td>
            @endif
            @if($data->discount_price == 1)
            <td style="text-align: center !important;">{{$quot->price * $quot->discount / 100}}</td>
            @endif
             @if($data->sp_price == 1)
            <td style="text-align: center !important;">@if(!($disprice == 0)){{$disprice}} @else {{$quot->price * $quot->qty}} @endif</td>
            @endif
             @if($data->gst == 1)
            <td style="text-align: center !important;">{{$gst}}</td>
            @endif
             @if($data->gst_amt == 1)
            <td style="text-align: center !important;">{{$totgst*$gst/100}}</td>
            @endif
            <?php 

            if($gst){

                $s_total = $s_total + ($quot->price - $quot->price * $quot->discount / 100 + $totgst*$gst/100)*$quot->qty;

                $last_amount = ($quot->price - $quot->price * $quot->discount / 100 + $totgst*$gst/100)*$quot->qty;

            }else{

                $s_total = $s_total + ($quot->price - $quot->price * $quot->discount / 100 + $totgst*$gst/100);

                $last_amount = ($quot->price - $quot->price * $quot->discount / 100 + $totgst*$gst/100);

            }



            ?>


             @if($data->sp_price_total == 1)
            <td style="text-align: center !important;">{{ $last_amount }}</td>
            @endif
        </tr>

       <?php
       $arr = [$data->code,$data->image,$data->item_name,$data->description,$data->qty];
       $count = 0;
        foreach ($arr as $key => $value) {
            if($value == 1)
            {
                $count = $count + 1;

            }
        }

         ?>
        @php 

        $final_total += $last_amount; 

        $total_mrp += $quot->price; 



        @endphp

        @endforeach

        @endif

        <tr>
            
            <th style="background-color:#92d14e;" colspan="{{$count + 1}}">Total</th>
           
             @if($data->mrp == 1)
            <th style="background-color:#92d14e;"><center>{{$total_mrp}}</center></th>
            @endif
             @if($data->discount == 1)
            <th style="background-color:#92d14e;"></th>
            @endif
            @if($data->discount_price == 1)
            <th style="background-color:#92d14e;"></th>
            @endif
             @if($data->sp_price == 1)
            <th style="background-color:#92d14e;"></th>
            @endif
            @if($data->gst == 1)
            <th style="background-color:#92d14e;"></th>
            @endif
             @if($data->gst_amt == 1)
            <th style="background-color:#92d14e;"></th>
            @endif
             @if($data->sp_price_total == 1)
            <th style="background-color:#92d14e;"><center>{{$final_total}}</center></th>
            @endif
        </tr>
    </table>
    <table>

        <tr>

            <th colspan="13">Terms & Conditions</th>

        </tr>

        <tr>

            <th colspan="13">*100 % Advance Payment Along With Purchase Order.</th>

        </tr>

        <tr>

            <th colspan="13">*Above Rates Are GST Paid EX.Our Godown.</th>

        </tr>

        <tr>

            <th colspan="8">* For Stone Products GST Extra As Applicable.</th>

            <th colspan="5">Created By : {{Auth::user()->name}}</th>

        </tr>

        <tr>

            <th colspan="8">* Freight & Unloading Charges Are Extra As Applicable</th>

            <th colspan="5">

                @if(Auth::guard('employee')->check())

                Contact No : {{Auth::user()->mobile_no}}

                @else

                Contact No : {{Auth::user()->number}}

                @endif

            </th>

        </tr>

    </table>

    @endforeach

    @endif



</div>

</html>