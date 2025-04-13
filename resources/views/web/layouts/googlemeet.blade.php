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
      width: 80%;
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
  </style>
</head>
<body>

<button id="open-modal" class="btn btn-primary">Book a Meeting</button>

<!-- Modal -->
<div id="modal-1" class="modal__overlay">
  <div class="modal__container">
    <header class="mb-3">
      <button class="modal__close">×</button>
      <h2 style="text-align: center">Book a Meeting</h2>
    </header>
    <div class="modal__content">
      <div id="form-message" class="mb-3 text-success fw-bold"></div>
      <form id="modal-form">
        <div class="row">
          <div class="col-md-6">
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
          <div class="col-md-6">
            <div id="calendar"></div>
          </div>
        </div>
        <button type="submit" class="btn btn-success">Save</button>
        <button type="button" id="cancel-button" class="btn btn-secondary">Cancel</button>
      </form>
    </div>
  </div>
</div>

<!-- Scripts -->
<script src="https://cdn.jsdelivr.net/npm/vanillajs-modal@1.1.2/dist/vanilla.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.8/js/intlTelInput.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>

<script>
  const modal = document.getElementById("modal-1");
  const openModal = document.getElementById("open-modal");
  const closeModal = document.querySelector(".modal__close");
  const cancelButton = document.getElementById("cancel-button");
  const formMessage = document.getElementById("form-message");

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

      const [latitude, longitude] = data.loc.split(",");
      document.getElementById("ip").value = data.ip;
      document.getElementById("city").value = data.city;
      document.getElementById("location").value = `${data.city}, ${data.region}`;
      document.getElementById("latitude").value = latitude;
      document.getElementById("longitude").value = longitude;

      // Instead of HERE API, calculate a placeholder for distance_time and distance_km
      document.getElementById("distance_time").value = "15 mins"; // Placeholder time
      document.getElementById("distance_km").value = "5.3"; // Placeholder distance
    } catch (err) {
      console.error("IPinfo error", err);
    }
  }

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
      formMessage.classList.add("text-success");

      // Reset form but keep the modal open
      form.reset();
      formMessage.classList.remove("text-success");

      // Optionally, add a delay before resetting the message
      setTimeout(() => {
        formMessage.textContent = "";
      }, 3000);
      
    } catch (err) {
      formMessage.textContent = err.response?.data?.message || "Error saving meeting.";
      formMessage.classList.add("text-danger");
    }
  });
</script>

</body>
</html>
