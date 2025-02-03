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
            <a href="{{ route($route.'.index') }}" class="btn btn-info">{{ __('dashboard.refresh') }}</a>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="header-title">{{ $title }} {{ __('dashboard.setup') }}</h4>
                </div>
                <div class="card-body">

                  <!-- Form Start -->
                  <form class="needs-validation" novalidate action="{{ route($route.'.store') }}" method="post" enctype="multipart/form-data">
                    @csrf
                    <input name="id" type="hidden" value="{{ (isset($row->id))?$row->id:-1 }}">

                    <div class="form-group">
                        <label for="title">{{ __('dashboard.title') }} <span>*</span></label>
                        <input type="text" class="form-control" name="title" id="title" value="{{ isset($row->title)?$row->title:'' }}" required>

                        <div class="invalid-feedback">
                          {{ __('dashboard.please_provide') }} {{ __('dashboard.title') }}
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="description">{{ __('dashboard.description') }} <span>*</span></label>
                        <textarea class="form-control summernote" name="description" id="description" rows="8" required>{{ isset($row->description)?$row->description:'' }}</textarea>

                        <div class="invalid-feedback">
                          {{ __('dashboard.please_provide') }} {{ __('dashboard.description') }}
                        </div>
                    </div>

                    <div class="row">
                      <div class="col-md-6">
                      <div class="form-group">
                        <label for="mission_title">{{ __('dashboard.mission_title') }}</label>
                        <input type="text" class="form-control" name="mission_title" id="mission_title" value="{{ isset($row->mission_title)?$row->mission_title:'' }}">

                        <div class="invalid-feedback">
                          {{ __('dashboard.please_provide') }} {{ __('dashboard.mission_title') }}
                        </div>
                      </div>

                      <div class="form-group">
                        <label for="mission_desc">{{ __('dashboard.mission_description') }}</label>
                        <textarea class="form-control summernote" name="mission_desc" id="mission_desc" rows="8">{{ isset($row->mission_desc)?$row->mission_desc:'' }}</textarea>

                        <div class="invalid-feedback">
                          {{ __('dashboard.please_provide') }} {{ __('dashboard.mission_description') }}
                        </div>
                      </div>
                      </div>

                      <div class="col-md-6">
                      <div class="form-group">
                        <label for="vision_title">{{ __('dashboard.vision_title') }}</label>
                        <input type="text" class="form-control" name="vision_title" id="vision_title" value="{{ isset($row->vision_title)?$row->vision_title:'' }}">

                        <div class="invalid-feedback">
                          {{ __('dashboard.please_provide') }} {{ __('dashboard.vision_title') }}
                        </div>
                      </div>

                      <div class="form-group">
                        <label for="vision_desc">{{ __('dashboard.vision_description') }}</label>
                        <textarea class="form-control summernote" name="vision_desc" id="vision_desc" rows="8">{{ isset($row->vision_desc)?$row->vision_desc:'' }}</textarea>

                        <div class="invalid-feedback">
                          {{ __('dashboard.please_provide') }} {{ __('dashboard.vision_description') }}
                        </div>
                      </div>
                      </div>
                    </div>

                    <div class="row">
                      {{-- <div class="form-group col-md-6">
                        <label for="image">{{ __('dashboard.thumbnail') }}: <span>{{ __('dashboard.image_size', ['height' => 600, 'width' => 600]) }}</span></label>
                        <input type="file" class="form-control" name="image" id="image">

                        <div class="invalid-feedback">
                          {{ __('dashboard.please_provide') }} {{ __('dashboard.thumbnail') }}
                        </div>

                        @if(isset($row->image_path))
                        <br/>
                        @if(is_file('uploads/'.$path.'/'.$row->image_path))
                        <img src="{{ asset('uploads/'.$path.'/'.$row->image_path) }}" class="img-fluid" alt="Thumb">
                        @endif
                        @endif
                      </div> --}}

                      <div class="form-group col-md-6">
                        <label for="video_id">{{ __('dashboard.youtube_video_id') }}</label>
                        <input type="text" class="form-control" name="video_id" id="video_id" value="{{ isset($row->video_id)?$row->video_id:'' }}">

                        <div class="invalid-feedback">
                          {{ __('dashboard.please_provide') }} {{ __('dashboard.youtube_video_id') }}
                        </div>

                        @if(!empty($row->video_id))
                        <br/>
                        <div class="embed-responsive embed-responsive-16by9">
                          <iframe class="embed-responsive-item" src="https://www.youtube.com/embed/{{ $row->video_id }}?rel=0" allowfullscreen></iframe>
                        </div>
                        @endif
                      </div>
                    </div>

                    <div class="form-group">
                        <label for="status">{{ __('dashboard.select_status') }}</label>
                        <select class="wide" name="status" id="status" data-plugin="customselect">
                            <option value="1" @if(isset($row->status)) @if( $row->status == 1 ) selected @endif @endif>{{ __('dashboard.active') }}</option>
                            <option value="0" @if(isset($row->status)) @if( $row->status == 0 ) selected @endif @endif>{{ __('dashboard.inactive') }}</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <button type="submit" class="btn btn-primary">{{ __('dashboard.update') }}</button>
                    </div>

                  </form>
                  <!-- Form End -->

                </div> <!-- end card body-->
            </div> <!-- end card -->
        </div><!-- end col-->
    </div>
    <!-- end row-->

    
</div> <!-- container -->
<!-- End Content-->

@endsection