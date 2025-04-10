@extends('admin.layouts.master')
{{-- @section('title', $title) --}}
@section('content')

<!-- Start Content-->
<div class="container-fluid">

    <!-- start page title -->
    <!-- Include page breadcrumb -->
    {{-- @include('admin.inc.breadcrumb') --}}
    <!-- end page title -->


    <div class="row">
        <div class="col-12">
            <a href="{{ route('admin.technologies.index') }}" class="btn btn-info">{{ __('dashboard.back') }}</a>
        </div>
    </div>

    <div class="row">
        <div class="col-12 col-lg-12">
            <div class="card">
                <div class="card-header">
                    {{-- <h4 class="header-title">{{ __('dashboard.edit') }} {{ $title }}</h4> --}}
                </div>
                <form class="needs-validation" novalidate action="{{ route('admin.technologies.update', $subservice->id) }}" method="post" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    <div class="card-body">

                        <!-- Form Start -->
                        <div class="card-body">
                            <div class="form-group">
                                <label for="status">{{ __('dashboard.select_status') }}</label>
                                <select class="wide" name="service_id" id="status" data-plugin="customselect">
                                    @foreach ($services as $service)
                                    <option value="{{$service->id}}" @if( $subservice->service_id == $service->id ) selected @endif>{{$service->title }}</option>
                                    @endforeach
                                    
                                </select>
                            </div>
                            <!-- Form Start -->
                            <div class="form-group">
                                <label for="title">{{ __('dashboard.title') }} <span>*</span></label>
                                <input type="text" class="form-control" name="title" id="title" value="{{ $subservice->title }}" required>
    
                                <div class="invalid-feedback">
                                    {{ __('dashboard.please_provide') }} {{ __('dashboard.title') }}
                                </div>
                            </div>
                         
                            <div class="row">
                                <div class="form-group col-6">
                                    <label for="slug">{{ __('dashboard.slug') }} <span>* </span></label>
                                    <input type="text" class="form-control" name="slug" id="slug" value="{{ $subservice->slug }}" readonly required>
                                    <div class="invalid-feedback">
                                        {{ __('dashboard.please_provide') }} {{ __('dashboard.slug') }}
                                    </div>
                                </div>
                                <div class="form-group col-6">
                                    <label for="short_title">{{ __('dashboard.short_title') }} <span>*</span></label>
                                    <input type="text" class="form-control" name="short_title" id="short_title" value="{{ $subservice->short_title }}" required>
        
                                    <div class="invalid-feedback">
                                        {{ __('dashboard.please_provide') }} {{ __('dashboard.short_title') }}
                                    </div>
                                </div>
                            </div>
    
                           
    
                            <div class="form-group">
                                <label for="description">{{ __('dashboard.description') }} <span>*</span></label>
                                <textarea class="form-control" name="description" id="editor1" rows="8" required>{{ $subservice->description }}</textarea>
    
                                <div class="invalid-feedback">
                                    {{ __('dashboard.please_provide') }} {{ __('dashboard.description') }}
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="meta_title">{{ __('dashboard.meta_title') }} <span>*</span></label>
                                <input type="text" class="form-control" name="meta_title" id="meta_title" value="{{ $subservice->meta_title }}" required>
    
                                <div class="invalid-feedback">
                                    {{ __('dashboard.please_provide') }} {{ __('dashboard.meta_title') }}
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="short_desc">{{ __('dashboard.meta_description') }} <span>*</span></label>
                                <textarea class="form-control" name="short_desc" id="editor" rows="4" required>{{ $subservice->short_desc }}</textarea>
    
                                <div class="invalid-feedback">
                                    {{ __('dashboard.please_provide') }} {{ __('dashboard.meta_description') }}
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="keywords">{{ __('dashboard.meta_keywords') }} <span>*</span></label>
                                <input type="text" class="form-control tagin" data-tagin-separator=" " name="keywords" value="{{ $subservice->keywords ?? '' }}" required>
                                
                                <div class="invalid-feedback">
                                    {{ __('dashboard.please_provide') }} {{ __('dashboard.meta_keywords') }}
                                </div>
                            </div>
                            <div class="row">
                                <div class="form-group col-6">
                                    <label for="image">{{ __('dashboard.thumbnail') }} <span>{{ __('dashboard.image_size', ['height' => 500, 'width' => 800]) }}</span></label>
                                    <input type="file" class="form-control" name="image" id="image">
        
                                    <div class="invalid-feedback">
                                        {{ __('dashboard.please_provide') }} {{ __('dashboard.thumbnail') }}
                                    </div>
                                </div>
                                <div class="form-group col-6">
                                    <label for="logo">{{ __('dashboard.logo') }} <span>{{ __('dashboard.image_size', ['height' => 100, 'width' => 100]) }}</span></label>
                                    <input type="file" class="form-control" name="logo" id="logo">
        
                                    <div class="invalid-feedback">
                                        {{ __('dashboard.please_provide') }} {{ __('dashboard.logo') }}
                                    </div>
                                </div>
                            </div>
    
                            <div class="row">
                                <div class="form-group col">
                                    <label for="price">{{ __('dashboard.price') }} <span>* </span></label>
                                    <input type="number" class="form-control" name="price" id="price" value="{{ $subservice->price }}" required>
                                    <div class="invalid-feedback">
                                        {{ __('dashboard.please_provide') }} {{ __('dashboard.price') }}
                                    </div>
                                </div>
                                <div class="form-group col">
                                    <label for="starting_price">{{ __('dashboard.starting_price') }} <span>*</span></label>
                                    <input type="number" class="form-control" name="starting_price" id="starting_price" value="{{ $subservice->starting_price }}" required>
        
                                    <div class="invalid-feedback">
                                        {{ __('dashboard.please_provide') }} {{ __('dashboard.starting_price') }}
                                    </div>
                                </div>
                                <div class="form-group col">
                                    <label for="review_count">{{ __('dashboard.review_count') }} <span>*</span></label>
                                    <input type="number" class="form-control" name="review_count" id="review_count" value="{{ $subservice->review_count }}" required>
        
                                    <div class="invalid-feedback">
                                        {{ __('dashboard.please_provide') }} {{ __('dashboard.review_count') }}
                                    </div>
                                </div>
                                <div class="form-group col">
                                    <label for="priceCurrency">{{ __('dashboard.priceCurrency') }} <span>*</span></label>
                                    <input type="text" class="form-control" name="priceCurrency" id="priceCurrency" value="{{ $subservice->priceCurrency }}" required>
        
                                    <div class="invalid-feedback">
                                        {{ __('dashboard.please_provide') }} {{ __('dashboard.priceCurrency') }}
                                    </div>
                                </div>
                                <div class="form-group col">
                                    <label for="average_rating">{{ __('dashboard.average_rating') }} <span>*</span></label>
                                    <input type="text" class="form-control" name="average_rating" id="average_rating" value="{{ $subservice->average_rating }}" required>
        
                                    <div class="invalid-feedback">
                                        {{ __('dashboard.please_provide') }} {{ __('dashboard.average_rating') }}
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="form-group col">
                                    <label for="manu">Manu</label>
                                    <select class="wide" name="manu" id="manu" data-plugin="customselect">
                                        <option value="0" @if( $subservice->manu == 0 ) selected @endif>Hidden</option>
                                        <option value="1" @if( $subservice->manu == 1 ) selected @endif>Show</option>
                                    </select>
                                </div>
    
                                <div class="form-group col">
                                    <label for="status">{{ __('dashboard.select_status') }}</label>
                                    <select class="wide" name="status" id="status" data-plugin="customselect">
                                        <option value="1" @if( $subservice->status == 1 ) selected @endif>{{ __('dashboard.active') }}</option>
                                        <option value="0" @if( $subservice->status == 0 ) selected @endif>{{ __('dashboard.inactive') }}</option>
                                    </select>
                                </div>
                            
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