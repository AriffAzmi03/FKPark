<?php
session_start();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Student Login</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        .center-container {
            height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            flex-direction: column;
            margin-top: -50px; /* Adjust as needed */
        }
        .login-container {
            background: #ffffff;
            padding: 40px;
            border-radius: 15px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            max-width: 500px;
            width: 100%;
            text-align: center;
            margin-top: 20px; /* Adjust as needed */
        }
        .login-title {
            font-family: 'Arial', sans-serif; 
            font-size: 30px; 
            font-weight: bold; 
            color: #333;
            margin-bottom: 20px;
            text-align: center;
        }
        .logo {
            max-width: 300px;
            margin-bottom: 20px;
            margin-top: -20px; /* Adjust as needed */
        }
        /* Set text alignment to left for labels */
        .login-container label {
            text-align: left;
            display: block;
            margin-bottom: 5px;
        }
        .error-message {
            color: red;
            margin-bottom: 15px;
        }
    </style>
</head>

<body class="bg-light">

<?php
include("includes/dbconnection.php");
$error = "";

if (isset($_POST['submit'])) {
    $email = $_POST['email'];
    $password = $_POST['password'];

    $query = "SELECT * FROM student WHERE studentEmail=? AND studentPassword=?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param('ss', $email, $password);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();

    if ($row) {
        $_SESSION['studentID'] = $row['studentID'];
        $_SESSION['studentName'] = $row['studentName']; // Store the student's name in the session
        $_SESSION['studentEmail'] = $row['studentEmail'];

        // Set a cookie that expires in 7 days
        setcookie("studentID", $row['studentID'], time() + (86400 * 7), "/");
        setcookie("studentName", $row['studentName'], time() + (86400 * 7), "/");

        header("Location: student-dashboard.php");
        exit();
    } else {
        $error = "Wrong Username or Password";
    }

    $stmt->close();
    $conn->close();
}
?>

<div class="center-container">
    <img src="images/FKPark.png" alt="FKPark Logo" class="logo"> <!-- Add your logo path here -->
    <div>
        <div class="login-container">
            <h1 class="login-title">STUDENT LOGIN</h1>
            <?php
            if ($error != "") {
                echo "<div class='error-message'>$error</div>";
            }
            ?>
            <form method="post" action="">
                <div class="form-group">
                    <label for="email">Email</label>
                    <input type="text" class="form-control" id="email" name="email" required>
                </div>
                <div class="form-group">
                    <label for="password">Password</label>
                    <input type="password" class="form-control" id="password" name="password" required>
                </div>
                <div class="form-group">
                    <button type="submit" name="submit" class="btn btn-primary btn-block">Login</button>
                </div>
                <div class="form-group text-center">
                    <a href="../admin/admin-login.php">Admin Login</a> | <a href="../ukstaff/ukstaff-login.php">Unit Keselamatan Staff Login</a>
                </div>
            </form>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

</body>
</html>


