<?php
// Include header file
include('includes/header.php');

// Include database connection file
include('includes/dbconnection.php');

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['add_user'])) {
    // Get form data from POST request
    $studentName = $_POST['studentName'];
    $studentID = $_POST['studentID'];
    $studentPhoneNum = $_POST['studentPhoneNum'];
    $studentAddress = $_POST['studentAddress'];
    $studentType = $_POST['studentType'];
    $studentYear = $_POST['studentYear'];
    $studentEmail = $_POST['studentEmail'];
    $studentPassword = $_POST['studentPassword']; // Store as plain text

    // Check if studentID already exists
    $checkQuery = "SELECT COUNT(*) FROM student WHERE studentID = ?";
    $checkStmt = $conn->prepare($checkQuery);
    $checkStmt->bind_param("s", $studentID);
    $checkStmt->execute();
    $checkStmt->bind_result($count);
    $checkStmt->fetch();
    $checkStmt->close();

    if ($count > 0) {
        echo "<div class='alert alert-danger' role='alert'>Error: Student ID already exists.</div>";
    } else {
        // Prepare and execute the insert query
        $query = "INSERT INTO student (studentName, studentID, studentPhoneNum, studentAddress, studentType, studentYear, studentEmail, studentPassword)
                  VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("ssssssss", $studentName, $studentID, $studentPhoneNum, $studentAddress, $studentType, $studentYear, $studentEmail, $studentPassword);

        // Check if the query executed successfully
        if ($stmt->execute()) {
            echo "<div class='alert alert-success' role='alert'>New user added successfully!</div>";
        } else {
            echo "<div class='alert alert-danger' role='alert'>Error: " . $stmt->error . "</div>";
        }

        // Close the statement
        $stmt->close();
    }
}

// Close the database connection
$conn->close();
?>

<div class="container mt-4">
    <!-- Breadcrumbs -->
    <ol class="breadcrumb">
        <li class="breadcrumb-item">
            <a href="#">Users</a>
        </li>
        <li class="breadcrumb-item active">User Registration</li>
    </ol>
    <hr>
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    User Registration
                </div>
                <div class="card-body">
                    <!-- Add User Form -->
                    <form method="POST">
                        <!-- Full Name Field -->
                        <div class="form-group mb-3">
                            <label for="studentName">Full Name</label>
                            <input type="text" required class="form-control" id="studentName" name="studentName">
                        </div>
                        <!-- Student ID Field -->
                        <div class="form-group mb-3">
                            <label for="studentID">Student ID</label>
                            <input type="text" class="form-control" id="studentID" name="studentID">
                        </div>
                        <!-- Phone Number Field -->
                        <div class="form-group mb-3">
                            <label for="studentPhoneNum">Phone Number</label>
                            <input type="tel" class="form-control" id="studentPhoneNum" name="studentPhoneNum">
                        </div>
                        <!-- Address Field -->
                        <div class="form-group mb-3">
                            <label for="studentAddress">Address</label>
                            <input type="text" class="form-control" id="studentAddress" name="studentAddress">
                        </div>
                        <!-- Level of Study Field -->
                        <div class="form-group mb-3">
                            <label for="studentType">Level Of Study</label>
                            <select class="form-control" id="studentType" name="studentType" required>
                                <option value="Undergraduate">Undergraduate</option>
                                <option value="Postgraduate">Postgraduate</option>
                            </select>
                        </div>
                        <!-- Year of Study Field -->
                        <div class="form-group mb-3">
                            <label for="studentYear">Year Of Study</label>
                            <input type="number" class="form-control" id="studentYear" name="studentYear">
                        </div>
                        <!-- Email Field -->
                        <div class="form-group mb-3">
                            <label for="studentEmail">Email</label>
                            <input type="email" class="form-control" id="studentEmail" name="studentEmail">
                        </div>
                        <!-- Password Field -->
                        <div class="form-group mb-3">
                            <label for="studentPassword">Password</label>
                            <input type="password" class="form-control" id="studentPassword" name="studentPassword">
                        </div>
                        <!-- Submit and Reset Buttons -->
                        <button type="submit" name="add_user" class="btn btn-primary">Add User</button>
                        <button type="reset" class="btn btn-light">Reset</button>
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
