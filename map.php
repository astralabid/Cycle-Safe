<?php
// Backend: Fetch user data from the database
session_start();
include '../config/db.php';

// Check if the user is logged in (optional authentication check)
if (!isset($_SESSION['user_id'])) {
    header('Location: ../auth/login.php');
    exit();
}

// Fetch user data from the database
$query = "SELECT user_name, tracking_number, latitude, longitude FROM user";
$result = mysqli_query($conn, $query);

$userLocations = [];
if ($result && mysqli_num_rows($result) > 0) {
    while ($row = mysqli_fetch_assoc($result)) {
        $userLocations[] = [
            'lat' => (float)$row['latitude'],
            'lng' => (float)$row['longitude'],
            'title' => htmlspecialchars($row['user_name']),
            'trackingNumber' => htmlspecialchars($row['tracking_number']),
        ];
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Map</title>
    <script src="https://maps.googleapis.com/maps/api/js?key=YOUR_GOOGLE_MAPS_API_KEY&callback=initMap" async defer></script>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f8f9fa;
            color: #333;
        }
        .container {
            max-width: 900px;
            margin: 20px auto;
            padding: 20px;
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }
        h1 {
            text-align: center;
            color: #007bff;
        }
        #map {
            width: 100%;
            height: 500px;
            border-radius: 8px;
            border: 1px solid #ddd;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }
    </style>
    <script>
        // Pass PHP data to JavaScript
        let userLocations = <?php echo json_encode($usertLocations); ?>;

        function initMap() {
            // Center the map on the first user location, or use a default location
            const defaultLocation = { lat: -34.397, lng: 150.644 }; // Replace with a fallback location if needed
            const centerLocation = usertLocations.length > 0 ? userLocations[0] : defaultLocation;

            const map = new google.maps.Map(document.getElementById('map'), {
                zoom: 10,
                center: centerLocation,
            });

            // Add markers for each user location
            userLocations.forEach(location => {
                const marker = new google.maps.Marker({
                    position: { lat: location.lat, lng: location.lng },
                    map: map,
                    title: location.title,
                });

                // Add an info window for each marker
                const infoWindow = new google.maps.InfoWindow({
                    content: `<div>
                        <h4>${location.title}</h4>
                        <p>Tracking Number: ${location.trackingNumber}</p>
                    </div>`,
                });

                marker.addListener('click', () => {
                    infoWindow.open(map, marker);
                });
            });
        }
    </script>
</head>
<body>
    <div class="container">
        <h1>Transport Tracking Map</h1>
        <div id="map"></div>
    </div>
</body>
</html>
