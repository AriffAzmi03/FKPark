<?php
// Start the session
session_start();

// Check if the student is logged in
if (!isset($_SESSION['studentID'])) {
    header("Location: student-login.php");
    exit();
}

// Include header file
include('includes/header.php');

// Include database connection file
include('includes/dbconnection.php');

// Get the student ID from the session
$studentID = $_SESSION['studentID'];

// Calculate total demerit points
$query = "SELECT SUM(summonDemerit) AS total_demerit FROM summon s 
          JOIN vehicle v ON s.vehiclePlateNum = v.vehiclePlateNum
          WHERE v.studentID = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("s", $studentID);
$stmt->execute();
$stmt->bind_result($total_demerit);
$stmt->fetch();
$stmt->close();

// Determine enforcement status based on total demerit points
$enforcement_status = "";
if ($total_demerit < 20) {
    $enforcement_status = "Warning given";
} elseif ($total_demerit < 50) {
    $enforcement_status = "Revoke of in-campus vehicle permission for 1 semester";
} elseif ($total_demerit < 80) {
    $enforcement_status = "Revoke of in-campus vehicle permission for 2 semesters";
} else {
    $enforcement_status = "Revoke of in-campus vehicle permission for the entire study duration";
}

// Retrieve summons for the logged-in student
$query = "SELECT s.summonID, s.vehiclePlateNum, s.summonViolationType, s.summonDemerit, s.summonDate 
          FROM summon s 
          JOIN vehicle v ON s.vehiclePlateNum = v.vehiclePlateNum
          WHERE v.studentID = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("s", $studentID);
$stmt->execute();
$result = $stmt->get_result();
?>

<div id="content-wrapper">
    <div class="container-fluid mt-4">
        <!-- Breadcrumbs -->
        <div class="row justify-content-center">
            <div class="col-md-12">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item">
                        <a href="#">Summons</a>
                    </li>
                    <li class="breadcrumb-item active">Manage Summons</li>
                </ol>
            </div>
        </div>

        <!-- Demerit Points and Enforcement Status -->
        <div class="row justify-content-center">
            <div class="col-md-12">
                <div class="card mb-4">
                    <div class="card-header">
                        <i class="fas fa-exclamation-triangle"></i>
                        Total Demerit Points and Enforcement Status
                    </div>
                    <div class="card-body">
                        <h5>Total Demerit Points: <?php echo $total_demerit; ?></h5>
                        <h5>Enforcement Status: <?php echo $enforcement_status; ?></h5>
                    </div>
                </div>
            </div>
        </div>

        <!-- DataTables Example -->
        <div class="row justify-content-center">
            <div class="col-md-12">
                <div class="card mb-4">
                    <div class="card-header">
                        <i class="fas fa-car"></i>
                        My Summons
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered table-hover table-striped" width="100%" cellspacing="0">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Vehicle Plate Number</th>
                                        <th>Violation Type</th>
                                        <th>Demerit Points</th>
                                        <th>Summon Date</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $cnt = 1;
                                    while ($row = $result->fetch_assoc()) {
                                    ?>
                                    <tr>
                                        <td><?php echo $cnt++; ?></td>
                                        <td><?php echo $row['vehiclePlateNum']; ?></td>
                                        <td><?php echo $row['summonViolationType']; ?></td>
                                        <td><?php echo $row['summonDemerit']; ?></td>
                                        <td><?php echo $row['summonDate']; ?></td>
                                        <td>
                                            <a href="student-view-summon.php?summonID=<?php echo $row['summonID']; ?>" class="badge bg-primary text-white">View</a>
                                        </td>
                                    </tr>
                                    <?php
                                    }
                                    $stmt->close();
                                    ?>
                                </tbody>
                            </table>
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
