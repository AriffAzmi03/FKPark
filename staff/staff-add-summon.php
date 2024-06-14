<?php
// Start output buffering at the beginning
ob_start();

// Include header file
include('includes/header.php');

// Include database connection file
include('includes/dbconnection.php');

// Include QR code library
include('../phpqrcode/qrlib.php');

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['add_summon'])) {
    // Get form data
    $vehiclePlateNum = $_POST['vehiclePlateNum'];
    $summonViolationType = $_POST['summonViolationType'];
    $summonDemerit = $_POST['summonDemerit'];
    $summonDate = $_POST['summonDate'];
    $summonTime = $_POST['summonTime'];
    $summonDateTime = $summonDate . ' ' . $summonTime;

    // Prepare and execute the insert query
    $query = "INSERT INTO summon (vehiclePlateNum, summonViolationType, summonDemerit, summonDate)
              VALUES (?, ?, ?, ?)";
    $stmt = $conn->prepare($query);
    if ($stmt === false) {
        echo "<div class='alert alert-danger' role='alert'>Prepare failed: " . htmlspecialchars($conn->error) . "</div>";
        exit();
    }
    $stmt->bind_param("ssis", $vehiclePlateNum, $summonViolationType, $summonDemerit, $summonDateTime);

    if ($stmt->execute()) {
        // Prepare the URL for the QR code
        $summonLink = "http://localhost/FKPark/staff/staff-view-summon.php?summonID=" . $stmt->insert_id;

        // Create QR Code directory if it does not exist
        $qrCodeDir = "../imageQR";
        if (!is_dir($qrCodeDir)) {
            mkdir($qrCodeDir, 0755, true);
        }

        // Generate QR Code with the full URL
        $qrCodeFile = $qrCodeDir . "/summon" . $stmt->insert_id . ".png";
        QRcode::png($summonLink, $qrCodeFile, QR_ECLEVEL_L, 5);

        // Redirect to staff-manage-summon.php with a success message
        header("Location: staff-manage-summon.php?success=1");
        exit();
    } else {
        echo "<div class='alert alert-danger' role='alert'>Execute failed: " . htmlspecialchars($stmt->error) . "</div>";
    }

    // Close the statement
    $stmt->close();
}

// Close the database connection
$conn->close();
?>

<div class="container mt-4">
    <!-- Breadcrumbs-->
    <ol class="breadcrumb">
        <li class="breadcrumb-item">
            <a href="#">Summons</a>
        </li>
        <li class="breadcrumb-item active">Add Summon</li>
    </ol>
    <hr>
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    Add Summon
                </div>
                <div class="card-body">
                    <!-- Add Summon Form -->
                    <form method="POST" id="summonForm">
                        <div class="form-group mb-3">
                            <label for="vehiclePlateNum">Vehicle Plate Number</label>
                            <input type="text" required class="form-control" id="vehiclePlateNum" name="vehiclePlateNum">
                        </div>
                        <div class="form-group mb-3">
                            <label for="summonViolationType">Violation Type</label>
                            <select class="form-control" id="summonViolationType" name="summonViolationType" required>
                                <option value="">Select Violation Type</option>
                                <option value="Parking Violation">Parking Violation</option>
                                <option value="Campus Traffic Regulations">Campus Traffic Regulations</option>
                                <option value="Accident Cause">Accident Cause</option>
                            </select>
                        </div>
                        <div class="form-group mb-3">
                            <label for="summonDemerit">Demerit Points</label>
                            <input type="number" required class="form-control" id="summonDemerit" name="summonDemerit" readonly>
                        </div>
                        <div class="form-group mb-3">
                            <label for="summonDate">Summon Date</label>
                            <input type="date" required class="form-control" id="summonDate" name="summonDate">
                        </div>
                        <div class="form-group mb-3">
                            <label for="summonTime">Summon Time</label>
                            <input type="time" required class="form-control" id="summonTime" name="summonTime">
                        </div>
                        <button type="submit" name="add_summon" class="btn btn-primary">Add Summon</button>
                        <button type="reset" class="btn btn-light">Reset</button>
                    </form>
                    <!-- End Form -->
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

// End output buffering and flush the output
ob_end_flush();
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
