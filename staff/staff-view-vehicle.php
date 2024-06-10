<?php
// Include header file
include('includes/header.php');

// Include database connection file
include('includes/dbconnection.php');

// Retrieve approved vehicles
$query = "SELECT v.vehicleType, v.vehicleBrand, v.vehicleColour, v.vehiclePlateNum, s.studentName 
          FROM vehicle v 
          JOIN student s ON v.studentID = s.studentID 
          WHERE v.status = 'approved'";
$result = $conn->query($query);
?>

<div class="container mt-4">
    <ol class="breadcrumb">
        <li class="breadcrumb-item">
            <a href="#">Vehicles</a>
        </li>
        <li class="breadcrumb-item active">View Approved Vehicles</li>
    </ol>
    <hr>
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card">
                <div class="card-header">
                    Approved Vehicles
                </div>
                <div class="card-body">
                    <?php if ($result->num_rows > 0) { ?>
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover table-striped">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Vehicle Type</th>
                                    <th>Vehicle Brand</th>
                                    <th>Vehicle Colour</th>
                                    <th>Vehicle Plate Number</th>
                                    <th>Student Name</th>
                                    <th>Grant</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php while ($row = $result->fetch_assoc()) { ?>
                                <tr>
                                    <td><?php echo $row['vehiclePlateNum']; ?></td>
                                    <td><?php echo $row['vehicleType']; ?></td>
                                    <td><?php echo $row['vehicleBrand']; ?></td>
                                    <td><?php echo $row['vehicleColour']; ?></td>
                                    <td><?php echo $row['vehiclePlateNum']; ?></td>
                                    <td><?php echo $row['studentName']; ?></td>
                                    <td>
                                        <a href="view-grant.php?vehiclePlateNum=<?php echo $row['vehiclePlateNum']; ?>" class="btn btn-primary btn-sm">View Grant</a>
                                    </td>
                                </tr>
                                <?php } ?>
                            </tbody>
                        </table>
                    </div>
                    <?php } else { ?>
                    <div class="alert alert-info" role="alert">No approved vehicles.</div>
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