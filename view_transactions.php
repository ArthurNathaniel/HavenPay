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
    <?php include './cdn.php' ?>
    <link rel="stylesheet" href="./css/base.css">
    <link rel="stylesheet" href="./css/view_transaction_all.css">
   
</head>

<body>
    <?php include './sidebar.php' ?>
    <div class="view_transaction_all">
      

        <!-- Search form -->
        <div class="forms">
            <input type="text" id="searchInput" placeholder="Search by Employee's First Name">
          
        </div>
        <div class="forms">
        <button onclick="searchByFirstName()">Search</button>
        </div>
        <div class="forms_title">
            <h2>Recorded Transactions</h2>
        </div>

        <table id="transactionsTable">
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
                            <td>" . htmlspecialchars(date('Y-m-d g:i A', strtotime($row['transaction_date']))) . "</td>
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
    </div>

    <!-- JavaScript for search functionality -->
    <script>
        function searchByFirstName() {
            // Get the input value
            var input = document.getElementById("searchInput").value.toLowerCase();
            
            // Get the table and rows
            var table = document.getElementById("transactionsTable");
            var rows = table.getElementsByTagName("tr");

            // Loop through the rows and hide those that don't match the search
            for (var i = 1; i < rows.length; i++) { // Start from index 1 to skip the header row
                var employeeCell = rows[i].getElementsByTagName("td")[2]; // Employee's full name column
                if (employeeCell) {
                    var firstName = employeeCell.textContent || employeeCell.innerText;
                    firstName = firstName.split(" ")[0].toLowerCase(); // Extract the first name
                    if (firstName.indexOf(input) > -1) {
                        rows[i].style.display = "";
                    } else {
                        rows[i].style.display = "none";
                    }
                }
            }
        }
    </script>
</body>

</html>
