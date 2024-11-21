<?php
session_start();
include('../connection.php');

if (!isset($_SESSION['id'])) {
    header('location: ../index.php');
}

$active = "home";

// Fetch user details
$sql = "SELECT * FROM residents WHERE resident_id = '" . $_SESSION["id"] . "'";
$result = mysqli_query($conn, $sql);
$row = mysqli_fetch_assoc($result);
$profile_pic = '../uploads/profile/default_profile.png'; 

// Fetch the profile picture from the accounts table
$account_sql = "SELECT profile FROM accounts WHERE account_id = '" . $_SESSION["id"] . "'";
$account_result = mysqli_query($conn, $account_sql);
if ($account_result) {
    $account_row = mysqli_fetch_assoc($account_result);
    if ($account_row && isset($account_row['profile'])) {
        // Set the profile picture if it exists
        $profile_pic = '../uploads/profile/' . $account_row['profile'];
    }
} else {
    // Handle the error when the account query fails
    error_log("Account query failed: " . mysqli_error($conn));
}
// Fetch totals
$total_requested_resources_sql = "SELECT COUNT(*) AS total_requested_resources FROM resource_req WHERE resident_id = '" . $_SESSION["id"] . "'";
$total_requested_certificates_sql = "SELECT COUNT(*) AS total_requested_certificates FROM clearance_req WHERE resident_id = '" . $_SESSION["id"] . "'";
$total_accepted_resources_sql = "SELECT COUNT(*) AS total_accepted_resources FROM resource_req WHERE resident_id = '" . $_SESSION["id"] . "' AND req_status = 1";
$total_accepted_certificates_sql = "SELECT COUNT(*) AS total_accepted_certificates FROM clearance_req WHERE resident_id = '" . $_SESSION["id"] . "' AND req_status = 1";

// Execute the queries
$total_requested_resources_result = mysqli_query($conn, $total_requested_resources_sql);
$total_requested_certificates_result = mysqli_query($conn, $total_requested_certificates_sql);
$total_accepted_resources_result = mysqli_query($conn, $total_accepted_resources_sql);
$total_accepted_certificates_result = mysqli_query($conn, $total_accepted_certificates_sql);

// Fetch the counts
$total_requested_resources = mysqli_fetch_assoc($total_requested_resources_result)['total_requested_resources'];
$total_requested_certificates = mysqli_fetch_assoc($total_requested_certificates_result)['total_requested_certificates'];
$total_accepted_resources = mysqli_fetch_assoc($total_accepted_resources_result)['total_accepted_resources'];
$total_accepted_certificates = mysqli_fetch_assoc($total_accepted_certificates_result)['total_accepted_certificates'];

// Function to format numbers to two digits
function formatToTwoDigits($number) {
    return str_pad($number, 2, '0', STR_PAD_LEFT);
}

