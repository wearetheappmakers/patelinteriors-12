

@php 



$edit = route('user.'.$route.'.edit',$id);



@endphp

<?php $check = DB::table('salesorder')->where('id',$id)->value('status'); ?>

<?php $visits = DB::table('interior_visit')->where('id',$id)->value('status'); ?>

<?php $pdf = DB::table('pdfs')->where('id',$id)->first() ?>

<?php  $existstable = DB::table('salesorder_invoice')->where('pending_so', $id)->exists(); ?>




@if($route !== 'quotations' && $route !== 'salesorder' && $route !== 'service' )

@if($visits == 0)

<a style="background: white;" href="{{ $edit }}" title="Edit details" class="btn btn-sm btn-clean btn-icon btn-icon-md">

	<i style="color: green;" class="la la-edit"></i>

</a>&nbsp;&nbsp;



@endif

@endif



@if($route === 'service')

<a style="background: white;" href="{{ $edit }}" title="Edit details" class="btn btn-sm btn-clean btn-icon btn-icon-md">

    <i style="color: green;" class="la la-edit"></i>

</a>&nbsp;&nbsp;

@endif

@if($route === 'salesorder')

<a style="background: white;" href="{{ route('user.salesorder.view', $id) }}" title="view" class="btn btn-sm btn-clean btn-icon btn-icon-md"><i style="color: black;" class="flaticon-eye"></i></a>

@endif





@if($route === 'quotations')

@php $lead_id = App\Quotation::where('id',$id)->value('lead_id'); @endphp

<a style="background: white;" href="{{ route('user.quotation.create', $lead_id)}}" title="Quotation" class="btn btn-sm btn-clean btn-icon btn-icon-md"><i style="color: black;" class="la la-edit"></i></a>

<a style="background: white;" href="{{ route('user.quotation.clone.create', $id)}}" title="PDF" class="btn btn-sm btn-clean btn-icon btn-icon-md"><i style="color: black;" class="fas fa-print"></i></a>

{{-- <button style="background: white;" title="PATEL" onclick="getpdf('{{$id}}')" id="pdf_{{$id}}" class="btn btn-sm btn-clean btn-icon btn-icon-md pdf">

    <i style="color: black;" class="fas fa-print"></i>

</button>

<button style="background: white;" title="KUCHE" onclick="getpdf2('{{$id}}')" id="pdf2_{{$id}}" class="btn btn-sm btn-clean btn-icon btn-icon-md pdf2">

    <i style="color: black;" class="fas fa-print"></i>

</button>

<button style="background: white;" title="ELEMENTRA" onclick="getpdf3('{{$id}}')" id="pdf3_{{$id}}" class="btn btn-sm btn-clean btn-icon btn-icon-md pdf3">

    <i style="color: black;" class="fas fa-print"></i>

</button>

<button style="background: white;" title="NOLTE" onclick="getpdf4('{{$id}}')" id="pdf4_{{$id}}" class="btn btn-sm btn-clean btn-icon btn-icon-md pdf4">

    <i style="color: black;" class="fas fa-print"></i>

</button> --}}

<a href="{{ route('user.delivery.index', $id)}}" class="btn btn-sm btn-clean btn-icon btn-icon-md" title="delivery"><i class="flaticon2-shopping-cart-1" style="color:black;font-weight:bold"></i></a>

<a href="{{ route('user.revisedquotation.index', $id)}}" class="btn btn-sm btn-clean btn-icon btn-icon-md" title="Revised Quotation"><i class="flaticon-layer" style="color:black;font-weight:bold"></i></a>

<a href="{{ route('user.delivery.log', $id)}}" class="btn btn-sm btn-clean btn-icon btn-icon-md" title="Delivery Log"><i class="flaticon2-open-box" style="color:black;font-weight:bold"></i></a>





@endif



@if($route === 'pdf')

@if($pdf->pdf_name == 2)

<button style="background: white;" title="KUCHE" onclick="getpdf2('{{$id}}')" id="pdf2_{{$id}}" class="btn btn-sm btn-clean btn-icon btn-icon-md pdf2">

    <i style="color: black;" class="fas fa-print"></i>

</button>

@elseif($pdf->pdf_name == 3)

<button style="background: white;" title="ELEMENTRA" onclick="getpdf3('{{$id}}')" id="pdf3_{{$id}}" class="btn btn-sm btn-clean btn-icon btn-icon-md pdf3">

    <i style="color: black;" class="fas fa-print"></i>

</button>

@else

<button style="background: white;" title="NOLTE" onclick="getpdf4('{{$id}}')" id="pdf4_{{$id}}" class="btn btn-sm btn-clean btn-icon btn-icon-md pdf4">

    <i style="color: black;" class="fas fa-print"></i>

</button>

@endif



@endif



@if(Auth::guard('user')->check())

@if(Auth::user()->role == 1)


@if($visits == 0 && $route !== 'service')
@if($existstable == false && $route == 'salesorder')

<button style="background: white;" title="Delete" data-id="{{$id}}" class="btn btn-sm btn-clean btn-icon btn-icon-md delete-record">

    <i style="color: red;" class="la la-trash">

    </i>

</button>

@endif
@endif

@endif

@endif



