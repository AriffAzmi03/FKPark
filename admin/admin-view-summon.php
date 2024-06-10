<?php
// Include header file
include('includes/header.php');

// Include database connection file
include('includes/dbconnection.php');

// Get the summonID from the GET request
if (isset($_GET['summonID'])) {
    $summonID = $_GET['summonID'];

    // Retrieve summon details
    $query = "SELECT s.summonID, s.vehiclePlateNum, s.summonViolationType, s.summonDemerit, s.summonDate, v.studentID, v.vehicleType, v.vehicleBrand, v.vehicleColour, st.studentName, st.studentPhoneNum, st.studentAddress, st.studentType, st.studentYear, st.studentEmail
              FROM summon s
              JOIN vehicle v ON s.vehiclePlateNum = v.vehiclePlateNum
              JOIN student st ON v.studentID = st.studentID
              WHERE s.summonID = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $summonID);
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
?>

<div class="container mt-4">
    <ol class="breadcrumb">
        <li class="breadcrumb-item">
            <a href="admin-manage-summon.php">Summons</a>
        </li>
        <li class="breadcrumb-item active">View Summon</li>
    </ol>
    <hr>
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card">
                <div class="card-header">
                    Summon Information
                </div>
                <div class="card-body">
                    <?php if ($summon) { ?>
                    <h5 class="card-title">Summon Details</h5>
                    <p><strong>Summon ID:</strong> <?php echo $summon['summonID']; ?></p>
                    <p><strong>Vehicle Plate Number:</strong> <?php echo $summon['vehiclePlateNum']; ?></p>
                    <p><strong>Violation Type:</strong> <?php echo $summon['summonViolationType']; ?></p>
                    <p><strong>Demerit Points:</strong> <?php echo $summon['summonDemerit']; ?></p>
                    <p><strong>Summon Date:</strong> <?php echo $summon['summonDate']; ?></p>
                    <h5 class="card-title">Vehicle Details</h5>
                    <p><strong>Vehicle Type:</strong> <?php echo $summon['vehicleType']; ?></p>
                    <p><strong>Vehicle Brand:</strong> <?php echo $summon['vehicleBrand']; ?></p>
                    <p><strong>Vehicle Colour:</strong> <?php echo $summon['vehicleColour']; ?></p>
                    <h5 class="card-title">Owner Details</h5>
                    <p><strong>Student Name:</strong> <?php echo $summon['studentName']; ?></p>
                    <p><strong>Student ID:</strong> <?php echo $summon['studentID']; ?></p>
                    <p><strong>Phone Number:</strong> <?php echo $summon['studentPhoneNum']; ?></p>
                    <p><strong>Address:</strong> <?php echo $summon['studentAddress']; ?></p>
                    <p><strong>Level of Study:</strong> <?php echo $summon['studentType']; ?></p>
                    <p><strong>Year of Study:</strong> <?php echo $summon['studentYear']; ?></p>
                    <p><strong>Email:</strong> <?php echo $summon['studentEmail']; ?></p>
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
