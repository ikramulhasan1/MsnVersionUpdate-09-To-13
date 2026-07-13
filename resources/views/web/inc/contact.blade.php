<<<<<<< HEAD
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
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet" />

  <style>

    .contact-container {
      background: #ffffff;
      border-radius: 0px;
      /* box-shadow: 0 10px 30px rgba(0,0,0,0.15); */
      overflow: hidden;
      width: 100%;
      max-width: 1200px;
      display: flex;
      flex-wrap: wrap;
      margin: auto;
      margin-top: 80px;
      margin-bottom: 80px;
    }

    .form-section {
      flex: 1 1 55%;
      padding: 50px;
      background-color: #052C58;
      width: 100%;
    }

    .form-section h3 {
      color: #000000;
      font-size: 28px;
      font-weight: 900;
    }

    .dayContainer {
      padding: 15 !important;
      width: 400px !important;
      min-width: 400 !important;
      max-width: 400px !important;
      height: 220px !important;
    }

    .calendar-section {
      flex: 1 1 45%;
      /* background: #fff; */
      display: flex;
      align-items: center;
      justify-content: center;
      padding: 0px;
      border-left: 1px solid #eee;
      background:
        linear-gradient(rgba(0, 0, 0, 0.6), rgba(0, 0, 0, 0.6)),
        url('https://t4.ftcdn.net/jpg/10/95/98/59/240_F_1095985933_J2wC9izxs9fZHvvgFxPC7sKutX8ntwhl.jpg');
      background-repeat: no-repeat;
      background-size: cover;
      background-position: center center;
      width: 100%;
    }


    .calendar-wrapper {
      /* background: linear-gradient(135deg, #2575fc 0%, #6a11cb 100%); */
      padding: 50px 40px;
      border-radius: 5px;
      box-shadow: 0 8px 20px rgba(0, 0, 0, 0.178);
      width: 100%;
      max-width: 470px;

      position: relative;
      /* background: rgba(0, 0, 0, 0.564); */

    }

    .calendar-wrapper h2 {
      color: #ffffff;
      font-size: 28px;
      text-align: center;
      margin-bottom: 20px;
      font-weight: 700;
    }

    /* Modern input fields */
    .form-control {
      background: #fff;
      border: 1px solid #ccc;
      border-radius: 2px;
      padding: 8px 12px;
      font-size: 16px;
      color: #333;
      transition: all 0.3s ease;
      margin-bottom: 20px;
    }

    .form-control:focus {
      border-color: #6a11cb;
      box-shadow: 0 0 0 4px rgba(106, 17, 203, 0.15);
      background-color: #fff;
      outline: none;
    }

    .btn-primary {
      background: linear-gradient(135deg, #6a11cb 0%, #2575fc 100%);
      border: none;
      border-radius: 5px;
      padding: 14px 30px;
      font-size: 16px;
      font-weight: 600;
      transition: background 0.3s;
      width: 100%;
    }

    .btn-primary:hover {
      background: linear-gradient(135deg, #2575fc 0%, #6a11cb 100%);
    }

    /* Make Flatpickr Calendar bigger and better */
    .flatpickr-calendar {
      font-size: 1rem;
      width: 100% !important;
      max-width: 100% !important;
      border-radius: 5px;
      overflow: hidden;
      box-shadow: 0 8px 20px rgba(0, 0, 0, 0.2);
    }

    .flatpickr-months {
      font-size: 1rem;
      background: #fff;
      padding: 8px 0;
      border-bottom: 1px solid #eee;
    }

    .flatpickr-next-month {
      margin-top: 10px;
    }

    .flatpickr-weekdays {
      background: #f0f0f0;
      font-size: 0.9rem;
      color: #555;
    }

    .flatpickr-days {
      width: 400px !important;
    }

    .flatpickr-day {
      height: 50px;
      line-height: 50px;
      width: 50px;
      font-size: 1.1rem;
      border-radius: 8px;
      font-weight: 700;
      transition: all 0.2s;
    }

    .flatpickr-day:hover {
      background: #2575fc;
      color: #fff;
      border-radius: 8px;
    }

    /* .flatpickr-day.today {
      background: #6a11cb;
      color: white;
      border-radius: 8px;
    } */
    @media (max-width: 575.98px) {
      .form-section {
        flex: 1 1 55%;
        padding: 50px;
        background-color: #052C58;
      }

      .form-section h3 {
        color: #000000;
        font-size: 28px;
        font-weight: 900;
      }

      .calendar-section {
        flex: 1 1 45%;
        /* background: #fff; */
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 0px;
        border-left: 1px solid #eee;
        background:
          linear-gradient(rgba(0, 0, 0, 0.6), rgba(0, 0, 0, 0.6)),
          url('https://t4.ftcdn.net/jpg/10/95/98/59/240_F_1095985933_J2wC9izxs9fZHvvgFxPC7sKutX8ntwhl.jpg');
        background-repeat: no-repeat;
        background-size: cover;
        background-position: center center;
      }


    }

    @media (min-width: 576px) {
      .form-section {
        flex: 1 1 55%;
        padding: 50px;
        background-color: #052C58;
      }

      .form-section h3 {
        color: #000000;
        font-size: 28px;
        font-weight: 900;
      }

      .calendar-section {
        flex: 1 1 45%;
        /* background: #fff; */
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 0px;
        border-left: 1px solid #eee;
        background:
          linear-gradient(rgba(0, 0, 0, 0.6), rgba(0, 0, 0, 0.6)),
          url('https://t4.ftcdn.net/jpg/10/95/98/59/240_F_1095985933_J2wC9izxs9fZHvvgFxPC7sKutX8ntwhl.jpg');
        background-repeat: no-repeat;
        background-size: cover;
        background-position: center center;
      }


    }

    @media (max-width: 768px) {
      .contact-container {
        flex-direction: column;
      }

      .calendar-section {
        border-left: none;
        border-top: 1px solid #eee;

        background-repeat: no-repeat;
        background-size: cover;
        background-position: center center;
      }
    }

    .flatpickr-rContainer {
      width: 100%;
    }

    #phone {
      width: 420px;
      /* margin-bottom: 20px !important; */
    }

    #email {
      margin-top: 20px !important;
    }

    .selected {
      background-color: #3CC065 !important;
    }
  </style>
