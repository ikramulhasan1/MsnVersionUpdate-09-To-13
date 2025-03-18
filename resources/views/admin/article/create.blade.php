@extends('admin.layouts.master')
@section('title', $title)
@section('content')
<style>
    .tagify__input {
        border: 2px solid #4CAF50;
        border-radius: 8px;
        padding: 10px;
        width: 100%;
    }
</style>
<!-- Start Content-->
<div class="container-fluid">

    <!-- start page title -->
    <!-- Include page breadcrumb -->
    @include('admin.inc.breadcrumb')
    <!-- end page title -->


    <div class="row">
        <div class="col">
            <a href="{{ route($route.'.index') }}" class="btn btn-info">{{ __('dashboard.back') }}</a>
        </div>
        <div class="col">
            <span>Keywords = <span class="text-danger ">[serviceshow, hidden]</span></span>
        </div>
    </div>

    <div class="row">
        <div class="col-12 col-lg-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="header-title">{{ __('dashboard.add') }} {{ $title }}</h4>
                </div>
                <form class="needs-validation" novalidate action="{{ route($route.'.store') }}" method="post" enctype="multipart/form-data">
                    @csrf
                    <div class="card-body">

                        <!-- Form Start -->
                        <div class="form-group">
                            <label for="title">{{ __('dashboard.title') }} <span>*</span></label>
                            <input type="text" class="form-control" name="title" id="title" value="{{ old('title') }}" required>

                            <div class="invalid-feedback">
                                {{ __('dashboard.please_provide') }} {{ __('dashboard.title') }}
                            </div>
                        </div>
                       
                        <div class="row">
                            <div class="form-group col">
                                <label for="category">{{ __('dashboard.category') }} <span>*</span></label>
                                <select class="form-control" name="category" id="category" required>
                                    <option value="">{{ __('dashboard.select') }}</option>
                                    @foreach( $categories as $category )
                                    <option value="{{ $category->id }}" @if(old('category')==$category->id) selected @endif>{{ $category->short_title }}</option>
                                    @endforeach
                                </select>
    
                                <div class="invalid-feedback">
                                    {{ __('dashboard.please_provide') }} {{ __('dashboard.category') }}
                                </div>
                            </div>
                            <div class="form-group col">
                                <label for="short_title">{{ __('dashboard.short_title') }} <span>*</span></label>
                                <input type="text" class="form-control" name="short_title" id="short_title" value="{{ old('short_title') }}" required>
    
                                <div class="invalid-feedback">
                                    {{ __('dashboard.please_provide') }} {{ __('dashboard.short_title') }}
                                </div>
                            </div>
                        </div>


                        <div class="row">
                            <div class="form-group col">
                                <label for="service_id">{{ __('dashboard.select_service_id') }}</label>
                                <select class="wide" name="service_id" id="service_id" data-plugin="customselect">
                                    <option value="">{{ __('dashboard.none') }}</option>
                                    @foreach($services as $service)
                                        <option value="{{ $service->id }}">{{ $service->title }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="form-group col">
                                <label for="placeholder">{{ __('dashboard.placeholder') }} <span>*</span></label>
                                <input type="text" class="form-control" name="placeholder" id="placeholder" value="serviceshow" placeholder="e.g. plan, bundle, etc." required>

                                <div class="invalid-feedback">
                                    {{ __('dashboard.please_provide') }} {{ __('dashboard.placeholder') }}
                                </div>
                            </div>
                        </div>




                        <div class="form-group">
                            <label for="description">{{ __('dashboard.description') }} <span>*</span></label>
                            <textarea class="form-control" name="description" id="editor" rows="8" required>{{ old('description') }}</textarea>

                            <div class="invalid-feedback">
                                {{ __('dashboard.please_provide') }} {{ __('dashboard.description') }}
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="keywords">{{ __('dashboard.meta_keywords') }} <span>*</span></label>
                            <input type="text" class="form-control tagin" data-tagin-separator=" " name="keywords" value="{{ old('keywords') }}" required>

                            <div class="invalid-feedback">
                                {{ __('dashboard.please_provide') }} {{ __('dashboard.meta_keywords') }}
                            </div>
                        </div>

                        
                       

                        <div class="form-group">
                            <label for="image">{{ __('dashboard.thumbnail') }} <span>*</span> <span>{{ __('dashboard.image_size', ['height' => 280, 'width' => 500]) }}</span></label>
                            <input type="file" class="form-control" name="image" id="image" required>

                            <div class="invalid-feedback">
                                {{ __('dashboard.please_provide') }} {{ __('dashboard.thumbnail') }}
                            </div>
                        </div>

                        {{-- <div class="form-group">
                        <label for="video_id">{{ __('dashboard.youtube_video_id') }}</label>
                        <input type="text" class="form-control" name="video_id" id="video_id" value="{{ old('video_id') }}">

                        <div class="invalid-feedback">
                            {{ __('dashboard.please_provide') }} {{ __('dashboard.youtube_video_id') }}
                        </div>
                    </div> --}}
                    <!-- Form End -->

            </div>
            <div class="card-footer">
                <div class="form-group">
                    <button type="submit" class="btn btn-primary">{{ __('dashboard.save') }}</button>
                </div>
            </div>
            </form>
        </div>
    </div><!-- end col-->
</div>
<!-- end row-->


</div> <!-- container -->
<!-- End Content-->
<script>
document.addEventListener("DOMContentLoaded", function () {
        const taginInputs = document.querySelectorAll(".tagin");
        taginInputs.forEach(input => new Tagin(input, {
            separator: ',',
            duplicate: false,      // Prevent duplicate tags in the frontend
            enter: true,           
            maxTags: 100            
        }));
    });





    CKEDITOR.replace('editor', {
        on: {
            instanceReady: function(ev) {
                this.dataProcessor.writer.setRules('strong', {
                    indent: false,
                    breakBeforeOpen: false,
                    breakAfterOpen: false,
                    breakBeforeClose: false,
                    breakAfterClose: false
                });
            }
        },
        coreStyles_bold: {
            element: 'b',
            overrides: 'strong'
        } // Converts <strong> to <b>
    });
    CKEDITOR.replace('editor1', {
        on: {
            instanceReady: function(ev) {
                this.dataProcessor.writer.setRules('strong', {
                    indent: false,
                    breakBeforeOpen: false,
                    breakAfterOpen: false,
                    breakBeforeClose: false,
                    breakAfterClose: false
                });
            }
        },
        coreStyles_bold: {
            element: 'b',
            overrides: 'strong'
        } // Converts <strong> to <b>
    });
</script>
@endsection