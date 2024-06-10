<?php
// Include header file
include('includes/header.php');

// Include database connection file
include('includes/dbconnection.php');

// Handle delete request
if (isset($_GET['del'])) {
    $vehiclePlateNum = $_GET['del'];
    $delQuery = "DELETE FROM vehicle WHERE vehiclePlateNum = ?";
    $stmt = $conn->prepare($delQuery);
    $stmt->bind_param("s", $vehiclePlateNum);

    if ($stmt->execute()) {
        $deleteMessage = "<div class='alert alert-success' role='alert'>Vehicle deleted successfully!</div>";
    } else {
        $deleteMessage = "<div class='alert alert-danger' role='alert'>Error: " . $stmt->error . "</div>";
    }

    // Close the statement
    $stmt->close();
}

// Retrieve approved vehicles
$query = "SELECT v.vehicleType, v.vehicleBrand, v.vehicleColour, v.vehiclePlateNum, s.studentName 
          FROM vehicle v 
          JOIN student s ON v.studentID = s.studentID 
          WHERE v.status = 'approved'";
$result = $conn->query($query);
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
                        Approved Vehicles
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
                                        <th>Student Name</th>
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
                                        <td><?php echo $row['studentName']; ?></td>
                                        <td>
                                            <a href="admin-view-vehicle.php?vehiclePlateNum=<?php echo $row['vehiclePlateNum']; ?>" class="badge bg-primary text-white">View</a>
                                            <a href="admin-manage-vehicle.php?del=<?php echo $row['vehiclePlateNum']; ?>" class="badge bg-danger text-white" onclick="return confirm('Are you sure you want to delete this vehicle?');">Delete</a>
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