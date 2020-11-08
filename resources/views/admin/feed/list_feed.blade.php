@extends('admin/admin_master')
@section('page_title')
<h1>Feeds Management
    <small>manage all your feeds here</small>
</h1>
@endsection

@section('breadcrumbs')
<ul class="page-breadcrumb breadcrumb">
    <li>
        <a href="{{URL::to('/admin')}}">Home</a>
    </li>
    <li>
        <a href="#">Feed Management</a>
    </li>
</ul>
@endsection

@section('container')

@if(session('success'))
    <div class="alert alert-success">
        {{session('success')}}
    </div>
@endif

@if(session('error'))
    <div class="alert alert-danger">
        {{session('error')}}
    </div>
@endif

<div class="col-md-12">
    <!-- BEGIN EXAMPLE TABLE PORTLET-->
    <div class="portlet light ">
        <div class="portlet-title">
            <div class="caption font-dark">
                <i class="icon-settings font-dark"></i>
                <span class="caption-subject bold uppercase"> All Feeds</span>
            </div>
            {{-- <div class="actions">             
                <a class="btn btn-primary" href="javascript:void(0);" onclick="addAttendance()"><i class="fa fa-plus"></i> Add Attendance</a>
                @endif
            </div> --}}
        </div>
        <div class="portlet-body">
            <div id='grid_container'>
                @include('admin/feed/feed_grid')
            </div>
        </div>
    </div>
    <!-- END EXAMPLE TABLE PORTLET-->
</div>









@endsection