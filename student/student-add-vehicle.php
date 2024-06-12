<?php
// Start the session
session_start();

ob_start(); // Start output buffering

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

include('../phpqrcode/qrlib.php');

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['add_vehicle'])) {
    // Get form data
    $vehicleType = $_POST['vehicleType'];
    $vehicleBrand = $_POST['vehicleBrand'];
    $vehicleColour = $_POST['vehicleColour'];
    $vehiclePlateNum = $_POST['vehiclePlateNum'];
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


    if ($stmt->execute()) {
        // Prepare the URL for the QR code
        $vehicleLink = "http://localhost/FKPark/admin/admin-view-vehicle.php?parkingID=$parkingID";

        // Create QR Code directory if it does not exist
        $qrCodeDir = "../imageQR";
        if (!is_dir($qrCodeDir)) {
            mkdir($qrCodeDir, 0755, true);
        }

        // Generate QR Code with the full URL
        $qrCodeFile = $qrCodeDir . "/parking" . $parkingID . ".png";
        QRcode::png($parkingLink, $qrCodeFile, QR_ECLEVEL_L, 5);

        // Redirect to admin-generate-park.php with the last inserted parkingID
        header("Location: admin-view-vehicle.php?parkingID=" . $parkingID);
        exit(); // Ensure no further code is executed
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
    <!-- Breadcrumbs -->
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
                        <!-- Vehicle Type Field -->
                        <div class="form-group mb-3">
                            <label for="vehicleType">Vehicle Type</label>
                            <select class="form-control" id="vehicleType" name="vehicleType" required>
                                <option value="">Select Vehicle Type</option>
                                <option value="Car">Car</option>
                                <option value="Motorcycle">Motorcycle</option>
                            </select>
                        </div>
                        <!-- Vehicle Brand Field -->
                        <div class="form-group mb-3">
                            <label for="vehicleBrand">Vehicle Brand</label>
                            <input type="text" required class="form-control" id="vehicleBrand" name="vehicleBrand">
                        </div>
                        <!-- Vehicle Colour Field -->
                        <div class="form-group mb-3">
                            <label for="vehicleColour">Vehicle Colour</label>
                            <input type="text" required class="form-control" id="vehicleColour" name="vehicleColour">
                        </div>
                        <!-- Vehicle Plate Number Field -->
                        <div class="form-group mb-3">
                            <label for="vehiclePlateNum">Vehicle Plate Number</label>
                            <input type="text" required class="form-control" id="vehiclePlateNum" name="vehiclePlateNum">
                        </div>
                        <!-- Vehicle Grant Field -->
                        <div class="form-group mb-3">
                            <label for="vehicleGrant">Vehicle Grant</label>
                            <input type="file" class="form-control" id="vehicleGrant" name="vehicleGrant" accept=".pdf, image/jpeg, image/png" required>
                        </div>
                        <!-- Submit and Reset Buttons -->
                        <button type="submit" name="add_vehicle" class="btn btn-primary">Add Vehicle</button>
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

<!-- Form Validation Script -->
<script>
function validateForm() {
    var vehicleType = document.getElementById('vehicleType').value;
    var vehicleBrand = document.getElementById('vehicleBrand').value;
    var vehicleColour = document.getElementById('vehicleColour').value;
    var vehiclePlateNum = document.getElementById('vehiclePlateNum').value;
    var vehicleGrant = document.getElementById('vehicleGrant').files[0];

    // Check if all required fields are filled
    if (!vehicleType || !vehicleBrand || !vehicleColour || !vehiclePlateNum || !vehicleGrant) {
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
