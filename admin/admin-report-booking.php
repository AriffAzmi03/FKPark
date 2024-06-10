<?php
// Include header file
include('includes/header.php');

// Include database connection file
include('includes/dbconnection.php');

// Fetch booking data for statistics
$dailyBookingsQuery = "SELECT DATE(bookingDate) as bookingDate, COUNT(*) as count FROM booking GROUP BY DATE(bookingDate)";
$dailyBookingsResult = $conn->query($dailyBookingsQuery);

$weeklyBookingsQuery = "SELECT WEEK(bookingDate) as bookingWeek, COUNT(*) as count FROM booking GROUP BY WEEK(bookingDate)";
$weeklyBookingsResult = $conn->query($weeklyBookingsQuery);

$monthlyBookingsQuery = "SELECT MONTH(bookingDate) as bookingMonth, COUNT(*) as count FROM booking GROUP BY MONTH(bookingDate)";
$monthlyBookingsResult = $conn->query($monthlyBookingsQuery);

$peakTimesQuery = "SELECT HOUR(bookingStart) as bookingHour, COUNT(*) as count FROM booking GROUP BY HOUR(bookingStart)";
$peakTimesResult = $conn->query($peakTimesQuery);

$bookingDurationQuery = "SELECT TIMESTAMPDIFF(MINUTE, bookingStart, bookingEnd) as duration FROM booking";
$bookingDurationResult = $conn->query($bookingDurationQuery);

// Prepare data for charts
$dailyBookingsData = [];
while($row = $dailyBookingsResult->fetch_assoc()) {
    $dailyBookingsData[] = $row;
}

$weeklyBookingsData = [];
while($row = $weeklyBookingsResult->fetch_assoc()) {
    $weeklyBookingsData[] = $row;
}

$monthlyBookingsData = [];
while($row = $monthlyBookingsResult->fetch_assoc()) {
    $monthlyBookingsData[] = $row;
}

$peakTimesData = [];
while($row = $peakTimesResult->fetch_assoc()) {
    $peakTimesData[] = $row;
}

$bookingDurationData = [];
while($row = $bookingDurationResult->fetch_assoc()) {
    $bookingDurationData[] = $row['duration'];
}

// Close the database connection
$conn->close();
?>

<div id="content-wrapper">
    <div class="container-fluid mt-4">
        <!-- Breadcrumbs -->
        <div class="row justify-content-center">
            <div class="col-md-12">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item">
                        <a href="#">Booking</a>
                    </li>
                    <li class="breadcrumb-item active">Booking Report</li>
                </ol>
            </div>
        </div>

        <!-- Booking Statistics -->
        <div class="row justify-content-center">
            <div class="col-md-12">
                <div class="card mb-4">
                    <div class="card-header">
                        <i class="fas fa-chart-bar"></i>
                        Booking Statistics
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <canvas id="dailyBookingsChart"></canvas>
                            </div>
                            <div class="col-md-6">
                                <canvas id="weeklyBookingsChart"></canvas>
                            </div>
                        </div>
                        <div class="row mt-4">
                            <div class="col-md-6">
                                <canvas id="monthlyBookingsChart"></canvas>
                            </div>
                            <div class="col-md-6">
                                <canvas id="peakTimesChart"></canvas>
                            </div>
                        </div>
                        <div class="row mt-4">
                            <div class="col-md-12">
                                <canvas id="bookingDurationChart"></canvas>
                            </div>
                        </div>
                    </div>
                    <div class="card-footer small text-muted">
                        <?php
                        date_default_timezone_set("Asia/Kuala_Lumpur");
                        echo "Generated : " . date("h:i:sa");
                        ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- /.container-fluid -->

    <!-- Footer -->
    <?php
    // Include footer
    include('includes/footer.php');
    ?>
</div>
<!-- /.content-wrapper -->

<?php
// Include scripts
include('includes/scripts.php');
?>

<!-- Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
// Prepare data for charts
var dailyBookingsData = <?php echo json_encode($dailyBookingsData); ?>;
var weeklyBookingsData = <?php echo json_encode($weeklyBookingsData); ?>;
var monthlyBookingsData = <?php echo json_encode($monthlyBookingsData); ?>;
var peakTimesData = <?php echo json_encode($peakTimesData); ?>;
var bookingDurationData = <?php echo json_encode($bookingDurationData); ?>;

