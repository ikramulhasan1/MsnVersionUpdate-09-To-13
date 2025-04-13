<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Meeting Booking Form</title>
  <meta name="csrf-token" content="{{ csrf_token() }}">

  <!-- Styles -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" />
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css" />
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.8/css/intlTelInput.min.css" />

  <style>
    .autocomplete-box {
      position: absolute;
      background: #fff;
      border: 1px solid #ccc;
      z-index: 1000;
      width: 100%;
      max-height: 200px;
      overflow-y: auto;
    }
    .autocomplete-suggestion {
      padding: 8px;
      cursor: pointer;
    }
    .autocomplete-suggestion:hover {
      background: #eee;
    }
  </style>
</head>
<body class="p-4 bg-light">

  <div class="container">
    <h3 class="mb-4">Book a Meeting</h3>

    <form id="meeting-form" class="p-4 bg-white shadow rounded">
      <div class="mb-3">
        <label for="name" class="form-label">Your Name</label>
        <input type="text" id="name" name="name" class="form-control" required />
      </div>
      <div class="mb-3">
        <label for="phone" class="form-label">Phone Number</label>
        <input type="tel" id="phone" name="phone" class="form-control" required />
      </div>
      <div class="mb-3 position-relative">
        <label for="location" class="form-label">Location</label>
        <input type="text" id="location" name="location" class="form-control" autocomplete="off" required />
        <div id="location-suggestions" class="autocomplete-box"></div>
      </div>
      <div class="mb-3">
        <label for="email" class="form-label">Email</label>
        <input type="email" id="email" name="email" class="form-control" required />
      </div>
      <div class="mb-3">
        <label for="meeting_time" class="form-label">Meeting Time</label>
        <input type="time" id="meeting_time" name="meeting_time" class="form-control" required />
      </div>
      <div class="mb-3">
        <label for="date" class="form-label">Date</label>
        <input type="text" id="date" name="date" class="form-control flatpickr" required />
      </div>

      <!-- Hidden Fields -->
      <input type="hidden" id="latitude" name="latitude" />
      <input type="hidden" id="longitude" name="longitude" />
      <input type="hidden" id="city" name="city" />
      <input type="hidden" id="ip" name="ip" />

      <button type="submit" class="btn btn-primary">Submit</button>
    </form>

    <div id="form-message" class="mt-3 fw-bold"></div>
  </div>

  <!-- Scripts -->
  <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.8/js/intlTelInput.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>

  <script>
    const phoneInput = document.querySelector("#phone");
    const iti = window.intlTelInput(phoneInput, {
      nationalMode: false,
      initialCountry: "auto",
      geoIpLookup: callback => {
        fetch("https://ipinfo.io/json?token=85d3b65b39e700")
          .then(res => res.json())
          .then(data => callback(data.country))
          .catch(() => callback("us"));
      },
      utilsScript: "https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.8/js/utils.js"
    });

    flatpickr("#date", { minDate: "today" });

    async function autofillFromIP() {
      try {
        const res = await fetch("https://ipinfo.io/json?token=85d3b65b39e700");
        const data = await res.json();
        const [lat, lon] = data.loc.split(",");

        document.getElementById("location").value = `${data.city}, ${data.region}`;
        document.getElementById("city").value = data.city;
        document.getElementById("latitude").value = lat;
        document.getElementById("longitude").value = lon;
        document.getElementById("ip").value = data.ip;
      } catch (err) {
        console.error("IP autofill failed:", err);
      }
    }

    window.onload = autofillFromIP;

    const locationInput = document.getElementById("location");
    const suggestionBox = document.getElementById("location-suggestions");

    locationInput.addEventListener("input", async () => {
      const query = locationInput.value.trim();
      if (query.length < 3) return suggestionBox.innerHTML = "";

      const apiKey = "YOUR_GEOAPIFY_API_KEY";
      const url = `https://api.geoapify.com/v1/geocode/autocomplete?text=${encodeURIComponent(query)}&apiKey=${apiKey}`;

      const res = await fetch(url);
      const data = await res.json();

      suggestionBox.innerHTML = "";
      data.features.forEach(item => {
        const div = document.createElement("div");
        div.classList.add("autocomplete-suggestion");
        div.textContent = item.properties.formatted;
        div.onclick = () => {
          locationInput.value = item.properties.formatted;
          document.getElementById("latitude").value = item.properties.lat;
          document.getElementById("longitude").value = item.properties.lon;
          document.getElementById("city").value = item.properties.city || "";
          suggestionBox.innerHTML = "";
        };
        suggestionBox.appendChild(div);
      });
    });

    document.getElementById("meeting-form").addEventListener("submit", async function (e) {
      e.preventDefault();

      const formData = new FormData(this);
      formData.set("phone", iti.getNumber());

      try {
        const response = await axios.post("/meetings", formData, {
          headers: {
            "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').content
          }
        });
        document.getElementById("form-message").textContent = response.data.message || "Meeting booked successfully!";
        this.reset();
        suggestionBox.innerHTML = "";
      } catch (error) {
        document.getElementById("form-message").textContent = "Error booking meeting. Please try again.";
      }
    });
  </script>
</body>
</html>
