@extends('auth.layouts.master')
@section('title', __('auth.verify'))
@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">{{ __('auth.verify_title') }}</div>

                <div class="card-body">

                    <div class="text-center w-75 m-auto">
                        @if(isset($setting))
                        <a href="{{ route('home') }}">
                            <span><img src="{{ asset('/uploads/setting/'.$setting->logo_path) }}" alt="Logo"></span>
                        </a>
                        @endif
                        <br><br>
                    </div>

                    @if (session('resent'))
                        <div class="alert alert-success" role="alert">
                            {{ __('auth.verify_email_sent') }}
                        </div>
                    @endif

                    {{ __('auth.check_your_email') }}
                    {{ __('auth.not_receive_email') }}, <a href="{{ route('verification.resend') }}">{{ __('auth.send_another_request') }}</a>.
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
