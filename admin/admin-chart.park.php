<?php
ob_start(); // Start output buffering

// Include header file
include('includes/header.php');

// Include database connection file
include('includes/dbconnection.php');

// Fetch parking space data from the database
$query = "SELECT parkingAvailabilityStatus, COUNT(*) AS count FROM parkingspace GROUP BY parkingAvailabilityStatus";
$result = $conn->query($query);

$totalSpaces = 0;
$availableSpaces = 0;
$unavailableSpaces = 0;

while ($row = $result->fetch_assoc()) {
    if ($row['parkingAvailabilityStatus'] == 'Available') {
        $availableSpaces = $row['count'];
    } else {
        $unavailableSpaces = $row['count'];
    }
    $totalSpaces += $row['count'];
}

// Close the database connection
$conn->close();
ob_end_flush(); // End output buffering and flush output
?>

<div class="container mt-4">
    <!-- Breadcrumbs-->
    <ol class="breadcrumb">
        <li class="breadcrumb-item">
            <a href="#">Parking</a>
        </li>
        <li class="breadcrumb-item active">Parking Statistics</li>
    </ol>
    <hr>
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    Parking Availability Status
                </div>
                <div class="card-body text-center">
                    <!-- Pie Chart -->
                    <canvas id="myPieChart"></canvas>
                    <!-- Summary Information -->
                    <div class="mt-4">
                        <p>Total Parking Spaces: <?php echo $totalSpaces; ?></p>
                        <p>Total Available Spaces: <?php echo $availableSpaces; ?></p>
                        <p>Total Unavailable Spaces: <?php echo $unavailableSpaces; ?></p>
                    </div>
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

<!-- Custom JavaScript to create the pie chart -->
<script>
    // Set new default font family and font color to mimic Bootstrap's default styling
    Chart.defaults.global.defaultFontFamily = '-apple-system,system-ui,BlinkMacSystemFont,"Segoe UI",Roboto,"Helvetica Neue",Arial,sans-serif';
    Chart.defaults.global.defaultFontColor = '#292b2c';

    // Pie Chart Example
    var ctx = document.getElementById("myPieChart");
    var myPieChart = new Chart(ctx, {
        type: 'pie',
        data: {
            labels: ["Available", "Unavailable"],
            datasets: [{
                data: [<?php echo $availableSpaces; ?>, <?php echo $unavailableSpaces; ?>],
                backgroundColor: ['#28a745', '#dc3545'],
            }],
        },
    });
</script>

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
