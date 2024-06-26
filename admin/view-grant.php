<?php
// Include database connection file
include('includes/dbconnection.php');

if (isset($_GET['vehiclePlateNum'])) {
    $vehiclePlateNum = $_GET['vehiclePlateNum'];

    $query = "SELECT vehicleGrant FROM vehicle WHERE vehiclePlateNum = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $vehiclePlateNum);
    $stmt->execute();
    $stmt->store_result();
    $stmt->bind_result($vehicleGrant);
    $stmt->fetch();

    if ($stmt->num_rows > 0 && !empty($vehicleGrant)) {
        // Determine the content type
        $finfo = new finfo(FILEINFO_MIME_TYPE);
        $contentType = $finfo->buffer($vehicleGrant);

        header("Content-Type: $contentType");
        echo $vehicleGrant;
    } else {
        echo "No grant found for this vehicle.";
    }

    $stmt->close();
} else {
    echo "Invalid request.";
}

$conn->close();
?>