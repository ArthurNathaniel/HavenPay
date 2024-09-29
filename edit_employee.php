<?php
include('db.php');
session_start();  // Start the session

// Check if user is logged in
if (!isset($_SESSION['username'])) {
    echo "<script>alert('Please login first.'); window.location.href = 'login.php';</script>";
    exit();
}

// Fetch the employee's current data based on the provided ID
if (isset($_GET['id'])) {
    $id = $_GET['id'];
    $sql = "SELECT * FROM employees WHERE id = $id";
    $result = $conn->query($sql);
    $employee = $result->fetch_assoc();
} else {
    echo "<script>alert('No employee selected.'); window.location.href = 'view_employees.php';</script>";
    exit();
}

if (isset($_POST['update'])) {
    // Get form data
    $first_name = $_POST['first_name'];
    $middle_name = $_POST['middle_name'];
    $last_name = $_POST['last_name'];
    $dob = $_POST['dob'];
    $phone_number = $_POST['phone_number'];
    $house_number = $_POST['house_number'];
    $emergency_contact_name = $_POST['emergency_contact_name'];
    $emergency_contact_number = $_POST['emergency_contact_number'];
    $emergency_relationship = $_POST['emergency_relationship'];
    $gender = $_POST['gender'];

    // Handle file upload for profile image
    $profile_image = $_FILES['profile_image']['name'];
    $target_dir = "uploads/";
    $target_file = $target_dir . basename($profile_image);
    $uploadOk = 1;
    $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

    // Check if a new image is uploaded
    if ($profile_image) {
        // Check if image file is a actual image or fake image
        $check = getimagesize($_FILES['profile_image']['tmp_name']);
        if ($check === false) {
            echo "<script>alert('File is not an image.');</script>";
            $uploadOk = 0;
        }

        // Check file size (e.g., limit to 2MB)
        if ($_FILES['profile_image']['size'] > 2000000) {
            echo "<script>alert('Sorry, your file is too large.');</script>";
            $uploadOk = 0;
        }

        // Allow certain file formats
        if ($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg") {
            echo "<script>alert('Sorry, only JPG, JPEG, & PNG files are allowed.');</script>";
            $uploadOk = 0;
        }

        // Check if $uploadOk is set to 0 by an error
        if ($uploadOk == 0) {
            echo "<script>alert('Sorry, your file was not uploaded.');</script>";
        } else {
            // If everything is ok, try to upload file
            if (move_uploaded_file($_FILES['profile_image']['tmp_name'], $target_file)) {
                // Update the database with the new image
                $sql = "UPDATE employees SET first_name = '$first_name', middle_name = '$middle_name', last_name = '$last_name', 
                        dob = '$dob', phone_number = '$phone_number', house_number = '$house_number', 
                        emergency_contact_name = '$emergency_contact_name', emergency_contact_number = '$emergency_contact_number', 
                        emergency_relationship = '$emergency_relationship', gender = '$gender', 
                        profile_image = '$target_file' WHERE id = $id";
            } else {
                echo "<script>alert('Sorry, there was an error uploading your file.');</script>";
            }
        }
    } else {
        // Update without changing the profile image
        $sql = "UPDATE employees SET first_name = '$first_name', middle_name = '$middle_name', last_name = '$last_name', 
                dob = '$dob', phone_number = '$phone_number', house_number = '$house_number', 
                emergency_contact_name = '$emergency_contact_name', emergency_contact_number = '$emergency_contact_number', 
                emergency_relationship = '$emergency_relationship', gender = '$gender' WHERE id = $id";
    }

    if ($conn->query($sql) === TRUE) {
        echo "<script>alert('Employee updated successfully!'); window.location.href = 'view_employees.php';</script>";
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
    <title>Edit Employee</title>
    <?php include './cdn.php' ?>
    <link rel="stylesheet" href="./css/base.css">
    <link rel="stylesheet" href="./css/add_employee.css">
</head>

<body>
    <?php include './sidebar.php' ?>
    <div class="add_employee_all">
        <div class="forms_title">
            <h2>Edit Employee</h2>
        </div>
        <form method="POST" action="edit_employee.php?id=<?php echo $employee['id']; ?>" enctype="multipart/form-data">
            <div class="forms">
            <label>Profile Image:</label>
                <input type="file" name="profile_image" accept="image/*">
                <img src="<?php echo $employee['profile_image']; ?>" alt="Profile Image" width="100" height="100">
            </div>
         <div class="forms_groups">
         <div class="forms">
            <label>First Name:</label>
                <input type="text" name="first_name" placeholder="First Name" value="<?php echo htmlspecialchars($employee['first_name']); ?>" required>

            </div>
          
            <div class="forms">
            <label>Middle Name:</label>
                <input type="text" name="middle_name" placeholder="Middle Name" value="<?php echo htmlspecialchars($employee['middle_name']); ?>">
            </div>

            <div class="forms">
            <label>Last Name:</label>
                <input type="text" name="last_name" placeholder="Last Name" value="<?php echo htmlspecialchars($employee['last_name']); ?>" required>
            </div>
         </div>
            <div class="forms_groups">
            <div class="forms">
                <label for="gender">Gender:</label>
                <select name="gender" id="gender" required>
                    <option value="Male" <?php if ($employee['gender'] == 'Male') echo 'selected'; ?>>Male</option>
                    <option value="Female" <?php if ($employee['gender'] == 'Female') echo 'selected'; ?>>Female</option>
                </select>
            </div>
            <div class="forms">
            <label>Date of Birth:</label>
                <input type="date" name="dob" value="<?php echo htmlspecialchars($employee['dob']); ?>" required>
            </div>

            <div class="forms">
            <label>Phone Number:</label>
                <input type="text" name="phone_number" placeholder="Phone Number" value="<?php echo htmlspecialchars($employee['phone_number']); ?>" required>
            </div>
            </div>
            <div class="forms">
            <label>House Number:</label>
                <input type="text" name="house_number" placeholder="House Number" value="<?php echo htmlspecialchars($employee['house_number']); ?>" required>
            </div>

           <div class="forms_groups">
           <div class="forms">
            <label>Emergency Contact Name:</label>
                <input type="text" name="emergency_contact_name" placeholder="Emergency Contact Name" value="<?php echo htmlspecialchars($employee['emergency_contact_name']); ?>" required>
            </div>

            <div class="forms">
            <label>Emergency Contact Number:</label>
                <input type="text" name="emergency_contact_number" placeholder="Emergency Contact Number" value="<?php echo htmlspecialchars($employee['emergency_contact_number']); ?>" required>
            </div>
            <div class="forms">
                <label for="emergency_relationship">Emergency Relationship:</label>
                <select name="emergency_relationship" id="emergency_relationship" required>
                    <option value="Parent" <?php if ($employee['emergency_relationship'] == 'Parent') echo 'selected'; ?>>Parent</option>
                    <option value="Friend" <?php if ($employee['emergency_relationship'] == 'Friend') echo 'selected'; ?>>Friend</option>
                    <option value="Family" <?php if ($employee['emergency_relationship'] == 'Family') echo 'selected'; ?>>Family</option>
                    <option value="Guardian" <?php if ($employee['emergency_relationship'] == 'Guardian') echo 'selected'; ?>>Guardian</option>
                </select>
            </div>
           </div>

          

            <div class="forms">
                <button type="submit" name="update">Update Employee</button>
            </div>
        </form>
    </div>
</body>

</html>