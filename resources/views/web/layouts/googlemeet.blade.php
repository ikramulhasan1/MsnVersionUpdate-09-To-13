<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Book a Meeting</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.19/css/intlTelInput.min.css">
    <style>
        .modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.6);
            justify-content: center;
            align-items: center;
        }

        .modal-content {
            background: white;
            padding: 2rem;
            border-radius: 12px;
            width: 500px;
            max-width: 95%;
        }

        .hidden {
            display: none;
        }

        .form-group {
            margin-bottom: 1rem;
        }

        input, select, textarea {
            width: 100%;
            padding: 0.5rem;
            border: 1px solid #ccc;
            border-radius: 6px;
        }

        label {
            font-weight: bold;
            display: block;
            margin-bottom: 0.3rem;
        }

        button {
            background-color: #007bff;
            color: white;
            padding: 0.6rem 1.2rem;
            border: none;
            border-radius: 6px;
            cursor: pointer;
        }

        button:hover {
            background-color: #0056b3;
        }
    </style>
</head>

<body>
    <button id="openModal">Book a Meeting</button>

    <div class="modal" id="meetingModal">
        <div class="modal-content">
            <form id="meetingForm">
                <h2>Book a Meeting</h2>

                <div class="form-group">
                    <label for="fullName">Full Name</label>
                    <input type="text" id="fullName" name="fullName" required>
                </div>

                <div class="form-group">
                    <label for="email">Email</label>
                    <input type="email" id="email" name="email" required>
                </div>

                <div class="form-group">
                    <label for="phone">Phone Number</label>
                    <input type="tel" id="phone" name="phone">
                </div>

                <div class="form-group">
                    <label for="location">Location</label>
                    <input type="text" id="location" name="location">
                </div>

                <div class="form-group">
                    <label for="date">Meeting Date & Time</label>
                    <input type="text" id="date" name="date" required>
                </div>

                <div class="form-group">
                    <label for="platform">Meeting Platform</label>
                    <select id="platform" name="platform" required>
                        <option value="">Select Platform</option>
                        <option value="zoom">Zoom</option>
                        <option value="google_meet">Google Meet</option>
                    </select>
                </div>

                <div class="form-group hidden" id="zoomLinkGroup">
                    <label for="zoomLink">Zoom Meeting Link</label>
                    <input type="url" id="zoomLink" name="zoomLink">
                </div>

                <div class="form-group hidden" id="googleMeetLinkGroup">
                    <label for="googleMeetLink">Google Meet Link</label>
                    <input type="url" id="googleMeetLink" name="googleMeetLink">
                </div>

                <button type="submit">Submit</button>
            </form>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.19/js/intlTelInput.min.js"></script>
    <script src="https://api.opencagedata.com/geocode/v1/json?key=YOUR_OPENCAGE_API_KEY&q="></script>

    <script>
        document.getElementById("openModal").onclick = function () {
            document.getElementById("meetingModal").style.display = "flex";
        };

        document.getElementById("platform").addEventListener("change", function () {
            const zoom = document.getElementById("zoomLinkGroup");
            const google = document.getElementById("googleMeetLinkGroup");
            if (this.value === "zoom") {
                zoom.classList.remove("hidden");
                google.classList.add("hidden");
            } else if (this.value === "google_meet") {
                google.classList.remove("hidden");
                zoom.classList.add("hidden");
            } else {
                zoom.classList.add("hidden");
                google.classList.add("hidden");
            }
        });

        flatpickr("#date", {
            enableTime: true,
            dateFormat: "Y-m-d H:i",
            minDate: "today"
        });

        const input = document.querySelector("#phone");
        window.intlTelInput(input, {
            initialCountry: "auto",
            geoIpLookup: function (success, failure) {
                fetch("https://ipapi.co/json")
                    .then(res => res.json())
                    .then(data => success(data.country_code))
                    .catch(() => success("us"));
            },
            utilsScript: "https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.19/js/utils.js"
        });

        document.getElementById("meetingForm").addEventListener("submit", function (e) {
            e.preventDefault();
            const formData = new FormData(this);

            fetch("/api/meetings", {
                method: "POST",
                headers: {
                    "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').getAttribute("content")
                },
                body: formData
            })
            .then(res => res.json())
            .then(data => {
                alert("Meeting successfully booked!");
                document.getElementById("meetingModal").style.display = "none";
            })
            .catch(err => {
                console.error(err);
                alert("Error booking meeting");
            });
        });
    </script>
</body>
</html>
