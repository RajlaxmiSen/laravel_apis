<!DOCTYPE html>
<html lang="en">
    <head>

        <meta charset="utf-8" />
        <meta name="_token" content="{{ csrf_token() }}"/>
        <meta name="csrf-token" content="{{ csrf_token() }}"/>
        <title>Gracefful @yield('web_title') </title>
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta content="width=device-width, initial-scale=1" name="viewport" />
        <meta content="" name="description" />
        <meta content="" name="author" />

        <link href="{{asset('public/assets/global/plugins/font-awesome/css/font-awesome.min.css')}}" rel="stylesheet" type="text/css" />
        <link href="{{asset('public/assets/global/plugins/simple-line-icons/simple-line-icons.min.css')}}" rel="stylesheet" type="text/css" />
        <link href="{{asset('public/assets/global/plugins/bootstrap/css/bootstrap.min.css')}}" rel="stylesheet" type="text/css" />
        <link href="{{asset('public/assets/global/plugins/bootstrap-daterangepicker/daterangepicker.min.css')}}" rel="stylesheet" type="text/css" />
        <link href="{{asset('public/assets/global/plugins/morris/morris.css')}}" rel="stylesheet" type="text/css" />
        <link href="{{asset('public/assets/global/plugins/fullcalendar/fullcalendar.min.css')}}" rel="stylesheet" type="text/css" />
        <link href="{{asset('public/assets/global/plugins/jqvmap/jqvmap/jqvmap.css')}}" rel="stylesheet" type="text/css" />
        <link href="{{asset('public/assets/global/css/components.min.css')}}" rel="stylesheet" id="style_components" type="text/css" />
        <link href="{{asset('public/assets/global/css/plugins.min.css')}}" rel="stylesheet" type="text/css" />
        <link href="{{asset('public/assets/layouts/layout/css/layout.min.css')}}" rel="stylesheet" type="text/css" />
        <link href="{{asset('public/assets/layouts/layout/css/themes/default.min.css')}}" rel="stylesheet" type="text/css" id="style_color" />
        <link href="{{asset('public/assets/layouts/layout/css/custom.min.css')}}" rel="stylesheet" type="text/css" />
        <link href="{{asset('public/assets/global/plugins/datatables/datatables.min.css')}}" rel="stylesheet" type="text/css" />
        <link href="{{asset('public/assets/global/plugins/datatables/plugins/bootstrap/datatables.bootstrap.css')}}" rel="stylesheet" type="text/css" />
        <link href="{{asset('public/assets/global/plugins/bootstrap-datepicker/css/bootstrap-datepicker3.min.css')}}" rel="stylesheet" type="text/css" />
        <link href="{{asset('public/assets/global/plugins/bootstrap-timepicker/css/bootstrap-timepicker.min.css')}}" rel="stylesheet" type="text/css" />
        <link href="{{asset('public/assets/global/plugins/bootstrap-datetimepicker/css/bootstrap-datetimepicker.min.css')}}" rel="stylesheet" type="text/css" />
        <link rel="stylesheet" type="text/css" href="{{asset('public/css/custom_admin.css')}}">
        {{-- <link rel="stylesheet" type="text/css" href="{{asset('public/css/pos_style.css')}}"> --}}
        <link rel="stylesheet" type="text/css" href="{{asset('public/assets/global/plugins/bootstrap-toastr/toastr.min.css')}}">
        <link href="{{asset('public/assets/global/plugins/select2/css/select2.min.css')}}" rel="stylesheet" type="text/css" />
        <link href="{{asset('public/assets/global/plugins/select2/css/select2-bootstrap.min.css')}}" rel="stylesheet" type="text/css" />
        <link href="{{asset('public/assets/global/plugins/icheck/skins/all.css')}}" rel="stylesheet" type="text/css" />
        <link href="{{asset('public/assets/global/plugins/fancybox/source/jquery.fancybox.css')}}" rel="stylesheet" type="text/css" />
        <link href="{{asset('public/assets/global/plugins/clockpicker/standalone.css')}}" rel="stylesheet" type="text/css" />
        <link href="{{asset('public/assets/global/plugins/clockpicker/bootstrap-clockpicker.min.css')}}" rel="stylesheet" type="text/css" />
        <link href="https://cdn.datatables.net/1.10.16/css/dataTables.jqueryui.min.css" rel="stylesheet" type="text/css" />
        <link href="{{asset('public/assets/global/plugins/multiselect/fSelect.css')}}" rel="stylesheet" type="text/css" />
        <link href="{{asset('public/assets/global/plugins/monthYearPicker/jquery-ui.min.css')}}" rel="stylesheet" type="text/css" />
        <link href="{{asset('public/assets/global/plugins/monthYearPicker/MonthPicker.css')}}" rel="stylesheet" type="text/css" />
        <link href="{{asset('public/assets/global/plugins/livesearch/jquery.ui.autocomplete.css')}}" rel="stylesheet">
       {{--  <link href="{{asset('public/assets/global/plugins/jquery-ui/jquery-ui.css')}}" rel="stylesheet"> --}}
        <script src="{{asset('public/assets/global/plugins/simple-line-icons/icons-lte-ie7.js')}}" type="text/javascript"></script>
        <script src="//js.pusher.com/3.0/pusher.min.js"></script>
        <script src="{{ URL::asset('public/js/app.js') }}" charset="utf-8"></script>
        <script src="{{asset('public/assets/global/plugins/jquery.min.js')}}" type="text/javascript"></script>
        <script src="{{asset('public/assets/global/plugins/monthYearPicker/jquery-ui.min.js')}}"></script>       
        <script src="{{asset('public/assets/global/plugins/bootstrap/js/bootstrap.min.js')}}" type="text/javascript"></script>
        <script src="{{asset('public/assets/global/plugins/js.cookie.min.js')}}" type="text/javascript"></script>
        <script src="{{asset('public/assets/global/plugins/moment.min.js')}}" type="text/javascript"></script>
        <script src="{{asset('public/assets/global/plugins/bootstrap-daterangepicker/daterangepicker.min.js')}}" type="text/javascript"></script>
        <script src="{{asset('public/assets/global/plugins/morris/morris.min.js')}}" type="text/javascript"></script>
        <script src="{{asset('public/assets/global/plugins/morris/raphael-min.js')}}" type="text/javascript"></script>
        <script src="{{asset('public/assets/global/scripts/app.min.js')}}" type="text/javascript"></script>
        <script src="{{asset('public/assets/layouts/layout/scripts/layout.min.js')}}" type="text/javascript"></script>
        <script src="{{asset('public/assets/global/plugins/datatables/datatables.min.js')}}" type="text/javascript"></script>
        <script src="{{asset('public/assets/global/plugins/datatables/plugins/bootstrap/datatables.bootstrap.js')}}" type="text/javascript"></script>
        <script src="{{asset('public/assets/global/plugins/bootstrap-datepicker/js/bootstrap-datepicker.min.js')}}" type="text/javascript"></script>
        <script src="{{asset('public/assets/global/plugins/bootstrap-timepicker/js/bootstrap-timepicker.min.js')}}" type="text/javascript"></script>
        <script src="{{asset('public/assets/global/plugins/bootstrap-datetimepicker/js/bootstrap-datetimepicker.min.js')}}" type="text/javascript"></script>
        <script src="{{asset('public/assets/global/plugins/bootbox.min.js')}}" type="text/javascript"></script>
        <script src="{{asset('public/assets/global/plugins/bootstrap-toastr/toastr.min.js')}}" type="text/javascript"></script>
        <script src="{{asset('public/assets/pages/scripts/components-date-time-pickers.min.js')}}" type="text/javascript"></script>
        <script src="{{asset('public/js/custom_admin.js')}}"></script>
        <script src="{{asset('public/js/ajax-loading.js')}}"></script>
        <script src="{{asset('public/assets/global/plugins/select2/js/select2.min.js')}}" type="text/javascript"></script>
        <script src="{{asset('public/assets/global/plugins/icheck/icheck.min.js')}}" type="text/javascript"></script>
        <script src="{{asset('public/assets/global/plugins/fancybox/source/jquery.fancybox.js')}}" type="text/javascript"></script>
        <script src="{{asset('public/assets/global/plugins/clockpicker/bootstrap-clockpicker.min.js')}}" type="text/javascript"></script>
        <script src="{{asset('public/assets/global/plugins/multiselect/fSelect.js')}}" type="text/javascript"></script>        
        <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
        <script src="{{asset('public/assets/global/plugins/monthYearPicker/MonthPicker.js')}}"></script>
        {{-- <script src="{{asset('public/assets/global/plugins/jquery-ui/jquery-ui.js')}}"></script> --}}

        <link href="{{asset('public/assets/global/plugins/bootstrap-timepicker/css/bootstrap-timepicker.min.css')}}" rel="stylesheet" type="text/css" />
        <link href="{{asset('public/assets/global/plugins/bootstrap-datetimepicker/css/bootstrap-datetimepicker.min.css')}}" rel="stylesheet" type="text/css" />
        
        <script src="{{asset('public/assets/global/plugins/bootstrap-timepicker/js/bootstrap-timepicker.min.js')}}" type="text/javascript"></script>
        <script src="{{asset('public/assets/global/plugins/bootstrap-datetimepicker/js/bootstrap-datetimepicker.min.js')}}" type="text/javascript"></script>
    </head>
    <body class="page-header-fixed  page-content-white page-md page-sidebar-closed">