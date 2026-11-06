@php
$permission = App\Group::where('id',Auth::user()->group_id)->first();
$per_exploded = explode(',',$permission->complain);
@endphp
@if($per_exploded[2] == 1)

<meta name="csrf-token" content="{{ csrf_token() }}" />
<div class="kt-portlet__body">
	<div class="row">
		  @if(Auth::guard('user')->check())
		 @if(Auth::user()->role == 1)  
         <div class="form-group col-lg-3">
            <label>Branch  </label>
            <select name="branch_id" class="form-control select2" onchange="getemployee(this)" required >
                <option value="">Select Branch</option>
                @foreach($branch as $sub)
                <option value="{{$sub->id}}" @if($sub->id == $data->branch_id) selected @endif>{{$sub->name}}</option>
                @endforeach
            </select>
        </div>
        @endif
               
                <?php
            $employee = \App\Employee::where('branch_id',$data->branch_id)->get();
          ?>                          
          <div class="form-group col-lg-3">
            <label>Employee  </label>
            <select name="employee_id" id="employee_id" class="form-control select2" >
                <option value="">Select Employee</option>
                @foreach($employee as $sub)
                <option value="{{$sub->id}}" @if($sub->id == $data->employee_id) selected @endif>{{$sub->name}}</option>
                @endforeach
            </select>
        </div>
       
        @endif
		<div class="form-group col-lg-3">
			<label>Name </label>
			<input type="text" class="form-control" placeholder=" Name" value="{{$data->name}}" name="name" required>
		</div>

		<div class="form-group col-lg-3">
			<label>Mobile No. </label>
			<input type="number" class="form-control" placeholder=" Mobile No." value="{{$data->mobile_no}}" name="mobile_no" >
		</div>

		<div class="form-group col-lg-3">
			<label>Company </label>
			<input type="text" class="form-control" placeholder=" Company" value="{{$data->company}}" name="company" >
		</div>

		<div class="form-group col-lg-3">
			<label>Reference No </label>
			<input type="text" class="form-control" placeholder="Reference No" value="{{$data->reference_no}}" name="reference_no" >
		</div>

		<div class="form-group col-lg-3">
			<label>Image </label>
			<input type="file" class="form-control" placeholder="" value="{{$data->complain}}" name="image" >
			  @if($data->image)
               <img src="{{ asset('/complain/'.$data->image) }}" height="70px" width="70px">

               @endif
		</div>

		<div class="form-group col-lg-3">
			<label>Address </label>
			<textarea type="text" class="form-control" placeholder=" Address" name="address">{{$data->address}}</textarea>  
		</div>

		<div class="form-group col-lg-3">
			<label>Problem </label>
			<textarea type="text" class="form-control" placeholder="Problem" name="problem">{{$data->problem}}</textarea>  
		</div>

		

		

		<div class="form-group col-lg-3">
			<label>Note </label>
			<textarea type="text" class="form-control" placeholder="" name="note">{{$data->note}}</textarea>  
		</div>

		
	</div>
	<!-- <div class="row">
		<div class="col-lg-3">
			@include('layouts.status_checkbox',array('data' => ""))
		</div>

	</div> -->
</div>
@endif
<script type="text/javascript">
	 function getemployee($this){

      var branch_id = $($this).val();
      var id = $($this).data('id');
      $.ajax({
        type:"POST",
        headers: {
          'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        url:"{{ route('user.complains.getemployee') }}",
        data: {
          'branch_id': branch_id
        },
        success: function(data){
          $('#employee_id').html(data);
        }
      });

    };

     $('.select2').select2({
 
});
</script>
