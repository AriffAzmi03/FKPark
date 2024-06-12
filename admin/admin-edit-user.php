<?php
// Include header file
include('includes/header.php');

// Include database connection file
include('includes/dbconnection.php');

// Check if studentID is set in the URL and fetch user details
if (isset($_GET['u_id'])) {
    $studentID = $_GET['u_id'];
    
    // Fetch user details
    $query = "SELECT * FROM student WHERE studentID = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $studentID);
    $stmt->execute();
    $result = $stmt->get_result();
    
    // Check if a user is found
    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
    } else {
        echo "<div class='alert alert-danger' role='alert'>No user found with the given ID.</div>";
        exit;
    }

    // Close the statement
    $stmt->close();
} else {
    echo "<div class='alert alert-danger' role='alert'>No user ID provided.</div>";
    exit;
}

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['update_user'])) {
    // Get form data
    $studentName = $_POST['studentName'];
    $studentPhoneNum = $_POST['studentPhoneNum'];
    $studentAddress = $_POST['studentAddress'];
    $studentType = $_POST['studentType'];
    $studentYear = $_POST['studentYear'];
    $studentEmail = $_POST['studentEmail'];
    $studentPassword = $_POST['studentPassword']; // Store as plain text

    // Prepare and execute the update query
    $query = "UPDATE student SET studentName = ?, studentPhoneNum = ?, studentAddress = ?, studentType = ?, studentYear = ?, studentEmail = ?, studentPassword = ? WHERE studentID = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("ssssssss", $studentName, $studentPhoneNum, $studentAddress, $studentType, $studentYear, $studentEmail, $studentPassword, $studentID);

    // Check if the query executed successfully
    if ($stmt->execute()) {
        echo "<div class='alert alert-success' role='alert'>User updated successfully!</div>";
    } else {
        echo "<div class='alert alert-danger' role='alert'>Error: " . $stmt->error . "</div>";
    }

    // Close the statement
    $stmt->close();
}

// Close the database connection
$conn->close();
?>

<div class="container mt-4">
    <!-- Breadcrumbs-->
    <ol class="breadcrumb">
        <li class="breadcrumb-item">
            <a href="admin-manage-user.php">Users</a>
        </li>
        <li class="breadcrumb-item active">User Update</li>
    </ol>
    <hr>
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <span>User Update</span>
                    <a href="admin-manage-user.php" class="btn btn-secondary">Back</a>
                </div>
                <div class="card-body">
                    <!-- Update User Form -->
                    <form method="POST">
                        <!-- Full Name Field -->
                        <div class="form-group mb-3">
                            <label for="studentName">Full Name</label>
                            <input type="text" required class="form-control readonly-input" id="studentName" name="studentName" value="<?php echo $user['studentName']; ?>" readonly>
                        </div>
                        <!-- Student ID Field -->
                        <div class="form-group mb-3">
                            <label for="studentID">Student ID</label>
                            <input type="text" class="form-control readonly-input" id="studentID" name="studentID" value="<?php echo $user['studentID']; ?>" readonly>
                        </div>
                        <!-- Phone Number Field -->
                        <div class="form-group mb-3">
                            <label for="studentPhoneNum">Phone Number</label>
                            <input type="tel" class="form-control" id="studentPhoneNum" name="studentPhoneNum" value="<?php echo $user['studentPhoneNum']; ?>">
                        </div>
                        <!-- Address Field -->
                        <div class="form-group mb-3">
                            <label for="studentAddress">Address</label>
                            <input type="text" class="form-control" id="studentAddress" name="studentAddress" value="<?php echo $user['studentAddress']; ?>">
                        </div>
                        <!-- Level of Study Field -->
                        <div class="form-group mb-3">
                            <label for="studentType">Level Of Study</label>
                            <select class="form-control" id="studentType" name="studentType" required>
                                <option value="Undergraduate" <?php if ($user['studentType'] == 'Undergraduate') echo 'selected'; ?>>Undergraduate</option>
                                <option value="Postgraduate" <?php if ($user['studentType'] == 'Postgraduate') echo 'selected'; ?>>Postgraduate</option>
                            </select>
                        </div>
                        <!-- Year of Study Field -->
                        <div class="form-group mb-3">
                            <label for="studentYear">Year Of Study</label>
                            <input type="number" class="form-control" id="studentYear" name="studentYear" value="<?php echo $user['studentYear']; ?>">
                        </div>
                        <!-- Email Field -->
                        <div class="form-group mb-3">
                            <label for="studentEmail">Email</label>
                            <input type="email" class="form-control" id="studentEmail" name="studentEmail" value="<?php echo $user['studentEmail']; ?>">
                        </div>
                        <!-- Password Field -->
                        <div class="form-group mb-3">
                            <label for="studentPassword">Password</label>
                            <input type="password" class="form-control" id="studentPassword" name="studentPassword" value="<?php echo $user['studentPassword']; ?>">
                        </div>
                        <!-- Submit Button -->
                        <button type="submit" name="update_user" class="btn btn-primary">Update</button>
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
    .readonly-input {
        background-color: #e9ecef;
        opacity: 1; /* Ensure background color is not faded */
    }
</style>