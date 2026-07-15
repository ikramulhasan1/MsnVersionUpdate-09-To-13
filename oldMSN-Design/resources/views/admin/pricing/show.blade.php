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
                    <h4><span class="text-highlight">{{ __('dashboard.title') }}:</span> {{ $row->title }}</h4>
                    <hr/>
                    <p><span class="text-highlight">{{ __('dashboard.price') }}:</span> {{ $row->price }} 
                        @if(!empty($row->old_price))
                            / <del>{{ $row->old_price }}</del>
                        @endif
                    </p>
                    <p><span class="text-highlight">{{ __('dashboard.duration') }}:</span> {{ $row->duration }}</p>

                    <hr/>
                    <h6><span class="text-highlight">{{ __('dashboard.features') }}:</span></h6>
                    @php
                        $features = json_decode($row->description, true);
                    @endphp

                    @if(isset($features))
                    @foreach($features as $key => $feature)
                    <span>{{ $key+1 }}. {{ $feature }}</span><br/>
                    @endforeach
                    @endif

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