@if(Auth::guard('user')->check())

@if(Auth::user()->role == 1)

@if($route === 'service')

<button style="background: white;" title="Delete" data-id="{{$id}}" class="btn btn-sm btn-clean btn-icon btn-icon-md delete-record">

    <i style="color: red;" class="la la-trash">

    </i>

</button>

@endif

@endif

@endif

@if($route === 'lead')

<a style="background: white;" href="{{ $edit }}" title="Edit details" class="btn btn-sm btn-clean btn-icon btn-icon-md">

    <i style="color: green;" class="la la-edit"></i>

</a>&nbsp;&nbsp;

<button style="background: white;" title="Delete" data-id="{{$id}}" class="btn btn-sm btn-clean btn-icon btn-icon-md delete-record">

    <i style="color: red;" class="la la-trash">

    </i>

</button>

@endif

@if(Auth::guard('user')->check())

@if($route === 'lead')



<?php

$flag = 1;

$checkers = App\Quotation::where('lead_id',$id)->first();

if(isset($checkers))

{

    $flag = 0;

}

?>

@if($flag)



<a style="background: white;" href="{{ route('user.quotation.create', $id)}}" title="Quotation" class="btn btn-sm btn-clean btn-icon btn-icon-md"><i style="color: black;" class="flaticon-envelope"></i></a>

@endif

@endif

@endif



@if(Auth::guard('employee')->check())

@if(Auth::user()->role == 1)

@if($route === 'lead')

<?php

$flag = 1;

$checkers = App\Quotation::where('lead_id',$id)->first();

if(isset($checkers))

{

    $flag = 0;

}

?>

@if($flag)

<a style="background: white;" href="{{ route('user.quotation.create', $id)}}" title="Quotation" class="btn btn-sm btn-clean btn-icon btn-icon-md"><i style="color: black;" class="flaticon-envelope"></i></a>

@endif

@endif

@endif

@endif







<!-- <button style="background: white;" title="Delete" data-id="{{$id}}" class="btn btn-sm btn-clean btn-icon btn-icon-md delete-record">

    <i style="color: red;" class="la la-trash">

    </i>

</button> -->





<script type="text/javascript">



    @if($route === 'pdf')

    function getpdf(id,name,invoiceno)

    {

        var parent = $('#pdf_'+id);           



        parent.attr('disabled',true);



        parent.html('<i class="fa fa-spinner fa-spin"></i>');



        $.ajax({

            type: "POST",

            url: "{{ route('user.quotation.pdf') }}",

            data: {

                '_token': $('input[name="_token"]').val(),

                'id': id

            },

            xhrFields: {

                responseType: 'blob'

            },

            success: function(blob) {

                parent.attr('disabled',false);



                parent.html('<i style="color: black;" class="fas fa-print"></i >');



                var link=document.createElement('a');

                link.href=window.URL.createObjectURL(blob);

                link.click();

            }

        });

    }



    function getpdf2(id,name,invoiceno)

    {

        var parent = $('#pdf2_'+id);           



        parent.attr('disabled',true);



        parent.html('<i class="fa fa-spinner fa-spin"></i>');



        $.ajax({

            type: "POST",

            url: "{{ route('user.pdf2.pdf') }}",

            data: {

                '_token': $('input[name="_token"]').val(),

                'id': id

            },

            xhrFields: {

                responseType: 'blob'

            },

            success: function(blob) {

                parent.attr('disabled',false);



                parent.html('<i style="color: black;" class="fas fa-print"></i >');



                var link=document.createElement('a');

                link.href=window.URL.createObjectURL(blob);

                link.download= id+'-Quotation.pdf';

                link.click();

            }

        });

    }





    function getpdf3(id,name,invoiceno)

    {

        var parent = $('#pdf3_'+id);           



        parent.attr('disabled',true);



        parent.html('<i class="fa fa-spinner fa-spin"></i>');



        $.ajax({

            type: "POST",

            url: "{{ route('user.pdf3.pdf') }}",

            data: {

                '_token': $('input[name="_token"]').val(),

                'id': id

            },

            xhrFields: {

                responseType: 'blob'

            },

            success: function(blob) {

                parent.attr('disabled',false);



                parent.html('<i style="color: black;" class="fas fa-print"></i >');



                var link=document.createElement('a');

                link.href=window.URL.createObjectURL(blob);

                link.download= id+'-Quotation.pdf';

                link.click();

            }

        });

    }





    function getpdf4(id,name,invoiceno)

    {

        var parent = $('#pdf4_'+id);           



        parent.attr('disabled',true);



        parent.html('<i class="fa fa-spinner fa-spin"></i>');



        $.ajax({

            type: "POST",

            url: "{{ route('user.pdf4.pdf') }}",

            data: {

                '_token': $('input[name="_token"]').val(),

                'id': id

            },

            xhrFields: {

                responseType: 'blob'

            },

            success: function(blob) {

                parent.attr('disabled',false);



                parent.html('<i style="color: black;" class="fas fa-print"></i >');



                var link=document.createElement('a');

                link.href=window.URL.createObjectURL(blob);

                link.download= id+'-Quotation.pdf';

                link.click();

            }

        });

    }

    @endif

</script>

