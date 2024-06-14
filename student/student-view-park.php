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

if (isset($_GET['parkingID'])) {
    $parkingID = $_GET['parkingID'];

    // Fetch the newly added parking space from the database
    $query = "SELECT parkingID, parkingArea, parkingType, parkingAvailabilityStatus, parkingAddDetail FROM parkingspace WHERE parkingID = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $parkingID);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        // Generate QR code
        $qrText = "Parking ID: " . $row['parkingID'] . "\nArea: " . $row['parkingArea'] . "\nType: " . $row['parkingType'] . "\nAvailability: " . $row['parkingAvailabilityStatus'] . "\nAdditional Details: " . $row['parkingAddDetail'];
        // Include QR code library
        include('../phpqrcode/qrlib.php');
        
        // Set path for saving QR code image
        $qrImagePath = '../imageQR/parking' . $row['parkingID'] . '.png';
        
        // Generate QR code image
        QRcode::png($qrText, $qrImagePath, QR_ECLEVEL_L, 4);

        // Display the parking space details along with QR code
        echo "
        <div class='container mt-4'>
            <div class='card'>
                <div class='card-header d-flex justify-content-between align-items-center'>
                    <span>Parking Space</span>
                    <a href='student-manage-area.php' class='btn btn-secondary'>Back</a>
                </div>
                <div class='card-body'>
                    <div class='table-responsive'>
                        <table class='table table-bordered'>
                            <tbody>
                                <tr>
                                    <th>Parking Space Name</th>
                                    <td>" . htmlspecialchars($row["parkingID"]) . "</td>
                                </tr>
                                <tr>
                                    <th>Parking Area</th>
                                    <td>" . htmlspecialchars($row["parkingArea"]) . "</td>
                                </tr>
                                <tr>
                                    <th>Vehicle Type</th>
                                    <td>" . htmlspecialchars($row["parkingType"]) . "</td>
                                </tr>
                                <tr>
                                    <th>Availability</th>
                                    <td>" . htmlspecialchars($row["parkingAvailabilityStatus"]) . "</td>
                                </tr>
                                <tr>
                                    <th>Additional Notes</th>
                                    <td>" . htmlspecialchars($row["parkingAddDetail"]) . "</td>
                                </tr>
                                <tr>
                                    <th>QR Code</th>
                                    <td><img src='" . $qrImagePath . "' alt='QR Code'></td>
                                </tr>
                            </tbody>
                        </table>
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
