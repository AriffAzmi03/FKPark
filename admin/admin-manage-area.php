<?php
// Include header file
include('includes/header.php');

// Include database connection file
include('includes/dbconnection.php');

// Handle deletion request
if (isset($_GET['del'])) {
    $parkingID = $_GET['del'];

    // Prepare and execute the delete query
    $query = "DELETE FROM parkingspace WHERE parkingID = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $parkingID);

    if ($stmt->execute()) {
        echo "<div class='alert alert-success' role='alert'>Parking space deleted successfully!</div>";
    } else {
        echo "<div class='alert alert-danger' role='alert'>Error: " . $stmt->error . "</div>";
    }

    // Close the statement
    $stmt->close();
}
?>

<div id="content-wrapper">
    <div class="container-fluid mt-4">
        <!-- Breadcrumbs -->
        <div class="row justify-content-center">
            <div class="col-md-12">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item">
                        <a href="#">Parking</a>
                    </li>
                    <li class="breadcrumb-item active">Manage Parking Spaces</li>
                </ol>
            </div>
        </div>

        <!-- DataTables Example -->
        <div class="row justify-content-center">
            <div class="col-md-12">
                <div class="card mb-4">
                    <div class="card-header">
                        <i class="fas fa-parking"></i>
                        Parking Spaces
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered table-hover table-striped" id="dataTable" width="100%" cellspacing="0">
                                <thead>
                                    <tr>
                                        <th style="width: 5%;">#</th>
                                        <th style="width: 25%;">Parking Space Name</th>
                                        <th style="width: 20%;">Parking Area</th>
                                        <th style="width: 25%;">Vehicle Type</th>
                                        <th style="width: 15%;">Availability</th>
                                        <th style="width: 10%;">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $ret = "SELECT * FROM parkingspace ORDER BY RAND() LIMIT 1000";
                                    $stmt = $conn->prepare($ret);
                                    $stmt->execute();
                                    $res = $stmt->get_result();
                                    $cnt = 1;
                                    while ($row = $res->fetch_object()) {
                                    ?>
                                        <tr>
                                            <td><?php echo $cnt; ?></td>
                                            <td><?php echo $row->parkingID; ?></td>
                                            <td><?php echo $row->parkingType; ?></td>
                                            <td><?php echo $row->vehicleType; ?></td>
                                            <td><?php echo $row->parkingAvailabilityStatus; ?></td>
                                            <td>
                                                <a href="admin-manage-single-parking.php?p_id=<?php echo $row->parkingID; ?>" class="badge bg-success text-white"><i class="fas fa-edit"></i> Update</a>
                                                <a href="admin-manage-parking.php?del=<?php echo $row->parkingID; ?>" class="badge bg-danger text-white"><i class="fas fa-trash-alt"></i> Delete</a>
                                            </td>
                                        </tr>
                                    <?php
                                        $cnt = $cnt + 1;
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
