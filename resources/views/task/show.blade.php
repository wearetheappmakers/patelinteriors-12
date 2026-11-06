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
					View Task
				</h3>
			</div>
			<div class="kt-portlet__head-toolbar">
				<div class="kt-portlet__head-wrapper">
					<div class="kt-portlet__head-actions">					
						<a href="{{ route('user.task.index') }}" class="btn btn-brand btn-elevate btn-icon-sm">
							<i class="flaticon2-fast-back"></i>
							Back
						</a>
					</div>
				</div>
			</div>
		</div>
		<div class="kt-portlet__body">
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
					<label>Client  </label>
					<input type="text" class="form-control" placeholder="Employee" value="{{ $data->clients->member }}" readonly>
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
					<label>Client  </label>
					<input type="text" class="form-control" placeholder="Employee" value="{{ $data->clients->member }}" readonly>
				</div>
				@endif
				@endif
				@if(Auth::guard('employee')->check())
				<div class="form-group col-lg-3">
					<label>Client  </label>
					<input type="text" class="form-control" placeholder="Employee" value="{{ $data->clients->member }}" readonly>
				</div>
				@endif
				<div class="form-group col-lg-3">
					<label>Mobile </label>
					<input type="number" class="form-control" placeholder="Mobile" value="{{$data->mobile}}" name="mobile" readonly>
				</div>
				<div class="form-group col-lg-3">
					<label>Purpose </label>
					<input type="text" class="form-control" placeholder="Purpose" value="{{$data->purpose}}" name="purpose" readonly>
				</div>
				@if(Auth::guard('user')->check())
				@if(Auth::user()->role == 1)
				<div class="form-group col-lg-3"> 
					<label>Assign To  </label>
					<input type="text" class="form-control" placeholder="Employee" value="{{ $data->assignto->name }}" readonly>
				</div>
				@endif
				@if(Auth::user()->role == 2)
				<div class="form-group col-lg-3"> 
					<label>Assign To  </label>
					<input type="text" class="form-control" placeholder="Employee" value="{{ $data->assignto->name }}" readonly>
				</div>
				@endif
				@endif
				@if(Auth::guard('employee')->check())
				<div class="form-group col-lg-3"> 
					<label>Assign To  </label>
					<input type="text" class="form-control" placeholder="Employee" value="{{ $data->assignto->name }}" readonly>
				</div>
				@endif
				<div class="form-group col-lg-6">
					<div class="form-group">
						<label><strong>Description</strong></label>
						<textarea class="description form-control" name="description" id="description" disabled>{{$data->description}}</textarea>
					</div>
					<script type="text/javascript">
						CKEDITOR.replace('description', {
							filebrowserUploadMethod: 'form'
						});
					</script>
				</div>
			</div>			
		</div>	
	</div>
</div>
@endsection