<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modal Example</title>

    <!-- Include Micromodal.js -->
    <script src="https://cdn.jsdelivr.net/npm/micromodal@0.4.6/dist/micromodal.min.js"></script>

    <!-- Include custom styles (optional) -->
    <style>
        .modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            z-index: 1000;
        }

        .modal__overlay {
            background-color: rgba(0, 0, 0, 0.5);
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .modal__container {
            background: white;
            padding: 20px;
            border-radius: 8px;
            width: 400px;
            box-sizing: border-box;
        }

        button.modal__close {
            position: absolute;
            top: 10px;
            right: 10px;
            background: none;
            border: none;
            font-size: 20px;
        }

        button[type="submit"] {
            padding: 10px 20px;
            background-color: #007bff;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }

        button[type="submit"]:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body>

<!-- Button to Trigger Modal -->
<button id="open-modal" class="button">Open Modal</button>

<!-- Modal Structure -->
<div id="modal-1" class="modal micromodal-slide" aria-hidden="true">
    <div class="modal__overlay" tabindex="-1">
        <div class="modal__container" role="dialog" aria-modal="true" aria-labelledby="modal-1-title">
            <header class="modal__header">
                <button class="modal__close" aria-label="Close modal" data-micromodal-close></button>
                <h2 class="modal__title" id="modal-1-title">Meeting Details</h2>
            </header>
            <div class="modal__content">
                <form id="modal-form">
                    <div>
                        <label for="user_id">User ID</label>
                        <input type="text" id="user_id" name="user_id" required>
                    </div>
                    <div>
                        <label for="name">Name</label>
                        <input type="text" id="name" name="name" required>
                    </div>
                    <div>
                        <label for="location">Location</label>
                        <input type="text" id="location" name="location" required>
                    </div>
                    <div>
                        <label for="latitude">Latitude</label>
                        <input type="text" id="latitude" name="latitude" required>
                    </div>
                    <div>
                        <label for="longitude">Longitude</label>
                        <input type="text" id="longitude" name="longitude" required>
                    </div>
                    <div>
                        <label for="meeting_time">Meeting Time</label>
                        <input type="datetime-local" id="meeting_time" name="meeting_time" required>
                    </div>
                    <div>
                        <label for="distance_time">Distance Time</label>
                        <input type="text" id="distance_time" name="distance_time" required>
                    </div>
                    <div>
                        <label for="date">Date</label>
                        <input type="date" id="date" name="date" required>
                    </div>
                    <button type="submit">Save</button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Include JavaScript to initialize Micromodal.js -->
<script>
    // Initialize Micromodal.js
    MicroModal.init();

    // Trigger to open modal when button is clicked
    document.getElementById('open-modal').addEventListener('click', function() {
        MicroModal.show('modal-1');  // Show the modal with ID "modal-1"
    });

    // Example of form submission handling
    document.getElementById('modal-form').addEventListener('submit', function(event) {
        event.preventDefault();
        // You can handle the form submission here (e.g., send data via AJAX)
        const formData = new FormData(event.target);
        console.log('Form Data:', Object.fromEntries(formData.entries()));

        // Close the modal after submission
        MicroModal.close('modal-1');
    });
</script>

</body>
</html>
