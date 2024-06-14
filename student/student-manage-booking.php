<?php
session_start();
// Include header file
include('includes/header.php');

// Include database connection file
include('includes/dbconnection.php');

// Include QR code library
include('../phpqrcode/qrlib.php');

// Check if the student is logged in
if (!isset($_SESSION['studentID'])) {
    header("Location: student-login.php");
    exit();
}

// Get the student ID from the session
$studentID = $_SESSION['studentID'];

// Handle delete request
if (isset($_GET['del_start'])) {
    $bookingStart = $_GET['del_start'];
    $delQuery = "DELETE FROM booking WHERE bookingStart = ? AND studentID = ?";
    $stmt = $conn->prepare($delQuery);
    $stmt->bind_param("ss", $bookingStart, $studentID);
    
    if ($stmt->execute()) {
        $deleteMessage = "<div class='alert alert-success' role='alert'>Booking deleted successfully!</div>";
    } else {
        $deleteMessage = "<div class='alert alert-danger' role='alert'>Error: " . $stmt->error . "</div>";
    }

    // Close the statement
    $stmt->close();
}

// Generate QR code for each booking
function generateQRCode($bookingID) {
    $tempDir = '../qrcodes/';
    if (!file_exists($tempDir)) {
        mkdir($tempDir, 0777, true);
    }
    $codeContents = "http://localhost/FKPark/student/student-view-booking.php?bookingID=" . $bookingID;
    $fileName = 'qrcode_' . $bookingID . '.png';
    $pngAbsoluteFilePath = $tempDir . $fileName;
    if (!file_exists($pngAbsoluteFilePath)) {
        QRcode::png($codeContents, $pngAbsoluteFilePath, QR_ECLEVEL_L, 4);
    }
    return $pngAbsoluteFilePath;
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

                        // Display update message if set
                        if (isset($_SESSION['updateMessage'])) {
                            echo $_SESSION['updateMessage'];
                            unset($_SESSION['updateMessage']);
                        }
                        ?>
                        <div class="table-responsive">
                            <table class="table table-bordered table-hover table-striped" id="dataTable" width="100%" cellspacing="0">
                                <thead>
                                    <tr>
                                        <th style="width: 5%;">#</th>
                                        <th style="width: 15%;">Booking ID</th>
                                        <th style="width: 15%;">Booking Date</th>
                                        <th style="width: 15%;">Time Start</th>
                                        <th style="width: 15%;">Time End</th>
                                        <th style="width: 20%;">QR Code</th>
                                        <th style="width: 15%;">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $ret = "SELECT * FROM booking WHERE studentID = ? ORDER BY bookingDate DESC";
                                    $stmt = $conn->prepare($ret);
                                    $stmt->bind_param("s", $studentID);
                                    $stmt->execute();
                                    $res = $stmt->get_result();
                                    $cnt = 1;
                                    while ($row = $res->fetch_object()) {
                                        $qrCodePath = generateQRCode($row->bookingID);
                                    ?>
                                        <tr>
                                            <td><?php echo $cnt; ?></td>
                                            <td><?php echo htmlspecialchars($row->bookingID); ?></td>
                                            <td><?php echo htmlspecialchars($row->bookingDate); ?></td>
                                            <td><?php echo htmlspecialchars($row->bookingStart); ?></td>
                                            <td><?php echo htmlspecialchars($row->bookingEnd); ?></td>
                                            <td><img src="<?php echo $qrCodePath; ?>" alt="QR Code" width="100" height="100"></td>
                                            <td>
                                                <a href="student-view-booking.php?bookingID=<?php echo htmlspecialchars($row->bookingID); ?>" class="btn btn-primary btn-sm"><i class="fas fa-eye"></i> View</a>
                                                <a href="student-update-booking.php?bookingID=<?php echo htmlspecialchars($row->bookingID); ?>" class="btn btn-success btn-sm text-white"><i class="fas fa-edit"></i> Update</a>
                                                <a href="student-manage-booking.php?del_start=<?php echo htmlspecialchars($row->bookingStart); ?>" class="btn btn-danger btn-sm text-white" onclick="return confirm('Are you sure you want to delete this booking?');"><i class="fas fa-trash-alt"></i> Delete</a>
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
