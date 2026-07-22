<style>
    /* ==========================================================
             QUICK QUOTE POPUP FORM — compact, single-column,
             built to live inside a modal (no hero/canvas grid).
             Scoped to .qpf-scope so it can't collide with .gq-scope
             on the full get-quote page.
             ========================================================== */
    .qpf-scope {
        --qpf-ink: #12181f;
        --qpf-ink-soft: #5c6672;
        --qpf-line: #e3e6df;
        --qpf-paper: #f7f7f4;
        --qpf-navy: #0c1626;
        --qpf-teal: #2fd6c0;
        --qpf-orange: #17C9A8;
        --qpf-orange-dark: #D2241D;
        --qpf-danger: #d9483f;
        --qpf-ok: #22b378;

        font-family: 'Inter', sans-serif;
        color: var(--qpf-ink);
    }

    .qpf-scope * {
        box-sizing: border-box;
    }

    .qpf-head {
        margin-bottom: 20px;
    }

    .qpf-head h2 {
        font-family: 'Space Grotesk', sans-serif;
        font-size: 22px;
        font-weight: 700;
        margin: 0 0 6px;
        color: var(--qpf-ink);
    }

    .qpf-head p {
        font-size: 13.5px;
        color: var(--qpf-ink-soft);
        margin: 0;
    }

    .qpf-alert {
        border: 1px solid var(--qpf-line);
        border-left: 3px solid var(--qpf-ok);
        background: #f2faf6;
        padding: 12px 40px 12px 16px;
        font-size: 13.5px;
        margin-bottom: 16px;
        border-radius: 8px;
        position: relative;
    }

    .qpf-alert.qpf-alert-danger {
        border-left-color: var(--qpf-danger);
        background: #fdf3f2;
    }

    .qpf-alert ul {
        margin: 0;
        padding-left: 18px;
    }

    .qpf-form {
        display: flex;
        flex-direction: column;
        gap: 18px;
    }

    .qpf-group-label {
        font-size: 11.5px;
        font-weight: 600;
        color: var(--qpf-ink);
        text-transform: uppercase;
        letter-spacing: .03em;
        margin-bottom: 10px;
    }

    .qpf-fields {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 12px 14px;
    }

    @media (max-width: 480px) {
        .qpf-fields {
            grid-template-columns: 1fr;
        }
    }

    .qpf-field.qpf-full {
        grid-column: 1 / -1;
    }

    .qpf-field label {
        display: block;
        font-size: 11px;
        color: var(--qpf-ink-soft);
        margin-bottom: 5px;
        text-transform: uppercase;
        letter-spacing: .03em;
    }

    .qpf-input,
    .qpf-textarea {
        width: 100%;
        border: 1.5px solid var(--qpf-line);
        border-radius: 10px;
        background: var(--qpf-paper);
        padding: 10px 12px;
        font-size: 14px;
        font-family: 'Inter', sans-serif;
        color: var(--qpf-ink);
        transition: border-color .2s ease, background .2s ease;
    }

    .qpf-input:focus,
    .qpf-textarea:focus {
        outline: none;
        border-color: var(--qpf-teal);
        background: #fff;
    }

    .qpf-textarea {
        resize: vertical;
        min-height: 90px;
    }

    .qpf-radio-row {
        display: flex;
        gap: 10px;
        flex-wrap: wrap;
    }

    .qpf-radio {
        display: inline-flex;
        align-items: center;
        gap: 7px;
        font-size: 13px;
        cursor: pointer;
        border: 1.5px solid var(--qpf-line);
        border-radius: 999px;
        padding: 8px 14px;
        background: var(--qpf-paper);
    }

    .qpf-radio:has(input:checked) {
        border-color: var(--qpf-navy);
        background: var(--qpf-navy);
        color: #fff;
    }

    .qpf-services {
        display: flex;
        flex-wrap: wrap;
        gap: 8px;
    }

    .qpf-service {
        position: relative;
    }

    .qpf-service-label {
        display: inline-block;
        padding: 7px 14px;
        border: 1.5px solid var(--qpf-line);
        border-radius: 999px;
        font-size: 12.5px;
        font-weight: 500;
        cursor: pointer;
        background: #fff;
    }

    .qpf-service-input:checked+.qpf-service-label {
        background: var(--qpf-navy);
        border-color: var(--qpf-orange);
        color: #fff;
    }

    .qpf-subservices {
        display: none;
        flex-wrap: wrap;
        gap: 6px;
        position: absolute;
        top: 110%;
        left: 0;
        width: max-content;
        max-width: 260px;
        background: #fff;
        border: 1px solid var(--qpf-line);
        border-radius: 12px;
        padding: 10px;
        box-shadow: 0 12px 28px rgba(7, 12, 20, .14);
        z-index: 10;
    }

    .qpf-subservice input {
        display: none;
    }

    .qpf-subservice label {
        font-size: 12px;
        padding: 6px 11px;
        border-radius: 999px;
        background: var(--qpf-paper);
        cursor: pointer;
        margin: 0;
        white-space: nowrap;
    }

    .qpf-subservice input:checked+label {
        background: var(--qpf-orange);
        color: #fff;
    }

    .qpf-dropzone.dropzone {
        border: 1.5px dashed var(--qpf-line);
        border-radius: 12px;
        background: var(--qpf-paper);
        padding: 16px;
        min-height: auto;
        font-family: 'Inter', sans-serif;
    }

    .qpf-dropzone.dropzone .dz-message {
        margin: 0;
        font-size: 13px;
        color: var(--qpf-ink-soft);
    }

    .qpf-submit {
        align-self: flex-start;
        background: var(--qpf-orange);
        color: #fff;
        border: none;
        padding: 13px 28px;
        font-size: 14px;
        font-weight: 600;
        border-radius: 999px;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        transition: background .2s ease;
    }

    .qpf-submit:hover {
        background: var(--qpf-orange-dark);
    }

    @media (max-width: 480px) {
        .qpf-submit {
            width: 100%;
            justify-content: center;
        }
    }
