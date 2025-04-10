<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Google Meet Smart Form</title>

  <!-- Semantic UI CSS -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/semantic-ui/2.4.1/semantic.min.css">

  <!-- Google Fonts (Optional) -->
  <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;600&display=swap" rel="stylesheet">

  <style>
    body {
      font-family: 'Roboto', sans-serif;
      padding-top: 60px;
      background-color: #f4f7fa;
    }

    .google-meet-button {
      display: flex;
      align-items: center;
      justify-content: center;
      background: linear-gradient(90deg, #48bb78, #4299e1);
      color: white;
      padding: 12px 30px;
      border-radius: 30px;
      box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
      cursor: pointer;
      transition: transform 0.3s, background-color 0.3s;
      border: none;
      font-weight: 600;
      margin-top: 30px;
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

    .ui.modal .header {
      text-align: center;
      font-size: 24px;
    }

    .ui.modal .content {
      padding: 30px;
    }

    .ui.input input,
    .ui.button {
      width: 100%;
      padding: 12px;
      margin-bottom: 12px;
      font-size: 16px;
    }

    .ui.button {
      background-color: #3182ce;
      color: white;
      border: none;
      font-weight: bold;
      cursor: pointer;
      transition: background-color 0.3s;
    }

    .ui.button:hover {
      background-color: #4299e1;
    }
  </style>
</head>
<body>

  <!-- Google Meet Button -->
  <div class="ui center aligned container">
    <button class="google-meet-button" onclick="toggleModal()">
      <img src="https://www.gstatic.com/meet/google_meet_horizontal_wordmark_2020q4_2x_icon_124_40_292e71bcb52a56e2a9005164118f183b.png" alt="Google Meet Logo" />
      <span>Book a Meeting</span>
    </button>
  </div>

  <!-- Modal -->
  <div class="ui modal" id="googleMeetModal">
    <div class="header">Schedule Your Meeting</div>
    <div class="content">
      <!-- Form -->
      <form class="ui form">
        <input type="text" hidden placeholder="User ID" />

        <div class="field">
          <label for="name">Your Name</label>
          <div class="ui input">
            <input type="text" placeholder="Your Name" id="name" />
          </div>
        </div>

        <div class="field">
          <label for="location">Location</label>
          <div class="ui input">
            <input type="text" placeholder="Location" id="location" />
          </div>
        </div>

        <div class="field">
          <label for="meeting_time">Meeting Time</label>
          <div class="ui input">
            <input id="meeting_time" type="time" placeholder="Meeting Time" />
          </div>
        </div>

        <div class="field">
          <label for="date">Meeting Date</label>
          <div class="ui input">
            <input id="date" type="date" placeholder="Date" />
          </div>
        </div>

        <button class="ui button primary" type="submit">Submit</button>
      </form>
    </div>
  </div>

  <!-- Semantic UI and jQuery CDN -->
  <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/semantic-ui/2.4.1/semantic.min.js"></script>

  <script>
    function toggleModal() {
      $('#googleMeetModal').modal('toggle');
    }
  </script>

</body>
</html>
