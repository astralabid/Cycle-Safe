<?php
session_start();
include '../config/db.php';

// Ensure user_id is set and sanitize the input
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header('Location: index.php');
    exit();
}

$user_id = (int)$_GET['id'];  // Cast to integer to prevent SQL injection

// Fetch the specific user record
$query = "SELECT * FROM user WHERE id = ?";
$stmt = mysqli_prepare($conn, $query);
mysqli_stmt_bind_param($stmt, 'i', $user_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$user = mysqli_fetch_assoc($result);

// Check if user record exists
if (!$user) {
    echo "user not found.";
    exit();
}

$latitude = $user['latitude'];
$longitude = $user['longitude'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View user</title>
    <link rel="stylesheet" href="../assets/css/styles.css">

    <!-- Google Maps API Key -->
    <script src="https://maps.googleapis.com/maps/api/js?key=YOUR_GOOGLE_MAPS_API_KEY&callback=initMap" async defer></script>
    
    <style>
        #map {
            height: 400px;
            width: 100%;
            margin-top: 20px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>View user</h1>
        <table class="table">
            <tr>
                <th>ID</th>
                <td><?php echo htmlspecialchars($user['id']); ?></td>
            </tr>
            <tr>
                <th>user Name</th>
                <td><?php echo htmlspecialchars($user['user_name']); ?></td>
            </tr>
            <tr>
                <th>Tracking Number</th>
                <td><?php echo htmlspecialchars($user['tracking_number']); ?></td>
            </tr>
            <tr>
                <th>Current Location</th>
                <td>Latitude: <?php echo htmlspecialchars($latitude); ?>, Longitude: <?php echo htmlspecialchars($longitude); ?></td>
            </tr>
        </table>

        <!-- Display Google Map -->
        <div id="map"></div>
    </div>

    <script>
        // Initialize and add the map
        function initMap() {
            // The user's location
            var userLocation = { lat: <?php echo $latitude; ?>, lng: <?php echo $longitude; ?> };
            
            // The map, centered at user's location
            var map = new google.maps.Map(document.getElementById('map'), {
                zoom: 15,
                center: userLocation
            });

            // Add a marker for the user's location
            var marker = new google.maps.Marker({
                position: usertLocation,
                map: map,
                title: "userLocation"
            });

            // If geolocation is supported and allowed, get user's current location
            if (navigator.geolocation) {
                navigator.geolocation.getCurrentPosition(function(position) {
                    var userLocation = {
                        lat: position.coords.latitude,
                        lng: position.coords.longitude
                    };

                    // Display user location on the map
                    var userMarker = new google.maps.Marker({
                        position: userLocation,
                        map: map,
                        title: "Your Location",
                        icon: 'http://maps.google.com/mapfiles/ms/icons/blue-dot.png'
                    });

                    // Center the map to show both user and user's location
                    map.setCenter(userLocation);

                    // Optionally, draw a line between the user's location and the user's location
                    var line = new google.maps.Polyline({
                        path: [userLocation, userLocation],
                        geodesic: true,
                        strokeColor: '#FF0000',
                        strokeOpacity: 1.0,
                        strokeWeight: 2
                    });
                    line.setMap(map);

                }, function() {
                    alert("Geolocation service failed. Please allow location access.");
                });
            } else {
                alert("Geolocation is not supported by this browser.");
            }
        }
    </script>
</body>
</html>
