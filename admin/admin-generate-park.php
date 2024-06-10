<?php
// Include header file
include('includes/header.php');

// Include database connection file
include('includes/dbconnection.php');

if (isset($_GET['parkingID'])) {
    $parkingID = $_GET['parkingID'];

    // Fetch the newly added parking space from the database
    $query = "SELECT parkingID, parkingArea, parkingType, parkingAvailabilityStatus FROM parkingspace WHERE parkingID = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $parkingID);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        echo "
        <div class='container mt-4'>
            <div class='card'>
                <div class='card-header'>
                    Newly Added Parking Space
                </div>
                <div class='card-body'>
                    <div class='table-responsive'>
                        <table class='table table-bordered'>
                            <thead>
                                <tr>
                                    <th>Parking Space Name</th>
                                    <th>Parking Area</th>
                                    <th>Vehicle Type</th>
                                    <th>Availability</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>" . htmlspecialchars($row["parkingID"]) . "</td>
                                    <td>" . htmlspecialchars($row["parkingArea"]) . "</td>
                                    <td>" . htmlspecialchars($row["parkingType"]) . "</td>
                                    <td>" . htmlspecialchars($row["parkingAvailabilityStatus"]) . "</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <div class='mt-3 text-center'>
                        <a href='admin-create-park.php' class='btn btn-dark'>Add New Parking Space</a>
                        <a href='admin-manage-area.php' class='btn btn-success'>Manage Parking Area</a>
                    </div>
                </div>
            </div>
        </div>";
    } else {
        echo "<div class='alert alert-danger' role='alert'>Error: Could not retrieve the new parking space.</div>";
    }

    // Close the statement and connection
    $stmt->close();
    $conn->close();
}
?>

<hr>

<?php
// Include footer and scripts
include('includes/footer.php');
include('includes/scripts.php');
?>

<!-- Custom CSS to ensure proper table layout -->
<style>
    .table-responsive table {
        table-layout: auto; /* Adjusted to auto for better column width management */
        width: 100%;
    }
    .table-responsive th, .table-responsive td {
        word-wrap: break-word;
    }
    .table th, .table td {
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }
</style>
