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
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Employees</title>
    <?php include './cdn.php' ?>
    <link rel="stylesheet" href="./css/base.css">
    <link rel="stylesheet" href="./css/view_employees.css">
  
</head>
<body>
<?php include './sidebar.php' ?>
    <div class="view_employees_all">
  <div class="forms">
  <h2>Employee List</h2>
  </div>
    <table>
        <thead>
            <tr>
                <th>Profile Image</th>
                <th>First Name</th>
                <th>Gender</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php
            if ($result->num_rows > 0) {
                while($row = $result->fetch_assoc()) {
                    echo "<tr>";
                    echo "<td><img src='" . $row['profile_image'] . "' alt='Profile Image' width='50' height='50'></td>";
                    echo "<td>" . htmlspecialchars($row['first_name']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['gender']) . "</td>";
                    echo "<td class='actions_btn'>
                            <button onclick='viewDetails(" . json_encode($row) . ")'><i class='fa-solid fa-eye'></i></button>
                            <a href='edit_employee.php?id=" . $row['id'] . "'><i class='fa-solid fa-user-pen'></i></a>
                            <a href='delete_employee.php?id=" . $row['id'] . "' onclick=\"return confirm('Are you sure you want to delete this employee?');\"><i class='fa-solid fa-trash'></i></a>
                          </td>";
                    echo "</tr>";
                }
            } else {
                echo "<tr><td colspan='4'>No employees found.</td></tr>";
            }
            ?>
        </tbody>
    </table>

    <!-- Modal for viewing employee details -->
    <div id="employeeModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeModal()">&times;</span>
            <h2>Employee Details</h2>
            <div id="modalBody"></div>
        </div>
    </div>
    </div>

    <script>
        function viewDetails(employee) {
            // Get the modal body element
            const modalBody = document.getElementById("modalBody");

            // Prepare the employee details
            const details = `
              <p><strong>Profile Image:</strong></p>
                <img src="${employee.profile_image}" width="100" height="100" alt="Profile Image">
                <p><strong>First Name:</strong> ${employee.first_name}</p>
                <p><strong>Middle Name:</strong> ${employee.middle_name}</p>
                <p><strong>Last Name:</strong> ${employee.last_name}</p>
                 <p><strong>Gender:</strong> ${employee.gender}</p>
                <p><strong>Date of Birth:</strong> ${employee.dob}</p>
                <p><strong>Phone Number:</strong> ${employee.phone_number}</p>
                <p><strong>House Number:</strong> ${employee.house_number}</p>
                <p><strong>Emergency Contact Name:</strong> ${employee.emergency_contact_name}</p>
                <p><strong>Emergency Contact Number:</strong> ${employee.emergency_contact_number}</p>
                <p><strong>Emergency Relationship:</strong> ${employee.emergency_relationship}</p>
            `;

            // Set the modal body content
            modalBody.innerHTML = details;

            // Display the modal
            document.getElementById("employeeModal").style.display = "block";
        }

        function closeModal() {
            document.getElementById("employeeModal").style.display = "none";
        }

        // Close the modal if user clicks anywhere outside of it
        window.onclick = function(event) {
            const modal = document.getElementById("employeeModal");
            if (event.target === modal) {
                closeModal();
            }
        }
    </script>
</body>
</html>
