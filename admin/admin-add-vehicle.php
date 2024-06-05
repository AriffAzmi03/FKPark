<?php
// Include header file
include('includes/header.php');

// Include database connection file
include('includes/dbconnection.php');

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
        $vehicleGrant = file_get_contents($_FILES['vehicleGrant']['tmp_name']);
    }

    // Prepare and execute the insert query
    $query = "INSERT INTO vehicle (vehicleType, vehicleBrand, vehicleColour, vehiclePlateNum, vehicleGrant)
              VALUES (?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("sssss", $vehicleType, $vehicleBrand, $vehicleColour, $vehiclePlateNum, $vehicleGrant);

    if ($stmt->execute()) {
        echo "<div class='alert alert-success' role='alert'>New vehicle added successfully!</div>";
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
                            <label for="vehicleGrant">Vehicle Grant</label>
                            <input type="file" class="form-control" id="vehicleGrant" name="vehicleGrant" required>
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
    var vehicleGrant = document.getElementById('vehicleGrant').value;

    if (!vehicleType || !vehicleBrand || !vehicleColour || !vehiclePlateNum || !vehicleGrant) {
        alert('Please fill in all the required fields.');
        return false;
    }

    return true;
}
</script>
