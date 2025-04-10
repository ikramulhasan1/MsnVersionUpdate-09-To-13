
  <style>
    /* Smart Google Meet Button */
    .google-meet-button {
      display: flex;
      align-items: center;
      background: linear-gradient(90deg, #48bb78, #4299e1);
      color: white;
      padding: 12px 24px;
      border-radius: 12px;
      box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
      cursor: pointer;
      transition: transform 0.3s, box-shadow 0.3s;
    }

    .google-meet-button:hover {
      transform: scale(1.05);
      box-shadow: 0 6px 12px rgba(0, 0, 0, 0.2);
    }

    .google-meet-button img {
      width: 50px;
      height: auto;
      margin-right: 12px;
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
      width: 100%;
      max-width: 600px;
      padding: 32px;
      border-radius: 12px;
      box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
      animation: fadeIn 0.25s ease-out;
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
      text-align: center;
      font-size: 24px;
      color: #2d3748;
      margin-bottom: 20px;
    }

    .modal-content input,
    .modal-content button {
      width: 100%;
      padding: 12px;
      margin-bottom: 12px;
      border: 1px solid #e2e8f0;
      border-radius: 8px;
      box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
      font-size: 16px;
    }

    .modal-content button:hover {
      background-color: #3182ce;
      color: white;
      cursor: pointer;
    }

    /* Close Button */
    .close-btn {
      position: absolute;
      top: 12px;
      right: 16px;
      font-size: 24px;
      color: #e2e8f0;
      background: none;
      border: none;
      cursor: pointer;
    }

    .close-btn:hover {
      color: #3182ce;
    }

    /* Input focus styles */
    input:focus {
      outline: none;
      border-color: #3182ce;
      box-shadow: 0 0 5px rgba(72, 130, 195, 0.5);
    }

    /* Button styles */
    button[type="submit"] {
      background-color: #3182ce;
      color: white;
      border: none;
      cursor: pointer;
      font-size: 16px;
      transition: background-color 0.3s;
    }

    button[type="submit"]:hover {
      background-color: #4299e1;
    }
  </style>

  <!-- Smart Google Meet Button -->
  <div class="flex justify-center items-center mt-24">
    <button onclick="toggleModal(true)" class="google-meet-button">
      <img src="https://www.gstatic.com/meet/google_meet_horizontal_wordmark_2020q4_2x_icon_124_40_292e71bcb52a56e2a9005164118f183b.png" alt="Google Meet Logo" />
      <span class="font-semibold text-lg">Book a Meeting</span>
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
