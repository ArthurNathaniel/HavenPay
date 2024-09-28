<?php
include('db.php');
session_start();  // Start the session

// Check if user is logged in
if (!isset($_SESSION['username'])) {
    echo "<script>alert('Please login first.'); window.location.href = 'login.php';</script>";
    exit();
}

// Check if the ID is set in the URL
if (isset($_GET['id'])) {
    $id = intval($_GET['id']);  // Get the employee ID and convert it to an integer

    // Prepare the SQL statement to select the profile image before deletion
    $sql = "SELECT profile_image FROM employees WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $profile_image_path = $row['profile_image'];

        // Delete the employee record
        $delete_sql = "DELETE FROM employees WHERE id = ?";
        $delete_stmt = $conn->prepare($delete_sql);
        $delete_stmt->bind_param("i", $id);

        // Execute the delete statement
        if ($delete_stmt->execute()) {
            // Check if the file exists and delete it
            if (file_exists($profile_image_path)) {
                unlink($profile_image_path);
            }
            echo "<script>alert('Employee deleted successfully!'); window.location.href = 'view_employees.php';</script>";
        } else {
            echo "<script>alert('Error deleting employee: " . $conn->error . "'); window.location.href = 'view_employees.php';</script>";
        }

        // Close the delete statement
        $delete_stmt->close();
    } else {
        echo "<script>alert('Employee not found.'); window.location.href = 'view_employees.php';</script>";
    }

    // Close the result set
    $result->close();
} else {
    echo "<script>alert('Invalid request.'); window.location.href = 'view_employees.php';</script>";
}

// Close the database connection
$conn->close();
?>
