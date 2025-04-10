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
      font-family: 'Poppins', sans-serif;
      background-color: #f7fafc;
      padding-top: 80px; /* Space for fixed navbar */
    }

    /* Navbar */
    .navbar {
      background-color: #1a202c;
      color: white;
      position: fixed;
      top: 0;
      left: 0;
      width: 100%;
      z-index: 9999;
      padding: 16px 20px;
      box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
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
      font-weight: 500;
      transition: color 0.3s ease-in-out;
    }

    .navbar .nav-links a:hover {
      color: #edf2f7;
    }

    /* Google Meet Button */
    .google-meet-button {
      display: flex;
      align-items: center;
      background: linear-gradient(90deg, #48bb78, #4299e1);
      color: white;
      padding: 16px 32px;
      border-radius: 30px;
      box-shadow: 0 6px 12px rgba(0, 0, 0, 0.1);
      cursor: pointer;
      font-weight: 600;
      transition: transform 0.2s, box-shadow 0.3s, background-color 0.3s ease-in-out;
      border: none;
      font-size: 18px;
    }

    .google-meet-button:hover {
      transform: scale(1.05);
      box-shadow: 0 8px 16px rgba(0, 0, 0, 0.2);
      background-color: #3b82f6;
    }

    .google-meet-button img {
      width: 40px;
      height: auto;
      margin-right: 16px;
    }

    /* Modal */
    #googleMeetModal {
      position: fixed;
      top: 50%;
      left: 50%;
      transform: translate(-50%, -50%);
      width: 90%;
      max-width: 500px;
      height: auto;
      background-color: rgba(0, 0, 0, 0.6);
      display: none;
      justify-content: center;
      align-items: center;
      z-index: 9999;
    }

    .modal-content {
      background-color: white;
      padding: 24px;
      border-radius: 15px;
      box-shadow: 0 6px 12px rgba(0, 0, 0, 0.2);
      max-width: 100%;
      animation: fadeIn 0.25s ease-out;
      transform: scale(0.95);
    }

    @keyframes fadeIn {
      from {
        opacity: 0;
        transform: scale(0.95);
      }
      to {
        opacity: 1;
        transform: scale(1);
      }
    }

    .modal-content h2 {
      font-size: 22px;
      color: #2d3748;
      margin-bottom: 20px;
      font-weight: 600;
      text-align: center;
    }

    .modal-content input,
    .modal-content button {
      width: 100%;
      padding: 12px;
      margin-bottom: 16px;
      border: 1px solid #e2e8f0;
      border-radius: 8px;
      font-size: 16px;
      transition: border-color 0.3s ease-in-out, box-shadow 0.3s;
    }

    .modal-content input:focus,
    .modal-content button:focus {
      outline: none;
      border-color: #3182ce;
      box-shadow: 0 0 6px rgba(72, 130, 195, 0.5);
    }

    .modal-content button {
      background-color: #3182ce;
      color: white;
      border: none;
      font-weight: 600;
      cursor: pointer;
      transition: background-color 0.3s;
    }

    .modal-content button:hover {
      background-color: #4299e1;
    }

    /* Close Button */
    .close-btn {
      position: absolute;
      top: 15px;
      right: 20px;
      font-size: 28px;
      color: #e2e8f0;
      background: none;
      border: none;
      cursor: pointer;
      transition: color 0.3s ease-in-out;
    }

    .close-btn:hover {
      color: #3182ce;
    }

    /* Label Styling */
    label {
      font-weight: 600;
      margin-bottom: 8px;
      display: inline-block;
      font-size: 14px;
      color: #4a5568;
    }

    /* Input Styling */
    input {
      background-color: #f9fafb;
      border: 1px solid #e2e8f0;
      border-radius: 8px;
      padding: 12px;
      font-size: 16px;
      transition: border-color 0.3s ease-in-out;
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
