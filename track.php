<?php
session_start();

// Check if the user is logged in
if (!isset($_SESSION['user'])) {
    header("Location: ../auth/login.php");
    exit;
}

include('../config/db.php');

// Ensure the 'id' is provided and is valid
$id = isset($_GET['id']) ? (int)$_GET['id'] : null;
if (!$id) {
    header("Location: index.php");
    exit;
}

// Fetch user data from the database
$sql = "SELECT * FROM user WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();

// Check if user data is found
if ($result->num_rows === 0) {
    echo "<p class='text-center text-danger'>No user found with the given ID.</p>";
    exit;
}

$user = $result->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Track user</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <h1 class="text-center">Tracking Information</h1>

        <!-- Display user details in a table -->
        <table class="table table-bordered mt-4">
            <tr>
                <th>ID</th>
                <td><?= htmlspecialchars($user['id']) ?></td>
            </tr>
            <tr>
                <th>user Name</th>
                <td><?= htmlspecialchars($user['user_name']) ?></td>
            </tr>
            <tr>
                <th>Driver</th>
                <td><?= htmlspecialchars($user['driver_name']) ?></td>
            </tr>
            <tr>
                <th>Status</th>
                <td><?= htmlspecialchars($user['status']) ?></td>
            </tr>
            <tr>
                <th>Last Location</th>
                <td><?= htmlspecialchars($user['last_location']) ?></td>
            </tr>
        </table>

        <!-- Buttons for map view and back to dashboard -->
        <a href="map.php?id=<?= htmlspecialchars($user['id']) ?>" class="btn btn-success">View on Map</a>
        <a href="index.php" class="btn btn-secondary">Back to Dashboard</a>
    </div>

    <!-- Optional: Include Bootstrap JS (if needed) -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
