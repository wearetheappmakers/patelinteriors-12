<meta name="csrf-token" content="{{ csrf_token() }}" />
<div class="kt-portlet__body">
	<div class="row">
        @if(Auth::guard('user')->check())
        @if(Auth::user()->role == 1)  
        <div class="form-group col-lg-3">
            <label>Branch  </label>
            <input type="hidden" name="branch_id" id="branch_id" value="{{ $data->branch_id }}">
            <input type="text" class="form-control" placeholder="Branch" value="{{ $data->branch->name }}" readonly>
        </div>                            
        <div class="form-group col-lg-3">
            <label>Employee  </label>
            <input type="hidden" name="employee_id" id="employee_id" value="{{ $data->employee_id }}">
            <input type="text" class="form-control" placeholder="Employee" value="{{ $data->employee->name }}" readonly>
        </div>
        <div class="form-group col-lg-3">
            <label>Client  </label>
            <input type="hidden" name="client_id" id="client_id" value="{{ $data->client_id }}">
            <input type="text" class="form-control" placeholder="Employee" value="{{ $data->clients->member }}" readonly>
        </div>
        @endif
        @endif
        @if(Auth::guard('user')->check())
        @if(Auth::user()->role == 2)
        <div class="form-group col-lg-3"> 
            <label>Employee  </label>
            <input type="hidden" name="employee_id" id="employee_id" value="{{ $data->employee_id }}">
            <input type="text" class="form-control" placeholder="Employee" value="{{ $data->employee->name }}" readonly>
        </div>
        <div class="form-group col-lg-3">
            <label>Client  </label>
            <input type="hidden" name="client_id" id="client_id" value="{{ $data->client_id }}">
            <input type="text" class="form-control" placeholder="Employee" value="{{ $data->clients->member }}" readonly>
        </div>
        @endif
        @endif
        @if(Auth::guard('employee')->check())
        <div class="form-group col-lg-3">
            <label>Client  </label>
            <input type="hidden" name="client_id" id="client_id" value="{{ $data->client_id }}">
            <input type="text" class="form-control" placeholder="Employee" value="{{ $data->clients->member }}" readonly>
        </div>
        @endif
        <div class="form-group col-lg-3">
            <label>Mobile </label>
            <input type="number" class="form-control" placeholder="Mobile" value="{{$data->mobile}}" name="mobile" readonly>
        </div>
        <div class="form-group col-lg-3">
            <label>Purpose </label>
            <input type="text" class="form-control" placeholder="Purpose" value="{{$data->purpose}}" name="purpose" >
        </div>
        @if(Auth::guard('user')->check())
        @if(Auth::user()->role == 1)
        <div class="form-group col-lg-3"> 
            <label>Assign To  </label>
            <select name="employee_ids" class="form-control select2" id="employee_ids">
                <option value="">Select Assign</option>
                @foreach($employee as $sub)
                <option value="{{$sub->id}}" @if($sub->id == $data->assign_to) selected @endif >{{$sub->name}}</option>
                @endforeach
            </select>
        </div>
        @endif
        @if(Auth::user()->role == 2)
        <div class="form-group col-lg-3"> 
            <label>Assign To  </label>
            <select name="employee_ids" class="form-control select2" id="employee_ids">
                <option value="">Select Assign</option>
                @foreach($employee as $sub)
                <option value="{{$sub->id}}" @if($sub->id == $data->assign_to) selected @endif >{{$sub->name}}</option>
                @endforeach
            </select>
        </div>
        @endif
        @endif
        @if(Auth::guard('employee')->check())
        <div class="form-group col-lg-3"> 
            <label>Assign To  </label>
            <select name="employee_ids" class="form-control select2" id="employee_ids">
                <option value="">Select Assign</option>
                @foreach($employee as $sub)
                <option value="{{$sub->id}}" @if($sub->id == $data->assign_to) selected @endif >{{$sub->name}}</option>
                @endforeach
            </select>
        </div>
        @endif
        <div class="form-group col-lg-6">
            <div class="form-group">
                <label><strong>Description</strong></label>
                <textarea class="description form-control" name="description" id="description">{{$data->description}}</textarea>
            </div>
            <script type="text/javascript">
                CKEDITOR.replace('description', {
                    filebrowserUploadMethod: 'form'
                });
            </script>
        </div>
        <!-- <div class="form-group  col-lg-3">
            <label>Is Completed</label>
            <div class="kt-checkbox-inline">
                <label class="kt-checkbox">
                    <input type="checkbox" name="is_completed" value="1" @if($data->is_completed == 1) checked @endif> Is Completed
                    <span></span>
                </label>
            </div>
        </div> -->
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
                $('#employee_id').html(data);
                $('#employee_ids').html(data);
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