@section('top_meta_tags')
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        .modal-dialog {
        max-width: 500px;
      }
  
      .modal-content {
        border-radius: 14px;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
      }
  
      .modal-header {
        background-color: #007bff;
        color: white;
        border-top-left-radius: 14px;
        border-top-right-radius: 14px;
        padding: 12px 20px;
      }
  
      .modal-title {
        font-size: 1rem;
        font-weight: 600;
      }
  
      .modal-body {
        padding: 16px 20px;
      }
  
      .form-label {
        font-size: 0.85rem;
        margin-bottom: 4px;
        color: #333;
      }
  
      .form-control {
        border: 1px solid #ddd;
        border-radius: 8px;
        font-size: 0.85rem;
        padding: 8px 10px;
        transition: all 0.2s ease-in-out;
        box-shadow: none;
      }
  
      .form-control:focus {
        border-color: #007bff;
        box-shadow: 0 0 0 2px rgba(0, 123, 255, 0.15);
      }
  
      .btn-primary {
        background-color: #007bff;
        border: none;
        font-size: 0.9rem;
        padding: 10px;
        border-radius: 8px;
        font-weight: 600;
        transition: background-color 0.2s ease;
      }
  
      .btn-primary:hover {
        background-color: #0056b3;
      }
  
      .btn-close {
        filter: invert(1);
      }
  
      .form-group {
        margin-bottom: 12px;
      }
    </style>
    @endsection
    
        <div class="flex justify-center items-center">
            <!-- Smart Google Meet Rectangle Button with Image -->
            <button onclick="toggleModal(true)" class="flex items-center px-8 py-3 bg-gradient-to-r from-green-400 to-blue-500 text-white rounded-lg shadow-lg hover:scale-105 transition-all duration-300 focus:outline-none transform hover:shadow-xl">
              
              <!-- Google Meet Logo (Image) -->
              <img src="https://www.gstatic.com/meet/google_meet_horizontal_wordmark_2020q4_2x_icon_124_40_292e71bcb52a56e2a9005164118f183b.png" alt="Google Meet Logo" class="w-32 h-12 mr-3" />
              
              <!-- Button Text -->
              <span class="font-semibold text-lg">Book a Meeting</span>
            </button>
          </div>
          
          <!-- Modal -->
          <div id="googleMeetModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50">
            <div class="bg-white w-full max-w-lg p-6 rounded-xl shadow-xl relative animate-fadeIn">
          
              <!-- Close Button -->
              <button onclick="toggleModal(false)" class="absolute top-3 right-4 text-gray-500 hover:text-gray-800 text-xl font-bold">&times;</button>
          
              <!-- Title -->
              <h2 class="text-xl font-semibold text-gray-800 text-center mb-5">Google Meet Smart Form</h2>
          
              <!-- Form -->
              <form class="grid grid-cols-1 gap-4 text-sm">
                <input type="text" hidden placeholder="User ID" class="w-full px-4 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 focus:bg-indigo-50 transition duration-200" />
          
                <input type="text" placeholder="Name" class="w-full px-4 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 focus:bg-indigo-50 transition duration-200" />
          
                <input type="text" placeholder="Location" class="w-full px-4 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 focus:bg-indigo-50 transition duration-200" />
          
                <div hidden class="grid grid-cols-2 gap-4">
                  <input type="text" placeholder="Latitude" class="w-full px-4 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 focus:bg-indigo-50 transition duration-200" />
          
                  <input type="text" placeholder="Longitude" class="w-full px-4 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 focus:bg-indigo-50 transition duration-200" />
                </div>
          
                <div class="grid grid-cols-2 gap-4">
                  <div class="">
                    <label for="meeting_time" class="block text-sm font-medium text-gray-700">Meeting Time</label>
                    <input id="meeting_time" type="time" placeholder="Meeting Time" class="w-full px-4 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 focus:bg-indigo-50 transition duration-200" />
                    
                  </div>
                    <div class="">
                        <label for="date" class="block text-sm font-medium text-gray-700">Meeting Date</label>
                    <input id="date" type="date" placeholder="Date" class="w-full px-4 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 focus:bg-indigo-50 transition duration-200" />
    
                    </div>
                  <input hidden type="text" placeholder="Travel Time" class="w-full px-4 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 focus:bg-indigo-50 transition duration-200" />
                </div>
          
          
                <button type="submit" class="w-full bg-indigo-600 text-white py-2 rounded-md hover:bg-indigo-700 transition">
                  Submit
                </button>
              </form>
            </div>
          </div>
    <!-- Modal Toggle Script -->
    <style>
        .animate-fadeIn {
          animation: fadeIn 0.25s ease-out;
        }
      
        @keyframes fadeIn {
          from {
            opacity: 0;
            transform: translateY(-12px);
          }
          to {
            opacity: 1;
            transform: translateY(0);
          }
        }
      </style>
      
      <!-- Script -->
      <script>
        function toggleModal(show) {
          const modal = document.getElementById('googleMeetModal');
          modal.classList.toggle('hidden', !show);
          modal.classList.toggle('flex', show);
        }
      </script>

