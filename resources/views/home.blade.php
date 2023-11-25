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

    .btn-logout {
        font-weight: bold;
        border-radius: 20px;
        transition: background-color 0.3s, color 0.3s;
    }

    .btn-logout:hover {
        background-color: #dc3545; /* Change to your preferred hover background color */
        color: #fff; /* Change to your preferred hover text color */
    }

    .btn-submit {
        font-weight: bold;
        border-radius: 20px;
        transition: background-color 0.3s, color 0.3s;
    }

    .btn-submit:hover {
        background-color: white;
        color: black;
    }

    tr {
        cursor: pointer;
    }



    .meetingsHeader {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 20px;
    margin-top: 40px
    }

    .meetingsHeader h2 {
        font-size: 24px;
        font-weight: bold;
    }

    .filter-container {
        display: flex;
        align-items: center;
    }

    .filter-container label {
        margin-right: 10px;
        font-weight: bold;
        color: #555; 
    }

    .filter-container input {
        padding: 8px;
        border: 2px solid #3498db;
        width: 150px;
        font-size: 14px; 
        color: #333; 

    .filter-container input:hover,
    .filter-container input:focus {
        border-color: #207bd7; 

    /* Add transition effect for smoother hover/focus */
    .filter-container input {
        transition: border-color 0.3s;
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
                <label for="location" class="form-label">Client Location</label>
                <input type="text" name="location" id="location" class="form-control" placeholder="Select Location"
                    required>
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
                <input type="text" id="longitude" name="longitude" class="form-control" placeholder="Longitude">
                <br>
                <input type="text" id="latitude" name="latitude" class="form-control" placeholder="Latitude">
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
        
            <div id="mapForForm" class="col-sm-9" style="height: 350px;"></div> 
        </div>

        <div class="row mt-3">
            <div class="col-sm">
                <button type="submit" class="btn btn-dark btn-submit" style="padding-inline: 2rem;">Submit</button>
            </div>
        </div>
    </form>
</div>

<div class="container">
    <div class="meetingsHeader">
        <h2>Meetings</h2>
        <div class="filter-container">
            <label for="date">Filter By Date</label>
            <input type="date" name="date" id="date" class="form-control" required>
        </div>
    </div>


    <table class="table table-striped table-bordered table-hover">
        <thead class="thead-dark">
            <tr>
                <th>S.N</th>
                <th>Name</th>
                <th>Client Location</th>
                <th>Latitude</th>
                <th>Longitude</th>
                <th>Meeting Time</th>
                <th>Distance Time</th>
                <th>Distance In Km</th>
                <th>Current Distance In Km</th>
                <th>Date</th>
            </tr>
        </thead>
        <tbody class="tbody">
            @if (count($meetings) > 0)
                @foreach ($meetings as $meeting)
                    <tr>
                        <td>{{$meeting->id}}</td>
                        <td>{{$meeting->name}}</td>
                        <td>{{$meeting->location}}</td>
                        <td>{{$meeting->latitude}}</td>
                        <td>{{$meeting->longitude}}</td>
                        <td>{{$meeting->meeting_time}}</td>
                        <td>{{$meeting->distance_time}}</td>
                        <td>{{$meeting->distance_km}}</td>
                        <td>{{$meeting->current_km}}</td>
                        <td>{{$meeting->date}}</td>
                    </tr>
                @endforeach
            @else
                <tr>
                    <td colspan="9" class="text-center">No Meeting Today</td>
                </tr>
            @endif
        </tbody>
    </table>
</div>


{{-- Google Map HTML --}}
<div class="container mb-5">
    <div id="map" style="width: 100%; height: 600px;"></div>
</div>

<script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
<script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>

<script>
    // Map Code
    var map; // Declare map globally
    var formMap; // Declare map globally

    function initMap() {
        map = L.map('map').setView([0, 0], 10);
        formMap= L.map('mapForForm').setView([0, 0], 10);

        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '© OpenStreetMap contributors'
        }).addTo(map);

        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '© OpenStreetMap contributors'
        }).addTo(formMap);
    }

    function showMap(lat, long) {
        map.setView([lat, long], 15);
        L.marker([lat, long]).addTo(map);
    }

    function updateFormLocation(lat, lon) {
    $("#latitude").val(lat);
    $("#longitude").val(lon);

    // Reverse geocode the coordinates
    $.ajax({
        url: "https://nominatim.openstreetmap.org/reverse",
        dataType: "json",
        data: {
            lat: lat,
            lon: lon,
            format: "json"
        },
        success: function (data) {
            // Update the location field with the reverse geocoded address
            $("#location").val(data.display_name);
            calculateDistance(); // Recalculate distance when location is updated
        }
    });
}

        function showMapForForm(lat, long) {
    console.log("showMapForForm called with lat:", lat, "long:", long);

    // Remove existing marker
    formMap.eachLayer(function (layer) {
        if (layer instanceof L.Marker) {
            formMap.removeLayer(layer);
        }
    });

    formMap.setView([lat, long], 15);

    // Add new marker with dragend event listener
    var marker = L.marker([lat, long], { draggable: true }).addTo(formMap);
    marker.on('dragend', function (event) {
        var markerPosition = event.target.getLatLng();
        updateFormLocation(markerPosition.lat, markerPosition.lng);
    });
}

    jQuery(document).ready(function ($) {
        initMap(); // Initialize the map when the document is ready



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

        // Get Meeting By date
        $("#date").change(function () {
    var date = $(this).val();

    $.ajax({
        url: "{{route('getDateMeetings')}}",
        type: "GET",
        data: {'date': date}, // Fix the typo here
        success: function (data) {
            var html = '';
            var meetings = data.meetings;
            console.log(meetings);
            if (meetings.length > 0) {
                for (let i = 0; i < meetings.length; i++) {
                    html += `
                        <tr>
                            <td>` + meetings[i]['id'] + `</td>
                            <td>` + meetings[i]['name'] + `</td>
                            <td>` + meetings[i]['location'] + `</td>
                            <td>` + meetings[i]['latitude'] + `</td>
                            <td>` + meetings[i]['longitude'] + `</td>
                            <td>` + meetings[i]['meeting_time'] + `</td>
                            <td>` + meetings[i]['distance_time'] + `</td>
                            <td>` + meetings[i]['distance_km'] + `</td>
                            <td>` + meetings[i]['current_km'] + `</td>
                            <td>` + meetings[i]['date'] + `</td>
                        </tr>
                    `;
                }
            } else {
                html += `
                    <tr>
                        <td colspan="9" class="text-center">No Meeting Today</td>
                    </tr>
                `;
            }

            $(".tbody").html(html);
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


            
            var parselatitude = parseFloat($("#latitude").val());
            var parselongitude = parseFloat($("#longitude").val());

            console.log("check");
            if (!isNaN(parselatitude) && !isNaN(parselatitude)) {
                showMapForForm(parselatitude, parselongitude);
            }
        } else {
            console.log("Invalid values for distance time calculation.");
        }
    }



        // Event listener for table row click
        $(".tbody").on("click", "tr", function () {
            var latitude = parseFloat($(this).find("td:eq(3)").text());
            var longitude = parseFloat($(this).find("td:eq(4)").text());

            if (!isNaN(latitude) && !isNaN(longitude)) {
                showMap(latitude, longitude);
            }
        });
</script>

@endsection