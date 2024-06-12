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
include('includes/sidebar.php'); // Include sidebar file

// Include database connection file
include('includes/dbconnection.php');

// Function to generate a random booking ID (varchar 10)
function generateRandomBookingID($length = 10) {
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
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
    $parkingID = $_POST['parkingID'];
    $bookingDate = $_POST['bookingDate'];
    $bookingStart = $_POST['bookingStart'];
    $bookingEnd = $_POST['bookingEnd'];
    $studentID = $_SESSION['studentID']; // Get the studentID from the session

    // Generate random booking ID
    $bookingID = generateRandomBookingID();

    // Start a transaction
    $conn->begin_transaction();

    try {
        // Insert into booking table
        $query = "INSERT INTO booking (bookingID, parkingID, bookingDate, bookingStart, bookingEnd, studentID)
                  VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("ssssss", $bookingID, $parkingID, $bookingDate, $bookingStart, $bookingEnd, $studentID);
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
    ob_end_flush(); // Flush the buffer and send output to the browser
    exit(); // Terminate the script
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

ob_end_flush(); // Flush the buffer and send output to the browser
?>
