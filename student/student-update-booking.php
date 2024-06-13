<?php
session_start();
ob_start(); // Start output buffering

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
$booking = $result->fetch_assoc();
$stmt->close();

// Handle update request
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $bookingDate = $_POST['bookingDate'];
    $bookingStart = $_POST['bookingStart'];
    $bookingEnd = $_POST['bookingEnd'];

    // Update booking details
    $updateQuery = "UPDATE booking SET bookingDate = ?, bookingStart = ?, bookingEnd = ? WHERE bookingID = ?";
    $stmt = $conn->prepare($updateQuery);
    $stmt->bind_param("ssss", $bookingDate, $bookingStart, $bookingEnd, $bookingID);

    if ($stmt->execute()) {
        // Redirect back to manage booking page with success message
        $_SESSION['updateMessage'] = "<div class='alert alert-success' role='alert'>Booking updated successfully!</div>";
        header("Location: student-manage-booking.php");
        exit();
    } else {
        $updateMessage = "<div class='alert alert-danger' role='alert'>Error: " . $stmt->error . "</div>";
    }

    $stmt->close();
    // Refresh booking details
    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $bookingID);
    $stmt->execute();
    $result = $stmt->get_result();
    $booking = $result->fetch_assoc();
    $stmt->close();
}
?>

<div class="container mt-4">
    <!-- Breadcrumbs -->
    <ol class="breadcrumb">
        <li class="breadcrumb-item">
            <a href="#">Booking</a>
        </li>
        <li class="breadcrumb-item active">Update Booking</li>
    </ol>
    <hr>
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    Update Booking
                </div>
                <div class="card-body">
                    <?php if (isset($updateMessage)) echo $updateMessage; ?>
                    <form method="POST">
                        <div class="form-group mb-3">
                            <label for="bookingDate">Booking Date</label>
                            <input type="date" class="form-control" id="bookingDate" name="bookingDate" value="<?php echo htmlspecialchars($booking['bookingDate']); ?>" required>
                        </div>
                        <div class="form-group mb-3">
                            <label for="bookingStart">Time Start</label>
                            <input type="time" class="form-control" id="bookingStart" name="bookingStart" value="<?php echo htmlspecialchars($booking['bookingStart']); ?>" required>
                        </div>
                        <div class="form-group mb-3">
                            <label for="bookingEnd">Time End</label>
                            <input type="time" class="form-control" id="bookingEnd" name="bookingEnd" value="<?php echo htmlspecialchars($booking['bookingEnd']); ?>" required>
                        </div>
                        <button type="submit" class="btn btn-primary">Update Booking</button>
                        <a href="student-manage-booking.php" class="btn btn-secondary">Cancel</a>
                    </form>
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

ob_end_flush(); // Flush the output buffer
?>
