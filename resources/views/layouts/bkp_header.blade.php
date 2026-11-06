<!DOCTYPE html>

<html lang="en">
<head>
    <base href="">
    <meta charset="utf-8" />
    <title>Patel Interior</title>
    <meta name="description" content="Latest updates and statistic charts">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Poppins:300,400,500,600,700|Roboto:300,400,500,600,700">
    <link href="{{asset('assets/plugins/custom/datatables/datatables.bundle.css')}}" rel="stylesheet" type="text/css" />
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"
    integrity="sha512-VEd+nq25CkR676O+pLBnDW09R7VQX9Mdiij052gVCp5yVH3jGtH70Ho/UUv4mJDsEdTvqRCFZg0NKGiojGnUCw=="
    crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <link href="{{ asset('/assets/plugins/custom/fullcalendar/fullcalendar.bundle.css') }}" rel="stylesheet" type="text/css" />
    <script src="https://cdn.ckeditor.com/4.14.1/standard/ckeditor.js" type="text/javascript"></script>
    <link href="{{ asset('/assets/plugins/global/plugins.bundle.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('/assets/css/style.bundle.css') }}" rel="stylesheet" type="text/css" />
    <style type="text/css">
        .toast.toast-success {
            background: #2675d1;
        }
        .toast.toast-warning {
            background: red;
        }
        .toast.toast-error {
            background: #808000;
        }
    </style>
    <style type="text/css">
        tbody, thead, th, tr, td {
            font-weight: bold !important;
            color: black!important;
            font-size : 1.3rem !important;

        }
        select {
            font-weight: bold;
            font-weight: 10000% !important;
            font-size : 1.3rem !important;

        }
        .form-group label{
            font-weight: bold;
            font-weight: 10000% !important;
            font-size : 1.3rem !important;

        }
        li, a, button, div, span, p, i, ul, h3{
            font-weight: bold !important;
            font-size: 1.3rem !important;
        }
        .loader-wrapper {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(255, 255, 255, 0.7); /* Semi-transparent background */
            display: flex;
            justify-content: center;
            align-items: center;
            z-index: 9999; /* Ensure it's on top of other elements */
        }
        .loader {
            border: 16px solid #f3f3f3; /* Light grey */
            border-top: 16px solid #3498db; /* Blue */
            border-radius: 50%;
            width: 120px;
            height: 120px;
            animation: spin 2s linear infinite;
        }
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
        .cke_notification_warning{
            display: none;
        }
    </style>
    <script src="{{ asset('/assets/plugins/global/plugins.bundle.js') }}" type="text/javascript"></script>
    <script src="{{ asset('/assets/js/scripts.bundle.js') }}" type="text/javascript"></script>
    <link rel="shortcut icon" href="{{asset('assets/media/logos/pp.png')}}" />
    @stack('styles')
