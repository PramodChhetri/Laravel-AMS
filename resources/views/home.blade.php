@extends('layout')

@section('main-section')

<style>
    .btn-logout {
        font-weight: bold;
        border-radius: 20px;
        padding: 10px 40px;
        transition: background-color 0.3s, color 0.3s;
    }

    .btn-logout:hover {
        background-color: #dc3545; /* Change to your preferred hover background color */
        color: #fff; /* Change to your preferred hover text color */
    }

    .form-control {
            border: none;
            border-bottom: 2px solid #3498db;
            border-radius: 0;
            background: transparent;
            color: #333;
        }
        .form-control:focus {
            box-shadow: none;
            border-color: #3498db;
        }
</style>

<style>
    .btn-logout {
        font-weight: bold;
        border-radius: 20px;
        transition: background-color 0.3s, color 0.3s;
    }

    .btn-logout:hover {
        background-color: #dc3545; /* Change to your preferred hover background color */
        color: #fff; /* Change to your preferred hover text color */
    }

    .btn-submit{
        font-weight: bold;
        border-radius: 20px;
        transition: background-color 0.3s, color 0.3s;
    }


    .btn-submit:hover{
        background-color: white;
        color: black;
    }

</style>

<nav class="navbar navbar-expand-lg navbar-dark bg-dark" style="padding-inline: 5rem; margin-bottom:5rem;">
    <a class="navbar-brand " href="#" style="font-size: 24px; font-weight: bold; color: #fff;">Appointment Manager</a>
    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav"
        aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse justify-content-end" id="navbarNav">
        <ul class="navbar-nav ml-4 mr-4">
            <!-- Add other navbar items if needed -->
            <li class="nav-item" style="margin: left">
                <a class="nav-link btn btn-outline-light btn-logout" style="padding: 10px 50px;" href="/logout">
                    Logout
                </a>
            </li>
        </ul>
    </div>
</nav>




