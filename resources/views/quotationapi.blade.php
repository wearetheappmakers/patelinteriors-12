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
<?php $clones2 = App\QuotationMainChild::where('is_hide',1)->where('quot_id',$clones->id)->first(); ?>
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
                <img src="{{asset('assets/media/logos/patellogo.jpg')}}" width="200" height="50" >
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
            <th colspan="4"><center></center></th>
        </tr>
        <tr>
            <th height="12px" style="text-align: center !important;" bgcolor="#92d14e;" colspan="13">{{$quot2->name}}</th>
        </tr>
        <tr >  
            <th><center>SR.NO</center></th>
            <th><center>IMAGE</center></th>  
            <th><center>CODE</center></th>  
            <th><center>ITEM NAME</center></th>  
            <th><center>DESCRIPTION</center></th>  
            <th><center>QTY</center></th>  
            <th><center>MRP</center></th>
            <th><center>DISCOUNT(%)</center></th>  
            <th><center>DISCOUNT PRICE</center></th>  
            <th><center>SP. PRICE</center></th>  
            <th><center>GST(%)</center></th>  
            <th><center>GST Amt.</center></th>  
            <th><center>SP. PRICE TOTAL</center></th>  
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
            <td style="text-align: center !important;">
                @if($image)
                <img src="{{ asset('/gallery/'.$image) }}" width="100" height="70" >
                @endif
            </td>
            <?php $totgst = $quot->price - $quot->price * $quot->discount / 100 ?>
            <?php $disprice = $quot->price - $quot->price * $quot->discount / 100 ?>

            <td style="text-align: center !important;">{{$codes}}</td>
            <td style="text-align: center !important;">{{$products}}</td>
            <td style="text-align: center !important;">{{$des}}</td>
            <td style="text-align: center !important;">{{$quot->qty}}</td>
            <td style="text-align: center !important;">{{$quot->price}}</td>
            <td style="text-align: center !important;">{{$quot->discount}}</td>
            <td style="text-align: center !important;">{{$quot->price * $quot->discount / 100}}</td>
            <td style="text-align: center !important;">@if(!($disprice == 0)){{$disprice}} @else {{$quot->price * $quot->qty}} @endif</td>
            <td style="text-align: center !important;">{{$gst}}</td>
            <td style="text-align: center !important;">{{$totgst*$gst/100}}</td>
            <?php 
            if($gst){
                $s_total = $s_total + ($quot->price - $quot->price * $quot->discount / 100 + $totgst*$gst/100)*$quot->qty;
                $last_amount = ($quot->price - $quot->price * $quot->discount / 100 + $totgst*$gst/100)*$quot->qty;
            }else{
                $s_total = $s_total + ($quot->price - $quot->price * $quot->discount / 100 + $totgst*$gst/100);
                $last_amount = ($quot->price - $quot->price * $quot->discount / 100 + $totgst*$gst/100);
            }

            ?>

            <td style="text-align: center !important;">{{ $last_amount }}</td>
        </tr>
        @php 
        $final_total += $last_amount; 
        $total_mrp += $quot->price; 
        @endphp
        @endforeach
        @endif
        <tr>
            <th style="background-color:#92d14e;" colspan="6">Total</th>
            <th style="background-color:#92d14e;"><center>{{$total_mrp}}</center></th>
            <th style="background-color:#92d14e;"></th>
            <th style="background-color:#92d14e;"></th>
            <th style="background-color:#92d14e;"></th>
            <th style="background-color:#92d14e;"></th>
            <th style="background-color:#92d14e;"></th>
            <th style="background-color:#92d14e;"><center>{{$final_total}}</center></th>
        </tr>
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
            <th colspan="5">Created By : {{JWTAuth::user()->name}}</th>
        </tr>
        <tr>
            <th colspan="8">* Freight & Unloading Charges Are Extra As Applicable</th>
            <th colspan="5">
                @if(JWTAuth::user()->id)
                Contact No : {{JWTAuth::user()->mobile_no}}
                @else
                Contact No : {{JWTAuth::user()->number}}
                @endif
            </th>
        </tr>
    </table>
    @endforeach
    @endif

</div>
</html>