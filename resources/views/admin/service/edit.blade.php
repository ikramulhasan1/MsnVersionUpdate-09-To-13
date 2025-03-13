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
                            <div class="form-group col-6">
                                <label for="slug">{{ __('dashboard.slug') }} <span>* </span></label>
                                <input type="text" class="form-control" name="slug" id="slug" value="{{ $row->slug }}" readonly required>
                                <div class="invalid-feedback">
                                    {{ __('dashboard.please_provide') }} {{ __('dashboard.slug') }}
                                </div>
                            </div>
                            <div class="form-group col-6">
                                <label for="short_title">{{ __('dashboard.short_title') }} <span>*</span></label>
                                <input type="text" class="form-control" name="short_title" id="short_title" value="{{ $row->short_title }}" required>
    
                                <div class="invalid-feedback">
                                    {{ __('dashboard.please_provide') }} {{ __('dashboard.short_title') }}
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="short_desc">{{ __('dashboard.short_desc') }} <span>*</span></label>
                            <textarea class="form-control" name="short_desc" id="editor" rows="4" required>{{ $row->short_desc }}</textarea>

                            <div class="invalid-feedback">
                                {{ __('dashboard.please_provide') }} {{ __('dashboard.short_desc') }}
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="description">{{ __('dashboard.description') }} <span>*</span></label>
                            <textarea class="form-control" name="description" id="editor1" rows="8" required>{{ $row->description }}</textarea>

                            <div class="invalid-feedback">
                                {{ __('dashboard.please_provide') }} {{ __('dashboard.description') }}
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="image">{{ __('dashboard.thumbnail') }} <span>{{ __('dashboard.image_size', ['height' => 500, 'width' => 800]) }}</span></label>
                            <input type="file" class="form-control" name="image" id="image">

                            <div class="invalid-feedback">
                                {{ __('dashboard.please_provide') }} {{ __('dashboard.thumbnail') }}
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="manu">Manu</label>
                            <select class="wide" name="manu" id="manu" data-plugin="customselect">
                                <option value="0" @if( $row->manu == 0 ) selected @endif>Hidden</option>
                                <option value="1" @if( $row->manu == 1 ) selected @endif>Show</option>
                            </select>
                        </div>

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