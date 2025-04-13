<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modal Example</title>

    <!-- Include VanillaModal.js -->
    <script src="https://cdn.jsdelivr.net/npm/vanillajs-modal@1.1.2/dist/vanilla.min.js"></script>

    <!-- Include custom styles (optional) -->
    <style>
        .modal__overlay {
            background-color: rgba(0, 0, 0, 0.5);
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
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
            box-sizing: border-box;
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


.meeting-logo {
  position: absolute;
  top: 50%; /* vertical centering start */
  left: 0;
  width: 100%;
  height: auto;
  object-fit: contain;
  transform: translateY(-50%); /* perfect vertical alignment */
  opacity: 0;
  transition: opacity 1s ease-in-out;
  pointer-events: none;
}

.meeting-logo.active {
  opacity: 1;
  pointer-events: auto;
}

    </style>
</head>
<body>

<!-- Button to Trigger Modal -->
<div style="display: flex; justify-content: space-between; align-items: center;">

<button id="open-modal" class="button google-meet-button" style="background-color: #48bb78; color: white; padding: 12px 24px; cursor: pointer; display: flex; align-items: center;">
    <div class="logo-container">
      <img id="google-meet-img" src="https://www.gstatic.com/meet/google_meet_horizontal_wordmark_2020q4_2x_icon_124_40_292e71bcb52a56e2a9005164118f183b.png" alt="Google Meet Logo" class="meeting-logo active" />
  
      <img id="zoom-img" src="https://upload.wikimedia.org/wikipedia/commons/7/7b/Zoom_Communications_Logo.svg" alt="Zoom Logo" class="meeting-logo" />
    </div>
    <!-- Button text -->
    <span style="font-weight: 600; font-size: 18px; color: white; margin-left: 12px;">Book a Meeting</span>
  </button>
</div>

<!-- Modal Structure -->
<div id="modal-1" class="modal__overlay" style="display: none;">
    <div class="modal__container">
        <header>
            <button class="modal__close" aria-label="Close modal">&times;</button>
            <h2>Meeting Details</h2>
        </header>
        <div class="modal__content">
            <form id="modal-form">
                <div hidden style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 10px;">
                    <label for="user_id">User ID</label>
                    <input style="box-shadow: 0 1px 4px rgba(0,0,0,0.2); width: 70%;" type="text" id="user_id" name="user_id" required>
                </div>
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 10px;">
                    <label for="name">Name</label>
                    <input style="box-shadow: 0 1px 4px rgba(0,0,0,0.2); width: 70%;" type="text" id="name" name="name" required>
                </div>
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 10px;">
                    <label for="location">Location</label>
                    <input  autocomplete="off" spellcheck="false" style="box-shadow: 0 1px 4px rgba(0,0,0,0.2); width: 70%;" type="text" id="location" name="location" required>
                </div>
                <div hidden style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 10px;">
                    <label for="latitude">Latitude</label>
                    <input style="box-shadow: 0 1px 4px rgba(0,0,0,0.2); width: 70%;" type="text" id="latitude" name="latitude" required>
                </div>
                <div hidden style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 10px;">
                    <label for="longitude">Longitude</label>
                    <input style="box-shadow: 0 1px 4px rgba(0,0,0,0.2); width: 70%;" type="text" id="longitude" name="longitude" required>
                </div>
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 10px;">
                    <label for="meeting_time">Meeting Time</label>
                    <input style="box-shadow: 0 1px 4px rgba(0,0,0,0.2); width: 70%;" type="time" id="meeting_time" name="meeting_time" required>
                </div>
                <div hidden style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 10px;">
                    <label for="distance_time">Distance Time</label>
                    <input style="box-shadow: 0 1px 4px rgba(0,0,0,0.2); width: 70%;" type="text" id="distance_time" name="distance_time" required>
                </div>
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 10px;">
                    <label for="date">Date</label>
                    <input style="box-shadow: 0 1px 4px rgba(0,0,0,0.2); width: 70%;" type="date" id="date" name="date" required>
                </div>

                <!-- Save and Cancel buttons -->
                <div style="margin-top: 20px; display: flex; justify-content: flex-end;">
                    <button type="submit">Save</button>
                    <button type="button" id="cancel-button">Cancel</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Google Maps Places API -->
<script async defer src="https://maps.googleapis.com/maps/api/js?key=AIzaSyBu6xAYnCNlOJ4R4k7eGd2GrkRhnCh6k7A&libraries=places&callback=initMap"></script>

<!-- JavaScript -->
<script>
    const openModalButton = document.getElementById('open-modal');
    const modal = document.getElementById('modal-1');
    const closeModalButton = document.querySelector('.modal__close');
    const cancelModalButton = document.getElementById('cancel-button');

    let autocompleteInitialized = false;

openModalButton.addEventListener('click', function() {
    modal.style.display = 'flex';

    if (!autocompleteInitialized) {
        const input = document.getElementById('location');
        if (input) {
            setTimeout(() => {
                const autocomplete = new google.maps.places.Autocomplete(input, { types: ['geocode'] });

                autocomplete.addListener('place_changed', function() {
                    const place = autocomplete.getPlace();
                    if (place.geometry) {
                        document.getElementById('latitude').value = place.geometry.location.lat();
                        document.getElementById('longitude').value = place.geometry.location.lng();
                    }
                });

                autocompleteInitialized = true;
            }, 30000); // Delay to allow DOM to fully render
        }
    }
});


    closeModalButton.addEventListener('click', function() {
        modal.style.display = 'none';
    });

    cancelModalButton.addEventListener('click', function() {
        modal.style.display = 'none';
    });

    document.getElementById('modal-form').addEventListener('submit', function(event) {
        event.preventDefault();
        const formData = new FormData(event.target);
        console.log('Form Data:', Object.fromEntries(formData.entries()));
        modal.style.display = 'none';
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
