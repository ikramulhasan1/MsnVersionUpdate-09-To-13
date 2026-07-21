<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Book a Meeting</title>

    <!-- Styles -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.8/css/intlTelInput.css" />
    <link
        href="https://fonts.googleapis.com/css2?family=Manrope:wght@500;600;700;800&family=Inter:wght@400;500;600;700&family=JetBrains+Mono:wght@500&display=swap"
        rel="stylesheet" />
    @include('web.layouts.googlehead')
    <style>
        :root {
            /* --- Token system --- */
            --ink: #0B1120;
            --ink-2: #1B2A4A;
            --teal: #0EA5A0;
            --teal-light: #14B8A6;
            --gold: #C9A667;
            --gold-soft: rgba(201, 166, 103, 0.16);
            --canvas: #F8F7F3;
            --canvas-2: #F1EFE8;
            --slate: #64748B;
            --slate-2: #94A0B2;
            --border: #E6E2D8;
            --border-strong: #D8D2C4;
            --danger: #DC2626;
            --success: #157A5A;
            --radius: 18px;
            --radius-sm: 10px;
            --shadow-lg: 0 30px 70px -16px rgba(11, 17, 32, 0.35), 0 10px 24px -8px rgba(11, 17, 32, 0.16);
            --shadow-focus: 0 0 0 4px rgba(14, 165, 160, 0.14);
        }

        * {
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', 'Manrope', sans-serif;
            -webkit-font-smoothing: antialiased;
        }

        .modal__overlay {
            position: fixed;
            inset: 0;
            width: 100%;
            height: 100%;
            background: radial-gradient(120% 120% at 50% 0%, rgba(27, 42, 74, 0.55) 0%, rgba(11, 17, 32, 0.72) 100%);
            backdrop-filter: blur(6px);
            display: none;
            justify-content: center;
            align-items: center;
            z-index: 9999;
            padding: 16px;
            overflow-y: auto;
        }

        .modal__container {
            background: #fff;
            width: 100%;
            max-width: 900px;
            border-radius: var(--radius);
            position: relative;
            box-shadow: var(--shadow-lg);
            overflow: hidden;
            max-height: 92vh;
            display: flex;
            flex-direction: column;
            animation: modalRise .4s cubic-bezier(.22, 1, .36, 1);
        }

        @keyframes modalRise {
            from {
                opacity: 0;
                transform: translateY(22px) scale(.97);
            }

            to {
                opacity: 1;
                transform: translateY(0) scale(1);
            }
        }

        @media (prefers-reduced-motion: reduce) {
            .modal__container {
                animation: none;
            }
        }

        /* Signature element: layered top band — deep gradient rule + fine gold hairline */
        .modal__container::before {
            content: "";
            display: block;
            height: 6px;
            width: 100%;
            /* background: linear-gradient(90deg, var(--ink) 0%, var(--ink-2) 45%, var(--teal) 85%, var(--gold) 100%); */
            flex-shrink: 0;
        }

        .modal__scroll {
            overflow-y: auto;
            padding: 36px 40px 30px;
        }

        @media (max-width: 576px) {
            .modal__scroll {
                padding: 26px 20px 22px;
            }
        }

        .modal__close {
            position: absolute;
            right: 20px;
            top: 20px;
            font-size: 18px;
            line-height: 1;
            border: none;
            background: rgba(11, 17, 32, 0.05);
            color: var(--ink);
            width: 34px;
            height: 34px;
            border-radius: 50%;
            cursor: pointer;
            transition: background .2s ease, transform .2s ease;
        }

        .modal__close:hover {
            background: rgba(11, 17, 32, 0.1);
            transform: rotate(90deg);
        }

        .modal__header {
            text-align: center;
            margin-bottom: 30px;
        }

        .modal__badge {
            width: 54px;
            height: 54px;
            border-radius: 15px;
            background: linear-gradient(135deg, var(--ink) 0%, var(--teal) 130%);
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 16px;
            box-shadow: 0 12px 26px -8px rgba(14, 165, 160, 0.5);
        }

        .modal__badge img {
            width: 26px;
            height: 26px;
            filter: brightness(0) invert(1);
        }

        .modal__eyebrow {
            font-family: 'JetBrains Mono', monospace;
            font-size: 10.5px;
            font-weight: 500;
            letter-spacing: 0.16em;
            text-transform: uppercase;
            color: var(--teal);
            margin: 0 0 8px;
        }

        .modal__title {
            font-family: 'Manrope', sans-serif;
            font-weight: 800;
            font-size: 26px;
            color: var(--ink);
            margin: 0 0 6px;
            letter-spacing: -0.02em;
        }

        .modal__subtitle {
            font-size: 13.5px;
            color: var(--slate);
            font-weight: 500;
            margin: 0;
        }

        #form-message {
            font-size: 14px;
            text-align: center;
            min-height: 20px;
            margin-bottom: 10px;
            font-weight: 600;
        }

        .form-grid {
            display: grid;
            grid-template-columns: 1.05fr 0.95fr;
            gap: 32px;
            align-items: start;
        }

        @media (max-width: 768px) {
            .form-grid {
                grid-template-columns: 1fr;
                gap: 6px;
            }
        }

        .section-label {
            display: flex;
            align-items: center;
            gap: 8px;
            font-family: 'JetBrains Mono', monospace;
            font-size: 10.5px;
            font-weight: 500;
            text-transform: uppercase;
            letter-spacing: 0.14em;
            color: var(--slate);
            margin-bottom: 16px;
        }

        .section-label .dot {
            width: 6px;
            height: 6px;
            border-radius: 50%;
            background: var(--teal);
            flex-shrink: 0;
        }

        /* --- Floating-label modern field --- */
        .field-group {
            position: relative;
            margin-bottom: 18px;
        }

        .field-shell {
            position: relative;
            display: flex;
            align-items: center;
        }

        .field-icon {
            position: absolute;
            left: 14px;
            width: 18px;
            height: 18px;
            color: var(--slate-2);
            pointer-events: none;
            transition: color .18s ease;
        }

        .custom-input,
        .form-control {
            width: 100%;
            border: 1.5px solid var(--border);
            font-size: 15px;
            font-family: 'Inter', sans-serif;
            font-weight: 500;
            padding: 20px 14px 8px 42px;
            background-color: var(--canvas);
            border-radius: var(--radius-sm);
            color: var(--ink);
            transition: border-color .18s ease, box-shadow .18s ease, background-color .18s ease;
        }

        .field-label-float {
            position: absolute;
            left: 42px;
            top: 15px;
            font-size: 14.5px;
            color: var(--slate-2);
            font-weight: 500;
            pointer-events: none;
            transform-origin: left top;
            transition: transform .16s ease, color .16s ease, top .16s ease;
        }

        .custom-input:focus,
        .form-control:focus {
            outline: none;
            border-color: var(--teal);
            background-color: #fff;
            box-shadow: var(--shadow-focus);
        }

        .custom-input:focus~.field-icon,
        .form-control:focus~.field-icon {
            color: var(--teal);
        }

        .custom-input:focus~.field-label-float,
        .form-control:focus~.field-label-float,
        .custom-input:not(:placeholder-shown)~.field-label-float,
        .form-control:not(:placeholder-shown)~.field-label-float {
            transform: translateY(-9px) scale(0.76);
            color: var(--teal);
            font-weight: 700;
            letter-spacing: 0.02em;
        }

        .custom-input::placeholder,
        .form-control::placeholder {
            color: transparent;
        }

        /* time input needs its label pinned since it always has a shown value once set */
        .field-group--time .field-label-float {
            transform: translateY(-9px) scale(0.76);
            color: var(--slate);
            font-weight: 700;
        }

        .field-group--time .custom-input:focus~.field-label-float {
            color: var(--teal);
        }

        .autocomplete-box {
            position: absolute;
            top: 100%;
            left: 0;
            background-color: #fff;
            border: 1px solid var(--border);
            border-radius: var(--radius-sm);
            box-shadow: 0 16px 34px -10px rgba(11, 17, 32, 0.2);
            max-height: 208px;
            overflow-y: auto;
            width: 100%;
            z-index: 1000;
            margin-top: 6px;
        }

        .autocomplete-suggestion {
            padding: 11px 14px;
            cursor: pointer;
            font-size: 14px;
            font-weight: 500;
            color: var(--ink);
            border-bottom: 1px solid var(--canvas-2);
        }

        .autocomplete-suggestion:last-child {
            border-bottom: none;
        }

        .autocomplete-suggestion:hover {
            background-color: var(--gold-soft);
        }

        .calendar-panel {
            border: 1.5px solid var(--border);
            border-radius: var(--radius-sm);
            padding: 16px;
            background: linear-gradient(180deg, var(--canvas) 0%, var(--canvas-2) 100%);
        }

        #calendar {
            width: 100%;
        }

        .flatpickr-calendar {
            box-shadow: none !important;
            width: 100% !important;
            max-width: 100%;
            background: transparent !important;
        }

        .flatpickr-days,
        .dayContainer {
            width: 100% !important;
            max-width: 100%;
        }

        .flatpickr-day {
            border-radius: 8px !important;
        }

        .flatpickr-day.selected {
            background: var(--teal) !important;
            border-color: var(--teal) !important;
            box-shadow: 0 6px 14px -4px rgba(14, 165, 160, 0.6);
        }

        .flatpickr-day.today {
            border-color: var(--gold) !important;
        }

        .flatpickr-day:hover {
            background: rgba(14, 165, 160, 0.15) !important;
        }

        .flatpickr-current-month .flatpickr-monthDropdown-months,
        .flatpickr-current-month input.cur-year {
            font-family: 'Manrope', sans-serif;
            font-weight: 700;
        }

        .iti {
            width: 100%;
        }

        .iti__country-list {
            width: 280px !important;
            max-width: 90vw;
            border-radius: var(--radius-sm);
        }

        .iti__flag-container {
            padding-left: 4px;
        }

        /* Live summary — the signature confirmation strip */
        .summary-strip {
            margin-top: 18px;
            border-radius: var(--radius-sm);
            border: 1.5px dashed var(--border-strong);
            background: var(--canvas);
            padding: 14px 16px;
            display: flex;
            align-items: center;
            gap: 12px;
            transition: border-color .2s ease, background .2s ease;
        }

        .summary-strip.is-ready {
            border-style: solid;
            border-color: var(--teal);
            background: rgba(14, 165, 160, 0.06);
        }

        .summary-strip__icon {
            width: 30px;
            height: 30px;
            border-radius: 50%;
            background: var(--ink);
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
            transition: background .2s ease;
        }

        .summary-strip.is-ready .summary-strip__icon {
            background: var(--teal);
        }

        .summary-strip__icon svg {
            width: 15px;
            height: 15px;
            color: #fff;
        }

        .summary-strip__text {
            font-size: 13px;
            color: var(--slate);
            font-weight: 500;
            line-height: 1.4;
        }

        .summary-strip__text strong {
            color: var(--ink);
            font-weight: 700;
        }

        .g-recaptcha {
            margin: 20px 0 4px;
            transform: scale(0.94);
            transform-origin: left;
        }

        @media (max-width: 400px) {
            .g-recaptcha {
                transform: scale(0.85);
            }
        }

        .modal__actions {
            display: flex;
            justify-content: flex-end;
            gap: 10px;
            margin-top: 14px;
            flex-wrap: wrap;
        }

        .btn {
            font-family: 'Manrope', sans-serif;
            font-weight: 700;
            font-size: 14.5px;
            padding: 13px 26px;
            border-radius: var(--radius-sm);
            border: none;
            cursor: pointer;
            transition: transform .15s ease, box-shadow .15s ease, background .15s ease;
        }

        .btn-success {
            background: linear-gradient(135deg, var(--ink) 0%, var(--teal) 140%);
            color: #fff;
            box-shadow: 0 12px 24px -8px rgba(14, 165, 160, 0.55);
        }

        .btn-success:hover {
            transform: translateY(-1px);
            box-shadow: 0 16px 28px -8px rgba(14, 165, 160, 0.6);
        }

        .btn-success:focus-visible,
        .btn-secondary:focus-visible,
        .modal__close:focus-visible {
            outline: 2px solid var(--gold);
            outline-offset: 2px;
        }

        .btn-secondary {
            background: var(--canvas-2);
            color: var(--ink);
        }

        .btn-secondary:hover {
            background: #E7E3D9;
        }

        .text-success {
            color: var(--success) !important;
        }

        .text-danger {
            color: var(--danger) !important;
        }

        fieldset {
            border: none;
        }
    </style>
    <script src="https://www.google.com/recaptcha/api.js" async defer></script>
