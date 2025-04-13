<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>Book a Meeting</title>

  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
  <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@500;600;700&display=swap" rel="stylesheet">
  <script src="https://cdn.jsdelivr.net/npm/vanillajs-modal@1.1.2/dist/vanilla.min.js"></script>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.8/css/intlTelInput.css">
  <script src="https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.8/js/intlTelInput.min.js"></script>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-SgOJa3DmI69IUzQ2PVdRZhwQ+dy64/BUtbMJw1MZ8t5HZApcHrRKUc4W0kG879m7" crossorigin="anonymous">

  <style>
    /* Modal and Form Styling (same as before) */
    .modal__overlay { background-color: rgba(0, 0, 0, 0.5); position: fixed; top: 0; left: 0; right: 0; bottom: 0; display: flex; justify-content: center; align-items: center; z-index: 1000; }
    .modal__container { background: white; padding: 20px; border-radius: 8px; width: 850px; position: relative; }
    button.modal__close { position: absolute; top: 10px; right: 10px; background: none; border: none; font-size: 20px; cursor: pointer; }
    button[type="submit"], button#cancel-button { padding: 10px 20px; border: none; border-radius: 4px; cursor: pointer; }
    button[type="submit"] { background-color: #007bff; color: white; }
    button[type="submit"]:hover { background-color: #0056b3; }
    button#cancel-button { background-color: #dc3545; color: white; margin-left: 10px; }
    button#cancel-button:hover { background-color: #c82333; }

    .suggestions { border: 1px solid #ccc; max-height: 200px; overflow-y: auto; margin-top: 10px; }
    .suggestions div { padding: 10px; cursor: pointer; }
    .suggestions div:hover { background-color: #f0f0f0; }

    .calendar-card { background: #ffffff; border-radius: 20px; padding: 0px; width: 460px; box-shadow: 0 10px 40px rgba(0, 0, 0, 0.08); text-align: center; transition: 0.3s; animation: fadeIn 0.7s ease; }
    .calendar-card:hover { transform: translateY(-4px); box-shadow: 0 15px 45px rgba(0, 0, 0, 0.1); }
    @keyframes fadeIn { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }

    .flatpickr-calendar.inline { display: block; position: relative; background: transparent; border: none; }
    .flatpickr-months { background: rgb(239, 239, 239); border-radius: 5px 5px 0 0; color: white; }
    .flatpickr-weekdays { background: #eef2ff; }
    .flatpickr-day { border-radius: 10px; font-weight: 500; color: #1f2937; }
    .flatpickr-day.selected { background: #4f46e5 !important; color: white !important; }

    /* ✅ Message box styling */
    #form-message {
      display: none;
      margin-bottom: 15px;
      padding: 10px;
      border-radius: 6px;
      font-weight: bold;
      text-align: center;
    }
    .success-message { background-color: #d1fae5; color: #065f46; }
    .error-message { background-color: #fee2e2; color: #991b1b; }

.floating-label-group {
  position: relative;
  margin-bottom: 20px;
}

.floating-label-group input,
.floating-label-group input[type="time"],
.floating-label-group input[type="tel"],
.floating-label-group input[type="email"] {
  width: 100%;
  padding: 12px 12px 12px 12px;
  font-size: 16px;
  border: 1px solid #ccc;
  border-radius: 8px;
  outline: none;
  transition: 0.3s ease;
}

.floating-label-group input:focus {
  border-color: #4f46e5;
  box-shadow: 0 0 0 3px rgba(79, 70, 229, 0.1);
}

.floating-label-group label {
  position: absolute;
  top: 12px;
  left: 14px;
  color: #999;
  font-size: 16px;
  pointer-events: none;
  transition: 0.2s ease all;
  background: white;
  padding: 0 4px;
}

.floating-label-group input:focus + label,
.floating-label-group input:not(:placeholder-shown) + label {
  top: -10px;
  left: 10px;
  font-size: 12px;
  color: #4f46e5;
}
/* Fix the wrapper that intl-tel-input creates */
.iti {
  width: 100% !important;
  display: block !important;
}

/* Ensure the input itself is full width */
.iti input {
  width: 100% !important;
  box-sizing: border-box;
}



.google-meet-button {
      display: flex;
      align-items: center;
      justify-content: center;
      background: linear-gradient(90deg, #48bb78, #4299e1);
      color: white;
      padding: 12px 30px;
      border-radius: 10px !important;
      box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
      cursor: pointer;
      transition: transform 0.3s, background-color 0.3s;
      border: none;
      font-weight: 600;
      /* margin-top: 30px; */
    }

    .google-meet-button:hover {
      transform: scale(1.05);
      background-color: #3182ce;
    }

    .google-meet-button img {
      width: 140px;
      height: auto;
      margin-right: 15px;
    }
    .logo-container {
      position: relative;
      width: 124px;
      height: 40px;
      display: inline-block;
      overflow: hidden;
    }
  </style>
</head>
<body>

{{-- <button id="open-modal" style="padding: 12px 24px; background-color: #48bb78; color: white; border: none; border-radius: 8px;">Book a Meeting</button> --}}

<button id="open-modal" class="button google-meet-button" style="background-color: #48bb78; color: white; padding: 12px 24px; cursor: pointer; display: flex; align-items: center;">
  <div class="logo-container">
    <img id="google-meet-img" src="https://www.gstatic.com/meet/google_meet_horizontal_wordmark_2020q4_2x_icon_124_40_292e71bcb52a56e2a9005164118f183b.png" alt="Google Meet Logo" class="meeting-logo active" />

    <img id="zoom-img" src="https://upload.wikimedia.org/wikipedia/commons/7/7b/Zoom_Communications_Logo.svg" alt="Zoom Logo" class="meeting-logo" />
  </div>
  <!-- Button text -->
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
      <!-- ✅ Message Display -->
      <div id="form-message"></div>

      <form id="modal-form">
        {{-- <div style="display: flex; justify-content: space-between; align-items: center;"> --}}
        <div class="row flex justify-content-between align-items-center ">
          <div class="col-6">

            {{-- <div style="display:flex; justify-content: space-between; align-items: center;" class="">
              <!-- Name Field -->
              <label style="width: 40% !important; font-size: 18px; font-weight: 600;" for="name">Name</label>
              <input style="width: 60% !important; font-size: 16px; padding-left: 5px;  " type="text" id="name" name="name" required>
            </div><br> --}}
            <!-- ✅ Modern Field: Name -->
            <div class="floating-label-group">
              <input type="text" id="name" name="name" required placeholder=" " />
              <label for="name">Name</label>
            </div>
            
            <!-- Phone Field -->
            {{-- <div style="display:flex !important; justify-content: space-between !important; align-items: center !important;" class="">
              <label style="width: 40% !important; font-size: 18px; font-weight: 600;" for="phone">Phone</label>
              <input style="width: 237px !important; font-size: 16px; padding-left: 5px; " type="tel" id="phone" name="phone" required>
            </div><br> --}}
            <!-- ✅ Modern Field: Phone -->
            <div class="floating-label-group">
              <input style="width: 237px !important;" type="tel" id="phone" name="phone" required placeholder="+1 (555) 123-4567" />
              <label for="phone">Phone</label>
            </div>

            <!-- Email Field -->
            {{-- <div style="display:flex; justify-content: space-between; align-items: center;" class="">
              <label style="width: 40% !important; font-size: 18px; font-weight: 600;" for="email">Email</label>
              <input style="width: 60% !important; font-size: 16px; padding-left: 5px;  " type="email" id="email" name="email" required>
            </div><br> --}}
            <!-- ✅ Modern Field: Email -->
            <div class="floating-label-group">
              <input type="email" id="email" name="email" required placeholder=" " />
              <label for="email">Email</label>
            </div>

            <!-- Location Field -->
            {{-- <div style="display:flex; justify-content: space-between; align-items: center;" class="">
              <label style="width: 40% !important; font-size: 18px; font-weight: 600;" for="location">Location</label>
              <input style="width: 60% !important; font-size: 16px; padding-left: 5px;  " type="text" id="location" name="location" required autocomplete="off">
            </div><br> --}}
            <!-- ✅ Modern Field: Location -->
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

            <!-- Meeting Time Field -->
            {{-- <div style="display:flex; justify-content: space-between; align-items: center;" class="">
              <label style="width: 40% !important; font-size: 18px; font-weight: 600;" for="meeting_time">Meeting Time</label>
              <input style="width: 60% !important; font-size: 16px; padding-left: 5px;  " type="time" id="meeting_time" name="meeting_time" required>
            </div><br> --}}
            <!-- ✅ Modern Field: Meeting Time -->
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
  const OPENCAGE_API_KEY = "c2c6d0469901439db4a812a841807002";
  const messageBox = document.getElementById('form-message');

  let phoneInput = document.getElementById('phone');
  let iti = intlTelInput(phoneInput, {
    initialCountry: "us", // Set the default country code
    separateDialCode: true,
    utilsScript: "https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.8/js/utils.js" // Import utils.js for formatting
  });

  function showMessage(msg, type = 'success') {
    messageBox.textContent = msg;
    messageBox.className = type === 'success' ? 'success-message' : 'error-message';
    messageBox.style.display = 'block';
    setTimeout(() => {
      messageBox.style.display = 'none';
    }, 5000);
  }

  openModalButton.addEventListener('click', () => {
    modal.style.display = 'flex';
    detectUserIPLocation();
  });
  closeModalButton.addEventListener('click', () => modal.style.display = 'none');
  cancelModalButton.addEventListener('click', () => modal.style.display = 'none');

  function fetchLocationSuggestions(query) {
    fetch(`https://api.opencagedata.com/geocode/v1/json?q=${encodeURIComponent(query)}&key=${OPENCAGE_API_KEY}`)
      .then(res => res.json())
      .then(data => {
        suggestionsBox.innerHTML = '';
        if (data.results) {
          data.results.forEach(item => {
            const div = document.createElement('div');
            div.textContent = item.formatted;
            div.onclick = () => selectLocation(item);
            suggestionsBox.appendChild(div);
          });
          suggestionsBox.style.display = 'block';
        }
      });
  }

  function selectLocation(item) {
    const { lat, lng } = item.geometry;
    locationInput.value = item.formatted;
    document.getElementById('latitude').value = lat;
    document.getElementById('longitude').value = lng;
    suggestionsBox.style.display = 'none';

    const ipLat = parseFloat(locationInput.dataset.ipLat);
    const ipLng = parseFloat(locationInput.dataset.ipLng);
    if (!isNaN(ipLat) && !isNaN(ipLng)) {
      const dist = calculateDistanceKm(ipLat, ipLng, lat, lng);
      const time = estimateTravelTimeKm(dist);
      document.getElementById('distance_km').value = dist;
      document.getElementById('distance_time').value = time;
    }
  }

  function detectUserIPLocation() {
    fetch('https://ipinfo.io/json?token=85d3b65b39e700')
      .then(res => res.json())
      .then(data => {
        const [ipLat, ipLng] = data.loc.split(',');
        document.getElementById('ip').value = data.ip;
        document.getElementById('location').value = `${data.city}, ${data.region}, ${data.country}`;
        document.getElementById('latitude').value = ipLat;
        document.getElementById('longitude').value = ipLng;
        document.getElementById('city').value = data.city;
        locationInput.dataset.ipLat = ipLat;
        locationInput.dataset.ipLng = ipLng;
      });
  }

  flatpickr("#calendar", {
    inline: true,
    minDate: "today",
    dateFormat: "Y-m-d",
    onChange: (selectedDates, dateStr) => {
      document.getElementById('selected_date').value = dateStr;
    }
  });

  function calculateDistanceKm(lat1, lon1, lat2, lon2) {
    const R = 6371;
    const dLat = (lat2 - lat1) * Math.PI / 180;
    const dLon = (lon2 - lon1) * Math.PI / 180;
    const a = Math.sin(dLat / 2) * Math.sin(dLat / 2) +
              Math.cos(lat1 * Math.PI / 180) * Math.cos(lat2 * Math.PI / 180) *
              Math.sin(dLon / 2) * Math.sin(dLon / 2);
    const c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1 - a));
    return (R * c).toFixed(2);
  }

  function estimateTravelTimeKm(distanceKm) {
    const avgSpeed = 40;
    return Math.ceil((distanceKm / avgSpeed) * 60);
  }

  document.getElementById('modal-form').addEventListener('submit', function (event) {
    event.preventDefault();

    // Get phone number with country code
    const phoneNumber = iti.getNumber();

    const formData = new FormData(this);
    formData.set("phone", phoneNumber); // Override phone field with the full phone number

    fetch("{{ route('admin.meetings.store') }}", {
        method: "POST",
        headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content },
        body: formData
    })
    .then(res => res.json())
    .then(data => {
        if (data.message === 'Already have a meeting booked at this date and time.') {
            // Show error message but don't close modal
            showMessage(data.message, 'error');
        } else {
            // Show success message and close the modal after 1.5 seconds
            showMessage(data.message || 'Meeting saved successfully!', 'success');
            this.reset();
            setTimeout(() => modal.style.display = 'none', 1500);
        }
    })
    .catch(() => {
        showMessage('Please select a calendar date and other all fields.', 'error');
    });
});

  locationInput.addEventListener('input', function () {
    const query = this.value.trim();
    if (query.length >= 3) fetchLocationSuggestions(query);
    else suggestionsBox.style.display = 'none';
  });



  document.addEventListener('DOMContentLoaded', function () {
  const countryListBox = document.getElementById('iti-0__country-listbox');
  if (countryListBox) {
    countryListBox.style.width = '300px';
  }



  const phoneInput = document.querySelector("#phone");

window.addEventListener("load", () => {
  if (phoneInput) {
    setTimeout(() => {
      const itiWrapper = phoneInput.closest(".iti");
      if (itiWrapper) {
        itiWrapper.style.width = "100%";
        phoneInput.style.width = "100%";
      }
    }, 200); // Delay to allow plugin to fully initialize
  }
});

});

  

const googleMeetImg = document.getElementById('google-meet-img');
    const zoomImg = document.getElementById('zoom-img');

    // Start with Google Meet active
    googleMeetImg.classList.add('active');

    setInterval(() => {
    googleMeetImg.classList.toggle('active');
    zoomImg.classList.toggle('active');
    }, 4000);
</script>
</body>
</html>
