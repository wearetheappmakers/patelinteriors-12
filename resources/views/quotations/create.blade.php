@extends('main')
@section('content')
<?php
if($resourcePath === 'quotations')
{
    if(Auth::guard('user')->check())
    {
        if(Auth::user()->role == 2)
        {
            $check = App\Quotation::where('id',$data->id)->first();
            if(isset($check) && !(Auth::user()->id == $check->branch_id))
            {
                dd('Access Denied');
            }
        }
    }else{
        $check = App\Quotation::where('id',$data->id)->first();
        if(isset($check) && !(Auth::user()->id == $check->employee_id))
        {
            dd('Access Denied');
        }
    }
}
?>
<div class="kt-container  kt-container--fluid  kt-grid__item kt-grid__item--fluid">
    <br>
    <div class="kt-portlet">
        <div class="kt-portlet__head">
            <div class="kt-portlet__head-label">
                <h3 class="kt-portlet__head-title">
                    {{ $title }}
                </h3>
            </div>
        </div>
        <div class="kt-portlet__body">
            <ul class="nav nav-pills" role="tablist" id="myTab">
                <li class="nav-item">
                    <a class="nav-link active" data-toggle="tab" data-id="quotation" href="#quotation" >Quotation</a>
                </li>
            </ul>
            <hr>
            <div class="tab-content">
                @include('quotations.quotation')
            </div>
        </div>
    </div>
</div>
@endsection
@push('scripts')
<script type="text/javascript">

</script>
@endpush
