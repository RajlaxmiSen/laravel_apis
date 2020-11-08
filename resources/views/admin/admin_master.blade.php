@include("admin_layout/header")
@include("admin_layout/topmenu")

<div class="page-container">  
        @include("admin_layout/sidebar")
    <div class="page-content-wrapper">
            <div class="page-content">
                    <!-- BEGIN PAGE BAR -->
                    <div class="page-bar">
                            <!-- BEGIN PAGE BREADCRUMBS -->
                        @yield('breadcrumbs')
                            <!-- END PAGE BREADCRUMBS -->
                        <div class="page-toolbar">
                            @yield('page_toolbar')        
                        </div>
                    </div>

                    <!-- END PAGE BAR --> 
                    <!-- BEGIN PAGE TITLE-->
                   {{--  <h3 class="page-title">
                        @yield('page_title')
                    </h3> --}}
                    <!-- END PAGE TITLE-->

                    <div class="row">
                        @yield("container")
                    </div>
            </div>
    </div>
</div>
@include("admin_layout/footer")