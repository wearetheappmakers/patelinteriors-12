
@extends('main')
@section('content')
<div class="kt-container  kt-container--fluid  kt-grid__item kt-grid__item--fluid">
    <br>
    <div class="kt-portlet kt-portlet--mobile">
        <div class="kt-portlet__head">
            <div class="kt-portlet__head-label">
                <span class="kt-portlet__head-icon">
                    <i class="kt-font-brand"></i>
                </span>
                <h3 class="kt-portlet__head-title">
                    View Project Name
                </h3>
            </div>
            <div class="kt-portlet__head-toolbar">
                <div class="kt-portlet__head-wrapper">
                    <div class="kt-portlet__head-actions">                  
                        <a href="{{URL::previous()}}" class="btn btn-brand btn-elevate btn-icon-sm">
                            <i class="flaticon2-fast-back"></i>
                            Back
                        </a>
                    </div>
                </div>
            </div>
        </div>
        <div class="kt-portlet__body">
            <div class="row">
                @if(Auth::guard('user')->check())
                @if(Auth::user()->role == 1)  
                <div class="form-group col-lg-3">
                    <label>Branch  </label>
                    <input  type="text" class="form-control"  value="{{$branchname}}" name="branch_id" readonly>
                </div>
                @endif
                @endif
                <div class="form-group col-lg-2">
                    <label>Name </label>
                    <input type="text" class="form-control" placeholder="Name" value="{{$data->name}}" name="name" readonly>
                </div>     
            </div>
        </div>
    </div>
</div>
@endsection
