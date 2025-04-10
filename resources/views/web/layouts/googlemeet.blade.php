
  <style>


    /* Smart Google Meet Button */
    .google-meet-button {
      display: flex;
      align-items: center;
      justify-content: center;
      background: linear-gradient(90deg, #48bb78, #4299e1);
      color: white;
      padding: 12px 30px;
      border-radius: 30px;
      box-shadow: 0 6px 12px rgba(0, 0, 0, 0.1);
      cursor: pointer;
      transition: transform 0.3s, background-color 0.3s;
      border: none;
      font-weight: bold;
    }

    .google-meet-button:hover {
      transform: scale(1.05);
      background-color: #3182ce;
    }

    .google-meet-button img {
      width: 40px;
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
      background-color: rgba(0, 0, 0, 0.6);
      display: none;
      justify-content: center;
      align-items: center;
      z-index: 9999;
    }

    .modal-content {
      background-color: #fff;
      width: 100%;
      max-width: 480px;
      padding: 30px;
      border-radius: 20px;
      box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
      animation: fadeIn 0.3s ease-out;
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
      font-size: 24px;
      font-weight: 600;
      color: #2d3748;
      margin-bottom: 25px;
      text-align: center;
    }

    /* Form Input Fields */
    .modal-content input,
    .modal-content button {
      width: 100%;
      padding: 14px 16px;
      margin-bottom: 15px;
      border: 2px solid #ddd;
      border-radius: 8px;
      font-size: 16px;
      background-color: #f7fafc;
      transition: border-color 0.3s, box-shadow 0.3s;
    }

    .modal-content input:focus,
    .modal-content button:focus {
      border-color: #3182ce;
      box-shadow: 0 0 8px rgba(72, 130, 195, 0.4);
      outline: none;
    }

    .modal-content input {
      background-color: #ffffff;
    }

    .modal-content button {
      background-color: #3182ce;
      color: white;
      border: none;
      font-weight: bold;
      cursor: pointer;
    }

    .modal-content button:hover {
      background-color: #4299e1;
    }

    /* Close Button */
    .close-btn {
      position: absolute;
      top: 15px;
      right: 20px;
      font-size: 24px;
      color: #e2e8f0;
      background: none;
      border: none;
      cursor: pointer;
    }

    .close-btn:hover {
      color: #3182ce;
    }

    /* Input focus styling */
    input:focus {
      outline: none;
      border-color: #3182ce;
      box-shadow: 0 0 6px rgba(72, 130, 195, 0.3);
    }

  </style>

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

      <h2>Schedule Your Meeting</h2>

      <!-- Form -->
      <form>
        <input type="text" hidden placeholder="User ID" />

        <input type="text" placeholder="Your Name" />

        <input type="text" placeholder="Location" />
        
        <div>
          <label for="meeting_time" style="font-weight: 500; color: #2d3748;">Meeting Time</label>
          <input id="meeting_time" type="time" placeholder="Time" />
        </div>

        <div>
          <label for="date" style="font-weight: 500; color: #2d3748;">Meeting Date</label>
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


