<?php
session_start();  // Start the session

// Check if user is logged in
if (!isset($_SESSION['username'])) {
    echo "<script>alert('Please login first.'); window.location.href = 'login.php';</script>";
    exit();
}

// Get the username from the session
$username = $_SESSION['username'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <?php include './cdn.php' ?>
    <link rel="stylesheet" href="./css/base.css">
</head>
<body>
<?php include './sidebar.php' ?>
    <h2>Welcome, <?php echo htmlspecialchars($username); ?>!</h2>
    <p>You are now logged in to the dashboard.</p>

    <form action="logout.php" method="POST">
        <button type="submit" name="logout">Logout</button>
    </form>
</body>
</html>
