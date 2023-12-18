<?php

use App\Http\Controllers\UserController;
use App\Http\Controllers\MeetingController;
use App\Models\Meeting;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

// Login for User
Route::get('/', [UserController::class, 'loadLogin']);
Route::get('/login', function () {
    return redirect('/');
});

Route::post('/login', [UserController::class, 'userLogin'])->name('userLogin');

// User Registration
Route::get('/register', [UserController::class, 'loadRegister']);
Route::post('/register', [UserController::class, 'userRegister'])->name('userRegister');

// Logout
Route::get('/logout', [UserController::class, 'logout']);

// Home Page
Route::get('/home', [UserController::class, 'home']);
Route::post('/home', [MeetingController::class, 'addMeeting'])->name('addMeeting');


// Get Meeting by date
Route::get('/get-meetings', [MeetingController::class, 'getDateMeetings'])->name('getDateMeetings');


// check1