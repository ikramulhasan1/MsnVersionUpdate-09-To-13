<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Meeting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Log;

class MeetingController extends Controller
{
    public function index()
    {
        // return view(view: 'welcome');
    }

    public function create()
    {
        //
    }

    public function store(Request $request)
    {
        // Validate the incoming data first (avoids wasting a reCAPTCHA API call on bad input)
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email',
            'phone' => 'required|string|max:20',
            'location' => 'required|string',
            'latitude' => 'required|string',
            'longitude' => 'required|string',
            'meeting_time' => 'required',
            'date' => 'required|date',
            'city' => 'nullable|string',
            'ip' => 'nullable|string',
            'distance_km' => 'nullable|string',
            'distance_time' => 'nullable|string',
            'g-recaptcha-response' => 'required',
        ]);

        // ✅ Verify reCAPTCHA
        try {
            $verify = Http::asForm()
                ->timeout(10)
                ->post('https://www.google.com/recaptcha/api/siteverify', [
                    'secret' => env('RECAPTCHA_SECRET_KEY'),
                    'response' => $validated['g-recaptcha-response'],
                    'remoteip' => $request->ip(),
                ]);

            $responseBody = $verify->json();
        } catch (\Exception $e) {
            Log::error('reCAPTCHA request failed: '.$e->getMessage());

            return response()->json([
                'message' => 'Could not verify reCAPTCHA. Please try again.',
            ], 500);
        }

        if (empty($responseBody['success'])) {
            return response()->json([
                'message' => 'reCAPTCHA verification failed. Please try again.',
            ], 422);
        }

        // g-recaptcha-response was only needed for verification, not for the DB
        unset($validated['g-recaptcha-response']);

        // Log the validated data
        Log::info($validated);

        // Check for duplicate meeting
        $exists = Meeting::where('email', $validated['email'])
            ->where('date', $validated['date'])
            ->where('meeting_time', $validated['meeting_time'])
            ->exists();

        if ($exists) {
            return response()->json([
                'message' => 'Already have a meeting booked at this date and time.',
            ], 409); // 409 Conflict
        }

        // Normalize time format
        $validated['meeting_time'] = date('H:i:s', strtotime($validated['meeting_time']));

        // Save the meeting
        $meeting = Meeting::create($validated);

        return response()->json([
            'message' => 'Meeting successfully booked!',
            'meeting' => $meeting,
        ]);
    }

    public function show(Meeting $meeting)
    {
        //
    }

    public function edit(Meeting $meeting)
    {
        //
    }

    public function update(Request $request, Meeting $meeting)
    {
        //
    }

    public function destroy(Meeting $meeting)
    {
        //
    }
}