</head>

<body>

  <div class="contact-container">
    <div class="form-section">
      <h3 class="mb-4 text-white">Let's schedule your meeting</h3>
      <div id="form-message" class="text-center mb-3 fw-bold"></div>

      <form id="booking-form">
        <input type="text" id="name" name="name" class="form-control" placeholder="Your Name" required />
        <input type="tel" id="phone" name="phone" class="form-control" placeholder="Phone Number" required />
        <input type="email" id="email" name="email" class="form-control" placeholder="Email Address" required />
        <input type="text" id="location" name="location" class="form-control" placeholder="Location" autocomplete="off"
          required />
        <input type="time" id="meeting_time" name="meeting_time" class="form-control" required />
        <input type="hidden" id="selected_date" name="date">
        <input type="hidden" id="latitude" name="latitude">
        <input type="hidden" id="longitude" name="longitude">
        <input type="hidden" id="ip" name="ip">
        <input type="hidden" id="city" name="city">
        <input type="hidden" id="distance_time" name="distance_time">
        <input type="hidden" id="distance_km" name="distance_km">

<div class="g-recaptcha" data-sitekey="{{ env('RECAPTCHA_SITE_KEY') }}"></div>
        <button type="submit" class="btn btn-primary mt-3">Book a Meeting</button>
      </form>
    </div>

    <div class="calendar-section">
      <div class="calendar-wrapper">
        <h2>Select Date</h2>
        <div id="calendar"></div>
      </div>
    </div>
  </div>

  <!-- Scripts -->
  <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.8/js/intlTelInput.min.js"></script>

  <script>
    const formMessage = document.getElementById("form-message");
    const locationInput = document.getElementById("location");
    const phoneInput = document.querySelector("#phone");

    const iti = window.intlTelInput(phoneInput, {
      nationalMode: false,
      initialCountry: "auto",
      geoIpLookup: async function (callback) {
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

    flatpickr("#calendar", {
      inline: true,
      minDate: "today",
      onChange: function (selectedDates, dateStr) {
        document.getElementById("selected_date").value = dateStr;
      }
    });

    // Auto Fetch IP Location Info when page loads
    document.addEventListener("DOMContentLoaded", function () {
      fetchIPInfo();
    });

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
        document.getElementById("distance_time").value = "15";  // default
        document.getElementById("distance_km").value = "5.3";   // default
      } catch (err) {
        console.error("IP info error:", err);
      }
    }

    // Submit Form
    document.getElementById("booking-form").addEventListener("submit", async function (e) {
      e.preventDefault();
      const form = e.target;
      const formData = new FormData(form);
      formData.set("phone", iti.getNumber());

      try {
        const res = await fetch("/meetings", {
          method: "POST",
          headers: {
            "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').content,
          },
          body: formData
        });

        const result = await res.json();

        formMessage.textContent = result.message || "Meeting booked successfully!";
        formMessage.className = "text-success fw-bold";
        form.reset();

        setTimeout(() => {
          formMessage.textContent = "";
        }, 3000);

      } catch (err) {
        formMessage.textContent = "Error saving meeting.";
        formMessage.className = "text-danger fw-bold";
      }
    });

  </script>
<script src="https://www.google.com/recaptcha/api.js" async defer></script>


</body>

</html>
=======
@extends('web.layouts.master')
@php($header = \App\Models\PageSetup::page('contact-us'))
@if(isset($header))
@section('title', $header->meta_title)
@section('top_meta_tags')
  <meta name="description"
    content="{!! str_limit(strip_tags($header->meta_description ?? $setting->description), 160, ' ...') !!}">
  <meta name="keywords" content="{!! strip_tags($header->meta_keywords ?? $setting->keywords) !!}">
@endsection
@endif

@section('content')
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@400;500;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="{{ asset('css/contact-us.css') }}">
  <main class="contact-us">
    <section class="contact-us-hero">
      <div class="container">
        <div class="contact-us-crumb"><a
            href="{{ route('home') }}">{{ __('navbar.home') }}</a><span>/</span>{{ __('navbar.contact') }}</div>
        <div class="contact-us-hero-grid">
          <div>
            <div class="contact-us-tag"><i class="fa-solid fa-circle"></i> Let's connect</div>
            <h1>Contact Us</h1>
            <p>Tell us what you are trying to build. We will reply with a clear scope, realistic timeline, and a
              straightforward quote.</p>
          </div>
          <div class="contact-us-hero-side">
            <div><strong>&lt;24</strong><span>hours reply time</span></div>
          </div>
        </div>
      </div>
    </section>
    <section class="contact-us-routes">
      <div class="container contact-us-routes-grid">@if(isset($setting->email_one))<a class="contact-us-route"
        href="mailto:{{ $setting->email_one }}"><i class="bi bi-envelope-arrow-up"></i>
        <div><small>Email us</small><strong>{{ $setting->email_one }}</strong></div>
      </a>@endif @if(isset($setting->phone_one))<a class="contact-us-route" href="tel:{{ $setting->phone_one }}"><i class="bi bi-telephone"></i>
            <div><small>Call us</small><strong>{{ $setting->phone_one }}</strong></div>
          </a><a class="contact-us-route" href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $setting->phone_one) }}"
            target="_blank" rel="noopener"><i class="bi bi-whatsapp"></i>
            <div><small>WhatsApp</small><strong>Start a conversation</strong></div>
        </a>@endif</div>
    </section>
    <section class="contact-us-main">
      <div class="container">
        <div class="contact-us-title">
          <div class="contact-us-tag"><i class="fa-solid fa-circle"></i> Project enquiry</div>
          <h2>Let's discuss what is next.</h2>
        </div>
        <div class="contact-us-work">
          <div class="contact-us-form">
            <div class="contact-us-form-head">
              <h3>Send us a message</h3><span>* Required fields</span>
            </div>@if(Session::has('success'))
              <div class="contact-us-alert"><button type="button" class="contact-us-alert-close"
                  onclick="this.parentElement.style.display='none'">&times;</button>{{ Session::get('success') }}</div>
            @endif @if(Session::has('error'))
              <div class="contact-us-alert contact-us-alert-error"><button type="button" class="contact-us-alert-close"
            onclick="this.parentElement.style.display='none'">&times;</button>{{ Session::get('error') }}</div>@endif
            @if($errors->any())
              <div class="contact-us-alert contact-us-alert-error">
                <ul>@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
            </div>@endif
            <form method="post" action="{{ route('contact.send') }}" accept-charset="utf-8">@csrf<div
                class="contact-us-fields">
                <div class="contact-us-field"><label for="contact-us-name">{{ __('contact.your_name') }}
                    <b>*</b></label><input id="contact-us-name" type="text" name="name" value="{{ old('name') }}"
                    placeholder="Jane Doe" required></div>
                <div class="contact-us-field"><label for="contact-us-phone">{{ __('contact.phone_no') }}</label><input
                    id="contact-us-phone" type="text" name="phone" value="{{ old('phone') }}"
                    placeholder="+880 1XXX-XXXXXX"></div>
                <div class="contact-us-field"><label for="contact-us-email">{{ __('contact.email_address') }}
                    <b>*</b></label><input id="contact-us-email" type="email" name="email" value="{{ old('email') }}"
                    placeholder="you@company.com" required></div>
                <div class="contact-us-field"><label for="contact-us-subject">{{ __('contact.subject') }}
                    <b>*</b></label><select id="contact-us-subject" name="subject" required>
                    <option value="" disabled {{ old('subject') ? '' : 'selected' }}>Select a topic</option>
                    <option value="Web Development" {{ old('subject') == 'Web Development' ? 'selected' : '' }}>Web Development
                    </option>
                    <option value="Laravel Development" {{ old('subject') == 'Laravel Development' ? 'selected' : '' }}>Laravel
                      Development</option>
                    <option value="Mobile App Development" {{ old('subject') == 'Mobile App Development' ? 'selected' : '' }}>
                      Mobile App Development</option>
                    <option value="SEO & Marketing" {{ old('subject') == 'SEO & Marketing' ? 'selected' : '' }}>SEO &amp;
                      Marketing</option>
                    <option value="General Inquiry" {{ old('subject') == 'General Inquiry' ? 'selected' : '' }}>General Inquiry
                    </option>
                  </select></div>
                <div class="contact-us-field contact-us-full"><label
                    for="contact-us-message">{{ __('contact.your_massage') }} <b>*</b></label><textarea
                    id="contact-us-message" name="message"
                    placeholder="Tell us about your project, goals, and expected timeline..."
                    required>{{ old('message') }}</textarea></div>
              </div>
              <div class="contact-us-submit-row"><button class="contact-us-submit" type="submit" name="submit-form">Send
                  message <i class="fa-solid fa-arrow-right"></i></button>
                <p class="contact-us-note">We only use your details to respond to your enquiry. Your information stays
                  private.</p>
              </div>
            </form>
          </div>
          <aside class="contact-us-side">
            <div class="contact-us-tag"><i class="fa-solid fa-circle"></i> Direct contact</div>
            <h3>We are here<br>to help.</h3>@if(isset($setting->email_one))
              <div class="contact-us-detail"><i class="bi bi-envelope-arrow-up"></i>
                <div><span>Email</span><a href="mailto:{{ $setting->email_one }}">{{ $setting->email_one }}</a></div>
            </div>@endif @if(isset($setting->phone_one))
              <div class="contact-us-detail"><i class="bi bi-telephone"></i>
                <div><span>Phone</span><a href="tel:{{ $setting->phone_one }}">{{ $setting->phone_one }}</a></div>
            </div>@endif @if(isset($setting->contact_address))
              <div class="contact-us-detail"><i class="fa-solid fa-location-dot"></i>
                <div><span>Location</span>
                  <div>{{ $setting->contact_address }}</div>
                </div>
            </div>@endif @if(isset($setting->office_hours))
              <div class="contact-us-hours"><strong>Office hours</strong>{!! strip_tags($setting->office_hours, '<br>') !!}
            </div>@endif @if(isset($setting->google_map))
            <div class="contact-us-map">{!! strip_tags($setting->google_map, '<iframe>') !!}</div>@endif
          </aside>
        </div>
      </div>
    </section>
    <section class="contact-us-process">
      <div class="container contact-us-process-grid">
        <div class="contact-us-process-copy">
          <div class="contact-us-tag"><i class="fa-solid fa-circle"></i> What happens next</div>
          <h2>A clear process, from the first message.</h2>
          <p>No sales pressure or vague replies. We learn about the opportunity and give you useful, honest direction.</p>
        </div>
        <div class="contact-us-steps">
          <article class="contact-us-step"><span class="contact-us-step-no">01</span>
            <h4>We review</h4>
            <p>Your request is read by the right person on our team.</p>
          </article>
          <article class="contact-us-step"><span class="contact-us-step-no">02</span>
            <h4>We clarify</h4>
            <p>We may ask focused questions to understand your goals.</p>
          </article>
          <article class="contact-us-step"><span class="contact-us-step-no">03</span>
            <h4>We respond</h4>
            <p>You receive a practical next step, estimate, or call invite.</p>
          </article>
        </div>
      </div>
    </section>
    <section class="contact-us-faq">
      <div class="container contact-us-faq-wrap">
        <div class="contact-us-faq-head">
          <div class="contact-us-tag"><i class="fa-solid fa-circle"></i> Common questions</div>
          <h2>Before you write in</h2>
        </div>
        <div class="contact-us-faq-item"><button class="contact-us-faq-q" type="button"><small>01</small><strong>How soon
              will I hear back?</strong><i class="fa-solid fa-plus"></i></button>
          <div class="contact-us-faq-a">
            <p>Most enquiries receive a reply within 24 hours on business days, with a clear recommendation for the next
              step.</p>
          </div>
        </div>
        <div class="contact-us-faq-item"><button class="contact-us-faq-q" type="button"><small>02</small><strong>Do you
              work with startups and small teams?</strong><i class="fa-solid fa-plus"></i></button>
          <div class="contact-us-faq-a">
            <p>Yes. We work with early-stage startups as well as established organisations, tailoring the approach to your
              needs.</p>
          </div>
        </div>
        <div class="contact-us-faq-item"><button class="contact-us-faq-q" type="button"><small>03</small><strong>Can we
              schedule a call instead?</strong><i class="fa-solid fa-plus"></i></button>
          <div class="contact-us-faq-a">
            <p>Of course. Use the meeting option below to choose a suitable time for an online conversation.</p>
          </div>
        </div>
        <div class="contact-us-faq-item"><button class="contact-us-faq-q" type="button"><small>04</small><strong>What do
              you need to prepare a quote?</strong><i class="fa-solid fa-plus"></i></button>
          <div class="contact-us-faq-a">
            <p>A short outline of the project, desired result, timeline, and budget range is enough to begin.</p>
          </div>
        </div>
      </div>
    </section>
    @if(isset($clients) && count($clients) > 0)
      <section class="contact-us-clients">
        <div class="container">
          <p>Businesses and teams worldwide trust us</p>
          <div class="contact-us-logos">@foreach($clients as $client)<div class="contact-us-logo"><img
          src="{{ asset('uploads/client/' . $client->image_path) }}" alt="{{ $client->title }}"></div>@endforeach</div>
        </div>
    </section>@endif
    <section class="contact-us-cta">
      <div class="container">
        <div class="contact-us-tag"><i class="fa-solid fa-circle"></i> Let's talk</div>
        <h2>Have a project in mind? Let's build it right.</h2>
        <p>Book a short call and speak with our team directly.</p><button id="open-modal"
          class="contact-us-book google-meet-button">Book a meeting <i class="fa-solid fa-arrow-right"></i></button>
      </div>
    </section>
    @include('web.layouts.googlemeet')
  </main>
  <script>document.addEventListener('DOMContentLoaded', function () { document.querySelectorAll('.contact-us-faq-q').forEach(function (b) { b.addEventListener('click', function () { var item = b.parentElement, open = item.classList.contains('contact-us-open'); document.querySelectorAll('.contact-us-faq-item.contact-us-open').forEach(function (x) { x.classList.remove('contact-us-open') }); if (!open) item.classList.add('contact-us-open') }) }) })</script>
@endsection
>>>>>>> e734773df (msn 2.0 theme change)
