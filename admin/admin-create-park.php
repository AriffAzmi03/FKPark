<?php
// Include header file
include('includes/header.php');

// Include database connection file
include('includes/dbconnection.php');

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['add_parking_space'])) {
    // Get form data
    $parkingSpaceName = $_POST['parkingSpaceName'];
    $parkingArea = $_POST['parkingArea'];
    $vehicleType = $_POST['vehicleType'];
    $availability = $_POST['availability'];

    // Prepare and execute the insert query
    $query = "INSERT INTO parking_spaces (parking_space_name, parking_area, vehicle_type, availability)
              VALUES (?, ?, ?, ?)";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("ssss", $parkingSpaceName, $parkingArea, $vehicleType, $availability);

    if ($stmt->execute()) {
        echo "<div class='alert alert-success' role='alert'>New parking space added successfully!</div>";
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
            <a href="#">Parking</a>
        </li>
        <li class="breadcrumb-item active">Add Parking Space</li>
    </ol>
    <hr>
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    Add Parking Space
                </div>
                <div class="card-body">
                    <!-- Add Parking Space Form -->
                    <form method="POST">
                        <div class="form-group mb-3">
                            <label for="parkingSpaceName">Parking Space Name</label>
                            <input type="text" required class="form-control" id="parkingSpaceName" name="parkingSpaceName">
                        </div>
                        <div class="form-group mb-3">
                            <label for="parkingArea">Parking Area</label>
                            <select class="form-control" id="parkingArea" name="parkingArea" required>
                                <option value="A">A</option>
                                <option value="B">B</option>
                            </select>
                        </div>
                        <div class="form-group mb-3">
                            <label for="vehicleType">Vehicle Type</label>
                            <select class="form-control" id="vehicleType" name="vehicleType" required>
                                <option value="None">None</option>
                                <option value="Car">Car</option>
                                <option value="Motorcycle">Motorcycle</option>
                                <option value="Others">Others</option>
                            </select>
                        </div>
                        <div class="form-group mb-3">
                            <label for="availability">Availability</label>
                            <select class="form-control" id="availability" name="availability" required>
                                <option value="Available">Available</option>
                                <option value="Unavailable">Unavailable</option>
                            </select>
                        </div>
                        <button type="submit" name="add_parking_space" class="btn btn-success">Add Parking Space</button>
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
