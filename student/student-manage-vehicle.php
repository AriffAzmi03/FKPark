<?php
// Start the session
session_start();

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

// Handle delete request
if (isset($_GET['del'])) {
    $vehicleID = $_GET['del'];
    $delQuery = "DELETE FROM vehicle WHERE vehicleID = ? AND studentID = ?";
    $stmt = $conn->prepare($delQuery);
    $stmt->bind_param("is", $vehicleID, $studentID);

    if ($stmt->execute()) {
        $deleteMessage = "<div class='alert alert-success' role='alert'>Vehicle deleted successfully!</div>";
    } else {
        $deleteMessage = "<div class='alert alert-danger' role='alert'>Error: " . $stmt->error . "</div>";
    }

    // Close the statement
    $stmt->close();
}

// Retrieve vehicles registered by the student
$query = "SELECT vehicleID, vehicleType, vehicleBrand, vehicleColour, vehiclePlateNum FROM vehicle WHERE studentID = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("s", $studentID);
$stmt->execute();
$result = $stmt->get_result();
?>

<div id="content-wrapper">
    <div class="container-fluid mt-4">
        <!-- Breadcrumbs -->
        <div class="row justify-content-center">
            <div class="col-md-12">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item">
                        <a href="#">Vehicles</a>
                    </li>
                    <li class="breadcrumb-item active">Manage Vehicles</li>
                </ol>
            </div>
        </div>

        <!-- DataTables Example -->
        <div class="row justify-content-center">
            <div class="col-md-12">
                <div class="card mb-4">
                    <div class="card-header">
                        <i class="fas fa-car"></i>
                        My Vehicles
                    </div>
                    <div class="card-body">
                        <?php
                        // Display delete message if set
                        if (isset($deleteMessage)) {
                            echo $deleteMessage;
                        }
                        ?>
                        <div class="table-responsive">
                            <table class="table table-bordered table-hover table-striped" width="100%" cellspacing="0">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Vehicle Type</th>
                                        <th>Vehicle Brand</th>
                                        <th>Vehicle Colour</th>
                                        <th>Vehicle Plate Number</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $cnt = 1;
                                    while ($row = $result->fetch_assoc()) {
                                    ?>
                                    <tr>
                                        <td><?php echo $cnt++; ?></td>
                                        <td><?php echo $row['vehicleType']; ?></td>
                                        <td><?php echo $row['vehicleBrand']; ?></td>
                                        <td><?php echo $row['vehicleColour']; ?></td>
                                        <td><?php echo $row['vehiclePlateNum']; ?></td>
                                        <td>
                                            <a href="student-view-vehicle.php?vehicleID=<?php echo $row['vehicleID']; ?>" class="badge bg-primary text-white">View</a>
                                            <a href="student-edit-vehicle.php?vehicleID=<?php echo $row['vehicleID']; ?>" class="badge bg-success text-white">Edit</a>
                                            <a href="student-manage-vehicle.php?del=<?php echo $row['vehicleID']; ?>" class="badge bg-danger text-white" onclick="return confirm('Are you sure you want to delete this vehicle?');">Delete</a>
                                        </td>
                                    </tr>
                                    <?php
                                    }
                                    ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="card-footer small text-muted">
                        <?php
                        date_default_timezone_set("Asia/Kuala_Lumpur");
                        echo "Generated : " . date("h:i:sa");
                        ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- /.container-fluid -->

    <!-- Footer -->
    <?php
    // Include footer
    include('includes/footer.php');
    ?>
</div>
<!-- /.content-wrapper -->

<?php
// Include scripts
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

