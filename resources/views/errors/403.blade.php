@extends('admin/admin_master')
@section('page_title')
<h1>403 ERROR</h1>
@endsection
@section('breadcrumbs')
@endsection



@section('container')
<h2>{{ $exception->getMessage() }}</h2>
@endsection

