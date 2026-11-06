<meta name="csrf-token" content="{{ csrf_token() }}" />

<div class="kt-portlet__foot">



	<div class="row">

		@if(Auth::guard('user')->check())

		@if(Auth::user()->role == 1)  

		<div class="form-group col-lg-2">

			<label>Branch  </label>

			<select name="branch_id" class="form-control select2" onchange="getcategory(this)" >

				<option value="">Select Branch</option>

				@foreach($branch as $sub)

				<option value="{{$sub->id}}">{{$sub->name}}</option>

				@endforeach

			</select>

		</div>

		

		@endif

		@endif



		



		<div class="form-group col-sm-2">

			<label>Name</label>

			<input type="text" name="name" placeholder="Enter Name" class="form-control" required>

		</div>

		<div class="form-group col-sm-2">

			<label>Number</label>

			<input type="number" name="number" placeholder="Enter Number" class="form-control" >

		</div>

		<div class="form-group col-sm-2">

			<label>Email</label>

			<input type="email" name="email" placeholder="Enter Email" class="form-control" >

		</div>
		<div class="form-group col-sm-2">

			<label>Contact Person</label>

			<input type="text" name="contact_person" placeholder="Enter Contact Person" class="form-control" >

		</div>



	</div>

</div>



<script type="text/javascript">

	function getcategory($this){



		var branch_id = $($this).val();

		var id = $($this).data('id');

		$.ajax({

			type:"POST",

			headers: {

				'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')

			},

			url:"{{ route('user.getcategory') }}",

			data: {

				'branch_id': branch_id

			},

			success: function(data){

				$('#pro_category_id').html(data);

			}

		});



	};

</script>