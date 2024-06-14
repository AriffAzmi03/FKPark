<?php
// Include header file
include('includes/header.php');

// Include database connection file
include('includes/dbconnection.php');

// Include QR code library
include('../phpqrcode/qrlib.php');

// Retrieve summon details
if (isset($_GET['summonID'])) {
    $summonID = $_GET['summonID'];

    $query = "SELECT s.summonID, s.vehiclePlateNum, s.summonViolationType, s.summonDemerit, 
                     DATE(s.summonDate) as summonDate, TIME(s.summonDate) as summonTime, 
                     v.vehicleType, v.vehicleBrand, v.vehicleColour, 
                     st.studentID, st.studentName, st.studentPhoneNum 
              FROM summon s 
              JOIN vehicle v ON s.vehiclePlateNum = v.vehiclePlateNum 
              JOIN student st ON v.studentID = st.studentID
              WHERE s.summonID = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $summonID);
    $stmt->execute();
    $result = $stmt->get_result();
    $summon = $result->fetch_assoc();
    $stmt->close();

    if ($summon) {
        // Generate QR code
        $qrText = "Summon ID: " . $summon['summonID'] . "\nVehicle Plate Number: " . $summon['vehiclePlateNum'] . "\nViolation Type: " . $summon['summonViolationType'] . "\nDemerit Points: " . $summon['summonDemerit'] . "\nSummon Date: " . $summon['summonDate'] . "\nSummon Time: " . $summon['summonTime'] . "\nVehicle Type: " . $summon['vehicleType'] . "\nVehicle Brand: " . $summon['vehicleBrand'] . "\nVehicle Colour: " . $summon['vehicleColour'] . "\nStudent ID: " . $summon['studentID'] . "\nStudent Name: " . $summon['studentName'] . "\nStudent Phone Number: " . $summon['studentPhoneNum'];

        // Set path for saving QR code image
        $qrImagePath = '../imageQR/summon' . $summon['summonID'] . '.png';

        // Generate QR code image
        QRcode::png($qrText, $qrImagePath, QR_ECLEVEL_L, 4);
    } else {
        echo "<div class='alert alert-danger' role='alert'>Summon details not found.</div>";
        exit();
    }
} else {
    echo "<div class='alert alert-danger' role='alert'>Invalid request.</div>";
    exit();
}
?>

<div class="container mt-4">
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <span>Summon Information</span>
            <div>
                <a href="staff-edit-summon.php?summonID=<?php echo $summonID; ?>" class="btn btn-success">Update Summon</a>
                <a href="staff-manage-summon.php" class="btn btn-secondary">Back</a>
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered">
                    <tbody>
                        <tr>
                            <th>Summon ID</th>
                            <td><?php echo htmlspecialchars($summon['summonID']); ?></td>
                        </tr>
                        <tr>
                            <th>Vehicle Plate Number</th>
                            <td><?php echo htmlspecialchars($summon['vehiclePlateNum']); ?></td>
                        </tr>
                        <tr>
                            <th>Violation Type</th>
                            <td><?php echo htmlspecialchars($summon['summonViolationType']); ?></td>
                        </tr>
                        <tr>
                            <th>Demerit Points</th>
                            <td><?php echo htmlspecialchars($summon['summonDemerit']); ?></td>
                        </tr>
                        <tr>
                            <th>Summon Date</th>
                            <td><?php echo htmlspecialchars($summon['summonDate']); ?></td>
                        </tr>
                        <tr>
                            <th>Summon Time</th>
                            <td><?php echo htmlspecialchars($summon['summonTime']); ?></td>
                        </tr>
                        <tr>
                            <th>Vehicle Type</th>
                            <td><?php echo htmlspecialchars($summon['vehicleType']); ?></td>
                        </tr>
                        <tr>
                            <th>Vehicle Brand</th>
                            <td><?php echo htmlspecialchars($summon['vehicleBrand']); ?></td>
                        </tr>
                        <tr>
                            <th>Vehicle Colour</th>
                            <td><?php echo htmlspecialchars($summon['vehicleColour']); ?></td>
                        </tr>
                        <tr>
                            <th>Student ID</th>
                            <td><?php echo htmlspecialchars($summon['studentID']); ?></td>
                        </tr>
                        <tr>
                            <th>Student Name</th>
                            <td><?php echo htmlspecialchars($summon['studentName']); ?></td>
                        </tr>
                        <tr>
                            <th>Student Phone Number</th>
                            <td><?php echo htmlspecialchars($summon['studentPhoneNum']); ?></td>
                        </tr>
                        <tr>
                            <th>QR Code</th>
                            <td><img src="<?php echo $qrImagePath; ?>" alt="QR Code"></td>
                        </tr>
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

<?php
// Include footer and scripts
include('includes/footer.php');
include('includes/scripts.php');
?>
