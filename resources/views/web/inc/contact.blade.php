<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>Book a Meeting</title>

  <!-- Styles -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css" />
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.8/css/intlTelInput.css" />
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet" />

  <style>
    body {
      margin: 0;
      padding: 0;
      font-family: 'Poppins', sans-serif;
      background: #f0f4f8;
      height: 100vh;
      display: flex;
      align-items: center;
      justify-content: center;
    }

    .contact-container {
      background: #ffffff;
      border-radius: 10px;
      box-shadow: 0 10px 30px rgba(0,0,0,0.15);
      display: flex;
      width: 90%;
      max-width: 1200px;
      min-height: 700px;
      overflow: hidden;
    }

    .form-section, .calendar-section {
      flex: 1;
      display: flex;
      flex-direction: column;
      justify-content: center;
      padding: 50px;
    }

    .form-section {
      background-color: #F2F6FF;
    }

    .form-section h3 {
      color: #333333;
      font-size: 32px;
      font-weight: 900;
      margin-bottom: 30px;
    }

    .form-control {
      background: #fff;
      border: 1px solid #ccc;
      border-radius: 5px;
      padding: 10px 15px;
      font-size: 16px;
      color: #333;
      transition: all 0.3s ease;
      margin-bottom: 20px;
      width: 100%;
    }

    .form-control:focus {
      border-color: #6a11cb;
      box-shadow: 0 0 0 4px rgba(106, 17, 203, 0.15);
      outline: none;
    }

    .btn-primary {
      background: linear-gradient(135deg, #6a11cb 0%, #2575fc 100%);
      border: none;
      border-radius: 5px;
      padding: 14px 30px;
      font-size: 16px;
      font-weight: 600;
      transition: background 0.3s;
      width: 100%;
      color: #fff;
      cursor: pointer;
    }

    .btn-primary:hover {
      background: linear-gradient(135deg, #2575fc 0%, #6a11cb 100%);
    }

    .calendar-section {
      background: url('https://t4.ftcdn.net/jpg/10/95/98/59/240_F_1095985933_J2wC9izxs9fZHvvgFxPC7sKutX8ntwhl.jpg') no-repeat center center/cover;
      position: relative;
    }

    .calendar-wrapper {
      background: rgba(0, 0, 0, 0.7);
      padding: 40px;
      border-radius: 10px;
      box-shadow: 0 8px 20px rgba(0, 0, 0, 0.3);
      max-width: 500px;
      width: 100%;
      margin: auto;
      text-align: center;
    }

    .calendar-wrapper h2 {
      color: #ffffff;
      font-size: 28px;
      margin-bottom: 20px;
      font-weight: 700;
    }

    #calendar {
      margin-top: 20px;
    }

    .flatpickr-calendar {
      font-size: 1rem;
      border-radius: 8px;
      overflow: hidden;
      box-shadow: 0 8px 20px rgba(0,0,0,0.2);
    }

    .flatpickr-day {
      height: 50px;
      line-height: 50px;
      width: 50px;
      font-size: 18px;
      border-radius: 8px;
      transition: all 0.3s ease;
    }

    .flatpickr-day:hover {
      background: #2575fc;
      color: #fff;
    }

    #phone {
      width: 100%;
    }

    #form-message {
      font-size: 16px;
      margin-bottom: 20px;
    }

    @media (max-width: 768px) {
      .contact-container {
        flex-direction: column;
      }

      .calendar-wrapper {
        margin: 30px 0;
      }
    }
  </style>
</head>

<body>

<div class="contact-container">
  <div class="form-section">
    <h3>Let's schedule your meeting</h3>
    <div id="form-message" class="text-center mb-3 fw-bold"></div>

    <form id="booking-form">
      <input type="text" id="name" name="name" class="form-control" placeholder="Your Name" required />
      <input type="tel" id="phone" name="phone" class="form-control" placeholder="Phone Number" required />
      <input type="email" id="email" name="email" class="form-control" placeholder="Email Address" required />
      <input type="text" id="location" name="location" class="form-control" placeholder="Location" autocomplete="off" required />
      <input type="time" id="meeting_time" name="meeting_time" class="form-control" required />
      <input type="hidden" id="selected_date" name="date">
      <input type="hidden" id="latitude" name="latitude">
      <input type="hidden" id="longitude" name="longitude">
      <input type="hidden" id="ip" name="ip">
      <input type="hidden" id="city" name="city">
      <input type="hidden" id="distance_time" name="distance_time">
      <input type="hidden" id="distance_km" name="distance_km">

      <button type="submit" class="btn btn-primary mt-3">Book Meeting</button>
    </form>
  </div>

  <div class="calendar-section">
    <div class="calendar-wrapper">
      <h2>Select Date</h2>
      <div id="calendar"></div>
    </div>
  </div>
</div>

<!-- Scripts -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.8/js/intlTelInput.min.js"></script>

<script>
document.addEventListener("DOMContentLoaded", function() {
  // Initialize phone input
  const phoneInput = document.querySelector("#phone");
  const iti = window.intlTelInput(phoneInput, {
    nationalMode: false,
    initialCountry: "auto",
    geoIpLookup: async function(callback) {
      try {
        const response = await fetch("https://ipinfo.io/json?token=85d3b65b39e700");
        const data = await response.json();
        callback(data.country || "us");

        // Autofill location field
        if (data.city && data.region && data.country) {
          document.getElementById("location").value = `${data.city}, ${data.region}, ${data.country}`;
        } else if (data.city && data.country) {
          document.getElementById("location").value = `${data.city}, ${data.country}`;
        } else if (data.country) {
          document.getElementById("location").value = data.country;
        }
      } catch (e) {
        callback("us");
      }
    },
    utilsScript: "https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.8/js/utils.js"
  });

  // Initialize calendar
  flatpickr("#calendar", {
    inline: true,
    minDate: "today",
    onChange: function(selectedDates, dateStr) {
      document.getElementById("selected_date").value = dateStr;
    }
  });

  // Form submission
  document.getElementById("booking-form").addEventListener("submit", async function(e) {
    e.preventDefault();
    const form = e.target;
    const formData = new FormData(form);

    // Set the full international phone number
    formData.set("phone", iti.getNumber());

    try {
      const res = await fetch("/meetings", {
        method: "POST",
        headers: {
          "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').content
        },
        body: formData
      });

      const data = await res.json();
      document.getElementById("form-message").textContent = data.message || "Meeting booked successfully!";
      document.getElementById("form-message").className = "text-success fw-bold";
      form.reset();
      iti.setCountry("auto");
    } catch (err) {
      document.getElementById("form-message").textContent = "Error saving meeting.";
      document.getElementById("form-message").className = "text-danger fw-bold";
    }
  });
});
</script>

</body>
</html>
