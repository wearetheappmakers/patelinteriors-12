@extends('main')
@section('content')

<meta name="csrf-token" content="{{ csrf_token() }}" />
<div class="kt-container  kt-container--fluid  kt-grid_item kt-grid_item--fluid">
	<br>
	<div class="kt-portlet kt-portlet--mobile">
		<div class="kt-portlet__head">
			<div class="kt-portlet__head-label">
				<h3 class="kt-portlet__head-title">
					Purchase Order Report
				</h3>
			</div>
			<div class="kt-portlet__head-toolbar">
				<div class="kt-portlet__head-wrapper">
				</div>
			</div>
		</div>
		<div class="kt-portlet__body">
			<div class="row">
				@if(Auth::guard('user')->check())
				@if(Auth::user()->role == 1)  
				<div class="form-group col-lg-3">
					<label>Branch</label>
					<select name="branch_id" id="branch_id" class="form-control select2" onchange="getemployee(this)" required >
						<option value="">Select</option>
						@foreach($branch as $sub)
						<option value="{{$sub->id}}" >{{$sub->name}}</option>
						@endforeach
					</select>
				</div>
				<div class="form-group col-lg-3">
					<label>Vendor<span style="color: red;">*</span></label>
					<a style="background: white; height: 2px !important" target="_blank" href="{{route('user.vendor.create')}}" title="Add" class="btn btn-sm btn-clean btn-icon btn-icon-md">
						<i class="flaticon2-plus-1"></i>
					</a>
					<select name="vendor_id" class="form-control select2" id="vendor_id" onchange="getdata(this)" required>
						<option value="">Select</option>
					</select>
				</div>
				@endif
				@endif
				@if(Auth::guard('user')->check())
				@if(Auth::user()->role == 2)
				<?php
				$vendor = \App\Vendor::where('branch_id',Auth::user()->id)->get();
				?>
				<div class="form-group col-lg-3">
					<label>Vendor<span style="color: red;">*</span></label>
					<a style="background: white; height: 2px !important" target="_blank" href="{{route('user.vendor.create')}}" title="Add" class="btn btn-sm btn-clean btn-icon btn-icon-md">
						<i class="flaticon2-plus-1"></i>
					</a>
					<select name="vendor_id" class="form-control select2" id="vendor_id" onchange="getdata(this)" required>
						<option value="">Select</option>
						@foreach($vendor as $sub)
						<option value="{{$sub->id}}" >{{$sub->name}}</option>
						@endforeach
					</select>
				</div>    
				@endif
				@endif
				<div class="form-group col-lg-2" style="margin-top: 30px !important;">
					<button type="button" onclick="exportexcelTEST()" id="excelBTN" style="margin-left:5px !important" class="btn btn-brand btn-elevate btn-icon-sm change_button1" >
						<i class="fas fa-file-excel"></i>
						Excel
					</button>&nbsp;&nbsp;
					<button type="button" class="btn btn-brand btn-elevate btn-icon-sm" onclick="location.reload()"><i class="flaticon2-refresh"></i>Refresh</button>
				</div>
			</div>
			<div class="row">
				<div id="getdata"></div>
			</div>
			<div class="row">
				<div id="getdata1"></div>
			</div>
		</div>
	</div>
</div>
@stop
@push('scripts')
<script type="text/javascript">
	function exportexcelTEST() {
		var uri = 'data:application/vnd.ms-excel;base64,',
		template = '<html xmlns:o="urn:schemas-microsoft-com:office:office" ' +
		'xmlns:x="urn:schemas-microsoft-com:office:excel" ' +
		'xmlns="http://www.w3.org/TR/REC-html40">' +
		'<head><!--[if gte mso 9]><xml><' + 'x:ExcelWorkbook><' + 'x:ExcelWorksheets>' +
		'<' + 'x:ExcelWorksheet><' + 'x:Name>{worksheet}</' + 'x:Name>' +
		'<' + 'x:WorksheetOptions><' + 'x:DisplayGridlines/></' + 'x:WorksheetOptions>' +
		'</' + 'x:ExcelWorksheet></' + 'x:ExcelWorksheets></' + 'x:ExcelWorkbook></xml><![endif]--></head>' +
		'<body><table>{table}</table></body></html>',
		base64 = function (s) { return window.btoa(unescape(encodeURIComponent(s))) },
		format = function (s, c) { return s.replace(/{(\w+)}/g, function (m, p) { return c[p]; }) };

		var name = 'Sheet 1';
		var table1 = document.getElementById("getdata")?.innerHTML || "";
		var table2 = document.getElementById("getdata1")?.innerHTML || "";
		var combinedTables = table1 + "<tr><td colspan='100%'>&nbsp;</td></tr>" + table2;

		var ctx = { worksheet: name || 'Worksheet', table: combinedTables };
		var result = uri + base64(format(template, ctx));

		var link = document.createElement("a");
		document.body.appendChild(link);
		link.download = "PurchaseOrder-Report.xls";
		link.href = result;
		link.click();
	}

	function getemployee($this){
		var branch_id = $($this).val();
		var id = $($this).data('id');
		if(branch_id != '')
		{
			$('.loader-wrapper').show();
			$('.loader').show();
			$.ajax({
				type:"POST",
				headers: {
					'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
				},
				url:"{{ route('user.po.getemployee') }}",
				data: {
					'branch_id': branch_id
				},
				success: function(data){
					$('#vendor_id').html(data.data);
					$('.loader-wrapper').hide();
					$('.loader').hide();
				}
			});
		}
		else
		{
			$('#vendor_id').html('');
			$('.loader-wrapper').hide();
			$('.loader').hide();
		}
	};

	function getdata($this){
		var vendor_id = $($this).val();
		var id = $($this).data('id');
		if(vendor_id)
		{
			$('.loader-wrapper').show();
			$('.loader').show();
			$.ajax({
				type:"POST",
				headers: {
					'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
				},
				url:"{{ route('user.purchaseorder.report.getdata') }}",
				data: {
					'vendor_id': vendor_id
				},
				success: function(data){
					$('#getdata').html(data.data);
					$('#getdata1').html(data.data1)
					$('.loader-wrapper').hide();
					$('.loader').hide();
				}
			});
		}
		else
		{
			$('#getdata').html('');
			$('#getdata1').html('')
			$('.loader-wrapper').hide();
			$('.loader').hide();
		}
	};
</script>
@endpush