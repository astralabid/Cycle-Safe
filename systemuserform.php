<?php
// Database connection settings
$servername = "localhost";
$username = "root";  // Default XAMPP username for MySQL
$password = "";      // Default XAMPP password for MySQL
$dbname = "auth_system"; // Replace with your actual database name

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if(isset($_POST['submit'])){
    $username = $_POST['username'];
    $password = $_POST['password'];
    $email = $_POST['email'];
    $gender = $_POST['gender'];


    // Insert into users1 table (Ensure username is unique)
    $sql1 = "INSERT INTO users1 (username, password) VALUES ('$username', '$password')";
    

    // Insert into userinfo table (Now correctly linking username, degree, and institute)
    $sql3 = "INSERT INTO userinfo (username, email, gender, degree, institute) 
             VALUES ('$username', '$email', '$gender', '$degree', '$institute')";

    // Execute queries
    if ($conn->query($sql1) === TRUE && $conn->query($sql2) === TRUE && $conn->query($sql3) === TRUE) {
        echo "Data inserted successfully!";
        header('Location: userdashboard.php'); // Redirect after successful insertion
        exit();
    } else {
        echo "Error: " . $conn->error;
    }
}

// Close the database connection
$conn->close();
?>


<!doctype html>
<html lang="en">
<head>
	<!--booRequired meta tags -->
	<meta charset="utf8mb4">

	<meta name="viewpoint" content ="witdth=device-width,
	initial-scale=1, shrink-to-fit=no">

	<!--Bootstrap CSS -->

	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css">
	
	<title> tanziuserpackage </title>
	<style>
    body {
      margin: 0;
      padding: 0;
      font-family: Arial, sans-serif;
      background-color: #f4f4f9;
    }

    .form-container {
      display: flex;
      justify-content: center;
      align-items: center;
      padding: 2rem;
    }

    .form-section {
      background-color: #ffffff;
      padding: 2rem;
      border-radius: 8px;
      box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
      width: 100%;
      max-width: 600px;
    }
	.form-section h2 {
      font-size: 1.5rem;
      margin-bottom: 1.5rem;
      text-align: center;
      color: #333;
    }

    .form-group {
      margin-bottom: 1rem;
    }

    .form-label {
      display: block;
      margin-bottom: 0.5rem;
      font-weight: bold;
      color: #555;
    }
	.form-control {
      width: 100%;
      padding: 0.75rem;
      border: 1px solid #ccc;
      border-radius: 4px;
      font-size: 1rem;
    }

    .form-control:focus {
      border-color: #007bff;
      outline: none;
      box-shadow: 0 0 5px rgba(0, 123, 255, 0.25);
    }

    .form-submit {
      display: flex;
      justify-content: center;
      margin-top: 1.5rem;
    }
	.btn {
      padding: 0.75rem 1.5rem;
      background-color: #007bff;
      color: white;
      border: none;
      border-radius: 4px;
      font-size: 1rem;
      cursor: pointer;
      transition: background-color 0.3s;
    }

    .btn:hover {
      background-color: #0056b3;
    }
	</style>
</head>
<body> 
<div class="form-container">
<div class="form-section">
	<h2>User Form</h2>
	<div class="container">
	<form method="post">
  		<div class="form-group">
    		<label>username</label>
    		<input type="text" class="form-control" 
			placeholder="Please Enter username" name="username">
    	</div>

  		<div class="form-group">
    		<label>password</label>
    		<input type="text" class="form-control" 
			placeholder="Please Enter password" name="password">
        </div>
		
		<div class="form-group">
    		<label>email</label>
    		<input type="text" class="form-control" 
			placeholder="Please Enter email" name="email">
        </div>
		<div class="form-group">
    		<label>Gender</label>
    		<input type="text" class="form-control" 
			placeholder="Please Enter gender" name="gender">
        </div>
		
<!-- Submit Button -->
<div class="form-submit">
  	<button type="submit" class="btn btn-primary" name="submit">Submit</button>
    
	</form>

	</div>
	</div>
	</div>
</body> 
</html>
