<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>Book a Meeting</title>

  @include('web.layouts.googlehead')
</head>
<body>

<button id="open-modal" class="button google-meet-button" style="background-color: #48bb78; color: white; padding: 12px 24px; cursor: pointer; display: flex; align-items: center;">
  <div class="logo-container">
    <img id="google-meet-img" src="https://www.gstatic.com/meet/google_meet_horizontal_wordmark_2020q4_2x_icon_124_40_292e71bcb52a56e2a9005164118f183b.png" alt="Google Meet Logo" class="meeting-logo active" />

    <img id="zoom-img" src="https://upload.wikimedia.org/wikipedia/commons/7/7b/Zoom_Communications_Logo.svg" alt="Zoom Logo" class="meeting-logo" />
  </div>
  <span style="font-weight: 600; font-size: 18px; color: white; margin-left: 12px;">Book a Meeting</span>
</button>
</div>

<div id="modal-1" class="modal__overlay" style="display: none;">
  <div class="modal__container">
    <header class="mb-3">
      <button class="modal__close" aria-label="Close modal">×</button>
      <h2 style="text-align: center" >Book a Meeting</h2>
      <h6 style="text-align: center">Select a Date and Time for the Meeting at Your Convenience</h6>
    </header>
    <div class="modal__content">
      <div id="form-message"></div>

      <form id="modal-form">
        @csrf
        <div class="row flex justify-content-between align-items-center ">
          <div class="col-6">

            <div class="floating-label-group">
              <input type="text" id="name" name="name" required placeholder=" " />
              <label for="name">Name</label>
            </div>

            <div class="floating-label-group">
              <input style="width: 237px !important;" type="tel" id="phone" name="phone" required placeholder="+1 (555) 123-4567" />
              <label for="phone">Phone</label>
            </div>

            <div class="floating-label-group">
              <input type="email" id="email" name="email" required placeholder=" " />
              <label for="email">Email</label>
            </div>

            <div class="floating-label-group">
              <input type="text" id="location" name="location" required placeholder=" " autocomplete="off" />
              <label for="location">Location</label>
            </div>
            <div id="location-suggestions" class="suggestions" style="display:none;"></div>

            <input hidden type="text" id="latitude" name="latitude">
            <input hidden type="text" id="longitude" name="longitude">
            <input hidden type="text" id="distance_time" name="distance_time">
            <input hidden type="text" id="distance_km" name="distance_km">
            <input hidden type="text" id="city" name="city">
            <input hidden type="text" id="ip" name="ip">
            <input hidden type="text" id="selected_date" name="date">

            <div class="floating-label-group">
              <input type="time" id="meeting_time" name="meeting_time" required placeholder=" " />
              <label for="meeting_time">Meeting Time</label>
            </div>
          </div>

          <div class="col-6 d-flex justify-content-end ">
            <div id="calendar"></div>
            </div>
        </div>

        <button type="submit">Save</button>
        <button type="button" id="cancel-button">Cancel</button>
      </form>
    </div>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<script>
  const openModalButton = document.getElementById('open-modal');
  const modal = document.getElementById('modal-1');
  const closeModalButton = document.querySelector('.modal__close');
  const cancelModalButton = document.getElementById('cancel-button');
  const locationInput = document.getElementById('location');
  const suggestionsBox = document.getElementById('location-suggestions');
  const messageBox = document.getElementById('form-message');
  const OPENCAGE_API_KEY = "c2c6d0469901439db4a812a841807002";

  // Intl Tel Input
  const phoneInput = window.intlTelInput(document.querySelector("#phone"), {
    initialCountry: "auto",
    geoIpLookup: function(callback) {
      fetch('https://ipapi.co/json')
        .then(res => res.json())
        .then(data => {
          document.getElementById('ip').value = data.ip;
          callback(data.country_code);
        });
    },
    utilsScript: "https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.8/js/utils.js"
  });

  // Flatpickr Inline Calendar
  flatpickr("#calendar", {
    inline: true,
    minDate: "today",
    onChange: function(selectedDates, dateStr) {
      document.getElementById('selected_date').value = dateStr;
    }
  });

  // Open modal
  openModalButton.addEventListener('click', () => {
    modal.style.display = 'flex';
  });

  // Close modal
  closeModalButton.addEventListener('click', () => {
    modal.style.display = 'none';
  });

  cancelModalButton.addEventListener('click', () => {
    modal.style.display = 'none';
  });

  // Location autocomplete using OpenCage API
  locationInput.addEventListener('input', function () {
    const query = this.value;
    if (query.length < 3) {
      suggestionsBox.style.display = 'none';
      return;
    }

    fetch(`https://api.opencagedata.com/geocode/v1/json?q=${encodeURIComponent(query)}&key=${OPENCAGE_API_KEY}`)
      .then(response => response.json())
      .then(data => {
        suggestionsBox.innerHTML = '';
        if (data.results.length > 0) {
          data.results.forEach(result => {
            const div = document.createElement('div');
            div.textContent = result.formatted;
            div.addEventListener('click', () => {
              locationInput.value = result.formatted;
              suggestionsBox.style.display = 'none';

              document.getElementById('latitude').value = result.geometry.lat;
              document.getElementById('longitude').value = result.geometry.lng;
              document.getElementById('city').value = result.components.city || result.components.town || result.components.village || "";

              // Optional: Calculate dummy distance/time values
              document.getElementById('distance_km').value = (Math.random() * 10 + 1).toFixed(2);
              document.getElementById('distance_time').value = `${Math.floor(Math.random() * 15 + 5)} mins`;
            });
            suggestionsBox.appendChild(div);
          });
          suggestionsBox.style.display = 'block';
        } else {
          suggestionsBox.style.display = 'none';
        }
      });
  });

  // Hide suggestions if clicking outside
  document.addEventListener('click', function (e) {
    if (!locationInput.contains(e.target) && !suggestionsBox.contains(e.target)) {
      suggestionsBox.style.display = 'none';
    }
  });

  // Optional: Google Meet / Zoom Logo Toggle
  const googleMeetImg = document.getElementById('google-meet-img');
  const zoomImg = document.getElementById('zoom-img');

  openModalButton.addEventListener('click', () => {
    const isZoom = Math.random() > 0.5;
    googleMeetImg.style.display = isZoom ? 'none' : 'inline';
    zoomImg.style.display = isZoom ? 'inline' : 'none';
  });

  // Handle form submission (AJAX logic placeholder)
  document.getElementById('modal-form').addEventListener('submit', function (e) {
    e.preventDefault();

    const formData = new FormData(this);
    const phoneNumber = phoneInput.getNumber();
    formData.set('phone', phoneNumber);

    fetch('/your-meeting-endpoint', {
      method: 'POST',
      headers: {
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
      },
      body: formData
    })
      .then(response => response.json())
      .then(data => {
        messageBox.textContent = data.message || "Meeting booked successfully!";
        messageBox.className = 'success-message';
        messageBox.style.display = 'block';

        this.reset();
        setTimeout(() => {
          messageBox.style.display = 'none';
          modal.style.display = 'none';
        }, 3000);
      })
      .catch(error => {
        messageBox.textContent = "Something went wrong!";
        messageBox.className = 'error-message';
        messageBox.style.display = 'block';
      });
  });
</script>

</body>
</html>