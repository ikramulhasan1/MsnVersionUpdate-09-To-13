@extends('admin.layouts.master')
@section('title', $title)
@section('content')
<style>
    .ts-dropdown{
        background-color: #ffffff !important;
    }
</style>
<!-- Tom Select CSS -->
<link href="https://cdn.jsdelivr.net/npm/tom-select/dist/css/tom-select.bootstrap5.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />

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
                    <h4 class="header-title">{{ __('dashboard.add') }} {{ $title }}</h4>
                </div>
                <form class="needs-validation" novalidate action="{{ route($route.'.store') }}" method="post" enctype="multipart/form-data">
                    @csrf
                    <div class="card-body">

                        <!-- Form Start -->
                        <div class="form-group">
                            <label for="main_title">{{ __('dashboard.title') }} <span>*</span></label>
                            <input type="text" class="form-control" name="main_title" id="main_title" value="{{ old('main_title') }}" required>

                            <div class="invalid-feedback">
                                {{ __('dashboard.please_provide') }} {{ __('dashboard.title') }}
                            </div>
                        </div>
                        
                        <div class="row">                        
                            <div class="form-group col-4">
                                <label for="the_client">{{ __('dashboard.the_client') }} <span>*</span></label>
                                <input type="text" class="form-control" name="the_client" id="the_client" value="The Client" required>
                                <div class="invalid-feedback">
                                    {{ __('dashboard.please_provide') }} {{ __('dashboard.the_client') }}
                                </div>
                            </div>
                            <div class="form-group col-4">
                                <label for="industry">{{ __('dashboard.industry') }} <span>* [Write a industry]</span></label>
                                <input type="text" class="form-control" name="industry" id="industry" value="{{ old('industry') }}" required>
                                <div class="invalid-feedback">
                                    {{ __('dashboard.please_provide') }} {{ __('dashboard.industry') }}
                                </div>
                            </div>
                            <div class="form-group col-4">
                                <label for="country">{{ __('dashboard.country') }} <span>*</span></label>
                                <input type="text" class="form-control" name="country" id="country" value="{{ old('country') }}" required>
    
                                <div class="invalid-feedback">
                                    {{ __('dashboard.please_provide') }} {{ __('dashboard.country') }}
                                </div>
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label for="tech_stack" class="form-label">Choose Your Skills</label>
                            <select id="tech_stack" name="tech_stack[]" multiple class="form-control" placeholder="Select tech stack...">
                                <option value="PHP">PHP</option>
                                <option value="Laravel">Laravel</option>
                                <option value="Vue.js">Vue.js</option>
                                <option value="React">React</option>
                                <option value="Node.js">Node.js</option>
                                <option value="JavaScript">JavaScript</option>
                            </select>
                            <div class="invalid-feedback">
                                {{ __('dashboard.please_provide') }} {{ __('dashboard.description') }}
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="the_client_desc">{{ __('dashboard.description') }} <span>*</span></label>
                            <textarea class="form-control" name="the_client_desc" id="editor1" rows="8" required>{{ old('the_client_desc') }}</textarea>

                            <div class="invalid-feedback">
                                {{ __('dashboard.please_provide') }} {{ __('dashboard.description') }}
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="meta_title">{{ __('dashboard.meta_title') }} <span>*</span></label>
                            <input type="text" class="form-control" name="meta_title" id="meta_title" value="{{ old('meta_title') }}" required>

                            <div class="invalid-feedback">
                                {{ __('dashboard.please_provide') }} {{ __('dashboard.meta_title') }}
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="meta_desc">{{ __('dashboard.meta_description') }} <span>*</span></label>
                            <textarea class="form-control" name="meta_desc" id="editor" rows="4" required>{{ old('meta_desc') }}</textarea>

                            <div class="invalid-feedback">
                                {{ __('dashboard.please_provide') }} {{ __('dashboard.meta_description') }}
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
                        
                        {{-- <h3>FAQs</h3>
                        <div class="row">
                        
                            <div id="faq-wrapper" class="form-group col-9 faq-group mb-2">
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
                        </div> --}}
                        {{-- <input hidden type="text" class="form-control mb-1" name="type" value="service" required>
                        <input hidden type="text" class="form-control mb-1" name="category_id" value="12" required> --}}

                        <hr>
                        <h3>Case Study</h3>
                        <div class="row process-row">
                            <div class="form-group col-9 faq-group mb-2">
                                {{-- <input type="text" class="form-control mb-1" name="case[0][case_title]" placeholder="Title"> --}}
                                {{-- <select name="case[0][case_title]" class="form-select" aria-label="Default select example">
                                    <option selected value="The Challenges">The Challenges</option>
                                    <option value="Solutions We Offered">Solutions We Offered</option>
                                    <option value="Results">Results</option>
                                    <option value="Key Features Delivered">Key Features Delivered</option>
                                    <option value="Client Testimonial">Client Testimonial (if available)</option>
                                  </select> --}}

                                <div class="form-group col p-0">
                                    <label for="status">{{ __('dashboard.title') }}</label>
                                    <select class="wide" name="case[0][case_title]" id="status" data-plugin="customselect">
                                        <option selected value="The Challenges">The Challenges</option>
                                        <option value="Solutions We Offered">Solutions We Offered</option>
                                        <option value="Results">Results</option>
                                        <option value="Key Features Delivered">Key Features Delivered</option>
                                        <option value="Client Testimonial">Client Testimonial (if available)</option>
                                    </select>
                                </div>
                                <textarea type="text" class="form-control mb-1" name="case[0][case_description]" placeholder="Description"></textarea>
                                <input type="file" class="form-control mb-1" name="case[0][case_image]">
                               
                            </div>
                            <div class="form-group col-3">
                                <button class="btn btn-success" type="button" onclick="addProcess()">{{ __('dashboard.case-studies') }}</button>
                            </div>
                            <br><br>
                        </div>
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
    const wrapper = document.querySelector('.faq-group').parentNode;
    const group = document.createElement('div');
    group.classList.add('form-group', 'faq-group', 'col-9', 'mb-2');
    group.innerHTML = `
        ${faqIndex + 1}. 
        <input type="text" class="form-control mb-1" name="faqs[${faqIndex}][title]" placeholder="${faqIndex + 1}. Question" required>
        <input type="text" class="form-control mb-1" name="faqs[${faqIndex}][description]" placeholder="${faqIndex + 1}. Answer" required>
        <input type="hidden" class="form-control mb-1" name="faqs[${faqIndex}][type]" value="service" required>
        <select hidden name="faqs[${faqIndex}][category_id]">
            @foreach ($faqCategories as $category)
                <option value="{{ 12 }}">{{ $category->name }}</option>
            @endforeach
        </select>
    `;
    wrapper.appendChild(group);
    faqIndex++;
}



