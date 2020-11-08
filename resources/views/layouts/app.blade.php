<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'Gracefful') }}</title>
    <!-- Styles -->
    <link href="{{ asset('public/css/app.css') }}" rel="stylesheet">    
</head>
<style>
    body{
        background-color:#1f1f1f;
    }
    #app{
        margin-top: 10%;
    }
    .title-logo{
        margin-bottom: 10px;
    }
    .panel{
        border-radius: 0px;
    }
</style>
<body >
    <div id="app">
        @yield('content')
    </div>
</body>
</html>
