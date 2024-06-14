<?php
// Start the session
session_start();

// Include header file
include('includes/header.php');

// Include database connection file
include('includes/dbconnection.php');

// Query to get the count of registered users
$queryUsers = "SELECT COUNT(*) AS totalUsers FROM student";
$resultUsers = $conn->query($queryUsers);
$totalUsers = $resultUsers->fetch_assoc()['totalUsers'];

// Query to get the count of undergraduate and postgraduate students
$queryUndergraduate = "SELECT COUNT(*) AS totalUndergraduate FROM student WHERE studentType = 'Undergraduate'";
$resultUndergraduate = $conn->query($queryUndergraduate);
$totalUndergraduate = $resultUndergraduate->fetch_assoc()['totalUndergraduate'];

$queryPostgraduate = "SELECT COUNT(*) AS totalPostgraduate FROM student WHERE studentType = 'Postgraduate'";
$resultPostgraduate = $conn->query($queryPostgraduate);
$totalPostgraduate = $resultPostgraduate->fetch_assoc()['totalPostgraduate'];

// Query to get the count of registered vehicles
$queryVehicles = "SELECT COUNT(*) AS totalVehicles FROM vehicle";
$resultVehicles = $conn->query($queryVehicles);
$totalVehicles = $resultVehicles->fetch_assoc()['totalVehicles'];

// Query to get the count of cars and motorcycles
$queryCars = "SELECT COUNT(*) AS totalCars FROM vehicle WHERE vehicleType = 'Car'";
$resultCars = $conn->query($queryCars);
$totalCars = $resultCars->fetch_assoc()['totalCars'];

$queryMotorcycles = "SELECT COUNT(*) AS totalMotorcycles FROM vehicle WHERE vehicleType = 'Motorcycle'";
$resultMotorcycles = $conn->query($queryMotorcycles);
$totalMotorcycles = $resultMotorcycles->fetch_assoc()['totalMotorcycles'];

// Query to get the number of students registered per year
$queryYearCounts = "SELECT studentYear, COUNT(*) as count FROM student GROUP BY studentYear";
$resultYearCounts = $conn->query($queryYearCounts);
$yearCounts = $resultYearCounts->fetch_all(MYSQLI_ASSOC);

// Convert PHP arrays to JavaScript
$yearLabels = json_encode(array_column($yearCounts, 'studentYear'));
$yearData = json_encode(array_column($yearCounts, 'count'));
?>

<div class="container-fluid px-4">
    <h1 class="mt-4">Student Dashboard</h1>
    <!-- Breadcrumb -->
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item active">Dashboard</li>
    </ol>

    <div class="row">
        <!-- Registered Students Card -->
        <div class="col-xl-6 col-md-6">
            <div class="card bg-primary text-white mb-4">
                <div class="card-body">
                    <span class="card-title">Registered Students:</span>
                    <span class="card-count"><?php echo $totalUsers; ?></span>
                </div>
                <div class="card-footer d-flex align-items-center justify-content-between">
                    <span>Total Undergraduate: <?php echo $totalUndergraduate; ?></span>
                </div>
                <div class="card-footer d-flex align-items-center justify-content-between">
                    <span>Total Postgraduate: <?php echo $totalPostgraduate; ?></span>
                </div>
            </div>
        </div>
        <!-- Registered Vehicles Card -->
        <div class="col-xl-6 col-md-6">
            <div class="card bg-warning text-white mb-4">
                <div class="card-body">
                    <span class="card-title">Registered Vehicles:</span>
                    <span class="card-count"><?php echo $totalVehicles; ?></span>
                </div>
                <div class="card-footer d-flex align-items-center justify-content-between">
                    <span>Cars: <?php echo $totalCars; ?></span>
                </div>
                <div class="card-footer d-flex align-items-center justify-content-between">
                    <span>Motorcycles: <?php echo $totalMotorcycles; ?></span>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Bar Chart -->
        <div class="col-xl-6">
            <div class="card mb-4">
                <div class="card-header">
                    <i class="fas fa-chart-bar me-1"></i>
                    Registered Students (Year of Study)
                </div>
                <div class="card-body">
                    <canvas id="studentsYearChart" width="400" height="200"></canvas>
                </div>
            </div>
        </div>
        <!-- Pie Chart -->
        <div class="col-xl-6">
            <div class="card mb-4">
                <div class="card-header">
                    <i class="fas fa-chart-pie me-1"></i>
                    Registered Vehicles Breakdown
                </div>
                <div class="card-body">
                    <canvas id="vehiclesChart" width="400" height="200"></canvas>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
// Include footer and scripts
include('includes/footer.php');
include('includes/scripts.php');
?>

<!-- Chart.js Script -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    // Data for the bar chart
    const yearLabels = <?php echo $yearLabels; ?>;
    const yearData = <?php echo $yearData; ?>;

    // Data for the pie chart
    const vehiclesData = {
        labels: ["Cars", "Motorcycles"],
        datasets: [{
            data: [<?php echo $totalCars; ?>, <?php echo $totalMotorcycles; ?>],
            backgroundColor: ["#007bff", "#dc3545"],
        }],
    };

    // Bar chart configuration
    var ctxBar = document.getElementById("studentsYearChart");
    var myBarChart = new Chart(ctxBar, {
        type: 'bar',
        data: {
            labels: yearLabels,
            datasets: [{
                label: "Number of Students",
                backgroundColor: "rgba(2,117,216,1)",
                borderColor: "rgba(2,117,216,1)",
                data: yearData,
            }],
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                xAxes: [{
                    time: {
                        unit: 'year'
                    },
                    gridLines: {
                        display: false
                    },
                    ticks: {
                        maxTicksLimit: 6
                    }
                }],
                yAxes: [{
                    ticks: {
                        min: 0,
                        max: Math.max(...yearData) + 10, // Adjust max value dynamically
                        maxTicksLimit: 5
                    },
                    gridLines: {
                        display: true
                    }
                }],
            },
            legend: {
                display: false
            },
        }
    });

    // Pie chart configuration
    var ctxPie = document.getElementById("vehiclesChart");
    var myPieChart = new Chart(ctxPie, {
        type: 'pie',
        data: vehiclesData,
        options: {
            responsive: true,
            maintainAspectRatio: false,
            legend: {
                position: 'top',
            },
            title: {
                display: true,
                text: 'Registered Vehicles Breakdown'
            }
        }
    });
</script>

<!-- Custom CSS -->
<style>
    .card-title {
        font-size: 1.2em;
        font-weight: bold;
    }
    .card-count {
        font-size: 1.2em;
    }
    .card-footer {
        font-size: 1em;
    }
</style>
