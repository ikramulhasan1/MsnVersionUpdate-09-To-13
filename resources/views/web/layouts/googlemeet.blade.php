<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>Book a Meeting</title>

  <!-- Stylesheets -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css" />
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.8/css/intlTelInput.css" />
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/css/bootstrap.min.css" />
  <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@500;600;700&display=swap" rel="stylesheet" />

  <!-- JS Libraries -->
  <script src="https://cdn.jsdelivr.net/npm/vanillajs-modal@1.1.2/dist/vanilla.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.8/js/intlTelInput.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
  <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>

  @include('web.layouts.googlehead')
</head>
<body>

<!-- Trigger Button -->
<button id="open-modal" class="google-meet-button">
  <div class="logo-container">
    <img id="google-meet-img" src="https://www.gstatic.com/meet/google_meet_horizontal_wordmark_2020q4_2x_icon_124_40_292e71bcb52a56e2a9005164118f183b.png" alt="Google Meet Logo" class="meeting-logo active" />
    <img id="zoom-img" src="https://upload.wikimedia.org/wikipedia/commons/7/7b/Zoom_Communications_Logo.svg" alt="Zoom Logo" class="meeting-logo" />
  </div>
  <span>Book a Meeting</span>
</button>

<!-- Modal Structure -->
<div id="modal-1" class="modal__overlay" style="display: none;">
  <div class="modal__container">
    <header class="mb-3">
      <button class="modal__close">×</button>
      <h2 style="text-align: center">Book a Meeting</h2>
      <h6 style="text-align: center">Select a Date and Time for the Meeting</h6>
    </header>
    <div class="modal__content">
      <div id="form-message"></div>
      <form id="modal-form">
        <div class="row">
          <div class="col-6">
            <div class="floating-label-group">
              <input type="text" id="name" name="name" required placeholder=" "/>
              <label for="name">Name</label>
            </div>

            <div class="floating-label-group">
              <input type="tel" id="phone" name="phone" required placeholder="+1 (555) 123-4567"/>
              <label for="phone">Phone</label>
            </div>

            <div class="floating-label-group">
              <input type="email" id="email" name="email" required placeholder=" "/>
              <label for="email">Email</label>
            </div>

            <div class="floating-label-group">
              <input type="text" id="location" name="location" required placeholder=" " autocomplete="off"/>
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
              <input type="time" id="meeting_time" name="meeting_time" required placeholder=" "/>
              <label for="meeting_time">Meeting Time</label>
            </div>
          </div>

          <!-- Calendar -->
          <div class="col-6 d-flex justify-content-end">
            <div id="calendar"></div>
          </div>
        </div>

        <button type="submit">Save</button>
        <button type="button" id="cancel-button">Cancel</button>
      </form>
    </div>
  </div>
</div>

<script>
// Phone Input
const phoneInput = document.querySelector("#phone");
const iti = window.intlTelInput(phoneInput, {
  nationalMode: false,
  initialCountry: "us",
  utilsScript: "https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.8/js/utils.js"
});

// Calendar
flatpickr("#calendar", {
  inline: true,
  minDate: "today",
  onChange: function(selectedDates, dateStr) {
    document.getElementById('selected_date').value = dateStr;
  }
});

// Modal logic
const modal = document.getElementById("modal-1");
const openModal = document.getElementById("open-modal");
const closeModal = document.querySelector(".modal__close");
const cancelButton = document.getElementById("cancel-button");

openModal.addEventListener("click", () => {
  modal.style.display = "flex";
  getUserIP(); // 🔥 Get IP when modal opens
});

closeModal.addEventListener("click", () => modal.style.display = "none");
cancelButton.addEventListener("click", () => modal.style.display = "none");

// IP Fetch
async function getUserIP() {
  try {
    const res = await fetch('https://api.ipify.org?format=json');
    const data = await res.json();
    document.getElementById("ip").value = data.ip;
  } catch (err) {
    console.error("IP fetch failed", err);
  }
}

// HERE API - Distance/Time
async function calculateDistanceTime() {
  const lat = document.getElementById("latitude").value;
  const lng = document.getElementById("longitude").value;

  // Office Location
  const baseLat = 40.712776;  // Example: New York
  const baseLng = -74.005974;

  const url = `https://router.hereapi.com/v8/routes?transportMode=car&origin=${baseLat},${baseLng}&destination=${lat},${lng}&return=summary&apikey=c2c6d0469901439db4a812a841807002`;

  try {
    const res = await fetch(url);
    const data = await res.json();
    const summary = data.routes[0].sections[0].summary;

    document.getElementById("distance_time").value = Math.round(summary.duration / 60) + " mins";
    document.getElementById("distance_km").value = (summary.length / 1000).toFixed(2);
  } catch (err) {
    console.error("Distance calculation failed", err);
  }
}

// Location Autocomplete
const locationInput = document.getElementById("location");
const suggestionBox = document.getElementById("location-suggestions");

locationInput.addEventListener("input", async () => {
  const query = locationInput.value;
  if (query.length < 3) return suggestionBox.style.display = "none";

  const res = await fetch(`https://api.opencagedata.com/geocode/v1/json?q=${query}&key=c2c6d0469901439db4a812a841807002`);
  const data = await res.json();
  suggestionBox.innerHTML = "";

  data.results.forEach(item => {
    const div = document.createElement("div");
    div.textContent = item.formatted;
    div.style.cursor = "pointer";
    div.addEventListener("click", () => {
      locationInput.value = item.formatted;
      document.getElementById("latitude").value = item.geometry.lat;
      document.getElementById("longitude").value = item.geometry.lng;
      document.getElementById("city").value = item.components.city || item.components.town || "";
      suggestionBox.style.display = "none";
      calculateDistanceTime(); // 🔥 Trigger HERE API
    });
    suggestionBox.appendChild(div);
  });
  suggestionBox.style.display = "block";
});

// Submit via AJAX
const form = document.getElementById("modal-form");
const messageBox = document.getElementById("form-message");

form.addEventListener("submit", async function(e) {
  e.preventDefault();
  messageBox.style.display = "none";

  const formData = new FormData(form);
  formData.set('phone', iti.getNumber());

  try {
    const res = await axios.post('/meetings', formData, {
      headers: {
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
        'Content-Type': 'multipart/form-data'
      }
    });
    messageBox.textContent = res.data.message || "Meeting booked successfully!";
    messageBox.className = "success-message";
    messageBox.style.display = "block";
    form.reset();
  } catch (err) {
    messageBox.textContent = err.response?.data?.message || "An error occurred.";
    messageBox.className = "error-message";
    messageBox.style.display = "block";
  }
});
</script>
</body>
</html>
