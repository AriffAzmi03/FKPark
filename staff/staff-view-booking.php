<?php
// Include header file
include('includes/header.php');

// Include database connection file
include('includes/dbconnection.php');

// Include QR code library
include('../phpqrcode/qrlib.php');

// Retrieve booking details
if (isset($_GET['bookingID'])) {
    $bookingID = $_GET['bookingID'];

    // Fetch booking details including parkingArea and parkingID
    $query = "SELECT b.*, ps.parkingArea, ps.parkingID AS parkingSpaceID 
              FROM booking b
              INNER JOIN parkingspace ps ON b.parkingID = ps.parkingID
              WHERE b.bookingID = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $bookingID);
    $stmt->execute();
    $result = $stmt->get_result();
    $booking = $result->fetch_object();
    $stmt->close();

    if ($booking) {
        // Generate QR code
        $qrText = "Booking ID: " . $booking->bookingID . "\nBooking Date: " . $booking->bookingDate . "\nTime Start: " . $booking->bookingStart . "\nTime End: " . $booking->bookingEnd . "\nVehicle Plate Number: " . $booking->vehiclePlateNum . "\nParking Area: " . $booking->parkingArea . "\nParking Space ID: " . $booking->parkingSpaceID;

        // Set path for saving QR code image
        $qrImagePath = '../imageQR/booking' . $booking->bookingID . '.png';

        // Generate QR code image
        QRcode::png($qrText, $qrImagePath, QR_ECLEVEL_L, 4);
    } else {
        echo "<div class='alert alert-danger' role='alert'>No booking details found.</div>";
        exit();
    }
} else {
    echo "<div class='alert alert-danger' role='alert'>Invalid request.</div>";
    exit();
}
?>

<div class="container mt-4">
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <span>Booking Information</span>
            <a href="staff-manage-booking.php" class="btn btn-secondary">Back</a>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered">
                    <tbody>
                        <tr>
                            <th>Booking ID</th>
                            <td><?php echo htmlspecialchars($booking->bookingID); ?></td>
                        </tr>
                        <tr>
                            <th>Booking Date</th>
                            <td><?php echo htmlspecialchars($booking->bookingDate); ?></td>
                        </tr>
                        <tr>
                            <th>Time Start</th>
                            <td><?php echo htmlspecialchars($booking->bookingStart); ?></td>
                        </tr>
                        <tr>
                            <th>Time End</th>
                            <td><?php echo htmlspecialchars($booking->bookingEnd); ?></td>
                        </tr>
                        <tr>
                            <th>Vehicle Plate Number</th>
                            <td><?php echo htmlspecialchars($booking->vehiclePlateNum); ?></td>
                        </tr>
                        <tr>
                            <th>Parking Area</th>
                            <td><?php echo htmlspecialchars($booking->parkingArea); ?></td>
                        </tr>
                        <tr>
                            <th>Parking Space ID</th>
                            <td><?php echo htmlspecialchars($booking->parkingSpaceID); ?></td>
                        </tr>
                        <tr>
                            <th>QR Code</th>
                            <td><img src="<?php echo $qrImagePath; ?>" alt="QR Code"></td>
                        </tr>
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

<?php
// Include footer and scripts
include('includes/footer.php');
include('includes/scripts.php');
?>
