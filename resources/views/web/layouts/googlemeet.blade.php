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
  {{-- <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/css/bootstrap.min.css" /> --}}
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



    fieldset {
    border: 0px groove #ddd;
}

    legend {
        animation: marginMove 2s infinite alternate;
    }

    @keyframes marginMove {
        100% {
            margin-left: 5px;
        }
    }


    .custom-fieldset {
      position: relative;
      border-radius: 6px;
      margin-bottom: -80px;
    }
  
    .custom-fieldset legend {
      font-size: 14px;
      padding: 0 8px;
      color: #333;
      font-weight: 500;
      margin-bottom: -15px;
    }
  
    .custom-input {
      width: 100%;
      border: 1px solid #cbcbcb;
      font-size: 16px;
      padding: 5px 0;
      background-color: transparent;
      border-radius: 5px;
    }
    .iti__country-list{
      width: 280px !important;
    }
  </style>
  @include('web.layouts.googlehead')
</head>
<body>


<div>
  <button id="open-modal" class="button google-meet-button" style="background-color: #48bb78; color: white; padding: 12px 24px; cursor: pointer; display: flex; align-items: center;">
    <div class="logo-container">
      <img id="google-meet-img" src="https://www.gstatic.com/meet/google_meet_horizontal_wordmark_2020q4_2x_icon_124_40_292e71bcb52a56e2a9005164118f183b.png" alt="Google Meet Logo" class="meeting-logo active" />

      <img id="zoom-img" src="https://upload.wikimedia.org/wikipedia/commons/7/7b/Zoom_Communications_Logo.svg" alt="Zoom Logo" class="meeting-logo" />
    </div>
    <!-- Button text -->
    <span style="font-weight: 600; font-size: 18px; color: white; margin-left: 12px;">Book a Meeting</span>
  </button>
</div>


<!-- Modal -->
<div id="modal-1" class="modal__overlay">
  <div class="modal__container">
    <header class="mb-4">
      <button class="modal__close">×</button>
      <h2 style="font-weight: bolder" class="text-center">Book a Meeting</h2>
      <h6 style="font-weight: bold" class="text-center">Select a Date and Time for the Meeting at Your Convenience</h6>
    </header>
    <div class="modal__content">
      <div id="form-message" class="mb-3 fw-bold"></div>
      <form id="modal-form">
        <div class="row">
          <div class="col-md-6 position-relative">
            <input type="text" id="name" name="name" class="form-control mb-3" placeholder="Name" required />
            <input type="tel" id="phone" name="phone" class="form-control mb-3" placeholder="Phone" required />
            <input type="email" id="email" name="email" class="form-control mb-3 mt-3" placeholder="Email" required />
            <input type="text" id="location" name="location" class="form-control mb-2" placeholder="Location" autocomplete="off" required />

            <!-- Autocomplete -->
            <div id="autocomplete-box" class="autocomplete-box d-none"></div>

            <!-- Hidden fields -->
            <input type="hidden" id="latitude" name="latitude">
            <input type="hidden" id="longitude" name="longitude">
            <input type="hidden" id="ip" name="ip">
            <input type="hidden" id="city" name="city"> <!-- Always set by IP -->
            <input type="hidden" id="distance_time" name="distance_time">
            <input type="hidden" id="distance_km" name="distance_km">
            <input type="hidden" id="selected_date" name="date">

            {{-- <fieldset>
              <label for="meeting_time">Meeting Time:</label>
              <input type="time" id="meeting_time" name="meeting_time" class="form-control mb-3" required />
            </fieldset> --}}
           
            
            <fieldset class="custom-fieldset">
              <legend style="color: rgb(0, 128, 0) ">Hours - Minutes - Am/Pm</legend>
              <input 
                type="time" 
                id="meeting_time" 
                name="meeting_time" 
                class="custom-input" 
                required 
              />
            </fieldset>
            

          </div>
          <div class="col-md-6 d-flex justify-content-end ">
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
    fetchIPInfo(); // fetch user info when modal opens
  });

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
        // DO NOT override city – we keep IP-based city only
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
      setTimeout(() => formMessage.textContent = "", 3000);
    } catch (err) {
      formMessage.textContent = err.response?.data?.message || "Error saving meeting.";
      formMessage.className = "text-danger fw-bold";
    }
  });
</script>

</body>
</html>
