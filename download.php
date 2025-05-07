<?php
// Connect to MySQL
$host = "localhost";
$user = "root";
$pass = "";
$db = "auth_system";

$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) {
  die("Connection failed: " . $conn->connect_error);
}

// Fetch users (username and password)
$sql = "SELECT username, password FROM users1";
$result = $conn->query($sql);

$users = [];
if ($result->num_rows > 0) {
  while ($row = $result->fetch_assoc()) {
    $users[] = $row;
  }
}
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Download Users as PDF</title>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>
  <style>
    body {
      font-family: Arial, sans-serif;
      background-color: #f8f8f8;
      margin: 0;
      padding: 0;
    }

    .page-header {
      background-color: #000;
      color: #fff;
      padding: 15px 20px;
      text-align: center;
      font-size: 24px;
      font-weight: bold;
    }

    #content {
      background: #fff;
      padding: 20px;
      border: 1px solid #ccc;
      width: 600px;
      margin: 20px auto;
      box-shadow: 2px 2px 8px rgba(0,0,0,0.1);
    }

    h1 {
      font-size: 20px;
      text-align: center;
      margin-bottom: 20px;
    }

    table {
      width: 100%;
      border-collapse: collapse;
    }

    th {
      background-color: #f2f2f2;
      padding: 10px;
      border: 1px solid #ddd;
      text-align: left;
    }

    td {
      padding: 10px;
      border: 1px solid #ddd;
    }

    tr:nth-child(even) {
      background-color: #f9f9f9;
    }

    #download-btn {
      display: block;
      margin: 20px auto;
      padding: 10px 20px;
      background-color: #4CAF50;
      color: white;
      border: none;
      border-radius: 4px;
      cursor: pointer;
      font-size: 16px;
    }

    #download-btn:hover {
      background-color: #45a049;
    }
  </style>
</head>
<body>

<div class="page-header">
  User Report
</div>

<div id="content">
  <h1>Users from Database</h1>
  <table>
    <tr>
      <th>Username</th>
      <th>Password</th>
    </tr>
    <?php foreach ($users as $user): ?>
    <tr>
      <td><?= htmlspecialchars($user['username']) ?></td>
      <td><?= htmlspecialchars($user['password']) ?></td>
    </tr>
    <?php endforeach; ?>
  </table>
</div>

<button id="download-btn">Download as PDF</button>

<script>
  document.getElementById("download-btn").addEventListener("click", () => {
    const element = document.getElementById("content");
    html2pdf()
      .set({
        margin: 0.5,
        filename: 'user-list.pdf',
        image: { type: 'jpeg', quality: 0.98 },
        html2canvas: { scale: 2 },
        jsPDF: { unit: 'in', format: 'letter', orientation: 'portrait' }
      })
      .from(element)
      .save();
  });
</script>

</body>
</html>
