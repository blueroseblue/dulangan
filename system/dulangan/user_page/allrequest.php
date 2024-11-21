<?php
session_start();
include('../connection.php');

if (!isset($_SESSION['id'])) {
    header('location: ../index.php');
    exit();
}

$active = "allrequest";

// Fetch user details
$sql = "SELECT * FROM residents WHERE resident_id = ?";
$stmt = mysqli_prepare($conn, $sql);
if (!$stmt) {
    die('Prepare failed: ' . mysqli_error($conn));
}
mysqli_stmt_bind_param($stmt, "s", $_SESSION["id"]);
if (!mysqli_stmt_execute($stmt)) {
    die('Database error: ' . mysqli_stmt_error($stmt));
}
$result = mysqli_stmt_get_result($stmt);
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

// Initialize search term
$searchTerm = '';

// Check if search is performed
if (isset($_POST['search'])) {
    $searchTerm = mysqli_real_escape_string($conn, $_POST['search_term']);
}

// Fetch requests from clearance_req table
$clearance_sql = "SELECT 'Clearance' AS request_type, document AS requested_item, purpose, request_date, req_status, pickup_status
                  FROM clearance_req 
                  WHERE resident_id = ? AND (document LIKE ? OR purpose LIKE ?)
                  ORDER BY request_date DESC"; // Order by request_date descending
$clearance_stmt = mysqli_prepare($conn, $clearance_sql);
if (!$clearance_stmt) {
    die('Prepare failed: ' . mysqli_error($conn));
}
$searchTermWildcard = '%' . $searchTerm . '%';
mysqli_stmt_bind_param($clearance_stmt, "sss", $_SESSION["id"], $searchTermWildcard, $searchTermWildcard);
if (!mysqli_stmt_execute($clearance_stmt)) {
    die('Database error: ' . mysqli_stmt_error($clearance_stmt));
}
$clearance_result = mysqli_stmt_get_result($clearance_stmt);

// Fetch requests from resource_req table
$resource_sql = "SELECT 'Resource' AS request_type, resource AS requested_item, purpose, request_date, req_status, pickup_status 
                 FROM resource_req 
                 WHERE resident_id = ? AND (resource LIKE ? OR purpose LIKE ?)
                 ORDER BY request_date DESC"; // Order by request_date descending
                 
$resource_stmt = mysqli_prepare($conn, $resource_sql);
if (!$resource_stmt) {
    die('Prepare failed: ' . mysqli_error($conn));
}
mysqli_stmt_bind_param($resource_stmt, "sss", $_SESSION["id"], $searchTermWildcard, $searchTermWildcard);
if (!mysqli_stmt_execute($resource_stmt)) {
    die('Database error: ' . mysqli_stmt_error($resource_stmt));
}
$resource_result = mysqli_stmt_get_result($resource_stmt);

// Combine the results
$requests = [];
while ($row = mysqli_fetch_assoc($clearance_result)) {
    $requests[] = $row; // Add clearance requests
}
while ($row = mysqli_fetch_assoc($resource_result)) {
    $requests[] = $row; // Add resource requests
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

// Pagination logic
$limit = 10;
$total_items = count($requests);
$total_pages = ceil($total_items / $limit);
$current_page = isset($_GET['page']) ? intval($_GET['page']) : 1;
$current_page = max(1, min($current_page, $total_pages));
$offset = ($current_page - 1) * $limit;
$requests = array_slice($requests, $offset, $limit); // Get the items for the current page
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../source/style.css">
    <link rel="icon" href="../image/logo.png">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://kit.fontawesome.com/866d550866.js" crossorigin="anonymous"></script>
    <title>Brgy. Dulangan Request System</title>
    <style>
        /* Sidebar styles */
        #side-nav {
            position: fixed;
            top: 0;
            left: 0;
            width: 250px;
            height: 100%;
            background-color: #343a40;
            transition: left 0.3s;
            z-index: 1000;
        }
        /* Main content styles */
        .main-content {
            margin-left: 250px;
            padding: 20px;
        }
        /* Custom styles for rounded table */
        .rounded-table {
            border-radius: 0.5rem;
            overflow: hidden;
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
        <h2 class="text-black">User Requests</h2>

        <form method="POST" class="mb-3">
            <div class="input-group">
                <input type="text" name="search_term" class="form-control" placeholder="Search requests..." value="<?php echo htmlspecialchars($searchTerm); ?>" style="max-width: 1000px;">
                <button type="submit" name="search" class="btn btn-primary" style="margin-left:5px">Search</button>
            </div>
        </form>

        <table class="table table-bordered rounded-table">
            <thead>
                <tr>
                    <th>Type of Request</th>
                    <th>Requested Item</th>
                    <th>Purpose</th>
                    <th>Request Date</th>
                    <th>Status</th>
                    <th>Pick-up Status</th>
                </tr>
            </thead>
            <tbody>
    <?php if (empty($requests)): ?>
        <tr>
            <td colspan="6" class="text-center">No requests found.</td>
        </tr>
    <?php else: ?>
        <?php foreach ($requests as $request): ?>
            <tr>
                <td><?php echo htmlspecialchars($request['request_type']); ?></td>
                <td><?php echo htmlspecialchars($request['requested_item']); ?></td>
                <td><?php echo htmlspecialchars($request['purpose']); ?></td>
                <td><?php echo htmlspecialchars(date('Y-m-d', strtotime($request['request_date']))); ?></td>
                <td>
                    <?php 
                        switch ($request['req_status']) {
                            case 0:
                                echo '<span class="badge bg-warning">Pending</span>';
                                break;
                            case 1:
                                echo '<span class="badge bg-success">Accepted</span>';
                                break;
                            case 2:
                                echo '<span class="badge bg-danger">Declined</span>';
                                break;
                            default:
                                echo '<span class="badge bg-secondary">Unknown</span>';
                        }
                    ?>
                </td>
                <td>
                <?php 
                switch ($request['pickup_status']) {
                case 0: 
                echo '<span class="badge bg-warning">Not Ready</span>';
                break;
                 case 1:
                echo '<span class="badge bg-success">Ready</span>';
                break;
                        }
                ?>
                </td>
            </tr>
        <?php endforeach; ?>
    <?php endif; ?>
                    </tbody>
        </table>

        <!-- Pagination -->
        <?php if ($total_pages > 1): ?>
            <nav aria-label="Page navigation example">
                <ul class="pagination">
                    <li class="page-item <?php if ($current_page <= 1) echo 'disabled'; ?>">
                        <a class="page-link" href="allrequest.php?page=<?php echo $current_page - 1; ?>" aria-label="Previous">
                            <span aria-hidden="true">&laquo;</span>
                        </a>
                    </li>
                    <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                        <li class="page-item <?php if ($i == $current_page) echo 'active'; ?>">
                            <a class="page-link" href="allrequest.php?page=<?php echo $i; ?>"><?php echo $i; ?></a>
                        </li>
                    <?php endfor; ?>
                    <li class="page-item <?php if ($current_page >= $total_pages) echo 'disabled'; ?>">
                        <a class="page-link" href="allrequest.php?page=<?php echo $current_page + 1; ?>" aria-label="Next">
                            <span aria-hidden="true">&raquo;</span>
                        </a>
                    </li>
                </ul>
            </nav>
        <?php endif; ?>
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
