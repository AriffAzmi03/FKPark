<?php
// Include header file
include('includes/header.php');

// Include database connection file
include('includes/dbconnection.php');

// Handle delete request
if (isset($_GET['del'])) {
    $studentID = $_GET['del'];
    // Prepare delete query
    $delQuery = "DELETE FROM student WHERE studentID = ?";
    $stmt = $conn->prepare($delQuery);
    $stmt->bind_param("s", $studentID);
    
    // Execute the query and set the delete message
    if ($stmt->execute()) {
        $deleteMessage = "<div class='alert alert-success' role='alert'>User deleted successfully!</div>";
    } else {
        $deleteMessage = "<div class='alert alert-danger' role='alert'>Error: " . $stmt->error . "</div>";
    }
    
    // Close the statement
    $stmt->close();
}

// Handle search request
$searchQuery = "";
if (isset($_GET['search'])) {
    $searchQuery = $_GET['search'];
}
?>

<div id="content-wrapper">
    <div class="container-fluid mt-4">
        <!-- Breadcrumbs -->
        <div class="row justify-content-center">
            <div class="col-md-12">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item">
                        <a href="#">User</a>
                    </li>
                    <li class="breadcrumb-item active">Manage Users</li>
                </ol>
            </div>
        </div>

        <!-- DataTables Example -->
        <div class="row justify-content-center">
            <div class="col-md-12">
                <div class="card mb-4">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <span><i class="fas fa-users"></i> Registered Users</span>
                        <!-- Search Form -->
                        <form class="form-inline d-flex" method="get" action="">
                            <input class="form-control form-control-sm mr-2" type="search" placeholder="Search" aria-label="Search" name="search" value="<?php echo htmlspecialchars($searchQuery); ?>">
                            <button class="btn btn-outline-success btn-sm" type="submit">Search</button>
                        </form>
                    </div>
                    <div class="card-body">
                        <?php
                        // Display delete message if set
                        if (isset($deleteMessage)) {
                            echo $deleteMessage;
                        }
                        ?>
                        <div class="table-responsive">
                            <table class="table table-bordered table-hover table-striped" id="dataTable" width="100%" cellspacing="0">
                                <thead>
                                    <tr>
                                        <th style="width: 5%;">#</th>
                                        <th style="width: 15%;">Name</th>
                                        <th style="width: 10%;">ID</th>
                                        <th style="width: 15%;">Phone Number</th>
                                        <th style="width: 20%;">Email</th>
                                        <th style="width: 15%;">Level Of Study</th>
                                        <th style="width: 10%;">Year Of Study</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    // Prepare the query to fetch students
                                    $query = "SELECT * FROM student";
                                    if (!empty($searchQuery)) {
                                        $query .= " WHERE studentName LIKE ? OR studentID LIKE ? OR studentEmail LIKE ? OR studentPhoneNum LIKE ? OR studentType LIKE ? OR studentYear LIKE ?";
                                    }
                                    $query .= " ORDER BY created_at DESC LIMIT 1000";
                                    
                                    $stmt = $conn->prepare($query);
                                    if (!empty($searchQuery)) {
                                        $searchParam = "%" . $searchQuery . "%";
                                        $stmt->bind_param("ssssss", $searchParam, $searchParam, $searchParam, $searchParam, $searchParam, $searchParam);
                                    }
                                    $stmt->execute();
                                    $res = $stmt->get_result();
                                    $cnt = 1;
                                    // Fetch and display student records
                                    while ($row = $res->fetch_object()) {
                                    ?>
                                        <tr>
                                            <td><?php echo $cnt; ?></td>
                                            <td><?php echo $row->studentName; ?></td>
                                            <td><?php echo $row->studentID; ?></td>
                                            <td><?php echo $row->studentPhoneNum; ?></td>
                                            <td><?php echo $row->studentEmail; ?></td>
                                            <td><?php echo $row->studentType; ?></td>
                                            <td><?php echo $row->studentYear; ?></td>
                                        </tr>
                                    <?php
                                        $cnt++;
                                    }
                                    ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="card-footer small text-muted">
                        <!-- Display the current time -->
                        <?php
                        date_default_timezone_set("Asia/Kuala_Lumpur");
                        echo "Generated : " . date("h:i:sa");
                        ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- /.container-fluid -->

    <!-- Footer -->
    <?php
    // Include footer
    include('includes/footer.php');
    ?>
</div>
<!-- /.content-wrapper -->

<?php
// Include scripts
include('includes/scripts.php');
?>

<!-- Custom CSS to ensure proper table layout and button spacing -->
<style>
    .table-responsive table {
        table-layout: auto; /* Adjusted to auto for better column width management */
        width: 100%;
        font-size: 0.8em; /* Smaller font size for the table content */
    }
    .table-responsive th, .table-responsive td {
        word-wrap: break-word;
        padding: 0.2rem; /* Further reduce padding for smaller cell size */
    }
    .table th, .table td {
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .card-header .form-control, .card-header .btn {
        font-size: 0.8em; /* Smaller font size for search bar and button */
    }
</style>
