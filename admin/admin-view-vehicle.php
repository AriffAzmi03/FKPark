<?php
// Include header file
include('includes/header.php');

// Include database connection file
include('includes/dbconnection.php');

// Include QR code library
include('../phpqrcode/qrlib.php');

// Retrieve vehicle details
if (isset($_GET['vehiclePlateNum'])) {
    $vehiclePlateNum = $_GET['vehiclePlateNum'];

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
    <ol class="breadcrumb">
        <li class="breadcrumb-item">
            <a href="#">Vehicles</a>
        </li>
        <li class="breadcrumb-item active">View Vehicle</li>
    </ol>
    <hr>
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <span>Vehicle Information</span>
                    <a href="admin-edit-vehicle.php?vehiclePlateNum=<?php echo $vehiclePlateNum; ?>" class="btn btn-success">Update Vehicle</a>
                </div>
                <div class="card-body">
                    <?php if ($vehicle) { ?>
                    <h5 class="card-title">Vehicle Details</h5>
                    <p><strong>Vehicle Type:</strong> <?php echo htmlspecialchars($vehicle['vehicleType']); ?></p>
                    <p><strong>Vehicle Brand:</strong> <?php echo htmlspecialchars($vehicle['vehicleBrand']); ?></p>
                    <p><strong>Vehicle Colour:</strong> <?php echo htmlspecialchars($vehicle['vehicleColour']); ?></p>
                    <p><strong>Vehicle Plate Number:</strong> <?php echo htmlspecialchars($vehicle['vehiclePlateNum']); ?></p>
                    <p><strong>Status:</strong> <?php echo ucfirst(htmlspecialchars($vehicle['status'])); ?></p>
                    <h5 class="card-title">Owner Details</h5>
                    <p><strong>Student Name:</strong> <?php echo htmlspecialchars($vehicle['studentName']); ?></p>
                    <p><strong>Student ID:</strong> <?php echo htmlspecialchars($vehicle['studentID']); ?></p>
                    <p><strong>Phone Number:</strong> <?php echo htmlspecialchars($vehicle['studentPhoneNum']); ?></p>
                    <p><strong>Address:</strong> <?php echo htmlspecialchars($vehicle['studentAddress']); ?></p>
                    <p><strong>Level of Study:</strong> <?php echo htmlspecialchars($vehicle['studentType']); ?></p>
                    <p><strong>Year of Study:</strong> <?php echo htmlspecialchars($vehicle['studentYear']); ?></p>
                    <p><strong>Email:</strong> <?php echo htmlspecialchars($vehicle['studentEmail']); ?></p>
                    <h5 class="card-title">Vehicle Grant</h5>
                    <?php if ($vehicle['vehicleGrant']) { ?>
                    <a href="view-grant.php?vehiclePlateNum=<?php echo $vehicle['vehiclePlateNum']; ?>" class="btn btn-primary btn-sm">View Grant</a>
                    <?php } else { ?>
                    <p>No grant uploaded for this vehicle.</p>
                    <?php } ?>
                    <h5 class="card-title">QR Code</h5>
                    <img src="<?php echo $qrImagePath; ?>" alt="QR Code">
                    <?php } else { ?>
                    <div class="alert alert-info" role='alert'>Vehicle details not found.</div>
                    <?php } ?>
                </div>
                <div class="card-footer small text-muted">
                    <?php
                    date_default_timezone_set("Asia/Kuala_Lumpur");
                    echo "Generated : " . date("h:i:sa");
                    ?>
                </div>
            </div>
            <div class="mt-3 text-left">
                <a href="admin-manage-vehicle.php" class="btn btn-success">Back</a>
            </div>
        </div>
    </div>
</div>

<?php
// Include footer and scripts
include('includes/footer.php');
include('includes/scripts.php');
?>
