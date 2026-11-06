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
  			width: 25%;
  			/*padding: 5px;*/
  		}

  		.column1 {
  			float: left;
  			width: 50%;
  			/*padding: 5px;*/
  		}

  		.column2 {
  			/*float: left;*/
  			width: 100%;
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
  <?php $clone = App\QuotationChild::with('products')->where('quotation_id',$data->id)->get(); ?>
  <?php $delivery = App\Delivery::where('quotation_id',$data->id)->first(); ?>
  <?php $branch = App\User::where('role',2)->where('id',$data->branch_id)->value('name'); ?>
  <?php $cname = App\Client::where('id',$data->client_id)->value('member'); ?>
  <?php $cnum = App\Client::where('id',$data->client_id)->value('mobile_no'); ?>
  <?php $caddress = App\Client::where('id',$data->client_id)->value('address'); ?>
  <?php $cmail = App\Client::where('id',$data->client_id)->value('email'); ?>
  <?php $gst = App\GST::where('id',$data->gst_id)->value('gst'); ?>
  <?php $lead = App\Lead::where('id',$data->lead_id)->first(); ?>
  <?php $employee = App\Employee::where('id',$data->employee_id)->value('name'); ?>







  <div class="row">
  	<div class="column2">
  		<table style="border: none !important;">
  			<tr>
  				<th style="text-align: right; border: none !important;" colspan="2">
  					<img src="{{asset('assets/media/logos/patellogo4.jpg')}}" width="175" height="65" >
  				</th>
  			</tr>

  			<tr>
  				<th style="border: none !important;" height="1%"></th>
  				<th style="border: none !important;" width="3%"></th>      	
  			</tr>

  			<tr>
  				<th style="text-align: right; border: none !important;">PATEL INTERIORS PVT LTD.</th>
  				<th style="border: none !important;" width="3%"></th>
  			</tr>

  			<tr>
  				<th style="text-align: right; border: none !important;">Opp. Selenium Sky Apartment off Kalawad Road, Mota Mauva,</th>
  				<th style="border: none !important;" width="3%"></th>

  			</tr>

  			<tr>
  				<th style="text-align: right; border: none !important;">Rajkot-360005. T: +917575022200, +917575066600</th>
  				<th style="border: none !important;" width="3%"></th>
  			</tr>
  		</table>
  	</div>
  </div>

  <div class="row">
  	<div class="column2">
  		<table>
  			<tr>
  				<th style="background-color: black;"></th>
  			</tr>
  		</table>
  	</div>
  </div>

  <div class="row">
  	<div class="column2">
  		<table>
  			<tr>
  				<th height="1%" style="background-color: white; border: none !important;"></th>
  			</tr>
  		</table>
  	</div>
  </div>

  <div class="row">
  	<div class="column2">
  		<table>
  			<tr>
  				<th style="border-right: none !important;" colspan="2">ESTIMATE / FINAL QUOTE</th>
  				<th style="border-left: none !important;"></th>
  				<th>Date:{{ date('d-m-Y',strtotime($data->quotation_date)) }}</th>
  			</tr>

  			<tr>
  				<th style="border-right: none !important;" colspan="2">Customer Name:&nbsp;{{$cname}}</th>
  				<th style="border-left: none !important;"></th>
  				<th>Qtn No:- &nbsp;{{$data->id}}</th>
  			</tr>

  			<tr>
  				<th style="border-right: none !important;" colspan="2">E-mail Id:&nbsp;{{$cmail}}</th>
  				<th style="border-left: none !important;"></th>
  				<th>Designer: &nbsp;</th>
  			</tr>

  			<tr>
  				<th style="border-right: none !important;" colspan="2">Contact number:&nbsp;{{$cnum}}</th>
  				<th style="border-left: none !important;"></th>
  				<th></th>
  				{{-- <th>Sales Contact: &nbsp;{{$employee}}</th> --}}
  			</tr>

  			<tr>
  				<th>Billing Name & Address</th>
  				<th colspan="2">Site Address : &nbsp;{{$caddress}}</th>
  				<th>Customer PAN NO</th>
  			</tr>

  			<tr>
  				<td><center>Billing Name & Address</center></td>
  				<td colspan="2" ><center>Site Address</center></td>
  				<td></td>
  			</tr>
  			<tr>
  				<td rowspan="3"></td>
  				<td colspan="2" rowspan="3"></td>
  				<td  height="2%"></td>
  			</tr>
  			<tr>
  				<td  height="2%"></td>
  			</tr>
  			<tr>
  				<td  height="2%"></td>
  			</tr>

  			<tr>
  				<th>Architect :-</th>
  				<th colspan="2">Email-Id :- &nbsp;{{$cmail}}</th>
  				<th>Project :-</th>
  			</tr>

  		</table>
  	</div>
  </div>

  <div class="row">
  <div class="column2">
    <table>
      <tr>
        <th style="background-color:yellow; border-top: none !important; border-bottom: none !important;" height="2%"></th>
      </tr>
    </table>
  </div>  
</div>

<div class="row">
	<div class="column2">
		<table>
			<tr>
				<td colspan="1"><b>Model:</b></td>
				<td colspan="3"><center>A19 Artwood</center></td>
			</tr>

			<tr>
  				<td rowspan="2"><center>Front Combination</center></td>
  				<td><center><b>Base</b></center></td>
  				<td><center><b>Wall</b></center></td>
  				<td><center><b>Tall</b></center></td>
  			</tr>
  			<tr>
  				<td>
  					<center>22R melamin resin,</center>
  					<center>walnut royal</center>
  				</td>
  				<td>
  					<center>22R melamin resin,</center>
  					<center>walnut royal</center>
  				</td>
  				<td>
  					<center>22R melamin resin,</center>
  					<center>walnut royal</center>
  				</td>
  			</tr>

  			<tr>
  				<td colspan="2"><b>Carcass Combination:</b></td>
  				<td colspan="2"><center>NBR walnut Royal</center></td>
  			</tr>

  			<tr>
  				<td colspan="2"><b>Glass:</b></td>
  				<td colspan="2"><center>-</center></td>
  			</tr>

  			<tr>
  				<td colspan="2"><b>Drawers & internals:</b></td>
  				<td colspan="2"><center>PLE Premium LINE Reling</center></td>
  			</tr>

  			<tr>
  				<td colspan="2"><b>Handle:</b></td>
  				<td colspan="2"><center>591 Matrix art, black</center></td>
  			</tr>

  			<tr>
  				<td colspan="2"><b>Plinth colour:</b></td>
  				<td colspan="2"><center>PWD premium white</center></td>
  			</tr>

  			<tr>
  				<td colspan="2"><b>Plinth height:</b></td>
  				<td colspan="2"><center>70mm</center></td>
  			</tr>

  			<tr>
  				<td colspan="2"><b>Worktop height:</b></td>
  				<td colspan="2"><center>40mm</center></td>
  			</tr>
		</table>
	</div>
</div>

<div class="row">
  <div class="column2">
    <table>
      <tr>
        <th style="background-color:yellow; border-top: none !important; border-bottom: none !important;" height="3%"></th>
      </tr>
    </table>
  </div>  
</div>

<div class="row">
  <div class="column2">
    <table>
      <tr>
        <th width="10%"><center>SI No</center></th>
        <th><center>SUMMARY</center></th>
        <th width="43%"><center>AMOUNT</center></th>
      </tr>

      <tr>
        <td width="10%"><center>1</center></td>
        <td><b>Appliances and sink accessories</b></td>
        <td width="43%"><center>-</center></td>
      </tr>

      <tr>
        <td width="10%"><center>2</center></td>
        <td><b>Worktop & Dado</b></td>
        <td width="43%"><center>-</center></td>
      </tr>

      <tr>
        <td width="10%"><center>3</center></td>
        <td><b>Ducting and gas piping</b></td>
        <td width="43%"><center>-</center></td>
      </tr>

      <tr>
        <td width="10%"><center>4</center></td>
        <td><b>Transportation & installation charges</b></td>
        <td width="43%"><center>-</center></td>
      </tr>

      @php
      $price = $data->q_price4 * 25/100;
      $total_price = $price + $data->q_price4;
      @endphp

      <tr>
        <td width="10%"><center>5</center></td>
        <td><b>Kitchen Furniture and Trade goods</b></td>
        <td width="43%"><center>{{number_format($total_price)}}</center></td>
      </tr>

      @php
      $discount = $total_price * 20 /100;
      $total_discount = $total_price - $discount;
      @endphp

      <tr>
        <td width="10%"><center></center></td>
        <td><b>Discount 20%</b></td>
        <td width="43%"><center>{{number_format($discount)}}</center></td>
      </tr>

      @php
      $gst = $total_discount * 18 /100;
      $gst_amount = $total_discount + $gst;
      @endphp

      <tr>
        <td width="10%"><center></center></td>
        <td><b>Total</b></td>
        <td width="43%"><center>{{number_format($total_discount)}}</center></td>
      </tr>

      <tr>
        <td width="10%"><center></center></td>
        <td><b>GST 18%</b></td>
        <td width="43%"><center>{{number_format($gst)}}</center></td>
      </tr>

      <tr>
        <td width="10%"><center></center></td>
        <td style="text-align: right;"><b>Grand total</b></td>
        <td width="43%"><center>{{number_format($gst_amount)}}</center></td>
      </tr>

     
    </table>
  </div>
</div>





  </html>