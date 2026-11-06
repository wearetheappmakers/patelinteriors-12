<meta name="csrf-token" content="{{ csrf_token() }}" />

<div class="kt-portlet__foot">
    <div class="row">

        @php
            $selectedBranches = explode(',', $data->branch_id);
        @endphp

        {{-- ✅ ADMIN --}}
        @if(Auth::guard('user')->check() && Auth::user()->role == 1)

        <div class="form-group col-lg-2">
            <label>Branch</label>
            <select name="branch_id[]" id="branch_id" class="form-control select2" multiple onchange="getbranchcategory()">
                <option value="">Select Branch</option>
                @foreach($branch as $sub)
                    <option value="{{$sub->id}}" 
                        @if(in_array($sub->id, $selectedBranches)) selected @endif>
                        {{$sub->name}}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="form-group col-sm-2">
            <label>Category Name</label>
            <select class="form-control select2" id="pro_category_id" name="pro_category_id">
                <option value="">Select</option>
                @foreach($category as $cat)
                    <option value="{{$cat->id}}" 
                        @if($cat->id == $data->pro_category_id) selected @endif>
                        {{$cat->name}}
                    </option>
                @endforeach
            </select>
        </div>

        @endif


        {{-- ✅ BRANCH USER --}}
        @if(Auth::guard('user')->check() && Auth::user()->role != 1)

        <div class="form-group col-sm-2">
            <label>Category Name</label>
            <select class="form-control select2" id="pro_category_id" name="pro_category_id">
                <option value="">Select</option>
                @foreach($category as $cat)
                    <option value="{{$cat->id}}" 
                        @if($cat->id == $data->pro_category_id) selected @endif>
                        {{$cat->name}}
                    </option>
                @endforeach
            </select>
        </div>

        @endif


        {{-- ✅ HR (EMPLOYEE ROLE 9) --}}
        @if(Auth::guard('employee')->check())

        <div class="form-group col-sm-2">
            <label>Category Name</label>
            <select class="form-control select2" id="pro_category_id" name="pro_category_id">
                <option value="">Select</option>
                @foreach($category as $cat)
                    <option value="{{$cat->id}}" 
                        @if($cat->id == $data->pro_category_id) selected @endif>
                        {{$cat->name}}
                    </option>
                @endforeach
            </select>
        </div>

        @endif


        {{-- NAME --}}
        <div class="form-group col-sm-2">
            <label>Name</label>
            <input type="text" name="name" class="form-control" value="{{$data->name}}" required>
        </div>

    </div>
</div>

<script>
$(document).ready(function() {
    $('.select2').select2();
});

function getbranchcategory(){
    var branch_id = $('#branch_id').val();

    if(branch_id){
        $.ajax({
            type: "POST",
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            url: "{{ route('user.getbranchcategory') }}",
            data: {
                branch_id: branch_id
            },
            success: function(data){
                $('#pro_category_id').html(data);
            }
        });
    } else {
        $('#pro_category_id').html('<option value="">Select</option>');
    }
}
</script>