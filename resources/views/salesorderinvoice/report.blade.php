@extends('main')
@section('content')

<meta name="csrf-token" content="{{ csrf_token() }}" />
<div class="kt-container  kt-container--fluid  kt-grid_item kt-grid_item--fluid">
	<br>
	<div class="kt-portlet kt-portlet--mobile">
		<div class="kt-portlet__head">
			<div class="kt-portlet__head-label">
				<h3 class="kt-portlet__head-title">
					Sales Invoice Report
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
					<label>Branch  </label>
					@php $branch = App\User::where('role',2)->get(); @endphp
					<select name="branch_id" class="form-control select2" id="branch_id" onchange="getemployee(this)" required >
						<option value="">Select</option>
						@foreach($branch as $sub)
						<option value="{{$sub->id}}" >{{$sub->name}}</option>
						@endforeach
					</select>
				</div>
				<div class="form-group col-lg-3">
					<label>Employee  </label>
					<select name="employee_id" class="form-control select2" id="employee_id" onchange="getdata(this)">
						<option value="">Select</option>
					</select>
				</div>
				<div class="form-group col-lg-3">
					<label>Client  </label>
					<select name="client_id" class="form-control select2" id="client_id" onchange="getdata(this)"  required >
						<option value="">Select</option>
					</select>
				</div>
				@else
				<div class="form-group col-lg-3">
					<label>Employee  </label>
					<select name="employee_id" class="form-control select2" id="employee_id" onchange="getdata(this)">
						@php  $emp = App\Employee::where('branch_id',Auth::user()->id)->get();
						@endphp
						<option value="">Select</option>
						@foreach($emp as $cl)
						<option value="{{$cl->id}}" >{{$cl->name}}</option>
						@endforeach
					</select>
				</div>
				<div class="form-group col-lg-3">
					<label>Client  </label>
					<select name="client_id" class="form-control select2" id="client_ids" onchange="getdata(this)"  required >
						@php  $clients = App\Client::where('branch_id',Auth::user()->id)->get();
						@endphp
						<option value="">Select</option>
						@foreach($clients as $cl)
						<option value="{{$cl->id}}" >{{$cl->member}}</option>
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
<script src="https://cdn.jsdelivr.net/npm/xlsx@0.18.5/dist/xlsx.full.min.js"></script>
<script type="text/javascript">
function exportexcelTEST() {
    let wb = XLSX.utils.book_new();
    let allData = [];
    document.querySelectorAll("#getdata table, #getdata1 table").forEach((table, tIndex) => {
        let tableData = XLSX.utils.sheet_to_json(
            XLSX.utils.table_to_sheet(table),
            { header: 1 }
        );
        if (tIndex > 0) {
            allData.push([]); 
        }
        allData = allData.concat(tableData);
    });
    let ws = XLSX.utils.aoa_to_sheet(allData);
    XLSX.utils.book_append_sheet(wb, ws, "Report");
    XLSX.writeFile(wb, "SalesInvoice-Report.xlsx");
}
</script>
<script type="text/javascript">

	function getemployee($this){
		var branch_id = $($this).val();
		var id = $($this).data('id');
		$('.loader-wrapper').show();
		$('.loader').show();
		$.ajax({
			type:"POST",
			headers: {
				'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
			},
			url:"{{ route('user.so.getemployee') }}",
			data: {
				'branch_id': branch_id
			},
			success: function(data){
				$('#client_id').html(data.data5);
				$('#employee_id').html(data.data);
				$('.loader-wrapper').hide();
				$('.loader').hide();
			}
		});
	};
	function getdata($this){
		var client_id = $($this).val();
		var employee_id = $('#employee_id').val();
		var id = $($this).data('id');
		if(client_id || employee_id)
		{
			$('.loader-wrapper').show();
			$('.loader').show();
			$.ajax({
				type:"POST",
				headers: {
					'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
				},
				url:"{{ route('user.salesorderinvoice.report.getdata') }}",
				data: {
					'client_id': client_id,
					'employee_id': employee_id
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