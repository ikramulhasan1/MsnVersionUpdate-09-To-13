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
  <style>
    body {
      font-family: 'Manrope', sans-serif;
    }
    .modal__overlay {
      position: fixed;
      top: 0; left: 0;
      width: 100%; height: 100%;
      background: rgba(0, 0, 0, 0.6);
      display: none;
      justify-content: center;
      align-items: center;
      z-index: 9999;
    }
    .modal__container {
      background: #fff;
      padding: 20px;
      width: 90%;
      max-width: 800px;
      border-radius: 10px;
      position: relative;
    }
    .modal__close {
      position: absolute;
      right: 15px;
      top: 10px;
      font-size: 20px;
      border: none;
      background: transparent;
    }
    .autocomplete-box {
      position: absolute;
      background-color: white;
      border: 1px solid #ccc;
      max-height: 200px;
      overflow-y: auto;
      width: 100%;
      z-index: 1000;
    }
    .autocomplete-suggestion {
      padding: 8px;
      cursor: pointer;
    }
    .autocomplete-suggestion:hover {
      background-color: #f0f0f0;
    }
    #city-display {
      font-weight: bold;
      margin-top: 4px;
    }
  </style>
</head>
<body>

<div class="container my-5 text-center">
  <button id="open-modal" class="btn btn-primary">Book a Meeting</button>
</div>

<!-- Modal -->
<div id="modal-1" class="modal__overlay">
  <div class="modal__container">
    <header class="mb-3">
      <button class="modal__close">×</button>
      <h2 class="text-center">Book a Meeting</h2>
    </header>
    <div class="modal__content">
      <div id="form-message" class="mb-3 fw-bold"></div>
      <form id="modal-form">
        <div class="row">
          <div class="col-md-6 position-relative">
            <input type="text" id="name" name="name" class="form-control mb-2" placeholder="Name" required />
            <input type="tel" id="phone" name="phone" class="form-control mb-2" placeholder="Phone" required />
            <input type="email" id="email" name="email" class="form-control mb-2" placeholder="Email" required />
            <input type="text" id="location" name="location" class="form-control mb-2" placeholder="Location" autocomplete="off" required />

            <!-- Autocomplete Box -->
            <div id="autocomplete-box" class="autocomplete-box d-none"></div>

            <!-- City Display -->
            <div id="city-display" class="text-primary"></div>

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
          <div class="col-md-6">
            <div id="calendar"></div>
          </div>
        </div>
        <div class="mt-3 text-end">
          <button type="submit" class="btn btn-success">Save</button>
          <button type="button" id="cancel-button" class="btn btn-secondary">Cancel</button>
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
  const cityField = document.getElementById("city");
  const cityDisplay = document.getElementById("city-display");

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

  openModal.addEventListener("click", () => {
    modal.style.display = "flex";
    fetchIPInfo();
  });
  closeModal.addEventListener("click", () => modal.style.display = "none");
  cancelButton.addEventListener("click", () => modal.style.display = "none");

  async function fetchIPInfo() {
    try {
      const res = await fetch("https://ipinfo.io/json?token=85d3b65b39e700");
      const data = await res.json();
      const [lat, lon] = data.loc.split(",");
      document.getElementById("ip").value = data.ip;
      cityField.value = data.city;
      cityDisplay.textContent = `City: ${data.city}`;
      locationInput.value = `${data.city}, ${data.region}`;
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
    if (value.length < 3) return suggestionBox.classList.add("d-none");

    const url = `https://api.geoapify.com/v1/geocode/autocomplete?text=${encodeURIComponent(value)}&apiKey=437507f257da48b28e1d22d7f9736e62&limit=5`;
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
        cityField.value = place.properties.city || "";
        cityDisplay.textContent = `City: ${place.properties.city || ""}`;
        suggestionBox.classList.add("d-none");
      };
      suggestionBox.appendChild(div);
    });

    suggestionBox.classList.remove("d-none");
  });

  document.getElementById("modal-form").addEventListener("submit", async function (e) {
    e.preventDefault();
    const form = e.target;
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
      cityDisplay.textContent = "";
      setTimeout(() => formMessage.textContent = "", 3000);
    } catch (err) {
      formMessage.textContent = err.response?.data?.message || "Error saving meeting.";
      formMessage.className = "text-danger fw-bold";
    }
  });
</script>

</body>
</html>
