<?php
// Include header file
include('includes/header.php');

// Include database connection file
include('includes/dbconnection.php');

// Handle delete request
if (isset($_GET['del'])) {
    $parkingID = $_GET['del'];
    $delQuery = "DELETE FROM parkingspace WHERE parkingID = ?";
    $stmt = $conn->prepare($delQuery);
    $stmt->bind_param("s", $parkingID);
    
    if ($stmt->execute()) {
        $deleteMessage = "<div class='alert alert-success' role='alert'>Parking space deleted successfully!</div>";
    } else {
        $deleteMessage = "<div class='alert alert-danger' role='alert'>Error: " . $stmt->error . "</div>";
    }

    // Close the statement
    $stmt->close();
}

// Handle search request
$searchQuery = "";
if (isset($_GET['search'])) {
    $searchQuery = $_GET['search'];
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
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <div>
                            <i class="fas fa-parking"></i>
                            Parking Spaces
                        </div>
                        <form class="form-inline d-flex" method="get" action="">
                            <input class="form-control mr-2" type="search" placeholder="Search" aria-label="Search" name="search" value="<?php echo htmlspecialchars($searchQuery); ?>">
                            <button class="btn btn-outline-success" type="submit">Search</button>
                        </form>
                    </div>
                    <div class="card-body">
                        <?php
                        // Display delete message if set
                        if (isset($deleteMessage)) {
                            echo $deleteMessage;
                        }
                        ?>
                        <div class="table-responsive">
                            <table class="table table-bordered table-hover table-striped" id="dataTable" width="100%" cellspacing="0">
                                <thead>
                                    <tr>
                                        <th style="width: 5%;">#</th>
                                        <th style="width: 15%;">Parking Space Name</th>
                                        <th style="width: 15%;">Parking Area</th>
                                        <th style="width: 15%;">Vehicle Type</th>
                                        <th style="width: 15%;">Availability</th>
                                        <th style="width: 20%;">Additional Notes</th>
                                        <th style="width: 15%;">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $query = "SELECT * FROM parkingspace";
                                    if (!empty($searchQuery)) {
                                        $query .= " WHERE parkingID LIKE ? OR parkingArea LIKE ? OR parkingType LIKE ?";
                                    }
                                    $query .= " ORDER BY parkingCreatedAt DESC LIMIT 1000";
                                    
                                    $stmt = $conn->prepare($query);
                                    if (!empty($searchQuery)) {
                                        $searchParam = "%" . $searchQuery . "%";
                                        $stmt->bind_param("sss", $searchParam, $searchParam, $searchParam);
                                    }
                                    $stmt->execute();
                                    $res = $stmt->get_result();
                                    $cnt = 1;
                                    while ($row = $res->fetch_object()) {
                                    ?>
                                        <tr>
                                            <td><?php echo $cnt; ?></td>
                                            <td><?php echo htmlspecialchars($row->parkingID); ?></td>
                                            <td><?php echo htmlspecialchars($row->parkingArea); ?></td>
                                            <td><?php echo htmlspecialchars($row->parkingType); ?></td>
                                            <td><?php echo htmlspecialchars($row->parkingAvailabilityStatus); ?></td>
                                            <td><?php echo htmlspecialchars(isset($row->parkingAddDetail) ? $row->parkingAddDetail : ''); ?></td>
                                            <td>
                                                <a href="admin-edit-park.php?u_id=<?php echo $row->parkingID; ?>" class="badge bg-success text-white"><i class="fas fa-user-edit"></i> Update</a>
                                                <a href="admin-manage-area.php?del=<?php echo $row->parkingID; ?>" class="badge bg-danger text-white" onclick="return confirm('Are you sure you want to delete this parking space?');"><i class="fas fa-trash-alt"></i> Delete</a>
                                                <a href="admin-view-park.php?parkingID=<?php echo $row->parkingID; ?>" class="badge bg-info text-white"><i class="fas fa-eye"></i> View</a>
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
