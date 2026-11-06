<meta name="csrf-token" content="{{ csrf_token() }}" />
<div class="kt-portlet__body">
	<div class="row">
        @if(Auth::guard('user')->check())
        @if(Auth::user()->role == 1)
        <div class="form-group col-lg-3">
            <label>Branch  </label>
            <select name="branch_id" class="form-control select2" onchange="getemployee(this)" required>
                <option value="">Select Branch</option>
                @foreach($branch as $sub)
                <option value="{{$sub->id}}" >{{$sub->name}}</option>
                @endforeach
            </select>
        </div>
        <div class="form-group col-lg-3">	
            <label>Employee</label>
            <select name="employee_id" class="form-control select2" id="employee_id" required>
                <option value="">Select Employee</option>

            </select>
        </div>
        <div class="form-group col-lg-3">   
            <label>Client</label>
            <select name="client_id" class="form-control select2" onchange="getclientdata(this)" id="client_id" required>
                <option value="">Select Client</option>

            </select>
        </div>
        @endif
        @if(Auth::user()->role == 2)
        <div class="form-group col-lg-3">	
            <label>Employee  </label>
            <select name="employee_id" class="form-control select2" id="employee_id" required>
                <option value="">Select Employee</option>
                @foreach($employee as $sub)
                <option value="{{$sub->id}}" >{{$sub->name}}</option>
                @endforeach
            </select>
        </div>
        <div class="form-group col-lg-3">   
            <label>Client  </label>
            <select name="client_id" class="form-control select2" onchange="getclientdata(this)" id="client_id" required>
                <option value="">Select Client</option>
                @foreach($client as $cl)
                <option value="{{$cl->id}}" >{{$cl->member}}</option>
                @endforeach
            </select>
        </div>
        @endif
        @endif
        @if(Auth::guard('employee')->check())
        <div class="form-group col-lg-3">   
            <label>Client  </label>
            <select name="client_id" class="form-control select2" onchange="getclientdata(this)" id="client_id" required>
                <option value="">Select Client</option>
                @foreach($client as $cl)
                <option value="{{$cl->id}}" >{{$cl->member}}</option>
                @endforeach
            </select>
        </div>
        @endif
        <div class="form-group col-lg-3">
            <label>Mobile </label>
            <input type="number" class="form-control" placeholder="Mobile" name="mobile" id="mobile" readonly>
        </div>
        <div class="form-group col-lg-3">
         <label>Purpose </label>
         <input type="text" class="form-control" placeholder="Purpose" name="purpose" required>
     </div>
     @if(Auth::guard('user')->check())
     @if(Auth::user()->role == 1)
     <div class="form-group col-lg-3"> 
        <label>Assign To  </label>
        <select name="employee_ids" class="form-control select2" id="employee_ids">
            <option value="">Select Assign</option>

        </select>
    </div>
    @endif
    @if(Auth::user()->role == 2)
    <div class="form-group col-lg-3"> 
        <label>Assign To</label>
        <select name="employee_ids" class="form-control select2" id="employee_ids">
            <option value="">Select Assign</option>
            @foreach($employee as $sub)
            <option value="{{$sub->id}}" >{{$sub->name}}</option>
            @endforeach
        </select>
    </div>
    @endif
    @endif
    @if(Auth::guard('employee')->check())
    <div class="form-group col-lg-3"> 
        <label>Assign To</label>
        <select name="employee_ids" class="form-control select2" id="employee_ids">
            <option value="">Select Assign</option>
            @foreach($employee as $sub)
            <option value="{{$sub->id}}" >{{$sub->name}}</option>
            @endforeach
        </select>
    </div>
    @endif
    <div class="form-group col-lg-9">
        <div class="form-group">
            <label><strong>Description</strong></label>
            <textarea class="description form-control" name="description" id="description"></textarea>
        </div>
        <script type="text/javascript">
            CKEDITOR.replace('description', {
                filebrowserUploadMethod: 'form'
            });
        </script>
    </div>
</div>
	<!-- <div class="row">
		<div class="col-lg-3">
			@include('layouts.status_checkbox',array('data' => ""))
		</div>
	</div> -->
</div>
<script type="text/javascript">
    function getemployee($this){
        var branch_id = $($this).val();
        var id = $($this).data('id');

        $('#employee_id').html('<option value="">Select Employee</option>');
        $('#employee_ids').html('<option value="">Select Assign</option>');
        $('#client_id').html('<option value="">Select Client</option>');
        $('#mobile').val('');

        if(branch_id != '')
        {
            showLoader();
            $.ajax({
                type:"POST",
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                url:"{{ route('user.task.getemployee') }}",
                data: {
                    'branch_id': branch_id
                },
                success: function(data){
                    hideLoader();
                    $('#employee_id').html(data.data);
                    $('#employee_ids').html(data.data);
                    $('#client_id').html(data.data1);
                }
            });
        }
    };
    function getclientdata($this){
        var client_id = $($this).val();
        var id = $($this).data('id');
        $.ajax({
            type:"POST",
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            url:"{{ route('user.task.getclientdata') }}",
            data: {
                'client_id': client_id
            },
            success: function(data){
                $('#mobile').val(data.data);
            }
        });
    };
</script>
<script type="text/javascript">
    $(document).ready(function() {
        $('.description').ckeditor();
    });
    $(".submit").click(function (event) {
        event.preventDefault();
        for ( instance in CKEDITOR.instances ) {
            CKEDITOR.instances.description.updateElement();
        }  
    })
</script>