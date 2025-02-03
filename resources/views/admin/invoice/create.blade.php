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

    <form class="needs-validation" novalidate action="{{ route($route.'.store') }}" method="post" enctype="multipart/form-data">
    @csrf
    <div class="row">
        <div class="col-12 col-lg-8">
            <div class="card">
                <div class="card-header">
                    <h4 class="header-title">{{ __('dashboard.add') }} {{ $title }}</h4>
                </div>
                <div class="card-body">

                    <!-- Form Start -->
                    <div class="form-group">
                        <label for="subject">{{ __('dashboard.subject') }} <span>*</span></label>
                        <input type="text" class="form-control" name="subject" id="subject" value="{{ $template_send->title }}" required>

                        <div class="invalid-feedback">
                          {{ __('dashboard.please_provide') }} {{ __('dashboard.subject') }}
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="name">{{ __('dashboard.name') }} <span>*</span></label>
                        <input type="text" class="form-control" name="name" id="name" value="{{ old('name') }}" required>

                        <div class="invalid-feedback">
                          {{ __('dashboard.please_provide') }} {{ __('dashboard.name') }}
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="email">{{ __('dashboard.email') }} <span>*</span></label>
                        <input type="email" class="form-control" name="email" id="email" value="{{ old('email') }}" required>

                        <div class="invalid-feedback">
                          {{ __('dashboard.please_provide') }} {{ __('dashboard.email') }}
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="company">{{ __('dashboard.company') }}</label>
                        <input type="text" class="form-control" name="company" id="company" value="{{ old('company') }}">

                        <div class="invalid-feedback">
                          {{ __('dashboard.please_provide') }} {{ __('dashboard.company') }}
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="address">{{ __('dashboard.address') }} <span>*</span></label>
                        <input type="text" class="form-control" name="address" id="address" value="{{ old('address') }}" required>

                        <div class="invalid-feedback">
                          {{ __('dashboard.please_provide') }} {{ __('dashboard.address') }}
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="city">{{ __('dashboard.city') }}</label>
                        <input type="text" class="form-control" name="city" id="city" value="{{ old('city') }}">

                        <div class="invalid-feedback">
                          {{ __('dashboard.please_provide') }} {{ __('dashboard.city') }}
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="service">{{ __('dashboard.services') }}</label>
                        <select class="select2 form-control select2-multiple" data-toggle="select2" multiple="multiple" data-placeholder="{{ __('dashboard.select') }}" name="services[]" id="service">
                            @foreach( $services as $service )
                            <option value="{{ $service->id }}" @if(old('service') == $service->id) selected @endif>{{ $service->title }}</option>
                            @endforeach
                        </select>

                        <div class="invalid-feedback">
                          {{ __('dashboard.please_provide') }} {{ __('dashboard.services') }}
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="message">{{ __('dashboard.message') }} <span>*</span></label>
                        <textarea class="form-control summernote" name="message" id="message" rows="8" required>{{ $template_send->description }}</textarea>

                        <div class="invalid-feedback">
                          {{ __('dashboard.please_provide') }} {{ __('dashboard.message') }}
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="reference">{{ __('dashboard.reference') }}</label>
                        <input type="text" class="form-control" name="reference" id="reference" value="{{ old('reference') }}">

                        <div class="invalid-feedback">
                          {{ __('dashboard.please_provide') }} {{ __('dashboard.reference') }}
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="attach">{{ __('dashboard.attach') }}</label>
                        <input type="file" class="form-control" name="attach" id="attach">

                        <div class="invalid-feedback">
                          {{ __('dashboard.please_provide') }} {{ __('dashboard.attach') }}
                        </div>
                    </div>
                </div>
            </div>
        </div><!-- end col-->
        <div class="col-12 col-lg-4">
            <div class="card">
                <div class="card-body">
                    <div class="form-group">
                        <label for="service_charge">{{ __('dashboard.service_bill') }} <span>*</span></label>
                        <input type="text" class="form-control" name="service_charge" id="service_charge" value="{{ old('service_charge') }}" required>

                        <div class="invalid-feedback">
                            {{ __('dashboard.please_provide') }} {{ __('dashboard.service_bill') }}
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="tax">{{ __('dashboard.tax_charge') }}</label>
                        <input type="text" class="form-control" name="tax" id="tax" value="{{ old('tax') }}">

                        <div class="invalid-feedback">
                            {{ __('dashboard.please_provide') }} {{ __('dashboard.tax_charge') }}
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="shipping">{{ __('dashboard.shipping_charge') }}</label>
                        <input type="text" class="form-control" name="shipping" id="shipping" value="{{ old('shipping') }}">

                        <div class="invalid-feedback">
                            {{ __('dashboard.please_provide') }} {{ __('dashboard.shipping_charge') }}
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="total_amount">{{ __('dashboard.total_amount') }} <span>*</span></label>
                        <input type="text" class="form-control" name="total_amount" id="total_amount" value="{{ old('total_amount') }}" required>

                        <div class="invalid-feedback">
                            {{ __('dashboard.please_provide') }} {{ __('dashboard.total_amount') }}
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="invoice_type">{{ __('dashboard.invoice_type') }} <span>*</span></label>
                        <select class="form-control" name="invoice_type" id="invoice_type" required>
                            <option value="">{{ __('dashboard.select') }}</option>
                            <option value="0" @if(old('invoice_type') == 1) selected @endif>{{ __('dashboard.estimate') }}</option>
                            <option value="1" @if(old('invoice_type') == 1) selected @endif>{{ __('dashboard.advance') }}</option>
                            <option value="2" @if(old('invoice_type') == 2) selected @endif>{{ __('dashboard.interval') }}</option>
                            <option value="3" @if(old('invoice_type') == 3) selected @endif>{{ __('dashboard.milestone') }}</option>
                            <option value="4" @if(old('invoice_type') == 4) selected @endif>{{ __('dashboard.final') }}</option>
                            <option value="5" @if(old('invoice_type') == 5) selected @endif>{{ __('dashboard.full') }}</option>
                        </select>

                        <div class="invalid-feedback">
                            {{ __('dashboard.please_provide') }} {{ __('dashboard.invoice_type') }}
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="discount_amount">{{ __('dashboard.discount_amount') }}</label>
                        <input type="text" class="form-control" name="discount_amount" id="discount_amount" value="{{ old('discount_amount') }}">

                        <div class="invalid-feedback">
                            {{ __('dashboard.please_provide') }} {{ __('dashboard.discount_amount') }}
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="invoice_amount">{{ __('dashboard.invoice_amount') }} <span>*</span></label>
                        <input type="text" class="form-control" name="invoice_amount" id="invoice_amount" value="{{ old('invoice_amount') }}" required>

                        <div class="invalid-feedback">
                            {{ __('dashboard.please_provide') }} {{ __('dashboard.invoice_amount') }}
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="due_date">{{ __('dashboard.due_date') }}</label>
                        <input type="date" class="form-control" name="due_date" id="due_date" value="{{ old('due_date') }}">

                        <div class="invalid-feedback">
                          {{ __('dashboard.please_provide') }} {{ __('dashboard.due_date') }}
                        </div>
                    </div>
                </div>
                <div class="card-footer">
                    <div class="form-group">
                        <button type="submit" class="btn btn-primary">{{ __('dashboard.send') }}</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    </form>
    <!-- end row-->

    
</div> <!-- container -->
<!-- End Content-->

@endsection