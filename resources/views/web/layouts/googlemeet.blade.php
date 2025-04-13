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

<button id="open-modal" class="btn btn-primary">Book a Meeting</button>

<!-- Modal -->
<div id="modal-1" class="modal__overlay" style="display: none;">
  <div class="modal__container">
    <header class="mb-3">
      <button class="modal__close">×</button>
      <h2 style="text-align: center">Book a Meeting</h2>
    </header>
    <div class="modal__content">
      <div id="form-message"></div>
      <form id="modal-form">
        <div class="row">
          <div class="col-6">
            <input type="text" id="name" name="name" class="form-control mb-2" placeholder="Name" required />
            <input type="tel" id="phone" name="phone" class="form-control mb-2" placeholder="Phone" required />
            <input type="email" id="email" name="email" class="form-control mb-2" placeholder="Email" required />
            <input type="text" id="location" name="location" class="form-control mb-2" placeholder="Location" required />

            <!-- Hidden Fields -->
            <input type="hidden" id="latitude" name="latitude">
            <input type="hidden" id="longitude" name="longitude">
            <input type="hidden" id="city" name="city">
            <input type="hidden" id="ip" name="ip">
            <input type="hidden" id="distance_time" name="distance_time">
            <input type="hidden" id="distance_km" name="distance_km">
            <input type="hidden" id="selected_date" name="date">

            <input type="time" id="meeting_time" name="meeting_time" class="form-control mb-2" required />
          </div>
          <div class="col-6">
            <div id="calendar"></div>
          </div>
        </div>
        <button type="submit" class="btn btn-success">Save</button>
        <button type="button" id="cancel-button" class="btn btn-secondary">Cancel</button>
      </form>
    </div>
  </div>
</div>

<script>
// Phone Input
const iti = window.intlTelInput(document.querySelector("#phone"), {
  nationalMode: false,
  initialCountry: "us",
  utilsScript: "https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.8/js/utils.js"
});

// Flatpickr
flatpickr("#calendar", {
  inline: true,
  minDate: "today",
  onChange: function(selectedDates, dateStr) {
    document.getElementById("selected_date").value = dateStr;
  }
});

// Modal logic
const modal = document.getElementById("modal-1");
const openModal = document.getElementById("open-modal");
const closeModal = document.querySelector(".modal__close");
const cancelButton = document.getElementById("cancel-button");

openModal.addEventListener("click", () => {
  modal.style.display = "flex";
  fetchIPInfo(); // 🔥 Trigger on open
});

closeModal.addEventListener("click", () => modal.style.display = "none");
cancelButton.addEventListener("click", () => modal.style.display = "none");

// Auto IP & Location with IPinfo
async function fetchIPInfo() {
  try {
    const res = await fetch("https://ipinfo.io/json?token=85d3b65b39e700");
    const data = await res.json();

    const loc = data.loc.split(","); // "lat,lng"
    const latitude = loc[0];
    const longitude = loc[1];

    document.getElementById("ip").value = data.ip;
    document.getElementById("city").value = data.city;
    document.getElementById("location").value = `${data.city}, ${data.region}`;
    document.getElementById("latitude").value = latitude;
    document.getElementById("longitude").value = longitude;

    calculateDistanceTime(latitude, longitude);
  } catch (err) {
    console.error("IPinfo location fetch failed", err);
  }
}

// HERE API Distance + Time
async function calculateDistanceTime(lat, lng) {
  const baseLat = 40.712776; // Office latitude (example: NY)
  const baseLng = -74.005974;

  const url = `https://router.hereapi.com/v8/routes?transportMode=car&origin=${baseLat},${baseLng}&destination=${lat},${lng}&return=summary&apikey=c2c6d0469901439db4a812a841807002`;

  try {
    const res = await fetch(url);
    const data = await res.json();
    const summary = data.routes[0].sections[0].summary;

    document.getElementById("distance_time").value = Math.round(summary.duration / 60) + " mins";
    document.getElementById("distance_km").value = (summary.length / 1000).toFixed(2);
  } catch (err) {
    console.error("HERE API error", err);
  }
}

// AJAX Form Submission
const form = document.getElementById("modal-form");
form.addEventListener("submit", async function (e) {
  e.preventDefault();
  const formData = new FormData(form);
  formData.set("phone", iti.getNumber());

  try {
    const res = await axios.post("/meetings", formData, {
      headers: {
        "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').content,
        "Content-Type": "multipart/form-data"
      }
    });
    document.getElementById("form-message").textContent = res.data.message || "Meeting booked!";
    form.reset();
  } catch (err) {
    document.getElementById("form-message").textContent = err.response?.data?.message || "An error occurred.";
  }
});
</script>
</body>
</html>
