<?php
// Start the session
session_start();

// Include header file
include('includes/header.php');

// Include database connection file
include('includes/dbconnection.php');

// Handle delete request
if (isset($_GET['del'])) {
    $summonID = (int)$_GET['del'];
    $delQuery = "DELETE FROM summon WHERE summonID = ?";
    $stmt = $conn->prepare($delQuery);
    $stmt->bind_param("i", $summonID);

    if ($stmt->execute()) {
        $deleteMessage = "<div class='alert alert-success' role='alert'>Summon deleted successfully!</div>";
    } else {
        $deleteMessage = "<div class='alert alert-danger' role='alert'>Error: " . htmlspecialchars($stmt->error) . "</div>";
    }

    // Close the statement
    $stmt->close();
}

// Handle filter/search request (GET)
$searchQuery = isset($_GET['search']) ? trim($_GET['search']) : '';
$violationTypeFilter = isset($_GET['violation_type']) ? trim($_GET['violation_type']) : '';
$dateFrom = isset($_GET['date_from']) ? trim($_GET['date_from']) : '';
$dateTo = isset($_GET['date_to']) ? trim($_GET['date_to']) : '';
$demeritLevel = isset($_GET['demerit_level']) ? trim($_GET['demerit_level']) : '';

// Build dynamic query with safe parameter binding
$baseQuery = "SELECT summonID, vehiclePlateNum, summonViolationType, summonDemerit, 
                     DATE(summonDate) as summonDate, TIME(summonDate) as summonTime
              FROM summon";
$whereClauses = [];
$params = [];
$types = "";

// Search by plate
if ($searchQuery !== '') {
    $whereClauses[] = "vehiclePlateNum LIKE ?";
    $params[] = "%" . $searchQuery . "%";
    $types .= "s";
}

// Violation type filter
if ($violationTypeFilter !== '') {
    $whereClauses[] = "summonViolationType = ?";
    $params[] = $violationTypeFilter;
    $types .= "s";
}

// Date range filter
if ($dateFrom !== '' && $dateTo !== '') {
    $whereClauses[] = "DATE(summonDate) BETWEEN ? AND ?";
    $params[] = $dateFrom;
    $params[] = $dateTo;
    $types .= "ss";
} else if ($dateFrom !== '') {
    $whereClauses[] = "DATE(summonDate) >= ?";
    $params[] = $dateFrom;
    $types .= "s";
} else if ($dateTo !== '') {
    $whereClauses[] = "DATE(summonDate) <= ?";
    $params[] = $dateTo;
    $types .= "s";
}

// Demerit level filter (Low <10, Medium 10-14, High >=15)
if ($demeritLevel !== '') {
    if ($demeritLevel === 'low') {
        $whereClauses[] = "summonDemerit BETWEEN ? AND ?";
        $params[] = 0;
        $params[] = 9;
        $types .= "ii";
    } else if ($demeritLevel === 'medium') {
        $whereClauses[] = "summonDemerit BETWEEN ? AND ?";
        $params[] = 10;
        $params[] = 14;
        $types .= "ii";
    } else if ($demeritLevel === 'high') {
        $whereClauses[] = "summonDemerit >= ?";
        $params[] = 15;
        $types .= "i";
    }
}

// Combine query
if (count($whereClauses) > 0) {
    $baseQuery .= " WHERE " . implode(" AND ", $whereClauses);
}

// Optionally add ordering (latest first)
$baseQuery .= " ORDER BY summonDate DESC, summonTime DESC";

$stmt = $conn->prepare($baseQuery);
if ($stmt === false) {
    echo "<div class='alert alert-danger' role='alert'>Database error: " . htmlspecialchars($conn->error) . "</div>";
    include('includes/footer.php');
    include('includes/scripts.php');
    exit();
}

if (!empty($params)) {
    // bind parameters dynamically (bind_param needs references)
    $bind_names = [];
    $bind_names[] = $types;
    for ($i = 0; $i < count($params); $i++) {
        $bind_name = 'param' . $i;
        $$bind_name = $params[$i];
        $bind_names[] = &$$bind_name;
    }
    call_user_func_array([$stmt, 'bind_param'], $bind_names);
}

$stmt->execute();
$result = $stmt->get_result();
?>

