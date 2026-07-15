@extends('web.layouts.master')
@section('title', __('navbar.payment_feedback'))

@section('top_meta_tags')
<meta name="description" content="{!! str_limit(strip_tags($setting->description), 160, ' ...') !!}">
@endsection

@section('content')
<style>
    table {
        width: px;
    }

    table,
    table th,
    table td {
        border: solid;
    }

    table th,
    table td {
        border: solid;
    }

    table th>ol>li,
    table td>ul>li,
    table th>ul>li,
    table td>ol>li {
        list-style: initial !important;
        margin-left: 20px;

    }

    .marker {
        background-color: yellow;
    }

    /* </p><table border="1" cellpadding="1" cellspacing="1" style="width:500px">  */

    .description>ul>li {
        margin-left: 30px !important;
        list-style: initial;
        font-size: 16px !important;
    }


    .description>ol>li {
        /* list-style: decimal; */
        margin-left: 30px !important;
        all: revert;
        font-size: 16px !important;
    }

    .description>p>a {
        color: blue;
        font-weight: bold;
        text-decoration: underline;
    }

    .description>p {
        font-size: 18px !important;
    }
</style>
<!--Page Title-->
<section class="page-title">
    <div class="container">
        <div class="inner-container clearfix">
            <div class="title-box">
                <h1>{{ __('navbar.payment_feedback') }}</h1>
            </div>
            <div class="bread-crumb">
                <ul>
                    <li>{{ __('navbar.payment_feedback') }}</li>
                    <li><a href="{{ route('home') }}">{{ __('navbar.home') }}</a></li>
                </ul>
            </div>
        </div>
    </div>
</section>
<!--End Page Title-->

<!-- Contact Section -->
<section class="contact-section">
    <div class="container">
        <div class="row">

            <!-- Form Column -->
            <div class="form-column col-lg-12 col-md-12 col-sm-12">
                <div class="sec-title left description">
                    @php
                    $section_payment = \App\Models\Section::section('payment');
                    @endphp
                    @if(isset($section_payment))
                    <h1>{{ $section_payment->title }}</h1>
                    <div class="text description">{!! $section_payment->description !!}</div>
                    @endif
                    {{-- Success Alert --}}
                    @if(session('success'))
                    <div class="text">{{session('success')}}</div>
                    @endif

                    {{-- Error Alert --}}
                    @if(session('error'))
                    <div class="text">{{session('error')}}</div>
                    @endif
                    <div class="separater"></div>
                </div>
            </div>

        </div>
        <div class="row">
            {{-- Success Alert --}}
            @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{session('success')}}
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            @endif

            {{-- Error Alert --}}
            @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                {{session('error')}}
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            @endif
        </div>
    </div>
</section>
<!--End Contact Section -->

@endsection