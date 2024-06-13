<?php
// Start the session
session_start();

// Include database connection file
include('includes/dbconnection.php');

// Check if plateNum is set
if (!isset($_GET['plateNum'])) {
    echo "<div class='alert alert-danger' role='alert'>Error: Vehicle Plate Number not provided.</div>";
    exit();
}

// Get the vehicle plate number from the URL
$vehiclePlateNum = $_GET['plateNum'];

// Retrieve vehicle details
$query = "SELECT vehicleType, vehicleBrand, vehicleColour, vehiclePlateNum, vehicleGrant FROM vehicle WHERE vehiclePlateNum = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("s", $vehiclePlateNum);
$stmt->execute();
$result = $stmt->get_result();
$vehicle = $result->fetch_assoc();
$stmt->close();

if (!$vehicle) {
    echo "<div class='alert alert-danger' role='alert'>Error: Vehicle not found.</div>";
    exit();
}

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['update_vehicle'])) {
    // Get form data
    $vehicleColour = $_POST['vehicleColour'];

    // Prepare and execute the update query
    $query = "UPDATE vehicle SET vehicleColour = ? WHERE vehiclePlateNum = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("ss", $vehicleColour, $vehiclePlateNum);

    if ($stmt->execute()) {
        $_SESSION['success_message'] = "Vehicle updated successfully!";
        header("Location: admin-manage-vehicle.php");
        exit();
    } else {
        echo "<div class='alert alert-danger' role='alert'>Error: " . $stmt->error . "</div>";
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
            <a href="admin-manage-vehicle.php">Vehicles</a>
        </li>
        <li class="breadcrumb-item active">Edit Vehicle</li>
    </ol>
    <hr>
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <span>Edit Vehicle</span>
                    <a href="admin-manage-vehicle.php" class="btn btn-secondary">Back</a>
                </div>
                <div class="card-body">
                    <!-- Edit Vehicle Form -->
                    <form method="POST">
                        <!-- Vehicle Type Field -->
                        <div class="form-group mb-3">
                            <label for="vehicleType">Vehicle Type</label>
                            <input type="text" required class="form-control readonly-input" id="vehicleType" name="vehicleType" value="<?php echo htmlspecialchars($vehicle['vehicleType']); ?>" readonly>
                        </div>
                        <!-- Vehicle Brand Field -->
                        <div class="form-group mb-3">
                            <label for="vehicleBrand">Vehicle Brand</label>
                            <input type="text" required class="form-control readonly-input" id="vehicleBrand" name="vehicleBrand" value="<?php echo htmlspecialchars($vehicle['vehicleBrand']); ?>" readonly>
                        </div>
                        <!-- Vehicle Colour Field -->
                        <div class="form-group mb-3">
                            <label for="vehicleColour">Vehicle Colour</label>
                            <input type="text" required class="form-control" id="vehicleColour" name="vehicleColour" value="<?php echo htmlspecialchars($vehicle['vehicleColour']); ?>">
                        </div>
                        <!-- Vehicle Plate Number Field -->
                        <div class="form-group mb-3">
                            <label for="vehiclePlateNum">Vehicle Plate Number</label>
                            <input type="text" required class="form-control readonly-input" id="vehiclePlateNum" name="vehiclePlateNum" value="<?php echo htmlspecialchars($vehicle['vehiclePlateNum']); ?>" readonly>
                        </div>
                        <!-- Submit Button -->
                        <button type="submit" name="update_vehicle" class="btn btn-primary">Update Vehicle</button>
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
