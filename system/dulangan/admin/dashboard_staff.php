<?php
session_start();

// Include the database connection file
include('../connection.php');
include('include/notif.php');

// Check if the user is logged in
if (!isset($_SESSION['admin_id'])) {
    header('Location: index.php');  // Redirect to login page if not logged in
    exit();
}

// Set the active section for sidebar highlighting
$active = "dashboard_staff"; 

// Get today's date for the clearance and resource requests
$c = date('Y-m-d'); // Get current date in 'YYYY-MM-DD' format

// Now you can safely run queries
$sql = "SELECT * FROM accounts";
$result = mysqli_query($conn, $sql);
if (!$result) {
    die("Query failed: " . mysqli_error($conn));
}
$row = mysqli_num_rows($result);

// Set the active section for sidebar highlighting
$active = "dashboard_staff"; 

// Fetch the name of the logged-in user from the database
$admin_id = $_SESSION['admin_id'];  // Assuming admin_id is stored in session
$sql_user = "SELECT name FROM admin WHERE admin_id = '$admin_id'"; // Correct column name is 'fullname'
$result_user = mysqli_query($conn, $sql_user);

if ($result_user && mysqli_num_rows($result_user) > 0) {
    $user_data = mysqli_fetch_assoc($result_user);
    $user_name = $user_data['name'];
} else {
    // Handle the case where the user is not found
    $user_name = "Brgy.Official";  // Default name in case of error
}

// Now you can safely run other queries
$sql = "SELECT * FROM admin";
$result = mysqli_query($conn, $sql);
if (!$result) {
    die("Query failed: " . mysqli_error($conn));
}
$row = mysqli_num_rows($result);

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../source/style.css">
    <link rel="icon" href="../image/logo.png">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <script src="https://kit.fontawesome.com/866d550866.js" crossorigin="anonymous"></script>
    <title>Staff</title>
</head>

<body style="height: 100vh; background-color: #F5EDED">
    <div class="main-container d-flex">
        <!-- Sidebar -->
        <?php include('include/sidebar_user.php') ?>

        <div class="content min-vh-100 w-100 overflow-x-auto">
            <?php include('include/navbar_user.php') ?>

            <div class="mainx p-3">
            <h1 class="fw-bolder">Welcome, <?= htmlspecialchars($user_name) ?>!</h1>

                <div class="row mb-5 gy-2">
                    <div class="col-12 col-lg-4">
                        <div class="bg-info bg-gradient text-dark rounded shadow p-3">
                            <p>Total Residents Registered:</p>
                            <?php
                            $sql = "SELECT * FROM accounts";
                            $result = mysqli_query($conn, $sql);
                            $row = mysqli_num_rows($result);
                            ?>
                            <h2><?= str_pad($row, 2, "0", STR_PAD_LEFT) ?></h2>
                        </div>
                    </div>
                    <div class="col-12 col-lg-4">
                        <div class="bg-danger bg-gradient rounded shadow p-3">
                            <p>Total Document Requests:</p>
                            <?php
                            $sql = "SELECT * FROM clearance_req";
                            $result = mysqli_query($conn, $sql);
                            $row = mysqli_num_rows($result);
                            ?>
                            <h2><?= str_pad($row, 2, "0", STR_PAD_LEFT) ?></h2>
                        </div>
                    </div>
                    <div class="col-12 col-lg-4">
                        <div class="bg-success bg-gradient rounded shadow p-3">
                            <p>Total Resource Requests:</p>
                            <?php
                            $sql = "SELECT * FROM resource_req";
                            $result = mysqli_query($conn, $sql);
                            $row = mysqli_num_rows($result);
                            ?>
                            <h2><?= str_pad($row, 2, "0", STR_PAD_LEFT) ?></h2>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-12 col-lg-6">
                        <h3>Clearance Requests Today</h3>
                        <div class="table-responsive">
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th style="width: 10px">#</th>
                                        <th>Fullname</th>
                                        <th>Request</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $sql = "SELECT * FROM clearance_req WHERE request_date = '$c'";
                                    $result = mysqli_query($conn, $sql);

                                    if (!mysqli_num_rows($result) > 0) {
                                        echo '<tr class="text-center"><td colspan="3">There are no requests today yet</td></tr>';
                                    } else {
                                        $count = 1;
                                        while ($row = mysqli_fetch_assoc($result)) {
                                    ?>
                                            <tr>
                                                <td class="text-center"><?= $count ?></td>
                                                <td><?= $row['fullname'] ?></td>
                                                <td><?= str_replace('_', ' ', $row['document']) ?></td>
                                            </tr>
                                    <?php
                                            $count++;
                                        }
                                    }
                                    ?>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <div class="col-12 col-lg-6">
                        <h3>Resource Requests Today</h3>
                        <div class="table-responsive">
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th style="width: 10px">#</th>
                                        <th>Fullname</th>
                                        <th>Request</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $sql = "SELECT * FROM resource_req WHERE request_date = '$c'";
                                    $result = mysqli_query($conn, $sql);

                                    if (!mysqli_num_rows($result) > 0) {
                                        echo '<tr class="text-center"><td colspan="3">There are no requests today yet</td></tr>';
                                    } else {
                                        $count = 1;
                                        while ($row = mysqli_fetch_assoc($result)) {
                                    ?>
                                            <tr>
                                                <td class="text-center"><?= $count ?></td>
                                                <td><?= $row['fullname'] ?></td>
                                                <td><?= str_replace('_', ' ', $row['resource']) ?></td>
                                            </tr>
                                    <?php
                                            $count++;
                                        }
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

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
</body>

</html>
