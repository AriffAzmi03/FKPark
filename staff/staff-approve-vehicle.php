<?php
// Include header file
include('includes/header.php');

// Include database connection file
include('includes/dbconnection.php');

// Handle approval/rejection
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['vehicle_id'])) {
    $vehicle_id = $_POST['vehicle_id'];
    $status = $_POST['status'];

    $query = "UPDATE vehicle SET status = ? WHERE vehicleID = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("si", $status, $vehicle_id);

    if ($stmt->execute()) {
        echo "<div class='alert alert-success' role='alert'>Vehicle status updated successfully!</div>";
    } else {
        echo "<div class='alert alert-danger' role='alert'>Error: " . $stmt->error . "</div>";
    }

    // Close the statement
    $stmt->close();
}

// Retrieve pending vehicles
$query = "SELECT v.vehicleID, v.vehicleType, v.vehicleBrand, v.vehicleColour, v.vehiclePlateNum, s.studentName 
          FROM vehicle v 
          JOIN student s ON v.studentID = s.studentID 
          WHERE v.status = 'pending'";
$result = $conn->query($query);
?>

<div class="container mt-4">
    <ol class="breadcrumb">
        <li class="breadcrumb-item">
            <a href="#">Vehicles</a>
        </li>
        <li class="breadcrumb-item active">Approve Vehicle</li>
    </ol>
    <hr>
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card">
                <div class="card-header">
                    Pending Vehicle Approvals
                </div>
                <div class="card-body">
                    <?php if ($result->num_rows > 0) { ?>
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover table-striped">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Vehicle Type</th>
                                    <th>Vehicle Brand</th>
                                    <th>Vehicle Colour</th>
                                    <th>Vehicle Plate Number</th>
                                    <th>Student Name</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php while ($row = $result->fetch_assoc()) { ?>
                                <tr>
                                    <td><?php echo $row['vehicleID']; ?></td>
                                    <td><?php echo $row['vehicleType']; ?></td>
                                    <td><?php echo $row['vehicleBrand']; ?></td>
                                    <td><?php echo $row['vehicleColour']; ?></td>
                                    <td><?php echo $row['vehiclePlateNum']; ?></td>
                                    <td><?php echo $row['studentName']; ?></td>
                                    <td>
                                        <form method="POST" style="display:inline;">
                                            <input type="hidden" name="vehicle_id" value="<?php echo $row['vehicleID']; ?>">
                                            <input type="hidden" name="status" value="approved">
                                            <button type="submit" class="btn btn-success btn-sm">Approve</button>
                                        </form>
                                        <form method="POST" style="display:inline;">
                                            <input type="hidden" name="vehicle_id" value="<?php echo $row['vehicleID']; ?>">
                                            <input type="hidden" name="status" value="rejected">
                                            <button type="submit" class="btn btn-danger btn-sm">Reject</button>
                                        </form>
                                    </td>
                                </tr>
                                <?php } ?>
                            </tbody>
                        </table>
                    </div>
                    <?php } else { ?>
                    <div class="alert alert-info" role="alert">No pending vehicle approvals.</div>
                    <?php } ?>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
// Include footer and scripts
include('includes/footer.php');
include('includes/scripts.php');
?>
