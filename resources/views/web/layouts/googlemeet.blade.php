<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>Book a Meeting</title>

  <!-- CSS -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css" />
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.8/css/intlTelInput.min.css"/>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css"/>

  <!-- JS -->
  <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.8/js/intlTelInput.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
  @include('web.layouts.googlehead')
</head>
<body style="font-family: sans-serif;">

<div class="container mt-5">
  <h2>Book a Meeting</h2>
  <form id="meetingForm">
    <div class="row">
      <div class="col-md-6">
        <input type="text" id="name" name="name" class="form-control mb-2" placeholder="Name" required>
        <input type="tel" id="phone" name="phone" class="form-control mb-2" placeholder="Phone" required>
        <input type="email" id="email" name="email" class="form-control mb-2" placeholder="Email" required>
        <input type="text" id="location" name="location" class="form-control mb-2" placeholder="Location" required>
        <input type="time" id="meeting_time" name="meeting_time" class="form-control mb-2" required>
        <input type="hidden" id="date" name="date" />
        <div id="calendar" class="mb-2"></div>
        <button type="submit" class="btn btn-primary">Submit</button>
        <div id="form-message" class="mt-2 text-success"></div>
      </div>
    </div>

    <!-- Hidden Fields -->
    <input type="hidden" id="latitude" name="latitude">
    <input type="hidden" id="longitude" name="longitude">
    <input type="hidden" id="city" name="city">
    <input type="hidden" id="ip" name="ip">
    <input type="hidden" id="distance_time" name="distance_time">
    <input type="hidden" id="distance_km" name="distance_km">
  </form>
</div>

<script>
  // Intl Tel Input
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

  // Flatpickr
  flatpickr("#calendar", {
    inline: true,
    minDate: "today",
    onChange: function(selectedDates, dateStr) {
      document.getElementById("date").value = dateStr;
    }
  });

  // Auto Location via IPinfo
  async function fetchIPInfo() {
    try {
      const res = await fetch("https://ipinfo.io/json?token=85d3b65b39e700");
      const data = await res.json();
      const [lat, lng] = data.loc.split(",");

      document.getElementById("ip").value = data.ip;
      document.getElementById("city").value = data.city;
      document.getElementById("location").value = `${data.city}, ${data.region}`;
      document.getElementById("latitude").value = lat;
      document.getElementById("longitude").value = lng;

      calculateDistanceTime(lat, lng);
    } catch (err) {
      console.error("IPinfo fetch error:", err);
    }
  }

  // Calculate Distance & Time with HERE API
  async function calculateDistanceTime(userLat, userLng) {
    const officeLat = 40.712776; // Replace with your office location
    const officeLng = -74.005974;

    const url = `https://router.hereapi.com/v8/routes?transportMode=car&origin=${officeLat},${officeLng}&destination=${userLat},${userLng}&return=summary&apikey=c2c6d0469901439db4a812a841807002`;

    try {
      const res = await fetch(url);
      const data = await res.json();
      const summary = data.routes[0].sections[0].summary;

      document.getElementById("distance_time").value = Math.round(summary.duration / 60) + " mins";
      document.getElementById("distance_km").value = (summary.length / 1000).toFixed(2);
    } catch (err) {
      console.error("HERE API error:", err);
    }
  }

  // Auto-run on load
  window.onload = fetchIPInfo;

  // AJAX Submit
  document.getElementById("meetingForm").addEventListener("submit", async function (e) {
    e.preventDefault();
    const formData = new FormData(this);
    formData.set("phone", iti.getNumber());

    try {
      const res = await axios.post("/meetings", formData, {
        headers: {
          "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').content,
          "Content-Type": "multipart/form-data"
        }
      });
      document.getElementById("form-message").textContent = "Meeting booked successfully!";
      this.reset();
    } catch (err) {
      document.getElementById("form-message").textContent = "Error: Could not submit form.";
      console.error("Form error:", err);
    }
  });
</script>
</body>
</html>
