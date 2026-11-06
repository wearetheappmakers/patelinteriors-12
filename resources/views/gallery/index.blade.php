
@extends('main')
@section('content')
<meta name="csrf-token" content="{{ csrf_token() }}" />
<div class="kt-container  kt-container--fluid  kt-grid__item kt-grid__item--fluid">
     <br>
     <div class="kt-portlet kt-portlet--mobile">
          <div class="kt-portlet__head kt-portlet__head--lg">
               <div class="kt-portlet__head-label">
                    <h3 class="kt-portlet__head-title">
                         Gallery
                    </h3>
               </div>
               <div class="kt-portlet__head-toolbar">
                    <div class="kt-portlet__head-wrapper">
                         <div class="kt-portlet__head-actions">                  

                              <form id="userimportform" enctype="multipart/form-data">
                                   @csrf
                    <!-- <a href="{{route('user.gallery_export')}}" class="btn btn-brand btn-elevate btn-icon-sm">
                    <i class="la la-download"></i>
                    Gallery Export
               </a> -->
               <a href="{{route('user.gallery.create')}}" class="btn btn-brand btn-elevate btn-icon-sm">
                    <i class="la la-plus"></i>
                    Add Gallery
               </a>
          </form>

     </div>
</div>
</div>
</div>
<div class="kt-portlet__body">
     <div class="row">
          @if(Auth::user()->id == 1)
          <div class="form-group col-lg-3">
               <label>Branch </label>
               <select class="form-control select2 onchange" id="branch_id" name="branch_id"  onchange="getdata(this)">
                    <option value="">Select Branch</option>
                    @foreach($branch as $key => $value)
                    <option value="{{ $value->id }}">{{ $value->name }}</option>
                    @endforeach
               </select>
          </div>
          <div class="form-group col-lg-3">
               <label>Category </label>
               <select class="form-control select2 onchange" id="category_id" name="category_id" onchange="getcompany(this)" >
                    <option value="">Select Category</option>
                  <!--   @foreach($category as $key => $value)
                    <option value="{{ $value->id }}">{{ $value->name }}</option>
                    @endforeach -->
               </select>
          </div>
          @endif
          @if(Auth::user()->id != 1)
          <div class="form-group col-lg-3">
               <label>Category </label>
               <select class="form-control select2 onchange" id="category_id" name="category_id" onchange="getcompany(this)" >
                    <option value="">Select Category</option>
                    @foreach($category as $key => $value)
                    <option value="{{ $value->id }}">{{ $value->name }}</option>
                    @endforeach
               </select>
          </div>
          @endif

          <div class="form-group col-lg-3">
               <label>Company </label>
               <select class="form-control select2 onchange" id="company_id" name="company_id"  >
                    <option value="">Select Company</option>
                    <!-- @foreach($company as $key => $value)
                    <option value="{{ $value->id }}">{{ $value->name }}</option>
                    @endforeach -->
               </select>
          </div>

          <div class="col-md-3">
               <br>

               <a href="{{ route('user.gallery.index') }}"><button type="button" class="btn btn-brand btn-elevate btn-icon-sm">Refresh</button></a>
          </div>
     </div>
     <div id="kt_table_1_wrapper" class="dataTables_wrapper dt-bootstrap4">
          <table class="table table-striped table-bordered table-hover table-checkable datatable" id="datatable_rows">
               @csrf
               <thead>
                    <tr>
                         <th>Gallery</th>
                         @if(Auth::user()->id == 1)
                         <th>Branch</th>
                         @endif
                         <th>Category</th>
                         <th>Company</th>
                         <th>Product</th>
                         <th>Action</th>
                    </tr>
               </thead>
          </table>
     </div>
</div>
@include('layouts.multiple_action', array(
'table_name' => 'galleries',
'is_orderby'=>'',
'folder_name'=>'',
'action' => array('change-status-1' => _('Active'), 'change-status-0' => _('Inactive'))
))
</div>
</div>  
@stop
@push('scripts')
<script>
     $(document).ready(function() {
          $(".onchange").on('change',function(){
               oTable.draw();
          });
          var oTable = $('#datatable_rows').DataTable({
               processing: true,
               serverSide: true,
               searchable: true,
               scrollX: true,
               ajax: {
                    url:'{{ route("user.gallery.index") }}',
                    data: function(d) {
                         d.branch_id=$('#branch_id').val(),
                         d.category_id=$('#category_id').val(),
                         d.company_id=$('#company_id').val()

                    }
               },

               columns: [
               {
                    orderable: false,
                    searchable: false,
                    data: "image",
               },
               @if(Auth::user()->id == 1)
               {
                    orderable: true,
                    searchable: true,
                    data: "branch_id",
               },
               @endif
               {
                    orderable: true,
                    searchable: true,
                    data: "category_id",
               },
               {
                    orderable: true,
                    searchable: true,
                    data: "company_id",
               },
               {
                orderable: true,
                searchable: true,
                data: "product.product", 
                render: function(data, type, row) {
                 return data ? data : '';
            },
       },
       {
          orderable: false,
          searchable: false,
          'data': 'action',
     },
     ]
});

     });
</script>

<script type="text/javascript">

     function getdata($this){
          var branch_id = $($this).val();
          var id = $($this).data('id');

          $.ajax({
               type:"POST",
               headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
               },
               url:"{{ route('user.report.getdata') }}",
               data: {
                    'branch_id': branch_id
               },
               success: function(data){

                    $('#category_id').html(data.data4);

               }
          });
     };
     function getcompany($this){
          var category_id = $($this).val();
          console.log(category_id);
          var id = $($this).data('id');
          $.ajax({
               type:"POST",
               headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
               },
               url:"{{ route('user.product.getcompany') }}",
               data: {
                    'productcategory_id': category_id
               },
               success: function(data){
                    console.log(data.data);

                    $('#company_id').html(data);
               }
          });
     };

</script>
@endpush

