<?php
// Include header file
include('includes/header.php');

// Include database connection file
include('includes/dbconnection.php');

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['add_summon'])) {
    // Get form data
    $vehiclePlateNum = $_POST['vehiclePlateNum'];
    $summonViolationType = $_POST['summonViolationType'];
    $summonDemerit = $_POST['summonDemerit'];
    $summonDate = $_POST['summonDate'];

    // Prepare and execute the insert query
    $query = "INSERT INTO summon (vehiclePlateNum, summonViolationType, summonDemerit, summonDate)
              VALUES (?, ?, ?, ?)";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("ssis", $vehiclePlateNum, $summonViolationType, $summonDemerit, $summonDate);

    if ($stmt->execute()) {
        echo "<div class='alert alert-success' role='alert'>New summon added successfully!</div>";
    } else {
        echo "<div class='alert alert-danger' role='alert'>Error: " . $stmt->error . "</div>";
    }

    // Close the statement
    $stmt->close();
}

// Close the database connection
$conn->close();
?>

<div class="container mt-4">
    <!-- Breadcrumbs-->
    <ol class="breadcrumb">
        <li class="breadcrumb-item">
            <a href="#">Summons</a>
        </li>
        <li class="breadcrumb-item active">Add Summon</li>
    </ol>
    <hr>
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    Add Summon
                </div>
                <div class="card-body">
                    <!-- Add Summon Form -->
                    <form method="POST" id="summonForm">
                        <div class="form-group mb-3">
                            <label for="vehiclePlateNum">Vehicle Plate Number</label>
                            <input type="text" required class="form-control" id="vehiclePlateNum" name="vehiclePlateNum">
                        </div>
                        <div class="form-group mb-3">
                            <label for="summonViolationType">Violation Type</label>
                            <select class="form-control" id="summonViolationType" name="summonViolationType" required>
                                <option value="">Select Violation Type</option>
                                <option value="Parking Violation">Parking Violation</option>
                                <option value="Campus Traffic Regulations">Campus Traffic Regulations</option>
                                <option value="Accident Cause">Accident Cause</option>
                            </select>
                        </div>
                        <div class="form-group mb-3">
                            <label for="summonDemerit">Demerit Points</label>
                            <input type="number" required class="form-control" id="summonDemerit" name="summonDemerit" readonly>
                        </div>
                        <div class="form-group mb-3">
                            <label for="summonDate">Summon Date</label>
                            <input type="date" required class="form-control" id="summonDate" name="summonDate">
                        </div>
                        <button type="submit" name="add_summon" class="btn btn-success">Add Summon</button>
                        <button type="reset" class="btn btn-secondary">Reset</button>
                    </form>
                    <!-- End Form -->
                </div>
            </div>
        </div>
    </div>
</div>

<hr>

<?php
// Include footer and scripts
include('includes/footer.php');
include('includes/scripts.php');
?>

<script>
document.getElementById('summonViolationType').addEventListener('change', function() {
    var violationType = this.value;
    var demeritPoints = 0;

    if (violationType === 'Parking Violation') {
        demeritPoints = 10;
    } else if (violationType === 'Campus Traffic Regulations') {
        demeritPoints = 15;
    } else if (violationType === 'Accident Cause') {
        demeritPoints = 20;
    }

    document.getElementById('summonDemerit').value = demeritPoints;
});
</script>
