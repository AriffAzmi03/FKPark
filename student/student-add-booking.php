<?php
// Include header file
include('includes/header.php');

// Include database connection file
include('includes/dbconnection.php');

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['confirm_booking'])) {
    // Get form data
    $parkingID = $_POST['parkingID'];
    $bookingDate = $_POST['bookingDate'];
    $bookingStart = $_POST['bookingStart'];
    $bookingEnd = $_POST['bookingEnd'];

    // Start a transaction
    $conn->begin_transaction();

    try {
        // Insert into booking table
        $query = "INSERT INTO booking (parkingID, bookingDate, bookingStart, bookingEnd)
                  VALUES (?, ?, ?, ?)";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("ssss", $parkingID, $bookingDate, $bookingStart, $bookingEnd);
        if (!$stmt->execute()) {
            throw new Exception($stmt->error);
        }

        // Update parking space status
        $updateQuery = "UPDATE parkingspace SET parkingAvailabilityStatus = 'Occupied' WHERE parkingID = ?";
        $stmt = $conn->prepare($updateQuery);
        $stmt->bind_param("s", $parkingID);
        if (!$stmt->execute()) {
            throw new Exception($stmt->error);
        }

        // Commit transaction
        $conn->commit();
        echo "<div class='alert alert-success' role='alert'>Parking space booked successfully!</div>";
    } catch (Exception $e) {
        // Rollback transaction if something went wrong
        $conn->rollback();
        echo "<div class='alert alert-danger' role='alert'>Error: " . $e->getMessage() . "</div>";
    }

    // Close the statement
    $stmt->close();
    // Redirect to manage booking page
    header("Location: student-manage-booking.php");
    exit();
}

// Get data from URL
$parkingID = $_GET['parkingID'];
$bookingDate = $_GET['bookingDate'];
$bookingStart = $_GET['bookingStart'];
$bookingEnd = $_GET['bookingEnd'];
?>

<div class="container mt-4">
    <!-- Breadcrumbs-->
    <ol class="breadcrumb">
        <li class="breadcrumb-item">
            <a href="#">Parking</a>
        </li>
        <li class="breadcrumb-item active">Confirm Booking</li>
    </ol>
    <hr>
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    Confirm Booking
                </div>
                <div class="card-body">
                    <form method="POST">
                        <input type="hidden" name="parkingID" value="<?php echo $parkingID; ?>">
                        <input type="hidden" name="bookingDate" value="<?php echo $bookingDate; ?>">
                        <input type="hidden" name="bookingStart" value="<?php echo $bookingStart; ?>">
                        <input type="hidden" name="bookingEnd" value="<?php echo $bookingEnd; ?>">
                        
                        <div class="form-group mb-3">
                            <label for="parkingID">Parking Space ID</label>
                            <input type="text" class="form-control" id="parkingID" value="<?php echo $parkingID; ?>" disabled>
                        </div>
                        <div class="form-group mb-3">
                            <label for="bookingDate">Booking Date</label>
                            <input type="text" class="form-control" id="bookingDate" value="<?php echo $bookingDate; ?>" disabled>
                        </div>
                        <div class="form-group mb-3">
                            <label for="bookingStart">Time Start</label>
                            <input type="text" class="form-control" id="bookingStart" value="<?php echo $bookingStart; ?>" disabled>
                        </div>
                        <div class="form-group mb-3">
                            <label for="bookingEnd">Time End</label>
                            <input type="text" class="form-control" id="bookingEnd" value="<?php echo $bookingEnd; ?>" disabled>
                        </div>
                        <button type="submit" name="confirm_booking" class="btn btn-success">Confirm Booking</button>
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
