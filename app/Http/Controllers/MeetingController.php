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


    public function getDateMeetings(Request $request)
    {
        $userCon = new UserController;

        $tableData = Meeting::where('date', $request->date)->get();


        // Use cURL to make a GET request to ipify API
        $ch = curl_init('https://api.ipify.org/?format=json');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $ipifyResponse = curl_exec($ch);
        curl_close($ch);

        // Decode the JSON response
        $ipifyData = json_decode($ipifyResponse);

        // Check if the request to ipify was successful
        if ($ipifyData && $ipifyData->ip) {
            // Use cURL to make a GET request to ip-api.com API
            $ch = curl_init("http://ip-api.com/php/{$ipifyData->ip}");
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            $ipApiResponse = curl_exec($ch);
            curl_close($ch);

            // Unserialize the response from ip-api.com
            $uInfo = unserialize($ipApiResponse);

            // Check if the request to ip-api.com was successful
            if ($uInfo && $uInfo['status'] === 'success') {




                $lat = $uInfo['lat'];
                $long = $uInfo['lon'];

                $meetings = [];

                foreach ($tableData as $data) {
                    $km = $userCon->calculateDistance($lat, $long, $data->latitude, $data->longitude);
                    $data['current_km'] = ceil($km);
                    $meetings[] = $data;
                }


                // Sorting based of current_km
                $key = array_column($meetings, 'current_km');
                array_multisort($key, SORT_ASC, $meetings);

                return response()->json(['meetings' => $meetings]);
            } else {
                // Handle the case where ip-api.com request fails
                return redirect()->back()->with('error', 'Failed to retrieve user information.');
            }
        } else {
            // Handle the case where ipify request fails
            return redirect()->back()->with('error', 'Failed to retrieve user IP address.');
        }
    }

    public function calculateDistance($userLat, $userLon, $clientLat, $clientLon)
    {
        $earthRadius = 6371; // Radius of the Earth in kilometers

        $latDifference = deg2rad($clientLat - $userLat);
        $lonDifference = deg2rad($clientLon - $userLon);

        $a = sin($latDifference / 2) * sin($latDifference / 2) +
            cos(deg2rad($userLat)) * cos(deg2rad($clientLat)) *
            sin($lonDifference / 2) * sin($lonDifference / 2);

        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

        $distance = $earthRadius * $c;

        return $distance;
    }
}
