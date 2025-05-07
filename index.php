<?php
session_start();
include '../config/db.php';

// Check if the user is logged in and has admin rights
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    header('Location: ../auth/login.php');
    exit();
}

// Fetch transport records and group them by transport name for the chart
$query = "SELECT transport_name, COUNT(*) as count FROM transport GROUP BY transport_name";
$result = mysqli_query($conn, $query);

if (!$result) {
    die("Error fetching data: " . mysqli_error($conn));
}

// Prepare data for the chart
$transportNames = [];
$transportCounts = [];

while ($row = mysqli_fetch_assoc($result)) {
    $transportNames[] = $row['transport_name'];
    $transportCounts[] = $row['count'];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Transport Tracking Dashboard</title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script> <!-- Chart.js Library -->

    <style>
        /* General Styles */
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f8f9fa;
            color: #343a40;
        }

        .container {
            max-width: 900px;
            margin: 20px auto;
            padding: 20px;
            background-color: #ffffff;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        h1 {
            text-align: center;
            color: #007bff;
            margin-bottom: 20px;
        }

        a {
            text-decoration: none;
        }

        /* Buttons */
        .btn {
            display: inline-block;
            padding: 10px 15px;
            font-size: 14px;
            font-weight: bold;
            color: #fff;
            background-color: #007bff;
            border: none;
            border-radius: 4px;
            text-align: center;
            transition: background-color 0.3s ease;
        }

        .btn:hover {
            background-color: #0056b3;
        }

        .btn:active {
            transform: scale(0.98);
        }

        /* Chart Container */
        .chart-container {
            padding: 20px;
            background-color: #f1f1f1;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            margin-bottom: 20px;
        }

        /* Table Styles */
        .table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        .table th,
        .table td {
            padding: 12px 15px;
            text-align: left;
            border: 1px solid #ddd;
        }

        .table th {
            background-color: #007bff;
            color: #ffffff;
        }

        .table tr:nth-child(even) {
            background-color: #f9f9f9;
        }

        .table tr:hover {
            background-color: #f1f1f1;
        }

        .table td a.btn {
            padding: 5px 10px;
            font-size: 12px;
        }

        .table td {
            text-align: center;
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            .chart-container {
                width: 90%;
            }

            .btn {
                font-size: 12px;
                padding: 8px 10px;
            }

            .table th,
            .table td {
                font-size: 14px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Transport Tracking Dashboard</h1>
        <a href="add-transport.php" class="btn">Add New Transport</a>

        <div class="chart-container" style="width: 70%; margin: auto;">
            <canvas id="transportChart"></canvas>
        </div>

        <table class="table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Transport Name</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php
                // Fetch all transport records for table display
                $query = "SELECT * FROM user";
                $result = mysqli_query($conn, $query);
                if (mysqli_num_rows($result) > 0):
                    while ($row = mysqli_fetch_assoc($result)): ?>
                        <tr>
                            <td><?php echo $row['id']; ?></td>
                            <td><?php echo htmlspecialchars($row['transport_name']); ?></td>
                            <td>
                                <a href="edit-user.php?id=<?php echo htmlspecialchars($row['id']); ?>" class="btn">Edit</a>
                                <a href="delete-user.php?id=<?php echo htmlspecialchars($row['id']); ?>" class="btn" onclick="return confirm('Are you sure you want to delete this user?');">Delete</a>
                                <a href="view-user.php?id=<?php echo htmlspecialchars($row['id']); ?>" class="btn">View</a>
                            </td>
                        </tr>
                    <?php endwhile;
                else: ?>
                    <tr>
                        <td colspan="3" style="text-align: center;">No transports found.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <script>
        // Pass PHP data to JavaScript
        const userNames = <?php echo json_encode($userNames); ?>;
        const userCounts = <?php echo json_encode($userCounts); ?>;

        // Create the Chart.js bar chart
        const ctx = document.getElementById('userChart').getContext('2d');
        const usertChart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: usertNames,
                datasets: [{
                    label: 'Number of Users',
                    data: userCounts,
                    backgroundColor: 'rgba(54, 162, 235, 0.6)',
                    borderColor: 'rgba(54, 162, 235, 1)',
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        display: true
                    },
                    tooltip: {
                        enabled: true
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });
    </script>
</body>
</html>
