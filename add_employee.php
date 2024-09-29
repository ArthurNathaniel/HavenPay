<?php
include('db.php');
session_start();  // Start the session

// Check if user is logged in
if (!isset($_SESSION['username'])) {
    echo "<script>alert('Please login first.'); window.location.href = 'login.php';</script>";
    exit();
}

if (isset($_POST['register'])) {
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
    $gender = $_POST['gender'];  // Get the selected gender

    // Handle file upload for profile image
    $profile_image = $_FILES['profile_image']['name'];
    $target_dir = "uploads/";
    $target_file = $target_dir . basename($profile_image);
    $uploadOk = 1;
    $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

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
            // Insert into the database
            $sql = "INSERT INTO employees (first_name, middle_name, last_name, dob, phone_number, house_number, emergency_contact_name, emergency_contact_number, emergency_relationship, gender, profile_image) 
                    VALUES ('$first_name', '$middle_name', '$last_name', '$dob', '$phone_number', '$house_number', '$emergency_contact_name', '$emergency_contact_number', '$emergency_relationship', '$gender', '$target_file')";

            if ($conn->query($sql) === TRUE) {
                echo "<script>alert('Employee registered successfully!');</script>";
            } else {
                echo "<script>alert('Error: " . $sql . "<br>" . $conn->error . "');</script>";
            }
        } else {
            echo "<script>alert('Sorry, there was an error uploading your file.');</script>";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Employee</title>
    <?php include './cdn.php' ?>
    <link rel="stylesheet" href="./css/base.css">
    <link rel="stylesheet" href="./css/add_employee.css">
</head>

<body>
    <?php include './sidebar.php' ?>
    <div class="add_employee_all">
        <div class="forms_title">
            <h2>Add Employee</h2>
        </div>
        <form method="POST" action="add_employee.php" enctype="multipart/form-data">
            <div class="forms">
                <label for="profile_image">Profile Image:</label>
                <input type="file" name="profile_image" accept="image/*" required>
            </div>
            <div class="forms_groups">
                <div class="forms">
                    <label for="first_name">First Name:</label>
                    <input type="text" name="first_name" placeholder="Enter your first name" required>
                </div>
                <div class="forms">
                    <label for="middle_name">Middle Name:</label>
                    <input type="text" name="middle_name" placeholder="Enter your middle name">
                </div>
                <div class="forms">
                    <label for="last_name">Last Name:</label>
                    <input type="text" name="last_name" placeholder="Enter your last name" required>
                </div>
            </div>
            <div class="forms_groups">
                <div class="forms">
                    <label for="gender">Gender:</label>
                    <select name="gender" id="gender" required>
                        <option value="" selected hidden>Select Gender</option>
                        <option value="Male">Male</option>
                        <option value="Female">Female</option>
                    </select>
                </div>
                <div class="forms">
                    <label for="dob">Date of Birth:</label>
                    <input type="date" name="dob" placeholder="Enter your date of birth" required>
                </div>
                <div class="forms">
                    <label for="phone_number">Phone Number:</label>
                    <input type="text" name="phone_number" placeholder="Enter your phone number" required>
                </div>

            </div>

                <div class="forms">
                    <label for="house_number">House Number:</label>
                    <input type="text" name="house_number" placeholder="Enter your house number" required>
                </div>

            <div class="forms_groups">
                <div class="forms">
                    <label for="emergency_contact_name">Emergency Contact Name:</label>
                    <input type="text" name="emergency_contact_name" placeholder="Enter your emergency contact name" required>
                </div>
                <div class="forms">
                    <label for="emergency_contact_number">Emergency Contact Number:</label>
                    <input type="text" name="emergency_contact_number" placeholder="Enter your emergency contact number" required>
                </div>

                <div class="forms">
                    <label for="emergency_relationship">Emergency Relationship:</label>
                    <select name="emergency_relationship" id="emergency_relationship" required>
                    <option value="" selected hidden>Select Emergency Relationship</option>
                        <option value="Parent">Parent</option>
                        <option value="Friend">Friend</option>
                        <option value="Family">Family</option>
                        <option value="Guardian">Guardian</option>
                    </select>
                </div>

            </div>



            <div class="forms">
                <button type="submit" name="register">Register Employee</button>
            </div>
        </form>
    </div>
</body>

</html>