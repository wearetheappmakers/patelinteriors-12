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

  <?php $data = App\Pdfs::where('id',request()->id)->first(); ?>
  

  <div class="row">
  	<div class="column2">
  		<table style="border: none !important;">
  			<tr>
  				<th style="text-align: right; border: none !important;" colspan="2">
  					<img src="{{asset('assets/media/logos/patellogo3.jpg')}}" width="175" height="85" >
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
  				<th>Date:{{ date('d-m-Y',strtotime($data->date)) }}</th>
  			</tr>

  			<tr>
  				<th style="border-right: none !important;" colspan="2">Customer Name:&nbsp;{{$data->customer_name}}</th>
  				<th style="border-left: none !important;"></th>
  				<th>Qtn No:- &nbsp;{{$data->quotation_no}}</th>
  			</tr>

  			<tr>
  				<th style="border-right: none !important;" colspan="2">E-mail Id:&nbsp;{{$data->email}}</th>
  				<th style="border-left: none !important;"></th>
  				<th>Designer: &nbsp;{{$data->designer}}</th>
  			</tr>

  			<tr>
  				<th style="border-right: none !important;" colspan="2">Contact number:&nbsp;{{$data->contact_no}}</th>
  				<th style="border-left: none !important;"></th>
  				<th>Sales Contact: &nbsp;{{$data->sales_contact}}</th>
  			</tr>

  			<tr>
  				<th>Billing Name & Address</th>
  				<th colspan="2">Site Address : &nbsp;</th>
  				<th>Customer PAN NO</th>
  			</tr>

  			<tr>
  				<th height="2%">{{$data->billing_name_no}}</th>
  				<th height="2%" colspan="2">{{$data->site_address}}</th>
  				<th height="2%">{{$data->customer_pan_no}}</th>
  			</tr>

  			<tr>
  				<td colspan="2"><b>Model:</b></td>
  				<td colspan="2"><center>{{$data->model}}</center></td>
  			</tr>

  			<tr>
  				<td rowspan="2"><center>Front Combination</center></td>
  				<td><center><b>Base</b></center></td>
  				<td><center><b>Wall</b></center></td>
  				<td><center><b>Tall</b></center></td>
  			</tr>
  			<tr>
  				<td><center>{{$data->f_base}}</center></td>
  				<td><center>{{$data->f_wall}}</center></td>
  				<td><center>{{$data->f_tall}}</center></td>
  			</tr>

  			<tr>
  				<td colspan="2"><b>Carcass Combination:</b></td>
  				<td colspan="2"><center>{{$data->carcase_combination}}</center></td>
  			</tr>

  			<tr>
  				<td colspan="2"><b>Glass: Clear/Satin/Wire look/ tinted black</b></td>
  				<td colspan="2"><center>{{$data->glass}}</center></td>
  			</tr>

  			<tr>
  				<td colspan="2"><b>Fillers & Panels</b></td>
  				<td colspan="2"><center>{{$data->fillers_panels}}</center></td>
  			</tr>

  			<tr>
  				<td colspan="2"><b>Drawers & internals:</b></td>
  				<td colspan="2"><center>{{$data->drawers_internals}}</center></td>
  			</tr>

  			<tr>
  				<td colspan="2"><b>Handle:</b></td>
  				<td colspan="2"><center>{{$data->handles}}</center></td>
  			</tr>

  			<tr>
  				<td colspan="2"><b>Plinth colour:</b></td>
  				<td colspan="2"><center>{{$data->plinth_colour}}</center></td>
  			</tr>

  			<tr>
  				<td colspan="2"><b>Cornice:</b></td>
  				<td colspan="2"><center>{{$data->cornish}}</center></td>
  			</tr>

  			<tr>
  				<td colspan="2"><b>Plinth Height:</b></td>
  				<td colspan="2"><center>{{$data->plinth_height}}</center></td>
  			</tr>

  			<tr>
  				<td colspan="2"><b>Worktop Height:</b></td>
  				<td colspan="2"><center>{{$data->worktop_height}}</center></td>
  			</tr>
  		</table>
  	</div>
  </div>

  <div class="row">
  <div class="column2">
    <table>
      <tr>
        <th style="border-top: none !important; border-bottom: none !important;" height="3%"></th>
      </tr>
    </table>
  </div>  
</div>

<div class="row">
  <div class="column2">
    <table>
      <tr>
        <th width="10%">SI No</th>
        <th><center>Summary</center></th>
        <th width="43%"><center>Amount</center></th>
      </tr>

      <tr>
        <td style="text-align:right;" width="10%">1</td>
        <td><b>Appliances and sink accessories – Not Included</b></td>
        <td width="43%"><center>-</center></td>
      </tr>

      <tr>
        <td style="text-align:right;" width="10%">2</td>
        <td><b>Worktop and Plywood – Not Included</b></td>
        <td width="43%"><center>-</center></td>
      </tr>

      <tr>
        <td style="text-align:right;" width="10%">3</td>
        <td><b>Ducting and gas piping – Not Included</b></td>
        <td width="43%"><center>-</center></td>
      </tr>


      @php
      $price = $data->kitchen_furniture * 25/100;
      $total_price = $price + $data->kitchen_furniture;
      @endphp
      <tr>
        <td style="text-align:right;" width="10%">4</td>
        <td><b>Kitchen Furniture and Trade goods</b></td>
        <td width="43%"><center>{{number_format($total_price)}}</center></td>
      </tr>

      @php
      $discount = $total_price * 20 /100;
      $total_discount = $total_price - $discount;
      @endphp

      <tr>
        <td style="text-align:right;" width="10%">5</td>
        <td><b>Discount 20%</b></td>
        <td width="43%"><center>{{number_format($discount)}}</center></td>
      </tr>

      @php
      $gst = $total_discount * 18 /100;
      $gst_amount = $total_discount + $gst;
      @endphp

      <tr>
        <td width="10%"></td>
        <td><b>Total</b></td>
        <td width="43%"><center>{{number_format($total_discount)}}</center></td>
      </tr>

      <tr>
        <td width="10%"></td>
        <td><b>GST 18%</b></td>
        <td width="43%"><center>{{number_format($gst)}}</center></td>
      </tr>

      <tr>
        <td height="3%" width="10%"></td>
        <td></td>
        <td width="43%" ><center>{{number_format($gst_amount)}}</center></td>
      </tr>

      <tr>
        <td height="2%" width="10%"></td>
        <td><b>Transportation & installation charges</b></td>
        <td width="43%"><center></center></td>
      </tr>

      <tr>
        <td height="2%" width="10%"></td>
        <td style="text-align:right"><b>Grand total</b></td>
        <td width="43%"><center>{{number_format($gst_amount)}}</center></td>
      </tr>

    </table>
  </div>
</div>



  </html>