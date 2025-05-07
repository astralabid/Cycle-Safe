<?php
session_start();
include '../config/db.php';

if (!isset($_GET['id'])) {
    header('Location: index.php');
    exit();
}

$user_id = $_GET['id'];

// Delete the user record
$query = "DELETE FROM user WHERE id = '$user_id'";
if (mysqli_query($conn, $query)) {
    header('Location: index.php');
} else {
    echo "Error: " . mysqli_error($conn);
}
?>
