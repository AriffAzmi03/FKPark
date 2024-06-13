<?php
session_start();
// Include header file
include('includes/header.php');

// Include database connection file
include('includes/dbconnection.php');

// Check if the student is logged in
if (!isset($_SESSION['studentID'])) {
    header("Location: student-login.php");
    exit();
}

// Handle delete request
if (isset($_GET['del_start'])) {
    $bookingStart = $_GET['del_start'];
    $delQuery = "DELETE FROM booking WHERE bookingStart = ?";
    $stmt = $conn->prepare($delQuery);
    $stmt->bind_param("s", $bookingStart );
    
    if ($stmt->execute()) {
        $deleteMessage = "<div class='alert alert-success' role='alert'>Booking deleted successfully!</div>";
    } else {
        $deleteMessage = "<div class='alert alert-danger' role='alert'>Error: " . $stmt->error . "</div>";
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
                        <a href="#">Booking</a>
                    </li>
                    <li class="breadcrumb-item active">View Bookings</li>
                </ol>
            </div>
        </div>

        <!-- DataTables Example -->
        <div class="row justify-content-center">
            <div class="col-md-12">
                <div class="card mb-4">
                    <div class="card-header">
                        <i class="fas fa-calendar-alt"></i>
                        Bookings
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
                                        <th style="width: 20%;">Booking ID</th>
                                        <th style="width: 20%;">Booking Date</th>
                                        <th style="width: 20%;">Time Start</th>
                                        <th style="width: 20%;">Time End</th>
                                        <th style="width: 15%;">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $ret = "SELECT * FROM booking ORDER BY bookingDate DESC";
                                    $stmt = $conn->prepare($ret);
                                    $stmt->execute();
                                    $res = $stmt->get_result();
                                    $cnt = 1;
                                    while ($row = $res->fetch_object()) {
                                    ?>
                                        <tr>
                                            <td><?php echo $cnt; ?></td>
                                            <td><?php echo $row->bookingID; ?></td>
                                            <td><?php echo $row->bookingDate; ?></td>
                                            <td><?php echo $row->bookingStart; ?></td>
                                            <td><?php echo $row->bookingEnd; ?></td>
                                            <td>
                                                <a href="student-view-booking.php?bookingID=<?php echo $row->bookingID;?>" class="badge bg-info text-white"><i class="fas fa-eye"></i> View</a>
                                                <a href="student-manage-booking.php?del_start=<?php echo $row->bookingStart;?>" class="badge bg-danger text-white" onclick="return confirm('Are you sure you want to delete this booking?');"><i class="fas fa-trash-alt"></i> Delete</a>
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
