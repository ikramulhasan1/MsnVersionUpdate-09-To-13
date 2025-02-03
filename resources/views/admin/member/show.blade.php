@extends('admin.layouts.master')
@section('title', $title)
@section('content')

<!-- Start Content-->
<div class="container-fluid">
    
    <!-- start page title -->
    <!-- Include page breadcrumb -->
    @include('admin.inc.breadcrumb')
    <!-- end page title --> 


    <div class="row">
        <div class="col-12">
            <a href="{{ route($route.'.index') }}" class="btn btn-info">{{ __('dashboard.back') }}</a>
        </div>
    </div>

    <div class="row">
        <div class="col-12 col-lg-8">
            <div class="card">
                <div class="card-header">
                    <h4 class="header-title">{{ __('dashboard.view') }} {{ $title }}</h4>
                </div>
                <div class="card-body">

                    <!-- Details View Start -->
                    <h4><span class="text-highlight">{{ __('dashboard.name') }}:</span> {{ $row->title }}</h4>
                    <p><span class="text-highlight">{{ __('dashboard.designation') }}:</span> {{ $row->designation->title }}</p>
                    @if(!empty($row->designation->department))
                    <p><span class="text-highlight">{{ __('dashboard.department') }}:</span> {{ $row->designation->department }}</p>
                    @endif
                    <hr/>

                    @if(is_file('uploads/'.$path.'/'.$row->image_path))
                    <p><span class="text-highlight">{{ __('dashboard.photo') }}:</span></p>
                    <img src="{{ asset('uploads/'.$path.'/'.$row->image_path) }}" class="img-fluid" alt="Profile">
                    @endif

                    <hr/>
                    <p><span class="text-highlight">{{ __('dashboard.description') }}:</span> {!! $row->description !!}</p>

                    <hr/>
                    <p><span class="text-highlight">{{ __('dashboard.phone') }}:</span> {{ $row->phone }}</p>
                    @if(isset($row->whatsapp))
                    <p><span class="text-highlight">{{ __('dashboard.whatsapp') }}:</span> {{ $row->whatsapp }}</p>
                    @endif
                    <p><span class="text-highlight">{{ __('dashboard.email') }}:</span> {{ $row->email }}</p>
                    <hr/>

                    <div class="row icons-list-demo">
                        @if(!empty($row->facebook))
                        <div class="col-4 col-sm-2 col-md-2 col-lg-2">
                            <a href="{{ $row->facebook }}" target="_blank">
                                <i class="fe-facebook"></i>
                            </a>
                        </div>
                        @endif
                        @if(!empty($row->twitter))
                        <div class="col-4 col-sm-2 col-md-2 col-lg-2">
                            <a href="{{ $row->twitter }}" target="_blank">
                                <i class="fe-twitter"></i>
                            </a>
                        </div>
                        @endif
                        @if(!empty($row->instagram))
                        <div class="col-4 col-sm-2 col-md-2 col-lg-2">
                            <a href="{{ $row->instagram }}" target="_blank">
                                <i class="fe-instagram"></i>
                            </a>
                        </div>
                        @endif
                        @if(!empty($row->linkedin))
                        <div class="col-4 col-sm-2 col-md-2 col-lg-2">
                            <a href="{{ $row->linkedin }}" target="_blank">
                                <i class="fe-linkedin"></i>
                            </a>
                        </div>
                        @endif
                        @if(!empty($row->website))
                        <div class="col-4 col-sm-2 col-md-2 col-lg-2">
                            <a href="{{ $row->website }}" target="_blank">
                                <i class="fe-airplay"></i>
                            </a>
                        </div>
                        @endif
                    </div>
                    <hr/>

                    <p><span class="text-highlight">{{ __('dashboard.status') }}:</span> 
                    @if( $row->status == 1 )
                    <span class="badge badge-success badge-pill">{{ __('dashboard.active') }}</span>
                    @else
                    <span class="badge badge-danger badge-pill">{{ __('dashboard.inactive') }}</span>
                    @endif
                    </p>
                    <!-- Details View End -->
                </div>
            </div>
        </div><!-- end col-->
    </div>
    <!-- end row-->

    
</div> <!-- container -->
<!-- End Content-->

@endsection