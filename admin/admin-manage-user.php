<?php
// Include header file
include('includes/header.php');

// Include database connection file
include('includes/dbconnection.php');

// Handle delete request
if (isset($_GET['del'])) {
    $studentID = $_GET['del'];
    $delQuery = "DELETE FROM student WHERE studentID = ?";
    $stmt = $conn->prepare($delQuery);
    $stmt->bind_param("s", $studentID);
    
    if ($stmt->execute()) {
        $deleteMessage = "<div class='alert alert-success' role='alert'>User deleted successfully!</div>";
    } else {
        $deleteMessage = "<div class='alert alert-danger' role='alert'>Error: " . $stmt->error . "</div>";
    }
    
    // Close the statement
    $stmt->close();
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
                    <div class="card-header">
                        <i class="fas fa-users"></i>
                        Registered Users
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
                                        <th style="width: 10%;">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $ret = "SELECT * FROM student ORDER BY RAND() LIMIT 1000";
                                    $stmt = $conn->prepare($ret);
                                    $stmt->execute();
                                    $res = $stmt->get_result();
                                    $cnt = 1;
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
                                            <td>
                                                <a href="admin-edit-user.php?u_id=<?php echo $row->studentID; ?>" class="badge bg-success text-white"><i class="fas fa-user-edit"></i> Update</a>
                                                <a href="admin-manage-user.php?del=<?php echo $row->studentID; ?>" class="badge bg-danger text-white" onclick="return confirm('Are you sure you want to delete this user?');"><i class="fas fa-trash-alt"></i> Delete</a>
                                            </td>
                                        </tr>
                                    <?php
                                        $cnt = $cnt + 1;
                                    }
                                    ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="card-footer small text-muted">
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



