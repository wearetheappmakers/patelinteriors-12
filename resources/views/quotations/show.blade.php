@extends('main')
@section('content')
<div class="kt-container  kt-container--fluid  kt-grid__item kt-grid__item--fluid">
  <br>
  <div class="kt-portlet">
    <div class="kt-portlet__head">
      <div class="kt-portlet__head-label">
        <h3 class="kt-portlet__head-title">
          {{ $title }}
        </h3>
      </div>
      <div class="kt-portlet__head-toolbar">
        <div class="kt-portlet__head-wrapper">
          <div class="kt-portlet__head-actions">
            <a href="{{ route('user.quotations.index') }}" class="btn btn-brand btn-elevate btn-icon-sm">
              <i class="flaticon2-fast-back"></i>
              Back
            </a>
          </div>
        </div>
      </div>
    </div>
<div class="kt-portlet__body">
  <ul class="nav nav-pills" role="tablist" id="myTab">
    <li class="nav-item">
      <a class="nav-link active" data-toggle="tab" data-id="editquotation" href="#editquotation" >Quotation</a>
    </li>
    
    
    <!-- <li class="nav-item">
      <a class="nav-link" data-toggle="tab" data-id="documents" href="#documents">Documents</a>
    </li> -->
  </ul>
  <hr>
  <div class="tab-content">
    @include('quotations.showquotation')
   
        
  </div>
</div>
  </div>
</div>
@endsection
@push('scripts')
<script type="text/javascript">
</script>
@endpush
	