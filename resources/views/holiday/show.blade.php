
 @extends('main')
@section('content')

<div class="kt-container kt-container--fluid kt-grid__item kt-grid__item--fluid">
    <br>

    <div class="kt-portlet kt-portlet--mobile">
        <div class="kt-portlet__head">
            <div class="kt-portlet__head-label">
                <span class="kt-portlet__head-icon">
                    <i class="kt-font-brand"></i>
                </span>
                <h3 class="kt-portlet__head-title">View Holiday </h3>
            </div>

            <div class="kt-portlet__head-toolbar">
                <div class="kt-portlet__head-actions">
                    <a href="{{ route('user.holiday.index') }}" class="btn btn-brand btn-elevate btn-icon-sm">
                        <i class="flaticon2-fast-back"></i> Back
                    </a>
                </div>
            </div>
        </div>

        
        <meta name="csrf-token" content="{{ csrf_token() }}" />

        <div class="kt-portlet__foot">
            <div class="row">
                <div class="form-group col-sm-2">
                    <label>From Date</label>
                    <input type="date" name="from_date" value="{{ $data->from_date }}" class="form-control" required disabled>
                </div>

                <div class="form-group col-sm-2">
                    <label>To Date</label>
                    <input type="date" name="to_date" value="{{ $data->to_date }}" class="form-control" required disabled>
                </div>

                <div class="form-group col-sm-2">
                    <label>Name</label>
                    <input type="text" name="name" value="{{ $data->name }}" class="form-control" required disabled>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