</head>

<body>

    {{-- <div>
  <button id="open-modal" class="button google-meet-button" style="background-color: #48bb78; color: white; padding: 12px 24px; cursor: pointer; display: flex; align-items: center;">
    <div class="logo-container">
      <img id="google-meet-img" src="https://www.gstatic.com/meet/google_meet_horizontal_wordmark_2020q4_2x_icon_124_40_292e71bcb52a56e2a9005164118f183b.png" alt="Google Meet Logo" />
      <img id="zoom-img" src="https://upload.wikimedia.org/wikipedia/commons/7/7b/Zoom_Communications_Logo.svg" alt="Zoom Logo" />
    </div>
    <span style="font-weight: 600; font-size: 18px; color: white; margin-left: 12px;">Book a Meeting</span>
  </button>
</div> --}}

    <!-- Modal -->
    <div id="modal-1" class="modal__overlay">
        <div class="modal__container">
            <div class="modal__scroll">
                <button class="modal__close" type="button" aria-label="Close">×</button>
                <header class="modal__header">
                    <div class="modal__badge">
                        <img src="https://upload.wikimedia.org/wikipedia/commons/thumb/a/a5/Google_Calendar_icon_%282020%29.svg/2048px-Google_Calendar_icon_%282020%29.svg.png"
                            alt="Calendar" />
                    </div>
                    <p class="modal__eyebrow">Schedule · 15 min</p>
                    <h2 class="modal__title">Book a Meeting</h2>
                    <p class="modal__subtitle">Select a date and time that works best for you</p>
                </header>

                <div id="form-message"></div>

                <form id="modal-form">
                    <div class="form-grid">
                        <div>
                            <p class="section-label"><span class="dot"></span>Your details</p>

                            <div class="field-group">
                                <div class="field-shell">
                                    <svg class="field-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                        stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2" />
                                        <circle cx="12" cy="7" r="4" />
                                    </svg>
                                    <input type="text" id="name" name="name" class="form-control"
                                        placeholder=" " required />
                                    <label class="field-label-float" for="name">Full name</label>
                                </div>
                            </div>

                            <div class="field-group">
                                <div class="field-shell">
                                    <input type="tel" id="phone" name="phone" class="form-control"
                                        placeholder=" " required style="padding-left: 52px;" />
                                    <label class="field-label-float" for="phone" style="left: 52px;">Phone
                                        number</label>
                                </div>
                            </div>

                            <div class="field-group">
                                <div class="field-shell">
                                    <svg class="field-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                        stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M4 4h16v16H4z" opacity="0" />
                                        <path d="M22 6 12 13 2 6" />
                                        <path d="M2 6h20v12H2z" />
                                    </svg>
                                    <input type="email" id="email" name="email" class="form-control"
                                        placeholder=" " required />
                                    <label class="field-label-float" for="email">Email address</label>
                                </div>
                            </div>

                            <div class="field-group position-relative">
                                <div class="field-shell">
                                    <svg class="field-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                        stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M20 10c0 6-8 12-8 12s-8-6-8-12a8 8 0 0 1 16 0Z" />
                                        <circle cx="12" cy="10" r="3" />
                                    </svg>
                                    <input type="text" id="location" name="location" class="form-control"
                                        placeholder=" " autocomplete="off" required />
                                    <label class="field-label-float" for="location">Location</label>
                                </div>
                                <div id="autocomplete-box" class="autocomplete-box d-none"></div>
                            </div>

                            <input type="hidden" id="latitude" name="latitude">
                            <input type="hidden" id="longitude" name="longitude">
                            <input type="hidden" id="ip" name="ip">
                            <input type="hidden" id="city" name="city">
                            <input type="hidden" id="distance_time" name="distance_time">
                            <input type="hidden" id="distance_km" name="distance_km">
                            <input type="hidden" id="selected_date" name="date">

                            <div class="field-group field-group--time mb-0">
                                <div class="field-shell">
                                    <svg class="field-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                        stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                                        <circle cx="12" cy="12" r="9" />
                                        <path d="M12 7v5l3 3" />
                                    </svg>
                                    <input type="time" id="meeting_time" name="meeting_time" class="custom-input"
                                        required />
                                    <label class="field-label-float" for="meeting_time">Meeting time</label>
                                </div>
                            </div>
                        </div>

                        <div>
                            <p class="section-label"><span class="dot"></span>Choose a date</p>
                            <div class="calendar-panel">
                                <div id="calendar"></div>
                            </div>

                            <div class="summary-strip" id="summary-strip">
                                <div class="summary-strip__icon">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"
                                        stroke-linecap="round" stroke-linejoin="round">
                                        <rect x="3" y="4" width="18" height="18" rx="3" />
                                        <path d="M16 2v4M8 2v4M3 10h18" />
                                    </svg>
                                </div>
                                <p class="summary-strip__text" id="summary-strip-text">Pick a date and time to see
                                    your booking summary here.</p>
                            </div>
                        </div>
                    </div>

                    <div class="g-recaptcha" data-sitekey="6Ldv410tAAAAAObli6t7JdOmtDeByqNt7m8CwuL_"></div>

                    <div class="modal__actions">
                        <button type="button" id="cancel-button" class="btn btn-secondary">Cancel</button>
                        <button type="submit" class="btn btn-success">Save</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.8/js/intlTelInput.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>

    <script>
        const modal = document.getElementById("modal-1");
        const openModal = document.getElementById("open-modal");
        const closeModal = document.querySelector(".modal__close");
        const cancelButton = document.getElementById("cancel-button");
        const formMessage = document.getElementById("form-message");
        const locationInput = document.getElementById("location");
        const suggestionBox = document.getElementById("autocomplete-box");
        const summaryStrip = document.getElementById("summary-strip");
        const summaryStripText = document.getElementById("summary-strip-text");
        const meetingTimeInput = document.getElementById("meeting_time");

        const phoneInput = document.querySelector("#phone");
        const iti = window.intlTelInput(phoneInput, {
            nationalMode: false,
            initialCountry: "auto",
            geoIpLookup: async function(callback) {
                try {
                    const response = await fetch("https://ipinfo.io/json?token=85d3b65b39e700");
                    const data = await response.json();
                    callback(data.country || "us");
                } catch (e) {
                    callback("us");
                }
            },
            utilsScript: "https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.8/js/utils.js"
        });

        function updateSummary() {
            const dateVal = document.getElementById("selected_date").value;
            const timeVal = meetingTimeInput.value;

            if (!dateVal || !timeVal) {
                summaryStrip.classList.remove("is-ready");
                summaryStripText.innerHTML = "Pick a date and time to see your booking summary here.";
                return;
            }

            const dateObj = new Date(`${dateVal}T${timeVal}`);
            const dateLabel = dateObj.toLocaleDateString(undefined, {
                weekday: "long",
                month: "long",
                day: "numeric"
            });
            const timeLabel = dateObj.toLocaleTimeString(undefined, {
                hour: "numeric",
                minute: "2-digit"
            });

            summaryStrip.classList.add("is-ready");
            summaryStripText.innerHTML = `You're booking <strong>${dateLabel}</strong> at <strong>${timeLabel}</strong>`;
        }

        const calendarInstance = flatpickr("#calendar", {
            inline: true,
            minDate: "today",
            defaultDate: "today",
            dateFormat: "Y-m-d",
            onChange: function(selectedDates, dateStr) {
                document.getElementById("selected_date").value = dateStr;
                updateSummary();
            }
        });

        // defaultDate populates the visible calendar but does not fire onChange,
        // so set the hidden field explicitly right after init.
        if (calendarInstance.selectedDates.length) {
            document.getElementById("selected_date").value = calendarInstance.formatDate(
                calendarInstance.selectedDates[0], "Y-m-d"
            );
        }

        meetingTimeInput.addEventListener("input", updateSummary);
        updateSummary();

        if (openModal) {
            openModal.addEventListener("click", () => {
                modal.style.display = "flex";
                fetchIPInfo();
            });
        }

        closeModal.addEventListener("click", () => modal.style.display = "none");
        cancelButton.addEventListener("click", () => modal.style.display = "none");

        async function fetchIPInfo() {
            try {
                const res = await fetch("https://ipinfo.io/json?token=85d3b65b39e700");
                const data = await res.json();
                const [lat, lon] = data.loc.split(",");
                document.getElementById("ip").value = data.ip;
                document.getElementById("city").value = data.city;
                document.getElementById("location").value = `${data.city}, ${data.region}`;
                document.getElementById("latitude").value = lat;
                document.getElementById("longitude").value = lon;
                document.getElementById("distance_time").value = "15";
                document.getElementById("distance_km").value = "5.3";
            } catch (err) {
                console.error("IP info error:", err);
            }
        }

        locationInput.addEventListener("input", async () => {
            const value = locationInput.value;
            if (value.length < 3) {
                suggestionBox.classList.add("d-none");
                return;
            }

            const url =
                `https://api.geoapify.com/v1/geocode/autocomplete?text=${encodeURIComponent(value)}&apiKey=437507f257da48b28e1d22d7f9736e62&limit=5`;
            const res = await fetch(url);
            const data = await res.json();

            suggestionBox.innerHTML = "";
            (data.features || []).forEach(place => {
                const div = document.createElement("div");
                div.className = "autocomplete-suggestion";
                div.textContent = place.properties.formatted;
                div.onclick = () => {
                    locationInput.value = place.properties.formatted;
                    document.getElementById("latitude").value = place.properties.lat;
                    document.getElementById("longitude").value = place.properties.lon;
                    suggestionBox.classList.add("d-none");
                };
                suggestionBox.appendChild(div);
            });

            suggestionBox.classList.remove("d-none");
        });

        document.getElementById("modal-form").addEventListener("submit", async function(e) {
            e.preventDefault();
            const form = e.target;

            // Guard against the hidden date field being empty (e.g. calendar re-rendered
            // or user cleared it) before it ever reaches the server.
            if (!document.getElementById("selected_date").value) {
                formMessage.textContent = "Please select a date.";
                formMessage.className = "text-danger fw-bold";
                return;
            }
            if (!document.getElementById("meeting_time").value) {
                formMessage.textContent = "Please select a meeting time.";
                formMessage.className = "text-danger fw-bold";
                return;
            }

            const formData = new FormData(form);
            formData.set("phone", iti.getNumber());

            try {
                const res = await axios.post("/meetings", formData, {
                    headers: {
                        "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').content,
                        "Content-Type": "multipart/form-data"
                    }
                });

                formMessage.textContent = res.data.message || "Meeting booked successfully!";
                formMessage.className = "text-success fw-bold";
                form.reset();
                calendarInstance.setDate("today", false);
                document.getElementById("selected_date").value = calendarInstance.formatDate(new Date(),
                    "Y-m-d");
                updateSummary();

                // Auto close after 3 seconds
                setTimeout(() => {
                    modal.style.display = "none";
                    formMessage.textContent = "";
                }, 3000);

            } catch (err) {
                const errors = err.response?.data?.errors;
                formMessage.textContent = errors ?
                    Object.values(errors).flat().join(" ") :
                    (err.response?.data?.message || "Error saving meeting.");
                formMessage.className = "text-danger fw-bold";
            }
        });
    </script>

</body>

</html>
