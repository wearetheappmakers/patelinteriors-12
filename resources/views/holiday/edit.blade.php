<meta name="csrf-token" content="{{ csrf_token() }}" />

<div class="kt-portlet__foot">
    <div class="row">
        <div class="form-group col-sm-2">
            <label>From Date</label>
            <input type="date" name="from_date" value="{{ $data->from_date }}" class="form-control" required>
        </div>

        <div class="form-group col-sm-2">
            <label>To Date</label>
            <input type="date" name="to_date" value="{{ $data->to_date }}" class="form-control" required>
        </div>

        <div class="form-group col-sm-2">
            <label>Name</label>
            <input type="text" name="name" value="{{ $data->name }}" class="form-control" required>
        </div>

    </div>
</div>

