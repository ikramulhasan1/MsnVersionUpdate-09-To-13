@extends('admin.layouts.master')
@section('title', $title)
@section('content')
    <style>
        .ts-dropdown {
            background-color: #ffffff !important;
        }


        .case-services {
            grid-column: 1 / -1;
            display: flex;
            flex-wrap: wrap;
            gap: 12px;
            border: 1px solid #ccc;
        }

        .case-services label {
            background-color: #f0f0f0;
            padding: 5px 14px;
            border-radius: 30px;
            cursor: pointer;
            user-select: none;
            font-size: 14px;
            transition: all 0.3s ease;
            border: 1px solid #ccc;
            margin: 0px;
        }

        .case-services input {
            display: none;
        }

        .case-services input:checked+label {
            background-color: #3f7cf4;
            color: #fff;
            border-color: #3f7cf4;
        }

        .technologyCase {
            grid-column: 1 / -1;
            display: flex;
            flex-wrap: wrap;
            gap: 12px;
            border: 1px solid #ccc;
        }

        .technologyCase .label {
            background-color: #f0f0f0;
            padding: 5px 14px;
            border-radius: 30px;
            cursor: pointer;
            user-select: none;
            font-size: 14px;
            transition: all 0.3s ease;
            border: 1px solid #ccc;
            margin: 0px;
        }

        .technologyCase .input {
            display: none;
        }

        .technologyCase .input:checked+label {
            background-color: #00a830;
            color: #fff;
            border-color: #078700;
        }

        .nice-select>ul{
            width: 100%;
            margin-bottom: 20px !important;
            margin-top: 20px !important;
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
                <a href="{{ route($route . '.index') }}" class="btn btn-info">{{ __('dashboard.back') }}</a>
            </div>
        </div>

        <div class="row">
            <div class="col-12 col-lg-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="header-title">{{ __('dashboard.add') }} {{ $title }}</h4>
                    </div>
                    <form class="needs-validation" novalidate action="{{ route($route . '.update',$row->id) }}" method="post"
                        enctype="multipart/form-data">
                        @csrf
                        @method('PATCH')
                        <div class="card-body">

                            <!-- Form Start -->
                            <div class="form-group">
                                <label for="main_title">{{ __('dashboard.title') }} <span>*</span></label>
                                <input type="text" class="form-control" name="main_title" id="main_title"
                                    value="{{ $row->main_title }}" required>

                                <div class="invalid-feedback">
                                    {{ __('dashboard.please_provide') }} {{ __('dashboard.title') }}
                                </div>
                            </div>

                            <div class="row">
                                <div class="form-group col-4">
                                    <label for="the_client">{{ __('dashboard.the_client') }} <span>*</span></label>
                                    <input type="text" class="form-control" name="the_client" id="the_client"
                                        value="The Client" required>
                                    <div class="invalid-feedback">
                                        {{ __('dashboard.please_provide') }} {{ __('dashboard.the_client') }}
                                    </div>
                                </div>
                                <div class="form-group col-4">
                                    <label for="industry">{{ __('dashboard.industry') }} <span>* [Write a
                                            industry]</span></label>
                                    <input type="text" class="form-control" name="industry" id="industry"
                                        value="{{ $row->industry }}" required>
                                    <div class="invalid-feedback">
                                        {{ __('dashboard.please_provide') }} {{ __('dashboard.industry') }}
                                    </div>
                                </div>
                                <div class="form-group col-4">
                                    <label for="country">{{ __('dashboard.country') }} <span>*</span></label>
                                    <input type="text" class="form-control" name="country" id="country"
                                        value="{{ $row->country }}" required>

                                    <div class="invalid-feedback">
                                        {{ __('dashboard.please_provide') }} {{ __('dashboard.country') }}
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="the_client_desc">{{ __('dashboard.description') }} <span>*</span></label>
                                <textarea class="form-control" name="the_client_desc" id="editor1" rows="8"
                                    required>{!! $row->the_client_desc !!}</textarea>

                                <div class="invalid-feedback">
                                    {{ __('dashboard.please_provide') }} {{ __('dashboard.description') }}
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="tech_stack" class="form-label">Choose Your Skills</label>
                                <select id="tech_stack" name="tech_stack[]" multiple class="form-control"
                                    placeholder="Select tech stack...">
                                    @php
                                        $selectedTechs = is_array($row->tech_stack) ? $row->tech_stack : explode(',', $row->tech_stack ?? '');
                                    @endphp
                                    <option value="PHP" {{ in_array('PHP', $selectedTechs) ? 'selected' : '' }}>PHP</option>
                                    <option value="Laravel" {{ in_array('Laravel', $selectedTechs) ? 'selected' : '' }}>
                                        Laravel</option>
                                    <option value="WordPress Development" {{ in_array('WordPress Development', $selectedTechs) ? 'selected' : '' }}>
                                        WordPress Development</option>
                                    <option value="MySQL" {{ in_array('MySQL', $selectedTechs) ? 'selected' : '' }}>
                                        MySQL</option>
                                    <option value="Bootstrap" {{ in_array('Bootstrap', $selectedTechs) ? 'selected' : '' }}>
                                        Bootstrap</option>
                                    <option value="Responsive Web Design" {{ in_array('Responsive Web Design', $selectedTechs) ? 'selected' : '' }}>
                                        Responsive Web Design</option>
                                    <option value="SEO Optimization" {{ in_array('SEO Optimization', $selectedTechs) ? 'selected' : '' }}>
                                        SEO Optimization</option>
                                    <option value="Shopify Development" {{ in_array('Shopify Development', $selectedTechs) ? 'selected' : '' }}>
                                        Shopify Development</option>
                                    <option value="Performance Optimization" {{ in_array('Performance Optimization', $selectedTechs) ? 'selected' : '' }}>
                                        Performance Optimization</option>
                                    <option value="Vue.js" {{ in_array('Vue.js', $selectedTechs) ? 'selected' : '' }}>Vue.js
                                    </option>
                                    <option value="UI/UX Design" {{ in_array('UI/UX Design', $selectedTechs) ? 'selected' : '' }}>UI/UX Design
                                    </option>
                                    <option value="React" {{ in_array('React', $selectedTechs) ? 'selected' : '' }}>React
                                    </option>
                                    <option value="Node.js" {{ in_array('Node.js', $selectedTechs) ? 'selected' : '' }}>
                                        Node.js</option>
                                    <option value="JavaScript" {{ in_array('JavaScript', $selectedTechs) ? 'selected' : '' }}>JavaScript</option>
                                </select>
                                <div class="invalid-feedback">
                                    {{ __('dashboard.please_provide') }} {{ __('dashboard.description') }}
                                </div>
                            </div>

                            <label for="Services" class="form-label">Services</label>
                            <div id="Services" class="case-services p-2 mb-3">
                                @foreach($services as $service)
                                    <input 
                                        name="services[]" 
                                        type="checkbox" 
                                        value="{{ $service->id }}" 
                                        {{ (is_array(old('services', $row->services->pluck('id')->toArray())) && in_array($service->id, old('services', $row->services->pluck('id')->toArray()))) ? 'checked' : '' }} 
                                        id="services-{{ $service->id }}">
                                    <label for="services-{{ $service->id }}">{{ $service->short_title }}</label>
                                @endforeach
                            </div>


                            <label for="technology" class="form-label">Technology</label>
                            <div id="technology" class="technologyCase p-2 mb-3">
                                @foreach($technologies as $tech)
                                    <input name="technologies[]" value="{{ $tech->id }}" class="input" type="checkbox"  {{ (is_array(old('technologies', $row->technologies->pluck('id')->toArray())) && in_array($tech->id, old('technologies', $row->technologies->pluck('id')->toArray()))) ? 'checked' : '' }}  id="technologies-{{ $tech->id }}">
                                    <label class="label" for="technologies-{{ $tech->id }}">{{ $tech->short_title }}</label>
                                @endforeach
                            </div>

                            
                            <div class="form-group">
                                <label for="meta_title">{{ __('dashboard.meta_title') }} <span>*</span></label>
                                <input type="text" class="form-control" name="meta_title" id="meta_title"
                                    value="{{ $row->meta_title }}" required>

                                <div class="invalid-feedback">
                                    {{ __('dashboard.please_provide') }} {{ __('dashboard.meta_title') }}
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="meta_desc">{{ __('dashboard.meta_description') }} <span>*</span></label>
                                <textarea class="form-control" name="meta_desc" id="editor" rows="4"
                                    required>{!! $row->meta_desc !!}</textarea>

                                <div class="invalid-feedback">
                                    {{ __('dashboard.please_provide') }} {{ __('dashboard.meta_description') }}
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="keywords">{{ __('dashboard.meta_keywords') }} <span>*</span></label>
                                <input type="text" class="form-control tagin" data-tagin-separator=" " name="keywords"
                                    value="{{ $row->keywords }}" required>

                                <div class="invalid-feedback">
                                    {{ __('dashboard.please_provide') }} {{ __('dashboard.meta_keywords') }}
                                </div>
                            </div>
                            <div class="col-12 col-lg-12">
                                <div class="form-group">
                                    <label for="image">{{ __('dashboard.thumbnail') }} <span>*</span>
                                        <span>{{ __('dashboard.image_size', ['height' => 500, 'width' => 800]) }}</span></label>
                                    <input type="file" class="form-control" name="image" id="image">

                                    <div class="invalid-feedback">
                                        {{ __('dashboard.please_provide') }} {{ __('dashboard.thumbnail') }}
                                    </div>
                                </div>
                            </div>
                            <div class="col-3 col-lg-3">
                                <img class="w-100" src="{{ asset('uploads/case-study/'.$row->image_path) }}" alt="">
                            </div>
                            <input hidden type="text" class="form-control mb-1" value="1" name="status">

                            {{-- <h3>FAQs</h3>
                            <div class="row">

                                <div id="faq-wrapper" class="form-group col-9 faq-group mb-2">
                                    <input type="text" class="form-control mb-1" name="faqs[0][title]"
                                        placeholder="0. Question" required>
                                    <input type="text" class="form-control mb-1" name="faqs[0][description]"
                                        placeholder="0. Answer" required>

                                    <div class="invalid-feedback">
                                        {{ __('dashboard.please_provide') }} {{ __('dashboard.faq') }}
                                    </div>
                                </div>
                                <div class="form-group col-3">
                                    <button class="btn btn-success" type="button" onclick="addFaq()">{{
                                        __('dashboard.add_another_FAQ') }}</button>
                                </div>
                                <br><br>
                            </div> --}}
                            {{-- <input hidden type="text" class="form-control mb-1" name="type" value="service" required>
                            <input hidden type="text" class="form-control mb-1" name="category_id" value="12" required> --}}

                            <hr>
                            <h3>Case Study</h3>
                            <div class="row process-row">
                                <div class="form-group col-9 faq-group mb-2">
                                    @php
                                        $steps = json_decode($row->case_steps, true);
                                    @endphp
                                    @foreach ($steps as $index => $case)
                                        <div class="form-group col p-0 w-100">
                                            {{-- <input type="text" class="form-control mb-1" name="case[{{ $index }}][case_title]"
                                                placeholder="Title"> --}}
                                            <select name="case[{{ $index }}][case_title]" class="form-select w-100 mb-3"
                                                aria-label="Default select example" data-plugin="customselect">
                                                <option class=" w-100" selected value="{{ $case['case_title'] }}">{{ $case['case_title'] }}
                                                </option>
                                                <option value="Business Need">Business Need</option>
                                                <option value="The Challenges">The Challenges</option>
                                                <option value="Solution">Solution</option>
                                                {{-- <option value="Services Involved">Services Involved</option> --}}
                                                <option value="Results">Results</option>
                                                <option value="Benefits">Benefits</option>
                                                <option value="Key Features Delivered">Key Features Delivered</option>
                                                <option value="Client Testimonial">Client Testimonial</option>
                                                <option value="Conclusion">Conclusion</option>
                                            </select>
                                        </div>
                                    

                                        <textarea type="text" class="form-control mb-3" id="editor{{ 2+$index }}"
                                            name="case[{{ $index }}][case_description]" placeholder="Description">{!! $case['case_description'] ?? '' !!}</textarea>
                                        <input type="hidden" name="case[{{ $index }}][old_case_image]" value="{{ $case['case_image'] }}">
                                        <input type="file" class="form-control mb-3" name="case[{{ $index }}][case_image]">

                                        <div class="col-3 col-lg-3 mb-4">
                                            <img class="w-100" src="{{ asset('uploads/case-study/'.$case['case_image']) }}" alt="">
                                        </div>
                                    @endforeach
                                </div>
                                <div class="form-group col-3">
                                    <button class="btn btn-success" type="button"
                                        onclick="addProcess()">{{ __('dashboard.case-studies') }}</button>
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
                instanceReady: function (ev) {
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
                instanceReady: function (ev) {
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
        CKEDITOR.replace('editor2', {
            on: {
                instanceReady: function (ev) {
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
        CKEDITOR.replace('editor3', {
            on: {
                instanceReady: function (ev) {
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
        CKEDITOR.replace('editor4', {
            on: {
                instanceReady: function (ev) {
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
        CKEDITOR.replace('editor5', {
            on: {
                instanceReady: function (ev) {
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
        CKEDITOR.replace('editor6', {
            on: {
                instanceReady: function (ev) {
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
        CKEDITOR.replace('editor7', {
            on: {
                instanceReady: function (ev) {
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
        CKEDITOR.replace('editor8', {
            on: {
                instanceReady: function (ev) {
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
        let processIndexImg = 1;

        function addProcess() {
            const processWrapper = document.querySelector('.process-row');

            const processGroup = document.createElement('div');
            processGroup.classList.add('form-group', 'faq-group', 'col-9', 'mb-2');
            processGroup.innerHTML = `
            <div class="form-group col p-0">
                <label for="status">{{ __('dashboard.title') }}</label>
                <select class="wide w-100 p-1 rounded-0" style="font-size:17px" name="case[${processIndex}][case_title]">
                    <option value="Business Need">Business Need</option>
                    <option value="The Challenges">The Challenges</option>
                    <option value="Solution">Solution</option>
                    <option value="Results">Results</option>
                    <option value="Benefits">Benefits</option>
                    <option value="Key Features Delivered">Key Features Delivered</option>
                    <option value="Client Testimonial">Client Testimonial</option>
                    <option value="Conclusion">Conclusion</option>
                </select>
            </div>
            <textarea type="text" class="form-control mb-1" name="case[${processIndex}][case_description]" placeholder="${processIndex + 1}. Description"></textarea>
            <input type="file" class="form-control mb-1" name="case[${processIndexImg}][case_image]">
        `;

            // Insert before the last column (button)
            const processButtonContainer = processWrapper.querySelector('.col-3');
            processWrapper.insertBefore(processGroup, processButtonContainer);

            processIndex++;
            processIndexImg++;
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
                item: function (data, escape) {
                    return '<div class="item bg-primary text-white px-2 py-1 rounded me-1">' + escape(data.text) + '</div>';
                }
            }
        });
    </script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script>
        $(document).ready(function () {
            $('.select2').select2();
        });
    </script>

@endsection