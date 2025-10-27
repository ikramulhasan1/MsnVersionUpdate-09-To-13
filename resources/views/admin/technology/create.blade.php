@extends('admin.layouts.master')
@section('content')

    <div class="container-fluid">
        @include('admin.inc.breadcrumb')

        <div class="row mb-3">
            <div class="col-12">
                <a href="{{ route('admin.technologies.index') }}" class="btn btn-info">{{ __('dashboard.back') }}</a>
            </div>
        </div>

        <div class="row">
            <div class="col-12 col-lg-12">
                <div class="card">
                    <div class="card-header">
                        <h4>{{ __('dashboard.add') }} {{ $title ?? 'Technology' }}</h4>
                    </div>

                    <form action="{{ route('admin.technologies.store') }}" method="POST" enctype="multipart/form-data"
                        class="needs-validation" novalidate>
                        @csrf
                        <div class="card-body">

                            {{-- Service --}}
                            <div class="form-group">
                                <label>{{ __('dashboard.select_service_id') }}</label>
                                <select name="service_id" class="wide form-control">
                                    @foreach ($services as $service)
                                        <option value="{{ $service->id }}">{{ $service->title }}</option>
                                    @endforeach
                                </select>
                            </div>

                            {{-- Banner Title --}}
                            <div class="form-group">
                                <label>Banner Title <span>*</span></label>
                                <input type="text" name="title" class="form-control" value="{{ old('title') }}" required>
                            </div>

                            {{-- Slug & Short Title --}}
                            <div class="row">
                                <div class="form-group col-6">
                                    <label>Slug <span>*</span></label>
                                    <input type="text" name="slug" class="form-control" value="{{ old('slug') }}" required>
                                </div>
                                <div class="form-group col-6">
                                    <label>Short Title <span>*</span></label>
                                    <input type="text" name="short_title" class="form-control"
                                        value="{{ old('short_title') }}" required>
                                </div>
                            </div>

                            {{-- Description --}}
                            <div class="form-group">
                                <label>Banner Description <span>*</span></label>
                                <textarea name="description" class="form-control" id="editor1"
                                    rows="5">{{ old('description') }}</textarea>
                            </div>

                            <hr>
                            {{-- toggle section --}}
                            <div class="row">
                                <div class="form-group col-6">
                                    <label>Toggle Title <span>*</span></label>
                                    <input type="text" name="toggle_title" class="form-control" value="{{ old('toggle_title') }}" required>
                                </div>
                                <div class="form-group col-6">
                                    <label>Toggle Sub Title <span>*</span></label>
                                    <input type="text" name="toggle_sub_title" class="form-control"
                                        value="{{ old('toggle_sub_title') }}" required>
                                </div>
                            </div>

                            {{-- Main Image --}}
                            <div class="row">
                                <div class="form-group col-6">
                                    <label>CTA Image <span>*</span></label>
                                    <input type="file" name="image" class="form-control" required>
                                    <label>Remove Background?</label>
                                    <select name="remove_bg_image" class="form-control">
                                        <option value="no" selected>No</option>
                                        <option value="yes">Yes</option>
                                    </select>
                                </div>

                                {{-- Logo --}}
                                <div class="form-group col-6">
                                    <label>Logo</label>
                                    <input type="file" name="logo" class="form-control">
                                    <label>Remove Background?</label>
                                    <select name="remove_bg_logo" class="form-control">
                                        <option value="no" selected>No</option>
                                        <option value="yes">Yes</option>
                                    </select>
                                </div>
                            </div>

                            <hr>

                            {{-- Tech Steps --}}
                            <h5>Tech Steps</h5>
                            <div class="row process-row-toggle">
                                <div class="form-group col-9 mb-2">
                                    <input type="text" name="tech[0][tech_title]" class="form-control mb-1"
                                        placeholder="Title">
                                    <textarea name="tech[0][tech_description]" class="form-control mb-1"
                                        placeholder="Description"></textarea>
                                </div>
                                <div class="form-group col-3">
                                    <button type="button" class="btn btn-success" onclick="addProcess()">Add Tech</button>
                                </div>
                            </div>

                            <hr>

                            {{-- Expertise --}}
                            <h5>Expertise Section</h5>
                            <div class="row process-row-expertise">
                                <div class="form-group col-9 mb-2">
                                    <input type="text" name="expertise[0][expertise_url]" class="form-control mb-1"
                                        placeholder="Url">
                                    <input type="file" name="expertise[0][expertise_image]" class="form-control mb-1">
                                    <label>Remove Background?</label>
                                    <select name="expertise[0][remove_bg]" class="form-control">
                                        <option value="no" selected>No</option>
                                        <option value="yes">Yes</option>
                                    </select>
                                </div>
                                <div class="form-group col-3">
                                    <button type="button" class="btn btn-success" onclick="addExpertise()">Add
                                        Expertise</button>
                                </div>
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
                            <hr>

                            {{-- Price, Rating --}}
                            <div class="row">
                                <div class="form-group col-3">
                                    <label>Price <span>*</span></label>
                                    <input type="number" name="price" class="form-control" value="499">
                                </div>
                                <div class="form-group col-3">
                                    <label>Starting Price <span>*</span></label>
                                    <input type="number" name="starting_price" class="form-control" value="499">
                                </div>
                                <div class="form-group col-3">
                                    <label>Review Count <span>*</span></label>
                                    <input type="number" name="review_count" class="form-control" value="150">
                                </div>
                                <div class="form-group col-3">
                                    <label>Average Rating <span>*</span></label>
                                    <input type="text" name="average_rating" class="form-control" value="4.9">
                                </div>
                            </div>

                            <div class="form-group">
                                <label>Price Currency <span>*</span></label>
                                <input type="text" name="priceCurrency" class="form-control" value="USD">
                            </div>

                            {{-- Manu --}}
                            <div class="form-group">
                                <label>Manu</label>
                                <select name="manu" class="form-control">
                                    <option value="0">Hidden</option>
                                    <option value="1">Show</option>
                                </select>
                            </div>

                        </div>

                        <div class="card-footer">
                            <button type="submit" class="btn btn-primary">{{ __('dashboard.save') }}</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    {{-- JS for dynamic rows --}}
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



        let processIndex = 1;  // tech index
        let expertiseIndex = 1; // expertise index

        function initCKEditorDynamic(id) {
            // CKEditor v4
            if (window.CKEDITOR) {
                if (CKEDITOR.instances[id]) {
                    CKEDITOR.instances[id].destroy(true);
                }
                CKEDITOR.replace(id, {
                    removeButtons: '',
                    height: 100
                });
            }
        }

        // Add Tech
        function addProcess() {
            const container = document.querySelector('.process-row-toggle');
            const editorId = `tech_editor_${processIndex}`;
            const html = `
                    <div class="form-group col-9 mb-2">
                        <input type="text" name="tech[${processIndex}][tech_title]" class="form-control mb-1" placeholder="Title">
                        <textarea id="${editorId}" name="tech[${processIndex}][tech_description]" class="form-control mb-1" placeholder="Description"></textarea>
                    </div>
                `;
            container.insertAdjacentHTML('beforeend', html);
            initCKEditorDynamic(editorId);
            processIndex++;
        }

        // Add Expertise
        function addExpertise() {
            const container = document.querySelector('.process-row-expertise');
            const editorId = `expertise_editor_${expertiseIndex}`;
            const html = `
                    <div class="form-group col-9 mb-2">
                        <input type="text" name="expertise[${expertiseIndex}][expertise_url]" class="form-control mb-2" placeholder="Url">
                        <input type="file" name="expertise[${expertiseIndex}][expertise_image]" class="form-control mb-1">
                        <label>Remove Background?</label>
                        <select name="expertise[${expertiseIndex}][remove_bg]" class="form-control">
                            <option value="no" selected>No</option>
                            <option value="yes">Yes</option>
                        </select>
                    </div>
                `;
            container.insertAdjacentHTML('beforeend', html);
            initCKEditorDynamic(editorId);
            expertiseIndex++;
        }

        // Initialize existing CKEditors
        document.addEventListener("DOMContentLoaded", function () {
            CKEDITOR.replace('editor1'); // main description
        });

    </script>

    {{-- CKEditor --}}
    <script>
        CKEDITOR.replace('editor1');
    </script>

@endsection