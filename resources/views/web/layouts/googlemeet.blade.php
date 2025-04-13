<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Meeting Booking</title>
  <meta name="csrf-token" content="{{ csrf_token() }}">

  <!-- Styles -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.8/css/intlTelInput.css">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    body {
      font-family: 'Manrope', sans-serif;
    }
    .modal__overlay {
      position: fixed; top: 0; left: 0;
      width: 100%; height: 100%;
      background: rgba(0, 0, 0, 0.6);
      display: none;
      justify-content: center;
      align-items: center;
      z-index: 9999;
    }
    .modal__container {
      background: white;
      padding: 20px;
      border-radius: 12px;
      max-width: 800px;
      width: 90%;
    }
    .modal__close {
      position: absolute;
      top: 10px; right: 15px;
      background: none;
      border: none;
      font-size: 24px;
    }
    #suggestions {
      position: absolute;
      background: white;
      border: 1px solid #ccc;
      width: 100%;
      z-index: 1000;
    }
    .autocomplete-suggestion {
      padding: 8px 12px;
      cursor: pointer;
    }
    .autocomplete-suggestion:hover {
      background-color: #f0f0f0;
    }
  </style>
</head>
<body>

<button id="open-modal" class="btn btn-primary m-4">Book a Meeting</button>

<!-- Modal -->
<div id="modal-1" class="modal__overlay">
  <div class="modal__container position-relative">
    <button class="modal__close" id="close-modal">&times;</button>
    <h2 class="text-center mb-3">Book a Meeting</h2>

    <div id="form-message" class="mb-3 text-success fw-bold"></div>
    <form id="modal-form">
      <div class="row">
        <div class="col-md-6 position-relative">
          <input type="text" id="name" name="name" class="form-control mb-2" placeholder="Name" required>
          <input type="tel" id="phone" name="phone" class="form-control mb-2" placeholder="Phone" required>
          <input type="email" id="email" name="email" class="form-control mb-2" placeholder="Email" required>

          <div class="position-relative mb-2">
            <input type="text" id="location" name="location" class="form-control" placeholder="Location" autocomplete="off" required>
            <div id="suggestions"></div>
          </div>

          <!-- Hidden fields -->
          <input type="hidden" id="latitude" name="latitude">
          <input type="hidden" id="longitude" name="longitude">
          <input type="hidden" id="city" name="city">
          <input type="hidden" id="ip" name="ip">
          <input type="hidden" id="distance_time" name="distance_time" value="15">
          <input type="hidden" id="distance_km" name="distance_km" value="5.3">
          <input type="hidden" id="selected_date" name="date">

          <input type="time" id="meeting_time" name="meeting_time" class="form-control mb-2" required>
        </div>

        <div class="col-md-6">
          <div id="calendar"></div>
        </div>
      </div>

      <button type="submit" class="btn btn-success mt-2">Save</button>
      <button type="button" class="btn btn-secondary mt-2" id="cancel-button">Cancel</button>
    </form>
  </div>
</div>

<!-- Scripts -->
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.8/js/intlTelInput.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>

<script>
  const modal = document.getElementById("modal-1");
  const openBtn = document.getElementById("open-modal");
  const closeBtn = document.getElementById("close-modal");
  const cancelBtn = document.getElementById("cancel-button");
  const formMsg = document.getElementById("form-message");

  openBtn.onclick = () => {
    modal.style.display = "flex";
    fetchIPInfo();
  };
  closeBtn.onclick = cancelBtn.onclick = () => modal.style.display = "none";

  // Phone input
  const phoneInput = document.querySelector("#phone");
  const iti = intlTelInput(phoneInput, {
    nationalMode: false,
    initialCountry: "auto",
    geoIpLookup: async (callback) => {
      try {
        const res = await fetch("https://ipinfo.io/json?token=85d3b65b39e700");
        const data = await res.json();
        callback(data.country || "us");
      } catch {
        callback("us");
      }
    },
    utilsScript: "https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.8/js/utils.js"
  });

  // Calendar
  flatpickr("#calendar", {
    inline: true,
    minDate: "today",
    onChange: (selectedDates, dateStr) => {
      document.getElementById("selected_date").value = dateStr;
    }
  });

  // Auto-detect IP & set city, location, lat, lon
  async function fetchIPInfo() {
    try {
      const res = await fetch("https://ipinfo.io/json?token=85d3b65b39e700");
      const data = await res.json();
      const [lat, lon] = data.loc.split(",");

      document.getElementById("ip").value = data.ip;
      document.getElementById("latitude").value = lat;
      document.getElementById("longitude").value = lon;
      document.getElementById("city").value = data.city;
      document.getElementById("location").value = `${data.city}, ${data.region}`;
    } catch (err) {
      console.error("Failed to get IP info", err);
    }
  }

  // Autocomplete with Nominatim
  const locInput = document.getElementById("location");
  const suggestions = document.getElementById("suggestions");
  let debounce;

  locInput.addEventListener("input", () => {
    clearTimeout(debounce);
    const query = locInput.value.trim();
    if (!query) return suggestions.innerHTML = "";

    debounce = setTimeout(async () => {
      const res = await fetch(`https://nominatim.openstreetmap.org/search?format=json&q=${encodeURIComponent(query)}&addressdetails=1&limit=5`);
      const places = await res.json();

      suggestions.innerHTML = "";
      places.forEach(place => {
        const div = document.createElement("div");
        div.classList.add("autocomplete-suggestion");
        div.textContent = place.display_name;
        div.onclick = () => {
          locInput.value = place.display_name;
          document.getElementById("latitude").value = place.lat;
          document.getElementById("longitude").value = place.lon;
          document.getElementById("city").value = place.address.city || place.address.town || place.address.village || "";
          suggestions.innerHTML = "";
        };
        suggestions.appendChild(div);
      });
    }, 400);
  });

  // Form submit
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

      formMsg.textContent = "Meeting booked successfully!";
      formMsg.className = "text-success fw-bold mb-3";
      form.reset();
    } catch (err) {
      formMsg.textContent = "Error saving meeting.";
      formMsg.className = "text-danger fw-bold mb-3";
    }
  });
</script>

</body>
</html>
