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

// Fetch all users
$sql = "SELECT username, email, gender FROM userinfo";
$result = $conn->query($sql);

$users = [];
if ($result->num_rows > 0) {
  while ($row = $result->fetch_assoc()) {
    $users[] = $row;
  }
}

// Fetch gender counts for chart
$genderData = [];
$genderQuery = "SELECT gender, COUNT(*) AS total FROM userinfo GROUP BY gender";
$genderResult = $conn->query($genderQuery);
if ($genderResult->num_rows > 0) {
  while ($row = $genderResult->fetch_assoc()) {
    $genderData[] = [$row['gender'], (int)$row['total']];
  }
}
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>User Info with Chart</title>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>
  <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
  <style>
    body {
      font-family: Arial, sans-serif;
      margin: 0;
      display: flex;
    }

    .left-margin {
      background-color: #000;
      width: 100px;
      height: 100vh;
      display: flex;
      flex-direction: column;
      align-items: center;
      padding-top: 10px;
    }

    .margin-logo {
      width: 80px;
      margin-bottom: 10px;
    }

    .margin-name {
      color: white;
      font-size: 12px;
      text-align: center;
    }

    #main-container {
      margin-left: 100px;
      flex-grow: 1;
      padding: 20px;
      background-color: #f8f8f8;
    }

    .page-header {
      background-color: #000;
      color: #fff;
      padding: 15px;
      text-align: center;
      font-size: 24px;
    }

    #content {
      background: #fff;
      padding: 20px;
      border: 1px solid #ccc;
      max-width: 800px;
      margin: 20px auto;
    }

    h1 {
      text-align: center;
      margin-bottom: 20px;
    }

    table {
      width: 100%;
      border-collapse: collapse;
    }

    th, td {
      padding: 10px;
      border: 1px solid #ccc;
    }

    tr:nth-child(even) {
      background-color: #f9f9f9;
    }

    #piechart {
      width: 100%;
      height: 400px;
      margin-top: 40px;
    }

    #download-btn {
      display: block;
      margin: 20px auto;
      padding: 10px 20px;
      background-color: #4CAF50;
      color: white;
      border: none;
      font-size: 16px;
      cursor: pointer;
    }

    #pdf-logo {
      display: none;
      width: 100px;
      margin: 10px auto;
    }

    #pdf-only-footer {
      display: none;
      text-align: center;
      margin-top: 30px;
      font-size: 14px;
      color: #555;
    }
  </style>
</head>
<body>

<div class="left-margin">
  <img src="sidebar-logo.png" alt="Sidebar Logo" class="margin-logo">
  <div class="margin-name">Admin Panel</div>
</div>

<div id="main-container">
  <div class="page-header">User Information</div>

  <div id="content">
    <img id="pdf-logo" src="logo.png" alt="Logo for PDF">
    <h1>User Info Table</h1>

    <table>
      <tr>
        <th>Username</th>
        <th>Email</th>
        <th>Gender</th>
      </tr>
      <?php foreach ($users as $user): ?>
      <tr>
        <td><?= htmlspecialchars($user['username']) ?></td>
        <td><?= htmlspecialchars($user['email']) ?></td>
        <td><?= htmlspecialchars($user['gender']) ?></td>
      </tr>
      <?php endforeach; ?>
    </table>

    <div id="piechart"></div>

    <div id="pdf-only-footer">© cccccc company</div>
  </div>

  <button id="download-btn">Download as PDF</button>
</div>

<script type="text/javascript">
  google.charts.load('current', {'packages':['corechart']});
  google.charts.setOnLoadCallback(drawChart);

  function drawChart() {
    var data = google.visualization.arrayToDataTable([
      ['Gender', 'Count'],
      <?php
        foreach ($genderData as $g) {
          echo "['" . $g[0] . "', " . $g[1] . "],";
        }
      ?>
    ]);

    var options = {
      title: 'Gender Distribution'
    };

    var chart = new google.visualization.PieChart(document.getElementById('piechart'));
    chart.draw(data, options);
  }

  document.getElementById("download-btn").addEventListener("click", () => {
    const logo = document.getElementById("pdf-logo");
    const footer = document.getElementById("pdf-only-footer");

    logo.style.display = 'block';
    footer.style.display = 'block';

    html2pdf()
      .set({
        margin: 0.5,
        filename: 'user-info.pdf',
        image: { type: 'jpeg', quality: 0.98 },
        html2canvas: { scale: 2 },
        jsPDF: { unit: 'in', format: 'letter', orientation: 'portrait' }
      })
      .from(document.getElementById("content"))
      .save()
      .then(() => {
        logo.style.display = 'none';
        footer.style.display = 'none';
      });
  });
</script>

</body>
</html>
