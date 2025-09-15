
<script type="module" src="https://cdn.jsdelivr.net/npm/flatpickr"></script>

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
        body: formData,
        
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
    .catch(($e) => {
        showMessage($e, 'error');
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
