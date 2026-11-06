<meta name="csrf-token" content="{{ csrf_token() }}" /> 

<div class="kt-portlet__foot">



	<div class="row">

		@if(Auth::guard('user')->check())

		@if(Auth::user()->role == 1)  

		<div class="form-group col-lg-2">

			<label>Branch  </label>

			<select name="branch_id[]" id="branch_id" class="form-control select2" multiple onchange="getbranchcategory(this)">

				<option value="">Select Branch</option>

				@foreach($branch as $sub)

				<option value="{{$sub->id}}">{{$sub->name}}</option>

				@endforeach

			</select>

		</div>

		<div class="form-group col-sm-2">

			<label>Category Name</label>

			<select class="form-control pro_category_id select2" id="pro_category_id"  name="pro_category_id" data-id = "0">

				<option value="">Select</option>

				<!-- @foreach($category as $datas)

				<option value="{{$datas->id}}">{{$datas->name}}</option>

				@endforeach -->

			</select>

		</div>

		@endif

		@endif



		@if(Auth::guard('user')->check() && Auth::user()->role != 1)

<div class="form-group col-sm-2">
    <label>Category Name</label>
    <select class="form-control pro_category_id select2" id="pro_category_id" name="pro_category_id">
        <option value="">Select</option>
        @foreach($category as $datas)
        <option value="{{$datas->id}}">{{$datas->name}}</option>
        @endforeach
    </select>
</div>

@endif

{{-- ✅ HR (Employee role 9) --}}
@if(Auth::guard('employee')->check())

<div class="form-group col-sm-2">
    <label>Category Name</label>
    <select class="form-control pro_category_id select2" id="pro_category_id" name="pro_category_id">
        <option value="">Select</option>
        @foreach($category as $datas)
        <option value="{{$datas->id}}">{{$datas->name}}</option>
        @endforeach
    </select>
</div>

@endif

	



		<div class="form-group col-sm-2">

			<label>Name</label>

			<input type="text" name="name" placeholder="Enter Name" class="form-control" required>

		</div>



	</div>

</div>


<script type="text/javascript">

$(document).ready(function() {
    $('.select2').select2();
});

function getbranchcategory($this){
		var branch_id = $('#branch_id').val();

		if(branch_id)
		{
			$.ajax({
				type:"POST",
				headers: {
					'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
				},
				url:"{{ route('user.getbranchcategory') }}",
				data: {
					'branch_id': branch_id
				},
				success: function(data){
					$('#pro_category_id').html(data);
				}
			});
		}
		else
		{
			$('#pro_category_id').html('');
		}
	};

</script>