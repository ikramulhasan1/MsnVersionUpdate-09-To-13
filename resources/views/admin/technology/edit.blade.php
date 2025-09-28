@extends('admin.layouts.master')

@section('content')
@php
    $tech = json_decode($subservice->tech_steps, true);
    $expertise = json_decode($subservice->expertise_steps, true);
@endphp

<div class="container-fluid">
    @include('admin.inc.breadcrumb')

    <div class="row mb-2">
        <div class="col-12">
            <a href="{{ route('admin.technologies.index') }}" class="btn btn-info">{{ __('dashboard.back') }}</a>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <form class="needs-validation" novalidate
                      action="{{ route('admin.technologies.update', $subservice->id) }}"
                      method="post" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')

                    <div class="card-body">
                        {{-- Service Select --}}
                        <div class="form-group">
                            <label for="service_id">{{ __('dashboard.select_service_id') }}</label>
                            <select class="wide" name="service_id" id="service_id" data-plugin="customselect">
                                @foreach ($services as $service)
                                    <option value="{{ $service->id }}" 
                                        @if ($subservice->service_id == $service->id) selected @endif>
                                        {{ $service->title }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        {{-- Title & Short Title --}}
                        <div class="form-group">
                            <label for="title">{{ __('Banner Title') }}</label>
                            <input type="text" class="form-control" name="title" id="title"
                                   value="{{ $subservice->title }}" required>
                        </div>

                        <div class="row">
                            <div class="form-group col-6">
                                <label for="slug">{{ __('dashboard.slug') }}</label>
                                <input type="text" class="form-control" name="slug" id="slug"
                                       value="{{ $subservice->slug }}" readonly required>
                            </div>
                            <div class="form-group col-6">
                                <label for="short_title">{{ __('dashboard.short_title') }}</label>
                                <input type="text" class="form-control" name="short_title" id="short_title"
                                       value="{{ $subservice->short_title }}" required>
                            </div>
                        </div>

                        {{-- Description --}}
                        <div class="form-group">
                            <label for="description">{{ __('Banner Description') }}</label>
                            <textarea class="form-control" name="description" id="editor1" rows="8" required>
                                {!! $subservice->description !!}
                            </textarea>
                        </div>

                        {{-- Toggle Section --}}
                        <hr>
                        <h3>Toggle</h3>
                        <div class="row process-row-toggle">
                            @foreach ($tech as $index => $item)
                                <div class="form-group col-9 faq-group mb-2">
                                    <input type="text" value="{{ $item['tech_title'] }}"
                                           class="form-control mb-1"
                                           name="tech[{{ $index }}][tech_title]"
                                           placeholder="Title">
                                    <textarea id="editor_toggle_{{ $index }}"
                                              class="form-control mb-1"
                                              name="tech[{{ $index }}][tech_description]"
                                              placeholder="Description">{!! $item['tech_description'] !!}</textarea>
                                </div>
                            @endforeach
                            <div class="form-group col-3">
                                <button class="btn btn-success" type="button"
                                        onclick="addProcess()">{{ __('Toggle add') }}</button>
                            </div>
                        </div>

                        {{-- Expertise Section --}}
                        <hr>
                        <h3>Expertise Section</h3>
                        <div class="row process-row-expertise">
                            @foreach ($expertise as $index => $item)
                                <div class="form-group col-9 faq-group mb-2">
                                    <input type="text" value="{{ $item['expertise_url'] }}"
                                           class="form-control mb-1"
                                           name="expertise[{{ $index }}][expertise_url]"
                                           placeholder="Url">
                                    <input type="file" class="form-control mb-1"
                                           name="expertise[{{ $index }}][expertise_image]">
                                </div>
                            @endforeach
                            <div class="form-group col-3">
                                <button class="btn btn-success" type="button"
                                        onclick="addExpertise()">{{ __('Expertise add') }}</button>
                            </div>
                        </div>

                        {{-- Meta Fields --}}
                        <div class="form-group mt-3">
                            <label for="meta_title">{{ __('dashboard.meta_title') }}</label>
                            <input type="text" class="form-control" name="meta_title" id="meta_title"
                                   value="{{ $subservice->meta_title }}" required>
                        </div>

                        <div class="form-group">
                            <label for="short_desc">{{ __('dashboard.meta_description') }}</label>
                            <textarea class="form-control" name="short_desc" id="editor2" rows="4" required>
                                {!! $subservice->short_desc !!}
                            </textarea>
                        </div>

                        <div class="form-group">
                            <label for="keywords">{{ __('dashboard.meta_keywords') }}</label>
                            <input type="text" class="form-control tagin" data-tagin-separator=" " name="keywords"
                                   value="{{ $subservice->keywords ?? '' }}" required>
                        </div>

                        {{-- Images --}}
                        <div class="row">
                            <div class="form-group col-6">
                                <label for="image">{{ __('CTA Image') }}</label>
                                <input type="file" class="form-control" name="image" id="image">
                            </div>
                            <div class="form-group col-6">
                                <label for="logo">{{ __('dashboard.logo') }}</label>
                                <input type="file" class="form-control" name="logo" id="logo">
                            </div>
                        </div>

                        {{-- Pricing & Ratings --}}
                        <div class="row">
                            <div class="form-group col">
                                <label for="price">{{ __('dashboard.price') }}</label>
                                <input type="number" class="form-control" name="price" value="{{ $subservice->price }}">
                            </div>
                            <div class="form-group col">
                                <label for="starting_price">{{ __('dashboard.starting_price') }}</label>
                                <input type="number" class="form-control" name="starting_price"
                                       value="{{ $subservice->starting_price }}">
                            </div>
                            <div class="form-group col">
                                <label for="review_count">{{ __('dashboard.review_count') }}</label>
                                <input type="number" class="form-control" name="review_count"
                                       value="{{ $subservice->review_count }}">
                            </div>
                            <div class="form-group col">
                                <label for="priceCurrency">{{ __('dashboard.priceCurrency') }}</label>
                                <input type="text" class="form-control" name="priceCurrency"
                                       value="{{ $subservice->priceCurrency }}">
                            </div>
                            <div class="form-group col">
                                <label for="average_rating">{{ __('dashboard.average_rating') }}</label>
                                <input type="text" class="form-control" name="average_rating"
                                       value="{{ $subservice->average_rating }}">
                            </div>
                        </div>

                        {{-- Status --}}
                        <div class="row">
                            <div class="form-group col">
                                <label for="manu">Manu</label>
                                <select class="wide" name="manu" id="manu" data-plugin="customselect">
                                    <option value="0" @if ($subservice->manu == 0) selected @endif>Hidden</option>
                                    <option value="1" @if ($subservice->manu == 1) selected @endif>Show</option>
                                </select>
                            </div>
                            <div class="form-group col">
                                <label for="status">{{ __('dashboard.select_status') }}</label>
                                <select class="wide" name="status" id="status" data-plugin="customselect">
                                    <option value="1" @if ($subservice->status == 1) selected @endif>
                                        {{ __('dashboard.active') }}
                                    </option>
                                    <option value="0" @if ($subservice->status == 0) selected @endif>
                                        {{ __('dashboard.inactive') }}
                                    </option>
                                </select>
                            </div>
                        </div>

                    </div>

                    <div class="card-footer">
                        <button type="submit" class="btn btn-primary">{{ __('dashboard.update') }}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener("DOMContentLoaded", function () {
    // Tagin init
    document.querySelectorAll(".tagin").forEach(input => new Tagin(input, {
        separator: ',',
        duplicate: false,
        enter: true,
        maxTags: 100
    }));

    // CKEditor static
    ["editor1","editor2"].forEach(id => { if(document.getElementById(id)) initCKEditor(id); });

    // CKEditor DB-loaded toggles
    document.querySelectorAll("textarea[id^='editor_toggle_']").forEach(el => initCKEditor(el.id));
});

function initCKEditor(id) {
    if(window.CKEDITOR){
        if(CKEDITOR.instances[id]) CKEDITOR.instances[id].destroy(true);
        CKEDITOR.replace(id, {
            toolbar: [
                { name: 'basicstyles', items: ['Bold','Italic','Underline'] },
                { name: 'paragraph', items: ['NumberedList','BulletedList'] },
                { name: 'links', items: ['Link','Unlink'] },
                { name: 'insert', items: ['Image','Table'] },
                { name: 'document', items: ['Source'] }
            ]
        });
    }
}

// Counters
let toggleIndex = document.querySelectorAll(".process-row-toggle textarea").length || 0;
let expertiseIndex = document.querySelectorAll(".process-row-expertise input[type='text']").length || 0;

// Add Toggle
function addProcess(){
    const wrapper = document.querySelector('.process-row-toggle');
    const editorId = `editor_toggle_${toggleIndex}`;
    const group = document.createElement('div');
    group.className = 'form-group faq-group col-9 mb-2';
    group.innerHTML = `
        <input type="text" class="form-control mb-1" name="tech[${toggleIndex}][tech_title]" placeholder="Title">
        <textarea id="${editorId}" class="form-control mb-1" name="tech[${toggleIndex}][tech_description]" placeholder="Description"></textarea>
    `;
    const btnContainer = wrapper.querySelector('.col-3');
    wrapper.insertBefore(group, btnContainer);
    setTimeout(()=>initCKEditor(editorId),100);
    toggleIndex++;
}

// Add Expertise
function addExpertise(){
    const wrapper = document.querySelector('.process-row-expertise');
    const group = document.createElement('div');
    group.className = 'form-group faq-group col-9 mb-2';
    group.innerHTML = `
        <input type="text" class="form-control mb-1" name="expertise[${expertiseIndex}][expertise_url]" placeholder="Url">
        <input type="file" class="form-control mb-1" name="expertise[${expertiseIndex}][expertise_image]">
    `;
    const btnContainer = wrapper.querySelector('.col-3');
    wrapper.insertBefore(group, btnContainer);
    expertiseIndex++;
}
</script>

@endsection
