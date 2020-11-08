<!-- BEGIN HEADER -->

        <div class="page-header navbar navbar-fixed-top">

            <!-- BEGIN HEADER INNER -->

            <div class="page-header-inner ">

                <!-- BEGIN LOGO -->

                <div class="page-logo">

                    <a href="{{URL::to('/admin')}}">
                        <img src="{{ asset('public\logo.png')}}" alt="logo" class="logo-default" /> 
                    </a>
                    <div class="menu-toggler  sidebar-toggler-Custom">
                        <span></span>
                    </div>

                </div>

                <!-- END LOGO -->

                <a href="javascript:;" class="menu-toggler responsive-toggler" data-toggle="collapse" data-target=".navbar-collapse">

                        <span></span>

                    </a>

                <!-- BEGIN TOP NAVIGATION MENU -->

                <div class="top-menu">

                    <ul class="nav navbar-nav pull-right">

                        <li class="dropdown dropdown-extended dropdown-inbox" id="header_inbox_bar"></li>

                        <li class="dropdown dropdown-extended dropdown-tasks" id="header_task_bar"></li>

                        <li class="menu-dropdown classic-menu-dropdown" style="color: #fff;padding-top: 10px;">
                        </li>
                        <li class="menu-dropdown classic-menu-dropdown" style="color: #fff;padding-top: 10px;">

                        </li>
                        <li class="dropdown dropdown-quick-sidebar-toggler pull-right">
                             <i class="fa fa-sign-out" style="font-size: 20px;cursor: pointer;color: white;margin-top: 20px;" onclick="event.preventDefault(); document.getElementById('logout-form').submit();" title="Sign Out"></i>
                            {{-- <a href="javascript:;" class="dropdown-toggle">

                               

                            </a> --}}

                            <form id="logout-form"  action="{{ URL::to('admin/logout') }}" method="POST">

                                {{ csrf_field() }}

                            </form>

                        </li>
                        <li class="dropdown dropdown-user pull-right">

                            <a href="javascript:;" class="dropdown-toggle" data-toggle="dropdown" data-hover="dropdown" data-close-others="true">

                                <img alt="" class="img-circle" src="../assets/layouts/layout/img/avatar3_small.jpg" />

                                <span class="username username-hide-on-mobile"> Welcome {{Auth::guard('admin')->user()->name}} </span>

                            </a>

                            

                        </li>
                      <li class="dropdown dropdown-user pull-right" id="orderNotification">
                          
                      </li>

                        <!-- END USER LOGIN DROPDOWN -->

                        <!-- BEGIN QUICK SIDEBAR TOGGLER -->

                        <!-- DOC: Apply "dropdown-dark" class after below "dropdown-extended" to change the dropdown styte -->

                        

                        <!-- END QUICK SIDEBAR TOGGLER -->

                    </ul>

                </div>

                <!-- END TOP NAVIGATION MENU -->

            </div>

            <!-- END HEADER INNER -->

        </div>

        <!-- END HEADER -->

        <!-- BEGIN HEADER & CONTENT DIVIDER -->

        <div class="clearfix"> </div>

        <!-- END HEADER & CONTENT DIVIDER -->

<style type="text/css">
    .page-header.navbar .top-menu{
        width: 70%;
    }
    .page-header.navbar .top-menu .navbar-nav{
        width: 100%;
    }
    body > div.page-header.navbar.navbar-fixed-top > div > div.top-menu > ul > li.menu-dropdown.classic-menu-dropdown > span.select2.select2-container.select2-container--bootstrap{
        float: right;
    display: inline;
    width: 180px !important;
    margin-left: 10px;
    margin-right: 10px;
    }
    .page-header.navbar .page-logo .logo-default {
         margin: 0px !important;
    }
    .logo-default {
       margin: 0px 0 !important;
       width: 130px !important; 
    }
</style>