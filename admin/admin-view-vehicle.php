<?php
// Include header file
include('includes/header.php');

// Include database connection file
include('includes/dbconnection.php');

// Retrieve vehicle details
if (isset($_GET['vehicleID'])) {
    $vehicleID = $_GET['vehicleID'];

    $query = "SELECT v.vehicleID, v.vehicleType, v.vehicleBrand, v.vehicleColour, v.vehiclePlateNum, v.vehicleGrant, v.status, s.studentName, s.studentID, s.studentPhoneNum, s.studentAddress, s.studentType, s.studentYear, s.studentEmail 
              FROM vehicle v 
              JOIN student s ON v.studentID = s.studentID 
              WHERE v.vehicleID = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $vehicleID);
    $stmt->execute();
    $result = $stmt->get_result();
    $vehicle = $result->fetch_assoc();
    $stmt->close();
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
                <div class="card-header">
                    Vehicle Information
                </div>
                <div class="card-body">
                    <?php if ($vehicle) { ?>
                    <h5 class="card-title">Vehicle Details</h5>
                    <p><strong>Vehicle Type:</strong> <?php echo $vehicle['vehicleType']; ?></p>
                    <p><strong>Vehicle Brand:</strong> <?php echo $vehicle['vehicleBrand']; ?></p>
                    <p><strong>Vehicle Colour:</strong> <?php echo $vehicle['vehicleColour']; ?></p>
                    <p><strong>Vehicle Plate Number:</strong> <?php echo $vehicle['vehiclePlateNum']; ?></p>
                    <p><strong>Status:</strong> <?php echo ucfirst($vehicle['status']); ?></p>
                    <h5 class="card-title">Owner Details</h5>
                    <p><strong>Student Name:</strong> <?php echo $vehicle['studentName']; ?></p>
                    <p><strong>Student ID:</strong> <?php echo $vehicle['studentID']; ?></p>
                    <p><strong>Phone Number:</strong> <?php echo $vehicle['studentPhoneNum']; ?></p>
                    <p><strong>Address:</strong> <?php echo $vehicle['studentAddress']; ?></p>
                    <p><strong>Level of Study:</strong> <?php echo $vehicle['studentType']; ?></p>
                    <p><strong>Year of Study:</strong> <?php echo $vehicle['studentYear']; ?></p>
                    <p><strong>Email:</strong> <?php echo $vehicle['studentEmail']; ?></p>
                    <h5 class="card-title">Vehicle Grant</h5>
                    <?php if ($vehicle['vehicleGrant']) { ?>
                    <a href="view-grant.php?vehicleID=<?php echo $vehicle['vehicleID']; ?>" class="btn btn-primary btn-sm">View Grant</a>
                    <?php } else { ?>
                    <p>No grant uploaded for this vehicle.</p>
                    <?php } ?>
                    <?php } else { ?>
                    <div class="alert alert-info" role='alert'>Vehicle details not found.</div>
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


