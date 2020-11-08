@extends('layouts.app')

@section('content')

@if(session('error'))
    <div class="alert alert-danger col-md-8 col-md-offset-2">
        {{ session('error') }}
    </div>
@endif
<div class="container">
        <div class="row title-logo">
                <div class="col-md-4 col-md-offset-4 text-center">
                     <img src="{{ asset('public\logo.png')}}" style="max-width:300px;max-height:200px;" class="">
                </div>    
            </div>
    <div class="row">
        <div class="col-md-8 col-md-offset-2">
            <div class="panel panel-default">
                <div class="panel-heading text-center">Administration: Reset Password</div>
                <div class="panel-body">
                    @if (session('status'))
                        <div class="alert alert-success">
                            {{ session('status') }}
                        </div>
                    @endif

                    <form class="form-horizontal" role="form" method="POST" action="{{ url('admin/password/email') }}">
                        {{ csrf_field() }}

                        <div class="form-group{{ $errors->has('email') ? ' has-error' : '' }}">
                            <label for="email" class="col-md-4 control-label">E-Mail Address</label>

                            <div class="col-md-6">
                                <input id="email" type="email" class="form-control" name="email" value="{{ old('email') }}" required>

                                @if ($errors->has('email'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('email') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>

                        <div class="form-group">
                            <div class="col-md-6 col-md-offset-4">
                                <button type="submit" class="btn btn-success">
                                    Send OTP
                                </button>
                                <a  class="btn btn-warning" href="{{ url('admin/login') }}">
                                        Back To Login
                                </a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