// Password Change Logic
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['change_password'])) {
    $old_password = mysqli_real_escape_string($conn, $_POST['old_password']);
    $new_password = mysqli_real_escape_string($conn, $_POST['new_password']);
    $confirm_password = mysqli_real_escape_string($conn, $_POST['confirm_password']);

    // Fetch the current password from the database
    $sql = "SELECT password FROM accounts WHERE account_id = '" . $_SESSION["id"] . "'";
    $result = mysqli_query($conn, $sql);
    $row = mysqli_fetch_assoc($result);

    if (password_verify($old_password, $row['password'])) {
        // Old password matches, check if new passwords match
        if ($new_password == $confirm_password) {
            // Hash the new password
            $hashed_password = password_hash($new_password, PASSWORD_BCRYPT);

            // Update the password in the database
            $update_sql = "UPDATE accounts SET password = '$hashed_password' WHERE account_id = '" . $_SESSION["id"] . "'";
            if (mysqli_query($conn, $update_sql)) {
                // Log out the user after changing the password
                session_destroy();
                echo "<script>alert('Password changed successfully. You have been logged out.'); window.location.href = '../index.php';</script>";
                exit();
            } else {
                echo "<script>alert('Error updating password. Please try again.');</script>";
            }
        } else {
            echo "<script>alert('New passwords do not match.');</script>";
        }
    } else {
        echo "<script>alert('Incorrect old password.');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../source/style.css">
    <link rel="icon" href="../image/logo.png">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <script src="https://kit.fontawesome.com/866d550866.js" crossorigin="anonymous"></script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <title>Brgy. Dulangan Request System</title>
    <style>
       
        #side-nav {
            position: fixed;
            top: 0;
            left: 0; 
            width: 250px; 
            height: 100%; 
            background-color: #343a40;
            transition: left 0.3s; /* Smooth transition */
            z-index: 1000; /* Ensure it stays on top */
        }

        /* Main content styles */
        .main-content {
            margin-left: 250px; /* Make space for the sidebar */
            padding: 20px; /* Add some padding for the content */
        }

        /* Card styles */
        .card {
            height: 150px; /* Set a height for the cards */
            margin-bottom: 20px; /* Space between cards */
        }

        .card-title {
            font-size: 1.5rem; /* Increase font size of titles */
        }

        .card-text {
            font-size: 3rem; /* Increase font size of numbers */
        }

        .bg-requested-resources {
            background-color: #28a745; /* Green for resources */
            color: white;
        }

        .bg-requested-certificates {
            background-color: #007bff; /* Blue for certificates */
            color: white;
        }

        .bg-accepted-resources {
            background-color: #ffc107; 
            color: black;
        }

        .bg-accepted-certificates {
            background-color: #dc3545;
            color: white;
        }
        .container {
    display: flex;
    align-items: center; 
    justify-content: center;
}

.text {
    margin-left: 20px; 
    margin-right: 20px;
}

.custom-navbar {
    background-color: #37AFE1;
}

.navbar-brand {
            margin-left: 251px; /* Adjust spacing from left */
        }
    </style>

</head>

<body style="height: 100vh; background-color: #F5EDED;">
<nav class="navbar custom-navbar">
        <div class="container-fluid">
            <a class="navbar-brand text-white" href="#">Barangay Dulangan System</a>
            <div class="d-flex">
                <div class="dropstart">
                    <button class="btn text-white" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                        <img src="<?php echo $profile_pic; ?>" class="rounded-circle" style="width: 30px; height: 30px;">
                    </button>
                    <ul class="dropdown-menu">
                        <li><a class="dropdown-item" href="logout.php">Logout</a></li>
                        <li><a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#changePasswordModal">Change Password</a></li>
                    </ul>
                </div>
            </div>
        </div>
    </nav>

    <?php include('sidebar.php'); ?>

    <div class="main-content">
   <center><div class="container" style = "margin-top:20px">
    <img src="../image/logo.png" style="width:150px;" alt="">
    <div class="text">
        <h2 class="text-black">WELCOME TO BRGY. DULANGAN SYSTEM</h2>
        <h2 class="text-black">DASHBOARD</h2>
    </div>
    <img src="../image/dulangan.png" style="width:150px;" alt="">
</div>
</center>
        <br>
        <br>
        <div class="row">
            <div class="col-md-3">
                <div class="card bg-requested-resources">
                    <div class="card-body">
                        <h5 class="card-title">Total Requested Resources</h5>
                        <p class="card-text"><?php echo htmlspecialchars(formatToTwoDigits($total_requested_resources)); ?></p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-requested-certificates">
                    <div class="card-body">
                        <h5 class="card-title">Total Requested Certificates</h5>
                        <p class="card-text"><?php echo htmlspecialchars(formatToTwoDigits($total_requested_certificates)); ?></p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-accepted-resources">
                    <div class="card-body">
                        <h5 class="card-title">Total Accepted Resources</h5>
                        <p class="card-text"><?php echo htmlspecialchars(formatToTwoDigits($total_accepted_resources)); ?></p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-accepted-certificates">
                    <div class="card-body">
                        <h5 class="card-title">Total Accepted Certificates</h5>
                        <p class="card-text"><?php echo htmlspecialchars(formatToTwoDigits($total_accepted_certificates)); ?></p>
                    </div>
                </div>
            </div>
        </div>
    </div>

     <!-- Modal for Changing Password -->
     <div class="modal fade" id="changePasswordModal" tabindex="-1" aria-labelledby="changePasswordModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <form method="POST" action="">
                        <div class="modal-header">
                            <h5 class="modal-title" id="changePasswordModalLabel">Change Password</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <div class="mb-3">
                                <label for="oldPassword" class="form-label">Old Password</label>
                                <input type="password" class="form-control" id="oldPassword" name="old_password" required>
                            </div>
                            <div class="mb-3">
                                <label for="newPassword" class="form-label">New Password</label>
                                <input type="password" class="form-control" id="newPassword" name="new_password" required>
                            </div>
                            <div class="mb-3">
                                <label for="confirmPassword" class="form-label">Confirm New Password</label>
                                <input type="password" class="form-control" id="confirmPassword" name="confirm_password" required>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                            <button type="submit" name="change_password" class="btn btn-primary">Save changes</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
</body>

</html>




