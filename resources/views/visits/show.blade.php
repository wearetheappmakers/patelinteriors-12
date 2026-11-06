@extends('main')
@section('content')
<div class="kt-container  kt-container--fluid  kt-grid__item kt-grid__item--fluid">
	<br>
	<div class="kt-portlet kt-portlet--mobile">
		<div class="kt-portlet__head">
			<div class="kt-portlet__head-label">
				<span class="kt-portlet__head-icon">
					<i class="kt-font-brand"></i>
				</span>
				<h3 class="kt-portlet__head-title">
					Show Interior Visit
				</h3>
			</div>
			<div class="kt-portlet__head-toolbar">
				<div class="kt-portlet__head-wrapper">
					<div class="kt-portlet__head-actions">					
						<a href="{{URL::previous()}}" class="btn btn-brand btn-elevate btn-icon-sm">
							<i class="flaticon2-fast-back"></i>
							Back
						</a>
					</div>
				</div>
			</div>
		</div>
		<div class="kt-portlet__body">
			<div class="tab-pane active" id="client" role="tabpanel">
				<div class="row">
					@if(Auth::guard('user')->check())
					@if(Auth::user()->role == 1)  
					<div class="form-group col-lg-2">
						<label>Branch  </label>
						<input type="text" class="form-control" value="{{ $data->branch->name }}" readonly>
					</div>                       
					<div class="form-group col-lg-2">
						<label>Employee  </label>
						<input type="text" class="form-control" value="{{ $data->employee->name }}" readonly>
					</div>
					<div class="form-group col-lg-2">
						<label>Ar/ID  </label>
						<input type="text" class="form-control" value="{{ $data->arids->name }}" readonly>
					</div>
					@endif			
					@endif
					@if(Auth::guard('user')->check())
					@if(Auth::user()->role == 2)
					<div class="form-group col-lg-2"> 
						<label>Employee  </label>
						<input type="text" class="form-control" value="{{ $data->employee->name }}" readonly>
					</div>
					<div class="form-group col-lg-2">
						<label>Ar/ID  </label>
						<input type="text" class="form-control" value="{{ $data->arids->name }}" readonly>
					</div>
					@endif
					@endif
					@if(Auth::guard('employee')->check())
					<div class="form-group col-lg-2">
						<label>Ar/ID  </label>
						<input type="text" class="form-control" value="{{ $data->arids->name }}" readonly>
					</div>
					@endif
					<div class="form-group col-lg-2">
						<label>Email</label>
						<input type="text" class="form-control" placeholder="Enter Email" value="{{$data->email}}" readonly>
					</div>
					<div class="form-group col-lg-2">
						<label>Number</label>
						<input type="number" class="form-control" placeholder="Enter Number" value="{{$data->number}}" readonly>
					</div>
					<div class="form-group col-lg-2">
						<label>Firm Name</label>
						<input type="text" class="form-control" placeholder="Enter Firm Name" value="{{$data->firm_name}}" readonly>
					</div>
					<div class="form-group col-lg-2">
						<label>Date</label>
						<input type="date" class="form-control" placeholder="" value="{{$data->date}}" readonly name="date" >
					</div>
					<div class="form-group col-lg-2">
						<label>Time</label>
						<input type="time" class="form-control" placeholder="" value="{{$data->time}}" readonly name="time" >
					</div>
					<div class="form-group col-lg-2">
						<label>Company Person</label>
						<input type="text" class="form-control" value="{{$data->company_person}}" placeholder="Enter Company Person" readonly>
					</div>
					<div class="form-group col-lg-2">
						<label>Remarks</label>
						<textarea id="remarks" name="remarks" rows="1" placeholder="Enter Remarks" readonly class="form-control">{{$data->remarks}}</textarea>
					</div>
					<div class="form-group col-lg-2">
						<label>Showroom Visit</label>
						<select class="form-control" disabled>
							<option value="">Select</option>
							<option value="1" @if($data->showroom_visit == 1) selected @endif>Yes</option>
							<option value="2" @if($data->showroom_visit == 2) selected @endif>No</option>
						</select>
					</div>
				</div>
			</div>
			<div class="row">
				<div class=" form-group col-sm-12" style="overflow: auto !important">
					<table cellspacing="0" border="0" class="table table-bordered">
						<thead>
							<tr>
								<th>Category</th>
								<th>Company</th>
								<th>Product</th>
							</tr>
						</thead>
						<tbody>
							@if(!empty($clone))
							@foreach($clone as $values)
							<tr class="master_clones" data-id="{{$values->id}}" data-master="{{$values->id}}">
								<td>
									<input type="text" class="form-control" value="{{ (($values->categories) ? ($values->categories->name) : '') }}" readonly>
								</td>
								<td width="30%">
									<input type="text" class="form-control" value="{{ (($values->companys) ? ($values->companys->name) : '') }}" readonly>
								</td>
								<td width="30%">
									<input type="text" class="form-control" value="{{ (($values->products) ? ($values->products->product) : '') }}" readonly>
								</td>
							</tr>
							@endforeach
							@endif
						</tbody>
					</table>
				</div>
			</div>
		</div>
	</div>
</div>
@endsection