let processIndex = 1;

function addProcess() {
    const processWrapper = document.querySelector('.process-row');

    const processGroup = document.createElement('div');
    processGroup.classList.add('form-group', 'faq-group', 'col-9', 'mb-2');
    processGroup.innerHTML = `
        <div class="">
            <label for="status">{{ __('dashboard.title') }}</label>
             <select name="case[${processIndex}][case_title]" class="form-select select2" id="select2Example">
                 <option selected value="The Challenges">The Challenges</option>
                <option value="Solutions We Offered">Solutions We Offered</option>
                <option value="Results">Results</option>
                <option value="Key Features Delivered">Key Features Delivered</option>
                <option value="Client Testimonial">Client Testimonial (if available)</option>
            </select>

        </div>
        <textarea type="text" class="form-control mb-1" name="case[${processIndex}][case_description]" placeholder="${processIndex + 1}. Description"></textarea>
        <input type="file" class="form-control mb-1" name="case[${processIndex}][case_image]">
    `;

    // Insert before the last column (button)
    const processButtonContainer = processWrapper.querySelector('.col-3');
    processWrapper.insertBefore(processGroup, processButtonContainer);

    processIndex++;
}
</script>
<!-- Tom Select JS -->
<script src="https://cdn.jsdelivr.net/npm/tom-select/dist/js/tom-select.complete.min.js"></script>
<script>
    new TomSelect("#tech_stack", {
      plugins: ['remove_button'],
      persist: false,
      create: false,
      maxItems: null,
      render: {
        item: function(data, escape) {
          return '<div class="item bg-primary text-white px-2 py-1 rounded me-1">' + escape(data.text) + '</div>';
        }
      }
    });
  </script>
  <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
  <script>
    $(document).ready(function() {
      $('.select2').select2();
    });
  </script>
  
@endsection