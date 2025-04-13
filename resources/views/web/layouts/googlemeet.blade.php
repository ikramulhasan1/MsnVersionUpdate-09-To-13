<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>Book a Meeting</title>

  <!-- Bootstrap CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <!-- Flatpickr CSS -->
  <link href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css" rel="stylesheet">
  <!-- Intl-Tel-Input CSS -->
  <link href="https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.8/css/intlTelInput.min.css" rel="stylesheet">
@include('web.layouts.googlehead')
</head>
<body style="font-family: sans-serif; background-color: #f8f9fa;">

<div class="container py-5">
  <h2 class="mb-4">Book a Meeting</h2>

  <form id="meetingForm">
    <div class="row g-3">
      <div class="col-md-6">
        <input type="text" name="name" class="form-control" placeholder="Name" required>
        <input type="tel" id="phone" name="phone" class="form-control mt-2" placeholder="Phone" required>
        <input type="email" name="email" class="form-control mt-2" placeholder="Email" required>
        <input type="text" id="location" name="location" class="form-control mt-2" placeholder="Location" required>
        <input type="time" name="meeting_time" class="form-control mt-2" required>
      </div>

      <div class="col-md-6">
        <div id="calendar"></div>
        <input type="hidden" id="date" name="date">
        <button type="submit" class="btn btn-primary mt-3">Submit</button>
        <div id="form-message" class="mt-3 text-success"></div>
      </div>
    </div>

    <!-- Hidden Fields -->
    <input type="hidden" id="city" name="city">
    <input type="hidden" id="ip" name="ip">
    <input type="hidden" id="latitude" name="latitude">
    <input type="hidden" id="longitude" name="longitude">
    <input type="hidden" id="distance_time" name="distance_time">
    <input type="hidden" id="distance_km" name="distance_km">
  </form>
</div>

<!-- JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.8/js/intlTelInput.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>

<script>
  // Initialize intl-tel-input
  const phoneInput = document.querySelector("#phone");
  const iti = window.intlTelInput(phoneInput, {
    nationalMode: false,
    initialCountry: "auto",
    geoIpLookup: function(success, failure) {
      fetch("https://ipinfo.io/json?token=85d3b65b39e700")
        .then(resp => resp.json())
        .then(resp => success(resp.country))
        .catch(failure);
    },
    utilsScript: "https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.8/js/utils.js"
  });

  // Initialize Flatpickr
  flatpickr("#calendar", {
    inline: true,
    minDate: "today",
    onChange: function(selectedDates, dateStr) {
      document.getElementById("date").value = dateStr;
    }
  });

  // Get user location using IPinfo
  async function fetchLocationByIP() {
    try {
      const res = await fetch("https://ipinfo.io/json?token=85d3b65b39e700");
      const data = await res.json();
      const [lat, lng] = data.loc.split(",");

      document.getElementById("ip").value = data.ip;
      document.getElementById("city").value = data.city;
      document.getElementById("location").value = `${data.city}, ${data.region}`;
      document.getElementById("latitude").value = lat;
      document.getElementById("longitude").value = lng;

      // Call HERE API for distance and time
      calculateDistanceTime(lat, lng);
    } catch (error) {
      console.error("IPinfo Error:", error);
    }
  }

  // Calculate distance/time with HERE API
  async function calculateDistanceTime(userLat, userLng) {
    const officeLat = 40.712776;  // Replace with your office's latitude
    const officeLng = -74.005974; // Replace with your office's longitude

    const url = `https://router.hereapi.com/v8/routes?transportMode=car&origin=${officeLat},${officeLng}&destination=${userLat},${userLng}&return=summary&apikey=c2c6d0469901439db4a812a841807002`;

    try {
      const res = await fetch(url);
      const data = await res.json();
      const summary = data.routes[0].sections[0].summary;

      document.getElementById("distance_time").value = Math.round(summary.duration / 60) + " mins";
      document.getElementById("distance_km").value = (summary.length / 1000).toFixed(2);
    } catch (error) {
      console.error("HERE API Error:", error);
    }
  }

  // Call IP info fetch on load
  window.onload = fetchLocationByIP;

  // AJAX form submission
  document.getElementById("meetingForm").addEventListener("submit", async function (e) {
    e.preventDefault();

    const form = e.target;
    const formData = new FormData(form);
    formData.set("phone", iti.getNumber());

    try {
      const response = await axios.post("/meetings", formData, {
        headers: {
          "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').getAttribute("content"),
          "Content-Type": "multipart/form-data"
        }
      });

      document.getElementById("form-message").innerText = "Meeting successfully booked!";
      form.reset();
    } catch (error) {
      document.getElementById("form-message").innerText = "Failed to submit. Please try again.";
      console.error("Form submit error:", error);
    }
  });
</script>

</body>
</html>
