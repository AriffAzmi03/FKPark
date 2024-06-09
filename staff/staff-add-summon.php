<?php
// Include header file
include('includes/header.php');

// Include database connection file
include('includes/dbconnection.php');

//session_start();
if (isset($_SESSION["username"])) {

    try {
        $statement = $connect->prepare("SELECT * FROM inv_order ORDER BY order_id DESC");
        $statement->execute();
        $all_result = $statement->fetchAll();
        $total_rows = $statement->rowCount();

        if (isset($_POST["create_invoice"])) {
            $connect->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $statement = $connect->prepare("INSERT INTO inv_order (order_no, order_date, order_receiver_name, order_receiver_address, order_datetime) VALUES (:order_no, :order_date, :order_receiver_name, :order_receiver_address, :order_datetime)");

            $statement->execute(
                array(
                    ':order_no'               =>  trim($_POST["order_no"]),
                    ':order_date'             =>  trim($_POST["order_date"]),
                    ':order_receiver_name'    =>  trim($_POST["order_receiver_name"]),
                    ':order_receiver_address' =>  trim($_POST["order_receiver_address"]),
                    ':order_datetime'         =>  time()
                )
            );

            $statement = $connect->query("SELECT LAST_INSERT_ID()");
            $order_id = $statement->fetchColumn();

            for ($count = 0; $count < $_POST["total_item"]; $count++) {
                $statement = $connect->prepare("
                    INSERT INTO inv_order_item 
                    (order_id, item_name, order_item_quantity)
                    VALUES (:order_id, :item_name, :order_item_quantity)
                ");

                $statement->execute(
                    array(
                        ':order_id'               =>  $order_id,
                        ':item_name'              =>  trim($_POST["item_name"][$count]),
                        ':order_item_quantity'    =>  trim($_POST["order_item_quantity"][$count])
                    )
                );
            }

            header("location:invoice.php");
        }
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
    }

    if (isset($_POST["update_invoice"])) {
        $order_id = $_POST["order_id"];

        $statement = $connect->prepare("
            DELETE FROM inv_order_item WHERE order_id = :order_id
        ");
        $statement->execute(
            array(
                ':order_id' => $order_id
            )
        );

        for ($count = 0; $count < $_POST["total_item"]; $count++) {
            $statement = $connect->prepare("
                INSERT INTO inv_order_item 
                (order_id, item_name, order_item_quantity) 
                VALUES (:order_id, :item_name, :order_item_quantity)
            ");
            $statement->execute(
                array(
                    ':order_id'                 =>  $order_id,
                    ':item_name'                =>  trim($_POST["item_name"][$count]),
                    ':order_item_quantity'      =>  trim($_POST["order_item_quantity"][$count])
                )
            );
        }

        $statement = $connect->prepare("
            UPDATE inv_order 
            SET order_no = :order_no, 
                order_date = :order_date, 
                order_receiver_name = :order_receiver_name, 
                order_receiver_address = :order_receiver_address
            WHERE order_id = :order_id 
        ");

        $statement->execute(
            array(
                ':order_no'               =>  trim($_POST["order_no"]),
                ':order_date'             =>  trim($_POST["order_date"]),
                ':order_receiver_name'    =>  trim($_POST["order_receiver_name"]),
                ':order_receiver_address' =>  trim($_POST["order_receiver_address"]),
                ':order_id'               =>  $order_id
            )
        );

        header("location:invoice.php");
    }

    if (isset($_GET["delete"]) && isset($_GET["id"])) {
        $statement = $connect->prepare("DELETE FROM inv_order WHERE order_id = :id");
        $statement->execute(
            array(
                ':id' => $_GET["id"]
            )
        );
        $statement = $connect->prepare("DELETE FROM inv_order_item WHERE order_id = :id");
        $statement->execute(
            array(
                ':id' => $_GET["id"]
            )
        );
        header("location:invoice.php");
    }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Traffic Summon</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex, nofollow">
    <link rel="stylesheet" href="css/bootstrap.min.css">
    <script src="js/jquery.min.js"></script>
    <script src="js/bootstrap.min.js"></script>
    <script src="js/jquery.dataTables.min.js"></script>
    <script src="js/dataTables.bootstrap.min.js"></script>
    <link rel="stylesheet" href="css/dataTables.bootstrap.min.css">
    <link rel="stylesheet" href="css/bootstrap-datepicker.css">
    <link rel="stylesheet" href="css/invoice.css">
    <script src="js/bootstrap-datepicker.js"></script>
</head>
<body style="background: rgb(233, 233, 233); font-family: Segoe UI light;">
    <div class="container-fluid">
        <br>
        <?php if (isset($_GET["add"])) { ?>
        <form method="post" id="invoice_form">
            <div class="table-responsive">
                <nav class="navbar navbar-default card">
                    <div class="container-fluid">
                        <div class="navbar-header">
                            <a class="navbar-brand" href="invoice.php?add=1">Traffic Summon</a>
                        </div>
                    </div>
                </nav>
                <table class="table table-bordered card">
                    <tr>
                        <td colspan="2">
                            <div class="row">
                                <div class="col-md-8">
                                    <b>RECEIVER INFORMATION</b><br />
                                    <div class="form-group">
                                        <input type="text" name="order_receiver_name" id="order_receiver_name" class="form-control input-sm" placeholder="Enter Name" />
                                    </div>
                                    <div class="form-group">
                                        <textarea name="order_receiver_address" id="order_receiver_address" class="form-control" placeholder="Plate Number"></textarea>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <b>INVOICE DETAILS</b><br />
                                    <div class="form-group">
                                        <input type="text" name="order_no" id="order_no" class="form-control input-sm number_only" maxlength="6" placeholder="Enter Summon ID" />
                                    </div>
                                    <div class="form-group">
                                        <input type="text" name="order_date" id="order_date" class="form-control input-sm" readonly placeholder="Select Invoice Date" />
                                    </div>
                                </div>
                            </div>
                            <br />
                            <table id="invoice-item-table" class="table table-bordered table-hover table-striped">
                                <tr>
                                    <th width="5%">S/N.</th>
                                    <th width="20%">Type Of Summon</th>
                                    <th width="10%">Demerit Points</th>
                                </tr>
                                <tr>
                                    <td><span id="sr_no">1</span></td>
                                    <td>
                                        <select name="item_name[]" id="item_name1" class="form-control input-sm item_name" data-srno="1">
                                            <option value="Parking Violation">Parking Violation</option>
                                            <option value="Campus Traffic Regulations">Campus Traffic Regulations</option>
                                            <option value="Accident Cause">Accident Cause</option>
                                        </select>
                                    </td>
                                    <td><input type="text" name="order_item_quantity[]" id="order_item_quantity1" data-srno="1" class="form-control input-sm order_item_quantity" readonly /></td>
                                </tr>
                            </table>
                            <div align="right">
                                <button type="button" name="add_row" id="add_row" class="btn btn-success">+</button>
                            </div>
                        </td>
                    </tr>
                    <tr>
                    </tr>
                    <tr>
                        <td colspan="2"></td>
                    </tr>
                    <tr>
                        <td colspan="2" align="center">
                            <input type="hidden" name="total_item" id="total_item" value="1" />
                            <input type="submit" name="create_invoice" id="create_invoice" class="btn btn-success" value="Create" />
                        </td>
                    </tr>
                </table>
            </div>
        </form>
        <script>
        $(document).ready(function(){
            var final_total_amt = $('#final_total_amt').text();
            var count = 1;

            $(document).on('click', '#add_row', function(){
                count++;
                $('#total_item').val(count);
                var html_code = '';
                html_code += '<tr id="row_id_'+count+'">';
                html_code += '<td><span id="sr_no">'+count+'</span></td>';
                html_code += '<td><select name="item_name[]" id="item_name'+count+'" data-srno="'+count+'" class="form-control input-sm item_name"><option value="Parking Violation">Parking Violation</option><option value="Campus Traffic Regulations">Campus Traffic Regulations</option><option value="Accident Cause">Accident Cause</option></select></td>';
                html_code += '<td><input type="text" name="order_item_quantity[]" id="order_item_quantity'+count+'" data-srno="'+count+'" class="form-control input-sm order_item_quantity" readonly /></td>';
                html_code += '<td><button type="button" name="remove_row" id="'+count+'" class="btn btn-danger btn-xs remove_row">X</button></td>';
                html_code += '</tr>';
                $('#invoice-item-table').append(html_code);
            });

            $(document).on('change', '.item_name', function(){
                var srno = $(this).data('srno');
                var item_name = $(this).val();
                var demerit_points = 0;
                if(item_name == 'Parking Violation') {
                    demerit_points = 10;
                } else if(item_name == 'Campus Traffic Regulations') {
                    demerit_points = 15;
                } else if(item_name == 'Accident Cause') {
                    demerit_points = 20;
                }
                $('#order_item_quantity'+srno).val(demerit_points);
            });

            $(document).on('click', '.remove_row', function(){
                var row_id = $(this).attr("id");
                $('#row_id_'+row_id).remove();
                count--;
                $('#total_item').val(count);
            });

            $('#create_invoice').click(function(){
                if($.trim($('#order_receiver_name').val()).length == 0)
                {
                    alert("Enter Name");
                    return false;
                }

                if($.trim($('#order_no').val()).length == 0)
                {
                    alert("Please Enter Invoice Number");
                    return false;
                }

                if($.trim($('#order_date').val()).length == 0)
                {
                    alert("Please Select Invoice Date");
                    return false;
                }

                for(var no=1; no<=count; no++)
                {
                    if($.trim($('#item_name'+no).val()).length == 0)
                    {
                        alert("Please Enter Item Name");
                        $('#item_name'+no).focus();
                        return false;
                    }

                    if($.trim($('#order_item_quantity'+no).val()).length == 0)
                    {
                        alert("Please Enter Demerit Points");
                        $('#order_item_quantity'+no).focus();
                        return false;
                    }

                }

                $('#invoice_form').submit();

            });

        });
        </script>
        <?php } elseif (isset($_GET["update"]) && isset($_GET["id"])) { 
            $statement = $connect->prepare("
                SELECT * FROM inv_order 
                WHERE order_id = :order_id
                LIMIT 1
            ");
            $statement->execute(
                array(
                    ':order_id' => $_GET["id"]
                )
            );
            $result = $statement->fetchAll();
            foreach($result as $row) {
        ?>
        <script>
        $(document).ready(function(){
            $('#order_no').val("<?php echo $row["order_no"]; ?>");
            $('#order_date').val("<?php echo $row["order_date"]; ?>");
            $('#order_receiver_name').val("<?php echo $row["order_receiver_name"]; ?>");
            $('#order_receiver_address').val("<?php echo $row["order_receiver_address"]; ?>");
        });
        </script>
        <form method="post" id="invoice_form">
            <nav class="navbar navbar-default card">
                <div class="container-fluid">
                    <div class="navbar-header">
                        <a class="navbar-brand" href="invoice.php?add=1">Edit Invoice</a>
                    </div>
                </div>
            </nav>
            <div class="table-responsive card">
                <table class="table table-bordered table-hover table-striped">
                    <tr>
                        <td colspan="2">
                            <div class="row">
                                <div class="col-md-8">
                                    <b>RECEIVER (BILL TO) INFORMATION</b><br />
                                    <div class="form-control">
                                        <input type="text" name="order_receiver_name" id="order_receiver_name" class="form-control input-sm" placeholder="Enter Receiver Name" />
                                    </div>
                                    <div class="form-control">
                                        <textarea name="order_receiver_address" id="order_receiver_address" class="form-control" placeholder="Enter Billing Address"></textarea>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    INVOICE DETAILS<br />
                                    <div class="form-control">
                                        <input type="text" name="order_no" id="order_no" class="form-control input-sm" placeholder="Enter Invoice No." />
                                    </div>
                                    <div class="form-control">
                                        <input type="text" name="order_date" id="order_date" class="form-control input-sm" readonly placeholder="Select Invoice Date" />
                                    </div>
                                </div>
                            </div>
                            <br />
                            <table id="invoice-item-table" class="table table-bordered table-hover table-striped">
                                <tr>
                                    <th width="5%">S/N</th>
                                    <th width="20%">Type Of Summon</th>
                                    <th width="10%">Demerit Points</th>
                                    <th width="12.5%" rowspan="2">Total</th>
                                    <th width="3%" rowspan="2"></th>
                                </tr>
                                <tr>
                                    <th></th>
                                    <th></th>
                                    <th></th>
                                    <th></th>
                                    <th></th>
                                </tr>
                                <?php
                                $statement = $connect->prepare("
                                    SELECT * FROM inv_order_item 
                                    WHERE order_id = :order_id
                                ");
                                $statement->execute(
                                    array(
                                        ':order_id' => $_GET["id"]
                                    )
                                );
                                $item_result = $statement->fetchAll();
                                $m = 0;
                                foreach($item_result as $sub_row) {
                                    $m = $m + 1;
                                ?>
                                <tr>
                                    <td><span id="sr_no"><?php echo $m; ?></span></td>
                                    <td>
                                        <select name="item_name[]" id="item_name<?php echo $m; ?>" data-srno="<?php echo $m; ?>" class="form-control input-sm item_name">
                                            <option value="Parking Violation" <?php if($sub_row["item_name"] == "Parking Violation") echo 'selected'; ?>>Parking Violation</option>
                                            <option value="Campus Traffic Regulations" <?php if($sub_row["item_name"] == "Campus Traffic Regulations") echo 'selected'; ?>>Campus Traffic Regulations</option>
                                            <option value="Accident Cause" <?php if($sub_row["item_name"] == "Accident Cause") echo 'selected'; ?>>Accident Cause</option>
                                        </select>
                                    </td>
                                    <td><input type="text" name="order_item_quantity[]" id="order_item_quantity<?php echo $m; ?>" data-srno="<?php echo $m; ?>" class="form-control input-sm order_item_quantity" value="<?php echo $sub_row["order_item_quantity"]; ?>" readonly /></td>
                                    <td></td>
                                </tr>
                                <?php
                                }
                                ?>
                            </table>
                        </td>
                    </tr>
                    <tr>
                    </tr>
                    <tr>
                        <td colspan="2"></td>
                    </tr>
                    <tr>
                        <td colspan="2" align="center">
                            <input type="hidden" name="total_item" id="total_item" value="<?php echo $m; ?>" />
                            <input type="hidden" name="order_id" id="order_id" value="<?php echo $row["order_id"]; ?>" />
                            <input type="submit" name="update_invoice" id="create_invoice" class="btn btn-info" value="Edit" />
                        </td>
                    </tr>
                </table>
            </div>
        </form>
        <script>
        $(document).ready(function(){
            var count = "<?php echo $m; ?>";

            $(document).on('click', '#add_row', function(){
                count++;
                $('#total_item').val(count);
                var html_code = '';
                html_code += '<tr id="row_id_'+count+'">';
                html_code += '<td><span id="sr_no">'+count+'</span></td>';
                html_code += '<td><select name="item_name[]" id="item_name'+count+'" data-srno="'+count+'" class="form-control input-sm item_name"><option value="Parking Violation">Parking Violation</option><option value="Campus Traffic Regulations">Campus Traffic Regulations</option><option value="Accident Cause">Accident Cause</option></select></td>';
                html_code += '<td><input type="text" name="order_item_quantity[]" id="order_item_quantity'+count+'" data-srno="'+count+'" class="form-control input-sm order_item_quantity" readonly /></td>';
                html_code += '<td><button type="button" name="remove_row" id="'+count+'" class="btn btn-danger btn-xs remove_row">X</button></td>';
                html_code += '</tr>';
                $('#invoice-item-table').append(html_code);
            });

            $(document).on('change', '.item_name', function(){
                var srno = $(this).data('srno');
                var item_name = $(this).val();
                var demerit_points = 0;
                if(item_name == 'Parking Violation') {
                    demerit_points = 10;
                } else if(item_name == 'Campus Traffic Regulations') {
                    demerit_points = 15;
                } else if(item_name == 'Accident Cause') {
                    demerit_points = 20;
                }
                $('#order_item_quantity'+srno).val(demerit_points);
            });

            $('#create_invoice').click(function(){
                if($.trim($('#order_receiver_name').val()).length == 0)
                {
                    alert("Please Enter Receiver Name");
                    return false;
                }

                if($.trim($('#order_no').val()).length == 0)
                {
                    alert("Please Enter Invoice Number");
                    return false;
                }

                if($.trim($('#order_date').val()).length == 0)
                {
                    alert("Please Select Invoice Date");
                    return false;
                }

                for(var no=1; no<=count; no++)
                {
                    if($.trim($('#item_name'+no).val()).length == 0)
                    {
                        alert("Please Enter Item Name");
                        $('#item_name'+no).focus();
                        return false;
                    }

                    if($.trim($('#order_item_quantity'+no).val()).length == 0)
                    {
                        alert("Please Enter Demerit Points");
                        $('#order_item_quantity'+no).focus();
                        return false;
                    }

                }

                $('#invoice_form').submit();

            });

        });
        </script>
        <?php 
            }
        } else { ?>
        <nav class="navbar navbar-inverse navbar-fixed-top">
            <div class="container">
                <div class="navbar-header">
                    <a class="navbar-brand" href="#">Traffic Summon System</a>
                </div>
        </nav>
        <br /><br />
        <div align="right">
            <a href="invoice.php?add=1" class="btn btn-success">Create New</a>
        </div>
        <br />
        <table id="data-table" class="table table-bordered table-striped card table-hover">
            <thead>
                <tr>
                    <th>Invoice No.</th>
                    <th>Invoice Date</th>
                    <th>Receiver Name</th>
                    <th>Type of Summon</th>
                    <th>Demerit Points</th>
                    <th>Summon QR Code</th>
                    <th>Edit</th>
                    <th>Delete</th>
                </tr>
            </thead>
            <?php
            if ($total_rows > 0) {
                foreach($all_result as $row) {
                    // Fetch the first type of summon for the order
                    $statement = $connect->prepare("
                        SELECT item_name, order_item_quantity FROM inv_order_item
                        WHERE order_id = :order_id
                        LIMIT 1
                    ");
                    $statement->execute(
                        array(
                            ':order_id' => $row["order_id"]
                        )
                    );
                    $summon_item = $statement->fetch(PDO::FETCH_ASSOC);

                    if ($summon_item) {
                        echo '
                        <tr>
                            <td>'.$row["order_no"].'</td>
                            <td>'.$row["order_date"].'</td>
                            <td>'.$row["order_receiver_name"].'</td>
                            <td>'.$summon_item["item_name"].'</td>
                            <td>'.$summon_item["order_item_quantity"].'</td>
                            <td><a href="printInvoice.php?pdf=1&id='.$row["order_id"].'">QR</a></td>
                            <td><a href="invoice.php?update=1&id='.$row["order_id"].'"><span class="glyphicon glyphicon-edit"></span></a></td>
                            <td><a href="#" id="'.$row["order_id"].'" class="delete text-danger"><span class="glyphicon glyphicon-remove"></span></a></td>
                        </tr>
                        ';
                    } else {
                        echo '
                        <tr>
                            <td>'.$row["order_no"].'</td>
                            <td>'.$row["order_date"].'</td>
                            <td>'.$row["order_receiver_name"].'</td>
                            <td colspan="3">No items found</td>
                            <td><a href="invoice.php?update=1&id='.$row["order_id"].'"><span class="glyphicon glyphicon-edit"></span></a></td>
                            <td><a href="#" id="'.$row["order_id"].'" class="delete text-danger"><span class="glyphicon glyphicon-remove"></span></a></td>
                        </tr>
                        ';
                    }
                }
            }
            ?>
        </table>
        <?php } ?>
    </div>
    <br>
</body>
</html>

<?php 
} else {
    header("staff-add-summon.php");
} 
?>
<script type="text/javascript">
$(document).ready(function(){
    var table = $('#data-table').DataTable({
        "order":[],
        "columnDefs":[
        {
            "targets":[4, 5, 6],
            "orderable":false,
        },
        ],
        "pageLength": 5
    });
    $(document).on('click', '.delete', function(){
        var id = $(this).attr("id");
        if(confirm("Are you sure you want to remove this?"))
        {
            window.location.href="invoice.php?delete=1&id="+id;
        }
        else
        {
            return false;
        }
    });
});
</script>
<script>
$(document).ready(function(){
    $('.number_only').keypress(function(e){
        return isNumbers(e, this);      
    });
    function isNumbers(evt, element) 
    {
        var charCode = (evt.which) ? evt.which : event.keyCode;
        if (
            (charCode != 46 || $(element).val().indexOf('.') != -1) &&      // “.” CHECK DOT, AND ONLY ONE.
            (charCode < 48 || charCode > 57))
            return false;
        return true;
    }
});
</script>
<script>
$(document).ready(function(){
    $('#order_date').datepicker({
        format: "yyyy-mm-dd",
        autoclose: true
    });
});
</script>

<?php
// Include footer and scripts
include('includes/footer.php');
include('includes/scripts.php');
?>