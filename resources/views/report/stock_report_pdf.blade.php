<!DOCTYPE html>
<html>
<head>
     <style type="text/css">
          body{
               font-family: "verdana", sans-serif;
               font-size: 12px !important;
          }
          table {
               width: 100%;
               border-collapse: collapse;
               margin-top: 20px;
          }

          table, th, td {
               border: 1px solid #ccc;
          }

          th {
               background-color: #f2f2f2;
               color: #333;
               font-weight: bold;
               text-align: center;
               padding: 10px;
          }

          td {
               padding: 8px;
               text-align: left;
          }

          tfoot {
               background-color: #e8f0fe;
               font-weight: bold;
          }

          tfoot td, tfoot th {
               text-align: center;
          }
     </style>
     <title>Stock Report</title>
</head> 
<div class="row">
     <div class="column">
          <h3 class="kt-portlet__head-title">
               Stock Report (
               Branch: <span style="color: #ff9800 !important;">{{ $product->branch->name }}</span> &nbsp; | &nbsp;
               Category: <span style="color: #009688 !important;">{{ $product->productcategory->name }}</span> &nbsp; | &nbsp;
               Company: <span style="color: #673ab7 !important;">{{ $product->company->name }}</span> &nbsp; | &nbsp;
               Product: <span style="color: #e91e63 !important;">{{ $product->product }}</span> &nbsp; | &nbsp;
               SKU Code: <span style="color: #9C27B0 !important;">{{ $product->code }}</span>
               )
          </h3>

          <table>
               <thead>
                    <tr>
                         <th>Sr. No.</th>
                         <th>Date</th>
                         <th>Client / Vendor Name</th>
                         <th>Tran Type</th>
                         <th>Invoice No.</th>
                         <th>Inward</th>
                         <th>Outward</th>
                    </tr>
               </thead>
               <tbody>
                    <?php $counter = 1; ?>
                    @if(count($stock_data))
                    @foreach ($stock_data as $value)
                    <?php
                         $vendorClientName = '-';
                         if($value->tran_type === 'Purchase Invoice')
                         {
                              $vendorClientName = $value->vendors->name ?? '-';
                         }
                         elseif($value->tran_type === 'Sales Invoice')
                         {
                              $vendorClientName = $value->clients->member ?? '-';
                         }
                    ?>
                    <tr>
                         <td style="text-align: center;">{{ $counter++ }}</td>
                         <td>{{ date('d-m-Y', strtotime($value->date)) }}</td>
                         <td>{{ $vendorClientName }}</td>
                         <td>{{ $value->tran_type }}</td>
                         <td>{{ $value->invoice_no }}</td>
                         <td style="text-align: right;">{{ $value->inward }}</td>
                         <td style="text-align: right;">{{ $value->outward }}</td>
                    </tr>
                    @endforeach
               </tbody>
               <tfoot>
                    <tr>
                         <th colspan="5" style="text-align: right;">Total</th>
                         <th style="text-align: right;">
                              {{ number_format($stock_data->sum('inward'), 2) }}
                         </th>
                         <th style="text-align: right;">
                              {{ number_format($stock_data->sum('outward'), 2) }}
                         </th>
                    </tr>
               </tfoot>
               @else
               <tr>
                    <td colspan="7" style="text-align:center;">No records found.</td>
               </tr>
               @endif
          </table>
     </div>
</div>
</html>