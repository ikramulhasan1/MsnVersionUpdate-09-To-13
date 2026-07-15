@extends('web.layouts.master')
@section('title', __('navbar.error'))
@section('content')

    <!--Page Title-->
    <section class="page-title">
        <div class="container">
            <div class="inner-container clearfix">
                <div class="title-box">
                    <h1>{{ __('navbar.error') }}</h1>
                </div>
                <div class="bread-crumb">
                    <ul>
                        <li>{{ __('navbar.error') }}</li>
                        <li><a href="{{ route('home') }}">{{ __('navbar.home') }}</a></li>
                    </ul>
                </div>
            </div>
        </div>
    </section>
    <!--End Page Title-->

    @php
        $section_error = \App\Models\Section::section('error');
    @endphp
    @if(isset($section_error))
    <!--Error Section-->     
    <section class="error-section">
        <div class="container">
            <div class="row">
                <div class="col-12">
                    <div class="inner-container text-center">
                        <div class="image-box">
                            <figure><img src="{{ asset('web/images/resource/error-img.png') }}" alt="Error"></figure>
                        </div>
                        <div class="text-box">
                            <h1>{{ $section_error->title }}</h1>
                            <div class="text">{!! $section_error->description !!}</div>
                            <a href="{{ route('home') }}" class="thm-btn btn-style-four">{{ __('common.go_home') }}</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!--End Error Section-->
    @endif

@endsection