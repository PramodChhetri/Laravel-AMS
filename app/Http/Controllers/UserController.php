<?php

namespace App\Http\Controllers;

use App\Models\Meeting;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function loadRegister()
    {
        if (Auth::check()) {
            return redirect('/home');
        } else {
            return view('register');
        };
    }

    public function userRegister(Request $request)
    {
        $request->validate([
            'name' => 'string|required|min:1',
            'email' => 'string|required|email|max:100|unique:users',
            'password' => 'string|required|min:6|confirmed',
        ]);

        $user = new User();
        $user->name = $request->name;
        $user->email = $request->email;
        $user->password = Hash::make($request->password);
        $user->save();

        return back()->with('success', 'Your Registration has been successful.');
    }

    public function loadLogin()
    {
        if (Auth::check()) {
            return redirect('/home');
        } else {
            return view('login');
        }
    }

    public function userLogin(Request $request)
    {
        $request->validate([
            'email' => 'string|required|email',
            'password' => 'string|required',
        ]);

        $userCredential = $request->only('email', 'password');

        if (Auth::attempt($userCredential)) {
            return redirect('/home');
        } else {
            return back()->with('error', 'Username & Password are Incorrect!');
        }

        return back()->with('success', 'Your Registration has been successful.');
    }
    public function home()
    {
        if (Auth::check()) {
            $tableData = Meeting::where('user_id', Auth::id())->get();

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
                        $km = $this->calculateDistance($lat, $long, $data->latitude, $data->longitude);
                        $data['current_km'] = ceil($km);
                        $meetings[] = $data;
                    }

                    // Sorting based of current_km
                    $key = array_column($meetings, 'current_km');
                    array_multisort($key, SORT_ASC, $meetings);

                    return view('home', compact('meetings'));
                } else {
                    // Handle the case where ip-api.com request fails
                    return redirect()->back()->with('error', 'Failed to retrieve user information.');
                }
            } else {
                // Handle the case where ipify request fails
                return redirect()->back()->with('error', 'Failed to retrieve user IP address.');
            }
        } else {
            return redirect('/');
        }
    }

    public function logout(Request $request)
    {
        $request->session()->flush();
        Auth::logout();
        return redirect('/');
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
