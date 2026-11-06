<meta name="csrf-token" content="{{ csrf_token() }}" />

<div class="kt-portlet__foot">



	<div class="row">
        @if(Auth::guard('user')->check())
        @if(Auth::user()->role == 1 || Auth::user()->role == 2)

        <div class="form-group col-sm-2">

			<label>Name</label>

			<input type="text" name="name" placeholder="Enter Name"  value="{{ $data->name }}"class="form-control" required>

		</div>

        @endif
        @endif
      
	</div>
</div>

