<?php
session_start(); // Start the session

// Check if studentID session variable is not set
if (!isset($_SESSION['studentID'])) {
    // Redirect to the login page
    header("Location: student-login.php");
    exit(); // Terminate the script
}

// Include header file
include('includes/header.php');

// Include database connection file
include('includes/dbconnection.php');

// Function to delete expired bookings
function deleteExpiredBookings($conn) {
    $currentDateTime = date('Y-m-d H:i:s');
    
    // Start a transaction
    $conn->begin_transaction();
    try {
        // Get the list of expired bookings
        $query = "SELECT bookingID FROM booking WHERE bookingEnd < ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("s", $currentDateTime);
        $stmt->execute();
        $result = $stmt->get_result();

        while ($row = $result->fetch_assoc()) {
            // Delete the expired booking
            $delQuery = "DELETE FROM booking WHERE bookingID = ?";
            $stmtDel = $conn->prepare($delQuery);
            $stmtDel->bind_param("s", $row['bookingID']);
            if (!$stmtDel->execute()) {
                throw new Exception($stmtDel->error);
            }
        }

        // Commit the transaction
        $conn->commit();
    } catch (Exception $e) {
        // Rollback the transaction if something went wrong
        $conn->rollback();
        echo "<div class='alert alert-danger' role='alert'>Error: " . $e->getMessage() . "</div>";
    }

    // Close the statement
    $stmt->close();
}

// Call the function to delete expired bookings
deleteExpiredBookings($conn);

// Handle delete request
if (isset($_GET['del_start'])) {
    $bookingStart = $_GET['del_start'];
    $studentID = $_SESSION['studentID'];

    // Start a transaction
    $conn->begin_transaction();

    try {
        // Get the parkingID from the booking to be deleted
        $query = "SELECT parkingID FROM booking WHERE bookingStart = ? AND studentID = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("ss", $bookingStart, $studentID);
        $stmt->execute();
        $stmt->bind_result($parkingID);
        $stmt->fetch();
        $stmt->close();

        if (!$parkingID) {
            throw new Exception("Booking not found or you don't have permission to delete this booking.");
        }

        // Delete the booking
        $delQuery = "DELETE FROM booking WHERE bookingStart = ? AND studentID = ?";
        $stmt = $conn->prepare($delQuery);
        $stmt->bind_param("ss", $bookingStart, $studentID);
        if (!$stmt->execute()) {
            throw new Exception($stmt->error);
        }

        // Commit the transaction
        $conn->commit();
        $deleteMessage = "<div class='alert alert-success' role='alert'>Booking deleted successfully!</div>";
    } catch (Exception $e) {
        // Rollback the transaction if something went wrong
        $conn->rollback();
        $deleteMessage = "<div class='alert alert-danger' role='alert'>Error: " . $e->getMessage() . "</div>";
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
                                        <th style="width: 10%;">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $studentID = $_SESSION['studentID'];
                                    $ret = "SELECT * FROM booking WHERE studentID = ? ORDER BY bookingDate DESC";
                                    $stmt = $conn->prepare($ret);
                                    $stmt->bind_param("s", $studentID);
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
                                                <a href="student-manage-booking.php?del_start=<?php echo $row->bookingStart;?>" class="badge bg-danger text-white" onclick="return confirm('Are you sure you want to delete this booking?');"><i class="fas fa-trash-alt"></i> Delete</a>
                                            </td>
                                        </tr>
                                    <?php
                                        $cnt++;
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
