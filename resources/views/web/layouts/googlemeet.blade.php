<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Modal with Google Maps Autocomplete</title>

  <!-- VanillaModal -->
  <script src="https://cdn.jsdelivr.net/npm/vanillajs-modal@1.1.2/dist/vanilla.min.js"></script>

  <!-- Custom Styles -->
  <style>
    .modal__overlay {
      background-color: rgba(0, 0, 0, 0.5);
      position: fixed;
      top: 0; left: 0; right: 0; bottom: 0;
      display: flex;
      justify-content: center;
      align-items: center;
      z-index: 1000;
    }

    .modal__container {
      background: white;
      padding: 20px;
      border-radius: 8px;
      width: 400px;
      position: relative;
    }

    button.modal__close {
      position: absolute;
      top: 10px;
      right: 10px;
      background: none;
      border: none;
      font-size: 20px;
      cursor: pointer;
    }

    button[type="submit"],
    button#cancel-button {
      padding: 10px 20px;
      border: none;
      border-radius: 4px;
      cursor: pointer;
    }

    button[type="submit"] {
      background-color: #007bff;
      color: white;
    }

    button[type="submit"]:hover {
      background-color: #0056b3;
    }

    button#cancel-button {
      background-color: #dc3545;
      color: white;
      margin-left: 10px;
    }

    button#cancel-button:hover {
      background-color: #c82333;
    }
  </style>
</head>
<body>

<!-- Open Modal Button -->
<div style="display: flex; justify-content: space-between; align-items: center;">
  <button id="open-modal" style="background-color: #48bb78; color: white; padding: 12px 24px; border-radius: 30px; cursor: pointer;">
    <img src="https://www.gstatic.com/meet/google_meet_horizontal_wordmark_2020q4_2x_icon_124_40_292e71bcb52a56e2a9005164118f183b.png" alt="Google Meet" style="width: 100px; height: auto; margin-right: 10px;">
    <span style="font-weight: 600;">Start Google Meet</span>
  </button>
</div>

<!-- Modal -->
<div id="modal-1" class="modal__overlay" style="display: none;">
  <div class="modal__container">
    <header>
      <button class="modal__close" aria-label="Close modal">&times;</button>
      <h2>Meeting Details</h2>
    </header>
    <div class="modal__content">
      <form id="modal-form">
        <div style="margin-bottom: 10px;">
          <label for="user_id">User ID</label>
          <input type="text" id="user_id" name="user_id" required style="width: 100%;">
        </div>
        <div style="margin-bottom: 10px;">
          <label for="name">Name</label>
          <input type="text" id="name" name="name" required style="width: 100%;">
        </div>
        <div style="margin-bottom: 10px;">
          <label for="location">Location</label>
          <gmpx-place-autocomplete id="location-input" style="width: 100%;"></gmpx-place-autocomplete>
        </div>
        <div style="margin-bottom: 10px;">
          <label for="latitude">Latitude</label>
          <input type="text" id="latitude" name="latitude" readonly required style="width: 100%;">
        </div>
        <div style="margin-bottom: 10px;">
          <label for="longitude">Longitude</label>
          <input type="text" id="longitude" name="longitude" readonly required style="width: 100%;">
        </div>
        <div style="margin-bottom: 10px;">
          <label for="meeting_time">Meeting Time</label>
          <input type="time" id="meeting_time" name="meeting_time" required style="width: 100%;">
        </div>
        <div style="margin-bottom: 10px;">
          <label for="distance_time">Distance Time</label>
          <input type="text" id="distance_time" name="distance_time" required style="width: 100%;">
        </div>
        <div style="margin-bottom: 10px;">
          <label for="date">Date</label>
          <input type="date" id="date" name="date" required style="width: 100%;">
        </div>

        <div style="margin-top: 20px; display: flex; justify-content: flex-end;">
          <button type="submit">Save</button>
          <button type="button" id="cancel-button">Cancel</button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- Load Maps JS API without callback -->
<script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyBu6xAYnCNlOJ4R4k7eGd2GrkRhnCh6k7A&libraries=places" async defer></script>

<!-- Load Web Components for PlaceAutocompleteElement -->
<script type="module" src="https://unpkg.com/@googlemaps/extended-component-library@latest/dist/place_autocomplete_element.js"></script>

<script>
  const openModalButton = document.getElementById('open-modal');
  const modal = document.getElementById('modal-1');
  const closeModalButton = document.querySelector('.modal__close');
  const cancelModalButton = document.getElementById('cancel-button');
  const autocompleteElement = document.getElementById('location-input');

  openModalButton.addEventListener('click', () => {
    modal.style.display = 'flex';
  });

  closeModalButton.addEventListener('click', () => {
    modal.style.display = 'none';
  });

  cancelModalButton.addEventListener('click', () => {
    modal.style.display = 'none';
  });

  // Get coordinates when a place is selected
  autocompleteElement?.addEventListener('gmpx-placechange', () => {
    const place = autocompleteElement?.value;
    const latInput = document.getElementById('latitude');
    const lngInput = document.getElementById('longitude');

    if (autocompleteElement?.place?.geometry?.location) {
      latInput.value = autocompleteElement.place.geometry.location.lat();
      lngInput.value = autocompleteElement.place.geometry.location.lng();
    }
  });

  document.getElementById('modal-form').addEventListener('submit', function (event) {
    event.preventDefault();
    const formData = new FormData(event.target);
    console.log('Form Data:', Object.fromEntries(formData.entries()));
    modal.style.display = 'none';
  });
</script>

</body>
</html>
