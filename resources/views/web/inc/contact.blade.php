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
    /* body {
      font-family: 'Poppins', sans-serif;
      background: linear-gradient(135deg, #6a11cb 0%, #2575fc 100%);
      min-height: 100vh;
      display: flex;
      align-items: center;
      justify-content: center;
      padding: 30px;
    } */

    .contact-container {
      background: #ffffff;
      border-radius: 0px;
      /* box-shadow: 0 10px 30px rgba(0,0,0,0.15); */
      overflow: hidden;
      width: 100%;
      max-width: 1200px;
      display: flex;
      flex-wrap: wrap;
      margin: auto;
      margin-top: 50px;
      margin-bottom: 50px;
    }

    .form-section {
      flex: 1 1 50%;
      padding: 50px;
      background-color: #F2F6FF;
    }

    .form-section h3 {
      color: #000000;
      font-size: 28px;
      font-weight: 900;
    }
    .dayContainer {
    padding: 15!important;
    width: 400px!important;
    min-width: 400!important;
    max-width: 400px!important;
    height: 220px!important;
  }
  
  .calendar-section {
      flex: 1 1 50%;
      /* background: #fff; */
      display: flex;
      align-items: center;
      justify-content: center;
      padding: 0px;
      border-left: 1px solid #eee;
    }

    .calendar-wrapper {
      /* background: linear-gradient(135deg, #2575fc 0%, #6a11cb 100%); */
      padding: 50px 40px;
      border-radius: 5px;
      box-shadow: 0 8px 20px rgba(0, 0, 0, 0.178);
      width: 100%;
      max-width: 470px;

      position: relative;
      background: rgba(0, 0, 0, 0.564);
     
    }

    .calendar-wrapper h2 {
      color: #ffffff;
      font-size: 28px;
      text-align: center;
      margin-bottom: 20px;
      font-weight: 700;
    }

    /* Modern input fields */
    .form-control {
      background: #fff;
      border: 1px solid #ccc;
      border-radius: 2px;
      padding: 8px 12px;
      font-size: 16px;
      color: #333;
      transition: all 0.3s ease;
      margin-bottom: 20px;
    }

    .form-control:focus {
      border-color: #6a11cb;
      box-shadow: 0 0 0 4px rgba(106, 17, 203, 0.15);
      background-color: #fff;
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
    }

    .btn-primary:hover {
      background: linear-gradient(135deg, #2575fc 0%, #6a11cb 100%);
    }

    /* Make Flatpickr Calendar bigger and better */
    .flatpickr-calendar {
      font-size: 1rem;
      width: 100% !important;
      max-width: 100% !important;
      border-radius: 5px;
      overflow: hidden;
      box-shadow: 0 8px 20px rgba(0,0,0,0.2);
    }

    .flatpickr-months {
      font-size: 1rem;
      background: #fff;
      padding: 8px 0;
      border-bottom: 1px solid #eee;
    }
    .flatpickr-next-month {
      margin-top: 10px;
    }
    .flatpickr-weekdays {
      background: #f0f0f0;
      font-size: 0.9rem;
      color: #555;
    }
    .flatpickr-days {
      width: 400px !important;
    }

    .flatpickr-day {
      height: 50px;
      line-height: 50px;
      width: 50px;
      font-size: 1.1rem;
      border-radius: 8px;
      transition: all 0.2s;
    }

    .flatpickr-day:hover {
      background: #2575fc;
      color: #fff;
      border-radius: 8px;
    }

    /* .flatpickr-day.today {
      background: #6a11cb;
      color: white;
      border-radius: 8px;
    } */

    @media (max-width: 768px) {
      .contact-container {
        flex-direction: column;
      }
      .calendar-section {
        border-left: none;
        border-top: 1px solid #eee;
      }
    }

    .flatpickr-rContainer {
        width: 100%;
    }

    #phone{
        width: 420px !important;
        /* margin-bottom: 20px !important; */
    }
    #email{
        margin-top: 20px !important;
    }
    .selected{
        background-color: #3CC065 !important;
    }
  </style>
</head>

<body>

<div class="contact-container">
  <div class="form-section">
    <h3 class="mb-4">Let's schedule your meeting</h3>
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

  <div style="background-image: url('https://t4.ftcdn.net/jpg/10/95/98/59/240_F_1095985933_J2wC9izxs9fZHvvgFxPC7sKutX8ntwhl.jpg');" class="calendar-section">
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
      
      // Reset phone input state
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
