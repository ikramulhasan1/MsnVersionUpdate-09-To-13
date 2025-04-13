<?php

namespace App\Http\Controllers;

use Log;
use App\Models\Meeting;
use Illuminate\Http\Request;

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
         // Validate the incoming data
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
        ]);
    
        // Log the validated data
        Log::info($validated);  

        // Check for duplicate meeting
        $exists = Meeting::where('email', $request->email)
            ->where('date', $request->date)
            ->where('meeting_time', $request->meeting_time)
            ->exists();

        if ($exists) {
            return response()->json([
                'message' => 'Already have a meeting booked at this date and time.',
            ], 409); // 409 Conflict
        }

        // Save the meeting
        $meeting = Meeting::create($request->all());

        return response()->json([
            'message' => 'Meeting successfully booked!',
            'meeting' => $meeting
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
