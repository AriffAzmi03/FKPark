<?php
// Include header file
include('includes/header.php');

// Include database connection file
include('includes/dbconnection.php');

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['book_parking_space'])) {
    // Get form data
    $bookingStart = $_POST['bookingStart'];
    $bookingEnd = $_POST['bookingEnd'];
    $bookingDate = $_POST['bookingDate'];

    // Prepare and execute the insert query
    $query = "INSERT INTO booking (bookingStart, bookingEnd, bookingDate)
              VALUES (?, ?, ?)";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("sss", $bookingStart, $bookingEnd, $bookingDate);

    if ($stmt->execute()) {
        echo "<div class='alert alert-success' role='alert'>Parking space booked successfully!</div>";
    } else {
        echo "<div class='alert alert-danger' role='alert'>Error: " . $stmt->error . "</div>";
    }

    // Close the statement
    $stmt->close();
}

// Close the database connection
$conn->close();
?>

<div class="container mt-4">
    <!-- Breadcrumbs-->
    <ol class="breadcrumb">
        <li class="breadcrumb-item">
            <a href="#">Parking</a>
        </li>
        <li class="breadcrumb-item active">Book Parking Space</li>
    </ol>
    <hr>
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    Book Parking Space
                </div>
                <div class="card-body">
                    <!-- Book Parking Space Form -->
                    <form method="POST">
                        <div class="form-group mb-3">
                            <label for="bookingStart">Time Start</label>
                            <input type="time" required class="form-control" id="bookingStart" name="bookingStart">
                        </div>
                        <div class="form-group mb-3">
                            <label for="bookingEnd">Time End</label>
                            <input type="time" required class="form-control" id="bookingEnd" name="bookingEnd">
                        </div>
                        <div class="form-group mb-3">
                            <label for="bookingDate">Booking Date</label>
                            <input type="date" required class="form-control" id="bookingDate" name="bookingDate">
                        </div>
                        <button type="submit" name="book_parking_space" class="btn btn-success">Book Parking Space</button>
                    </form>
                    <!-- End Form -->
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
