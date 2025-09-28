@extends('admin.layouts.master')
{{-- @section('title', $title) --}}
@section('content')

    <!-- Start Content-->
    <div class="container-fluid">

        <!-- start page title -->
        <!-- Include page breadcrumb -->
        @include('admin.inc.breadcrumb')
        <!-- end page title -->


        <div class="row">
            <div class="col-12">
                <a href="{{ route('admin.technologies.index') }}" class="btn btn-info">{{ __('dashboard.back') }}</a>
            </div>
        </div>

        <div class="row">
            <div class="col-12 col-lg-12">
                <div class="card">
                    <div class="card-header">
                        {{-- <h4 class="header-title">{{ __('dashboard.add') }} {{ $title }}</h4> --}}
                    </div>
                    <form class="needs-validation" novalidate action="{{ route('admin.technologies.store') }}" method="post"
                        enctype="multipart/form-data">
                        @csrf
                        <div class="card-body">
                            <div class="form-group">
                                <label for="status">{{ __('dashboard.select_service_id') }}</label>
                                <select class="wide" name="service_id" id="status" data-plugin="customselect">
                                    @foreach ($services as $service)
                                        <option value="{{$service->id}}">{{$service->title }}</option>
                                    @endforeach

                                </select>
                            </div>
                            <!-- Form Start -->
                            <div class="form-group">
                                <label for="title">{{ __('Banner title') }} <span>*</span></label>
                                <input type="text" class="form-control" name="title" id="title" value="{{ old('title') }}"
                                    required>

                                <div class="invalid-feedback">
                                    {{ __('dashboard.please_provide') }} {{ __('dashboard.title') }}
                                </div>
                            </div>


                            <div class="row">
                                <div class="form-group col-6">
                                    <label for="slug">{{ __('dashboard.slug') }} <span>* [Write a unique
                                            slug]</span></label>
                                    <input type="text" class="form-control" name="slug" id="slug" value="{{ old('slug') }}"
                                        required>
                                    <div class="invalid-feedback">
                                        {{ __('dashboard.please_provide') }} {{ __('dashboard.slug') }}
                                    </div>
                                </div>
                                <div class="form-group col-6">
                                    <label for="short_title">{{ __('dashboard.short_title') }} <span>*</span></label>
                                    <input type="text" class="form-control" name="short_title" id="short_title"
                                        value="{{ old('short_title') }}" required>

                                    <div class="invalid-feedback">
                                        {{ __('dashboard.please_provide') }} {{ __('dashboard.short_title') }}
                                    </div>
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="description">{{ __('Banner Description') }} <span>*</span></label>
                                <textarea class="form-control" name="description" id="editor1" rows="8"
                                    required>{{ old('description') }}</textarea>

                                <div class="invalid-feedback">
                                    {{ __('dashboard.please_provide') }} {{ __('dashboard.description') }}
                                </div>
                            </div>
                            <hr>
                            <h3>Toggle</h3>
                            <div class="row process-row-toggle">
                                <div class="form-group col-9 faq-group mb-2">
                                    <input type="text" class="form-control mb-1" name="tech[0][tech_title]"
                                        placeholder="Title">
                                    <textarea id="editor2" type="text" class="form-control mb-1"
                                        name="tech[0][tech_description]" placeholder="Description"></textarea>
                                </div>
                                <div class="form-group col-3">
                                    <button class="btn btn-success" type="button"
                                        onclick="addProcess()">{{ __('Toggle add') }}</button>
                                </div>
                                <br><br>
                            </div>

                            <hr>
                            <h3>Expertise Section</h3>
                            <div class="row process-row-expertise">
                                <div class="form-group col-9 faq-group mb-2">
                                    <input type="text" class="form-control mb-1" name="expertise[0][expertise_url]"
                                        placeholder="Url">
                                    <input type="file" class="form-control mb-1" name="expertise[0][expertise_image]">
                                </div>
                                <div class="form-group col-3">
                                    <button class="btn btn-success" type="button"
                                        onclick="addExpertise()">{{ __('Expertise add') }}</button>
                                </div>
                                <br><br>
                            </div>

                            <div class="form-group">
                                <label for="meta_title">{{ __('dashboard.meta_title') }} <span>*</span></label>
                                <input type="text" class="form-control" name="meta_title" id="meta_title"
                                    value="{{ old('meta_title') }}" required>

                                <div class="invalid-feedback">
                                    {{ __('dashboard.please_provide') }} {{ __('dashboard.meta_title') }}
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="short_desc">{{ __('dashboard.meta_description') }} <span>*</span></label>
                                <textarea class="form-control" name="short_desc" id="editor" rows="4"
                                    required>{{ old('short_desc') }}</textarea>

                                <div class="invalid-feedback">
                                    {{ __('dashboard.please_provide') }} {{ __('dashboard.meta_description') }}
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="keywords">{{ __('dashboard.meta_keywords') }} <span>*</span></label>
                                <input type="text" class="form-control tagin" data-tagin-separator=" " name="keywords"
                                    value="{{ old('keywords') }}" required>

                                <div class="invalid-feedback">
                                    {{ __('dashboard.please_provide') }} {{ __('dashboard.meta_keywords') }}
                                </div>
                            </div>
                            <div class="row">
                                <div class="form-group col-6">
                                    <label for="image">{{ __('CTA Image') }} <span>*</span>
                                        <span>{{ __('dashboard.image_size', ['height' => 500, 'width' => 800]) }}</span></label>
                                    <input type="file" class="form-control" name="image" id="image" required>

                                    <div class="invalid-feedback">
                                        {{ __('dashboard.please_provide') }} {{ __('dashboard.thumbnail') }}
                                    </div>
                                </div>
                                <div class="form-group col-6">
                                    <label for="logo">{{ __('dashboard.logo') }} <span>*</span>
                                        <span>{{ __('dashboard.image_size', ['height' => 100, 'width' => 100]) }}</span></label>
                                    <input type="file" class="form-control" name="logo" id="logo" required>

                                    <div class="invalid-feedback">
                                        {{ __('dashboard.please_provide') }} {{ __('dashboard.logo') }}
                                    </div>
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
                                    <input type="number" class="form-control" name="starting_price" id="starting_price"
                                        value="499" required>

                                    <div class="invalid-feedback">
                                        {{ __('dashboard.please_provide') }} {{ __('dashboard.starting_price') }}
                                    </div>
                                </div>
                                <div class="form-group col">
                                    <label for="review_count">{{ __('dashboard.review_count') }} <span>*</span></label>
                                    <input type="number" class="form-control" name="review_count" id="review_count"
                                        value="150" required>

                                    <div class="invalid-feedback">
                                        {{ __('dashboard.please_provide') }} {{ __('dashboard.review_count') }}
                                    </div>
                                </div>
                                <div class="form-group col">
                                    <label for="priceCurrency">{{ __('dashboard.priceCurrency') }} <span>*</span></label>
                                    <input type="text" class="form-control" name="priceCurrency" id="priceCurrency"
                                        value="USD" required>

                                    <div class="invalid-feedback">
                                        {{ __('dashboard.please_provide') }} {{ __('dashboard.priceCurrency') }}
                                    </div>
                                </div>
                                <div class="form-group col">
                                    <label for="average_rating">{{ __('dashboard.average_rating') }} <span>*</span></label>
                                    <input type="text" class="form-control" name="average_rating" id="average_rating"
                                        value="4.9" required>

                                    <div class="invalid-feedback">
                                        {{ __('dashboard.please_provide') }} {{ __('dashboard.average_rating') }}
                                    </div>
                                </div>
                            </div>
                            <!-- Form End -->
                            <div class="form-group">
                                <label for="manu">Manu</label>
                                <select class="wide" name="manu" id="manu" data-plugin="customselect">
                                    <option value="0">Hidden</option>
                                    <option value="1">Show</option>
                                </select>
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

        // ---- initCKEditor helper (উভয় CKEditor v5/v4 সমর্থন করে) ----
        function initCKEditor(id) {
            const el = document.getElementById(id);
            if (!el) {
                console.warn('initCKEditor: element not found ->', id);
                return;
            }

            // CKEditor 5 (Classic)
            if (window.ClassicEditor) {
                // যদি আগে থেকেই ইনস্ট্যান্স থাকে সেভাবেই ধরে রাখুন
                if (el._ckEditorInstance) {
                    return;
                }
                ClassicEditor.create(el).then(editor => {
                    el._ckEditorInstance = editor; // future destroy করতে চাইলে রাখছি
                }).catch(err => console.error(err));
                return;
            }

            // CKEditor 4
            if (window.CKEDITOR) {
                if (CKEDITOR.instances[id]) {
                    try { CKEDITOR.instances[id].destroy(true); } catch (e) { }
                }
                CKEDITOR.replace(id);
                return;
            }

            console.warn('No CKEditor found on the page.');
        }

        // ---- একবারই declare ----
        let processIndex = 1;
        let processIndexImg = 1;
        let placeholderIndex = 2;

        document.addEventListener("DOMContentLoaded", () => {
            const existing = document.querySelectorAll("textarea[id^='editor']");
            existing.forEach(el => initCKEditor(el.id));

            processIndex = existing.length + 1;
            processIndexImg = existing.length + 1;
            placeholderIndex = existing.length + 2;
        });

        // ---- common creator ----
        function createProcessGroup({ index, placeholder, namePrefix }) {
            const editorId = `editor${index}`;
            const wrapper = document.createElement('div');
            wrapper.className = 'form-group faq-group col-9 mb-2';
            wrapper.innerHTML = `
                <div class="form-group col p-0">
                    <input type="text" class="form-control mb-1" name="${namePrefix}[${index}][${namePrefix}_title]"
                           placeholder="Title">
                </div>
                <textarea id="${editorId}" class="form-control mb-1"
                    name="${namePrefix}[${index}][${namePrefix}_description]"
                    placeholder="Description"></textarea>
            `;
            return { wrapper, editorId };
        }

        // ---- নতুন row যোগ করার generic ফাংশন ----
        function addRow(namePrefix = 'tech') {
            const processWrapper = document.querySelector('.process-row');
            if (!processWrapper) return console.warn('.process-row not found');

            const index = processIndex++;
            const placeholder = placeholderIndex++;
            processIndexImg++;

            const { wrapper, editorId } = createProcessGroup({ index, placeholder, namePrefix });
            const processButtonContainer = processWrapper.querySelector('.col-3');
            processWrapper.insertBefore(wrapper, processButtonContainer);

            setTimeout(() => initCKEditor(editorId), 0);
        }

        // ---- আলাদা function শুধু prefix অনুযায়ী ----
        function addProcess() {
            const processWrapper = document.querySelector('.process-row-toggle');
            if (!processWrapper) return;

            const index = processIndex++;
            const placeholder = placeholderIndex++;
            processIndexImg++;

            const editorId = `editor${index}`;

            const group = document.createElement('div');
            group.className = 'form-group faq-group col-9 mb-2';
            group.innerHTML = `
            <div class="form-group col p-0">
                <input type="text" class="form-control mb-1" name="tech[${index}][tech_title]"
                    placeholder="Title">
            </div>
            <textarea id="${editorId}" class="form-control mb-1"
                name="tech[${index}][tech_description]"
                placeholder="Description"></textarea>
        `;

            const btnContainer = processWrapper.querySelector('.col-3');
            processWrapper.insertBefore(group, btnContainer);

            setTimeout(() => initCKEditor(editorId), 0);
        }

        function addExpertise() {
            const processWrapper = document.querySelector('.process-row-expertise');
            if (!processWrapper) return;

            const index = processIndexImg++;
            const placeholder = placeholderIndex++;

            const group = document.createElement('div');
            group.className = 'form-group faq-group col-9 mb-2';
            group.innerHTML = `
            <input type="text" class="form-control mb-1" name="expertise[${index}][expertise_url]"
                placeholder="Url">
            <input type="file" class="form-control mb-1"
                name="expertise[${index}][expertise_image]">
        `;

            const btnContainer = processWrapper.querySelector('.col-3');
            processWrapper.insertBefore(group, btnContainer);
        }

    </script>
@endsection