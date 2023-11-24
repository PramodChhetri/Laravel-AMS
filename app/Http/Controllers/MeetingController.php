<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Meeting;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class MeetingController extends Controller
{
    public function addMeeting(Request $request)
    {
        // Validate the incoming request data
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'location' => 'required|string',
            'longitude' => 'required|string',
            'latitude' => 'required|string',
            'meeting_time' => 'required|integer',
            'distance_time' => 'required|integer',
            'distance_km' => 'required|integer',
            'date' => 'required|date',
        ]);
        $validatedData['user_id'] = Auth::id();

        $minutes = Meeting::select(DB::raw("SUM(meeting_time) as total_time"))->where(['user_id' => $validatedData['user_id'], 'date' => $validatedData['date']])->get();

        $currentUserMinutes = 9 * 60;

        if ($minutes[0]['total_time'] >= $currentUserMinutes) {
            return back()->with('error', 'On this date ' . $validatedData['date'] . 'all schedules are busy. Please select new date.');
        } elseif ($minutes[0]['total_time'] + $validatedData['meeting_time'] > $currentUserMinutes) {
            return back()->with('error', 'On this date ' . $validatedData['date'] . ' you have only ' . $currentUserMinutes - $minutes[0]['total_time'] . ' minutes.');
        } else {
            Meeting::create($validatedData);
            return back()->with('success', 'Meeting Schedule with client Successfully added.');
        }
    }
}
