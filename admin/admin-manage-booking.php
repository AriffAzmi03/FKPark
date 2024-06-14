<?php
session_start();
// Include header file
include('includes/header.php');

// Include database connection file
include('includes/dbconnection.php');

// Handle delete request
if (isset($_GET['del_start'])) {
    $bookingStart = $_GET['del_start'];
    $delQuery = "DELETE FROM booking_history WHERE bookingStart = ?";
    $stmt = $conn->prepare($delQuery);
    $stmt->bind_param("s", $bookingStart);
    
    if ($stmt->execute()) {
        $deleteMessage = "<div class='alert alert-success' role='alert'>Booking deleted successfully!</div>";
    } else {
        $deleteMessage = "<div class='alert alert-danger' role='alert'>Error: " . $stmt->error . "</div>";
    }

    // Close the statement
    $stmt->close();
}

// Fetch booking history details
$query = "SELECT * FROM booking_history ORDER BY bookingDate DESC";
$stmt = $conn->prepare($query);
$stmt->execute();
$result = $stmt->get_result();

// Prepare data for the chart
$dates = [];
$counts = [];

while ($row = $result->fetch_assoc()) {
    $date = $row['bookingDate'];
    if (!isset($dates[$date])) {
        $dates[$date] = 0;
    }
    $dates[$date]++;
}

// Note: Do not close the connection here as we need it later for fetching the booking details again.
// $stmt->close();
// $conn->close();
?>

<div class="container mt-4">
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <span>Booking Report</span>
        </div>
        <div class="card-body">
            <?php if (isset($deleteMessage)) { echo $deleteMessage; } ?>
            <?php if (count($dates) > 0) { ?>
                <div class="table-responsive mt-4">
                    <table class="table table-bordered table-hover table-striped" id="dataTable" width="100%" cellspacing="0">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Booking ID</th>
                                <th>Booking Date</th>
                                <th>Time Start</th>
                                <th>Time End</th>
                                <th>Student Name</th>
                                <th>Parking Area</th>
                                <th>Parking Type</th>
                                <th>Vehicle Plate Number</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $cnt = 1;
                            // Fetch booking details again for the table
                            $stmt = $conn->prepare($query);
                            $stmt->execute();
                            $result = $stmt->get_result();
                            while ($row = $result->fetch_object()) {
                            ?>
                                <tr>
                                    <td><?php echo $cnt; ?></td>
                                    <td><?php echo htmlspecialchars($row->bookingID); ?></td>
                                    <td><?php echo htmlspecialchars($row->bookingDate); ?></td>
                                    <td><?php echo htmlspecialchars($row->bookingStart); ?></td>
                                    <td><?php echo htmlspecialchars($row->bookingEnd); ?></td>
                                    <td><?php echo htmlspecialchars($row->studentName); ?></td>
                                    <td><?php echo htmlspecialchars($row->parkingArea); ?></td>
                                    <td><?php echo htmlspecialchars($row->parkingType); ?></td>
                                    <td><?php echo htmlspecialchars($row->vehiclePlateNum); ?></td>
                                    <td>
                                        <a href="admin-view-booking.php?bookingID=<?php echo htmlspecialchars($row->bookingID); ?>" class="btn btn-primary btn-sm"><i class="fas fa-eye"></i> View</a>
                                        <a href="admin-manage-booking.php?del_start=<?php echo htmlspecialchars($row->bookingStart); ?>" class="btn btn-danger btn-sm text-white" onclick="return confirm('Are you sure you want to delete this booking?');"><i class="fas fa-trash-alt"></i> Delete</a>
                                    </td>
                                </tr>
                            <?php
                                $cnt++;
                            }
                            $stmt->close();
                            ?>
                        </tbody>
                    </table>
                </div>
            <?php } else { ?>
                <div class="alert alert-danger" role="alert">No booking details found.</div>
            <?php } ?>
        </div>
    </div>
</div>

<hr>

<?php
// Close the connection here after all queries are done
$conn->close();

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
