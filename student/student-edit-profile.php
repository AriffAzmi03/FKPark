<?php
// Start the session
session_start();

// Check if the student is logged in
if (!isset($_SESSION['studentID'])) {
    header("Location: student-login.php");
    exit();
}

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

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['update_profile'])) {
    // Get form data
    $studentPhoneNum = $_POST['studentPhoneNum'];
    $studentAddress = $_POST['studentAddress'];
    $studentYear = $_POST['studentYear'];
    $studentEmail = $_POST['studentEmail'];
    $studentPassword = password_hash($_POST['studentPassword'], PASSWORD_BCRYPT);

    // Prepare and execute the update query
    $query = "UPDATE student SET studentPhoneNum = ?, studentAddress = ?, studentYear = ?, studentEmail = ?, studentPassword = ? WHERE studentID = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("ssssss", $studentPhoneNum, $studentAddress, $studentYear, $studentEmail, $studentPassword, $studentID);

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
            <a href="student-view-profile.php">Profile</a>
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
                        <!-- Student Name Field -->
                        <div class="form-group mb-3">
                            <label for="studentName">Name</label>
                            <input type="text" required class="form-control readonly-input" id="studentName" name="studentName" value="<?php echo htmlspecialchars($student['studentName']); ?>" readonly>
                        </div>
                        <!-- Student ID Field -->
                        <div class="form-group mb-3">
                            <label for="studentID">Student ID</label>
                            <input type="text" required class="form-control readonly-input" id="studentID" name="studentID" value="<?php echo htmlspecialchars($student['studentID']); ?>" readonly>
                        </div>
                        <!-- Student Phone Number Field -->
                        <div class="form-group mb-3">
                            <label for="studentPhoneNum">Phone Number</label>
                            <input type="text" required class="form-control" id="studentPhoneNum" name="studentPhoneNum" value="<?php echo htmlspecialchars($student['studentPhoneNum']); ?>">
                        </div>
                        <!-- Student Address Field -->
                        <div class="form-group mb-3">
                            <label for="studentAddress">Address</label>
                            <input type="text" required class="form-control" id="studentAddress" name="studentAddress" value="<?php echo htmlspecialchars($student['studentAddress']); ?>">
                        </div>
                        <!-- Student Level of Study Field -->
                        <div class="form-group mb-3">
                            <label for="studentType">Level of Study</label>
                            <input type="text" required class="form-control readonly-input" id="studentType" name="studentType" value="<?php echo htmlspecialchars($student['studentType']); ?>" readonly>
                        </div>
                        <!-- Student Year of Study Field -->
                        <div class="form-group mb-3">
                            <label for="studentYear">Year of Study</label>
                            <input type="text" required class="form-control" id="studentYear" name="studentYear" value="<?php echo htmlspecialchars($student['studentYear']); ?>">
                        </div>
                        <!-- Student Email Field -->
                        <div class="form-group mb-3">
                            <label for="studentEmail">Email</label>
                            <input type="email" required class="form-control" id="studentEmail" name="studentEmail" value="<?php echo htmlspecialchars($student['studentEmail']); ?>">
                        </div>
                        <!-- Student Password Field -->
                        <div class="form-group mb-3">
                            <label for="studentPassword">Password</label>
                            <input type="password" required class="form-control" id="studentPassword" name="studentPassword">
                        </div>
                        <!-- Submit Button -->
                        <button type="submit" name="update_profile" class="btn btn-primary">Update Profile</button>
                    </form>
                    <!-- End Form -->
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

<!-- Custom CSS to ensure proper table layout and darken readonly inputs -->
<style>
.readonly-input {
    background-color: #e9ecef;
    opacity: 1; /* Ensure background color is not faded */
}
</style>
