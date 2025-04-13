
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>Book a Meeting</title>

  @include('web.layouts.googlehead')



<button id="open-modal" class="button google-meet-button" style="background-color: #48bb78; color: white; padding: 12px 24px; cursor: pointer; display: flex; align-items: center;">
  <div class="logo-container">
    <img id="google-meet-img" src="https://www.gstatic.com/meet/google_meet_horizontal_wordmark_2020q4_2x_icon_124_40_292e71bcb52a56e2a9005164118f183b.png" alt="Google Meet Logo" class="meeting-logo active" />

    <img id="zoom-img" src="https://upload.wikimedia.org/wikipedia/commons/7/7b/Zoom_Communications_Logo.svg" alt="Zoom Logo" class="meeting-logo" />
  </div>
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
      <div id="form-message"></div>

      <form id="modal-form">
        @csrf
        <div class="row flex justify-content-between align-items-center ">
          <div class="col-6">

            <div class="floating-label-group">
              <input type="text" id="name" name="name" required placeholder=" " />
              <label for="name">Name</label>
            </div>

            <div class="floating-label-group">
              <input style="width: 237px !important;" type="tel" id="phone" name="phone" required placeholder="+1 (555) 123-4567" />
              <label for="phone">Phone</label>
            </div>

            <div class="floating-label-group">
              <input type="email" id="email" name="email" required placeholder=" " />
              <label for="email">Email</label>
            </div>

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
        console.log(data);
        
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
    console.log(item);
    
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
        console.log(data);
        
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
  locationInput.addEventListener('input', function () {
    const query = this.value.trim();
    if (query.length >= 3) fetchLocationSuggestions(query);
    else suggestionsBox.style.display = 'none';
  });

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
        body: formData,
        
    })
    .then(res => res.json())
    .then(data => {
      console.log(data);
      
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
    .catch(($e) => {
        showMessage($e, 'error');
    });
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

