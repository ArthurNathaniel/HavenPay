<?php
include('db.php');
session_start();

// Check if user is logged in
if (!isset($_SESSION['username'])) {
    echo "<script>alert('Please login first.'); window.location.href = 'login.php';</script>";
    exit();
}

// Get the selected year from the form or default to the current year
$selectedYear = isset($_GET['year']) ? intval($_GET['year']) : date('Y');

// Get the selected employee from the form (default to null)
$selectedEmployeeId = isset($_GET['employee_id']) ? intval($_GET['employee_id']) : null;

// Query to get all employees for the dropdown
$employeeQuery = "SELECT id, first_name, last_name, profile_image FROM employees";
$employeeResult = $conn->query($employeeQuery);

// Initialize empty earnings data
$weeklyEarnings = [];
$employeeImage = null;
$employeeName = null;

// Only query if an employee is selected
if ($selectedEmployeeId) {
    // Get selected employee's profile image and name
    $employeeDetailsQuery = "SELECT first_name, last_name, profile_image FROM employees WHERE id = $selectedEmployeeId";
    $employeeDetailsResult = $conn->query($employeeDetailsQuery);
    if ($employeeDetailsResult->num_rows > 0) {
        $employeeDetails = $employeeDetailsResult->fetch_assoc();
        $employeeName = htmlspecialchars($employeeDetails['first_name'] . " " . $employeeDetails['last_name']);
        $employeeImage = $employeeDetails['profile_image']; // Assuming this is the path to the image
    }

    // Query to get the selected employee's earnings for the selected year grouped by weeks
    $sql = "SELECT WEEK(t.transaction_date, 1) AS week_number, 
                   SUM(t.amount) AS total_earnings
            FROM transactions t
            WHERE YEAR(t.transaction_date) = '$selectedYear' AND t.employee_id = '$selectedEmployeeId'
            GROUP BY WEEK(t.transaction_date, 1)";
    $result = $conn->query($sql);

    // Populate the earnings array for the employee
    while ($row = $result->fetch_assoc()) {
        $weekNumber = $row['week_number'];
        $totalEarnings = $row['total_earnings'] ? $row['total_earnings'] : 0;
        $commission = 0.05 * $totalEarnings;

        $weeklyEarnings[$weekNumber] = [
            'earnings' => $totalEarnings,
            'commission' => $commission
        ];
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Employee Weekly Earnings</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
            padding: 0;
            background-color: #f4f4f9;
        }

        h2 {
            text-align: center;
            margin-bottom: 20px;
        }

        form {
            text-align: center;
            margin-bottom: 30px;
        }

        label, select, input[type="submit"] {
            font-size: 16px;
            padding: 5px 10px;
            margin: 5px;
        }

        table {
            width: 100%;
            max-width: 800px;
            margin: 0 auto;
            border-collapse: collapse;
            background-color: white;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }

        table, th, td {
            border: 1px solid #ccc;
        }

        th, td {
            padding: 15px;
            text-align: center;
        }

        th {
            background-color: #007bff;
            color: white;
        }

        tr:nth-child(even) {
            background-color: #f2f2f2;
        }

        tr.highlight {
            background-color: #ffdd57;
            font-weight: bold;
        }

        .employee-profile {
            text-align: center;
            margin-bottom: 20px;
        }

        .employee-profile img {
            border-radius: 50%;
            width: 150px;
            height: 150px;
            object-fit: cover;
            border: 4px solid #007bff;
        }

        @media (max-width: 600px) {
            table, th, td {
                font-size: 12px;
                padding: 10px;
            }

            .employee-profile img {
                width: 100px;
                height: 100px;
            }
        }
    </style>
</head>
<body>
    <h2>Employee Weekly Earnings for <?php echo $selectedYear; ?></h2>

    <!-- Employee and Year Filter Form -->
    <form method="GET" action="">
        <label for="employee">Select Employee: </label>
        <select name="employee_id" id="employee" required>
            <option value="">-- Select Employee --</option>
            <?php
            // Populate employee dropdown options
            if ($employeeResult->num_rows > 0) {
                while ($employeeRow = $employeeResult->fetch_assoc()) {
                    $employeeId = $employeeRow['id'];
                    $employeeNameDropdown = htmlspecialchars($employeeRow['first_name'] . " " . $employeeRow['last_name']);
                    echo "<option value=\"$employeeId\"" . ($employeeId == $selectedEmployeeId ? " selected" : "") . ">$employeeNameDropdown</option>";
                }
            }
            ?>
        </select>

        <label for="year">Select Year: </label>
        <select name="year" id="year">
            <?php
            $currentYear = date('Y');
            for ($year = $currentYear; $year >= 2000; $year--) {
                echo "<option value=\"$year\"" . ($year == $selectedYear ? " selected" : "") . ">$year</option>";
            }
            ?>
        </select>
        <input type="submit" value="Filter">
    </form>

    <!-- Display Employee Profile Image and Name -->
    <?php if ($selectedEmployeeId && $employeeImage): ?>
        <div class="employee-profile">
            <img src="<?php echo htmlspecialchars($employeeImage); ?>" alt="Profile Image of <?php echo htmlspecialchars($employeeName); ?>">
            <h3><?php echo htmlspecialchars($employeeName); ?></h3>
        </div>
    <?php endif; ?>

<!-- Display Earnings Table -->
<!-- Display Earnings Table -->
<?php 
// Get the current week number
$currentWeekNumber = date('W');

if ($selectedEmployeeId): ?>
    <table>
        <thead>
            <tr>
                <th>Week Number</th>
                <th>Earnings</th>
                <th>5% Commission</th>
            </tr>
        </thead>
        <tbody>
            <?php
            // Display earnings and commission for each week (52 to 1), starting with the current week at the top
            for ($week = 52; $week >= 1; $week--) {
                $earnings = isset($weeklyEarnings[$week]) ? number_format($weeklyEarnings[$week]['earnings'], 2) : '0.00';
                $commission = isset($weeklyEarnings[$week]) ? number_format($weeklyEarnings[$week]['commission'], 2) : '0.00';

                // Check if it's the current week and add a red background
                $rowStyle = ($week == $currentWeekNumber) ? 'style="background-color: red;"' : '';

                echo "<tr $rowStyle>";
                echo "<td>Week $week</td>";
                echo "<td>$earnings</td>";
                echo "<td>$commission</td>";
                echo "</tr>";
            }
            ?>
        </tbody>
    </table>
<?php else: ?>
    <p>Please select an employee to view their weekly earnings.</p>
<?php endif; ?>


</body>
</html>
