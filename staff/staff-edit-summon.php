<?php
// Include header file
include('includes/header.php');

// Include database connection file
include('includes/dbconnection.php');

// Get the summonID from the GET request
if (isset($_GET['summonID'])) {
    $summonID = $_GET['summonID'];

    // Retrieve summon details
    $query = "SELECT summonID, vehiclePlateNum, summonViolationType, summonDemerit, DATE(summonDate) as summonDate, TIME(summonDate) as summonTime FROM summon WHERE summonID = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $summonID);
    $stmt->execute();
    $result = $stmt->get_result();
    $summon = $result->fetch_assoc();
    $stmt->close();

    if (!$summon) {
        echo "<div class='alert alert-danger' role='alert'>Invalid request or summon not found.</div>";
        exit();
    }
} else {
    echo "<div class='alert alert-danger' role='alert'>Invalid request.</div>";
    exit();
}

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['update_summon'])) {
    // Get form data
    $vehiclePlateNum = $_POST['vehiclePlateNum'];
    $summonViolationType = $_POST['summonViolationType'];
    $summonDemerit = $_POST['summonDemerit'];
    $summonDate = $_POST['summonDate'];
    $summonTime = $_POST['summonTime'];
    $summonDateTime = $summonDate . ' ' . $summonTime;

    // Prepare and execute the update query
    $update_query = "UPDATE summon SET vehiclePlateNum = ?, summonViolationType = ?, summonDemerit = ?, summonDate = ? WHERE summonID = ?";
    $stmt = $conn->prepare($update_query);
    $stmt->bind_param("ssssi", $vehiclePlateNum, $summonViolationType, $summonDemerit, $summonDateTime, $summonID);

    if ($stmt->execute()) {
        echo "<div class='alert alert-success' role='alert'>Summon updated successfully!</div>";
        // Refresh summon details
        $query = "SELECT summonID, vehiclePlateNum, summonViolationType, summonDemerit, DATE(summonDate) as summonDate, TIME(summonDate) as summonTime FROM summon WHERE summonID = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("i", $summonID);
        $stmt->execute();
        $result = $stmt->get_result();
        $summon = $result->fetch_assoc();
    } else {
        echo "<div class='alert alert-danger' role='alert'>Error: " . $stmt->error . "</div>";
    }

    // Close the statement
    $stmt->close();
}
?>

<div class="container mt-4">
    <ol class="breadcrumb">
        <li class="breadcrumb-item">
            <a href="staff-manage-summon.php">Summons</a>
        </li>
        <li class="breadcrumb-item active">Edit Summon</li>
    </ol>
    <hr>
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <span>Edit Summon</span>
                    <a href="staff-manage-summon.php" class="btn btn-secondary">Back</a>
                </div>
                <div class="card-body">
                    <?php if ($summon) { ?>
                    <form method="POST">
                        <div class="form-group mb-3">
                            <label for="vehiclePlateNum">Vehicle Plate Number</label>
                            <input type="text" required class="form-control" id="vehiclePlateNum" name="vehiclePlateNum" value="<?php echo htmlspecialchars($summon['vehiclePlateNum']); ?>">
                        </div>
                        <div class="form-group mb-3">
                            <label for="summonViolationType">Violation Type</label>
                            <select class="form-control" id="summonViolationType" name="summonViolationType" required>
                                <option value="Parking Violation" <?php if($summon['summonViolationType'] == "Parking Violation") echo 'selected'; ?>>Parking Violation</option>
                                <option value="Campus Traffic Regulations" <?php if($summon['summonViolationType'] == "Campus Traffic Regulations") echo 'selected'; ?>>Campus Traffic Regulations</option>
                                <option value="Accident Cause" <?php if($summon['summonViolationType'] == "Accident Cause") echo 'selected'; ?>>Accident Cause</option>
                            </select>
                        </div>
                        <div class="form-group mb-3">
                            <label for="summonDemerit">Demerit Points</label>
                            <input type="text" required class="form-control" id="summonDemerit" name="summonDemerit" value="<?php echo htmlspecialchars($summon['summonDemerit']); ?>" readonly>
                        </div>
                        <div class="form-group mb-3">
                            <label for="summonDate">Summon Date</label>
                            <input type="date" required class="form-control" id="summonDate" name="summonDate" value="<?php echo htmlspecialchars($summon['summonDate']); ?>">
                        </div>
                        <div class="form-group mb-3">
                            <label for="summonTime">Summon Time</label>
                            <input type="time" required class="form-control" id="summonTime" name="summonTime" value="<?php echo htmlspecialchars($summon['summonTime']); ?>">
                        </div>
                        <button type="submit" name="update_summon" class="btn btn-success">Update Summon</button>
                    </form>
                    <?php } else { ?>
                    <div class="alert alert-info" role='alert'>Summon details not found.</div>
                    <?php } ?>
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

<script>
document.getElementById('summonViolationType').addEventListener('change', function() {
    var violationType = this.value;
    var demeritPoints = 0;

    if (violationType === 'Parking Violation') {
        demeritPoints = 10;
    } else if (violationType === 'Campus Traffic Regulations') {
        demeritPoints = 15;
    } else if (violationType === 'Accident Cause') {
        demeritPoints = 20;
    }

    document.getElementById('summonDemerit').value = demeritPoints;
});
</script>
