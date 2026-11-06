

<div class="kt-portlet__body">

    <div class="row">

        @if(Auth::guard('user')->check())

        @if(Auth::user()->role == 1)  

        <div class="form-group col-lg-3">

            <label>Branch  </label>

            <select name="branch_id" class="form-control select2" >

                <option value="">Select Branch</option>

                @foreach($branch as $sub)

                <option value="{{$sub->id}}">{{$sub->name}}</option>

                @endforeach

            </select>

        </div>

        @endif

        @endif

        <div class="form-group col-lg-3">

            <label>GST </label>

            <input type="number" class="form-control" placeholder="GST" name="gst" required>

        </div>

    </div>

</div>


