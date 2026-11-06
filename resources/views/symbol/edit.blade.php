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

				<option value="{{$sub->id}}" @if($sub->id == $data->branch_id) selected @endif>{{$sub->name}}</option>

				@endforeach

			</select>

		</div>

		

		@endif

		@endif



		



		<div class="form-group col-sm-2">

			<label>Name</label>

			<input type="text" name="name" placeholder="Enter Name" value="{{$data->name}}" class="form-control" required>

		</div>

		<div class="form-group col-sm-2">

			<label>Image</label>

			<input type="file" name="image" placeholder="Enter Name" class="form-control" >
			@if($data->image)
			<img src="{{asset('/symbol/'.$data->image)}}" height="50px" width="50px">
			@endif

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