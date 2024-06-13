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

// Get booking ID from URL
$bookingID = $_GET['bookingID'];

// Fetch booking details
$query = "SELECT * FROM booking WHERE bookingID = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("s", $bookingID);
$stmt->execute();
$result = $stmt->get_result();
$booking = $result->fetch_object();

// Close the statement
$stmt->close();
?>

<div class="container mt-4">
    <!-- Breadcrumbs-->
    <ol class="breadcrumb">
        <li class="breadcrumb-item">
            <a href="#">Booking</a>
        </li>
        <li class="breadcrumb-item active">View Booking</li>
    </ol>
    <hr>
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    Booking Details
                </div>
                <div class="card-body">
                    <?php if ($booking) { ?>
                        <div class="form-group mb-3">
                            <label for="bookingID">Booking ID</label>
                            <input type="text" class="form-control" id="bookingID" value="<?php echo $booking->bookingID; ?>" disabled>
                        </div>
                        <div class="form-group mb-3">
                            <label for="bookingDate">Booking Date</label>
                            <input type="text" class="form-control" id="bookingDate" value="<?php echo $booking->bookingDate; ?>" disabled>
                        </div>
                        <div class="form-group mb-3">
                            <label for="bookingStart">Time Start</label>
                            <input type="text" class="form-control" id="bookingStart" value="<?php echo $booking->bookingStart; ?>" disabled>
                        </div>
                        <div class="form-group mb-3">
                            <label for="bookingEnd">Time End</label>
                            <input type="text" class="form-control" id="bookingEnd" value="<?php echo $booking->bookingEnd; ?>" disabled>
                        </div>
                        <div class="form-group mb-3">
                            <label for="vehiclePlateNum">Vehicle Plate Number</label>
                            <input type="text" class="form-control" id="vehiclePlateNum" value="<?php echo $booking->vehiclePlateNum; ?>" disabled>
                        </div>
                        <a href="student-manage-booking.php" class="btn btn-primary">Back</a>
                    <?php } else { ?>
                        <div class="alert alert-danger" role="alert">No booking details found.</div>
                        <a href="student-manage-booking.php" class="btn btn-primary">Back</a>
                    <?php } ?>
                </div>
            </div>
        </div>
    </div>
</div>

<hr>

<?php
// Include footer and scripts
include('includes/footer.php');
include('includes/scripts.php');
?>
