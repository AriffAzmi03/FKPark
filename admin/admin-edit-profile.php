<?php
// Start the session
session_start();

// Check if the admin is logged in
if (!isset($_SESSION['adminID'])) {
    header("Location: admin-login.php");
    exit();
}

// Include database connection file
include('includes/dbconnection.php');

// Get the admin ID from the session
$adminID = $_SESSION['adminID'];

// Retrieve admin details
$query = "SELECT adminName, adminID, adminPhoneNum, adminEmail FROM admin WHERE adminID = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("s", $adminID);
$stmt->execute();
$result = $stmt->get_result();
$admin = $result->fetch_assoc();
$stmt->close();

if (!$admin) {
    echo "<div class='alert alert-danger' role='alert'>Error: Admin not found.</div>";
    exit();
}

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['update_profile'])) {
    // Get form data
    $adminPhoneNum = $_POST['adminPhoneNum'];
    $adminEmail = $_POST['adminEmail'];
    $adminPassword = password_hash($_POST['adminPassword'], PASSWORD_BCRYPT);

    // Prepare and execute the update query
    $query = "UPDATE admin SET adminPhoneNum = ?, adminEmail = ?, adminPassword = ? WHERE adminID = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("ssss", $adminPhoneNum, $adminEmail, $adminPassword, $adminID);

    if ($stmt->execute()) {
        $_SESSION['success_message'] = "Profile updated successfully!";
    } else {
        $_SESSION['error_message'] = "Error: " . $stmt->error;
    }

    // Close the statement
    $stmt->close();
}

// Close the database connection
$conn->close();

// Include header file
include('includes/header.php');
?>

<div class="container mt-4">
    <!-- Breadcrumbs -->
    <ol class="breadcrumb">
        <li class="breadcrumb-item">
            <a href="admin-view-profile.php">Profile</a>
        </li>
        <li class="breadcrumb-item active">Edit Profile</li>
    </ol>
    <hr>
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    Edit Profile
                </div>
                <div class="card-body">
                    <!-- Display success or error message -->
                    <?php
                    if (isset($_SESSION['success_message'])) {
                        echo "<div class='alert alert-success' role='alert'>" . $_SESSION['success_message'] . "</div>";
                        unset($_SESSION['success_message']);
                    }
                    if (isset($_SESSION['error_message'])) {
                        echo "<div class='alert alert-danger' role='alert'>" . $_SESSION['error_message'] . "</div>";
                        unset($_SESSION['error_message']);
                    }
                    ?>
                    <!-- Edit Profile Form -->
                    <form method="POST">
                        <!-- Admin Name Field -->
                        <div class="form-group mb-3">
                            <label for="adminName">Name</label>
                            <input type="text" required class="form-control readonly-input" id="adminName" name="adminName" value="<?php echo htmlspecialchars($admin['adminName']); ?>" readonly>
                        </div>
                        <!-- Admin ID Field -->
                        <div class="form-group mb-3">
                            <label for="adminID">Admin ID</label>
                            <input type="text" required class="form-control readonly-input" id="adminID" name="adminID" value="<?php echo htmlspecialchars($admin['adminID']); ?>" readonly>
                        </div>
                        <!-- Admin Phone Number Field -->
                        <div class="form-group mb-3">
                            <label for="adminPhoneNum">Phone Number</label>
                            <input type="text" required class="form-control" id="adminPhoneNum" name="adminPhoneNum" value="<?php echo htmlspecialchars($admin['adminPhoneNum']); ?>">
                        </div>
                        <!-- Admin Email Field -->
                        <div class="form-group mb-3">
                            <label for="adminEmail">Email</label>
                            <input type="email" required class="form-control" id="adminEmail" name="adminEmail" value="<?php echo htmlspecialchars($admin['adminEmail']); ?>">
                        </div>
             
