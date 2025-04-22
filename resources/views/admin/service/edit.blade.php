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
                        <div class="row faq-row">
                       
                            @foreach ($row->faqs as $key => $faq)
                            <div class="form-group col-10 faq-group mb-2 row">
                                <div class="col-1">
                                    {{ $key+1 }}. 
                                </div>
                                <div class="col-11">
                                    <input type="text" class="form-control mb-1" name="faqs[{{ $key }}][title]" value="{{ $faq->title }}" placeholder="{{ $key+1 }}. Question" required>
                                    <input type="text" class="form-control mb-1" name="faqs[{{ $key }}][description]" value="{{ $faq->description }}" placeholder="{{ $key+1 }}. Answer" required>
                                    <input type="hidden" class="form-control mb-1" name="type" value="{{ $faq->type }}" required>
                                    <select hidden name="faqs[{{ $key }}][category_id]">
                                        @foreach ($faqCategories as $category)
                                            <option value="{{ 12 }}" @if($category->id == $faq->category_id) selected @endif>{{ $category->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            @endforeach
                        
                            <div class="form-group col-2">
                                <button class="btn btn-success" type="button" onclick="addFaq()">{{ __('dashboard.add_another_FAQ') }}</button>
                            </div>
                            <br><br>
                        </div>
                        <hr>
                       
                        <h3>Work Process</h3>
                        <div class="row process-row">
                       
                            @foreach ($row->processworks as $key => $process)
                            <div class="form-group col-10 faq-group mb-2 row">
                                <div class="col-1">
                                    {{ $key+1 }}. 
                                </div>
                                <div class="col-11">
                                    <input type="text" class="form-control mb-1" name="workprocess[{{ $key }}][title]" value="{{ $process->title }}" placeholder="{{ $key+1 }}. Title">
                                    <input type="text" class="form-control mb-1" name="workprocess[{{ $key }}][description]" value="{{ $process->description }}" placeholder="{{ $key+1 }}. Description">
                                    <div class="d-flex">
                                        <input type="file" class="form-control mb-1 mr-3 w-75" name="workprocess[{{ $key }}][process_image]">
                                    <img style="width: 40px; height: 40px;" src="{{ asset('uploads/process/' . $process->image_path) }}" class="process-step-icon" alt="">
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        
                            <div class="form-group col-2">
                                <button class="btn btn-success" type="button" onclick="addProcess()">{{ __('dashboard.add_work_process') }}</button>
                            </div>
                            <br><br>
                        </div>
                        <hr>
                       
                        <h3>Industries Serve</h3>
                        <div class="row industry-row">
                       
                            @foreach ($row->industries as $key => $industry)
                            <div class="form-group col-10 industry-group mb-2 row" id="industry-{{ $we->id }}">
                                <div class="col-1">
                                    {{ $key+1 }}.
                                </div> 
                                <div class="col-8">
                                    <input type="text" class="form-control mb-1" name="industries[{{ $key }}][title]" value="{{ $industry->title }}" placeholder="{{ $key+1 }}. Title" required>                                
                                    <input type="text" class="form-control mb-1" name="industries[{{ $key }}][link]" value="{{ $industry->link }}" placeholder="{{ $key+1 }}. Link">                                
                                </div>
                                <div class="col-1">
                                    <button type="button" class="btn btn-danger btn-sm" onclick="removeIndustry(this)">✕</button>
                                </div>
                                <div class="col-1">
                                    <button type="button" class="btn btn-danger btn-sm" onclick="deleteIndustry({{ $industry->id }})">🗑️ Delete</button>
                                </div>
                            </div>
                            @endforeach
                        
                            <div class="form-group col-2">
                                <button class="btn btn-success" type="button" onclick="addIndustry()">{{ __('dashboard.industry') }}</button>
                            </div>
                            <br><br>
                        </div>
                       
                        <hr>
                       
                        <h3>Why We</h3>
                        <div class="row whywes-row">
                            @foreach ($row->whywes as $key => $we)
                            <div class="form-group col-10 whywes-group mb-2 row align-items-center" id="whywes-{{ $we->id }}">
                                <div class="col-1">
                                    {{ $key+1 }}.
                                </div> 
                                <div class="col-8">
                                    <input type="text" class="form-control mb-1" name="whywes[{{ $key }}][title]" value="{{ $we->title }}" placeholder="{{ $key+1 }}. Title" required>                                
                                    <input type="text" class="form-control mb-1" name="whywes[{{ $key }}][link]" value="{{ $we->link }}" placeholder="{{ $key+1 }}. Link">                                
                                </div>
                                <div class="col-1">
                                    <button type="button" class="btn btn-danger btn-sm" onclick="removeWhyWe(this)">✕</button>
                                </div>
                                <div class="col-1">
                                    <button type="button" class="btn btn-danger btn-sm" onclick="deleteWhyWe({{ $we->id }})">🗑️ Delete</button>
                                </div>
                            </div>
                            @endforeach
                        
                            <div class="form-group col-2">
                                <button class="btn btn-success" type="button" onclick="addWhywes()">{{ __('dashboard.why_wes') }}</button>
                            </div>
                            <br><br>
                        </div>
                       
                        
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


// Initial index count
  let faqIndex = {{ count($row->faqs) }};

// Render all category options as string
const categoryOptions = `{!! collect($faqCategories)->map(fn($c) => "<option value='{$c->id}'>{$c->name}</option>")->implode('') !!}`;

function addFaq() {
    const wrapper = document.querySelector('.faq-row');

    const group = document.createElement('div');
    group.classList.add('form-group', 'faq-group', 'col-10', 'mb-2');
    group.innerHTML = `
        <input type="text" class="form-control mb-1" name="faqs[${faqIndex}][title]" placeholder="${faqIndex + 1}. Question" required>
        <input type="text" class="form-control mb-1" name="faqs[${faqIndex}][description]" placeholder="${faqIndex + 1}. Answer" required>
        <input type="hidden" name="faqs[${faqIndex}][type]" value="service">
        <select hidden name="faqs[${faqIndex}][category_id]">
            ${categoryOptions}
        </select>
    `;

    // Insert before the last column (button)
    const buttonContainer = wrapper.querySelector('.col-2');
    wrapper.insertBefore(group, buttonContainer);

    faqIndex++;
}



  let processIndex = {{ count($row->processworks) }};

// Render all category options as string

function addProcess() {
    const processWrapper = document.querySelector('.process-row');

    const processGroup = document.createElement('div');
    processGroup.classList.add('form-group', 'faq-group', 'col-10', 'mb-2');
    processGroup.innerHTML = `
        <input type="text" class="form-control mb-1" name="workprocess[${processIndex}][title]" placeholder="${processIndex + 1}. Title">
        <input type="text" class="form-control mb-1" name="workprocess[${processIndex}][description]" placeholder="${processIndex + 1}. Description">
        <input type="file" class="form-control mb-1" name="workprocess[${processIndex}][process_image]">
    `;

    // Insert before the last column (button)
    const processButtonContainer = processWrapper.querySelector('.col-2');
    processWrapper.insertBefore(processGroup, processButtonContainer);

    processIndex++;
}



let industryIndex = {{ count($row->industries) }};

function addIndustry() {
    const industryWrapper = document.querySelector('.industry-row');

    const industryGroup = document.createElement('div');
    industryGroup.classList.add('form-group', 'industry-group', 'col-10', 'mb-2');
    industryGroup.innerHTML = `
        <input type="text" class="form-control mb-1" name="industries[${industryIndex}][title]" placeholder="${industryIndex + 1}. Title">
        <input type="text" class="form-control mb-1" name="industries[${industryIndex}][link]" placeholder="${industryIndex + 1}. Link">
    `;

    // Insert before the last column (button)
    const industryButtonContainer = industryWrapper.querySelector('.col-2');
    industryWrapper.insertBefore(industryGroup, industryButtonContainer);

    industryIndex++;
}


function removeIndustry(button) {
    const row = button.closest('.industry-group');
    if (row) {
        row.remove();
    }
}

const csrfToken = '{{ csrf_token() }}';

function deleteIndustry(id) {
    if (!confirm('Are you sure you want to delete this item permanently?')) return;

    fetch(`/admin/industries/${id}`, {
        method: 'DELETE',
        headers: {
            'X-CSRF-TOKEN': csrfToken,
            'Accept': 'application/json'
        }
    }).then(response => response.json())
      .then(data => {
        if (data.success) {
            document.getElementById(`industry-${id}`).remove();
        } else {
            alert('Failed to delete.');
        }
    }).catch(error => {
        alert('Error occurred.');
        console.error(error);
    });
}



// 
let whywesIndex = {{ count($row->whywes) }};

function addWhywes() {
    const whywesWrapper = document.querySelector('.whywes-row');

    const whywesGroup = document.createElement('div');
    whywesGroup.classList.add('form-group', 'whywes-group', 'col-10', 'mb-2');
    whywesGroup.innerHTML = `
        <input type="text" class="form-control mb-1" name="whywes[${whywesIndex}][title]" placeholder="${whywesIndex + 1}. Title">
        <input type="text" class="form-control mb-1" name="whywes[${whywesIndex}][link]" placeholder="${whywesIndex + 1}. Link">
    `;

    // Insert before the last column (button)
    const whywesButtonContainer = whywesWrapper.querySelector('.col-2');
    whywesWrapper.insertBefore(whywesGroup, whywesButtonContainer);

    whywesIndex++;
}

function removeWhyWe(button) {
    const row = button.closest('.whywes-group');
    if (row) {
        row.remove();
    }
}

// const csrfToken = '{{ csrf_token() }}';

function deleteWhyWe(id) {
    if (!confirm('Are you sure you want to delete this item permanently?')) return;

    fetch(`/admin/whywes/${id}`, {
        method: 'DELETE',
        headers: {
            'X-CSRF-TOKEN': csrfToken,
            'Accept': 'application/json'
        }
    }).then(response => response.json())
      .then(data => {
        if (data.success) {
            document.getElementById(`whywes-${id}`).remove();
        } else {
            alert('Failed to delete.');
        }
    }).catch(error => {
        alert('Error occurred.');
        console.error(error);
    });
}

</script>
@endsection