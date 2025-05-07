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

$sql = "SELECT username, email, gender FROM userinfo";
$result = $conn->query($sql);
$users = [];
$genderCount = ['Male' => 0, 'Female' => 0, 'Other' => 0];
if ($result->num_rows > 0) {
  while ($row = $result->fetch_assoc()) {
    $users[] = $row;
    $gender = ucfirst(strtolower($row['gender']));
    if (isset($genderCount[$gender])) {
      $genderCount[$gender]++;
    } else {
      $genderCount['Other']++;
    }
  }
}
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Admin Dashboard</title>
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.9.2/html2pdf.bundle.min.js"></script>
  <style>
    body {
      margin: 0;
      font-family: Arial, sans-serif;
    }

    header {
      background-color: black;
      color: white;
      padding: 1rem;
      display: flex;
      justify-content: space-between;
      align-items: center;
    }

    header img.logo {
      height: 40px;
    }

    .container {
      display: flex;
    }

    .sidebar {
      background-color: black;
      color: white;
      width: 200px;
      padding: 1rem;
      text-align: center;
    }

    .sidebar img {
      width: 100px;
      border-radius: 50%;
      margin-bottom: 1rem;
    }

    .main {
      flex-grow: 1;
      padding: 2rem;
    }

    table {
      width: 100%;
      border-collapse: collapse;
      margin-bottom: 2rem;
    }

    th, td {
      border: 1px solid #ddd;
      padding: 0.75rem;
      text-align: left;
    }

    th {
      background-color: #f2f2f2;
    }

    canvas {
      display: block;
      margin: auto;
      max-width: 300px;
      max-height: 300px;
    }

    button {
      padding: 0.5rem 1rem;
      background-color: black;
      color: white;
      border: none;
      cursor: pointer;
      margin-top: 1rem;
    }

    button:hover {
      background-color: #333;
    }

    .logo-title {
      text-align: center;
      margin-bottom: 1rem;
    }

    .copyright {
      text-align: center;
      margin-top: 1.5rem;
      font-size: 0.9rem;
      color: #555;
    }
  </style>
</head>
<body>

<header>
  <h1>Admin Dashboard</h1>
  <img class="logo" src="logo.png" alt="Logo">
</header>

<div class="container">
  <div class="sidebar">
    <img src="Rabbits.png" alt="Rabbit">
    <p><strong>Admin Name</strong></p>
    <p>+1234567890</p>
  </div>

  <div class="main">
    <div id="pdf-content">
      <div class="logo-title">
        <img src="logo.png" alt="Logo" width="100">
        <h2>Cycle Safe Route App</h2>
      </div>

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

      <canvas id="genderChart" width="300" height="300"></canvas>

      <div class="copyright">© Cycle Safe Route</div>
    </div>

    <button onclick="downloadPDF()">Download PDF</button>
  </div>
</div>

<script>
  const genderChart = new Chart(document.getElementById('genderChart').getContext('2d'), {
    type: 'pie',
    data: {
      labels: ['Male', 'Female', 'Other'],
      datasets: [{
        label: 'Gender Distribution',
        data: [
          <?= $genderCount['Male'] ?>,
          <?= $genderCount['Female'] ?>,
          <?= $genderCount['Other'] ?>
        ],
        backgroundColor: ['#36A2EB', '#FF6384', '#FFCE56'],
        borderWidth: 1
      }]
    },
    options: {
      responsive: false,
      maintainAspectRatio: false
    }
  });

  function downloadPDF() {
    const element = document.getElementById('pdf-content');
    html2pdf().from(element).save('dashboard.pdf');
  }
</script>

</body>
</html>
