@extends('admin.layouts.master')
@section('title', $title)
@section('content')
<!-- Tom Select CSS -->
<link href="https://cdn.jsdelivr.net/npm/tom-select/dist/css/tom-select.bootstrap5.min.css" rel="stylesheet">

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
                            <label for="title">{{ __('dashboard.title') }} <span>*</span></label>
                            <input type="text" class="form-control" name="title" id="title" value="{{ old('title') }}" required>

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
                            <label for="tags" class="form-label">Choose Your Skills</label>
                            <select id="tags" name="tags[]" multiple class="form-control" placeholder="Select tags...">
                                <option value="php">PHP</option>
                                <option value="laravel">Laravel</option>
                                <option value="vue">Vue.js</option>
                                <option value="react">React</option>
                                <option value="node">Node.js</option>
                                <option value="js">JavaScript</option>
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
                        <input hidden type="text" class="form-control mb-1" name="category_id" value="12" required>

                        <hr>
                        <h3>Work Process</h3>
                        <div class="row process-row">
                            <div class="form-group col-9 faq-group mb-2">
                                
                                <input type="text" class="form-control mb-1" name="workprocess[0][title]" placeholder="Title">
                                <input type="text" class="form-control mb-1" name="workprocess[0][description]" placeholder="Description">
                                <input type="file" class="form-control mb-1" name="workprocess[0][process_image]">
                               
                            </div>
                            <div class="form-group col-3">
                                <button class="btn btn-success" type="button" onclick="addProcess()">{{ __('dashboard.add_another_FAQ') }}</button>
                            </div>
                            <br><br>
                        </div>
                       
                        <!-- Form End -->
                        <div class="form-group">
                            <label for="manu">Manu</label>
                            <select class="wide" name="manu" id="manu" data-plugin="customselect">
                                <option value="0" >Hidden</option>
                                <option value="1" >Show</option>
                            </select>
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
        <input type="text" class="form-control mb-1" name="workprocess[${processIndex}][title]" placeholder="${processIndex + 1}. Title">
        <input type="text" class="form-control mb-1" name="workprocess[${processIndex}][description]" placeholder="${processIndex + 1}. Description">
        <input type="file" class="form-control mb-1" name="workprocess[${processIndex}][process_image]">
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
    new TomSelect("#tags", {
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
  
@endsection