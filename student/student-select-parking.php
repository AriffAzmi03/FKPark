<?php
session_start(); // Start the session
ob_start(); // Start output buffering

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

// Initialize variables
$bookingDate = '';
$bookingStart = '';
$bookingEnd = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $bookingDate = $_POST['bookingDate'];
    $bookingStart = $_POST['bookingStart'];
    $bookingEnd = $_POST['bookingEnd'];
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
                    <li class="breadcrumb-item active">Select Parking Spaces</li>
                </ol>
            </div>
        </div>

        <!-- Form to select date and time -->
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card mb-4">
                    <div class="card-header">
                        Select Date and Time
                    </div>
                    <div class="card-body">
                        <form method="POST">
                            <div class="form-group mb-3">
                                <label for="bookingDate">Booking Date</label>
                                <input type="date" required class="form-control" id="bookingDate" name="bookingDate" value="<?php echo $bookingDate; ?>">
                            </div>
                            <div class="form-group mb-3">
                                <label for="bookingStart">Time Start</label>
                                <input type="time" required class="form-control" id="bookingStart" name="bookingStart" value="<?php echo $bookingStart; ?>">
                            </div>
                            <div class="form-group mb-3">
                                <label for="bookingEnd">Time End</label>
                                <input type="time" required class="form-control" id="bookingEnd" name="bookingEnd" value="<?php echo $bookingEnd; ?>">
                            </div>
                            <button type="submit" class="btn btn-primary">Check Availability</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <?php if ($bookingDate && $bookingStart && $bookingEnd) { ?>
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
                                        <th style="width: 10%;">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    // Fetch available parking spaces
                                    $query = "
                                        SELECT ps.*
                                        FROM parkingspace ps
                                        WHERE ps.parkingAvailabilityStatus = 'Available'
                                        AND NOT EXISTS (
                                            SELECT 1
                                            FROM booking b
                                            WHERE b.parkingID = ps.parkingID
                                            AND b.bookingDate = ?
                                            AND (
                                                (b.bookingStart < ? AND b.bookingEnd > ?)
                                                OR (b.bookingStart < ? AND b.bookingEnd > ?)
                                                OR (b.bookingStart >= ? AND b.bookingEnd <= ?)
                                            )
                                        )
                                        ORDER BY RAND() LIMIT 1000";
                                    $stmt = $conn->prepare($query);
                                    $stmt->bind_param("sssssss", $bookingDate, $bookingEnd, $bookingStart, $bookingEnd, $bookingStart, $bookingStart, $bookingEnd);
                                    $stmt->execute();
                                    $res = $stmt->get_result();
                                    $cnt = 1;
                                    while ($row = $res->fetch_object()) {
                                    ?>
                                        <tr>
                                            <td><?php echo $cnt; ?></td>
                                            <td><?php echo $row->parkingID; ?></td>
                                            <td><?php echo $row->parkingArea; ?></td>
                                            <td><?php echo $row->parkingType; ?></td>
                                            <td>
                                                <a href="student-add-booking.php?parkingID=<?php echo $row->parkingID; ?>&bookingDate=<?php echo $bookingDate; ?>&bookingStart=<?php echo $bookingStart; ?>&bookingEnd=<?php echo $bookingEnd; ?>" class="btn btn-primary">Select</a>
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
        <?php } ?>
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

ob_end_flush(); // Flush the buffer and send output to the browser
?>
