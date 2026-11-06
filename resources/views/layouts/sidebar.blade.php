    <!-- begin:: Header Topbar -->



    <div class="kt-header__topbar">







<!--begin: User bar -->



<div class="kt-header__topbar-item kt-header__topbar-item--user">



    <div class="kt-header__topbar-wrapper" data-toggle="dropdown" data-offset="10px,0px">



        



        <span class="kt-header__topbar-icon kt-hidden-"><i class="flaticon2-user-outline-symbol"></i></span>



    </div>



    <div class="dropdown-menu dropdown-menu-fit dropdown-menu-right dropdown-menu-anim dropdown-menu-xl">







        <!--begin: Head -->



        <div class="kt-user-card kt-user-card--skin-dark kt-notification-item-padding-x" style="background-image: url(assets/media/misc/bg-3.jpg)">



            <div class="kt-user-card__avatar">



                <!-- <img class="kt-hidden" alt="Pic" src="{{asset('assets/media/users/300_25.jpg')}}" /> -->







                <!--use below badge element instead the user avatar to display username's first letter(remove kt-hidden class to display it) -->



                <!-- <span class="kt-badge kt-badge--lg kt-badge--rounded kt-badge--bold kt-font-success">Admin</span> -->



            </div>



            <div class="kt-user-card__name" style="color: #000000;">



            

               {{Auth::user()->name}}



            </div>



            <div class="kt-user-card__badge">

            @if(Auth::guard('user')->check())

            <form id="logout-form" action="{{  url('/logout') }}" method="POST" >

                            @csrf

                    <button class="btn btn-brand" type="submit"  style="color: white;">Logout</button>

            </form>

            @endif

            @if(Auth::guard('employee')->check())

            <form id="logout-form" action="{{  url('employee/logout') }}" method="POST" >

                            @csrf

                    <button class="btn btn-brand" type="submit"  style="color: white;">Logout</button>

            </form>

            @endif

        </div>



        </div>







        <!--end: Head -->







    



    </div>



</div>







<!--end: User bar -->







</div>



</div>



<div class="kt-content  kt-grid__item kt-grid__item--fluid kt-grid kt-grid--hor" id="kt_content">