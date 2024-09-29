<?php
$current_page = basename($_SERVER['PHP_SELF']); // Get the current page name
?>

<div class="navbar_all">
    <button id="toggleButton">
        <i class="fa-solid fa-bars-staggered"></i>
    </button>
    <div class="logout">
            <a href="logout.php" class="<?= $current_page === 'logout.php' ? 'active' : '' ?>">Logout</a>
        </div>
    <div class="mobile">
        <div class="logo"></div>
        <div class="dashed"></div>
        <a href="dashboard.php" class="<?= $current_page === 'dashboard.php' ? 'active' : '' ?>">Dashboard</a>
        <a href="add_employee.php" class="<?= $current_page === 'add_employee.php' ? 'active' : '' ?>">Add Employee</a>

        <a href="view_employees.php" class="<?= $current_page === 'view_employees.php' ? 'active' : '' ?>">View Employees</a>

        <a href="record_transaction.php" class="<?= $current_page === 'record_transaction.php' ? 'active' : '' ?>">Record Transaction</a>

        <a href="view_transactions.php" class="<?= $current_page === 'view_transactions.php' ? 'active' : '' ?>">Recorded Transactions
        </a>
        <a href="weekly_earnings.php" class="<?= $current_page === 'weekly_earnings.php' ? 'active' : '' ?>"> Weekly Earnings
        </a>
        <a href="monthly_earnings.php" class="<?= $current_page === 'monthly_earnings.php' ? 'active' : '' ?>"> Monthly Earnings
        </a>
    </div>
</div>


<script>
    // Get the button and sidebar elements
    var toggleButton = document.getElementById("toggleButton");
    var sidebar = document.querySelector(".mobile");
    var icon = toggleButton.querySelector("i");

    // Add click event listener to the button
    toggleButton.addEventListener("click", function() {
        // Toggle the visibility of the sidebar
        if (sidebar.style.display === "none" || sidebar.style.display === "") {
            sidebar.style.display = "flex";
            sidebar.style.flexDirection = "column";
            icon.classList.remove("fa-bars-staggered");
            icon.classList.add("fa-xmark");
        } else {
            sidebar.style.display = "none";
            icon.classList.remove("fa-xmark");
            icon.classList.add("fa-bars-staggered");
        }
    });
</script>