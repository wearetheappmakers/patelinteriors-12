@extends('main')
@section('content')

<div class="kt-container kt-container--fluid kt-grid__item kt-grid__item--fluid">
    <br>

    <div class="kt-portlet kt-portlet--mobile">

        {{-- Header --}}
        <div class="kt-portlet__head">
            <div class="kt-portlet__head-label">
                <span class="kt-portlet__head-icon">
                    <i class="kt-font-brand"></i>
                </span>
                <h3 class="kt-portlet__head-title">View Company policy </h3>
            </div>

            <div class="kt-portlet__head-toolbar">
                <div class="kt-portlet__head-actions">
                    <a href="{{ route('user.company_policy.index') }}" class="btn btn-brand btn-elevate btn-icon-sm">
                        <i class="flaticon2-fast-back"></i> Back
                    </a>
                </div>
            </div>
        </div>
<meta name="csrf-token" content="{{ csrf_token() }}" />

<div class="kt-portlet__foot">
    <div class="row">

        @if(Auth::guard('user')->check() && Auth::user()->role == 1)

        <div class="form-group col-sm-3">
            <label>Branch</label>

            <select class="form-control select2" id="branch_id" name="branch_id" disabled>

            <option value="">Select</option>

            @foreach($branch as $datas)

            <option value="{{ $datas->id }}"
            @if(isset($data) && $data->branch_id == $datas->id) selected @endif>
            {{ $datas->name }}
            </option>

            @endforeach

            </select>

        </div>

        @endif
    </div>

    <div class="row">
        <div class="form-group col-sm-12">
            <label>Policy Description</label>

            <textarea name="policy_description" id="policy_description" class="form-control" readonly>
            {{ $data->policy_description ?? '' }}
            </textarea>

        </div>


    </div>
</div>

<script src="https://cdn.ckeditor.com/4.25.1-lts/standard/ckeditor.js"></script>

<script>

CKEDITOR.replace('policy_description',{
height:200
});

$(document).on('submit','form',function(){

for (instance in CKEDITOR.instances) {
CKEDITOR.instances[instance].updateElement();
}

});

</script>

@endsection