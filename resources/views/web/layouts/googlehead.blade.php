{{-- <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css"> --}}
  {{-- <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@500;600;700&display=swap" rel="stylesheet">
  <script src="https://cdn.jsdelivr.net/npm/vanillajs-modal@1.1.2/dist/vanilla.min.js"></script>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.8/css/intlTelInput.css">
  <script src="https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.8/js/intlTelInput.min.js"></script> --}}

  <style>
    /* Modal and Form Styling (same as before) */
    .modal__overlay { background-color: rgba(0, 0, 0, 0.5); position: fixed; top: 0; left: 0; right: 0; bottom: 0; display: flex; justify-content: center; align-items: center; z-index: 1000; }
    .modal__container { background: white; padding: 20px; border-radius: 8px; width: 850px; position: relative; }
    button.modal__close { position: absolute; top: 10px; right: 10px; background: none; border: none; font-size: 20px; cursor: pointer; }
    button[type="submit"], button#cancel-button { padding: 10px 20px; border: none; border-radius: 4px; cursor: pointer; }
    button[type="submit"] { background-color: #007bff; color: white; }
    button[type="submit"]:hover { background-color: #0056b3; }
    button#cancel-button { background-color: #dc3545; color: white; margin-left: 10px; }
    button#cancel-button:hover { background-color: #c82333; }

    .suggestions { border: 1px solid #ccc; max-height: 200px; overflow-y: auto; margin-top: 10px; }
    .suggestions div { padding: 10px; cursor: pointer; }
    .suggestions div:hover { background-color: #f0f0f0; }

    .calendar-card { background: #ffffff; border-radius: 20px; padding: 0px; width: 460px; box-shadow: 0 10px 40px rgba(0, 0, 0, 0.08); text-align: center; transition: 0.3s; animation: fadeIn 0.7s ease; }
    .calendar-card:hover { transform: translateY(-4px); box-shadow: 0 15px 45px rgba(0, 0, 0, 0.1); }
    @keyframes fadeIn { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }

    .flatpickr-calendar.inline { display: block; position: relative; background: transparent; border: none; }
    .flatpickr-months { background: rgb(239, 239, 239); border-radius: 5px 5px 0 0; color: white; }
    .flatpickr-weekdays { background: #eef2ff; }
    .flatpickr-day { border-radius: 10px; font-weight: 500; color: #1f2937; }
    .flatpickr-day.selected { background: #4f46e5 !important; color: white !important; }

    /* ✅ Message box styling */
    #form-message {
      display: none;
      margin-bottom: 15px;
      padding: 10px;
      border-radius: 6px;
      font-weight: bold;
      text-align: center;
    }
    .success-message { background-color: #d1fae5; color: #065f46; }
    .error-message { background-color: #fee2e2; color: #991b1b; }

.floating-label-group {
  position: relative;
  margin-bottom: 20px;
}

.floating-label-group input,
.floating-label-group input[type="time"],
.floating-label-group input[type="tel"],
.floating-label-group input[type="email"] {
  width: 100%;
  padding: 12px 12px 12px 12px;
  font-size: 16px;
  border: 1px solid #ccc;
  border-radius: 8px;
  outline: none;
  transition: 0.3s ease;
}

.floating-label-group input:focus {
  border-color: #4f46e5;
  box-shadow: 0 0 0 3px rgba(79, 70, 229, 0.1);
}

.floating-label-group label {
  position: absolute;
  top: 12px;
  left: 14px;
  color: #999;
  font-size: 16px;
  pointer-events: none;
  transition: 0.2s ease all;
  background: white;
  padding: 0 4px;
}

.floating-label-group input:focus + label,
.floating-label-group input:not(:placeholder-shown) + label {
  top: -10px;
  left: 10px;
  font-size: 12px;
  color: #4f46e5;
}
/* Fix the wrapper that intl-tel-input creates */
.iti {
  width: 100% !important;
  display: block !important;
}

/* Ensure the input itself is full width */
.iti input {
  width: 100% !important;
  box-sizing: border-box;
}



.google-meet-button {
      display: flex;
      align-items: center;
      justify-content: center;
      background: linear-gradient(90deg, #48bb78, #4299e1);
      color: white;
      padding: 12px 30px;
      border-radius: 10px !important;
      box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
      cursor: pointer;
      transition: transform 0.3s, background-color 0.3s;
      border: none;
      font-weight: 600;
      /* margin-top: 30px; */
    }

    .google-meet-button:hover {
      transform: scale(1.05);
      background-color: #3182ce;
    }

    .google-meet-button img {
      width: 140px;
      height: auto;
      margin-right: 15px;
    }
    .logo-container {
      position: relative;
      width: 124px;
      height: 40px;
      display: inline-block;
      overflow: hidden;
    }
  </style>