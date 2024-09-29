<?php
session_start();  // Start the session
include('db.php');

if (isset($_POST['login'])) {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Check if user exists
    $check_user = "SELECT * FROM users WHERE username='$username'";
    $result = $conn->query($check_user);

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();

        // Verify password
        if (password_verify($password, $row['password'])) {
            // Store the username in session
            $_SESSION['username'] = $username;
            echo "<script>alert('Login successful!'); window.location.href = 'dashboard.php';</script>";
        } else {
            echo "<script>alert('Incorrect password');</script>";
        }
    } else {
        echo "<script>alert('Username does not exist');</script>";
    }
}
?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <?php include './cdn.php' ?>
    <link rel="stylesheet" href="./css/base.css">
    <link rel="stylesheet" href="./css/login.css">
</head>

<body>
    <div class="login_all">
    <div class="login_box">
        <div class="logo"></div>
        <div class="forms_title">
            <h2>HavenPay - Login</h2>
        </div>
        <form method="POST" action="login.php">
            <div class="forms">
                <label>Username</label>
                <input type="text" name="username" placeholder="Enter your username" required>
            </div>
            <div class="forms">
                <label>Password</label>
                <input type="password" name="password" placeholder="Enter your  password" required>
            </div>
            <div class="forms">
                <button type="submit" name="login">Login</button>
            </div>
        </form>
    </div>
    </div>
</body>

</html>