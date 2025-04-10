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
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            background-color: #f4f4f4;
        }

        /* Open Modal Button Style */
        #open-modal {
            padding: 12px 25px;
            background-color: #34b7f1;
            color: white;
            font-size: 16px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            display: flex;
            align-items: center;
        }

        #open-modal img {
            margin-right: 10px;
        }

        #open-modal:hover {
            background-color: #0099cc;
        }

        /* Modal Overlay */
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

        /* Rectangular Modal Box */
        .modal__container {
            background: white;
            padding: 20px;
            border-radius: 8px;
            width: 500px; /* Shorter and more rectangular */
            box-sizing: border-box;
            position: relative;
        }

        button.modal__close,
        button.modal__delete {
            position: absolute;
            top: 10px;
            right: 10px;
            background: none;
            border: none;
            font-size: 20px;
            cursor: pointer;
        }

        .modal__delete {
            top: 40px;
            right: 10px;
            font-size: 16px;
            color: red;
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

        input[type="text"], input[type="date"], input[type="datetime-local"] {
            width: 100%;
            padding: 8px;
            margin: 10px 0;
            border: 1px solid #ccc;
            border-radius: 4px;
        }

        .modal__header h2 {
            margin: 0;
            font-size: 20px;
            display: flex;
            align-items: center;
        }

        .modal__header img {
            margin-right: 10px;
        }

    </style>
</head>
<body>

<!-- Button to Trigger Modal with Google Meet Logo -->
<button id="open-modal" class="button">
    <img src="https://upload.wikimedia.org/wikipedia/commons/3/3b/Google_Meet_logo_2020.svg" alt="Google Meet" width="24" height="24">
    Open Modal
</button>

<!-- Modal Structure -->
<div id="modal-1" class="modal__overlay" style="display: none;">
    <div class="modal__container">
        <header class="modal__header">
            <button class="modal__close" aria-label="Close modal">&times;</button>
            <img src="https://upload.wikimedia.org/wikipedia/commons/3/3b/Google_Meet_logo_2020.svg" alt="Google Meet" width="24" height="24">
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
        <button class="modal__delete" aria-label="Delete modal">Delete</button>
    </div>
</div>

<!-- Include JavaScript to initialize VanillaModal -->
<script>
    // Initialize VanillaModal and the button to trigger it
    const openModalButton = document.getElementById('open-modal');
    const modal = document.getElementById('modal-1');
    const closeModalButton = document.querySelector('.modal__close');
    const deleteModalButton = document.querySelector('.modal__delete');

    // Focus on the first input field when the modal opens
    openModalButton.addEventListener('click', function() {
        modal.style.display = 'flex'; // Show the modal
        document.getElementById('user_id').focus(); // Focus on the first input field
    });

    // Close the modal when the close button is clicked
    closeModalButton.addEventListener('click', function() {
        modal.style.display = 'none'; // Hide the modal
    });

    // Delete the modal when the delete button is clicked
    deleteModalButton.addEventListener('click', function() {
        modal.style.display = 'none'; // Remove the modal
        // Optionally, delete the modal DOM element itself (if you want to completely remove it)
        modal.remove();
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
