<?php
// Start the session
session_start();

// Check if the student is logged in
if (!isset($_SESSION['studentID'])) {
    header("Location: student-login.php");
    exit();
}

// Include header file
include('includes/header.php');

// Include database connection file
include('includes/dbconnection.php');

// Get the student ID from the session
$studentID = $_SESSION['studentID'];

// Retrieve student details
$query = "SELECT studentName, studentID, studentPhoneNum, studentAddress, studentType, studentYear, studentEmail FROM student WHERE studentID = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("s", $studentID);
$stmt->execute();
$result = $stmt->get_result();
$student = $result->fetch_assoc();
$stmt->close();

if (!$student) {
    echo "<div class='alert alert-danger' role='alert'>Error: Student not found.</div>";
    exit();
}

// Close the database connection
$conn->close();
?>

<div class="container mt-4">
    <!-- Breadcrumbs -->
    <ol class="breadcrumb">
        <li class="breadcrumb-item">
            <a href="student-dashboard.php">Dashboard</a>
        </li>
        <li class="breadcrumb-item active">View Profile</li>
    </ol>
    <hr>
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <span>Profile Information</span>
                    <a href="student-edit-profile.php" class="btn btn-success">Edit Profile</a>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <tbody>
                                <tr>
                                    <th>Name</th>
                                    <td><?php echo htmlspecialchars($student['studentName']); ?></td>
                                </tr>
                                <tr>
                                    <th>Student ID</th>
                                    <td><?php echo htmlspecialchars($student['studentID']); ?></td>
                                </tr>
                                <tr>
                                    <th>Phone Number</th>
                                    <td><?php echo htmlspecialchars($student['studentPhoneNum']); ?></td>
                                </tr>
                                <tr>
                                    <th>Address</th>
                                    <td><?php echo htmlspecialchars($student['studentAddress']); ?></td>
                                </tr>
                                <tr>
                                    <th>Level of Study</th>
                                    <td><?php echo htmlspecialchars($student['studentType']); ?></td>
                                </tr>
                                <tr>
                                    <th>Year of Study</th>
                                    <td><?php echo htmlspecialchars($student['studentYear']); ?></td>
                                </tr>
                                <tr>
                                    <th>Email</th>
                                    <td><?php echo htmlspecialchars($student['studentEmail']); ?></td>
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