</style>

<div class="qpf-scope">
    <div class="qpf-head">
        <h2>Project Brief</h2>
        <p>Tell us what you're trying to build — we'll reply with a clear scope, timeline, and a real quote.</p>
    </div>

    @if (Session::has('success'))
        <div class="qpf-alert">{{ Session::get('success') }}</div>
    @endif

    @if (Session::has('error'))
        <div class="qpf-alert qpf-alert-danger">{{ Session::get('error') }}</div>
    @endif

    @if ($errors->any())
        <div class="qpf-alert qpf-alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form id="qpfForm" class="qpf-form" method="post" action="{{ route('get-quote.store') }}"
        enctype="multipart/form-data" accept-charset="utf-8">
        @csrf
        <input type="hidden" name="work_model" value="{{ $work_model ?? '' }}">
        <input type="hidden" name="work_scope" value="{{ $work_scope ?? '' }}">

        <div>
            <div class="qpf-group-label">Contact details</div>
            <div class="qpf-fields">
                <div class="qpf-field">
                    <label for="qpf_name">{{ __('form.your_name') }}</label>
                    <input class="qpf-input" id="qpf_name" type="text" name="name" placeholder="Jane Cooper"
                        value="{{ old('name') }}" required>
                </div>
                <div class="qpf-field">
                    <label for="qpf_email">{{ __('form.email_address') }}</label>
                    <input class="qpf-input" id="qpf_email" type="email" name="email" placeholder="jane@company.com"
                        value="{{ old('email') }}" required>
                </div>
                <div class="qpf-field">
                    <label for="qpf_phone">{{ __('form.phone_no') }}</label>
                    <input class="qpf-input" id="qpf_phone" type="tel" name="phone"
                        placeholder="+1 (___) ___ ____" value="{{ old('phone') }}" required>
                </div>
                <div class="qpf-field">
                    <label for="qpf_company">{{ __('form.company') }}</label>
                    <input class="qpf-input" id="qpf_company" type="text" name="company" placeholder="Optional"
                        value="{{ old('company') }}">
                </div>
                <div class="qpf-field">
                    <label for="qpf_address">{{ __('form.address') }}</label>
                    <input class="qpf-input" id="qpf_address" type="text" name="address" placeholder="Street address"
                        value="{{ old('address') }}" required>
                </div>
                <div class="qpf-field">
                    <label for="qpf_city">{{ __('form.city') }}</label>
                    <input class="qpf-input" id="qpf_city" type="text" name="city" placeholder="City"
                        value="{{ old('city') }}" required>
                </div>
            </div>
        </div>

        <div>
            <div class="qpf-group-label">{{ __('form.prefer_contact') }}</div>
            <div class="qpf-radio-row">
                <label class="qpf-radio">
                    <input type="radio" name="prefer_contact" value="1" id="qpf_pre_email"
                        @if (old('prefer_contact') != '2') checked @endif required> Email
                </label>
                <label class="qpf-radio">
                    <input type="radio" name="prefer_contact" value="2" id="qpf_pre_phone"
                        @if (old('prefer_contact') == '2') checked @endif required> Phone
                </label>
            </div>
        </div>

        @if (isset($services))
            <div>
                <div class="qpf-group-label">{{ __('form.services') }}</div>
                <div class="qpf-services">
                    @foreach ($services as $service)
                        <div class="qpf-service">
                            <input type="checkbox" class="qpf-service-input" name="services[]"
                                value="{{ $service->id }}" id="qpf-service-{{ $service->id }}">
                            <label class="qpf-service-label"
                                for="qpf-service-{{ $service->id }}">{{ $service->short_title }}</label>

                            @if ($service->subservices && $service->subservices->count() > 0)
                                <div class="qpf-subservices" id="qpf-subservices-{{ $service->id }}">
                                    @foreach ($service->subservices as $sub)
                                        <div class="qpf-subservice">
                                            <input type="checkbox" name="sub_service[]" value="{{ $sub->short_title }}"
                                                id="qpf-sub-{{ $sub->id }}">
                                            <label for="qpf-sub-{{ $sub->id }}">{{ $sub->short_title }}</label>
                                        </div>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

        <div>
            <div class="qpf-group-label">Tell us about the project</div>
            <div class="qpf-field qpf-full">
                <textarea class="qpf-textarea" name="message" placeholder="What are you building? What does success look like?"
                    required>{{ old('message') }}</textarea>
            </div>
        </div>

        <div>
            <div class="qpf-group-label">Attachments</div>
            <div id="qpfDropzone" class="qpf-dropzone dropzone"></div>
        </div>

        <div>
            <div class="g-recaptcha mb-2" data-sitekey="{{ config('services.recaptcha.site_key') }}"></div>
            @if ($errors->has('captcha'))
                <p style="font-size:12.5px;color:var(--qpf-danger);margin-top:6px;">{{ $errors->first('captcha') }}
                </p>
            @endif
        </div>

        <button class="qpf-submit" type="submit" name="submit-form">
            Send Project Brief
            <svg width="16" height="16" viewBox="0 0 16 16" fill="none">
                <path d="M2 8H14M14 8L9 3M14 8L9 13" stroke="white" stroke-width="1.6" stroke-linecap="round"
                    stroke-linejoin="round" />
            </svg>
        </button>
    </form>