</head>
<body class="kt-quick-panel--right kt-demo-panel--right kt-offcanvas-panel--right kt-header--fixed kt-header-mobile--fixed kt-subheader--enabled kt-subheader--solid kt-aside--enabled kt-aside--fixed kt-aside--minimize kt-page--loading">
    <div class="loader-wrapper">
        <div class="loader"></div>
    </div>
    <script type="text/javascript">
        document.onreadystatechange = function () {
            var state = document.readyState;
            if (state == 'interactive') {

                document.querySelector('.loader-wrapper').style.display = "flex";
            } else if (state == 'complete') {

                document.querySelector('.loader-wrapper').style.display = "none";
            }
        };

        function showLoader()
        {
            $('.loader').show();
            $('.loader-wrapper').show();
        }

        function hideLoader()
        {
            $('.loader').hide();
            $('.loader-wrapper').hide();
        }
    </script>
    <div id="kt_header_mobile" class="kt-header-mobile  kt-header-mobile--fixed ">
        <div class="kt-header-mobile__logo">
            @if(Auth::guard('user')->check())
            @if(Auth::user()->role == 1)
            <a href="{{route('user.dashboard')}}">
                <img alt="Logo" src="{{asset('assets/media/logos/patellogo.jpg')}}" style="height: 30px; width: 50px;" />
            </a>
            @endif
            @if(Auth::user()->role == 2)
            @if(Auth::user()->image)
            <img src="{{ asset('/user/'.Auth::user()->image) }}" style="height: 30px; width: 50px;">
            @endif
            @endif
            @endif
            @if(Auth::guard('employee')->check())
            @if(Auth::user()->branch_id)
            @php $image = App\User::where('id',Auth::user()->branch_id)->value('image'); @endphp
            <img src="{{ asset('/user/'.$image)}}" style="height: 30px; width: 50px;">
            @endif
            @endif
        </div>
        <div class="kt-header-mobile__toolbar">
            <div class="kt-header-mobile__toolbar-toggler kt-header-mobile__toolbar-toggler--left" id="kt_aside_mobile_toggler"><span></span></div>
            <div class="kt-header-mobile__toolbar-toggler" id="kt_header_mobile_toggler"><span></span></div>
            <div class="kt-header-mobile__toolbar-topbar-toggler" id="kt_header_mobile_topbar_toggler"><i class="flaticon-more"></i></div>
        </div>
    </div>
    <div class="kt-grid kt-grid--hor kt-grid--root">
        <div class="kt-grid__item kt-grid__item--fluid kt-grid kt-grid--ver kt-page">
            <button class="kt-aside-close " id="kt_aside_close_btn"><i class="la la-close"></i></button>
            <div class="kt-aside  kt-aside--fixed  kt-grid__item kt-grid kt-grid--desktop kt-grid--hor-desktop" id="kt_aside">
                <div class="kt-aside-menu-wrapper kt-grid__item kt-grid__item--fluid" id="kt_aside_menu_wrapper">
                    <?php $dates = date('Y-m-d') ?>
                    <?php $client_count = App\Client::whereDate('created_at','>=',$dates)->count() ?>
                    <div id="kt_aside_menu" class="kt-aside-menu " data-ktmenu-vertical="1" data-ktmenu-scroll="1" data-ktmenu-dropdown-timeout="500">
                        <ul class="kt-menu__nav ">
                            <li class="kt-menu__item  kt-menu__item--active" aria-haspopup="true"><a href="" class="kt-menu__link "><i class="kt-menu__link-icon flaticon2-protection"></i><span class="kt-menu__link-text">Dashboard</span></a></li>
                            @if(Auth::guard('user')->check())
                            @if(Auth::user()->role == 1 || Auth::user()->role == 2)
                            <li class="kt-menu__item  kt-menu__item--active" aria-haspopup="true"><a href="{{route('user.role.index')}}" class="kt-menu__link "><i class="kt-menu__link-icon fa fa-user-cog"></i><span class="kt-menu__link-text">Role</span></a></li>
                            @endif
                            @endif
                            @if(Auth::guard('employee')->check())
                            @if(Auth::user()->role == 3)
                            <li class="kt-menu__item  kt-menu__item--active" aria-haspopup="true"><a href="{{route('user.client.index')}}" class="kt-menu__link "><i class="kt-menu__link-icon fa fa-user"></i><span class="kt-menu__link-text">Clients</span></a></li>
                            @endif
                            @endif
                            @if(Auth::guard('user')->check())
                            @if(Auth::user()->role == 1)
                            <li class="kt-menu__item  kt-menu__item--active" aria-haspopup="true"><a href="{{route('user.client.index')}}" class="kt-menu__link "><i class="kt-menu__link-icon fa fa-user"></i><span class="kt-menu__link-text">Clients({{$client_count}})</span></a></li>
                            @endif
                            @if(Auth::user()->role == 2)
                            <?php $dates2 = date('Y-m-d') ?>
                            <?php $client_count2 = App\Client::where('branch_id',Auth::user()->id)->whereDate('created_at','>=',$dates2)->count() ?>
                            <li class="kt-menu__item  kt-menu__item--active" aria-haspopup="true"><a href="{{route('user.client.index')}}" class="kt-menu__link "><i class="kt-menu__link-icon fa fa-user"></i><span class="kt-menu__link-text">Clients({{$client_count2}})</span></a></li>
                            @endif
                            <li class="kt-menu__item  kt-menu__item--active" aria-haspopup="true"><a href="{{route('user.employee.index')}}" class="kt-menu__link "><i class="kt-menu__link-icon flaticon-users-1"></i><span class="kt-menu__link-text">Employee</span></a></li>
                            @if(Auth::user()->role == 1)
                            <li class="kt-menu__item  kt-menu__item--active" aria-haspopup="true"><a href="{{route('user.user.index')}}" class="kt-menu__link "><i class="kt-menu__link-icon flaticon-map"></i><span class="kt-menu__link-text">Branch</span></a></li>
                            @endif
                            @endif
                            @if(Auth::guard('user')->check())
                            <li class="kt-menu__item  kt-menu__item--active" aria-haspopup="true"><a href="{{route('user.visits.index')}}" class="kt-menu__link "><i class="kt-menu__link-icon flaticon-paper-plane-1"></i><span class="kt-menu__link-text">Interior Visit</span></a></li>
                            @endif
                            @if(Auth::guard('employee')->check())
                            @if(!(Auth::user()->role == 3 || Auth::user()->role == 4))
                            <li class="kt-menu__item kt-menu__item--active kt-menu__item--submenu kt-menu__item--rel kt-menu__item--open-dropdown" data-ktmenu-submenu-toggle="click" aria-haspopup="true"><a href="javascript:;" class="kt-menu__link kt-menu__toggle"><i class="kt-menu__link-icon flaticon2-list-1"></i><span class="kt-menu__link-text">Report</span><i class="kt-menu__hor-arrow la la-angle-down"></i><i class="kt-menu__ver-arrow la la-angle-right"></i></a>
                                <div class="kt-menu__submenu kt-menu__submenu--classic kt-menu__submenu--left">
                                    <ul class="kt-menu__subnav">
                                        <li class="kt-menu__item  kt-menu__item--active" aria-haspopup="true"><a href="{{route('user.report.index')}}" class="kt-menu__link "><i class="kt-menu__link-bullet kt-menu__link-bullet--dot"><span></span></i><span class="kt-menu__link-text">Lead Report</span></a></li>
                                        <li class="kt-menu__item  kt-menu__item--active" aria-haspopup="true"><a href="{{route('user.followup.report')}}" class="kt-menu__link "><i class="kt-menu__link-bullet kt-menu__link-bullet--dot"><span></span></i><span class="kt-menu__link-text">Followup Report</span></a></li>
                                        <li class="kt-menu__item  kt-menu__item--active" aria-haspopup="true"><a href="{{route('user.todays.followup.report')}}" class="kt-menu__link "><i class="kt-menu__link-bullet kt-menu__link-bullet--dot"><span></span></i><span class="kt-menu__link-text">Todays Followup Report</span></a></li>
                                        <li class="kt-menu__item  kt-menu__item--active" aria-haspopup="true"><a href="{{route('user.yesterday.followup.report')}}" class="kt-menu__link "><i class="kt-menu__link-bullet kt-menu__link-bullet--dot"><span></span></i><span class="kt-menu__link-text">Yesterday Followup Report</span></a></li>

                                    </ul>
                                </div>
                            </li>
                            <li class="kt-menu__item  kt-menu__item--active" aria-haspopup="true"><a href="{{route('user.visits.index')}}" class="kt-menu__link "><i class="kt-menu__link-icon flaticon-paper-plane-1"></i><span class="kt-menu__link-text">Interior Visit</span></a></li>
                            <li class="kt-menu__item  kt-menu__item--active" aria-haspopup="true"><a href="{{route('user.client.index')}}" class="kt-menu__link "><i class="kt-menu__link-icon flaticon-paper-plane-1"></i><span class="kt-menu__link-text">Client</span></a></li>
                            <li class="kt-menu__item  kt-menu__item--active" aria-haspopup="true"><a href="{{route('user.sourcename.index')}}" class="kt-menu__link "><i class="kt-menu__link-icon flaticon-paper-plane-1"></i><span class="kt-menu__link-text">Source Name</span></a></li>
                            <li class="kt-menu__item  kt-menu__item--active" aria-haspopup="true"><a href="{{route('user.leadsource.index')}}" class="kt-menu__link "><i class="kt-menu__link-icon flaticon-paper-plane-1"></i><span class="kt-menu__link-text">Source</span></a></li>
                            <li class="kt-menu__item kt-menu__item--active" aria-haspopup="true"><a href="{{ route('user.landmark.index') }}" class="kt-menu__link "><i class="kt-menu__link-icon flaticon-paper-plane-1"></i><span class="kt-menu__link-text">Landmark</span></a></li>
                            <li class="kt-menu__item kt-menu__item--active" aria-haspopup="true"><a href="{{ route('user.arid.index') }}" class="kt-menu__link "><i class="kt-menu__link-icon flaticon-paper-plane-1"></i><span class="kt-menu__link-text">Ar/ID</span></a></li>
                            <li class="kt-menu__item kt-menu__item--active" aria-haspopup="true"><a href="{{ route('user.technician.index') }}" class="kt-menu__link "><i class="kt-menu__link-icon flaticon-paper-plane-1"></i><span class="kt-menu__link-text">Technician</span></a></li>
                            @endif
                            @if(Auth::user()->role == 6 || Auth::user()->role == 7)
                            <li class="kt-menu__item kt-menu__item--active" aria-haspopup="true"><a href="{{ route('user.employee.index') }}" class="kt-menu__link "><i class="kt-menu__link-icon flaticon-paper-plane-1"></i><span class="kt-menu__link-text">Employee</span></a></li>
                            @endif
                            @endif
                            @if(Auth::guard('user')->check())
                            <li class="kt-menu__item kt-menu__item--active kt-menu__item--submenu kt-menu__item--rel kt-menu__item--open-dropdown" data-ktmenu-submenu-toggle="click" aria-haspopup="true"><a href="javascript:;" class="kt-menu__link kt-menu__toggle"><i class="kt-menu__link-icon flaticon2-list-1"></i><span class="kt-menu__link-text">Report</span><i class="kt-menu__hor-arrow la la-angle-down"></i><i class="kt-menu__ver-arrow la la-angle-right"></i></a>
                                <div class="kt-menu__submenu kt-menu__submenu--classic kt-menu__submenu--left">
                                    <ul class="kt-menu__subnav">
                                        <li class="kt-menu__item  kt-menu__item--active" aria-haspopup="true"><a href="{{route('user.report.index')}}" class="kt-menu__link "><i class="kt-menu__link-bullet kt-menu__link-bullet--dot"><span></span></i><span class="kt-menu__link-text">Lead Report</span></a></li>
                                        <li class="kt-menu__item  kt-menu__item--active" aria-haspopup="true"><a href="{{route('user.followup.report')}}" class="kt-menu__link "><i class="kt-menu__link-bullet kt-menu__link-bullet--dot"><span></span></i><span class="kt-menu__link-text">Followup Report</span></a></li>
                                        <li class="kt-menu__item  kt-menu__item--active" aria-haspopup="true"><a href="{{route('user.todays.followup.report')}}" class="kt-menu__link "><i class="kt-menu__link-bullet kt-menu__link-bullet--dot"><span></span></i><span class="kt-menu__link-text">Todays Followup Report</span></a></li>
                                        <li class="kt-menu__item  kt-menu__item--active" aria-haspopup="true"><a href="{{route('user.yesterday.followup.report')}}" class="kt-menu__link "><i class="kt-menu__link-bullet kt-menu__link-bullet--dot"><span></span></i><span class="kt-menu__link-text">Yesterday Followup Report</span></a></li>
                                        <li class="kt-menu__item  kt-menu__item--active" aria-haspopup="true"><a href="{{route('user.visitreport.index')}}" class="kt-menu__link "><i class="kt-menu__link-bullet kt-menu__link-bullet--dot"><span></span></i><span class="kt-menu__link-text">Interior Visit Report</span></a></li>
                                        <li class="kt-menu__item  kt-menu__item--active" aria-haspopup="true"><a href="{{route('user.client.report')}}" class="kt-menu__link "><i class="kt-menu__link-bullet kt-menu__link-bullet--dot"><span></span></i><span class="kt-menu__link-text">Client Report</span></a></li>
                                        @if(Auth::user()->role == 1 || Auth::user()->role == 2)
                                        <li class="kt-menu__item  kt-menu__item--active" aria-haspopup="true"><a href="{{route('user.salesorder.report')}}" class="kt-menu__link "><i class="kt-menu__link-bullet kt-menu__link-bullet--dot"><span></span></i><span class="kt-menu__link-text">Salesorder Report</span></a></li>
                                        <li class="kt-menu__item  kt-menu__item--active" aria-haspopup="true"><a href="{{route('user.salesorderinvoice.report')}}" class="kt-menu__link "><i class="kt-menu__link-bullet kt-menu__link-bullet--dot"><span></span></i><span class="kt-menu__link-text">Sales Invoice Report</span></a></li>
                                        @endif
                                        <li class="kt-menu__item  kt-menu__item--active" aria-haspopup="true"><a href="{{route('user.purchaseinvoice.report')}}" class="kt-menu__link "><i class="kt-menu__link-bullet kt-menu__link-bullet--dot"><span></span></i><span class="kt-menu__link-text">Purchase Invoice Report</span></a></li>
                                    </ul>
                                </div>
                            </li>
                            @endif
                            @if(Auth::guard('employee')->check() || Auth::guard('user')->check())

                            <li class="kt-menu__item kt-menu__item--active kt-menu__item--submenu kt-menu__item--rel kt-menu__item--open-dropdown" data-ktmenu-submenu-toggle="click" aria-haspopup="true"><a href="javascript:;" class="kt-menu__link kt-menu__toggle"><i class="kt-menu__link-icon flaticon2-list-1"></i><span class="kt-menu__link-text">HR</span><i class="kt-menu__hor-arrow la la-angle-down"></i><i class="kt-menu__ver-arrow la la-angle-right"></i></a>
                                <div class="kt-menu__submenu kt-menu__submenu--classic kt-menu__submenu--left">
                                    <ul class="kt-menu__subnav">
                                        <li class="kt-menu__item  kt-menu__item--active" aria-haspopup="true"><a href="{{route('user.meeting_entry_employee.index')}}" class="kt-menu__link "><i class="kt-menu__link-bullet kt-menu__link-bullet--dot"><span></span></i><span class="kt-menu__link-text">Meeting Entry For Employee</span></a></li>
                                        <li class="kt-menu__item  kt-menu__item--active" aria-haspopup="true"><a href="{{route('user.travel_update.index')}}" class="kt-menu__link "><i class="kt-menu__link-bullet kt-menu__link-bullet--dot"><span></span></i><span class="kt-menu__link-text">Travel Update</span></a></li>
                                        <!-- <li class="kt-menu__item  kt-menu__item--active" aria-haspopup="true"><a href="{{route('user.holiday.index')}}" class="kt-menu__link "><i class="kt-menu__link-bullet kt-menu__link-bullet--dot"><span></span></i><span class="kt-menu__link-text">Holiday</span></a></li> -->
                                       <!--  <li class="kt-menu__item  kt-menu__item--active " aria-haspopup="true"><a href="{{route('user.leave.index')}}" class="kt-menu__link "><i class="kt-menu__link-bullet kt-menu__link-bullet--dot"><span></span></i><span class="kt-menu__link-text">Leave</span></a></li>
                                        @if(Auth::user()->role == 9)
                                        <li class="kt-menu__item  kt-menu__item--active" aria-haspopup="true"><a href="{{route('user.appointment_letter.index')}}" class="kt-menu__link "><i class="kt-menu__link-bullet kt-menu__link-bullet--dot"><span></span></i><span class="kt-menu__link-text">Appointment Letter</span></a></li>
                                        @endif
                                        @php
                                            $expense = \App\Expense::latest()->first();
                                        @endphp

                                        @if($expense)
                                        <li class="kt-menu__item kt-menu__item--active" aria-haspopup="true">
                                            <a href="{{ route('user.expense.edit', $expense->id) }}" class="kt-menu__link">
                                                <i class="kt-menu__link-bullet kt-menu__link-bullet--dot"><span></span></i>
                                                <span class="kt-menu__link-text">Expense</span>
                                            </a>
                                        </li>
                                        @endif -->
                             </div>
                            </li>
                            @endif
                        </ul>
                    </div>
                </div>
            </div>
            <div class="kt-grid__item kt-grid__item--fluid kt-grid kt-grid--hor kt-wrapper" id="kt_wrapper">
                <div id="kt_header" class="kt-header kt-grid kt-grid--ver  kt-header--fixed ">
                    <div class="kt-header__brand kt-grid__item  " id="kt_header_brand" style="background-color: white;">
                        <div class="kt-header__brand-logo">
                            @if(Auth::guard('user')->check())
                            @if(Auth::user()->role == 1 )
                            <a href="{{route('user.dashboard')}}">
                                <img alt="Logo" src="{{asset('assets/media/logos/patellogo.jpg')}}" style=" height: 70px; width: 95px;" />
                            </a>
                            @endif
                            @if(Auth::user()->role == 2)
                            @if(Auth::user()->image)
                            <img src="{{ asset('/user/'.Auth::user()->image) }}"  style=" height: 70px; width: 95px;" />
                            @endif
                            @endif
                            @else
                            <a href="{{route('user.dashboard')}}">

                            </a>
                            @endif
                            @if(Auth::guard('employee')->check())
                            @if(Auth::user()->branch_id)
                            @php $image = App\User::where('id',Auth::user()->branch_id)->value('image'); @endphp
                            <img src="{{ asset('/user/'.$image)}}" style="height: 30px; width: 50px;">
                            @endif
                            @endif
                        </div>
                    </div>

                    <button class="kt-header-menu-wrapper-close" id="kt_header_menu_mobile_close_btn"><i class="la la-close"></i></button>
                    <div class="kt-header-menu-wrapper kt-grid__item kt-grid__item--fluid" id="kt_header_menu_wrapper">
                        <div id="kt_header_menu" class="kt-header-menu kt-header-menu-mobile  kt-header-menu--layout-default ">
                            <ul class="kt-menu__nav ">
                                <li class="kt-menu__item  kt-menu__item--active " aria-haspopup="true"><a href="{{route('user.dashboard')}}" class="kt-menu__link "><span class="kt-menu__link-text">Dashboard</span></a></li>
                                @if(Auth::guard('user')->check())
                                <?php $dates = date('Y-m-d') ?>
                                <?php $lead_count = App\Lead::whereDate('created_at','>=',$dates)->count() ?>
                                <?php $leadassign_count = App\LeadAssign::whereDate('created_at','>=',$dates)->count() ?>
                                <?php $quot_count = App\Quotation::whereDate('created_at','>=',$dates)->count() ?>
                                <?php $task_count = App\Task::whereDate('created_at','>=',$dates)->count() ?>
                                <?php $att_count = App\Attendance::whereDate('created_at','>=',$dates)->count() ?>
                                <?php $leave_count = App\Leave::whereDate('created_at','>=',$dates)->count() ?>
                                @if(Auth::user()->role == 1)
                                <li class="kt-menu__item kt-menu__item--active kt-menu__item--submenu kt-menu__item--rel kt-menu__item--open-dropdown" data-ktmenu-submenu-toggle="click" aria-haspopup="true"><a href="javascript:;" class="kt-menu__link kt-menu__toggle"><span class="kt-menu__link-text">Voucher</span><i class="kt-menu__hor-arrow la la-angle-down"></i><i class="kt-menu__ver-arrow la la-angle-right"></i></a>
                                    <div class="kt-menu__submenu kt-menu__submenu--classic kt-menu__submenu--left">
                                        <ul class="kt-menu__subnav">
                                            <li class="kt-menu__item " aria-haspopup="true"><a href="{{ route('user.purchaseorder.index') }}" class="kt-menu__link "><i class="kt-menu__link-bullet kt-menu__link-bullet--dot"><span></span></i><span class="kt-menu__link-text"> Purchase Order</span></a></li>
                                            <li class="kt-menu__item " aria-haspopup="true"><a href="{{ route('user.purchaseinvoice.index') }}" class="kt-menu__link "><i class="kt-menu__link-bullet kt-menu__link-bullet--dot"><span></span></i><span class="kt-menu__link-text"> Purchase Invoice</span></a></li>
                                            <li class="kt-menu__item " aria-haspopup="true"><a href="{{route('user.salesorder.index')}}" class="kt-menu__link "><i class="kt-menu__link-bullet kt-menu__link-bullet--dot"><span></span></i><span class="kt-menu__link-text"> Sales Order</span></a></li>
                                            <li class="kt-menu__item " aria-haspopup="true"><a href="{{route('user.salesorderinvoice.index')}}" class="kt-menu__link "><i class="kt-menu__link-bullet kt-menu__link-bullet--dot"><span></span></i><span class="kt-menu__link-text"> Sales Invoice</span></a></li>
                                            <li class="kt-menu__item " aria-haspopup="true"><a href="{{route('user.warehouse.inventory')}}" class="kt-menu__link "><i class="kt-menu__link-bullet kt-menu__link-bullet--dot"><span></span></i><span class="kt-menu__link-text"> Inventory</span></a></li>
                                            <li class="kt-menu__item " aria-haspopup="true"><a href="{{route('user.credit.index')}}" class="kt-menu__link "><i class="kt-menu__link-bullet kt-menu__link-bullet--dot"><span></span></i><span class="kt-menu__link-text"> Credit Note</span></a></li>
                                            <li class="kt-menu__item " aria-haspopup="true"><a href="{{route('user.debit.index')}}" class="kt-menu__link "><i class="kt-menu__link-bullet kt-menu__link-bullet--dot"><span></span></i><span class="kt-menu__link-text"> Debit Note</span></a></li>
                                            <li class="kt-menu__item " aria-haspopup="true"><a href="{{route('user.closing.stock')}}" class="kt-menu__link "><i class="kt-menu__link-bullet kt-menu__link-bullet--dot"><span></span></i><span class="kt-menu__link-text"> Closing Stock</span></a></li>
                                        </ul>
                                    </div>
                                </li>
                                <li class="kt-menu__item  kt-menu__item--active " aria-haspopup="true"><a href="{{route('user.lead.index')}}" class="kt-menu__link "><span class="kt-menu__link-text">Leads({{$leadassign_count}})</span></a></li>
                                <li class="kt-menu__item  kt-menu__item--active " aria-haspopup="true"><a href="{{route('user.task.index')}}" class="kt-menu__link "><span class="kt-menu__link-text">Task({{$task_count}})</span></a></li>
                                <li class="kt-menu__item  kt-menu__item--active " aria-haspopup="true"><a href="{{route('user.service.index')}}" class="kt-menu__link "><span class="kt-menu__link-text">Service</span></a></li>
                                <li class="kt-menu__item  kt-menu__item--active " aria-haspopup="true"><a href="{{route('user.quotations.index')}}" class="kt-menu__link "><span class="kt-menu__link-text">Quotation({{$quot_count}})</span></a></li>     
                                <li class="kt-menu__item  kt-menu__item--active " aria-haspopup="true"><a href="{{route('user.attendance.index')}}" class="kt-menu__link "><span class="kt-menu__link-text">Attendance({{$att_count}})</span></a></li>
                                <!-- <li class="kt-menu__item  kt-menu__item--active " aria-haspopup="true"><a href="{{route('user.leave.index')}}" class="kt-menu__link "><span class="kt-menu__link-text">Leave({{$leave_count}})</span></a></li> -->
                                @endif                               
                                @if(Auth::user()->role == 2)
                                <?php $dates2 = date('Y-m-d') ?>
                                <?php $lead_count2 = App\Lead::where('branch_id',Auth::user()->id)->whereDate('created_at','>=',$dates2)->count() ?>
                                <?php $leadassign_count2 = App\LeadAssign::where('branch_id',Auth::user()->id)->whereDate('created_at','>=',$dates2)->count() ?>
                                <?php $quot_count2 = App\Quotation::where('branch_id',Auth::user()->id)->whereDate('created_at','>=',$dates2)->count() ?>
                                <?php $task_count2 = App\Task::where('branch_id',Auth::user()->id)->whereDate('created_at','>=',$dates2)->count() ?>
                                <?php $att_count2 = App\Attendance::where('branch_id',Auth::user()->id)->whereDate('created_at','>=',$dates2)->count() ?>
                                <?php $leave_count2 = App\Leave::where('branch_id',Auth::user()->id)->whereDate('created_at','>=',$dates2)->count() ?>
                                <li class="kt-menu__item kt-menu__item--active kt-menu__item--submenu kt-menu__item--rel kt-menu__item--open-dropdown" data-ktmenu-submenu-toggle="click" aria-haspopup="true"><a href="javascript:;" class="kt-menu__link kt-menu__toggle"><span class="kt-menu__link-text">Voucher</span><i class="kt-menu__hor-arrow la la-angle-down"></i><i class="kt-menu__ver-arrow la la-angle-right"></i></a>
                                    <div class="kt-menu__submenu kt-menu__submenu--classic kt-menu__submenu--left">
                                        <ul class="kt-menu__subnav">
                                            <li class="kt-menu__item " aria-haspopup="true"><a href="{{ route('user.purchaseorder.index') }}" class="kt-menu__link "><i class="kt-menu__link-bullet kt-menu__link-bullet--dot"><span></span></i><span class="kt-menu__link-text"> Purchase Order</span></a></li>
                                            <li class="kt-menu__item " aria-haspopup="true"><a href="{{ route('user.purchaseinvoice.index') }}" class="kt-menu__link "><i class="kt-menu__link-bullet kt-menu__link-bullet--dot"><span></span></i><span class="kt-menu__link-text"> Purchase Invoice</span></a></li>
                                            <li class="kt-menu__item " aria-haspopup="true"><a href="{{route('user.salesorder.index')}}" class="kt-menu__link "><i class="kt-menu__link-bullet kt-menu__link-bullet--dot"><span></span></i><span class="kt-menu__link-text"> Sales Order</span></a></li>
                                            <li class="kt-menu__item " aria-haspopup="true"><a href="{{route('user.salesorderinvoice.index')}}" class="kt-menu__link "><i class="kt-menu__link-bullet kt-menu__link-bullet--dot"><span></span></i><span class="kt-menu__link-text"> Sales Invoice</span></a></li>
                                            <li class="kt-menu__item " aria-haspopup="true"><a href="{{route('user.warehouse.inventory')}}" class="kt-menu__link "><i class="kt-menu__link-bullet kt-menu__link-bullet--dot"><span></span></i><span class="kt-menu__link-text"> Inventory</span></a></li>
                                            <li class="kt-menu__item " aria-haspopup="true"><a href="{{route('user.credit.index')}}" class="kt-menu__link "><i class="kt-menu__link-bullet kt-menu__link-bullet--dot"><span></span></i><span class="kt-menu__link-text"> Credit Note</span></a></li>
                                            <li class="kt-menu__item " aria-haspopup="true"><a href="{{route('user.debit.index')}}" class="kt-menu__link "><i class="kt-menu__link-bullet kt-menu__link-bullet--dot"><span></span></i><span class="kt-menu__link-text"> Debit Note</span></a></li>
                                            <li class="kt-menu__item " aria-haspopup="true"><a href="{{route('user.opening.stock')}}" class="kt-menu__link "><i class="kt-menu__link-bullet kt-menu__link-bullet--dot"><span></span></i><span class="kt-menu__link-text"> Opening Stock</span></a></li>
                                            <li class="kt-menu__item " aria-haspopup="true"><a href="{{route('user.closing.stock')}}" class="kt-menu__link "><i class="kt-menu__link-bullet kt-menu__link-bullet--dot"><span></span></i><span class="kt-menu__link-text"> Closing Stock</span></a></li>
                                        </ul>
                                    </div>
                                </li>
                                <li class="kt-menu__item  kt-menu__item--active " aria-haspopup="true"><a href="{{route('user.lead.index')}}" class="kt-menu__link "><span class="kt-menu__link-text">Leads({{$leadassign_count2}})</span></a></li>
                                <li class="kt-menu__item  kt-menu__item--active " aria-haspopup="true"><a href="{{route('user.task.index')}}" class="kt-menu__link "><span class="kt-menu__link-text">Task({{$task_count2}})</span></a></li>
                                <li class="kt-menu__item  kt-menu__item--active " aria-haspopup="true"><a href="{{route('user.service.index')}}" class="kt-menu__link "><span class="kt-menu__link-text">Service</span></a></li>
                                <li class="kt-menu__item  kt-menu__item--active " aria-haspopup="true"><a href="{{route('user.quotations.index')}}" class="kt-menu__link "><span class="kt-menu__link-text">Quotation({{$quot_count2}})</span></a></li>     
                                <li class="kt-menu__item  kt-menu__item--active " aria-haspopup="true"><a href="{{route('user.attendance.index')}}" class="kt-menu__link "><span class="kt-menu__link-text">Attendance({{$att_count2}})</span></a></li>
                                <!-- <li class="kt-menu__item  kt-menu__item--active " aria-haspopup="true"><a href="{{route('user.leave.index')}}" class="kt-menu__link "><span class="kt-menu__link-text">Leave({{$leave_count2}})</span></a></li> -->
                                @endif  
                                @if(Auth::guard('user')->check())
                                @if(Auth::user()->role == 1 || Auth::user()->role == 2)
                                <li class="kt-menu__item kt-menu__item--active kt-menu__item--submenu kt-menu__item--rel" data-ktmenu-submenu-toggle="click" aria-haspopup="true"><a href="javascript:;" class="kt-menu__link kt-menu__toggle"><span class="kt-menu__link-text">Master </span><i class="kt-menu__hor-arrow la la-angle-down"></i><i class="kt-menu__ver-arrow la la-angle-right"></i></a>
                                    <div class="kt-menu__submenu  kt-menu__submenu--fixed kt-menu__submenu--center" style="width:450px">
                                        <div class="kt-menu__subnav">
                                            <ul class="kt-menu__content">
                                                <li class="kt-menu__item "><br>
                                                    <ul class="kt-menu__inner">
                                                        <li class="kt-menu__item " aria-haspopup="true"><a href="{{ route('user.product.index') }}" class="kt-menu__link "><i class="kt-menu__link-bullet kt-menu__link-bullet--dot"><span></span></i><span class="kt-menu__link-text"> Product</span></a></li>
                                                        <li class="kt-menu__item " aria-haspopup="true"><a href="{{ route('user.productcategory.index') }}" class="kt-menu__link "><i class="kt-menu__link-bullet kt-menu__link-bullet--dot"><span></span></i><span class="kt-menu__link-text">Category</span></a></li>
                                                        <li class="kt-menu__item " aria-haspopup="true"><a href="{{ route('user.company.index') }}" class="kt-menu__link "><i class="kt-menu__link-bullet kt-menu__link-bullet--dot"><span></span></i><span class="kt-menu__link-text">Company</span></a></li>
                                                        <li class="kt-menu__item " aria-haspopup="true"><a href="{{ route('user.leadstage.index') }}" class="kt-menu__link "><i class="kt-menu__link-bullet kt-menu__link-bullet--dot"><span></span></i><span class="kt-menu__link-text">Lead Stage</span></a></li>
                                                        <li class="kt-menu__item " aria-haspopup="true"><a href="{{ route('user.leadsource.index') }}" class="kt-menu__link "><i class="kt-menu__link-bullet kt-menu__link-bullet--dot"><span></span></i><span class="kt-menu__link-text">Lead Source</span></a></li>
                                                        <li class="kt-menu__item " aria-haspopup="true"><a href="{{ route('user.gst.index') }}" class="kt-menu__link "><i class="kt-menu__link-bullet kt-menu__link-bullet--dot"><span></span></i><span class="kt-menu__link-text">GST</span></a></li>
                                                        <li class="kt-menu__item " aria-haspopup="true"><a href="{{ route('user.type.index') }}" class="kt-menu__link "><i class="kt-menu__link-bullet kt-menu__link-bullet--dot"><span></span></i><span class="kt-menu__link-text">Followup Type </span></a></li>
                                                        <li class="kt-menu__item " aria-haspopup="true"><a href="{{ route('user.symbol.index') }}" class="kt-menu__link "><i class="kt-menu__link-bullet kt-menu__link-bullet--dot"><span></span></i><span class="kt-menu__link-text">Symbol </span></a></li>
                                                        <li class="kt-menu__item " aria-haspopup="true"><a href="{{ route('user.pmc.index') }}" class="kt-menu__link "><i class="kt-menu__link-bullet kt-menu__link-bullet--dot"><span></span></i><span class="kt-menu__link-text">PMC </span></a></li>
                                                        <li class="kt-menu__item " aria-haspopup="true"><a href="{{ route('user.technician.index') }}" class="kt-menu__link "><i class="kt-menu__link-bullet kt-menu__link-bullet--dot"><span></span></i><span class="kt-menu__link-text">Technician </span></a></li>
                                                    </ul>
                                                </li>
                                                <li class="kt-menu__item "><br>
                                                    <ul class="kt-menu__inner">
                                                        <li class="kt-menu__item " aria-haspopup="true"><a href="{{ route('user.vendor.index') }}" class="kt-menu__link "><i class="kt-menu__link-bullet kt-menu__link-bullet--dot"><span></span></i><span class="kt-menu__link-text">Vendor </span></a></li>
                                                        <li class="kt-menu__item " aria-haspopup="true"><a href="{{ route('user.sourcename.index') }}" class="kt-menu__link "><i class="kt-menu__link-bullet kt-menu__link-bullet--dot"><span></span></i><span class="kt-menu__link-text">Source Name</span></a></li>
                                                        <li class="kt-menu__item " aria-haspopup="true"><a href="{{ route('user.warehouse.index') }}" class="kt-menu__link "><i class="kt-menu__link-bullet kt-menu__link-bullet--dot"><span></span></i><span class="kt-menu__link-text">Warehouse</span></a></li>
                                                        <li class="kt-menu__item " aria-haspopup="true"><a href="{{ route('user.gallery.index') }}" class="kt-menu__link "><i class="kt-menu__link-bullet kt-menu__link-bullet--dot"><span></span></i><span class="kt-menu__link-text">Gallery</span></a></li>
                                                        <li class="kt-menu__item " aria-haspopup="true"><a href="{{ route('user.brandlogo.index') }}" class="kt-menu__link "><i class="kt-menu__link-bullet kt-menu__link-bullet--dot"><span></span></i><span class="kt-menu__link-text">Brand Logo</span></a></li>
                                                        <li class="kt-menu__item " aria-haspopup="true"><a href="{{ route('user.project_type.index') }}" class="kt-menu__link "><i class="kt-menu__link-bullet kt-menu__link-bullet--dot"><span></span></i><span class="kt-menu__link-text">Project Type</span></a></li>
                                                        <li class="kt-menu__item " aria-haspopup="true"><a href="{{ route('user.project_name.index') }}" class="kt-menu__link "><i class="kt-menu__link-bullet kt-menu__link-bullet--dot"><span></span></i><span class="kt-menu__link-text">Project Name</span></a></li>
                                                        <li class="kt-menu__item " aria-haspopup="true"><a href="{{ route('user.landmark.index') }}" class="kt-menu__link "><i class="kt-menu__link-bullet kt-menu__link-bullet--dot"><span></span></i><span class="kt-menu__link-text">Landmark</span></a></li>
                                                        <li class="kt-menu__item " aria-haspopup="true"><a href="{{ route('user.arid.index') }}" class="kt-menu__link "><i class="kt-menu__link-bullet kt-menu__link-bullet--dot"><span></span></i><span class="kt-menu__link-text">Ar/ID</span></a></li>
                                                        <li class="kt-menu__item " aria-haspopup="true"><a href="{{ route('user.consent_person.index') }}" class="kt-menu__link "><i class="kt-menu__link-bullet kt-menu__link-bullet--dot"><span></span></i><span class="kt-menu__link-text">Consent Person</span></a></li>
                                                    </ul>
                                                </li>
                                            </ul>
                                        </div>
                                    </div>
                                </li>
                                @endif
                                @endif
                                @endif
                                @if(Auth::guard('employee')->check())
                                @if(Auth::user()->role == 4)
                                <li class="kt-menu__item kt-menu__item--active kt-menu__item--submenu kt-menu__item--rel kt-menu__item--open-dropdown" data-ktmenu-submenu-toggle="click" aria-haspopup="true"><a href="javascript:;" class="kt-menu__link kt-menu__toggle"><span class="kt-menu__link-text">Voucher</span><i class="kt-menu__hor-arrow la la-angle-down"></i><i class="kt-menu__ver-arrow la la-angle-right"></i></a>
                                    <div class="kt-menu__submenu kt-menu__submenu--classic kt-menu__submenu--left">
                                        <ul class="kt-menu__subnav">
                                            <li class="kt-menu__item " aria-haspopup="true"><a href="{{ route('user.purchaseorder.index') }}" class="kt-menu__link "><i class="kt-menu__link-bullet kt-menu__link-bullet--dot"><span></span></i><span class="kt-menu__link-text"> Purchase Order</span></a></li>
                                            <li class="kt-menu__item " aria-haspopup="true"><a href="{{ route('user.purchaseinvoice.index') }}" class="kt-menu__link "><i class="kt-menu__link-bullet kt-menu__link-bullet--dot"><span></span></i><span class="kt-menu__link-text"> Purchase Invoice</span></a></li>
                                            <li class="kt-menu__item " aria-haspopup="true"><a href="{{route('user.salesorder.index')}}" class="kt-menu__link "><i class="kt-menu__link-bullet kt-menu__link-bullet--dot"><span></span></i><span class="kt-menu__link-text"> Sales Order</span></a></li>
                                            <li class="kt-menu__item " aria-haspopup="true"><a href="{{route('user.salesorderinvoice.index')}}" class="kt-menu__link "><i class="kt-menu__link-bullet kt-menu__link-bullet--dot"><span></span></i><span class="kt-menu__link-text"> Sales Invoice</span></a></li>
                                            <li class="kt-menu__item " aria-haspopup="true"><a href="{{route('user.warehouse.inventory')}}" class="kt-menu__link "><i class="kt-menu__link-bullet kt-menu__link-bullet--dot"><span></span></i><span class="kt-menu__link-text"> Inventory</span></a></li>
                                            <li class="kt-menu__item " aria-haspopup="true"><a href="{{route('user.credit.index')}}" class="kt-menu__link "><i class="kt-menu__link-bullet kt-menu__link-bullet--dot"><span></span></i><span class="kt-menu__link-text"> Credit Note</span></a></li>
                                            <li class="kt-menu__item " aria-haspopup="true"><a href="{{route('user.debit.index')}}" class="kt-menu__link "><i class="kt-menu__link-bullet kt-menu__link-bullet--dot"><span></span></i><span class="kt-menu__link-text"> Debit Note</span></a></li>
                                            <li class="kt-menu__item " aria-haspopup="true"><a href="{{route('user.closing.stock')}}" class="kt-menu__link "><i class="kt-menu__link-bullet kt-menu__link-bullet--dot"><span></span></i><span class="kt-menu__link-text"> Closing Stock</span></a></li>
                                        </ul>
                                    </div>
                                </li>
                                @endif
                                @if(Auth::user()->role == 1)
                                <?php $dates = date('Y-m-d') ?>
                                <?php $lead_count = App\Lead::where('employee_id',Auth::user()->id)->whereDate('created_at','>=',$dates)->count() ?>
                                <?php $leadassign_count = App\LeadAssign::where('employee_id',Auth::user()->id)->whereDate('created_at','>=',$dates)->count() ?>
                                <?php $quot_count = App\Quotation::where('employee_id',Auth::user()->id)->whereDate('created_at','>=',$dates)->count() ?>
                                <?php $task_count = App\Task::where('assign_to',Auth::user()->id)->whereDate('created_at','>=',$dates)->count() ?>
                                <?php $att_count = App\Attendance::where('employee_id',Auth::user()->id)->whereDate('created_at','>=',$dates)->count() ?>
                                <?php $leave_count = App\Leave::where('employee_id',Auth::user()->id)->whereDate('created_at','>=',$dates)->count() ?>
                                <?php $client_count = App\Client::where('employee_id',Auth::user()->id)->whereDate('created_at','>=',$dates)->count() ?>
                                <li class="kt-menu__item kt-menu__item--active kt-menu__item--submenu kt-menu__item--rel kt-menu__item--open-dropdown" data-ktmenu-submenu-toggle="click" aria-haspopup="true"><a href="javascript:;" class="kt-menu__link kt-menu__toggle"><span class="kt-menu__link-text">Voucher</span><i class="kt-menu__hor-arrow la la-angle-down"></i><i class="kt-menu__ver-arrow la la-angle-right"></i></a>
                                    <div class="kt-menu__submenu kt-menu__submenu--classic kt-menu__submenu--left">
                                        <ul class="kt-menu__subnav">
                                            <li class="kt-menu__item " aria-haspopup="true"><a href="{{route('user.salesorder.index')}}" class="kt-menu__link "><i class="kt-menu__link-bullet kt-menu__link-bullet--dot"><span></span></i><span class="kt-menu__link-text"> Sales Order</span></a></li>
                                            <li class="kt-menu__item " aria-haspopup="true"><a href="{{route('user.salesorderinvoice.index')}}" class="kt-menu__link "><i class="kt-menu__link-bullet kt-menu__link-bullet--dot"><span></span></i><span class="kt-menu__link-text"> Sales Invoice</span></a></li>
                                            <li class="kt-menu__item " aria-haspopup="true"><a href="{{route('user.warehouse.inventory')}}" class="kt-menu__link "><i class="kt-menu__link-bullet kt-menu__link-bullet--dot"><span></span></i><span class="kt-menu__link-text"> Inventory</span></a></li>
                                            <li class="kt-menu__item " aria-haspopup="true"><a href="{{route('user.closing.stock')}}" class="kt-menu__link "><i class="kt-menu__link-bullet kt-menu__link-bullet--dot"><span></span></i><span class="kt-menu__link-text"> Closing Stock</span></a></li>
                                        </ul>
                                    </div>
                                </li>
                                <li class="kt-menu__item  kt-menu__item--active " aria-haspopup="true"><a href="{{route('user.lead.index')}}" class="kt-menu__link "><span class="kt-menu__link-text">Leads({{$leadassign_count}})</span></a></li>
                                <li class="kt-menu__item  kt-menu__item--active " aria-haspopup="true"><a href="{{route('user.quotations.index')}}" class="kt-menu__link "><span class="kt-menu__link-text">Quotation({{$quot_count}})</span></a></li>
                                <li class="kt-menu__item  kt-menu__item--active " aria-haspopup="true"><a href="{{route('user.attendance.index')}}" class="kt-menu__link "><span class="kt-menu__link-text">Attendance({{$att_count}})</span></a></li>
                                <!-- <li class="kt-menu__item  kt-menu__item--active " aria-haspopup="true"><a href="{{route('user.leave.index')}}" class="kt-menu__link "><span class="kt-menu__link-text">Leave({{$leave_count}})</span></a></li>  -->
                                <li class="kt-menu__item  kt-menu__item--active " aria-haspopup="true"><a href="{{route('user.task.index')}}" class="kt-menu__link "><span class="kt-menu__link-text">Task({{$task_count}})</span></a></li>
                                @endif

                                @if(Auth::guard('employee')->check())
                                @if(Auth::user()->role == 5)
                                <li class="kt-menu__item kt-menu__item--active kt-menu__item--submenu kt-menu__item--rel kt-menu__item--open-dropdown" data-ktmenu-submenu-toggle="click" aria-haspopup="true"><a href="javascript:;" class="kt-menu__link kt-menu__toggle"><span class="kt-menu__link-text">Voucher</span><i class="kt-menu__hor-arrow la la-angle-down"></i><i class="kt-menu__ver-arrow la la-angle-right"></i></a>
                                    <div class="kt-menu__submenu kt-menu__submenu--classic kt-menu__submenu--left">
                                        <ul class="kt-menu__subnav">
                                            <li class="kt-menu__item " aria-haspopup="true"><a href="{{ route('user.purchaseorder.index') }}" class="kt-menu__link "><i class="kt-menu__link-bullet kt-menu__link-bullet--dot"><span></span></i><span class="kt-menu__link-text"> Purchase Order</span></a></li>
                                            <li class="kt-menu__item " aria-haspopup="true"><a href="{{ route('user.purchaseinvoice.index') }}" class="kt-menu__link "><i class="kt-menu__link-bullet kt-menu__link-bullet--dot"><span></span></i><span class="kt-menu__link-text"> Purchase Invoice</span></a></li>
                                            <li class="kt-menu__item " aria-haspopup="true"><a href="{{route('user.warehouse.inventory')}}" class="kt-menu__link "><i class="kt-menu__link-bullet kt-menu__link-bullet--dot"><span></span></i><span class="kt-menu__link-text"> Inventory</span></a></li>
                                        </ul>
                                    </div>
                                </li>
                                @endif
                                @endif

                                @if(Auth::user()->role == 2)
                                <?php $dates = date('Y-m-d') ?>
                                <?php $lead_count = App\Lead::where('employee_id',Auth::user()->id)->whereDate('created_at','>=',$dates)->count() ?>
                                <?php $task_count = App\Task::where('employee_id',Auth::user()->id)->whereDate('created_at','>=',$dates)->count() ?>
                                <?php $att_count = App\Attendance::where('employee_id',Auth::user()->id)->whereDate('created_at','>=',$dates)->count() ?>
                                <?php $leave_count = App\Leave::where('employee_id',Auth::user()->id)->whereDate('created_at','>=',$dates)->count() ?>
                                <?php $client_count = App\Client::where('employee_id',Auth::user()->id)->whereDate('created_at','>=',$dates)->count() ?>
                                <li class="kt-menu__item  kt-menu__item--active " aria-haspopup="true"><a href="{{route('user.lead.index')}}" class="kt-menu__link "><span class="kt-menu__link-text">Leads({{$lead_count}})</span></a></li>
                                <li class="kt-menu__item  kt-menu__item--active " aria-haspopup="true"><a href="{{route('user.client.index')}}" class="kt-menu__link "><span class="kt-menu__link-text">Clients({{$client_count}})</span></a></li>
                                <li class="kt-menu__item  kt-menu__item--active " aria-haspopup="true"><a href="{{route('user.attendance.index')}}" class="kt-menu__link "><span class="kt-menu__link-text">Attendance({{$att_count}})</span></a></li>
                                <!-- <li class="kt-menu__item  kt-menu__item--active " aria-haspopup="true"><a href="{{route('user.leave.index')}}" class="kt-menu__link "><span class="kt-menu__link-text">Leave({{$leave_count}})</span></a></li>  -->
                                <li class="kt-menu__item  kt-menu__item--active " aria-haspopup="true"><a href="{{route('user.task.index')}}" class="kt-menu__link "><span class="kt-menu__link-text">Task({{$task_count}})</span></a></li>
                                @endif
                                @if(Auth::user()->role == 3)
                                <?php $dates = date('Y-m-d') ?>
                                <?php $task_count = App\Task::where('employee_id',Auth::user()->id)->whereDate('created_at','>=',$dates)->count() ?>
                                <?php $att_count = App\Attendance::where('employee_id',Auth::user()->id)->whereDate('created_at','>=',$dates)->count() ?>
                                <?php $leave_count = App\Leave::where('employee_id',Auth::user()->id)->whereDate('created_at','>=',$dates)->count() ?>
                                <li class="kt-menu__item  kt-menu__item--active " aria-haspopup="true"><a href="{{route('user.service.index')}}" class="kt-menu__link "><span class="kt-menu__link-text">Service</span></a></li>
                                <li class="kt-menu__item  kt-menu__item--active " aria-haspopup="true"><a href="{{route('user.attendance.index')}}" class="kt-menu__link "><span class="kt-menu__link-text">Attendance({{$att_count}})</span></a></li>
                                <!-- <li class="kt-menu__item  kt-menu__item--active " aria-haspopup="true"><a href="{{route('user.leave.index')}}" class="kt-menu__link "><span class="kt-menu__link-text">Leave({{$leave_count}})</span></a></li>  -->
                                <li class="kt-menu__item  kt-menu__item--active " aria-haspopup="true"><a href="{{route('user.task.index')}}" class="kt-menu__link "><span class="kt-menu__link-text">Task({{$task_count}})</span></a></li>
                                <li class="kt-menu__item  kt-menu__item--active " aria-haspopup="true"><a href="{{route('user.consent_person.index')}}" class="kt-menu__link "><span class="kt-menu__link-text">Consent Person</span></a></li>
                                @endif
                                @endif
                            </ul>
                        </div>
                    </div>
