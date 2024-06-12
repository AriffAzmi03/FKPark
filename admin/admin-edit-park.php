<?php
// Include header file
include('includes/header.php');

// Include database connection file
include('includes/dbconnection.php');

// Check if parkingID is set in the URL and fetch parking space details
if (isset($_GET['u_id'])) {
    $parkingID = $_GET['u_id'];
    
    // Fetch parking space details
    $query = "SELECT * FROM parkingspace WHERE parkingID = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $parkingID);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        $parking = $result->fetch_assoc();
    } else {
        echo "<div class='alert alert-danger' role='alert'>No parking space found with the given ID.</div>";
        exit;
    }

    // Close the statement
    $stmt->close();
} else {
    echo "<div class='alert alert-danger' role='alert'>No parking space ID provided.</div>";
    exit;
}

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['update_parking'])) {
    // Get form data
    $parkingAvailabilityStatus = $_POST['parkingAvailabilityStatus'];
    $parkingAddDetail = $_POST['parkingAddDetail'];

    // Prepare and execute the update query
    $query = "UPDATE parkingspace SET parkingAvailabilityStatus = ?, parkingAddDetail = ? WHERE parkingID = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("sss", $parkingAvailabilityStatus, $parkingAddDetail, $parkingID);

    if ($stmt->execute()) {
        echo "<div class='alert alert-success' role='alert'>Parking space updated successfully!</div>";
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
            <a href="admin-manage-area.php">Parking Spaces</a>
        </li>
        <li class="breadcrumb-item active">Parking Space Update</li>
    </ol>
    <hr>
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    Parking Space Update
                </div>
                <div class="card-body">
                    <!-- Update Parking Space Form -->
                    <form method="POST">
                        <div class="form-group mb-3">
                            <label for="parkingID">Parking Space Name</label>
                            <input type="text" class="form-control" id="parkingID" name="parkingID" value="<?php echo htmlspecialchars($parking['parkingID']); ?>" disabled>
                        </div>
                        <div class="form-group mb-3">
                            <label for="parkingArea">Parking Area</label>
                            <input type="text" class="form-control" id="parkingArea" name="parkingArea" value="<?php echo htmlspecialchars($parking['parkingArea']); ?>" disabled>
                        </div>
                        <div class="form-group mb-3">
                            <label for="parkingType">Type of Vehicle</label>
                            <input type="text" class="form-control" id="parkingType" name="parkingType" value="<?php echo htmlspecialchars($parking['parkingType']); ?>" disabled>
                        </div>
                        <div class="form-group mb-3">
                            <label for="parkingAvailabilityStatus">Availability</label>
                            <select class="form-control" id="parkingAvailabilityStatus" name="parkingAvailabilityStatus" required>
                                <option value="Available" <?php if ($parking['parkingAvailabilityStatus'] == 'Available') echo 'selected'; ?>>Available</option>
                                <option value="Unavailable" <?php if ($parking['parkingAvailabilityStatus'] == 'Unavailable') echo 'selected'; ?>>Unavailable</option>
                            </select>
                        </div>
                        <div class="form-group mb-3">
                            <label for="parkingAddDetail">Additional Notes</label>
                            <select class="form-control" id="parkingAddDetail" name="parkingAddDetail" required>
                                <option value="Not Applicable" <?php if ($parking['parkingAddDetail'] == 'Not Applicable') echo 'selected'; ?>>Not Applicable</option>
                                <option value="Maintenance" <?php if ($parking['parkingAddDetail'] == 'Maintenance') echo 'selected'; ?>>Maintenance</option>
                                <option value="Events" <?php if ($parking['parkingAddDetail'] == 'Events') echo 'selected'; ?>>Events</option>
                            </select>
                        </div>
                        <button type="submit" name="update_parking" class="btn btn-success">Update</button>
                        <a href="admin-manage-area.php" class="btn btn-secondary">Back</a>
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
