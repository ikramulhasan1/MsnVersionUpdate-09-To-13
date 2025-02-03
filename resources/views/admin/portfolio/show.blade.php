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
                    <p><span class="text-highlight">{{ __('dashboard.category') }}:</span> 
                        @foreach($row->categories as $category)
                            <span class="badge badge-primary">{{ $category->title }}</span> 
                        @endforeach
                    </p>
                    <hr/>

                    @if(!empty($row->video_id))
                    <p><span class="text-highlight">{{ __('dashboard.youtube_video') }}:</span></p>
                    <div class="embed-responsive embed-responsive-16by9">
                      <iframe class="embed-responsive-item" src="https://www.youtube.com/embed/{{ $row->video_id }}?rel=0" allowfullscreen></iframe>
                    </div>
                    <br/>
                    @endif

                    @if(is_file('uploads/'.$path.'/'.$row->image_path))
                    <p><span class="text-highlight">{{ __('dashboard.thumbnail') }}:</span></p>
                    <img src="{{ asset('uploads/'.$path.'/'.$row->image_path) }}" class="img-fluid" alt="Portfolio">
                    @endif

                    <hr/>
                    <p><span class="text-highlight">{{ __('dashboard.description') }}:</span> {!! $row->description !!}</p>

                    @if(!empty($row->link))
                    <hr/>
                    <p><span class="badge badge-primary">{{ __('dashboard.web_link') }}:</span> <a href="{{ $row->link }}" target="_blank">{{ $row->link }}</a></p>
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