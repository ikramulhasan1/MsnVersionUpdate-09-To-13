@extends('admin.layouts.master')
@section('title', $title)
@section('content')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/choices.js/public/assets/styles/choices.min.css" />

    <!-- Start Content-->
    <div class="container-fluid">

        <!-- start page title -->
        <!-- Include page breadcrumb -->
        @include('admin.inc.breadcrumb')
        <!-- end page title -->

        <div class="row">
            <div class="col-12">
                <a href="{{ route('admin.subservices.index') }}" class="btn btn-info">{{ __('dashboard.back') }}</a>
            </div>
        </div>

        <div class="row">
            <div class="col-12 col-lg-12">
                <div class="card">
                    <div class="card-header">
                        {{-- <h4 class="header-title">{{ __('dashboard.edit') }} {{ $title }}</h4> --}}
                    </div>
                    <form class="needs-validation" novalidate
                        action="{{ route('admin.subservices.update', $subservice->id) }}" method="post"
                        enctype="multipart/form-data">
                        @csrf
                        @method('PUT')
                        <div class="card-body">

                            <!-- Form Start -->
                            <div class="card-body">
                                <div class="form-group">
                                    <label for="status">{{ __('dashboard.select_status') }}</label>
                                    <select class="wide" name="service_id" id="status" data-plugin="customselect">
                                        @foreach ($services as $service)
                                            <option value="{{ $service->id }}" @if ($subservice->service_id == $service->id)
                                            selected @endif>{{ $service->title }}
                                            </option>
                                        @endforeach

                                    </select>
                                </div>
                                <!-- Form Start -->
                                <div class="form-group">
                                    <label for="title">{{ __('dashboard.title') }} <span>*</span></label>
                                    <input type="text" class="form-control" name="title" id="title"
                                        value="{{ $subservice->title }}" required>

                                    <div class="invalid-feedback">
                                        {{ __('dashboard.please_provide') }} {{ __('dashboard.title') }}
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="form-group col-6">
                                        <label for="slug">{{ __('dashboard.slug') }} <span>* </span></label>
                                        <input type="text" class="form-control" name="slug" id="slug"
                                            value="{{ $subservice->slug }}" readonly required>
                                        <div class="invalid-feedback">
                                            {{ __('dashboard.please_provide') }} {{ __('dashboard.slug') }}
                                        </div>
                                    </div>
                                    <div class="form-group col-6">
                                        <label for="short_title">{{ __('dashboard.short_title') }} <span>*</span></label>
                                        <input type="text" class="form-control" name="short_title" id="short_title"
                                            value="{{ $subservice->short_title }}" required>

                                        <div class="invalid-feedback">
                                            {{ __('dashboard.please_provide') }} {{ __('dashboard.short_title') }}
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label for="description">{{ __('dashboard.description') }} <span>*</span></label>
                                    <textarea class="form-control" name="description" id="editor1" rows="8"
                                        required>{{ $subservice->description }}</textarea>

                                    <div class="invalid-feedback">
                                        {{ __('dashboard.please_provide') }} {{ __('dashboard.description') }}
                                    </div>
                                </div>

                                {{-- --}}
                                <hr>
                                <h3>Banner Section</h3>
                                <div class="row banner-row">

                                    @php
                                        $bannerSteps = is_string($subservice->banner_steps)
                                            ? json_decode($subservice->banner_steps)
                                            : $subservice->banner_steps;
                                    @endphp

                                    @foreach ($bannerSteps ?? [] as $key => $banner_step)
                                        <div class="form-group col-10 banner-group mb-2 row">
                                            <div class="col-1">
                                                {{ $key + 1 }}.
                                            </div>
                                            <div class="col-11">
                                                <input type="text" class="form-control mb-1" name="banner[{{ $key }}][title]"
                                                    value="{{ $banner_step->title }}" placeholder="{{ $key + 1 }}. Title">
                                                <input type="text" class="form-control mb-1"
                                                    name="banner[{{ $key }}][sub_title]" value="{{ $banner_step->sub_title }}"
                                                    placeholder="{{ $key + 1 }}. Sub title">
                                                <div class="d-flex">
                                                    <input type="file" class="form-control mb-1 mr-3 w-75"
                                                        name="banner[{{ $key }}][banner_image]">
                                                    <img style="width: 40px; height: 40px;"
                                                        src="{{ asset('uploads/banner/' . $banner_step->banner_image) }}"
                                                        class="process-step-icon" alt="">
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach

                                    <div class="form-group col-2">
                                        <button class="btn btn-success" type="button"
                                            onclick="addBanner()">{{ __('dashboard.add_work_process') }}</button>
                                    </div>
                                    <br><br>
                                </div>
                                <div class="form-group mb-4">
                                    <label for="technologies"
                                        class="block text-sm font-medium text-gray-700 mb-1">Technologies</label>
                                    <select name="technologies[]" id="technologies" multiple>
                                        @foreach($allTechnologies as $tech)
                                            <option value="{{ $tech->id }}" {{ in_array($tech->id, $subservice->technologies->pluck('id')->toArray()) ? 'selected' : '' }}>
                                                {{ $tech->title }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="form-group mb-4">
                                    <label for="portfolios"
                                        class="block text-sm font-medium text-gray-700 mb-1">Portfolios</label>
                                    <select name="portfolios[]" id="portfolios" multiple>
                                        @foreach($allPortfolios as $portfolio)
                                            <option value="{{ $portfolio->id }}" {{ in_array($portfolio->id, $subservice->portfolios->pluck('id')->toArray()) ? 'selected' : '' }}>
                                                {{ $portfolio->title }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>


                                <hr>
                                <h3>Core Features</h3>
                                <div class="row features-row">
                                    @php
                                        $features = json_decode($subservice->features_steps, true);
                                    @endphp
                                    @if (!empty($features) && is_array($features))
                                        @foreach ($features as $key => $feature)
                                            <div class="form-group col-10 features-group mb-2 row">
                                                <div class="col-1">
                                                    {{ $key + 1 }}.
                                                </div>
                                                <div class="col-11">
                                                    <input type="text" class="form-control mb-1"
                                                        name="features[{{ $key }}][icon_class]" value="{{ $feature['icon_class'] }}"
                                                        placeholder="{{ $key + 1 }}. Icon Class">
                                                    <input type="text" class="form-control mb-1" name="features[{{ $key }}][title]"
                                                        value="{{ $feature['title'] }}" placeholder="{{ $key + 1 }}. Title">
                                                    <input type="text" class="form-control mb-1"
                                                        name="features[{{ $key }}][bottom_text]"
                                                        value="{{ $feature['bottom_text'] }}"
                                                        placeholder="{{ $key + 1 }}. Bottom Text">
                                                </div>
                                            </div>
                                        @endforeach
                                    @endif
                                    <div class="form-group col-2">
                                        <button class="btn btn-success" type="button"
                                            onclick="addFeatures()">{{ __('dashboard.add_another_FAQ') }}</button>
                                    </div>
                                    <br><br>
                                </div>

                                <hr>
                                <h3>Work Process</h3>
                                <div class="row process-row">
                                    @php
                                        $process_steps = is_string($subservice->process_steps)
                                            ? json_decode($subservice->process_steps, true)
                                            : $subservice->process_steps;
                                    @endphp
                                    @if (!empty($process_steps) && is_array($process_steps))
                                        @foreach ($process_steps as $key => $step)
                                            <div class="form-group col-10 process-group mb-2 row">
                                                <div class="col-1">
                                                    {{ $key + 1 }}.
                                                </div>
                                                <div class="col-11">
                                                    <input type="text" class="form-control mb-1" name="process[{{ $key }}][title]"
                                                        value="{{ $step['title'] }}" placeholder="{{ $key + 1 }}. Title">
                                                    <input type="text" class="form-control mb-1"
                                                        name="process[{{ $key }}][bottom_text]" value="{{ $step['bottom_text'] }}"
                                                        placeholder="{{ $key + 1 }}. Bottom Text">
                                                </div>
                                            </div>
                                        @endforeach
                                    @endif
                                    <div class="form-group col-2">
                                        <button class="btn btn-success" type="button"
                                            onclick="addProcess()">{{ __('dashboard.add_another_FAQ') }}</button>
                                    </div>
                                    <br><br>
                                </div>

                                <hr>
                                <h3>Why Choose Us</h3>
                                <div class="row WhyWe-row">
                                    @php
                                        $whyWeSteps = is_string($subservice->why_we_steps)
                                            ? json_decode($subservice->why_we_steps, true)
                                            : ($subservice->why_we_steps ?? []);
                                    @endphp
                                    @if (!empty($whyWeSteps) && is_array($whyWeSteps))
                                        @foreach ($whyWeSteps as $key => $why)
                                            <div class="form-group col-10 WhyWe-group mb-2 row">
                                                <div class="col-1">
                                                    {{ $key + 1 }}.
                                                </div>
                                                <div class="col-11">
                                                    <input type="text" class="form-control mb-1"
                                                        name="why_we[{{ $key }}][icon_class]" value="{{ $why['icon_class'] }}"
                                                        placeholder="{{ $key + 1 }}. Icon Class">
                                                    <input type="text" class="form-control mb-1" name="why_we[{{ $key }}][title]"
                                                        value="{{ $why['title'] }}" placeholder="{{ $key + 1 }}. Title">
                                                    <input type="text" class="form-control mb-1"
                                                        name="why_we[{{ $key }}][bottom_text]" value="{{ $why['bottom_text'] }}"
                                                        placeholder="{{ $key + 1 }}. Bottom Text">
                                                </div>
                                            </div>
                                        @endforeach
                                    @endif
                                    <div class="form-group col-2">
                                        <button class="btn btn-success" type="button"
                                            onclick="addWhyWe()">{{ __('dashboard.add_another_FAQ') }}</button>
                                    </div>
                                    <br><br>
                                </div>
                                <hr>
                                <h3>Industries We Serve</h3>
                                <div class="row industries-row">
                                    @php
                                        $industriesSteps = is_string($subservice->industries_steps)
                                            ? json_decode($subservice->industries_steps, true)
                                            : ($subservice->industries_steps ?? []);
                                    @endphp
                                    @if (!empty($industriesSteps) && is_array($industriesSteps))
                                        @foreach ($industriesSteps as $key => $industry)
                                            <div class="form-group col-10 industry-group mb-2 row">
                                                <div class="col-1">
                                                    {{ $key + 1 }}.
                                                </div>
                                                <div class="col-11">
                                                    <input type="text" class="form-control mb-1"
                                                        name="industry[{{ $key }}][icon_class]"
                                                        value="{{ $industry['icon_class'] }}"
                                                        placeholder="{{ $key + 1 }}. Icon Class">
                                                    <input type="text" class="form-control mb-1" name="industry[{{ $key }}][title]"
                                                        value="{{ $industry['title'] }}" placeholder="{{ $key + 1 }}. Title">

                                                </div>
                                            </div>
                                        @endforeach
                                    @endif
                                    <div class="form-group col-2">
                                        <button class="btn btn-success" type="button"
                                            onclick="addIndustries()">{{ __('dashboard.add_another_FAQ') }}</button>
                                    </div>
                                    <br><br>
                                </div>
                                <hr>
                                <h3>Achievements</h3>
                                <div class="row achievement-row">
                                    @php
                                        $achievementsSteps = is_string($subservice->achievements_steps)
                                            ? json_decode($subservice->achievements_steps, true)
                                            : ($subservice->achievements_steps ?? []);
                                    @endphp
                                    @if (!empty($achievementsSteps) && is_array($achievementsSteps))
                                        @foreach ($achievementsSteps as $key => $achievement)
                                            <div class="form-group col-10 achievement-group mb-2 row">
                                                <div class="col-1">
                                                    {{ $key + 1 }}.
                                                </div>
                                                <div class="col-11">
                                                    <input type="text" class="form-control mb-1"
                                                        name="achievement[{{ $key }}][count_number]"
                                                        value="{{ $achievement['count_number'] }}"
                                                        placeholder="{{ $key + 1 }}. Count Number">
                                                    <input type="text" class="form-control mb-1"
                                                        name="achievement[{{ $key }}][title]" value="{{ $achievement['title'] }}"
                                                        placeholder="{{ $key + 1 }}. Title">

                                                </div>
                                            </div>
                                        @endforeach
                                    @endif
                                    <div class="form-group col-2">
                                        <button class="btn btn-success" type="button"
                                            onclick="addAchievements()">{{ __('dashboard.add_another_FAQ') }}</button>
                                    </div>
                                    <br><br>
                                </div>
                                <hr>
                                <h3>Success Stories</h3>
                                <div class="row success-stories-row">
                                    @php
                                        $successStoriesSteps = is_string($subservice->success_stories_steps)
                                            ? json_decode($subservice->success_stories_steps, true)
                                            : $subservice->success_stories_steps;
                                    @endphp
                                    @if (!empty($successStoriesSteps) && is_array($successStoriesSteps))
                                        @foreach ($successStoriesSteps as $key => $story)
                                            <div class="form-group col-10 SuccessStories-group mb-2 row">
                                                <div class="col-1">
                                                    {{ $key + 1 }}.
                                                </div>
                                                <div class="col-11">
                                                    <input type="text" class="form-control mb-1" name="story[{{ $key }}][title]"
                                                        value="{{ $story['title'] }}" placeholder="{{ $key + 1 }}. Title">
                                                    <input type="text" class="form-control mb-1"
                                                        name="story[{{ $key }}][bottom_text]" value="{{ $story['bottom_text'] }}"
                                                        placeholder="{{ $key + 1 }}. Bottom Text">

                                                </div>
                                            </div>
                                        @endforeach
                                    @endif
                                    <div class="form-group col-2">
                                        <button class="btn btn-success" type="button"
                                            onclick="addSuccessStories()">{{ __('dashboard.add_another_FAQ') }}</button>
                                    </div>
                                    <br><br>
                                </div>
                                <hr>
                                <h3>Clients Say</h3>
                                <div class="row clients-say-row">
                                    @php
                                        $clientsSaySteps = is_string($subservice->clients_say_steps)
                                            ? json_decode($subservice->clients_say_steps, true)
                                            : $subservice->clients_say_steps;
                                    @endphp
                                    @if (!empty($clientsSaySteps) && is_array($clientsSaySteps))
                                        @foreach ($clientsSaySteps as $key => $client)
                                            <div class="form-group col-10 clients-group mb-2 row">
                                                <div class="col-1">
                                                    {{ $key + 1 }}.
                                                </div>
                                                <div class="col-11">
                                                    <input type="text" class="form-control mb-1" name="client[{{ $key }}][title]"
                                                        value="{{ $client['title'] }}" placeholder="{{ $key + 1 }}. Title">
                                                    <input type="text" class="form-control mb-1" name="client[{{ $key }}][meassage]"
                                                        value="{{ $client['meassage'] }}" placeholder="{{ $key + 1 }}. Meassage">

                                                </div>
                                            </div>
                                        @endforeach
                                    @endif
                                    <div class="form-group col-2">
                                        <button class="btn btn-success" type="button"
                                            onclick="addClientsSay()">{{ __('dashboard.add_another_FAQ') }}</button>
                                    </div>
                                    <br><br>
                                </div>

                                <hr>
                                <h3>FAQs</h3>
                                <div class="row faq-row">
                                    @php
                                        $faqSteps = is_string($subservice->faq_steps)
                                            ? json_decode($subservice->faq_steps, true)
                                            : $subservice->faq_steps;
                                    @endphp
                                    @if (!empty($faqSteps) && is_array($faqSteps))
                                        @foreach ($faqSteps as $key => $faq)
                                            <div class="form-group col-10 faq-group mb-2 row">
                                                <div class="col-1">
                                                    {{ $key + 1 }}.
                                                </div>
                                                <div class="col-11">
                                                    <input type="text" class="form-control mb-1" name="faq[{{ $key }}][question]"
                                                        value="{{ $faq['question'] }}" placeholder="{{ $key + 1 }}. Question">
                                                    <input type="text" class="form-control mb-1" name="faq[{{ $key }}][answer]"
                                                        value="{{ $faq['answer'] }}" placeholder="{{ $key + 1 }}. Answer">

                                                </div>
                                            </div>
                                        @endforeach
                                    @endif
                                    <div class="form-group col-2">
                                        <button class="btn btn-success" type="button"
                                            onclick="addFaqStep()">{{ __('dashboard.add_another_FAQ') }}</button>
                                    </div>
                                    <br><br>
                                </div>
                                <hr>
                                <h3>Our Promise</h3>
                                <div class="row promise-row">
                                    @php
                                        $ourPromise = is_string($subservice->our_promise)
                                            ? json_decode($subservice->our_promise, true)
                                            : $subservice->our_promise;
                                    @endphp
                                    @if (!empty($ourPromise) && is_array($ourPromise))
                                        @foreach ($ourPromise as $key => $item)
                                            <div class="form-group col-10 promise-group mb-2 row">
                                                <div class="col-1">
                                                    {{ $key + 1 }}.
                                                </div>
                                                <div class="col-11">
                                                    <input type="text" class="form-control mb-1"
                                                        name="item[{{ $key }}][bottom_text]" value="{{ $item['bottom_text'] }}"
                                                        placeholder="{{ $key + 1 }}. Bottom Text">

                                                </div>
                                            </div>
                                        @endforeach
                                    @endif
                                    <div class="form-group col-2">
                                        <button class="btn btn-success" type="button"
                                            onclick="addPromiseStep()">{{ __('dashboard.add_another_FAQ') }}</button>
                                    </div>
                                    <br><br>
                                </div>
                                <hr>
                                <h3>Call to Action</h3>
                                <div class="row cta-row">
                                    @php
                                        $ctaSteps = is_string($subservice->cta_steps)
                                            ? json_decode($subservice->cta_steps, true)
                                            : $subservice->cta_steps;
                                    @endphp
                                    @if (!empty($ctaSteps) && is_array($ctaSteps))
                                        @foreach ($ctaSteps as $key => $cta)
                                            <div class="form-group col-10 cta-group mb-2 row">
                                                <div class="col-1">
                                                    {{ $key + 1 }}.
                                                </div>
                                                <div class="col-11">
                                                    <input type="text" class="form-control mb-1" name="cta[{{ $key }}][bottom_text]"
                                                        value="{{ $cta['bottom_text'] }}" placeholder="{{ $key + 1 }}. Bottom Text">

                                                </div>
                                            </div>
                                        @endforeach
                                    @endif
                                    <div class="form-group col-2">
                                        <button class="btn btn-success" type="button"
                                            onclick="addCtaStep()">{{ __('dashboard.add_another_FAQ') }}</button>
                                    </div>
                                    <br><br>
                                </div>

                                <div class="form-group">
                                    <label for="meta_title">{{ __('dashboard.meta_title') }} <span>*</span></label>
                                    <input type="text" class="form-control" name="meta_title" id="meta_title"
                                        value="{{ $subservice->meta_title }}" required>

                                    <div class="invalid-feedback">
                                        {{ __('dashboard.please_provide') }} {{ __('dashboard.meta_title') }}
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label for="short_desc">{{ __('dashboard.meta_description') }} <span>*</span></label>
                                    <textarea class="form-control" name="short_desc" id="editor" rows="4"
                                        required>{{ $subservice->short_desc }}</textarea>

                                    <div class="invalid-feedback">
                                        {{ __('dashboard.please_provide') }} {{ __('dashboard.meta_description') }}
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="keywords">{{ __('dashboard.meta_keywords') }} <span>*</span></label>
                                    <input type="text" class="form-control tagin" data-tagin-separator=" " name="keywords"
                                        value="{{ $subservice->keywords ?? '' }}" required>

                                    <div class="invalid-feedback">
                                        {{ __('dashboard.please_provide') }} {{ __('dashboard.meta_keywords') }}
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="image">{{ __('dashboard.thumbnail') }}
                                        <span>{{ __('dashboard.image_size', ['height' => 500, 'width' => 800]) }}</span></label>
                                    <input type="file" class="form-control" name="image" id="image">

                                    <div class="invalid-feedback">
                                        {{ __('dashboard.please_provide') }} {{ __('dashboard.thumbnail') }}
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="form-group col">
                                        <label for="price">{{ __('dashboard.price') }} <span>* </span></label>
                                        <input type="number" class="form-control" name="price" id="price"
                                            value="{{ $subservice->price }}" required>
                                        <div class="invalid-feedback">
                                            {{ __('dashboard.please_provide') }} {{ __('dashboard.price') }}
                                        </div>
                                    </div>
                                    <div class="form-group col">
                                        <label for="starting_price">{{ __('dashboard.starting_price') }}
                                            <span>*</span></label>
                                        <input type="number" class="form-control" name="starting_price" id="starting_price"
                                            value="{{ $subservice->starting_price }}" required>

                                        <div class="invalid-feedback">
                                            {{ __('dashboard.please_provide') }} {{ __('dashboard.starting_price') }}
                                        </div>
                                    </div>
                                    <div class="form-group col">
                                        <label for="review_count">{{ __('dashboard.review_count') }}
                                            <span>*</span></label>
                                        <input type="number" class="form-control" name="review_count" id="review_count"
                                            value="{{ $subservice->review_count }}" required>

                                        <div class="invalid-feedback">
                                            {{ __('dashboard.please_provide') }} {{ __('dashboard.review_count') }}
                                        </div>
                                    </div>
                                    <div class="form-group col">
                                        <label for="priceCurrency">{{ __('dashboard.priceCurrency') }}
                                            <span>*</span></label>
                                        <input type="text" class="form-control" name="priceCurrency" id="priceCurrency"
                                            value="{{ $subservice->priceCurrency }}" required>

                                        <div class="invalid-feedback">
                                            {{ __('dashboard.please_provide') }} {{ __('dashboard.priceCurrency') }}
                                        </div>
                                    </div>
                                    <div class="form-group col">
                                        <label for="average_rating">{{ __('dashboard.average_rating') }}
                                            <span>*</span></label>
                                        <input type="text" class="form-control" name="average_rating" id="average_rating"
                                            value="{{ $subservice->average_rating }}" required>

                                        <div class="invalid-feedback">
                                            {{ __('dashboard.please_provide') }} {{ __('dashboard.average_rating') }}
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="form-group col">
                                        <label for="manu">Manu</label>
                                        <select class="wide" name="manu" id="manu" data-plugin="customselect">
                                            <option value="0" @if ($subservice->manu == 0) selected @endif>Hidden
                                            </option>
                                            <option value="1" @if ($subservice->manu == 1) selected @endif>Show
                                            </option>
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
                duplicate: false, // Prevent duplicate tags in the frontend
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

        let bannerIndex = {{ count($subservice->bannerSteps ?? []) }};

        // Render all category options as string

        function addBanner() {
            const bannerWrapper = document.querySelector('.banner-row');

            const bannerGroup = document.createElement('div');
            bannerGroup.classList.add('form-group', 'banner-group', 'col-10', 'mb-2');
            bannerGroup.innerHTML = `
                        <input type="text" class="form-control mb-1" name="banner[${bannerIndex}][title]" placeholder="${bannerIndex + 1}. Title">
                        <input type="text" class="form-control mb-1" name="banner[${bannerIndex}][sub_title]" placeholder="${bannerIndex + 1}. Sub Title">
                        <input type="file" class="form-control mb-1" name="banner[${bannerIndex}][banner_image]">
                    `;

            // Insert before the last column (button)
            const bannerButtonContainer = bannerWrapper.querySelector('.col-2');
            bannerWrapper.insertBefore(bannerGroup, bannerButtonContainer);

            bannerIndex++;
        }


        // Initial index count
        let FeaturesIndex = {{ count($subservice->features ?? []) }};

        // Function to add new feature inputs
        function addFeatures() {
            const wrapperFeatures = document.querySelector('.features-row');

            const groupFeatures = document.createElement('div');
            groupFeatures.classList.add('form-group', 'features-group', 'col-10', 'mb-2', 'row');
            groupFeatures.innerHTML = `
                    <div class="col-11">
                        <input type="text" class="form-control mb-1" name="features[${FeaturesIndex}][icon_class]" placeholder="${FeaturesIndex + 1}. Icon Class">
                        <input type="text" class="form-control mb-1" name="features[${FeaturesIndex}][title]" placeholder="${FeaturesIndex + 1}. Title">
                        <input type="text" class="form-control mb-1" name="features[${FeaturesIndex}][bottom_text]" placeholder="${FeaturesIndex + 1}. Bottom Text">
                    </div>
                    <div class="col-1 d-flex align-items-start">
                        <button type="button" class="btn btn-danger btn-sm mt-1" onclick="removeFeature(this)">
                            <i class="fa fa-trash"></i>
                        </button>
                    </div>
                `;

            // Insert before the last column (Add button)
            const buttonContainer = wrapperFeatures.querySelector('.col-2');
            wrapperFeatures.insertBefore(groupFeatures, buttonContainer);

            FeaturesIndex++;
        }

        // Function to remove feature block
        function removeFeature(button) {
            const row = button.closest('.features-group');
            if (row) {
                row.remove();
            }
        }


        // Initial index count
        let processIndex = {{ is_array($process_steps) ? count($process_steps) : 0 }};
        // Function to add new process inputs
        function addProcess() {
            const wrapperProcess = document.querySelector('.process-row');

            const groupProcess = document.createElement('div');
            groupProcess.classList.add('form-group', 'process-group', 'col-10', 'mb-2', 'row');
            groupProcess.innerHTML = `
                    <div class="col-11">
                        <input type="text" class="form-control mb-1" name="process[${processIndex}][title]" placeholder="${processIndex + 1}. Title">
                        <input type="text" class="form-control mb-1" name="process[${processIndex}][bottom_text]" placeholder="${processIndex + 1}. Bottom Text">
                    </div>
                    <div class="col-1 d-flex align-items-start">
                        <button type="button" class="btn btn-danger btn-sm mt-1" onclick="removeProcess(this)">
                            <i class="fa fa-trash"></i>
                        </button>
                    </div>
                `;

            // Insert before the last column (Add button)
            const buttonContainer = wrapperProcess.querySelector('.col-2');
            wrapperProcess.insertBefore(groupProcess, buttonContainer);

            processIndex++;
        }

        // Function to remove process block
        function removeProcess(button) {
            const row = button.closest('.process-group');
            if (row) {
                row.remove();
            }
        }



        // Initial index count
        let WhyWeIndex = {{ is_array($whyWeSteps) ? count($whyWeSteps) : 0 }};
        // Function to add new feature inputs
        function addWhyWe() {
            const wrapperWhyWe = document.querySelector('.WhyWe-row');

            const groupWhyWe = document.createElement('div');
            groupWhyWe.classList.add('form-group', 'WhyWe-group', 'col-10', 'mb-2', 'row');
            groupWhyWe.innerHTML = `
                        <div class="col-11">
                            <input type="text" class="form-control mb-1" name="why_we[${WhyWeIndex}][icon_class]" placeholder="${WhyWeIndex + 1}. Icon Class">
                            <input type="text" class="form-control mb-1" name="why_we[${WhyWeIndex}][title]" placeholder="${WhyWeIndex + 1}. Title">
                            <input type="text" class="form-control mb-1" name="why_we[${WhyWeIndex}][bottom_text]" placeholder="${WhyWeIndex + 1}. Bottom Text">
                        </div>
                        <div class="col-1 d-flex align-items-start">
                            <button type="button" class="btn btn-danger btn-sm mt-1" onclick="removeWhyWe(this)">
                                <i class="fa fa-trash"></i>
                            </button>
                        </div>
                    `;

            // Insert before the last column (Add button)
            const buttonContainer = wrapperWhyWe.querySelector('.col-2');
            wrapperWhyWe.insertBefore(groupWhyWe, buttonContainer);

            WhyWeIndex++;
        }

        // Function to remove feature block
        function removeWhyWe(button) {
            const row = button.closest('.WhyWe-group');
            if (row) {
                row.remove();
            }
        }
        // Initial index count
        let industriesIndex = {{ is_array($industriesSteps) ? count($industriesSteps) : 0 }};

        // Function to add new feature inputs
        function addIndustries() {
            const wrapperIndustries = document.querySelector('.industries-row');

            const groupIndustries = document.createElement('div');
            groupIndustries.classList.add('form-group', 'industry-group', 'col-10', 'mb-2', 'row');
            groupIndustries.innerHTML = `
                        <div class="col-11">
                            <input type="text" class="form-control mb-1" name="industry[${industriesIndex}][icon_class]" placeholder="${industriesIndex + 1}. Icon Class">
                            <input type="text" class="form-control mb-1" name="industry[${industriesIndex}][title]" placeholder="${industriesIndex + 1}. Title">
                        </div>
                        <div class="col-1 d-flex align-items-start">
                            <button type="button" class="btn btn-danger btn-sm mt-1" onclick="removeIndustry(this)">
                                <i class="fa fa-trash"></i>
                            </button>
                        </div>
                    `;

            // Insert before the last column (Add button)
            const buttonContainer = wrapperIndustries.querySelector('.col-2');
            wrapperIndustries.insertBefore(groupIndustries, buttonContainer);

            industriesIndex++;
        }

        // Function to remove feature block
        function removeIndustry(button) {
            const row = button.closest('.industry-group');
            if (row) {
                row.remove();
            }
        }

        // Initial index count
        let achievementsIndex = {{ is_array($achievementsSteps) ? count($achievementsSteps) : 0 }};

        // Function to add new feature inputs
        function addAchievements() {
            const wrapperIndustries = document.querySelector('.achievement-row');

            const groupIndustries = document.createElement('div');
            groupIndustries.classList.add('form-group', 'achievement-group', 'col-10', 'mb-2', 'row');
            groupIndustries.innerHTML = `
                        <div class="col-11">
                            <input type="text" class="form-control mb-1" name="achievement[${achievementsIndex}][count_number]" placeholder="${achievementsIndex + 1}. Count Number">
                            <input type="text" class="form-control mb-1" name="achievement[${achievementsIndex}][title]" placeholder="${achievementsIndex + 1}. Title">
                        </div>
                        <div class="col-1 d-flex align-items-start">
                            <button type="button" class="btn btn-danger btn-sm mt-1" onclick="removeAchievement(this)">
                                <i class="fa fa-trash"></i>
                            </button>
                        </div>
                    `;

            // Insert before the last column (Add button)
            const buttonContainer = wrapperIndustries.querySelector('.col-2');
            wrapperIndustries.insertBefore(groupIndustries, buttonContainer);

            achievementsIndex++;
        }

        // Function to remove feature block
        function removeAchievement(button) {
            const row = button.closest('.achievement-group');
            if (row) {
                row.remove();
            }
        }

        // Initial success_stories_steps
        let success_stories = {{ count($successStoriesSteps ?? []) }};

        // Function to add new feature inputs
        function addSuccessStories() {
            const wrapperSuccessStories = document.querySelector('.success-stories-row');

            const groupSuccessStories = document.createElement('div');
            groupSuccessStories.classList.add('form-group', 'SuccessStories-group', 'col-10', 'mb-2', 'row');
            groupSuccessStories.innerHTML = `
                        <div class="col-11">
                            <input type="text" class="form-control mb-1" name="story[${success_stories}][title]" placeholder="${success_stories + 1}. Title">
                            <input type="text" class="form-control mb-1" name="story[${success_stories}][bottom_text]" placeholder="${success_stories + 1}. Bottom Text">
                        </div>
                        <div class="col-1 d-flex align-items-start">
                            <button type="button" class="btn btn-danger btn-sm mt-1" onclick="removeSuccessStories(this)">
                                <i class="fa fa-trash"></i>
                            </button>
                        </div>
                    `;

            // Insert before the last column (Add button)
            const buttonContainer = wrapperSuccessStories.querySelector('.col-2');
            wrapperSuccessStories.insertBefore(groupSuccessStories, buttonContainer);

            success_stories++;
        }

        // Function to remove feature block
        function removeSuccessStories(button) {
            const row = button.closest('.SuccessStories-group');
            if (row) {
                row.remove();
            }
        }
        // Initial clients_say_steps
        let clients_say = {{ count($clientsSaySteps ?? []) }};

        // Function to add new feature inputs
        function addClientsSay() {
            const wrapperClientsSay = document.querySelector('.clients-say-row');

            const groupClientsSay = document.createElement('div');
            groupClientsSay.classList.add('form-group', 'clients-group', 'col-10', 'mb-2', 'row');
            groupClientsSay.innerHTML = `
                        <div class="col-11">
                            <input type="text" class="form-control mb-1" name="client[${clients_say}][title]" placeholder="${clients_say + 1}. Title">
                            <input type="text" class="form-control mb-1" name="client[${clients_say}][meassage]" placeholder="${clients_say + 1}. Meassage">
                        </div>
                        <div class="col-1 d-flex align-items-start">
                            <button type="button" class="btn btn-danger btn-sm mt-1" onclick="removeClientsSay(this)">
                                <i class="fa fa-trash"></i>
                            </button>
                        </div>
                    `;

            // Insert before the last column (Add button)
            const buttonContainer = wrapperClientsSay.querySelector('.col-2');
            wrapperClientsSay.insertBefore(groupClientsSay, buttonContainer);

            clients_say++;
        }

        // Function to remove feature block
        function removeClientsSay(button) {
            const row = button.closest('.clients-group');
            if (row) {
                row.remove();
            }
        }
        // Initial faq_steps
        let faqSteps = {{ count($faqSteps ?? []) }};

        // Function to add new feature inputs
        function addFaqStep() {
            const wrapperFaqSteps = document.querySelector('.faq-row');

            const groupFaq = document.createElement('div');
            groupFaq.classList.add('form-group', 'faq-group', 'col-10', 'mb-2', 'row');
            groupFaq.innerHTML = `
                        <div class="col-11">
                            <input type="text" class="form-control mb-1" name="faq[${faqSteps}][question]" placeholder="${faqSteps + 1}. Question">
                            <input type="text" class="form-control mb-1" name="faq[${faqSteps}][answer]" placeholder="${faqSteps + 1}. Answer">
                        </div>
                        <div class="col-1 d-flex align-items-start">
                            <button type="button" class="btn btn-danger btn-sm mt-1" onclick="removeFaqStep(this)">
                                <i class="fa fa-trash"></i>
                            </button>
                        </div>
                    `;

            // Insert before the last column (Add button)
            const buttonContainer = wrapperFaqSteps.querySelector('.col-2');
            wrapperFaqSteps.insertBefore(groupFaq, buttonContainer);

            faqSteps++;
        }

        // Function to remove feature block
        function removeFaqStep(button) {
            const row = button.closest('.faq-group');
            if (row) {
                row.remove();
            }
        }
        // Initial our_promise
        let promise = {{ count($ourPromise ?? []) }};

        // Function to add new feature inputs
        function addPromiseStep() {
            const wrapperPromiseSteps = document.querySelector('.promise-row');

            const groupPromise = document.createElement('div');
            groupPromise.classList.add('form-group', 'promise-group', 'col-10', 'mb-2', 'row');
            groupPromise.innerHTML = `
                        <div class="col-11">
                            <input type="text" class="form-control mb-1" name="item[${promise}][bottom_text]" placeholder="${promise + 1}. Bottom Text">
                        </div>
                        <div class="col-1 d-flex align-items-start">
                            <button type="button" class="btn btn-danger btn-sm mt-1" onclick="removePromiseStep(this)">
                                <i class="fa fa-trash"></i>
                            </button>
                        </div>
                    `;

            // Insert before the last column (Add button)
            const buttonContainer = wrapperPromiseSteps.querySelector('.col-2');
            wrapperPromiseSteps.insertBefore(groupPromise, buttonContainer);

            promise++;
        }

        // Function to remove feature block
        function removePromiseStep(button) {
            const row = button.closest('.promise-group');
            if (row) {
                row.remove();
            }
        }
        // Initial our_promise
        let cta_steps = {{ count($ctaSteps ?? []) }};

        // Function to add new feature inputs
        function addCtaStep() {
            const wrapperCtaStep = document.querySelector('.cta-row');

            const groupCtaStep = document.createElement('div');
            groupCtaStep.classList.add('form-group', 'cta-group', 'col-10', 'mb-2', 'row');
            groupCtaStep.innerHTML = `
                        <div class="col-11">
                            <input type="text" class="form-control mb-1" name="cta[${cta_steps}][bottom_text]" placeholder="${cta_steps + 1}. Bottom Text">
                        </div>
                        <div class="col-1 d-flex align-items-start">
                            <button type="button" class="btn btn-danger btn-sm mt-1" onclick="removeCtaStep(this)">
                                <i class="fa fa-trash"></i>
                            </button>
                        </div>
                    `;

            // Insert before the last column (Add button)
            const buttonContainer = wrapperCtaStep.querySelector('.col-2');
            wrapperCtaStep.insertBefore(groupCtaStep, buttonContainer);

            cta_steps++;
        }

        // Function to remove feature block
        function removeCtaStep(button) {
            const row = button.closest('.cta-group');
            if (row) {
                row.remove();
            }
        }

    </script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const technologiesSelect = document.getElementById('technologies');
            new Choices(technologiesSelect, {
                removeItemButton: true,    // show "x" to remove selected items
                placeholder: true,
                placeholderValue: 'Select technologies',
                searchPlaceholderValue: 'Search technologies...',
                shouldSort: false          // optional: keeps original order
            });
        });

        document.addEventListener('DOMContentLoaded', function () {
            new Choices('#portfolios', {
                removeItemButton: true,
                placeholder: true,
                placeholderValue: 'Select portfolios',
                searchPlaceholderValue: 'Search portfolios...',
                shouldSort: false
            });
        });

    </script>
    <script src="https://cdn.jsdelivr.net/npm/choices.js/public/assets/scripts/choices.min.js"></script>

@endsection