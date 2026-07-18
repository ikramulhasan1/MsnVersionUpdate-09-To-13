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
            <div class="contact-us-tag" style="color: #ffffff"><i class="fa-solid fa-circle"></i> Let's connect</div>
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
      </a>@endif @if(isset($setting->phone_one))<a class="contact-us-route" href="tel:{{ $setting->phone_one }}"><i
              class="bi bi-telephone"></i>
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
                    <option value="Web Development" {{ old('subject') == 'Web Development' ? 'selected' : '' }}>Web
                      Development
                    </option>
                    <option value="Laravel Development" {{ old('subject') == 'Laravel Development' ? 'selected' : '' }}>
                      Laravel
                      Development</option>
                    <option value="Mobile App Development" {{ old('subject') == 'Mobile App Development' ? 'selected' : '' }}>
                      Mobile App Development</option>
                    <option value="SEO & Marketing" {{ old('subject') == 'SEO & Marketing' ? 'selected' : '' }}>SEO &amp;
                      Marketing</option>
                    <option value="General Inquiry" {{ old('subject') == 'General Inquiry' ? 'selected' : '' }}>General
                      Inquiry
                    </option>
                  </select></div>
                <div class="contact-us-field contact-us-full"><label
                    for="contact-us-message">{{ __('contact.your_massage') }} <b>*</b></label><textarea
                    id="contact-us-message" name="message"
                    placeholder="Tell us about your project, goals, and expected timeline..."
                    required>{{ old('message') }}</textarea></div>
              </div>
              <div class="contact-us-submit-row"><button
                  style="background-color: #D2241D; color: white; border-style: none;" type="submit"
                  name="submit-form">Send
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
          src="{{ asset('uploads/client/' . $client->image_path) }}" alt="{{ $client->title }}"></div>@endforeach
          </div>
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