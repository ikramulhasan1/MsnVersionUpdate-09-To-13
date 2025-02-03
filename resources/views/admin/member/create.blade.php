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
                        <label for="title">{{ __('dashboard.name') }} <span>*</span></label>
                        <input type="text" class="form-control" name="title" id="title" value="{{ old('title') }}" required>

                        <div class="invalid-feedback">
                          {{ __('dashboard.please_provide') }} {{ __('dashboard.name') }}
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="designation">{{ __('dashboard.designation') }} <span>*</span></label>
                        <select class="form-control" name="designation" id="designation" required>
                            <option value="">{{ __('dashboard.select') }}</option>
                            @foreach( $designations as $designation )
                            <option value="{{ $designation->id }}" @if(old('designation') == $designation->id) selected @endif>{{ $designation->title }}</option>
                            @endforeach
                        </select>

                        <div class="invalid-feedback">
                          {{ __('dashboard.please_provide') }} {{ __('dashboard.designation') }}
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="description">{{ __('dashboard.description') }}</label>
                        <textarea class="form-control summernote" name="description" id="description" rows="8">{{ old('description') }}</textarea>

                        <div class="invalid-feedback">
                          {{ __('dashboard.please_provide') }} {{ __('dashboard.description') }}
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="image">{{ __('dashboard.photo') }} <span>*</span> <span>{{ __('dashboard.image_size', ['height' => 500, 'width' => 400]) }}</span></label>
                        <input type="file" class="form-control" name="image" id="image" required>

                        <div class="invalid-feedback">
                          {{ __('dashboard.please_provide') }} {{ __('dashboard.photo') }}
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="facebook">{{ __('dashboard.facebook') }}</label>
                        <input type="url" class="form-control" name="facebook" id="facebook" value="{{ old('facebook') }}">

                        <div class="invalid-feedback">
                          {{ __('dashboard.please_provide') }} {{ __('dashboard.facebook') }}
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="twitter">{{ __('dashboard.twitter') }}</label>
                        <input type="url" class="form-control" name="twitter" id="twitter" value="{{ old('twitter') }}">

                        <div class="invalid-feedback">
                          {{ __('dashboard.please_provide') }} {{ __('dashboard.twitter') }}
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="instagram">{{ __('dashboard.instagram') }}</label>
                        <input type="url" class="form-control" name="instagram" id="instagram" value="{{ old('instagram') }}">

                        <div class="invalid-feedback">
                          {{ __('dashboard.please_provide') }} {{ __('dashboard.instagram') }}
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="linkedin">{{ __('dashboard.linkedin') }}</label>
                        <input type="url" class="form-control" name="linkedin" id="linkedin" value="{{ old('linkedin') }}">

                        <div class="invalid-feedback">
                          {{ __('dashboard.please_provide') }} {{ __('dashboard.linkedin') }}
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="email">{{ __('dashboard.email') }}</label>
                        <input type="email" class="form-control" name="email" id="email" value="{{ old('email') }}">

                        <div class="invalid-feedback">
                          {{ __('dashboard.please_provide') }} {{ __('dashboard.email') }}
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="phone">{{ __('dashboard.phone') }}</label>
                        <input type="text" class="form-control" name="phone" id="phone" value="{{ old('phone') }}">

                        <div class="invalid-feedback">
                          {{ __('dashboard.please_provide') }} {{ __('dashboard.phone') }}
                        </div>
                    </div>

                    {{-- <div class="form-group">
                        <label for="whatsapp">{{ __('dashboard.whatsapp') }}</label>
                        <input type="text" class="form-control" name="whatsapp" id="whatsapp" value="{{ old('whatsapp') }}">

                        <div class="invalid-feedback">
                          {{ __('dashboard.please_provide') }} {{ __('dashboard.whatsapp') }}
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="website">{{ __('dashboard.website') }}</label>
                        <input type="url" class="form-control" name="website" id="website" value="{{ old('website') }}">

                        <div class="invalid-feedback">
                          {{ __('dashboard.please_provide') }} {{ __('dashboard.website') }}
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

@endsection