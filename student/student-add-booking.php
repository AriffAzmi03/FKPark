<?php
session_start();
ob_start(); // Start output buffering

// Check if the student is logged in
if (!isset($_SESSION['studentID'])) {
    header("Location: student-login.php");
    exit();
}

// Include header file
include('includes/header.php');

// Include database connection file
include('includes/dbconnection.php');

// Function to generate a random booking ID
function generateRandomBookingID($length = 10) {
    $characters = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $charactersLength = strlen($characters);
    $randomString = '';
    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[rand(0, $charactersLength - 1)];
    }
    return $randomString;
}

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['confirm_booking'])) {
    // Get form data
    $studentID = $_SESSION['studentID']; // Get studentID from session
    $parkingID = $_POST['parkingID'];
    $bookingDate = $_POST['bookingDate'];
    $bookingStart = $_POST['bookingStart'];
    $bookingEnd = $_POST['bookingEnd'];
    $vehiclePlateNum = $_POST['vehiclePlateNum'];
    $bookingID = generateRandomBookingID(); // Generate random booking ID

    // Start a transaction
    $conn->begin_transaction();

    try {
        // Insert into booking table
        $query = "INSERT INTO booking (bookingID, studentID, parkingID, bookingDate, bookingStart, bookingEnd, vehiclePlateNum)
                  VALUES (?, ?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("sssssss", $bookingID, $studentID, $parkingID, $bookingDate, $bookingStart, $bookingEnd, $vehiclePlateNum);
        if (!$stmt->execute()) {
            throw new Exception($stmt->error);
        }


        // Commit transaction
        $conn->commit();
        // Close the statement
        $stmt->close();
        
        // Redirect to manage booking page
        header("Location: student-manage-booking.php");
        exit();
    } catch (Exception $e) {
        // Rollback transaction if something went wrong
        $conn->rollback();
        echo "<div class='alert alert-danger' role='alert'>Error: " . $e->getMessage() . "</div>";
    }
}

// Get data from URL
$parkingID = $_GET['parkingID'];
$bookingDate = $_GET['bookingDate'];
$bookingStart = $_GET['bookingStart'];
$bookingEnd = $_GET['bookingEnd'];
$vehiclePlateNum = $_GET['vehiclePlateNum'];
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
                        <input type="hidden" name="vehiclePlateNum" value="<?php echo $vehiclePlateNum; ?>">
                        
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
                        <div class="form-group mb-3">
                            <label for="vehiclePlateNum">Vehicle Plate Number</label>
                            <input type="text" class="form-control" id="vehiclePlateNum" value="<?php echo $vehiclePlateNum; ?>" disabled>
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

ob_end_flush(); // Flush the output buffer
?>
