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
            <span class="font-weight-bolder text-right" >Keywords = <span class="text-danger font-weight-bold text-right">[serviceshow, hidden]</span></span>
        </div>
    </div>

    <div class="row">
        <div class="col-12 col-lg-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="header-title">{{ __('dashboard.edit') }} {{ $title }}</h4>
                </div>
                <form class="needs-validation" novalidate action="{{ route($route.'.update', $row->id) }}" method="post" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    <div class="card-body">

                        <!-- Form Start -->
                        <div class="form-group">
                            <label for="title">{{ __('dashboard.title') }} <span>*</span></label>
                            <input type="text" class="form-control" name="title" id="title" value="{{ $row->title }}" required>

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
                                    <option value="{{ $category->id }}" @if( $category->id == $row->category_id ) selected @endif>{{ $category->title }}</option>
                                    @endforeach
                                </select>
    
                                <div class="invalid-feedback">
                                    {{ __('dashboard.please_provide') }} {{ __('dashboard.category') }}
                                </div>
                            </div>
                            <div class="form-group col">
                                <label for="short_title">{{ __('dashboard.short_title') }} <span>*</span></label>
                                <input type="text" class="form-control" name="short_title" id="short_title" value="{{ $row->short_title }}" required>
    
                                <div class="invalid-feedback">
                                    {{ __('dashboard.please_provide') }} {{ __('dashboard.short_title') }}
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="description">{{ __('dashboard.description') }} <span>*</span></label>
                            <textarea class="form-control" name="description" id="editor" rows="8" required>{{ $row->description }}</textarea>

                            <div class="invalid-feedback">
                                {{ __('dashboard.please_provide') }} {{ __('dashboard.description') }}
                            </div>
                        </div>

                        {{--  --}}
                        <div class="row">
                            <div class="form-group col">
                                <label for="service_id">{{ __('dashboard.select_service_id') }}</label>
                                <select class="wide" name="service_id" id="service_id" data-plugin="customselect">
                                    <option value="">{{ __('dashboard.none') }}</option>
                                    @foreach($services as $key=>$service)
                                        <option value="{{ $service->id }}" @if( $service->id == $row->service_id ) selected @endif>{{ $key+1 }}. {{ $service->title }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="form-group col">
                                <label for="placeholder">{{ __('dashboard.placeholder') }} <span>*</span></label>
                                <input type="text" class="form-control" name="placeholder" id="placeholder" value="{{ $row->placeholder }}" placeholder="e.g. plan, bundle, etc.">

                                <div class="invalid-feedback">
                                    {{ __('dashboard.please_provide') }} {{ __('dashboard.placeholder') }}
                                </div>
                            </div>
                            <div class="form-group col">
                                <label for="service_title">{{ __('dashboard.service_title') }} <span>*</span></label>
                                <input type="text" class="form-control" name="service_title" id="service_title" value="{{ $row->service_title }}" placeholder="e.g. plan, bundle, etc.">

                                <div class="invalid-feedback">
                                    {{ __('dashboard.please_provide') }} {{ __('dashboard.service_title') }}
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="service_desc">{{ __('dashboard.service_desc') }} <span>*</span></label>
                            <textarea class="form-control" name="service_desc" id="editor" rows="4" required>{{ $row->service_desc }}</textarea>

                            <div class="invalid-feedback">
                                {{ __('dashboard.please_provide') }} {{ __('dashboard.service_desc') }}
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="meta_desc">{{ __('dashboard.meta_description') }} <span>*</span></label>
                            <textarea class="form-control" name="meta_desc" id="editor" rows="4" required>{{ $row->meta_desc }}</textarea>

                            <div class="invalid-feedback">
                                {{ __('dashboard.please_provide') }} {{ __('dashboard.meta_description') }}
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="keywords">{{ __('dashboard.meta_keywords') }} <span>*</span></label>
                            <input type="text" class="form-control tagin" data-tagin-separator=" " name="keywords" value="{{ $row->keywords ?? '' }}" required>
                            
                            <div class="invalid-feedback">
                                {{ __('dashboard.please_provide') }} {{ __('dashboard.meta_keywords') }}
                            </div>
                        </div>

                        

                        <div class="form-group">
                            <label for="image">{{ __('dashboard.thumbnail') }} <span>{{ __('dashboard.image_size', ['height' => 280, 'width' => 500]) }}</span></label>
                            <input type="file" class="form-control" name="image" id="image">

                            <div class="invalid-feedback">
                                {{ __('dashboard.please_provide') }} {{ __('dashboard.thumbnail') }}
                            </div>
                        </div>

                        {{-- <div class="form-group">
                        <label for="video_id">{{ __('dashboard.youtube_video_id') }}</label>
                        <input type="text" class="form-control" name="video_id" id="video_id" value="{{ $row->video_id }}">

                        <div class="invalid-feedback">
                            {{ __('dashboard.please_provide') }} {{ __('dashboard.youtube_video_id') }}
                        </div>
                    </div> --}}

                    <div class="form-group">
                        <label for="status">{{ __('dashboard.select_status') }}</label>
                        <select class="wide" name="status" id="status" data-plugin="customselect">
                            <option value="1" @if( $row->status == 1 ) selected @endif>{{ __('dashboard.active') }}</option>
                            <option value="0" @if( $row->status == 0 ) selected @endif>{{ __('dashboard.inactive') }}</option>
                        </select>
                    </div>
                    <!-- Form End -->

            </div>
            <div class="card-footer">
                <div class="form-group">
                    <button type="submit" class="btn btn-primary">{{ __('dashboard.update') }}</button>
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