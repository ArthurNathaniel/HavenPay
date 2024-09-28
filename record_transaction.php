<?php
include('db.php');
session_start();  // Start the session

// Check if user is logged in
if (!isset($_SESSION['username'])) {
    echo "<script>alert('Please login first.'); window.location.href = 'login.php';</script>";
    exit();
}

// Fetch employees from the database
$sql = "SELECT * FROM employees";
$result = $conn->query($sql);

// Handle form submission
if (isset($_POST['record_transaction'])) {
    $employee_id = $_POST['employee_id'];
    $service = $_POST['service'];
    $client_name = $_POST['client_name'];
    $amount = $_POST['amount'];

    // Insert transaction into the database
    $sql = "INSERT INTO transactions (employee_id, service, client_name, amount) 
            VALUES ('$employee_id', '$service', '$client_name', '$amount')";

    if ($conn->query($sql) === TRUE) {
        echo "<script>alert('Transaction recorded successfully!');</script>";
    } else {
        echo "<script>alert('Error: " . $sql . "<br>" . $conn->error . "');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Record Transaction</title>
</head>
<body>
    <h2>Record Transaction</h2>
    <form method="POST" action="record_transaction.php">
        <label for="transaction_date">Date:</label>
        <input type="date" name="transaction_date" id="transaction_date" required><br><br>

        <label for="employee_id">Select Employee:</label>
        <select name="employee_id" id="employee_id" required>
            <option value="">Select an employee</option>
            <?php
            if ($result->num_rows > 0) {
                while($row = $result->fetch_assoc()) {
                    echo "<option value='" . $row['id'] . "'>" . htmlspecialchars($row['first_name'] . " " . $row['last_name']) . "</option>";
                }
            } else {
                echo "<option value=''>No employees found</option>";
            }
            ?>
        </select><br><br>

        <label for="service">Service:</label>
        <input type="text" name="service" id="service" placeholder="Enter service" required><br><br>

        <label for="client_name">Client Name:</label>
        <input type="text" name="client_name" id="client_name" placeholder="Enter client name" required><br><br>

        <label for="amount">Amount:</label>
        <input type="number" name="amount" id="amount" placeholder="Enter amount" step="0.01" required><br><br>

        <button type="submit" name="record_transaction">Record Transaction</button>
    </form>
</body>
</html>