</div>

<script>
    // Guard against duplicate library loads / duplicate init if this partial
    // ever gets included more than once on the same page, or if jQuery /
    // Dropzone / reCAPTCHA are already loaded by the parent layout.
    (function() {
        function loadOnce(id, src, onload) {
            if (document.getElementById(id)) {
                if (onload) onload();
                return;
            }
            const s = document.createElement('script');
            s.id = id;
            s.src = src;
            if (onload) s.onload = onload;
            document.body.appendChild(s);
        }

        function loadCssOnce(id, href) {
            if (document.getElementById(id)) return;
            const l = document.createElement('link');
            l.id = id;
            l.rel = 'stylesheet';
            l.href = href;
            document.head.appendChild(l);
        }

        function initDropzone() {
            if (typeof Dropzone === 'undefined') return;
            Dropzone.autoDiscover = false;

            const dzElem = document.getElementById('qpfDropzone');
            if (!dzElem || dzElem.dropzone) return; // already initialized

            new Dropzone(dzElem, {
                url: "{{ route('quote.upload') }}",
                paramName: "file",
                maxFilesize: 20,
                acceptedFiles: ".jpg,.jpeg,.png,.gif,.svg,.webp,.pdf,.doc,.docx,.txt,.zip,.rar,.csv,.xls,.xlsx,.ppt,.pptx,.mp3,.avi,.mp4,.mpeg,.3gp",
                addRemoveLinks: true,
                dictDefaultMessage: "Drag files here, or click to browse",
                headers: {
                    "X-CSRF-TOKEN": "{{ csrf_token() }}",
                },
                success: function(file, response) {
                    if (response.file_name) {
                        const hiddenInput = document.createElement("input");
                        hiddenInput.type = "hidden";
                        hiddenInput.name = "uploaded_files[]";
                        hiddenInput.value = response.file_name;
                        document.querySelector("#qpfForm").appendChild(hiddenInput);
                        file._hiddenInput = hiddenInput;
                    }
                },
                removedfile: function(file) {
                    if (file.previewElement) file.previewElement.remove();
                    if (file._hiddenInput) file._hiddenInput.remove();
                },
                error: function(file, response) {
                    console.error("Dropzone error:", response);
                    alert("File upload failed!");
                },
            });
        }

        function initServiceToggle() {
            if (typeof jQuery === 'undefined') return;
            const $ = jQuery;

            $(document).off('click.qpf').on('click.qpf', '.qpf-service-label', function(e) {
                e.preventDefault();
                let parent = $(this).closest('.qpf-service');
                let checkbox = parent.find('.qpf-service-input');
                let subDiv = parent.find('.qpf-subservices');

                if (subDiv.length > 0) {
                    if (!checkbox.is(':checked')) checkbox.prop('checked', true);
                    subDiv.is(':visible') ? subDiv.stop(true, true).slideUp(300) : subDiv.stop(true, true)
                        .slideDown(300);
                } else {
                    checkbox.prop('checked', !checkbox.prop('checked'));
                }
            });

            $(document).off('change.qpf').on('change.qpf', '.qpf-subservice input[type="checkbox"]', function() {
                let parentService = $(this).closest('.qpf-service');
                let parentCheckbox = parentService.find('.qpf-service-input');
                let subDiv = parentService.find('.qpf-subservices');

                if (parentService.find('.qpf-subservice input:checked').length > 0) {
                    parentCheckbox.prop('checked', true);
                } else {
                    parentCheckbox.prop('checked', false);
                    subDiv.stop(true, true).slideUp(300);
                }
            });
        }

        loadCssOnce('qpf-dropzone-css', 'https://cdnjs.cloudflare.com/ajax/libs/dropzone/5.9.3/dropzone.min.css');

        loadOnce('qpf-jquery-js', 'https://code.jquery.com/jquery-3.6.0.min.js', function() {
            initServiceToggle();
        });
        if (typeof jQuery !== 'undefined') initServiceToggle();

        loadOnce('qpf-dropzone-js', 'https://cdnjs.cloudflare.com/ajax/libs/dropzone/5.9.3/min/dropzone.min.js',
            function() {
                initDropzone();
            });

        loadOnce('qpf-recaptcha-js', 'https://www.google.com/recaptcha/api.js');
    })();
</script>

