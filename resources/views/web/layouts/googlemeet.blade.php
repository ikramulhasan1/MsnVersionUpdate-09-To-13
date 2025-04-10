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

        /* Add focus style for input fields */
        input[type="text"], input[type="date"], input[type="datetime-local"] {
            width: 100%;
            padding: 8px;
            margin: 10px 0;
            border: 1px solid #ccc;
            border-radius: 4px;
            transition: border 0.3s ease, box-shadow 0.3s ease;
        }

        /* Style when input field is focused */
        input[type="text"]:focus, input[type="date"]:focus, input[type="datetime-local"]:focus {
            border-color: #007bff;
            box-shadow: 0 0 5px rgba(0, 123, 255, 0.5);
            outline: none;
        }

    </style>
</head>
<body>

<!-- Button to Trigger Modal -->
<button id="open-modal" class="button">Open Modal</button>

<!-- Modal Structure -->
<div id="modal-1" class="modal__overlay" style="display: none;">
    <div class="modal__container">
        <header>
            <button class="modal__close" aria-label="Close modal">&times;</button>
            <h2>Meeting Details</h2>
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

<!-- Include JavaScript to initialize VanillaModal -->
<script>
    // Initialize VanillaModal and the button to trigger it
    const openModalButton = document.getElementById('open-modal');
    const modal = document.getElementById('modal-1');
    const closeModalButton = document.querySelector('.modal__close');

    openModalButton.addEventListener('click', function() {
        modal.style.display = 'flex'; // Show the modal
    });

    closeModalButton.addEventListener('click', function() {
        modal.style.display = 'none'; // Hide the modal
    });

    // Example of form submission handling
    document.getElementById('modal-form').addEventListener('submit', function(event) {
        event.preventDefault();
        // You can handle the form submission here (e.g., send data via AJAX)
        const formData = new FormData(event.target);
        console.log('Form Data:', Object.fromEntries(formData.entries()));

        // Close the modal after submission
        modal.style.display = 'none';
    });
</script>

</body>
</html>
