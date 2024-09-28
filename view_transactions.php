<?php
include('db.php');
session_start();  // Start the session

// Check if user is logged in
if (!isset($_SESSION['username'])) {
    echo "<script>alert('Please login first.'); window.location.href = 'login.php';</script>";
    exit();
}

// Fetch transactions from the database
$sql = "SELECT t.id, t.transaction_date, e.first_name, e.last_name, t.service, t.client_name, t.amount
        FROM transactions t
        JOIN employees e ON t.employee_id = e.id
        ORDER BY t.transaction_date DESC";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Transactions</title>
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
    <h2>Recorded Transactions</h2>
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Date</th>
                <th>Employee</th>
                <th>Service</th>
                <th>Client Name</th>
                <th>Amount</th>
            </tr>
        </thead>
        <tbody>
            <?php
            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    echo "<tr>
                            <td>" . htmlspecialchars($row['id']) . "</td>
                            <td>" . htmlspecialchars(date('Y-m-d H:i:s', strtotime($row['transaction_date']))) . "</td>
                            <td>" . htmlspecialchars($row['first_name'] . " " . $row['last_name']) . "</td>
                            <td>" . htmlspecialchars($row['service']) . "</td>
                            <td>" . htmlspecialchars($row['client_name']) . "</td>
                            <td>" . htmlspecialchars(number_format($row['amount'], 2)) . "</td>
                          </tr>";
                }
            } else {
                echo "<tr><td colspan='6'>No transactions found</td></tr>";
            }
            ?>
        </tbody>
    </table>
</body>
</html>
