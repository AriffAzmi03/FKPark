<?php
// Start the session
session_start();

// Include header file
include('includes/header.php');

// Include database connection file
include('includes/dbconnection.php');

// Handle delete request
if (isset($_GET['del'])) {
    $summonID = $_GET['del'];
    $delQuery = "DELETE FROM summon WHERE summonID = ?";
    $stmt = $conn->prepare($delQuery);
    $stmt->bind_param("i", $summonID);

    if ($stmt->execute()) {
        $deleteMessage = "<div class='alert alert-success' role='alert'>Summon deleted successfully!</div>";
    } else {
        $deleteMessage = "<div class='alert alert-danger' role='alert'>Error: " . $stmt->error . "</div>";
    }

    // Close the statement
    $stmt->close();
}

// Handle search request
$searchQuery = "";
if (isset($_GET['search'])) {
    $searchQuery = $_GET['search'];
}

// Retrieve all summons
$query = "SELECT summonID, vehiclePlateNum, summonViolationType, summonDemerit, DATE(summonDate) as summonDate, TIME(summonDate) as summonTime FROM summon";
if (!empty($searchQuery)) {
    $query .= " WHERE vehiclePlateNum LIKE ?";
}

$stmt = $conn->prepare($query);
if (!empty($searchQuery)) {
    $searchParam = "%" . $searchQuery . "%";
    $stmt->bind_param("s", $searchParam);
}
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

        <!-- DataTables Example -->
        <div class="row justify-content-center">
            <div class="col-md-12">
                <div class="card mb-4">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <span><i class="fas fa-car"></i> Registered Summons</span>
                        <!-- Search Form -->
                        <form class="form-inline d-flex" method="get" action="">
                            <input class="form-control mr-2" type="search" placeholder="Search by Plate Number" aria-label="Search" name="search" value="<?php echo htmlspecialchars($searchQuery); ?>">
                            <button class="btn btn-outline-success" type="submit">Search</button>
                        </form>
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
                                        <th>#</th>
                                        <th>Vehicle Plate Number</th>
                                        <th>Violation Type</th>
                                        <th>Demerit Points</th>
                                        <th>Summon Date</th>
                                        <th>Summon Time</th>
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
                                        <td><?php echo $row['summonTime']; ?></td>
                                        <td class='action-column'>
                                            <a href="staff-view-summon.php?summonID=<?php echo $row['summonID']; ?>" class="btn btn-primary btn-sm mr-1 mb-1"><i class="fas fa-eye"></i> View</a>
                                            <a href="staff-edit-summon.php?summonID=<?php echo $row['summonID']; ?>" class="btn btn-success btn-sm mr-1 mb-1"><i class="fas fa-edit"></i> Edit</a>
                                            <a href="staff-manage-summon.php?del=<?php echo $row['summonID']; ?>" class="btn btn-danger btn-sm mb-1" onclick="return confirm('Are you sure you want to delete this summon?');"><i class="fas fa-trash"></i> Delete</a>
                                        </td>
                                    </tr>
                                    <?php
                                    }
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
.action-column {
    display: flex;
    flex-wrap: wrap;
    gap: 5px;
}
</style>