<div class="container-lg">
    @if (session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    @if (session('error'))
    <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    @if (count($errors) > 0)
    <div class="alert alert-danger">
        <ul>
            @foreach ($errors->all() as $error)
            <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    {{-- <h1 class="text text-primary mt-4 mb-4">Appointment Management System</h1> --}}

    <form action="{{ route('addMeeting') }}" method="POST" class="mb-4">
        @csrf

        <div class="row">
            <div class="col-sm-3">
                <label for="location" class="form-label">Location</label>
                <input type="text" name="location" id="location" class="form-control" placeholder="Select Location" required>
            </div>
            <div class="col-sm-3">
                <label for="name" class="form-label">Client Name</label>
                <input type="text" name="name" id="name" class="form-control" placeholder="Client Name" required>
            </div>
            <div class="col-sm-3">
                <label for="time" class="form-label">Meeting Time Duration</label>
                <input type="number" name="meeting_time" class="form-control" placeholder="(In Minutes)" required>
                <span class="text-secondary">Available (09:00 to 18:00) </span>
            </div>
            <div class="col-sm-3">
                <label for="date" class="form-label">Date</label>
                <input type="date" name="date" class="form-control" required>
            </div>
        </div>

        <div class="row">
            <div class="col-sm-3">
                <input type="text" id="longitude" name="longitude" class="form-control" placeholder="Longitude" readonly>
                <br>
                <input type="text" id="latitude" name="latitude" class="form-control" placeholder="Latitude" readonly>
                <br>
                <input type="text" id="ip" name="ip" class="form-control" placeholder="IP" readonly>
                <br>
                <input type="text" id="city" name="city" class="form-control" placeholder="City" readonly>
                <input type="hidden" id="cityLat" name="cityLat" value="">
                <input type="hidden" id="cityLon" name="cityLon" value="">
                <br>
                <input type="number" id="dtime" name="distance_time" class="form-control" placeholder="Distance Time" readonly>
                <br>
                <input type="number" id="dkm" name="distance_km" class="form-control" placeholder="Distance (km)" readonly>
            </div>
        </div>

        <div class="row mt-3">
            <div class="col-sm">
                <button type="submit" class="btn btn-dark btn-submit" style="padding-inline: 2rem;">Submit</button>
            </div>
        </div>
    </form>
</div>

<script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>

<script>
    jQuery(document).ready(function ($) {
        // Autocomplete for location field
        var to = 'location';
        $("#" + to).autocomplete({
            source: function (request, response) {
                $.ajax({
                    url: "https://nominatim.openstreetmap.org/search",
                    dataType: "json",
                    data: {
                        q: request.term,
                        format: "json",
                        limit: 5
                    },
                    success: function (data) {
                        response($.map(data, function (item) {
                            return {
                                label: item.display_name,
                                value: item.display_name,
                                latitude: item.lat,
                                longitude: item.lon
                            };
                        }));
                    }
                });
            },
            minLength: 1, // Minimum characters before autocomplete starts
            select: function (event, ui) {
                // Set latitude and longitude when a location is selected
                $("#latitude").val(ui.item.latitude);
                $("#longitude").val(ui.item.longitude);

                $.getJSON("https://api.ipify.org/?format=json", function (data) {
                    let ip = data.ip;
                    jQuery("#ip").val(ip);
                    getCity(ip);
                });
            }
        });

        // Event listener for latitude and longitude fields
        $("#latitude, #longitude").on("input", function () {
            // Get latitude and longitude values
            var latitude = $("#latitude").val();
            var longitude = $("#longitude").val();

            // Reverse geocode the coordinates
            $.ajax({
                url: "https://nominatim.openstreetmap.org/reverse",
                dataType: "json",
                data: {
                    lat: latitude,
                    lon: longitude,
                    format: "json"
                },
                success: function (data) {
                    // Update the location field with the reverse geocoded address
                    $("#location").val(data.display_name);
                    calculateDistance(); // Trigger distance calculation when location is updated
                }
            });
        });
    });

    function getCity(ip) {
        var req = new XMLHttpRequest();
        req.open("GET", "https://ipapi.co/" + ip + "/json/", true);
        req.send();

        req.onreadystatechange = function () {
            if (req.readyState == 4 && req.status == 200) {
                var obj = JSON.parse(req.responseText);
                console.log(obj);

                // Check if the API response contains valid latitude and longitude values
                if (obj.latitude && obj.longitude) {
                    jQuery("#city").val(obj.city);
                    jQuery("#cityLat").val(obj.latitude);
                    jQuery("#cityLon").val(obj.longitude);

                    calculateDistance();
                } else {
                    console.log("Invalid latitude and longitude values in the API response.");
                }
            }
        };
    }

    function calculateDistance() {
        var locationLat = parseFloat($("#latitude").val());
        var locationLon = parseFloat($("#longitude").val());
        var cityLat = parseFloat($("#cityLat").val());
        var cityLon = parseFloat($("#cityLon").val());

        console.log("Location Lat/Lon:", locationLat, locationLon);
        console.log("City Lat/Lon:", cityLat, cityLon);

        if (!isNaN(locationLat) && !isNaN(locationLon) && !isNaN(cityLat) && !isNaN(cityLon)) {
            var R = 6371; // Radius of the Earth in kilometers
            var dLat = deg2rad(cityLat - locationLat);
            var dLon = deg2rad(cityLon - locationLon);

            var a =
                Math.sin(dLat / 2) * Math.sin(dLat / 2) +
                Math.cos(deg2rad(locationLat)) * Math.cos(deg2rad(cityLat)) *
                Math.sin(dLon / 2) * Math.sin(dLon / 2);

            var c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1 - a));
            var distance = R * c; // Distance in kilometers

            console.log("Calculated Distance:", distance);

            // Convert distance to integer
            var distanceInteger = parseInt(distance.toFixed(0), 10);
            $("#dkm").val(distanceInteger);

            calculateDistanceTime();
        } else {
            console.log("Invalid values for distance calculation.");
        }
    }

    function deg2rad(deg) {
        return deg * (Math.PI / 180);
    }

    function calculateDistanceTime() {
        var distance = parseFloat($("#dkm").val());
        var speed = 50; // Assume a default speed (you should replace this with the actual speed)

        if (!isNaN(distance)) {
            var time = distance / speed; // Time in hours

            // Convert time to minutes
            var timeInMinutes = Math.round(time * 60); // Rounded to the nearest integer

            console.log("Calculated Time:", timeInMinutes);

            $("#dtime").val(timeInMinutes);
        } else {
            console.log("Invalid values for distance time calculation.");
        }
    }
</script>

@endsection
