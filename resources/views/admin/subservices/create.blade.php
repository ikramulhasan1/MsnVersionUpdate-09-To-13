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
            <a href="{{ route('admin.subservices.index') }}" class="btn btn-info">{{ __('dashboard.back') }}</a>
        </div>
    </div>

    <div class="row">
        <div class="col-12 col-lg-12">
            <div class="card">
                <div class="card-header">
                    {{-- <h4 class="header-title">{{ __('dashboard.add') }} {{ $title }}</h4> --}}
                </div>
                <form class="needs-validation" novalidate action="{{ route('admin.subservices.store') }}" method="post" enctype="multipart/form-data">
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

                        <div class="form-group">
                            <label for="short_desc">{{ __('dashboard.short_desc') }} <span>*</span></label>
                            <textarea class="form-control" name="short_desc" id="editor" rows="4" required>{{ old('short_desc') }}</textarea>

                            <div class="invalid-feedback">
                                {{ __('dashboard.please_provide') }} {{ __('dashboard.short_desc') }}
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
                            <label for="image">{{ __('dashboard.thumbnail') }} <span>*</span> <span>{{ __('dashboard.image_size', ['height' => 500, 'width' => 800]) }}</span></label>
                            <input type="file" class="form-control" name="image" id="image" required>

                            <div class="invalid-feedback">
                                {{ __('dashboard.please_provide') }} {{ __('dashboard.thumbnail') }}
                            </div>
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