<?php
// Start the session
session_start();

// Check if the student is logged in
if (!isset($_SESSION['studentID'])) {
    header("Location: student-login.php");
    exit();
}

// Include header file
include('includes/header.php');

// Include database connection file
include('includes/dbconnection.php');

// Get the student ID from the session
$studentID = $_SESSION['studentID'];

// Handle delete request
if (isset($_GET['del'])) {
    $vehiclePlateNum = $_GET['del'];
    $delQuery = "DELETE FROM vehicle WHERE vehiclePlateNum = ? AND studentID = ?";
    $stmt = $conn->prepare($delQuery);
    $stmt->bind_param("ss", $vehiclePlateNum, $studentID);

    if ($stmt->execute()) {
        $deleteMessage = "<div class='alert alert-success' role='alert'>Vehicle deleted successfully!</div>";
    } else {
        $deleteMessage = "<div class='alert alert-danger' role='alert'>Error: " . $stmt->error . "</div>";
    }

    // Close the statement
    $stmt->close();
}

// Retrieve vehicles registered by the student
$query = "SELECT vehiclePlateNum, vehicleType, vehicleBrand, vehicleColour, status FROM vehicle WHERE studentID = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("s", $studentID);
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
                        <a href="#">Vehicles</a>
                    </li>
                    <li class="breadcrumb-item active">Manage Vehicles</li>
                </ol>
            </div>
        </div>

        <!-- DataTables Example -->
        <div class="row justify-content-center">
            <div class="col-md-12">
                <div class="card mb-4">
                    <div class="card-header">
                        <i class="fas fa-car"></i>
                        My Vehicles
                    </div>
                    <div class="card-body">
                        <?php
                        // Display delete message if set
                        if (isset($deleteMessage)) {
                            echo $deleteMessage;
                        }
                        // Display success message if set
                        if (isset($_SESSION['success_message'])) {
                            echo "<div class='alert alert-success' role='alert'>" . $_SESSION['success_message'] . "</div>";
                            unset($_SESSION['success_message']);
                        }
                        ?>
                        <div class="table-responsive">
                            <table class="table table-bordered table-hover table-striped" width="100%" cellspacing="0">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Vehicle Type</th>
                                        <th>Vehicle Brand</th>
                                        <th>Vehicle Colour</th>
                                        <th>Vehicle Plate Number</th>
                                        <th>Status</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $cnt = 1;
                                    while ($row = $result->fetch_assoc()) {
                                        echo "<tr>";
                                        echo "<td>" . $cnt . "</td>";
                                        echo "<td>" . $row['vehicleType'] . "</td>";
                                        echo "<td>" . $row['vehicleBrand'] . "</td>";
                                        echo "<td>" . $row['vehicleColour'] . "</td>";
                                        echo "<td>" . $row['vehiclePlateNum'] . "</td>";
                                        echo "<td>" . ucfirst($row['status']) . "</td>";
                                        echo "<td class='action-column'>";
                                        if (strtolower($row['status']) != 'pending') {
                                            echo "<a href='student-view-vehicle.php?plateNum=" . $row['vehiclePlateNum'] . "' class='btn btn-primary btn-sm mr-1 mb-1'><i class='fas fa-eye'></i> View</a>";
                                            echo "<a href='student-edit-vehicle.php?plateNum=" . $row['vehiclePlateNum'] . "' class='btn btn-success btn-sm mr-1 mb-1'><i class='fas fa-edit'></i> Update</a>";
                                        }
                                        echo "<a href='student-manage-vehicle.php?del=" . $row['vehiclePlateNum'] . "' class='btn btn-danger btn-sm mb-1' onclick='return confirm(\"Are you sure you want to delete this vehicle?\");'><i class='fas fa-trash'></i> Delete</a>";
                                        echo "</td>";
                                        echo "</tr>";
                                        $cnt++;
                                    }
                                    ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Include footer and scripts -->
    <?php
    include('includes/footer.php');
    include('includes/scripts.php');
    ?>

</div>

<!-- Custom CSS for table and button spacing -->
<style>
.table th, .table td {
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

.table-responsive {
    margin-top: 15px;
}

.action-column {
    display: flex;
    flex-wrap: wrap;
    gap: 5px;
}
</style>
