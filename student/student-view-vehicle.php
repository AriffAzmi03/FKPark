<?php
// Include header file
include('includes/header.php');

// Include database connection file
include('includes/dbconnection.php');

// Include QR code library
include('../phpqrcode/qrlib.php');

// Retrieve vehicle details
if (isset($_GET['plateNum'])) {
    $vehiclePlateNum = $_GET['plateNum'];

    $query = "SELECT v.vehicleType, v.vehicleBrand, v.vehicleColour, v.vehiclePlateNum, v.vehicleGrant, v.status, s.studentName, s.studentID, s.studentPhoneNum, s.studentAddress, s.studentType, s.studentYear, s.studentEmail 
              FROM vehicle v 
              JOIN student s ON v.studentID = s.studentID 
              WHERE v.vehiclePlateNum = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $vehiclePlateNum);
    $stmt->execute();
    $result = $stmt->get_result();
    $vehicle = $result->fetch_assoc();
    $stmt->close();

    if ($vehicle) {
        // Generate QR code
        $qrText = "Vehicle Plate Number: " . $vehicle['vehiclePlateNum'] . "\nType: " . $vehicle['vehicleType'] . "\nBrand: " . $vehicle['vehicleBrand'] . "\nColour: " . $vehicle['vehicleColour'] . "\nStatus: " . ucfirst($vehicle['status']) . "\nOwner: " . $vehicle['studentName'] . "\nStudent ID: " . $vehicle['studentID'] . "\nPhone: " . $vehicle['studentPhoneNum'] . "\nAddress: " . $vehicle['studentAddress'] . "\nLevel of Study: " . $vehicle['studentType'] . "\nYear of Study: " . $vehicle['studentYear'] . "\nEmail: " . $vehicle['studentEmail'];

        // Set path for saving QR code image
        $qrImagePath = '../imageQR/vehicle' . $vehicle['vehiclePlateNum'] . '.png';

        // Generate QR code image
        QRcode::png($qrText, $qrImagePath, QR_ECLEVEL_L, 4);
    } else {
        echo "<div class='alert alert-danger' role='alert'>Vehicle details not found.</div>";
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
            <span>Vehicle Information</span>
            <div>
                <a href="student-edit-vehicle.php?plateNum=<?php echo $vehiclePlateNum; ?>" class="btn btn-success">Update Vehicle</a>
                <a href="student-manage-vehicle.php" class="btn btn-secondary">Back</a>
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered">
                    <tbody>
                        <tr>
                            <th>Vehicle Type</th>
                            <td><?php echo htmlspecialchars($vehicle['vehicleType']); ?></td>
                        </tr>
                        <tr>
                            <th>Vehicle Brand</th>
                            <td><?php echo htmlspecialchars($vehicle['vehicleBrand']); ?></td>
                        </tr>
                        <tr>
                            <th>Vehicle Colour</th>
                            <td><?php echo htmlspecialchars($vehicle['vehicleColour']); ?></td>
                        </tr>
                        <tr>
                            <th>Vehicle Plate Number</th>
                            <td><?php echo htmlspecialchars($vehicle['vehiclePlateNum']); ?></td>
                        </tr>
                        <tr>
                            <th>Status</th>
                            <td><?php echo ucfirst(htmlspecialchars($vehicle['status'])); ?></td>
                        </tr>
                        <tr>
                            <th>Student Name</th>
                            <td><?php echo htmlspecialchars($vehicle['studentName']); ?></td>
                        </tr>
                        <tr>
                            <th>Student ID</th>
                            <td><?php echo htmlspecialchars($vehicle['studentID']); ?></td>
                        </tr>
                        <tr>
                            <th>Phone Number</th>
                            <td><?php echo htmlspecialchars($vehicle['studentPhoneNum']); ?></td>
                        </tr>
                        <tr>
                            <th>Address</th>
                            <td><?php echo htmlspecialchars($vehicle['studentAddress']); ?></td>
                        </tr>
                        <tr>
                            <th>Level of Study</th>
                            <td><?php echo htmlspecialchars($vehicle['studentType']); ?></td>
                        </tr>
                        <tr>
                            <th>Year of Study</th>
                            <td><?php echo htmlspecialchars($vehicle['studentYear']); ?></td>
                        </tr>
                        <tr>
                            <th>Email</th>
                            <td><?php echo htmlspecialchars($vehicle['studentEmail']); ?></td>
                        </tr>
                        <tr>
                            <th>Vehicle Grant</th>
                            <td>
                                <?php if ($vehicle['vehicleGrant']) { ?>
                                    <a href="view-grant.php?vehiclePlateNum=<?php echo $vehicle['vehiclePlateNum']; ?>" class="btn btn-primary btn-sm">View Grant</a>
                                <?php } else { ?>
                                    <p>No grant uploaded for this vehicle.</p>
                                <?php } ?>
                            </td>
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