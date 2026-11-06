@extends('main')
@section('content')

<div class="kt-container kt-container--fluid kt-grid__item kt-grid__item--fluid">
    <br>

    <div class="kt-portlet kt-portlet--mobile">

        <div class="kt-portlet__head">
            <div class="kt-portlet__head-label">
                <h3 class="kt-portlet__head-title">View Company</h3>
            </div>

            <div class="kt-portlet__head-toolbar">
                <a href="{{URL::previous()}}" class="btn btn-brand btn-elevate btn-icon-sm">
                    <i class="flaticon2-fast-back"></i> Back
                </a>
            </div>
        </div>

        <div class="kt-portlet__body">
            <div class="row">

                {{-- ✅ ADMIN --}}
                @if(Auth::guard('user')->check() && Auth::user()->role == 1)

                <div class="form-group col-lg-3">
                    <label>Branch</label>
                    <input type="text" class="form-control" value="{{ $branchname }}" readonly>
                </div>

                <div class="form-group col-sm-3">
                    <label>Category Name</label>
                    <input type="text" class="form-control" value="{{ $categorys }}" readonly>
                </div>

                @endif


                {{-- ✅ BRANCH USER --}}
                @if(Auth::guard('user')->check() && Auth::user()->role != 1)

                <div class="form-group col-sm-3">
                    <label>Category Name</label>
                    <input type="text" class="form-control" value="{{ $categorys }}" readonly>
                </div>

                @endif


                {{-- ✅ HR (EMPLOYEE ROLE 9) --}}
                @if(Auth::guard('employee')->check())

                <div class="form-group col-sm-3">
                    <label>Category Name</label>
                    <input type="text" class="form-control" value="{{ $categorys }}" readonly>
                </div>

                @endif


                {{-- NAME --}}
                <div class="form-group col-sm-3">
                    <label>Name</label>
                    <input type="text" class="form-control" value="{{ $data->name }}" readonly>
                </div>

            </div>
        </div>

    </div>
</div>

@endsection