<?php
// Start the session
session_start();

// Debug: Check session status
if (!isset($_SESSION['adminID'])) {
    echo "Session expired or not set.";
    exit();
}

// Include header file
include('includes/header.php');

// Include database connection file
include('includes/dbconnection.php');

// Get the admin ID from the session
$adminID = $_SESSION['adminID'];

// Debug: Check if adminID is set
if (!$adminID) {
    echo "Admin ID not found in session.";
    exit();
}

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

// Close the database connection
$conn->close();
?>

<div class="container mt-4">
    <!-- Breadcrumbs -->
    <ol class="breadcrumb">
        <li class="breadcrumb-item">
            <a href="admin-dashboard.php">Dashboard</a>
        </li>
        <li class="breadcrumb-item active">View Profile</li>
    </ol>
    <hr>
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <span>Profile Information</span>
                    <a href="admin-edit-profile.php" class="btn btn-success">Edit Profile</a>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <tbody>
                                <tr>
                                    <th>Name</th>
                                    <td><?php echo htmlspecialchars($admin['adminName']); ?></td>
                                </tr>
                                <tr>
                                    <th>Admin ID</th>
                                    <td><?php echo htmlspecialchars($admin['adminID']); ?></td>
                                </tr>
                                <tr>
                                    <th>Phone Number</th>
                                    <td><?php echo htmlspecialchars($admin['adminPhoneNum']); ?></td>
                                </tr>
                                <tr>
                                    <th>Email</th>
                                    <td><?php echo htmlspecialchars($admin['adminEmail']); ?></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="card-footer small text-muted">
                    <?php
                    date_default_timezone_set("Asia/Kuala_Lumpur");
                    echo "Profile viewed on: " . date("h:i:sa");
                    ?>
                </div>
            </div>
        </div>
    </div>
</div>

<hr>

<?php
// Include footer and scripts
include('includes/footer.php');
include('includes/scripts.php');
?>

<!-- Custom CSS to ensure proper table layout -->
<style>
.table-responsive table {
    table-layout: auto; /* Adjusted to auto for better column width management */
    width: 100%;
}
.table-responsive th, .table-responsive td {
    word-wrap: break-word;
}
.table th, .table td {
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}
</style>
