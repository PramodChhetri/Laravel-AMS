@extends('layout')

@section('main-section')

<div class="container mt-5">

    <a href="/logout" class="btn btn-danger mt-2">Logout</a>

    <h1 class="text text-primary mt-4 mb-4">Appointment Management System</h1>

    <form action="" method="POST" class="mb-4">
        @csrf

        <div class="row">
            <div class="col-sm-3">
                <label for="location" class="form-label">Location</label>
                <input type="text" name="location" id="location" class="form-control" placeholder="Enter Location" required>
            </div>
            <div class="col-sm-3">
                <label for="name" class="form-label">Client Name</label>
                <input type="text" name="name" id="name" class="form-control" placeholder="Client Name" required>
            </div> 
            <div class="col-sm-3">
                <label for="time" class="form-label">Meeting Time Duration</label>
                <input type="number" name="time" class="form-control" placeholder="(In Minutes)" required>
                <span class="text-secondary">Available (09:00 to 18:00) </span>
            </div> 
            <div class="col-sm-3">
                <label for="date" class="form-label">Date</label>
                <input type="date" name="date" class="form-control" required>
            </div>            
        </div>

        <div class="row mt-3">
            <div class="col-sm">
                <input type="submit" class="btn btn-primary">
            </div>
        </div>
    </form>


    <div class="row">
        <div class="col-sm-3">
            <input type="text" id="longitude" name="longitude" class="form-control"placeholder="Longitude" readonly>
            <br>
            <input type="text" id="latitude" name="latitude" class="form-control" placeholder="Latitude" readonly>
            <br>
            <input type="text" id="ip" name="ip" class="form-control" placeholder="IP" readonly>
            <br>
            <input type="text" id="city" name="city" class="form-control" placeholder="city" readonly>
            <input type="hidden" id="cityLat" name="cityLat" value="">
            <input type="hidden" id="cityLon" name="cityLon" value="">
            <br>
            <input type="text" id="dtime" name="dtime" class="form-control" placeholder="dtime" readonly>
            <br>
            <input type="text" id="dkm" name="dkm" class="form-control" placeholder="dkm" readonly>
        </div>
    </div>



</div>

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
                })
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

        $("#dkm").val(distance.toFixed(2) + "km");

        calculateDistanceTime();
    }
    else {
        console.log("Invalid values for distance calculation.");
        }
    }


    function deg2rad(deg) {
        return deg * (Math.PI / 180);
    }
    
    function calculateDistanceTime() {
    var distance = parseFloat($("#dkm").val().replace("km", ""));
    var speed = 50; // Assume a default speed (you should replace this with the actual speed)

    if (!isNaN(distance)) {
        var time = distance / speed; // Time in hours

        // Convert time to minutes
        var timeInMinutes = time * 60;

        console.log("Calculated Time:", timeInMinutes);

        $("#dtime").val(timeInMinutes.toFixed(2) + " min");
    } else {
        console.log("Invalid values for distance time calculation.");
        }
    }   

</script>



@endsection