@extends('main')
@section('content')

<style type="text/css">
	body {
		font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
		font-size: 14px;
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

	.kt-portlet__head-title {
		font-size: 22px;
		font-weight: bold;
		color: #2c3e50;
		margin: 20px 0;
	}

	tfoot {
		background-color: #e8f0fe;
		font-weight: bold;
	}

	tfoot td, tfoot th {
		text-align: center;
	}
</style>

<meta name="csrf-token" content="{{ csrf_token() }}" />
<div class="kt-container  kt-container--fluid  kt-grid__item kt-grid__item--fluid">
	<br>
	<div class="kt-portlet kt-portlet--mobile">
		<div class="kt-portlet__head kt-portlet__head--lg">
			<div class="kt-portlet__head-label">
				<h3 class="kt-portlet__head-title">
					📦 Stock Report (
					Branch: <span style="color: #ff9800 !important;">{{ $product->branch->name }}</span> &nbsp; | &nbsp;
					Category: <span style="color: #009688 !important;">{{ $product->productcategory->name }}</span> &nbsp; | &nbsp;
					Company: <span style="color: #673ab7 !important;">{{ $product->company->name }}</span> &nbsp; | &nbsp;
					Product: <span style="color: #e91e63 !important;">{{ $product->product }}</span> &nbsp; | &nbsp;
					SKU Code: <span style="color: #9C27B0 !important;">{{ $product->code }}</span>
					)
				</h3>
			</div>
			
			<form class="kt-form kt-form--label-right add_form" id="add_form">
				<input type="hidden" name="report_type" value="stock_report">
				@csrf
				<div class="kt-portlet__head-toolbar"><br><br><br>
					<div class="kt-portlet__head-wrapper">
						<button type="button" onclick="exportexcelTEST()" id="excelBTN" style="margin-left: 5px !important" class="btn btn-brand btn-elevate btn-icon-sm change_button1">
							<i class="fas fa-file-excel"></i>
							Excel
						</button>&nbsp;&nbsp;
						<button type="button" id="pdfexport" class="btn btn-brand btn-elevate btn-icon-sm">
							<i class="fas fa-print"></i>
							PDF
						</button>
					</div>
				</div>
			</form>
		</div>
		<div class="kt-portlet__body" id="getdata">
			<table id="stockReportTable">
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
						<td>
							@if ($value->tran_type === 'Purchase Invoice')
							<a href="{{ route('user.purchaseinvoice.view', $value->purchaseinvoice_id) }}" target="_blank">
								{{ $value->invoice_no }}
							</a>
							@elseif ($value->tran_type === 'Sales Invoice')
							<a href="{{ route('user.salesorderinvoice.view', $value->salesinvoice_id) }}" target="_blank">
								{{ $value->invoice_no }}
							</a>
							@else
							{{ $value->invoice_no }}
							@endif
						</td>
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
			<br><br>
			

			<div class="d-flex justify-content-between align-items-center mb-3">

				<h4 style="font-weight:600; margin:0;">
					Stock Transfer History
				</h4>
				
				<form class="kt-form kt-form--label-right"
					id="transfer_history_form">
					@csrf
					<input type="hidden" name="report_type" value="transfer_history">

					<button type="button"
							onclick="exportTransferHistoryExcel()"
							id="transferExcelBTN"
							class="btn btn-brand btn-elevate btn-icon-sm">

						<i class="fas fa-file-excel"></i>
						Excel

					</button>

					<button type="button"
							id="transferPdfExport"
							class="btn btn-brand btn-elevate btn-icon-sm ml-2">

						<i class="fas fa-print"></i>
						PDF

					</button>

				</form>
			</div>
			@php
				$showEmployeeColumn = $transferHistory->contains(function ($row) {
					return !empty($row->employee_id);
				});
			@endphp
			<table id="transferHistoryTable">
				<thead>
					<tr>
						<th>Sr. No.</th>
						<th>Date</th>
						<th>Branch</th>
						<th>Category</th>
						<th>Company</th>
						<th>Product</th>
						<th>From Warehouse</th>
						<th>To Warehouse</th>
						<th>Inward</th>
						<th>Outward</th>
						@if($showEmployeeColumn)
							<th>Employee Name</th>
						@endif
						<th>Remark</th>
					</tr>
				</thead>

				<tbody>

				@php $i = 1; @endphp

				@forelse($transferHistory as $row)
				<tr>
					<td>{{ $i++ }}</td>
					<td>{{ date('d-m-Y', strtotime($row->transfer_date)) }}</td>
					<td>{{ $row->branch->name ?? '-' }}</td>
					<td>{{ $row->product->productcategory->name ?? '-' }}</td>
					<td>{{ $row->product->company->name ?? '-' }}</td>
					<td>{{ $row->product->product ?? '-' }}</td>
					<td>{{ $row->fromWarehouse->name ?? '-' }}</td>
					<td>{{ $row->toWarehouse->name ?? '-' }}</td>
					<td style="text-align: right">{{ number_format($row->inward, 2) }}</td>
					<td style="text-align: right">{{ number_format($row->outward, 2) }}</td>
					@if($showEmployeeColumn)
						<td>
							@if($row->employee_id)
								{{ $row->employee->name ?? '-' }}
							@else
								-
							@endif
						</td>
					@endif
					<td>{{ $row->remark }}</td>
				</tr>

				@empty

				<tr>
					<td colspan="11" style="text-align:center;">
						No Stock Transfer History Found.
					</td>
				</tr>

				@endforelse

				</tbody>

				<tfoot>
					<tr>
						<th colspan="8" style="text-align:right;">
							Total
						</th>

						<th style="text-align:right;">
							{{ number_format($transferHistory->sum('inward'), 2) }}
						</th>

						<th style="text-align:right;">
							{{ number_format($transferHistory->sum('outward'), 2) }}
						</th>
						@if($showEmployeeColumn)
							<th></th>
						@endif
						<th></th>
					</tr>
				</tfoot>
			</table>
		</div>
	</div>
</div>	

@stop

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/xlsx@0.18.5/dist/xlsx.full.min.js"></script>
<script type="text/javascript">
	function exportexcelTEST() {

    let wb = XLSX.utils.book_new();

    let ws = XLSX.utils.table_to_sheet(
        document.getElementById("stockReportTable")
    );

    XLSX.utils.book_append_sheet(wb, ws, "Stock Report");

    XLSX.writeFile(wb, "Stock-Report.xlsx");
}
	function exportTransferHistoryExcel() {

		let wb = XLSX.utils.book_new();

		let ws = XLSX.utils.table_to_sheet(
			document.getElementById("transferHistoryTable")
		);

		XLSX.utils.book_append_sheet(wb, ws, "Transfer History");

		XLSX.writeFile(wb, "Stock-Transfer-History.xlsx");
	}

	$(document).ready(function() {
		$("#pdfexport").on("click", function(e) {
			e.preventDefault();
			if ($(".add_form").valid()) {
				$('.loader-wrapper').show();
				$('.loader').show();
				$.ajax({
					type: "POST",
					url: "{{ route('user.stock.report.pdf', ['product_id' => $product->id, 'warehouse_id' => $warehouse_id]) }}",
					data: new FormData($('.add_form')[0]),
					xhrFields: {
						responseType: 'blob',
					},
					processData: false,
					contentType: false,
					success: function(blob) {
						var link=document.createElement('a');
						link.href=window.URL.createObjectURL(blob);
						link.download = 'Stock-Report.pdf'
						link.click();
						$('.loader-wrapper').hide();
						$('.loader').hide();
					},
				});
			} else {
				e.preventDefault();
				$('.loader-wrapper').hide();
				$('.loader').hide();
			}
		});

		$("#transferPdfExport").click(function(e){

    e.preventDefault();

    $('.loader-wrapper').show();
    $('.loader').show();

    $.ajax({

        type: "POST",

       url: "{{ route('user.stock.report.pdf',['product_id' => $product->id, 'warehouse_id' => $warehouse_id]) }}",

        data: new FormData($('#transfer_history_form')[0]),

        xhrFields:{
            responseType:'blob'
        },

        processData:false,

        contentType:false,

        success:function(blob){

            var link=document.createElement('a');

            link.href=window.URL.createObjectURL(blob);

            link.download='Stock-Transfer-History.pdf';

            link.click();

            $('.loader-wrapper').hide();
            $('.loader').hide();

        }

    });

});
		
	});
</script>
@endpush