// Daily Bookings Chart
var ctx1 = document.getElementById('dailyBookingsChart').getContext('2d');
var dailyLabels = dailyBookingsData.map(function(e) {
    return e.bookingDate;
});
var dailyCounts = dailyBookingsData.map(function(e) {
    return e.count;
});
var dailyBookingsChart = new Chart(ctx1, {
    type: 'line',
    data: {
        labels: dailyLabels,
        datasets: [{
            label: 'Daily Bookings',
            data: dailyCounts,
            borderColor: 'rgba(75, 192, 192, 1)',
            borderWidth: 1,
            fill: false
        }]
    }
});

// Weekly Bookings Chart
var ctx2 = document.getElementById('weeklyBookingsChart').getContext('2d');
var weeklyLabels = weeklyBookingsData.map(function(e) {
    return 'Week ' + e.bookingWeek;
});
var weeklyCounts = weeklyBookingsData.map(function(e) {
    return e.count;
});
var weeklyBookingsChart = new Chart(ctx2, {
    type: 'line',
    data: {
        labels: weeklyLabels,
        datasets: [{
            label: 'Weekly Bookings',
            data: weeklyCounts,
            borderColor: 'rgba(153, 102, 255, 1)',
            borderWidth: 1,
            fill: false
        }]
    }
});

// Monthly Bookings Chart
var ctx3 = document.getElementById('monthlyBookingsChart').getContext('2d');
var monthlyLabels = monthlyBookingsData.map(function(e) {
    return 'Month ' + e.bookingMonth;
});
var monthlyCounts = monthlyBookingsData.map(function(e) {
    return e.count;
});
var monthlyBookingsChart = new Chart(ctx3, {
    type: 'line',
    data: {
        labels: monthlyLabels,
        datasets: [{
            label: 'Monthly Bookings',
            data: monthlyCounts,
            borderColor: 'rgba(255, 159, 64, 1)',
            borderWidth: 1,
            fill: false
        }]
    }
});

// Peak Booking Times Chart
var ctx4 = document.getElementById('peakTimesChart').getContext('2d');
var peakLabels = peakTimesData.map(function(e) {
    return e.bookingHour + ':00';
});
var peakCounts = peakTimesData.map(function(e) {
    return e.count;
});
var peakTimesChart = new Chart(ctx4, {
    type: 'bar',
    data: {
        labels: peakLabels,
        datasets: [{
            label: 'Peak Booking Times',
            data: peakCounts,
            backgroundColor: 'rgba(54, 162, 235, 0.5)',
            borderColor: 'rgba(54, 162, 235, 1)',
            borderWidth: 1
        }]
    }
});

// Booking Duration Chart
var ctx5 = document.getElementById('bookingDurationChart').getContext('2d');
var durationLabels = ['< 30 min', '30-60 min', '1-2 hours', '2-4 hours', '> 4 hours'];
var durationCounts = [0, 0, 0, 0, 0];
bookingDurationData.forEach(function(duration) {
    if (duration < 30) {
        durationCounts[0]++;
    } else if (duration < 60) {
        durationCounts[1]++;
    } else if (duration < 120) {
        durationCounts[2]++;
    } else if (duration < 240) {
        durationCounts[3]++;
    } else {
        durationCounts[4]++;
    }
});
var bookingDurationChart = new Chart(ctx5, {
    type: 'pie',
    data: {
        labels: durationLabels,
        datasets: [{
            label: 'Booking Duration',
            data: durationCounts,
            backgroundColor: [
                'rgba(255, 99, 132, 0.2)',
                'rgba(54, 162, 235, 0.2)',
                'rgba(255, 206, 86, 0.2)',
                'rgba(75, 192, 192, 0.2)',
                'rgba(153, 102, 255, 0.2)'
            ],
            borderColor: [
                'rgba(255, 99, 132, 1)',
                'rgba(54, 162, 235, 1)',
                'rgba(255, 206, 86, 1)',
                'rgba(75, 192, 192, 1)',
                'rgba(153, 102, 255, 1)'
            ],
           
