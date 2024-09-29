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
$monthlyEarnings = [];
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

    // Query to get the selected employee's earnings for the selected year
    $sql = "SELECT MONTH(t.transaction_date) AS month, 
                   SUM(t.amount) AS total_earnings
            FROM transactions t
            WHERE YEAR(t.transaction_date) = '$selectedYear' AND t.employee_id = '$selectedEmployeeId'
            GROUP BY MONTH(t.transaction_date)";
    $result = $conn->query($sql);

    // Populate the earnings array for the employee
    while ($row = $result->fetch_assoc()) {
        $month = $row['month'];
        $totalEarnings = $row['total_earnings'] ? $row['total_earnings'] : 0;
        $commission = 0.05 * $totalEarnings;

        $monthlyEarnings[$month] = [
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
    <title>Employee Monthly Earnings</title>
    <?php include './cdn.php' ?>
    <link rel="stylesheet" href="./css/base.css">
    <link rel="stylesheet" href="./css/earnings.css">
    <!-- <style>
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
        .employee-profile {
            text-align: center;
            margin-bottom: 20px;
        }
        .employee-profile img {
            border-radius: 50%;
            width: 150px;
            height: 150px;
        }
    </style> -->
</head>
<body>
<?php include './sidebar.php' ?>
    <div class="earnings_all">
   <div class="forms_title">
    <h2>Employee Monthly Earnings for <?php echo $selectedYear; ?></h2>
   </div>
    <!-- Employee and Year Filter Form -->
    <form method="GET" action="">
  <div class="forms">
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
  </div>
<div class="forms">
    
<label for="year">Select Year: </label>
        <select name="year" id="year">
            <?php
            $currentYear = date('Y');
            for ($year = $currentYear; $year >= 2000; $year--) {
                echo "<option value=\"$year\"" . ($year == $selectedYear ? " selected" : "") . ">$year</option>";
            }
            ?>
            </select>
</div>
        
      <div class="forms">
      <!-- <input type="submit" value="Filter"> -->
      <button type="submit">Filter</button>
      </div>
    </form>

    <!-- Display Employee Profile Image and Name -->
    <?php if ($selectedEmployeeId && $employeeImage): ?>
        <div class="employee-profile">
            <img src="<?php echo htmlspecialchars($employeeImage); ?>" alt="Profile Image of <?php echo htmlspecialchars($employeeName); ?>">
            <h3><?php echo htmlspecialchars($employeeName); ?></h3>
        </div>
    <?php endif; ?>

    <!-- Display Earnings Table -->
    <?php if ($selectedEmployeeId): ?>
        <table>
            <thead>
                <tr>
                    <th>Month</th>
                    <th>Earnings</th>
                    <th>Salary</th>
                </tr>
            </thead>
            <tbody>
                <?php
                // Display earnings and commission for each month
                for ($month = 1; $month <= 12; $month++) {
                    $earnings = isset($monthlyEarnings[$month]) ? number_format($monthlyEarnings[$month]['earnings'], 2) : '0.00';
                    $commission = isset($monthlyEarnings[$month]) ? number_format($monthlyEarnings[$month]['commission'], 2) : '0.00';

                    echo "<tr>";
                    echo "<td>" . date("F", mktime(0, 0, 0, $month, 1)) . "</td>";
                    echo "<td>$earnings</td>";
                    echo "<td>$commission</td>";
                    echo "</tr>";
                }
                ?>
            </tbody>
        </table>
    <?php else: ?>
        <p>Please select an employee to view their earnings.</p>
    <?php endif; ?>
    </div>
</body>
</html>
