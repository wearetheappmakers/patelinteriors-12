<table class="table table-bordered dashed-table">
	<thead>
		<tr>
			@if(Auth::guard('user')->check())
			@if(Auth::user()->role == 1)
			<th style="font-weight: bold !important;">Branch</th>
			<th style="font-weight: bold !important;">Employee</th>
			@endif
			@if(Auth::user()->role == 2)
			<th style="font-weight: bold !important;">Employee</th>
			@endif
			@endif
			<th style="font-weight: bold !important;">Client</th>
			<th style="font-weight: bold !important;">Mobile</th>
			<th style="font-weight: bold !important;">Email</th>
			<th style="font-weight: bold !important;">Landmark</th>
		</tr>
	</thead>
	<tbody>
		@if(count($clients) > 0)
		@foreach($clients as $key => $value)
		<tr>
			@if(Auth::guard('user')->check())
			@if(Auth::user()->role == 1)
			<td>{{ isset($value->branch) ? $value->branch->name : '' }}</td>
			<td>{{ isset($value->employee) ? $value->employee->name : '' }}</td>
			@endif
			@if(Auth::user()->role == 2)
			<td>{{ isset($value->employee) ? $value->employee->name : '' }}</td>
			@endif
			@endif
			<td>{{ $value->member }}</td>
			<td>{{ $value->mobile_no }}</td>
			<td>{{ $value->email }}</td>
			<td>{{ isset($value->landmarks) ? $value->landmarks->name : '' }}</td>
		</tr>
		@endforeach
		@endif
	</tbody>
</table>