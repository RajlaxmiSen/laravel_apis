<div class="page-sidebar-wrapper">
      <div class="page-sidebar navbar-collapse collapse">
        <ul class="page-sidebar-menu page-sidebar-menu-closed page-header-fixed " data-auto-scroll="true" data-keep-expanded="false" data-slide-speed="200" style="padding-top: 20px">
            

          
            <li class="nav-item start {{ request()->is('admin/feeds')?'active open':'' }}">
                <a class="nav-link nav-toggle" href="{{ URL::to('/admin/feeds')}}">
                    <i class="fa fa-home" style="font-size: 20px;">
                    </i>
                    <span class="title">
                        Feeds
                    </span>
                    <span class="selected">
                    </span>
                  
                </a>
                
            </li>
            <li class="nav-item start {{ request()->is('admin/users')?'active open':'' }}">
                <a class="nav-link nav-toggle" href="{{ URL::to('/admin/users')}}">
                    <i class="fa fa-users">
                    </i>
                    <span class="title">
                        Users
                    </span>
                    <span class="selected">
                    </span>
                  
                </a>
                
            </li> 
            <li class="nav-item start {{ request()->is('admin/photoCompetition')?'active open':'' }}">
                <a class="nav-link nav-toggle" href="{{ URL::to('/admin/photoCompetition')}}">
                    <i class="fa fa-picture-o">
                    </i>
                    <span class="title">
                        Photo Competition
                    </span>
                    <span class="selected">
                    </span>
                  
                </a>
                
            </li> 
            <li class="nav-item start {{ request()->is('admin/results')?'active open':'' }}">
                <a class="nav-link nav-toggle" href="{{ URL::to('/admin/results')}}">
                    <i class="fa fa-newspaper-o">
                    </i>
                    <span class="title">
                        Result
                    </span>
                    <span class="selected">
                    </span>
                  
                </a>
                
            </li> 
            <li class="nav-item start {{ request()->is('admin/reports')?'active open':'' }}">
                <a class="nav-link nav-toggle" href="{{ URL::to('/admin/reports')}}">
                    <i class="fa fa-sticky-note">
                    </i>
                    <span class="title">
                        Report 
                    </span>
                    <span class="selected">
                    </span>
                  
                </a>
                
            </li> 
    </ul>
        <br>
        <br>
        <br>
        <br>
        
        <br>
        <br>
        <br>
        <br>
        <br>
        <br>
        <!-- END SIDEBAR MENU -->
        <!-- END SIDEBAR MENU -->
    </div>
    <!-- END SIDEBAR -->
</div>
