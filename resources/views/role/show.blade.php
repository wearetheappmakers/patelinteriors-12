@extends('main')

@section('content')



<div class="kt-content  kt-grid__item kt-grid__item--fluid kt-grid kt-grid--hor" id="kt_content">

    <br>

    <div class="kt-container  kt-container--fluid  kt-grid__item kt-grid__item--fluid">        

        <link href="{{ asset('assets/plugins/custom/datatables/datatables.bundle.css')}}" rel="stylesheet" type="text/css" />

        <div class="kt-portlet kt-portlet--mobile">

            <div class="kt-portlet__head kt-portlet__head--lg">

                <div class="kt-portlet__head-label">

                    <h3 class="kt-portlet__head-title">

                        View Role

                    </h3>

                </div>

                <div class="kt-portlet__head-toolbar">

                    <div class="kt-portlet__head-wrapper">

                        <div class="kt-portlet__head-actions">

                            <a href="{{ route('user.role.index') }}" class="btn btn-brand btn-elevate btn-icon-sm">

                                <i class="flaticon2-fast-back"></i>

                                Back

                            </a>

                        </div>

                    </div>

                </div>

            </div>

            <div class="kt-portlet__body">

                <div id="kt_table_1_wrapper" class="dataTables_wrapper dt-bootstrap4">

                    <div class="row">

                      
                        @if(Auth::guard('user')->check())
			            @if(Auth::user()->role == 1)  
                        <div class="form-group col-sm-3">

                            <label>Name</label>

                            <input type="text" class="form-control" name="name" id="name" value="{{$data->name}}" readonly>

                        </div>
                        @endif
                        @endif     
                    </div>

                </div>

            </div>

            

        </div>

    </div>

</div>

@stop

@push('scripts')