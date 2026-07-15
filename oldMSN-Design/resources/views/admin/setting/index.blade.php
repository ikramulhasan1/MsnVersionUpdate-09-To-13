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
                <div class="card-body">
                  <h4 class="header-title">{{ $title }}</h4>
                  <br/>

                  <ul class="nav nav-tabs mb-3">
                    <li class="nav-item">
                        <a href="#website-tab" data-toggle="tab" aria-expanded="false" class="nav-link active">
                            {{ __('dashboard.site_info') }}
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="#contact-tab" data-toggle="tab" aria-expanded="true" class="nav-link">
                            {{ __('dashboard.contact_info') }}
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="#social-tab" data-toggle="tab" aria-expanded="false" class="nav-link">
                            {{ __('dashboard.social_info') }}
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="#custom-tab" data-toggle="tab" aria-expanded="true" class="nav-link">
                            {{ __('dashboard.customization') }}
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="#account-tab" data-toggle="tab" aria-expanded="false" class="nav-link">
                            {{ __('dashboard.account') }}
                        </a>
                    </li>
                  </ul>
                  <div class="tab-content">
                    <div class="tab-pane show active" id="website-tab">
                        
                      <!-- Form Start -->
                      <form class="needs-validation" novalidate action="{{ route($route.'.siteinfo') }}" method="post" enctype="multipart/form-data">
                        @csrf
                        <input name="id" type="hidden" value="{{ (isset($row->id))?$row->id:-1 }}">

                        <div class="form-group">
                            <label for="title">{{ __('dashboard.site_title') }} <span>*</span></label>
                            <input type="text" class="form-control" name="title" id="title" value="{{ isset($row->title)?$row->title:'' }}" required>

                            <div class="invalid-feedback">
                              {{ __('dashboard.please_provide') }} {{ __('dashboard.site_title') }}
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="description">{{ __('dashboard.meta_description') }}: <span>{{ __('dashboard.meta_desc_length') }}</span></label>
                            <textarea class="form-control" name="description" id="description" rows="4">{{ isset($row->description)?$row->description:'' }}</textarea>

                            <div class="invalid-feedback">
                              {{ __('dashboard.please_provide') }} {{ __('dashboard.meta_description') }}
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="keywords">{{ __('dashboard.meta_keywords') }}: <span>{{ __('dashboard.keywords_separate') }}</span></label>
                            <textarea class="form-control" name="keywords" id="keywords" rows="4">{{ isset($row->keywords)?$row->keywords:'' }}</textarea>

                            <div class="invalid-feedback">
                              {{ __('dashboard.please_provide') }} {{ __('dashboard.meta_keywords') }}
                            </div>
                        </div>

                        <div class="row">
                          <div class="form-group col-md-6">

                            @if(isset($row->logo_path))
                            @if(is_file('uploads/'.$path.'/'.$row->logo_path))
                            <img src="{{ asset('uploads/'.$path.'/'.$row->logo_path) }}" class="img-fluid site-image" alt="{{ __('dashboard.site_logo') }}">
                            @endif
                            @endif

                            <label for="logo">{{ __('dashboard.site_logo') }}: <span>{{ __('dashboard.image_size', ['height' => 80, 'width' => 'Any']) }}</span></label>
                            <input type="file" class="form-control" name="logo" id="logo">

                            <div class="invalid-feedback">
                              {{ __('dashboard.please_provide') }} {{ __('dashboard.site_logo') }}
                            </div>
                          </div>

                          <div class="form-group col-md-6">

                            @if(isset($row->favicon_path))
                            @if(is_file('uploads/'.$path.'/'.$row->favicon_path))
                            <img src="{{ asset('uploads/'.$path.'/'.$row->favicon_path) }}" class="img-fluid site-image" alt="{{ __('dashboard.site_favicon') }}">
                            @endif
                            @endif

                            <label for="favicon">{{ __('dashboard.site_favicon') }}: <span>{{ __('dashboard.image_size', ['height' => 64, 'width' => 64]) }}</span></label>
                            <input type="file" class="form-control" name="favicon" id="favicon">

                            <div class="invalid-feedback">
                              {{ __('dashboard.please_provide') }} {{ __('dashboard.site_favicon') }}
                            </div>
                          </div>
                        </div>

                        <div class="form-group">
                            <label for="footer_text">{{ __('dashboard.footer_text') }} <span>*</span></label>
                            <textarea class="form-control" name="footer_text" id="footer_text" rows="2" required>{{ isset($row->footer_text)?$row->footer_text:'' }}</textarea>

                            <div class="invalid-feedback">
                              {{ __('dashboard.please_provide') }} {{ __('dashboard.footer_text') }}
                            </div>
                        </div>

                        <div class="form-group">
                            <button type="submit" class="btn btn-primary">{{ __('dashboard.update') }}</button>
                        </div>

                      </form>
                      <!-- Form End -->

                    </div>
                    <div class="tab-pane" id="contact-tab">
                        
                      <!-- Form Start -->
                      <form class="needs-validation" novalidate action="{{ route($route.'.contactinfo') }}" method="post" enctype="multipart/form-data">
                        @csrf
                        <input name="id" type="hidden" value="{{ (isset($row->id))?$row->id:-1 }}">

                        <div class="row">
                          <div class="form-group col-md-6">
                            <label for="phone_no">{{ __('dashboard.phone_no_1') }} <span>*</span></label>
                            <input type="text" class="form-control" name="phone_no" id="phone_no" value="{{ isset($row->phone_one)?$row->phone_one:'' }}" required>

                            <div class="invalid-feedback">
                              {{ __('dashboard.please_provide') }} {{ __('dashboard.phone_no_1') }}
                            </div>
                          </div>

                          <div class="form-group col-md-6">
                            <label for="phone_no2">{{ __('dashboard.phone_no_2') }}</label>
                            <input type="text" class="form-control" name="phone_no2" id="phone_no2" value="{{ isset($row->phone_two)?$row->phone_two:'' }}">

                            <div class="invalid-feedback">
                              {{ __('dashboard.please_provide') }} {{ __('dashboard.phone_no_2') }}
                            </div>
                          </div>
                        </div>

                        <div class="row">
                          <div class="form-group col-md-6">
                            <label for="email_address">{{ __('dashboard.email_address_1') }} <span>*</span></label>
                            <input type="email" class="form-control" name="email_address" id="email_address" value="{{ isset($row->email_one)?$row->email_one:'' }}" required>

                            <div class="invalid-feedback">
                              {{ __('dashboard.please_provide') }} {{ __('dashboard.email_address_1') }}
                            </div>
                          </div>

                          <div class="form-group col-md-6">
                            <label for="email_address2">{{ __('dashboard.email_address_2') }}</label>
                            <input type="email" class="form-control" name="email_address2" id="email_address2" value="{{ isset($row->email_two)?$row->email_two:'' }}">

                            <div class="invalid-feedback">
                              {{ __('dashboard.please_provide') }} {{ __('dashboard.email_address_2') }}
                            </div>
                          </div>
                        </div>

                        <div class="row">
                          <div class="form-group col-md-6">
                            <label for="contact_address">{{ __('dashboard.contact_address') }} <span>*</span></label>
                            <input type="text" class="form-control" name="contact_address" id="contact_address" value="{{ isset($row->contact_address)?$row->contact_address:'' }}" required>

                            <div class="invalid-feedback">
                              {{ __('dashboard.please_provide') }} {{ __('dashboard.contact_address') }}
                            </div>
                          </div>

                          <div class="form-group col-md-6">
                            <label for="contact_mail">{{ __('dashboard.contact_mail') }} <span>*</span></label>
                            <input type="email" class="form-control" name="contact_mail" id="contact_mail" value="{{ isset($row->contact_mail)?$row->contact_mail:'' }}" required>

                            <div class="invalid-feedback">
                              {{ __('dashboard.please_provide') }} {{ __('dashboard.contact_mail') }}
                            </div>
                          </div>
                        </div>

                        <div class="form-group">
                            <label for="office_hours">{{ __('dashboard.office_hours') }} <span>{{ __('dashboard.open_close_times') }}</span></label>
                            <textarea class="form-control summernote" name="office_hours" id="office_hours" rows="4">{{ isset($row->office_hours)?$row->office_hours:'' }}</textarea>

                            <div class="invalid-feedback">
                              {{ __('dashboard.please_provide') }} {{ __('dashboard.office_hours') }}
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="google_map">{{ __('dashboard.google_map') }} <span>{{ __('dashboard.embed_code') }}</span> <a href="https://www.google.com/maps" target="_blank">{{ __('dashboard.google_map') }}</a></label>
                            <textarea class="form-control" name="google_map" id="google_map" rows="4">{{ isset($row->google_map)?$row->google_map:'' }}</textarea>

                            <div class="invalid-feedback">
                              {{ __('dashboard.please_provide') }} {{ __('dashboard.google_map') }}
                            </div>
                        </div>

                        <div class="form-group">
                            <button type="submit" class="btn btn-primary">{{ __('dashboard.update') }}</button>
                        </div>

                      </form>
                      <!-- Form End -->

                    </div>
                    <div class="tab-pane" id="social-tab">
                        
                      <!-- Form Start -->
                      <form class="needs-validation" novalidate action="{{ route($route.'.socialinfo') }}" method="post" enctype="multipart/form-data">
                        @csrf
                        <input name="id" type="hidden" value="{{ (isset($social->id))?$social->id:-1 }}">

                        <div class="row">
                          <div class="form-group col-md-6">
                            <label for="facebook">{{ __('dashboard.facebook') }}</label>
                            <input type="url" class="form-control" name="facebook" id="facebook" value="{{ isset($social->facebook)?$social->facebook:'' }}">

                            <div class="invalid-feedback">
                              {{ __('dashboard.please_provide') }} {{ __('dashboard.facebook') }}
                            </div>
                          </div>

                          <div class="form-group col-md-6">
                            <label for="twitter">{{ __('dashboard.twitter') }}</label>
                            <input type="url" class="form-control" name="twitter" id="twitter" value="{{ isset($social->twitter)?$social->twitter:'' }}">

                            <div class="invalid-feedback">
                              {{ __('dashboard.please_provide') }} {{ __('dashboard.twitter') }}
                            </div>
                          </div>
                        </div>

                        <div class="row">
                          <div class="form-group col-md-6">
                            <label for="linkedin">{{ __('dashboard.linkedin') }}</label>
                            <input type="url" class="form-control" name="linkedin" id="linkedin" value="{{ isset($social->linkedin)?$social->linkedin:'' }}">

                            <div class="invalid-feedback">
                              {{ __('dashboard.please_provide') }} {{ __('dashboard.linkedin') }}
                            </div>
                          </div>

                          <div class="form-group col-md-6">
                            <label for="instagram">{{ __('dashboard.instagram') }}</label>
                            <input type="url" class="form-control" name="instagram" id="instagram" value="{{ isset($social->instagram)?$social->instagram:'' }}">

                            <div class="invalid-feedback">
                              {{ __('dashboard.please_provide') }} {{ __('dashboard.instagram') }}
                            </div>
                          </div>
                        </div>

                        <div class="row">
                          <div class="form-group col-md-6">
                            <label for="pinterest">{{ __('dashboard.pinterest') }}</label>
                            <input type="url" class="form-control" name="pinterest" id="pinterest" value="{{ isset($social->pinterest)?$social->pinterest:'' }}">

                            <div class="invalid-feedback">
                              {{ __('dashboard.please_provide') }} {{ __('dashboard.pinterest') }}
                            </div>
                          </div>

                          <div class="form-group col-md-6">
                            <label for="youtube">{{ __('dashboard.youtube') }}</label>
                            <input type="url" class="form-control" name="youtube" id="youtube" value="{{ isset($social->youtube)?$social->youtube:'' }}">

                            <div class="invalid-feedback">
                              {{ __('dashboard.please_provide') }} {{ __('dashboard.youtube') }}
                            </div>
                          </div>
                        </div>

                        <div class="row">
                          <div class="form-group col-md-6">
                            <label for="skype">{{ __('dashboard.skype') }}</label>
                            <input type="text" class="form-control" name="skype" id="skype" value="{{ isset($social->skype)?$social->skype:'' }}">

                            <div class="invalid-feedback">
                              {{ __('dashboard.please_provide') }} {{ __('dashboard.skype') }}
                            </div>
                          </div>

                          <div class="form-group col-md-6">
                            <label for="whatsapp">{{ __('dashboard.whatsapp') }} <span>({{ __('dashboard.inc_country_code') }})</span></label>
                            <input type="text" class="form-control" name="whatsapp" id="whatsapp" value="{{ isset($social->whatsapp)?$social->whatsapp:'' }}">

                            <div class="invalid-feedback">
                              {{ __('dashboard.please_provide') }} {{ __('dashboard.whatsapp') }}
                            </div>
                          </div>
                        </div>

                        <div class="form-group">
                            <button type="submit" class="btn btn-primary">{{ __('dashboard.update') }}</button>
                        </div>

                      </form>
                      <!-- Form End -->

                    </div>
                    <div class="tab-pane" id="custom-tab">
                        
                      <!-- Form Start -->
                      <form class="needs-validation" novalidate action="{{ route($route.'.customcode') }}" method="post" enctype="multipart/form-data">
                        @csrf
                        <input name="id" type="hidden" value="{{ (isset($row->id))?$row->id:-1 }}">

                        <div class="form-group">
                            <label for="custom_css">{{ __('dashboard.custom_css') }}</label>
                            <textarea class="form-control codeEditor" name="custom_css" id="custom_css" rows="20">{{ isset($row->custom_css)?$row->custom_css:'' }}</textarea>
                        </div>

                        <div class="form-group">
                            <button type="submit" class="btn btn-primary">{{ __('dashboard.update') }}</button>
                        </div>

                      </form>
                      <!-- Form End -->

                    </div>
                    <div class="tab-pane" id="account-tab">

                      <div class="card">
                        <div class="card-header">
                          <h4 class="header-title">{{ __('dashboard.admin_mail_address') }}</h4>
                        </div>
                        <div class="card-body">
                          <!-- Form Start -->
                          <form class="needs-validation" novalidate action="{{ route($route.'.changemail') }}" method="post" enctype="multipart/form-data">
                            @csrf

                            <div class="form-group row">
                                <label for="email" class="col-md-4 col-form-label text-md-right">{{ __('dashboard.mail_address') }} <span>*</span></label>

                                <div class="col-md-6">
                                    <input id="email" type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ Auth::user()->email }}" required>

                                    @error('email')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror

                                    <div class="invalid-feedback">
                                      {{ __('dashboard.please_provide') }} {{ __('dashboard.mail_address') }}
                                    </div>
                                </div>
                            </div>

                            <div class="form-group row">
                                <div class="col-md-6 offset-md-4">
                                    <button type="submit" class="btn btn-primary">{{ __('dashboard.change') }}</button>
                                </div>
                            </div>

                          </form>
                          <!-- Form End -->
                        </div>
                      </div>
                        
                      <div class="card">
                        <div class="card-header">
                          <h4 class="header-title">{{ __('dashboard.admin_change_password') }}</h4>
                        </div>
                        <div class="card-body">
                          <!-- Form Start -->
                          <form class="needs-validation" novalidate action="{{ route($route.'.changepass') }}" method="post" enctype="multipart/form-data">
                            @csrf

                            <div class="form-group row">
                                <label for="password" class="col-md-4 col-form-label text-md-right">{{ __('dashboard.old_password') }} <span>*</span></label>

                                <div class="col-md-6">
                                    <input id="old_password" type="password" class="form-control @error('old_password') is-invalid @enderror" name="old_password" required autocomplete="old_password">

                                    @error('password')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror

                                    <div class="invalid-feedback">
                                      {{ __('dashboard.please_provide') }} {{ __('dashboard.old_password') }}
                                    </div>
                                </div>
                            </div>

                            <div class="form-group row">
                                <label for="password" class="col-md-4 col-form-label text-md-right">{{ __('dashboard.new_password') }} <span>*</span></label>

                                <div class="col-md-6">
                                    <input id="password" type="password" class="form-control @error('password') is-invalid @enderror" name="password" required autocomplete="new-password">

                                    @error('password')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror

                                    <div class="invalid-feedback">
                                      {{ __('dashboard.please_provide') }} {{ __('dashboard.new_password') }}
                                    </div>
                                </div>
                            </div>

                            <div class="form-group row">
                                <label for="password-confirm" class="col-md-4 col-form-label text-md-right">{{ __('dashboard.confirm_password') }} <span>*</span></label>

                                <div class="col-md-6">
                                    <input id="password-confirm" type="password" class="form-control" name="password_confirmation" required autocomplete="new-password">
                                </div>

                                <div class="invalid-feedback">
                                    {{ __('dashboard.please_provide') }} {{ __('dashboard.confirm_password') }}
                                </div>
                            </div>

                            <div class="form-group row">
                                <div class="col-md-6 offset-md-4">
                                    <button type="submit" class="btn btn-primary">{{ __('dashboard.change') }}</button>
                                </div>
                            </div>

                          </form>
                          <!-- Form End -->
                        </div>
                      </div>
                      

                    </div>
                  </div>

                </div> <!-- end card body-->
            </div> <!-- end card -->
        </div><!-- end col-->
    </div>
    <!-- end row-->

    
</div> <!-- container -->
<!-- End Content-->

@endsection