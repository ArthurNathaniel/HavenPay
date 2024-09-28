<?php
include('db.php');
session_start();  // Start the session

// Check if user is logged in
if (!isset($_SESSION['username'])) {
    echo "<script>alert('Please login first.'); window.location.href = 'login.php';</script>";
    exit();
}

// Function to get start and end dates of the current week
function getStartAndEndDate($week, $year) {
    $dto = new DateTime();
    $dto->setISODate($year, $week);
    $start = $dto->format('Y-m-d');
    $dto->modify('+6 days');
    $end = $dto->format('Y-m-d');
    return [$start, $end];
}

// Get the current week and year
$currentWeek = date('W');
$currentYear = date('Y');
list($startDate, $endDate) = getStartAndEndDate($currentWeek, $currentYear);

// Fetch weekly earnings for employees
$sql = "SELECT e.id, e.first_name, e.last_name, SUM(t.amount) AS total_earnings
        FROM employees e
        LEFT JOIN transactions t ON e.id = t.employee_id
        WHERE t.transaction_date BETWEEN '$startDate' AND '$endDate'
        GROUP BY e.id, e.first_name, e.last_name";
$result = $conn->query($sql);

// Initialize total sum for all employees' 5% earnings
$total_sum_5_percent = 0;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Employee Weekly Earnings</title>
    <style>
        table {
            width: 100%;
            border-collapse: collapse;
        }
        table, th, td {
            border: 1px solid black;
        }
        th, td {
            padding: 10px;
            text-align: left;
        }
    </style>
</head>
<body>
    <h2>Employee Weekly Earnings (5%)</h2>
    <table>
        <thead>
            <tr>
                <th>Employee</th>
                <th>Total Weekly Earnings</th>
                <th>5% of Weekly Earnings</th>
            </tr>
        </thead>
        <tbody>
            <?php
            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    $employee_name = htmlspecialchars($row['first_name'] . " " . $row['last_name']);
                    $total_earnings = $row['total_earnings'] ? $row['total_earnings'] : 0;
                    $employee_earnings_5_percent = 0.05 * $total_earnings;

                    // Add the 5% commission to the total sum
                    $total_sum_5_percent += $employee_earnings_5_percent;

                    echo "<tr>
                            <td>" . $employee_name . "</td>
                            <td>" . htmlspecialchars(number_format($total_earnings, 2)) . "</td>
                            <td>" . htmlspecialchars(number_format($employee_earnings_5_percent, 2)) . "</td>
                          </tr>";
                }
            } else {
                echo "<tr><td colspan='3'>No transactions found for this week</td></tr>";
            }
            ?>
        </tbody>
        <tfoot>
            <tr>
                <th colspan="2">Total Payroll (5%) for All Employees</th>
                <th><?php echo number_format($total_sum_5_percent, 2); ?></th>
            </tr>
        </tfoot>
    </table>
</body>
</html>
