<?php
// Include header file
include('includes/header.php');

// Include database connection file
include('includes/dbconnection.php');

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

$date_labels = [];
$counts = [];

foreach ($dates as $date => $count) {
    $date_labels[] = $date;
    $counts[] = $count;
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
            <?php if (count($dates) > 0) { ?>
                <canvas id="bookingBarChart"></canvas>
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

<!-- Include Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<!-- Include Chart Bar Demo Script -->
<script src="path/to/chart-bar-demo.js"></script>

<script>
    // Assuming chart-bar-demo.js initializes a chart with the ID 'myBarChart'
    document.addEventListener('DOMContentLoaded', function() {
        const ctx = document.getElementById('bookingBarChart').getContext('2d');
        const bookingBarChart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: <?php echo json_encode($date_labels); ?>,
                datasets: [{
                    label: 'Bookings per Date',
                    data: <?php echo json_encode($counts); ?>,
                    backgroundColor: 'rgba(54, 162, 235, 0.2)',
                    borderColor: 'rgba(54, 162, 235, 1)',
                    borderWidth: 1
                }]
            },
            options: {
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });
    });
</script>
