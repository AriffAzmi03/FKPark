<?php
// Include header file
include('includes/header.php');

// Include database connection file
include('includes/dbconnection.php');

// Retrieve students to populate the dropdown
$students_query = "SELECT studentID, studentName FROM student";
$students_result = $conn->query($students_query);

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['add_vehicle'])) {
    // Get form data
    $vehicleType = $_POST['vehicleType'];
    $vehicleBrand = $_POST['vehicleBrand'];
    $vehicleColour = $_POST['vehicleColour'];
    $vehiclePlateNum = $_POST['vehiclePlateNum'];
    $studentID = $_POST['studentID'];
    $vehicleGrant = null;

    // Handle file upload
    if (isset($_FILES['vehicleGrant']) && $_FILES['vehicleGrant']['error'] == 0) {
        $fileType = $_FILES['vehicleGrant']['type'];
        $allowedTypes = ['application/pdf', 'image/jpeg', 'image/png'];

        if (in_array($fileType, $allowedTypes)) {
            $vehicleGrant = file_get_contents($_FILES['vehicleGrant']['tmp_name']);
        } else {
            echo "<div class='alert alert-danger' role='alert'>Error: Invalid file type. Please upload a PDF, JPG, or PNG file.</div>";
            exit();
        }
    }

    // Check if studentID exists in the student table
    $check_student_query = "SELECT studentID FROM student WHERE studentID = ?";
    $stmt_check = $conn->prepare($check_student_query);
    $stmt_check->bind_param("s", $studentID);
    $stmt_check->execute();
    $stmt_check->store_result();

    if ($stmt_check->num_rows > 0) {
        // Prepare and execute the insert query
        $query = "INSERT INTO vehicle (vehicleType, vehicleBrand, vehicleColour, vehiclePlateNum, vehicleGrant, studentID, status)
                  VALUES (?, ?, ?, ?, ?, ?, 'pending')";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("ssssss", $vehicleType, $vehicleBrand, $vehicleColour, $vehiclePlateNum, $vehicleGrant, $studentID);

        if ($stmt->execute()) {
            echo "<div class='alert alert-success' role='alert'>New vehicle added successfully!</div>";
        } else {
            echo "<div class='alert alert-danger' role='alert'>Error: " . $stmt->error . "</div>";
        }

        // Close the statement
        $stmt->close();
    } else {
        echo "<div class='alert alert-danger' role='alert'>Error: Invalid student ID.</div>";
    }

    $stmt_check->close();
}

// Close the database connection
$conn->close();
?>

<div class="container mt-4">
    <!-- Breadcrumbs-->
    <ol class="breadcrumb">
        <li class="breadcrumb-item">
            <a href="#">Vehicles</a>
        </li>
        <li class="breadcrumb-item active">Vehicle Registration</li>
    </ol>
    <hr>
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    Vehicle Registration
                </div>
                <div class="card-body">
                    <!-- Add Vehicle Form -->
                    <form id="vehicleForm" method="POST" enctype="multipart/form-data" onsubmit="return validateForm()">
                        <div class="form-group mb-3">
                            <label for="vehicleType">Vehicle Type</label>
                            <select class="form-control" id="vehicleType" name="vehicleType" required>
                                <option value="">Select Vehicle Type</option>
                                <option value="Car">Car</option>
                                <option value="Motorcycle">Motorcycle</option>
                            </select>
                        </div>
                        <div class="form-group mb-3">
                            <label for="vehicleBrand">Vehicle Brand</label>
                            <input type="text" required class="form-control" id="vehicleBrand" name="vehicleBrand">
                        </div>
                        <div class="form-group mb-3">
                            <label for="vehicleColour">Vehicle Colour</label>
                            <input type="text" required class="form-control" id="vehicleColour" name="vehicleColour">
                        </div>
                        <div class="form-group mb-3">
                            <label for="vehiclePlateNum">Vehicle Plate Number</label>
                            <input type="text" required class="form-control" id="vehiclePlateNum" name="vehiclePlateNum">
                        </div>
                        <div class="form-group mb-3">
                            <label for="studentID">Student</label>
                            <select class="form-control" id="studentID" name="studentID" required>
                                <option value="">Select Student</option>
                                <?php
                                if ($students_result->num_rows > 0) {
                                    while($student = $students_result->fetch_assoc()) {
                                        echo "<option value='{$student['studentID']}'>{$student['studentName']} ({$student['studentID']})</option>";
                                    }
                                }
                                ?>
                            </select>
                        </div>
                        <div class="form-group mb-3">
                            <label for="vehicleGrant">Vehicle Grant</label>
                            <input type="file" class="form-control" id="vehicleGrant" name="vehicleGrant" accept=".pdf, image/jpeg, image/png" required>
                        </div>
                        <button type="submit" name="add_vehicle" class="btn btn-success">Add Vehicle</button>
                        <button type="reset" class="btn btn-secondary">Reset</button>
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

<script>
function validateForm() {
    var vehicleType = document.getElementById('vehicleType').value;
    var vehicleBrand = document.getElementById('vehicleBrand').value;
    var vehicleColour = document.getElementById('vehicleColour').value;
    var vehiclePlateNum = document.getElementById('vehiclePlateNum').value;
    var vehicleGrant = document.getElementById('vehicleGrant').files[0];
    var studentID = document.getElementById('studentID').value;

    if (!vehicleType || !vehicleBrand || !vehicleColour || !vehiclePlateNum || !vehicleGrant || !studentID) {
        alert('Please fill in all the required fields.');
        return false;
    }

    // Check file type
    var allowedTypes = ['application/pdf', 'image/jpeg', 'image/png'];
    if (allowedTypes.indexOf(vehicleGrant.type) === -1) {
        alert('Please upload a file in PDF, JPG, or PNG format.');
        return false;
    }

    return true;
}
</script>



