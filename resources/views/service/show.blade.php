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
					View Service
				</h3>
			</div>
			<div class="kt-portlet__head-toolbar">
				<div class="kt-portlet__head-wrapper">
					<div class="kt-portlet__head-actions">					
						<a href="{{ route('user.service.index') }}" class="btn btn-brand btn-elevate btn-icon-sm">
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
					<div class="form-group col-lg-3">
						<label>Branch  </label>
						<input type="text" class="form-control" placeholder="Branch" value="{{ $data->branch->name }}" readonly>
					</div>                 
					<div class="form-group col-lg-3">
						<label>Employee  </label>
						<input type="text" class="form-control" placeholder="Employee" value="{{ $data->employee->name }}" readonly>
					</div>    
					<div class="form-group col-lg-3">
						<label>Client </label>
						<input type="text" class="form-control" placeholder="Client" value="{{ $data->client->member }}" readonly>
					</div>
					<div class="form-group col-lg-3">
						<label>Consent Person </label>
						<input type="text" class="form-control" placeholder="Consent Person" value="{{ (($data->consentperson) ? ($data->consentperson->name) : '') }}" readonly>
					</div>
					@endif
					@endif
					@if(Auth::guard('user')->check())
					@if(Auth::user()->role == 2)
					<div class="form-group col-lg-3">
						<label>Employee  </label>
						<input type="text" class="form-control" placeholder="Employee" value="{{ $data->employee->name }}" readonly>
					</div>
					<div class="form-group col-lg-3">
						<label>Client </label>
						<input type="text" class="form-control" placeholder="Client" value="{{ $data->client->member }}" readonly>
					</div>
					<div class="form-group col-lg-3">
						<label>Consent Person </label>
						<input type="text" class="form-control" placeholder="Consent Person" value="{{ (($data->consentperson) ? ($data->consentperson->name) : '') }}" readonly>
					</div>
					@endif
					@endif
					@if(Auth::guard('employee')->check())
					<div class="form-group col-lg-3">
						<label>Client </label>
						<input type="text" class="form-control" placeholder="Client" value="{{ $data->client->member }}" readonly>
					</div>
					<div class="form-group col-lg-3">
						<label>Consent Person </label>
						<input type="text" class="form-control" placeholder="Consent Person" value="{{ (($data->consentperson) ? ($data->consentperson->name) : '') }}" readonly>
					</div>
					@endif
					<div class="form-group col-lg-3">
						<label>Phone No.</label>
						<input type="number" class="form-control" placeholder="Enter Phone No." value="{{$data->phone_no}}" id="phone_no"  name="phone_no" readonly>
					</div>
					<div class="form-group col-lg-3">
						<label>Address</label>
						<input type="text" class="form-control" placeholder="Enter Address" id="address" value="{{$data->address}}"  name="address" readonly >
					</div>
					<div class="form-group col-lg-3">
						<label>Complain No.</label>
						<input type="text" class="form-control" placeholder="Enter Complain No." value="{{$data->complain_no}}"  name="complain_no" readonly>
					</div>
					<div class="form-group col-lg-3">
						<label>Register Date</label>
						<input type="date" class="form-control" placeholder="" value="{{$data->register_date}}" name="register_date" readonly>
					</div>
					<div class="form-group col-lg-3">
						<label>Remarks</label>
						<input id="remarks" name="remark" rows="1" placeholder="Enter Remarks" value="{{$data->remark}}" class="form-control" readonly> 
					</div>
					<div class="form-group col-lg-3">
						<label>Complete Date</label>
						<input type="date" class="form-control" placeholder="" value="{{$data->completed_date}}"  name="completed_date" readonly>
					</div>
					<div class="form-group col-lg-3">
						<label>Image</label>
					</div>
					@php $datas=App\ServiceImage::where('service_id',$data->id)->get(); @endphp
					<div>
						@foreach($datas as $key => $value)
						<input type="hidden" name="hidden_id[]" value="{{$value->id}}">
						<a href="{{asset('service/'.$value->image)}}" target="_blank" download="">
							<i class="fas fa-download" style="padding: 1vh;font-size: 20px;color: black;"></i>
						</a>
						@endforeach
					</div>
					<div class="form-group col-lg-3">
						<label>Type</label>
						<select name="type" disabled class="form-control">
							<option value="">Select</option>
							<option value="1" @if($data->type == 1) selected @endif >Complain</option>
							<option value="2" @if($data->type == 2) selected @endif >Demostration</option>
							<option value="3" @if($data->type == 3) selected @endif >Pre Delivery Inspection</option>
							<option value="4" @if($data->type == 4) selected @endif >Installation Guideline</option>
							<option value="5" @if($data->type == 5) selected @endif >After Installation Guideline</option>
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
								<th>Technician</th>
								<th>Product</th>
								<th>Qty</th>
							</tr>
						</thead>
						<tbody>
							@if(isset($child))
							@foreach($child as $keys => $values)
							<tr class="master_clones" data-id="{{$values->id}}" data-master="{{$values->id}}">
								<td>
									<input type="text" class="form-control" @if(isset($values->category)) value="{{ $values->category->name }}" @endif readonly>
								</td>
								<td>
									<input type="text" class="form-control" @if(isset($values->company)) value="{{ $values->company->name }}" @endif readonly>
								</td>
								<td>
									<input type="text" class="form-control" @if(isset($values->technician)) value="{{ $values->technician->technician_name }}" @endif readonly>
								</td>
								<td>
									<input type="text" class="form-control" @if(isset($values->product)) value="{{ $values->product->dropdown_name }}" @endif readonly>
								</td>
								<td>
									<input type="number" name="edit_qty[]" class="form-control edit_qty" value="{{$values->qty}}" id="edit_qty_{{$values->id}}" data-id="{{$values->id}}" readonly>
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