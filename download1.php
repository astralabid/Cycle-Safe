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

// Fetch users (username and email, gender)
$sql = "SELECT username, email, gender FROM userinfo";
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
    <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
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

        #piechart {
            margin-top: 30px;
            height: 400px;
            width: 100%;
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
</div>

<button id="download-btn">Download as PDF</button>

<script type="text/javascript">
    google.charts.load('current', {'packages':['corechart']});
    google.charts.setOnLoadCallback(drawChart);

    function drawChart() {
        // Hardcoded gender data for pie chart
        var genderData = <?php
            // Fetch gender data for chart
            $genderData = [];
            $genderQuery = "SELECT gender, COUNT(*) AS total FROM userinfo GROUP BY gender";
            $genderResult = $conn->query($genderQuery);
            while ($row = $genderResult->fetch_assoc()) {
                $genderData[] = [$row['gender'], (int)$row['total']];
            }
            echo json_encode($genderData);
        ?>;

        var data = google.visualization.arrayToDataTable([
            ['Gender', 'Count'],
            ...genderData
        ]);

        var options = {
            title: 'Gender Distribution',
            width: 600,
            height: 400
        };

        var chart = new google.visualization.PieChart(document.getElementById('piechart'));
        chart.draw(data, options);
    }

    document.getElementById("download-btn").addEventListener("click", () => {
        const element = document.getElementById("content");

        // Preprint (footer) for PDF only
        const footerText = '© Cycle Safe Routing';
        const logoImage = 'logo.png'; // Path to your logo image

        html2pdf()
            .set({
                margin: 0.5,
                filename: 'user-info.pdf',
                image: { type: 'jpeg', quality: 0.98 },
                html2canvas: { scale: 2, useCORS: true },
                jsPDF: { unit: 'in', format: 'a4', orientation: 'portrait' }
            })
            .from(element)
            .toPdf()
            .get('pdf')
            .then((pdf) => {
                const totalPages = pdf.internal.pages.length;

                // Loop through all pages to add the logo and footer
                for (let i = 1; i <= totalPages; i++) {
                    // Add logo image to the top-left of each page
                    pdf.addImage(logoImage, 'PNG', 0.5, 0.5, 1, 1); // Adjust logo position and size as needed

                    // Add footer on each page
                    pdf.setFontSize(10);
                    pdf.text(footerText, 0.5, pdf.internal.pageSize.height - 0.5); // Footer position at bottom
                }

                // Save the PDF
                pdf.save();
            });
    });
</script>

</body>
</html>
