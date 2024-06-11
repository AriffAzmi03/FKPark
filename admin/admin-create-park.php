<?php
ob_start(); // Start output buffering

// Include header file
include('includes/header.php');

// Include database connection file
include('includes/dbconnection.php');

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['add_parking_space'])) {
    // Get form data
    $parkingID = $_POST['parkingID'];
    $parkingArea = $_POST['parkingArea'];
    $parkingType = $_POST['parkingType'];
    $parkingAvailabilityStatus = $_POST['parkingAvailabilityStatus'];
    $parkingAddDetail = $_POST['parkingAddDetail'];

    // Prepare and execute the insert query
    $query = "INSERT INTO parkingspace (parkingID, parkingArea, parkingType, parkingAvailabilityStatus, parkingAddDetail)
              VALUES (?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("sssss", $parkingID, $parkingArea, $parkingType, $parkingAvailabilityStatus, $parkingAddDetail);

    if ($stmt->execute()) {
        // Redirect to admin-generate-park.php with the last inserted parkingID
        header("Location: admin-generate-park.php?parkingID=" . $parkingID);
        exit(); // Ensure no further code is executed
    } else {
        echo "<div class='alert alert-danger' role='alert'>Error: " . $stmt->error . "</div>";
    }

    // Close the statement
    $stmt->close();
}

// Close the database connection
$conn->close();
ob_end_flush(); // End output buffering and flush output
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
                            <label for="parkingID">Parking Space Name</label>
                            <input type="text" required class="form-control" id="parkingID" name="parkingID">
                        </div>
                        <div class="form-group mb-3">
                            <label for="parkingArea">Parking Area</label>
                            <select class="form-control" id="parkingArea" name="parkingArea" required>
                                <option value="A">A</option>
                                <option value="B">B</option>
                            </select>
                        </div>
                        <div class="form-group mb-3">
                            <label for="parkingType">Vehicle Type</label>
                            <select class="form-control" id="parkingType" name="parkingType" required>
                                <option value="Car">Car</option>
                                <option value="Motorcycle">Motorcycle</option>
                            </select>
                        </div>
                        <div class="form-group mb-3">
                            <label for="parkingAvailabilityStatus">Availability</label>
                            <select class="form-control" id="parkingAvailabilityStatus" name="parkingAvailabilityStatus" required>
                                <option value="Available">Available</option>
                                <option value="Unavailable">Unavailable</option>
                            </select>
                        </div>
                        <div class="form-group mb-3">
                            <label for="parkingAddDetail">Additional Notes</label>
                            <input type="text" required class="form-control" id="parkingAddDetail" name="parkingAddDetail">
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
