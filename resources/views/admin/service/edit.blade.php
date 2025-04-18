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
                    @method('PATCH')
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
                            <label for="description">{{ __('dashboard.description') }} <span>*</span></label>
                            <textarea class="form-control" name="description" id="editor1" rows="8" required>{{ $row->description }}</textarea>

                            <div class="invalid-feedback">
                                {{ __('dashboard.please_provide') }} {{ __('dashboard.description') }}
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="meta_title">{{ __('dashboard.meta_title') }} <span>*</span></label>
                            <input type="text" class="form-control" name="meta_title" id="meta_title" value="{{ $row->meta_title }}" required>

                            <div class="invalid-feedback">
                                {{ __('dashboard.please_provide') }} {{ __('dashboard.meta_title') }}
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="short_desc">{{ __('dashboard.meta_description') }} <span>*</span></label>
                            <textarea class="form-control" name="short_desc" id="editor" rows="4" required>{{ $row->short_desc }}</textarea>

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
                            <label for="image">{{ __('dashboard.thumbnail') }} <span>{{ __('dashboard.image_size', ['height' => 500, 'width' => 800]) }}</span></label>
                            <input type="file" class="form-control" name="image" id="image">

                            <div class="invalid-feedback">
                                {{ __('dashboard.please_provide') }} {{ __('dashboard.thumbnail') }}
                            </div>
                        </div>

                        <div class="row">
                            <div class="form-group col">
                                <label for="price">{{ __('dashboard.price') }} <span>* </span></label>
                                <input type="number" class="form-control" name="price" id="price" value="{{ $row->price }}" required>
                                <div class="invalid-feedback">
                                    {{ __('dashboard.please_provide') }} {{ __('dashboard.price') }}
                                </div>
                            </div>
                            <div class="form-group col">
                                <label for="starting_price">{{ __('dashboard.starting_price') }} <span>*</span></label>
                                <input type="number" class="form-control" name="starting_price" id="starting_price" value="{{ $row->starting_price }}" required>
    
                                <div class="invalid-feedback">
                                    {{ __('dashboard.please_provide') }} {{ __('dashboard.starting_price') }}
                                </div>
                            </div>
                            <div class="form-group col">
                                <label for="review_count">{{ __('dashboard.review_count') }} <span>*</span></label>
                                <input type="number" class="form-control" name="review_count" id="review_count" value="{{ $row->review_count }}" required>
    
                                <div class="invalid-feedback">
                                    {{ __('dashboard.please_provide') }} {{ __('dashboard.review_count') }}
                                </div>
                            </div>
                            <div class="form-group col">
                                <label for="priceCurrency">{{ __('dashboard.priceCurrency') }} <span>*</span></label>
                                <input type="text" class="form-control" name="priceCurrency" id="priceCurrency" value="{{ $row->priceCurrency }}" required>
    
                                <div class="invalid-feedback">
                                    {{ __('dashboard.please_provide') }} {{ __('dashboard.priceCurrency') }}
                                </div>
                            </div>
                            <div class="form-group col">
                                <label for="average_rating">{{ __('dashboard.average_rating') }} <span>*</span></label>
                                <input type="text" class="form-control" name="average_rating" id="average_rating" value="{{ $row->average_rating }}" required>
    
                                <div class="invalid-feedback">
                                    {{ __('dashboard.please_provide') }} {{ __('dashboard.average_rating') }}
                                </div>
                            </div>
                        </div>
                        <hr>
                        <h3>FAQs</h3>
                        <div class="row">
                        
                            <div id="faq-wrapper" class="form-group col-9 faq-group mb-2">
                                {{-- <label for="average_rating">{{ __('dashboard.average_rating') }} <span>*</span></label> --}}
                                <input type="text" class="form-control mb-1" name="faqs[0][title]" placeholder="0. Question" required>
                                <input type="text" class="form-control mb-1" name="faqs[0][description]" placeholder="0. Answer" required>
    
                                <div class="invalid-feedback">
                                    {{ __('dashboard.please_provide') }} {{ __('dashboard.faq') }}
                                </div>
                            </div>
                            <div class="form-group col-3">
                                <button class="btn btn-success" type="button" onclick="addFaq()">{{ __('dashboard.add_another_FAQ') }}</button>
                            </div>
                            <br><br>
                        </div>
                        <input hidden type="text" class="form-control mb-1" name="type" value="service" required>

                        <div class="row">
                            <div class="form-group col">
                                <label for="manu">Manu</label>
                                <select class="wide" name="manu" id="manu" data-plugin="customselect">
                                    <option value="0" @if( $row->manu == 0 ) selected @endif>Hidden</option>
                                    <option value="1" @if( $row->manu == 1 ) selected @endif>Show</option>
                                </select>
                            </div>

                            <div class="form-group col">
                                <label for="status">{{ __('dashboard.select_status') }}</label>
                                <select class="wide" name="status" id="status" data-plugin="customselect">
                                    <option value="1" @if( $row->status == 1 ) selected @endif>{{ __('dashboard.active') }}</option>
                                    <option value="0" @if( $row->status == 0 ) selected @endif>{{ __('dashboard.inactive') }}</option>
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


    


    // FAQs Section
    let faqIndex = 1;

    function addFaq() {
        const wrapper = document.getElementById('faq-wrapper');
        const group = document.createElement('div');
        group.classList.add('form-group');
        group.classList.add('faq-group');
        group.classList.add('mb-2');
        group.innerHTML = `
            <input type="text" class="form-control mb-1" name="faqs[${faqIndex}][question]" placeholder="${faqIndex}. Question" required>
            <input type="text" class="form-control mb-1" name="faqs[${faqIndex}][answer]" placeholder="${faqIndex+2-2}. Answer" required>
        `;
        wrapper.appendChild(group);
        faqIndex++;
    }
</script>
@endsection