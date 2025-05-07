<?php
session_start();
include '../config/db.php';

if (!isset($_GET['id'])) {
    header('Location: index.php');
    exit();
}

$user_id = $_GET['id'];

// Fetch the user record to edit
$query = "SELECT * FROM user WHERE id = '$user_id'";
$result = mysqli_query($conn, $query);
$transport = mysqli_fetch_assoc($result);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $new_user_name = mysqli_real_escape_string($conn, $_POST['user_name']);
    $new_tracking_number = mysqli_real_escape_string($conn, $_POST['tracking_number']);
    
    $query = "UPDATE user SET user_name = '$new_user_name', tracking_number = '$new_tracking_number' WHERE id = '$user_id'";
    if (mysqli_query($conn, $query)) {
        header('Location: index.php');
    } else {
        echo "Error: " . mysqli_error($conn);
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit User</title>
    <link rel="stylesheet" href="../assets/css/styles.css">
</head>
<body>
    <div class="container">
        <h1>Edit User</h1>
        <form action="edit-user.php?id=<?php echo $user['id']; ?>" method="POST">
            <input type="text" name="user_name" value="<?php echo $user['user_name']; ?>" required>
            <input type="text" name="tracking_number" value="<?php echo $user['tracking_number']; ?>" required>
            <button type="submit" class="btn">Update User</button>
        </form>
    </div>
</body>
</html>
