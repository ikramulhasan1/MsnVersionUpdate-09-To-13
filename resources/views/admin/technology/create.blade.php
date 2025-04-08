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
                    {{-- <h4 class="header-title">{{ __('dashboard.add') }} {{ $title }}</h4> --}}
                </div>
                <form class="needs-validation" novalidate action="{{ route('admin.technologies.store') }}" method="post" enctype="multipart/form-data">
                    @csrf
                    <div class="card-body">
                        <div class="form-group">
                            <label for="status">{{ __('dashboard.select_status') }}</label>
                            <select class="wide" name="service_id" id="status" data-plugin="customselect">
                                @foreach ($services as $service)
                                <option value="{{$service->id}}">{{$service->title }}</option>
                                @endforeach
                                
                            </select>
                        </div>
                        <!-- Form Start -->
                        <div class="form-group">
                            <label for="title">{{ __('dashboard.title') }} <span>*</span></label>
                            <input type="text" class="form-control" name="title" id="title" value="{{ old('title') }}" required>

                            <div class="invalid-feedback">
                                {{ __('dashboard.please_provide') }} {{ __('dashboard.title') }}
                            </div>
                        </div>

                        <div class="row">                        
                            <div class="form-group col-6">
                                <label for="slug">{{ __('dashboard.slug') }} <span>* [Write a unique slug]</span></label>
                                <input type="text" class="form-control" name="slug" id="slug" value="{{ old('slug') }}" required>
                                <div class="invalid-feedback">
                                    {{ __('dashboard.please_provide') }} {{ __('dashboard.slug') }}
                                </div>
                            </div>
                            <div class="form-group col-6">
                                <label for="short_title">{{ __('dashboard.short_title') }} <span>*</span></label>
                                <input type="text" class="form-control" name="short_title" id="short_title" value="{{ old('short_title') }}" required>
    
                                <div class="invalid-feedback">
                                    {{ __('dashboard.please_provide') }} {{ __('dashboard.short_title') }}
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="short_desc">{{ __('dashboard.meta_description') }} <span>*</span></label>
                            <textarea class="form-control" name="short_desc" id="editor" rows="4" required>{{ old('short_desc') }}</textarea>

                            <div class="invalid-feedback">
                                {{ __('dashboard.please_provide') }} {{ __('dashboard.meta_description') }}
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="description">{{ __('dashboard.description') }} <span>*</span></label>
                            <textarea class="form-control" name="description" id="editor1" rows="8" required>{{ old('description') }}</textarea>

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
                            <label for="image">{{ __('dashboard.thumbnail') }} <span>*</span> <span>{{ __('dashboard.image_size', ['height' => 500, 'width' => 800]) }}</span></label>
                            <input type="file" class="form-control" name="image" id="image" required>

                            <div class="invalid-feedback">
                                {{ __('dashboard.please_provide') }} {{ __('dashboard.thumbnail') }}
                            </div>
                        </div>
                        <div class="row">
                            <div class="form-group col">
                                <label for="price">{{ __('dashboard.price') }} <span>* </span></label>
                                <input type="number" class="form-control" name="price" id="price" value="499" required>
                                <div class="invalid-feedback">
                                    {{ __('dashboard.please_provide') }} {{ __('dashboard.price') }}
                                </div>
                            </div>
                            <div class="form-group col">
                                <label for="starting_price">{{ __('dashboard.starting_price') }} <span>*</span></label>
                                <input type="number" class="form-control" name="starting_price" id="starting_price" value="499" required>
    
                                <div class="invalid-feedback">
                                    {{ __('dashboard.please_provide') }} {{ __('dashboard.starting_price') }}
                                </div>
                            </div>
                            <div class="form-group col">
                                <label for="review_count">{{ __('dashboard.review_count') }} <span>*</span></label>
                                <input type="number" class="form-control" name="review_count" id="review_count" value="150" required>
    
                                <div class="invalid-feedback">
                                    {{ __('dashboard.please_provide') }} {{ __('dashboard.review_count') }}
                                </div>
                            </div>
                            <div class="form-group col">
                                <label for="priceCurrency">{{ __('dashboard.priceCurrency') }} <span>*</span></label>
                                <input type="text" class="form-control" name="priceCurrency" id="priceCurrency" value="USD" required>
    
                                <div class="invalid-feedback">
                                    {{ __('dashboard.please_provide') }} {{ __('dashboard.priceCurrency') }}
                                </div>
                            </div>
                            <div class="form-group col">
                                <label for="average_rating">{{ __('dashboard.average_rating') }} <span>*</span></label>
                                <input type="text" class="form-control" name="average_rating" id="average_rating" value="4.9" required>
    
                                <div class="invalid-feedback">
                                    {{ __('dashboard.please_provide') }} {{ __('dashboard.average_rating') }}
                                </div>
                            </div>
                        </div>
                        <!-- Form End -->
                        <div class="form-group">
                            <label for="manu">Manu</label>
                            <select class="wide" name="manu" id="manu" data-plugin="customselect">
                                <option value="0" >Hidden</option>
                                <option value="1" >Show</option>
                            </select>
                        </div>
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