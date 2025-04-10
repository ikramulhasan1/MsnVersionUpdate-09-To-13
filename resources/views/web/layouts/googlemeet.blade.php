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
      background-color: #f7fafc;
      padding-top: 60px; /* Space for fixed navbar */
      text-align: center;
    }

    /* Smart Google Meet Button */
    .google-meet-button {
      display: flex;
      align-items: center;
      justify-content: center;
      background: linear-gradient(90deg, #48bb78, #4299e1);
      color: white;
      padding: 12px 24px;
      border-radius: 40px;
      box-shadow: 0 4px 8px rgba(0, 0, 0, 0.15);
      cursor: pointer;
      transition: transform 0.3s, background-color 0.3s;
      border: none;
    }

    .google-meet-button:hover {
      transform: scale(1.05);
      background-color: #3182ce;
    }

    .google-meet-button img {
      width: 35px;
      height: auto;
      margin-right: 10px;
    }

    .google-meet-button span {
      font-weight: 600;
      font-size: 16px;
    }

    /* Modal */
    #googleMeetModal {
      position: fixed;
      top: 0;
      left: 0;
      width: 100%;
      height: 100%;
      background-color: rgba(0, 0, 0, 0.5);
      display: none;
      justify-content: center;
      align-items: center;
      z-index: 9999;
    }

    .modal-content {
      background-color: white;
      width: 90%;
      max-width: 450px;
      padding: 20px;
      border-radius: 16px;
      box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
      animation: fadeIn 0.2s ease-out;
    }

    @keyframes fadeIn {
      from {
        opacity: 0;
        transform: translateY(-20px);
      }
      to {
        opacity: 1;
        transform: translateY(0);
      }
    }

    .modal-content h2 {
      font-size: 22px;
      color: #2d3748;
      margin-bottom: 20px;
      font-weight: 700;
    }

    .modal-content input,
    .modal-content button {
      width: 100%;
      padding: 12px;
      margin-bottom: 12px;
      border: 1px solid #e2e8f0;
      border-radius: 8px;
      background-color: #f9fafb;
      font-size: 16px;
      transition: border-color 0.3s, box-shadow 0.3s;
    }

    .modal-content input:focus,
    .modal-content button:focus {
      outline: none;
      border-color: #3182ce;
      box-shadow: 0 0 6px rgba(72, 130, 195, 0.4);
    }

    .modal-content button {
      background-color: #3182ce;
      color: white;
      border: none;
      font-weight: 600;
      cursor: pointer;
    }

    .modal-content button:hover {
      background-color: #4299e1;
    }

    /* Close Button */
    .close-btn {
      position: absolute;
      top: 10px;
      right: 10px;
      font-size: 24px;
      color: #e2e8f0;
      background: none;
      border: none;
      cursor: pointer;
    }

    .close-btn:hover {
      color: #3182ce;
    }

  </style>
</head>
<body>

  <!-- Smart Google Meet Button -->
  <button onclick="toggleModal(true)" class="google-meet-button">
    <img src="https://www.gstatic.com/meet/google_meet_horizontal_wordmark_2020q4_2x_icon_124_40_292e71bcb52a56e2a9005164118f183b.png" alt="Google Meet Logo" />
    <span>Book a Meeting</span>
  </button>

  <!-- Modal -->
  <div id="googleMeetModal">
    <div class="modal-content">
      <!-- Close Button -->
      <button onclick="toggleModal(false)" class="close-btn">&times;</button>

      <h2>Schedule Your Meeting</h2>

      <!-- Form -->
      <form>
        <input type="text" hidden placeholder="User ID" />
        <input type="text" placeholder="Your Name" />
        <input type="text" placeholder="Location" />
        
        <div>
          <label for="meeting_time">Time</label>
          <input id="meeting_time" type="time" placeholder="Time" />
        </div>

        <div>
          <label for="date">Date</label>
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
