@extends('admin.layouts.master')
@section('title', $title)
@section('content')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/choices.js/public/assets/styles/choices.min.css" />

    @php
        // Prevent undefined variable errors for Create page
        $screenshotImage = $screenshotImage ?? [];
    @endphp
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
                    <form class="needs-validation" novalidate action="{{ route($route . '.store') }}" method="post"
                        enctype="multipart/form-data">
                        @csrf
                        <div class="card-body">

                            <!-- Form Start -->
                            <div class="form-group">
                                <label for="title">{{ __('dashboard.title') }} <span>*</span></label>
                                <input type="text" class="form-control" name="title" id="title"
                                    value="{{ old('title') }}" required>

                                <div class="invalid-feedback">
                                    {{ __('dashboard.please_provide') }} {{ __('dashboard.title') }}
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="subtitle">{{ __('dashboard.sub_title') }} <span>*</span></label>
                                <input type="text" class="form-control" name="sub_title" id="subtitle"
                                    value="{{ old('sub_title') }}" required>

                                <div class="invalid-feedback">
                                    {{ __('dashboard.please_provide') }} {{ __('dashboard.sub_title') }}
                                </div>
                            </div>
                            <div class="row">
                                <div class="form-group col-lg-6 col-md-6 col-12">
                                    <label for="client">{{ __('dashboard.client') }} <span>*</span></label>
                                    <input type="text" class="form-control" name="client" id="client"
                                        value="{{ old('client') }}" required>

                                    <div class="invalid-feedback">
                                        {{ __('dashboard.please_provide') }} {{ __('dashboard.client') }}
                                    </div>
                                </div>
                                <div class="form-group col-lg-6 col-md-6 col-12">
                                    <label for="date">{{ __('dashboard.date') }} <span>*</span></label>
                                    <input type="date" class="form-control" name="date" id="date"
                                        value="{{ old('date') }}" required>

                                    <div class="invalid-feedback">
                                        {{ __('dashboard.please_provide') }} {{ __('dashboard.date') }}
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="form-group col-lg-6 col-md-6 col-12">
                                    <label for="category">{{ __('dashboard.category') }} <span>*</span></label>
                                    <select class="select2 form-control select2-multiple" data-toggle="select2"
                                        multiple="multiple" data-placeholder="{{ __('dashboard.select') }}"
                                        name="categories[]" id="category" required>
                                        @foreach ($categories as $category)
                                            <option value="{{ $category->id }}"
                                                @if (old('category') == $category->id) selected @endif>{{ $category->title }}
                                            </option>
                                        @endforeach
                                    </select>

                                    <div class="invalid-feedback">
                                        {{ __('dashboard.please_provide') }} {{ __('dashboard.category') }}
                                    </div>
                                </div>
                                <div class="form-group mb-4 col-lg-6 col-md-6 col-12">
                                    <label for="technologies"
                                        class="block text-sm font-medium text-gray-700 mb-1">Technologies</label>
                                    <select name="technologies[]" id="technologies" multiple>
                                        @foreach ($allTechnologies as $tech)
                                            <option value="{{ $tech->id }}">
                                                {{ $tech->short_title }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <hr>
                            <h3>Screenshot Section</h3>
                            <div class="row banner-row">

                                @foreach ($screenshotImage ?? [] as $key => $screenshot_step)
                                    <div class="form-group col-10 banner-group mb-2 row">
                                        <div class="col-1">
                                            {{ $key + 1 }}.
                                        </div>
                                        <div class="col-11">

                                            <div class="d-flex">
                                                <input type="file" class="form-control mb-1 mr-3 w-75"
                                                    name="screenshot[{{ $key }}][screenshot_image]">
                                                <img style="width: 40px; height: 40px;"
                                                    src="{{ asset('uploads/screenshot/' . $screenshot_step->screenshot_image) }}"
                                                    class="process-step-icon" alt="">
                                            </div>
                                        </div>
                                    </div>
                                @endforeach

                                <div class="form-group col-2">
                                    <button class="btn btn-success" type="button" onclick="addScreenshot()">Add
                                        Screenshot</button>
                                </div>
                                <br><br>
                            </div>
                            <hr>
                            <h3>Results & Impact:</h3>
                            <div class="row">

                                <div id="faq-wrapper" class="form-group col-9 faq-group mb-2">
                                    {{-- <label for="average_rating">{{ __('dashboard.average_rating') }}
                                        <span>*</span></label> --}}
                                    <input type="text" class="form-control mb-1" name="icon[0][icon_class]"
                                        placeholder="0. Icon Class">
                                    <input type="text" class="form-control mb-1" name="icon[0][title]"
                                        placeholder="0. Title">
                                    <input type="text" class="form-control mb-1" name="icon[0][description]"
                                        placeholder="0. Description">

                                    <div class="invalid-feedback">
                                        {{ __('dashboard.please_provide') }} {{ __('dashboard.faq') }}
                                    </div>
                                </div>
                                <div class="form-group col-3">
                                    <button class="btn btn-success" type="button"
                                        onclick="addFaq()">{{ __('Results & Impact') }}</button>
                                </div>
                                <br><br>
                            </div>
                            <div class="form-group">
                                <label for="description">Overview<span>*</span></label>
                                <textarea class="form-control" name="description" id="editor" rows="8" required>{{ old('description') }}</textarea>

                                <div class="invalid-feedback">
                                    {{ __('dashboard.please_provide') }} {{ __('dashboard.description') }}
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="link2">The Challenge</label>
                                <textarea class="form-control" name="link2" id="editor1" rows="8">{{ old('link2') }}</textarea>
                                <div class="invalid-feedback">
                                    {{ __('dashboard.please_provide') }} {{ __('dashboard.web_link') }}
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="image">Laptop view<span>*</span>
                                    <span>{{ __('dashboard.image_size', ['height' => 390, 'width' => 1270]) }}</span></label>
                                <input type="file" class="form-control" name="image" id="image" required>

                                <div class="invalid-feedback">
                                    {{ __('dashboard.please_provide') }} {{ __('dashboard.thumbnail') }}
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="overview_image">Mobile view<span>*</span>
                                    <span>{{ __('dashboard.image_size', ['height' => 500, 'width' => 800]) }}</span></label>
                                <input type="file" class="form-control" name="overview_image" id="overview_image"
                                    required>

                                <div class="invalid-feedback">
                                    {{ __('dashboard.please_provide') }} {{ __('dashboard.overview_image') }}
                                </div>
                            </div>

                            {{-- <div class="form-group">
                                <label for="video_id">{{ __('dashboard.youtube_video_id') }}</label>
                                <input type="text" class="form-control" name="video_id" id="video_id"
                                    value="{{ old('video_id') }}">

                                <div class="invalid-feedback">
                                    {{ __('dashboard.please_provide') }} {{ __('dashboard.youtube_video_id') }}
                                </div>
                            </div> --}}

                            <div class="form-group">
                                <label for="link">{{ __('dashboard.web_link') }}</label>
                                <input type="url" class="form-control" name="link" id="link"
                                    value="{{ old('link') }}">

                                <div class="invalid-feedback">
                                    {{ __('dashboard.please_provide') }} {{ __('dashboard.web_link') }}
                                </div>
                            </div>

                            {{-- <div class="form-group">
                                <label for="link3">User Panel</label>
                                <input type="url" class="form-control" name="link3" id="link3"
                                    value="{{ old('link3') }}">

                                <div class="invalid-feedback">
                                    {{ __('dashboard.please_provide') }} {{ __('dashboard.web_link') }}
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
    <script>
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


        let screenshotIndex = {{ count($subservice->screenshot ?? []) }};

        // Render all category options as string

        function addScreenshot() {
            const screenshotWrapper = document.querySelector('.banner-row');

            const screenshotGroup = document.createElement('div');
            screenshotGroup.classList.add('form-group', 'banner-group', 'col-10', 'mb-2');
            screenshotGroup.innerHTML = `
                            <input type="file" class="form-control mb-1" name="screenshot[${screenshotIndex}][screenshot_image]">
                        `;

            // Insert before the last column (button)
            const screenshotButtonContainer = screenshotWrapper.querySelector('.col-2');
            screenshotWrapper.insertBefore(screenshotGroup, screenshotButtonContainer);

            screenshotIndex++;
        }


        document.addEventListener('DOMContentLoaded', function() {
            const technologiesSelect = document.getElementById('technologies');
            new Choices(technologiesSelect, {
                removeItemButton: true, // show "x" to remove selected items
                placeholder: true,
                placeholderValue: 'Select technologies',
                searchPlaceholderValue: 'Search technologies...',
                shouldSort: false // optional: keeps original order
            });
        });


        // FAQs Section
        let faqIndex = 1;

        function addFaq() {
            const wrapper = document.querySelector('.faq-group').parentNode;
            const group = document.createElement('div');
            group.classList.add('form-group', 'faq-group', 'col-9', 'mb-2');
            group.innerHTML = `
                ${faqIndex + 1}. 
                <input type="text" class="form-control mb-1" name="icon[${faqIndex}][icon_class]" placeholder="${faqIndex + 1}. Icon Class">
                <input type="text" class="form-control mb-1" name="icon[${faqIndex}][title]" placeholder="${faqIndex + 1}. Title">
                <input type="text" class="form-control mb-1" name="icon[${faqIndex}][description]" placeholder="${faqIndex + 1}. Description">           
            `;
            wrapper.appendChild(group);
            faqIndex++;
        }
    </script>
    <script src="https://cdn.jsdelivr.net/npm/choices.js/public/assets/scripts/choices.min.js"></script>

@endsection