<div id="content-wrapper">
    <div class="container-fluid mt-4">
        <!-- Breadcrumbs -->
        <div class="row">
            <div class="col-12">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="#">Summons</a></li>
                    <li class="breadcrumb-item active">Manage Summons</li>
                </ol>
            </div>
        </div>

        <!-- Manage Summons Card -->
        <div class="row">
            <div class="col-12">
                <div class="card mb-4">
                    <!-- Card header with compact filter form -->
                    <div class="card-header">
                        <div class="d-flex align-items-center justify-content-between">
                            <span><i class="fas fa-car"></i> Registered Summons</span>
                        </div>

                        <!-- Compact Filter & Search Form -->
                        <form class="w-100 mt-3" method="get" action="">
                          <div class="row g-2 align-items-center">
                            <div class="col-md-4 col-sm-12">
                              <div class="input-group">
                                <input class="form-control" type="search" name="search" placeholder="Search by Plate Number" value="<?php echo htmlspecialchars($searchQuery); ?>">
                                <button class="btn btn-outline-success" type="submit">Search</button>
                              </div>
                            </div>

                            <div class="col-auto col-md-2 col-sm-6">
                              <select name="violation_type" class="form-select" aria-label="Violation type">
                                <option value="">All Violation Types</option>
                                <option value="Parking Violation" <?php if($violationTypeFilter === 'Parking Violation') echo 'selected'; ?>>Parking Violation</option>
                                <option value="Campus Traffic Regulations" <?php if($violationTypeFilter === 'Campus Traffic Regulations') echo 'selected'; ?>>Campus Traffic Regulations</option>
                                <option value="Accident Cause" <?php if($violationTypeFilter === 'Accident Cause') echo 'selected'; ?>>Accident Cause</option>
                              </select>
                            </div>

                            <div class="col-auto col-md-2 col-sm-6">
                              <select name="demerit_level" class="form-select" aria-label="Demerit level">
                                <option value="">All Demerit Levels</option>
                                <option value="low" <?php if($demeritLevel === 'low') echo 'selected'; ?>>Low (&lt; 10)</option>
                                <option value="medium" <?php if($demeritLevel === 'medium') echo 'selected'; ?>>Medium (10-14)</option>
                                <option value="high" <?php if($demeritLevel === 'high') echo 'selected'; ?>>High (≥ 15)</option>
                              </select>
                            </div>

                            <div class="col-auto col-md-3 col-sm-12">
                              <div class="d-flex gap-2">
                                <input type="date" name="date_from" class="form-control" value="<?php echo htmlspecialchars($dateFrom); ?>" title="From">
                                <input type="date" name="date_to" class="form-control" value="<?php echo htmlspecialchars($dateTo); ?>" title="To">
                              </div>
                            </div>

                            <div class="col-auto col-md-1 col-sm-12">
                              <a href="staff-manage-summon.php" class="btn btn-light w-100">Reset</a>
                            </div>
                          </div>
                        </form>
                        <!-- End filter form -->
                    </div>

                    <div class="card-body">
                        <?php
                        // Display delete message if set
                        if (isset($deleteMessage)) {
                            echo $deleteMessage;
                        }
                        // Display success message if set
                        if (isset($_GET['success'])) {
                            echo "<div class='alert alert-success' role='alert'>New summon added successfully!</div>";
                        }
                        ?>
                        <div class="table-responsive">
                            <table class="table table-bordered table-hover table-striped" width="100%" cellspacing="0">
                                <thead>
                                    <tr>
                                        <th style="width:45px">#</th>
                                        <th>Vehicle Plate Number</th>
                                        <th>Violation Type</th>
                                        <th>Demerit Points</th>
                                        <th>Summon Date</th>
                                        <th>Summon Time</th>
                                        <th style="width:200px">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $cnt = 1;
                                    while ($row = $result->fetch_assoc()) {
                                    ?>
                                    <tr>
                                        <td><?php echo $cnt++; ?></td>
                                        <td><?php echo htmlspecialchars($row['vehiclePlateNum']); ?></td>
                                        <td><?php echo htmlspecialchars($row['summonViolationType']); ?></td>
                                        <td><?php echo htmlspecialchars($row['summonDemerit']); ?></td>
                                        <td><?php echo htmlspecialchars($row['summonDate']); ?></td>
                                        <td><?php echo htmlspecialchars($row['summonTime']); ?></td>
                                        <td class='action-column'>
                                            <a href="staff-view-summon.php?summonID=<?php echo $row['summonID']; ?>" class="btn btn-primary btn-sm me-1 mb-1"><i class="fas fa-eye"></i> View</a>
                                            <a href="staff-edit-summon.php?summonID=<?php echo $row['summonID']; ?>" class="btn btn-success btn-sm me-1 mb-1"><i class="fas fa-edit"></i> Edit</a>
                                            <a href="staff-manage-summon.php?del=<?php echo $row['summonID']; ?>" class="btn btn-danger btn-sm mb-1" onclick="return confirm('Are you sure you want to delete this summon?');"><i class="fas fa-trash"></i> Delete</a>
                                        </td>
                                    </tr>
                                    <?php
                                    }
                                    // free result and close statement
                                    $result->free();
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
    include('includes/footer.php');
    ?>
</div>
<!-- /.content-wrapper -->

<?php
// Include scripts
include('includes/scripts.php');
?>

<!-- Compact filter spacing -->
<style>
.card-header .form-control,
.card-header .form-select {
  height: 42px;
  padding: .45rem .75rem;
}
@media (max-width: 576px) {
  .card-header .form-control,
  .card-header .form-select {
    height: auto;
  }
}
.action-column {
    display: flex;
    flex-wrap: wrap;
    gap: 5px;
}
</style>
