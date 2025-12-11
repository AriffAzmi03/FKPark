<?php
// Start output buffering at the beginning
ob_start();

// Include header file
include('includes/header.php');

// Include database connection file
include('includes/dbconnection.php');

// Include QR code library
include('../phpqrcode/qrlib.php');

// Simple logger helper (optional)
function app_log($msg) {
    // file_put_contents(__DIR__ . '/error.log', date('[Y-m-d H:i:s] ') . $msg . PHP_EOL, FILE_APPEND);
}

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['add_summon'])) {
    // Get and normalize form data
    $vehiclePlateNum = strtoupper(trim($_POST['vehiclePlateNum'])); // normalize
    $summonViolationType = trim($_POST['summonViolationType']);
    $summonDemerit = (int)$_POST['summonDemerit'];
    $summonDate = $_POST['summonDate'];
    $summonTime = $_POST['summonTime'];
    $summonDateTime = $summonDate . ' ' . $summonTime;

    // Basic server-side validation
    if ($vehiclePlateNum === '' || $summonViolationType === '' || $summonDemerit <= 0) {
        echo "<div class='alert alert-danger' role='alert'>Please fill all required fields correctly.</div>";
    } else {
        // 1) Check vehicle exists to avoid FK constraint error
        $sqlCheck = "SELECT studentID FROM vehicle WHERE vehiclePlateNum = ? LIMIT 1";
        $chkStmt = $conn->prepare($sqlCheck);
        if ($chkStmt === false) {
            echo "<div class='alert alert-danger' role='alert'>Database error: " . htmlspecialchars($conn->error) . "</div>";
            exit();
        }
        $chkStmt->bind_param("s", $vehiclePlateNum);
        $chkStmt->execute();
        $res = $chkStmt->get_result();
        $owner = $res->fetch_assoc();
        $chkStmt->close();

        if (!$owner) {
            // Friendly message: vehicle not registered
            echo "<div class='alert alert-warning' role='alert'>Vehicle <strong>" . htmlspecialchars($vehiclePlateNum) . "</strong> is not registered. Please register the vehicle first or use the unregistered-summon workflow.</div>";
        } else {
            // 2) Insert summon and update demerit inside a transaction
            try {
                $conn->begin_transaction();

                // Insert summon
                $query = "INSERT INTO summon (vehiclePlateNum, summonViolationType, summonDemerit, summonDate)
                          VALUES (?, ?, ?, ?)";
                $stmt = $conn->prepare($query);
                if ($stmt === false) throw new Exception("Prepare insert failed: " . $conn->error);
                $stmt->bind_param("ssis", $vehiclePlateNum, $summonViolationType, $summonDemerit, $summonDateTime);
                if (!$stmt->execute()) throw new Exception("Execute insert failed: " . $stmt->error);
                $summonID = $stmt->insert_id;
                $stmt->close();

                // Update student demerit_points (make sure student table has this column)
                $studentID = $owner['studentID'];
                $sqlUpdate = "UPDATE student SET demerit_points = COALESCE(demerit_points,0) + ? WHERE studentID = ?";
                $st2 = $conn->prepare($sqlUpdate);
                if ($st2 === false) throw new Exception("Prepare update failed: " . $conn->error);
                $st2->bind_param("is", $summonDemerit, $studentID);
                if (!$st2->execute()) throw new Exception("Execute update failed: " . $st2->error);
                $st2->close();

                // Generate QR after DB operations succeed
                $summonLink = "http://localhost/FKPark/staff/staff-view-summon.php?summonID=" . $summonID;
                $qrCodeDir = "../imageQR";
                if (!is_dir($qrCodeDir)) {
                    if (!mkdir($qrCodeDir, 0755, true)) {
                        throw new Exception("Failed to create QR directory.");
                    }
                }
                $qrCodeFile = $qrCodeDir . "/summon" . $summonID . ".png";
                QRcode::png($summonLink, $qrCodeFile, QR_ECLEVEL_L, 5);

                // Commit transaction
                $conn->commit();

                // Redirect with success
                header("Location: staff-manage-summon.php?success=1");
                exit();

            } catch (Exception $e) {
                if ($conn->in_transaction) $conn->rollback();
                app_log($e->getMessage());
                echo "<div class='alert alert-danger' role='alert'>Failed to save summon. Please try again or contact admin.</div>";
            }
        }
    }
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
