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

                    <div class="form-group">
                        <label for="price">{{ __('dashboard.price') }} <span>*</span></label>
                        <input type="text" class="form-control" name="price" id="price" value="{{ old('price') }}" required>

                        <div class="invalid-feedback">
                          {{ __('dashboard.please_provide') }} {{ __('dashboard.price') }}
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="old_price">{{ __('dashboard.old_price') }}</label>
                        <input type="text" class="form-control" name="old_price" id="old_price" value="{{ old('old_price') }}">

                        <div class="invalid-feedback">
                          {{ __('dashboard.please_provide') }} {{ __('dashboard.old_price') }}
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="duration">{{ __('dashboard.duration') }} <span>*</span></label>
                        <input type="text" class="form-control" name="duration" id="duration" value="{{ old('duration') }}" required>

                        <div class="invalid-feedback">
                          {{ __('dashboard.please_provide') }} {{ __('dashboard.duration') }}
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="feature">{{ __('dashboard.feature_name') }} <span>*</span></label>
                    </div>
                    <div class="form-group row" id="inputFormField">
                        <div class="col-8"><input type="text" class="form-control" name="features[]" placeholder="{{ __('dashboard.feature_name') }}" required></div>
                        <div class="col-4"><button id="removeField" type="button" class="btn btn-danger">{{ __('dashboard.remove') }}</button></div>
                    </div>

                    <div id="newField"></div>
                    <div class="form-group">
                        <button id="addField" type="button" class="btn btn-info">{{ __('dashboard.add_feature') }}</button>
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

@endsection

@section('page_js')
<script type="text/javascript">
(function ($) {
    "use strict";
    // add Field
    $(document).on('click', '#addField', function () {
        var html = '';
        html += '<div class="form-group row" id="inputFormField">';
        html += '<div class="col-8"><input type="text" class="form-control" name="features[]" placeholder="{{ __('dashboard.feature_name') }}" required></div>';
        html += '<div class="col-4"><button id="removeField" type="button" class="btn btn-danger">Remove</button></div>';
        html += '</div>';

        $('#newField').append(html);
    });

    // remove Field
    $(document).on('click', '#removeField', function () {
        $(this).closest('#inputFormField').remove();
    });
}(jQuery));
</script>
@endsection