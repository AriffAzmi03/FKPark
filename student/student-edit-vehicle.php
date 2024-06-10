<?php
// Include header file
include('includes/header.php');

// Include database connection file
include('includes/dbconnection.php');

// Retrieve vehicle details
if (isset($_GET['vehiclePlateNum'])) {
    $vehiclePlateNum = $_GET['vehiclePlateNum'];

    $query = "SELECT vehicleType, vehicleBrand, vehicleColour, vehiclePlateNum, vehicleGrant FROM vehicle WHERE vehiclePlateNum = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $vehiclePlateNum);
    $stmt->execute();
    $result = $stmt->get_result();
    $vehicle = $result->fetch_assoc();
    $stmt->close();
} else {
    echo "<div class='alert alert-danger' role='alert'>Invalid request.</div>";
    exit();
}

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['edit_vehicle'])) {
    // Get form data
    $vehicleType = $_POST['vehicleType'];
    $vehicleBrand = $_POST['vehicleBrand'];
    $vehicleColour = $_POST['vehicleColour'];
    $vehiclePlateNum = $_POST['vehiclePlateNum'];
    $vehicleGrant = $vehicle['vehicleGrant'];

    // Handle file upload
    if (isset($_FILES['vehicleGrant']) && $_FILES['vehicleGrant']['error'] == 0) {
        $fileType = $_FILES['vehicleGrant']['type'];
        $allowedTypes = ['application/pdf', 'image/jpeg', 'image/png'];

        if (in_array($fileType, $allowedTypes)) {
            $vehicleGrant = file_get_contents($_FILES['vehicleGrant']['tmp_name']);
        } else {
            echo "<div class='alert alert-danger' role='alert'>Error: Invalid file type. Please upload a PDF, JPG, or PNG file.</div>";
            exit();
        }
    }

    // Prepare and execute the update query
    $query = "UPDATE vehicle SET vehicleType = ?, vehicleBrand = ?, vehicleColour = ?, vehiclePlateNum = ?, vehicleGrant = ? WHERE vehiclePlateNum = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("ssssss", $vehicleType, $vehicleBrand, $vehicleColour, $vehiclePlateNum, $vehicleGrant, $vehiclePlateNum);

    if ($stmt->execute()) {
        echo "<div class='alert alert-success' role='alert'>Vehicle updated successfully!</div>";
        // Refresh vehicle details
        $stmt = $conn->prepare($query);
        $stmt->bind_param("s", $vehiclePlateNum);
        $stmt->execute();
        $result = $stmt->get_result();
        $vehicle = $result->fetch_assoc();
        $stmt->close();
    } else {
        echo "<div class='alert alert-danger' role='alert'>Error: " . $stmt->error . "</div>";
    }

    // Close the statement
    $stmt->close();
}
?>

<div class="container mt-4">
    <ol class="breadcrumb">
        <li class="breadcrumb-item">
            <a href="#">Vehicles</a>
        </li>
        <li class="breadcrumb-item active">Edit Vehicle</li>
    </ol>
    <hr>
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card">
                <div class="card-header">
                    Edit Vehicle
                </div>
                <div class="card-body">
                    <?php if ($vehicle) { ?>
                    <form id="vehicleForm" method="POST" enctype="multipart/form-data" onsubmit="return validateForm()">
                        <div class="form-group mb-3">
                            <label for="vehicleType">Vehicle Type</label>
                            <select class="form-control" id="vehicleType" name="vehicleType" required>
                                <option value="Car" <?php echo ($vehicle['vehicleType'] == 'Car') ? 'selected' : ''; ?>>Car</option>
                                <option value="Motorcycle" <?php echo ($vehicle['vehicleType'] == 'Motorcycle') ? 'selected' : ''; ?>>Motorcycle</option>
                            </select>
                        </div>
                        <div class="form-group mb-3">
                            <label for="vehicleBrand">Vehicle Brand</label>
                            <input type="text" required class="form-control" id="vehicleBrand" name="vehicleBrand" value="<?php echo $vehicle['vehicleBrand']; ?>">
                        </div>
                        <div class="form-group mb-3">
                            <label for="vehicleColour">Vehicle Colour</label>
                            <input type="text" required class="form-control" id="vehicleColour" name="vehicleColour" value="<?php echo $vehicle['vehicleColour']; ?>">
                        </div>
                        <div class="form-group mb-3">
                            <label for="vehiclePlateNum">Vehicle Plate Number</label>
                            <input type="text" required class="form-control" id="vehiclePlateNum" name="vehiclePlateNum" value="<?php echo $vehicle['vehiclePlateNum']; ?>">
                        </div>
                        <div class="form-group mb-3">
                            <label for="vehicleGrant">Vehicle Grant</label>
                            <input type="file" class="form-control" id="vehicleGrant" name="vehicleGrant" accept=".pdf, image/jpeg, image/png">
                        </div>
                        <button type="submit" name="edit_vehicle" class="btn btn-success">Save Changes</button>
                    </form>
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

<script>
function validateForm() {
    var vehicleType = document.getElementById('vehicleType').value;
    var vehicleBrand = document.getElementById('vehicleBrand').value;
    var vehicleColour = document.getElementById('vehicleColour').value;
    var vehiclePlateNum = document.getElementById('vehiclePlateNum').value;
    var vehicleGrant = document.getElementById('vehicleGrant').files[0];

    if (!vehicleType || !vehicleBrand || !vehicleColour || !vehiclePlateNum) {
        alert('Please fill in all the required fields.');
        return false;
    }

    if (vehicleGrant) {
        var allowedTypes = ['application/pdf', 'image/jpeg', 'image/png'];
        if (allowedTypes.indexOf(vehicleGrant.type) === -1) {
            alert('Please upload a file in PDF, JPG, or PNG format.');
            return false;
        }
    }

    return true;
}
</script>
