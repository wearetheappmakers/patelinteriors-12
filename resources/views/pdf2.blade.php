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
  <div class="column">
    <table>
      <tr>
        <th>
          <img src="{{asset('assets/media/logos/patellogo.jpg')}}" width="175" height="85" >
        </th>
      </tr>
    </table>
  </div>

  <div class="column1">
    <table>
      <tr><th><center>PATEL INTERIORS PVT. LTD.</center></th></tr>
      <tr><td><center>Opp. Masoom School,Opp,Selenium,Sky Apartment</center></td></tr> 
      <tr><td><center>off Kalawad Road,Mota,Mauva,Rajkot-360005.</center></td></tr> 
      <tr><td><center>Call: +91 7567523124 E-mail: rajkot@kuche7.com</center></td></tr>   
      
    </table>
  </div>

  <div class="column">
    <table>
      <tr>
        <th>
          <img src="{{asset('assets/media/logos/patellogo2.jpg')}}" width="175" height="85" >
        </th>
      </tr>     
      
    </table>
  </div>
</div>

<div class="row">
  <div class="column2">
    <tr>
      <th height="1%"></th>
    </tr>
  </div>  
</div>

<div class="row">

  <table>
    <tr>
      <th height="1%" colspan="4"></th>
    </tr>

    <tr>
      <td colspan="2">ESTIMATE / FINAL QUOTE :-</td>
      <td>Date</td>
      <td><center>{{ date('d-m-Y',strtotime($data->date)) }}</center></td>
    </tr>

    <tr>
      <td>Customer Name</td>
      <td><center>{{$data->customer_name}}</center></td>
      <td>Qtn No</td>
      <td><center>{{$data->quotation_no}}</center></td>
    </tr>

    <tr>
      <td>Site Address</td>
      <td><center>{{$data->site_address}}</center></td>
      <td>Designer</td>
      <td><center>{{$data->designer}}</center></td>
    </tr>

    <tr>
      <td>Email ID</td>
      <td><center>{{$data->email}}</center></td>
      <td>Sales Contact</td>
      <td><center>{{$data->sales_contact}}</center></td>
    </tr>

    <tr>
      <td>Contact Number</td>
      <td><center>{{$data->contact_no}}</center></td>
      <td colspan="2"></td>
    </tr>

    <tr>
      <td>Architect</td>
      <td><center>{{$data->architect}}</center></td>
      <td colspan="2"></td>
    </tr>

    <tr>
      <td colspan="2"><center>Model</center></td>
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
    <td colspan="2"><center>Carcase Combination</center></td>
    <td colspan="2"><center>{{$data->carcase_combination}}</center></td>
  </tr>

  <tr>
    <td colspan="2"><center>Glass : Clear / Tinted / Satin / Ribbed</center></td>
    <td colspan="2"><center>{{$data->glass}}</center></td>
  </tr>

  <tr>
    <td colspan="2"><center>Fillers & Panels</center></td>
    <td colspan="2"><center>{{$data->fillers_panels}}</center></td>
  </tr>

  <tr>
    <td colspan="2"><center>Drawers & Internals</center></td>
    <td colspan="2"><center>{{$data->drawers_internals}}</center></td>
  </tr>

  <tr>
    <td colspan="2"><center>Handles</center></td>
    <td colspan="2"><center>{{$data->handles}}</center></td>
  </tr>

  <tr>
    <td colspan="2"><center>Plinth Colour</center></td>
    <td colspan="2"><center>{{$data->plinth_colour}}</center></td>
  </tr>

  <tr>
    <td colspan="2"><center>Cornish</center></td>
    <td colspan="2"><center>{{$data->cornish}}</center></td>
  </tr>

  <tr>
    <td colspan="2"><center>Plinth Height</center></td>
    <td colspan="2"><center>{{$data->plinth_height}}</center></td>
  </tr>

</table>
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
        <td width="10%">Sr. No</td>
        <td>Summary</td>
        <td width="43%"><center>Amount</center></td>
      </tr>

      <tr>
        <td width="10%" style="text-align:right;">1</td>
        <td>Kitchen Appliances</td>
        <td width="43%"><center>{{$data->kitchen_appliances}}</center></td>
      </tr>

      <tr>
        <td width="10%" style="text-align:right;">2</td>
        <td>Sitchen Sink</td>
        <td width="43%"><center>{{$data->sitchen_sink}}</center></td>
      </tr>

      <tr>
        <td width="10%" style="text-align:right;">3</td>
        <td>Core cutting, Ducting and Gas Piping</td>
        <td width="43%"><center>{{$data->core_cutting}}</center></td>
      </tr>

      <tr>
        <td width="10%" style="text-align:right;">4</td>
        <td>Worktop / Stone</td>
        <td width="43%"><center>{{$data->worktop}}</center></td>
      </tr>

      @php
      $price = $data->kitchen_furniture * 25/100;
      $total_price = $price + $data->kitchen_furniture;
      @endphp
      <tr>
        <td width="10%" style="text-align:right;">5</td>
        <td>Kitchen Furniture and Trade goods</td>
        <td style="text-align:right;" width="43%">{{number_format($total_price)}}</td>
      </tr>

      @php 

      $discount = $total_price * 20/100;
      $total_discount = $total_price - $discount;
      @endphp
      <tr>
        <td width="10%"></td>
        <td>Discount 20%</td>
        <td style="text-align:right;" width="43%">{{number_format($total_discount)}}</td>
      </tr>

      @php
      $gst = $total_discount * 18 /100;
      $gst_amount = $total_discount + $gst;
      @endphp
      <tr>
        <td width="10%"></td>
        <td>GST 18%</td>
        <td style="text-align:right;" width="43%">{{number_format($gst)}}</td>
      </tr>

      <tr>
        <td width="10%"></td>
        <td>Total</td>
        <td style="text-align:right;" width="43%">{{number_format($gst_amount)}}</td>
      </tr>

      <tr>
        <td></td>
        <td>Transportation and Installation</td>
        <td style="text-align: right;">Included</td>
      </tr>

      <tr>
        <td height="8%" style="border-right: none !important;">Office <br>
          Sign.
        </td>

        <td height="8%" style="border-right: none !important; border-left: none !important;"></td>

        <td height="8%" style="border-left: none !important;"><br><br>
          <center>Client Sign.</center>
        </td>
      </tr>
    </table>
  </div>
</div>



</html>