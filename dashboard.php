<?php
session_start();  // Start the session

// Check if user is logged in
if (!isset($_SESSION['username'])) {
    echo "<script>alert('Please login first.'); window.location.href = 'login.php';</script>";
    exit();
}

// Include the database connection
include('db.php');

// Query for total employees
$totalEmployeesQuery = "SELECT COUNT(*) as total FROM employees";
$totalEmployeesResult = $conn->query($totalEmployeesQuery);
$totalEmployees = $totalEmployeesResult->fetch_assoc()['total'];

// Query for gender distribution
$genderQuery = "SELECT gender, COUNT(*) as count FROM employees GROUP BY gender";
$genderResult = $conn->query($genderQuery);
$genderData = [];
while ($row = $genderResult->fetch_assoc()) {
    $genderData[$row['gender']] = $row['count'];
}

// Query for employee commissions (current month)
$month = date('m');
$year = date('Y');
$commissionMonthQuery = "
    SELECT e.first_name, e.last_name, SUM(t.amount) as total_earnings
    FROM employees e
    LEFT JOIN transactions t ON e.id = t.employee_id
    WHERE MONTH(t.transaction_date) = $month AND YEAR(t.transaction_date) = $year
    GROUP BY e.id
    ORDER BY total_earnings DESC
    LIMIT 10";
$commissionMonthResult = $conn->query($commissionMonthQuery);
$employeeCommissionsMonth = [];
while ($row = $commissionMonthResult->fetch_assoc()) {
    $employeeCommissionsMonth[] = [
        'name' => $row['first_name'] . ' ' . $row['last_name'],
        'commission' => $row['total_earnings'] ? 0.05 * $row['total_earnings'] : 0 // 5% commission
    ];
}

// Query for employee commissions (current year)
$commissionYearQuery = "
    SELECT e.first_name, e.last_name, SUM(t.amount) as total_earnings
    FROM employees e
    LEFT JOIN transactions t ON e.id = t.employee_id
    WHERE YEAR(t.transaction_date) = $year
    GROUP BY e.id
    ORDER BY total_earnings DESC
    LIMIT 10";
$commissionYearResult = $conn->query($commissionYearQuery);
$employeeCommissionsYear = [];
while ($row = $commissionYearResult->fetch_assoc()) {
    $employeeCommissionsYear[] = [
        'name' => $row['first_name'] . ' ' . $row['last_name'],
        'commission' => $row['total_earnings'] ? 0.05 * $row['total_earnings'] : 0 // 5% commission
    ];
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <?php include './cdn.php'; // Include your CDN links 
    ?>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <link rel="stylesheet" href="./css/base.css">
    <link rel="stylesheet" href="./css/dashboard.css">
</head>

<body>
    <?php include './sidebar.php'; ?>
<div class="welcome_text">
<h2>Welcome, <?php echo htmlspecialchars($_SESSION['username']); ?>!</h2>
</div>

    <!-- Display charts -->
    <div class="chart_grid">
        <div class="chart">
            <canvas id="employeeChart" width="400" height="200"></canvas>
        </div>
        <div class="chart">
            <canvas id="genderChart" width="400" height="200"></canvas>
        </div>
        <div class="chart">
            <canvas id="commissionMonthChart" width="400" height="200"></canvas> <!-- Commission for the month -->
        </div>
        <div class="chart">
            <canvas id="commissionYearChart" width="400" height="200"></canvas> <!-- Commission for the year -->
        </div>
    </div>

    <script>
        // Total Employees Chart
        var ctx = document.getElementById('employeeChart').getContext('2d');
        var employeeChart = new Chart(ctx, {
            type: 'doughnut',
            data: {
                labels: ['Total Employees'],
                datasets: [{
                    label: 'Total Employees',
                    data: [<?php echo $totalEmployees; ?>],
                    backgroundColor: [
                                'rgb(255, 99, 132)',
                                'rgb(54, 162, 235)',
                                'rgb(255, 205, 86)',
                                'rgb(75, 192, 192)',
                                'rgb(153, 102, 255)',
                                'rgb(255, 159, 64)'
                            ],
                    borderWidth: 1
                }]
            },
            options: {
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });

        // Gender Distribution Chart
        var ctx2 = document.getElementById('genderChart').getContext('2d');
        var genderChart = new Chart(ctx2, {
            type: 'pie',
            data: {
                labels: ['Male', 'Female'],
                datasets: [{
                    label: 'Gender Distribution',
                    data: [<?php echo isset($genderData['Male']) ? $genderData['Male'] : 0; ?>,
                        <?php echo isset($genderData['Female']) ? $genderData['Female'] : 0; ?>
                    ],
                    backgroundColor: [
                                'rgb(255, 99, 132)',
                                'rgb(54, 162, 235)',
                                'rgb(255, 205, 86)',
                                'rgb(75, 192, 192)',
                                'rgb(153, 102, 255)',
                                'rgb(255, 159, 64)'
                            ],
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        position: 'top',
                    }
                }
            }
        });

        // Employee Commission Chart (Current Month)
        var ctx3 = document.getElementById('commissionMonthChart').getContext('2d');
        var commissionMonthChart = new Chart(ctx3, {
            type: 'bar',
            data: {
                labels: <?php echo json_encode(array_column($employeeCommissionsMonth, 'name')); ?>,
                datasets: [{
                    label: 'Commission (Current Month)',
                    data: <?php echo json_encode(array_column($employeeCommissionsMonth, 'commission')); ?>,
                    backgroundColor: [
                                'rgb(255, 99, 132)',
                                'rgb(54, 162, 235)',
                                'rgb(255, 205, 86)',
                                'rgb(75, 192, 192)',
                                'rgb(153, 102, 255)',
                                'rgb(255, 159, 64)'
                            ],
                    borderWidth: 1
                }]
            },
            options: {
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });

        // Employee Commission Chart (Current Year)
        var ctx4 = document.getElementById('commissionYearChart').getContext('2d');
        var commissionYearChart = new Chart(ctx4, {
            type: 'bar',
            data: {
                labels: <?php echo json_encode(array_column($employeeCommissionsYear, 'name')); ?>,
                datasets: [{
                    label: 'Commission (Current Year)',
                    data: <?php echo json_encode(array_column($employeeCommissionsYear, 'commission')); ?>,
                    backgroundColor: [
                                'rgb(255, 99, 132)',
                                'rgb(54, 162, 235)',
                                'rgb(255, 205, 86)',
                                'rgb(75, 192, 192)',
                                'rgb(153, 102, 255)',
                                'rgb(255, 159, 64)'
                            ],
                    borderWidth: 1
                }]
            },
            options: {
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