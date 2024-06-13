<?php
session_start();

// Include header file
include('includes/header.php');

// Include database connection file
include('includes/dbconnection.php');

// Fetch booking history details
$query = "SELECT * FROM booking_history ORDER BY bookingDate DESC";
$stmt = $conn->prepare($query);
$stmt->execute();
$result = $stmt->get_result();

// Close the statement
$stmt->close();
?>

<div class="container mt-4">
    <!-- Breadcrumbs-->
    <ol class="breadcrumb">
        <li class="breadcrumb-item">
            <a href="#">Booking</a>
        </li>
        <li class="breadcrumb-item active">Booking Report</li>
    </ol>
    <hr>
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    Booking Report
                </div>
                <div class="card-body">
                    <?php if ($result->num_rows > 0) { ?>
                        <div class="table-responsive">
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
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $cnt = 1;
                                    while ($row = $result->fetch_object()) {
                                    ?>
                                        <tr>
                                            <td><?php echo $cnt; ?></td>
                                            <td><?php echo $row->bookingID; ?></td>
                                            <td><?php echo $row->bookingDate; ?></td>
                                            <td><?php echo $row->bookingStart; ?></td>
                                            <td><?php echo $row->bookingEnd; ?></td>
                                            <td><?php echo $row->studentName; ?></td>
                                            <td><?php echo $row->parkingArea; ?></td>
                                            <td><?php echo $row->parkingType; ?></td>
                                            <td><?php echo $row->vehiclePlateNum; ?></td>
                                        </tr>
                                    <?php
                                        $cnt++;
                                    }
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
    </div>
</div>

<hr>

<?php
// Include footer and scripts
include('includes/footer.php');
include('includes/scripts.php');
?>
