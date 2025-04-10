<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Google Meet Smart Form</title>

  <style>
    /* General reset */
    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
    }

    body {
      font-family: 'Roboto', sans-serif;
      background-color: #f0f4f8;
      padding-top: 100px; /* Space for fixed navbar */
    }

    /* Navbar */
    .navbar {
      background-color: #2b6cb0;
      color: white;
      position: fixed;
      top: 0;
      left: 0;
      width: 100%;
      z-index: 9999;
      padding: 14px 20px;
      box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    }

    .navbar .container {
      display: flex;
      justify-content: space-between;
      align-items: center;
    }

    .navbar .nav-links a {
      color: white;
      margin-left: 20px;
      text-decoration: none;
      font-size: 16px;
      transition: color 0.3s;
    }

    .navbar .nav-links a:hover {
      color: #edf2f7;
    }

    /* Smart Google Meet Button */
    .google-meet-button {
      display: flex;
      align-items: center;
      background: linear-gradient(90deg, #48bb78, #4299e1);
      color: white;
      padding: 14px 28px;
      border-radius: 30px;
      box-shadow: 0 6px 12px rgba(0, 0, 0, 0.1);
      cursor: pointer;
      transition: transform 0.3s, box-shadow 0.3s, background-color 0.3s;
      border: none;
    }

    .google-meet-button:hover {
      transform: scale(1.05);
      box-shadow: 0 6px 12px rgba(0, 0, 0, 0.2);
      background-color: #3b82f6;
    }

    .google-meet-button img {
      width: 48px;
      height: auto;
      margin-right: 16px;
    }

    .google-meet-button span {
      font-weight: 600;
      font-size: 18px;
    }

    /* Modal */
    #googleMeetModal {
      position: fixed;
      top: 0;
      left: 0;
      width: 100%;
      height: 100%;
      background-color: rgba(0, 0, 0, 0.6);
      display: none;
      justify-content: center;
      align-items: center;
      z-index: 9999;
    }

    .modal-content {
      background-color: white;
      width: 100%;
      max-width: 700px;
      padding: 40px;
      border-radius: 20px;
      box-shadow: 0 6px 12px rgba(0, 0, 0, 0.1);
      animation: fadeIn 0.3s ease-out;
      transform: scale(0.9);
    }

    @keyframes fadeIn {
      from {
        opacity: 0;
        transform: scale(0.9);
      }
      to {
        opacity: 1;
        transform: scale(1);
      }
    }

    .modal-content h2 {
      text-align: center;
      font-size: 28px;
      color: #2d3748;
      margin-bottom: 25px;
      font-weight: 700;
    }

    .modal-content input,
    .modal-content button {
      width: 100%;
      padding: 16px;
      margin-bottom: 20px;
      border: 1px solid #d1d5db;
      border-radius: 12px;
      font-size: 16px;
      background-color: #f9fafb;
      transition: border-color 0.3s, box-shadow 0.3s;
    }

    .modal-content input:focus,
    .modal-content button:focus {
      outline: none;
      border-color: #3b82f6;
      box-shadow: 0 0 6px rgba(59, 130, 246, 0.4);
    }

    .modal-content button {
      background-color: #3b82f6;
      color: white;
      border: none;
      font-weight: 600;
      cursor: pointer;
    }

    .modal-content button:hover {
      background-color: #2563eb;
    }

    /* Close Button */
    .close-btn {
      position: absolute;
      top: 20px;
      right: 20px;
      font-size: 28px;
      color: #e2e8f0;
      background: none;
      border: none;
      cursor: pointer;
      transition: color 0.3s;
    }

    .close-btn:hover {
      color: #3b82f6;
    }

    /* Label Styling */
    label {
      font-weight: 600;
      margin-bottom: 8px;
      display: inline-block;
    }

    /* Form and Modal Animation */
    @keyframes fadeIn {
      0% {
        opacity: 0;
        transform: translateY(-30px);
      }
      100% {
        opacity: 1;
        transform: translateY(0);
      }
    }
  </style>
</head>
<body>

  <!-- Navbar -->
  <div class="navbar">
    <div class="container">
      <div class="text-xl font-semibold">My Website</div>
      <div class="nav-links">
        <a href="#home">Home</a>
        <a href="#services">Services</a>
        <a href="#contact">Contact</a>
      </div>
    </div>
  </div>

  <!-- Smart Google Meet Button -->
  <div class="flex justify-center items-center mt-24">
    <button onclick="toggleModal(true)" class="google-meet-button">
      <img src="https://www.gstatic.com/meet/google_meet_horizontal_wordmark_2020q4_2x_icon_124_40_292e71bcb52a56e2a9005164118f183b.png" alt="Google Meet Logo" />
      <span>Book a Meeting</span>
    </button>
  </div>

  <!-- Modal -->
  <div id="googleMeetModal">
    <div class="modal-content">
      <!-- Close Button -->
      <button onclick="toggleModal(false)" class="close-btn">&times;</button>

      <h2>Google Meet Smart Form</h2>

      <!-- Form -->
      <form>
        <input type="text" hidden placeholder="User ID" />

        <input type="text" placeholder="Name" />

        <input type="text" placeholder="Location" />

        <div>
          <label for="meeting_time">Meeting Time</label>
          <input id="meeting_time" type="time" placeholder="Meeting Time" />
        </div>

        <div>
          <label for="date">Meeting Date</label>
          <input id="date" type="date" placeholder="Date" />
        </div>

        <button type="submit">Submit</button>
      </form>
    </div>
  </div>

  <!-- Script -->
  <script>
    function toggleModal(show) {
      const modal = document.getElementById('googleMeetModal');
      modal.style.display = show ? 'flex' : 'none';
    }
  </script>

</body>
</html>
