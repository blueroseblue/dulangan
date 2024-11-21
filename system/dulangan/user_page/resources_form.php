<?php
session_start();
include('../connection.php');

if (!isset($_SESSION['id'])) {
    header('location: ../index.php');
}

$c = date('Y-m-d');
$profile_pic = '../uploads/profile/default_profile.png';

$active = "resources_form";

// Fetch the profile picture from the accounts table
$account_sql = "SELECT profile FROM accounts WHERE account_id = '" . $_SESSION["id"] . "'";
$account_result = mysqli_query($conn, $account_sql);
if ($account_result) {
    $account_row = mysqli_fetch_assoc($account_result);
    if ($account_row && isset($account_row['profile'])) {
        // Set the profile picture if it exists
        $profile_pic = '../uploads/profile/' . $account_row['profile'];
    }
}
$sql = "SELECT * FROM accounts WHERE account_id = '" . $_SESSION["id"] . "'";
$result = mysqli_query($conn, $sql);
$row = mysqli_fetch_assoc($result);

if (isset($_POST['submit'])) {
    $account_id = $_SESSION["id"];
    $resources = $_POST['resources'];

    if (is_array($resources)) {
        // Sanitize input fields outside of loop
        $fullname = mysqli_real_escape_string($conn, $_POST['fullname']);
        $age = mysqli_real_escape_string($conn, $_POST['age']);
        $purok = mysqli_real_escape_string($conn, $_POST['purok']);
        $purpose = mysqli_real_escape_string($conn, $_POST['purpose']);
        $return_date = mysqli_real_escape_string($conn, $_POST['return_date']);


        foreach ($resources as $resource) {
            $resource_id = $resource;
        
           
            $request_quantity = 1; 
            if (isset($_POST["quantity-$resource_id"])) {
                $request_quantity = intval($_POST["quantity-$resource_id"]);
            }
        
           
            if ($request_quantity <= 0) {
                $request_quantity = 1; 
            }
        
            $res_sql = "SELECT name FROM resources WHERE resource_id = '$resource_id'";
            $result = mysqli_query($conn, $res_sql);
            $res_row = mysqli_fetch_assoc($result);
            $resource_name = $res_row['name'];
        
           
            $sql = "INSERT INTO `resource_req`(`resident_id`, `resource_id`, `fullname`, `age`, `purok`, `resource`, `purpose`, `request_quantity`, `return_date`) 
                    VALUES (?,?,?,?,?,?,?,?,?)";
            $stmt = mysqli_prepare($conn, $sql);
        
            if ($stmt) {
                mysqli_stmt_bind_param($stmt, 'iisisssis', $account_id, $resource_id, $fullname, $age, $purok, $resource_name, $purpose, $request_quantity, $return_date);
                if (!mysqli_stmt_execute($stmt)) {
                    echo "Error executing statement for resource $resource_name: " . mysqli_stmt_error($stmt);
                }
            } else {
                echo "Error preparing statement: " . mysqli_error($conn);
            }
        }
        // Show success modal
        echo renderModal("Request Successful", "Your request has been sent. Please wait for the admin's approval. Thank you!", "home.php");
    } else {
        echo renderModal("Error", "No resources selected.", "home.php");
        exit;
    }
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


function renderModal($title, $message, $link)
{
    return "
    <div id='success' class='modal' tabindex='-1'>
        <div class='modal-dialog modal-dialog-centered'>
            <div class='modal-content'>
                <div class='modal-body text-center'>
                    <h3 class='text-success mb-3'><i class='fa-solid fa-circle-check'></i> $title <i class='fa-solid fa-circle-check'></i></h3>
                    <p>$message</p>
                    <a href='$link' class='btn btn-success'>Okay</a>
                </div>
            </div>
        </div>
    </div>
    ";
}
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
    <title>Brgy. Dulangan Request System</title>
</head>
<style>
    /* Sidebar styles */
    #side-nav {
        position: fixed;
        /* Keep sidebar fixed to the left */
        top: 0;
        left: 0;
        /* Sidebar visible from the start */
        width: 250px;
        /* Set width */
        height: 100%;
        /* Full height */
        background-color: #343a40;
        /* Dark background for the sidebar */
        transition: left 0.3s;
        /* Smooth transition */
        z-index: 1000;
        /* Ensure it stays on top */
    }

    /* Main content styles */
    .main-content {
        margin-left: 250px;
        /* Make space for the sidebar */
        padding: 20px;
        /* Add some padding for the content */
    }

    /* Custom styles for rounded table */
    .rounded-table {
        border-radius: 0.5rem;
        /* Set the radius for rounded corners */
        overflow: hidden;
        /* Hide overflow for the rounded corners */
    }

    .rounded-table th,
    .rounded-table td {
        border: none;
        /* Remove default borders */
    }

    .custom-navbar {
            background-color: #37AFE1;
        }

    .navbar-brand {
            margin-left: 251px; /* Adjust spacing from left */
        }
</style>

<script>
    if (ageValue < 14) {
        ageError.style.display = 'block'; // Show error message
        return false; // Prevent form submission
    } else {
        ageError.style.display = 'none'; // Hide error message
        return true; // Allow form submission
    }
</script>

<body style = "background-color: #F5EDED; height: 100vh;">
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
    <div class="container h-100">
        <div class="d-flex justify-content-center align-items-center my-5">
        <div class="p-3 p-lg-5 bg-white rounded shadow w-75" style="margin-left: 250px; margin-bottom: 50px;">
                <div class="text-center">
                    <img src="../image/logo.png" alt="logo" class="img-fluid w-25 mb-4">
                    <h2>Resources Request</h2>
                    <p>Please fill out the form below!</p>
                </div>
                <form action="" enctype="multipart/form-data" method="POST">
                    <div class="form">
                        <label for="fullname" class="form-label">Fullname <span class="text-danger">*</span>:</label>
                        <input class="form-control mb-2" type="text" name="fullname" id="fullname" value="<?= $row['fullname'] ?>" required readonly>
                        <div class="row">
                        <div class="col-12 col-lg-6">
                        <label for="age" class="form-label">Age <span class="text-danger">*</span>:</label>
                        <input class="form-control" type="number" name="age" id="age" min="14" required>
                        <div id="ageError" class="text-danger"></div>
                        </div>
                        <div class="col-12 col-lg-6">
                            <label for="purok" class="form-label">Purok <span class="text-danger">*</span>:</label>
                            <select class="form-control" type="text" name="purok" id="purok" required>
                            <option value="" selected disabled hidden>Select...</option>
                                <option value="1">1</option>
                                <option value="2">2</option>
                                <option value="3A">3A</option>
                                <option value="3B">3B</option>
                                <option value="4">4</option>
                                <option value="5">5</option>
                                <option value="6">6</option>
                                <option value="7">7</option>
                            </select>
                        </div>
                    </div>
                        <hr>
                        <h3>Resource<span class="text-danger">*</span>:</h3>
                        <div class="row px-0 px-lg-5">
                            <?php
                            $sql = "SELECT * FROM resources";
                            $result = mysqli_query($conn, $sql);
                            while ($row = mysqli_fetch_assoc($result)) {
                                $available = $row['quantity'] - $row['on_borrow'];
                            ?>
                                <div class="col-12 col-lg-4">
                                    <div class="form-check">
                                        <input class="form-check-input radio-outline resource-checkbox" type="checkbox" name="resources[]" id="resource-<?= $row['resource_id'] ?>" value="<?= $row['resource_id'] ?>" <?php if ($row['on_borrow'] == $row['quantity']) {
                                                                                                                                                                                                                            echo 'disabled';
                                                                                                                                                                                                                        } ?>>
                                        <label class="form-check-label p-abosolute" for="resource-<?= $row['resource_id'] ?>">
                                            <?= $row['name'] ?>(<?= $available ?>)
                                            <?php if ($row['on_borrow'] == $row['quantity']) {
                                                echo '<span class="text-danger badge rounded-pill">Unavailable</span>';
                                            } ?>
                                        </label>
                                        <?php if ($row['quantity'] >= 2): ?>
                                            <input type="number" class="form-control form-control-sm resource-quantity d-none" id="quantity-<?= $row['resource_id'] ?>" name="quantity-<?= $row['resource_id'] ?>" min="1" max="<?= $available ?>" placeholder="enter quantity">
                                        <?php endif; ?>
                                    </div>
                                </div>
                            <?php
                            }
                            ?>
                        </div>
                        <hr>
                        <h3>Purpose<span class="text-danger">*</span>:</h3>
                        <input class="form-control mb-2" type="text" name="purpose" id="purpose" placeholder="Enter purpose of borrowing" required>
                        <hr>
                        <h3>Return Date<span class="text-danger">*</span>:</h3>
                        <input class="form-control mb-2" type="date" name="return_date" id="return_date" min="<?= $c ?>" required>


                        <button type="submit" name="submit" class="btn btn-primary w-100 mt-3">Submit</button>
                    </div>
                </form>
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
    <script>
        //modal show
        document.addEventListener('DOMContentLoaded', function() {
            const successPopup = document.getElementById('success');
            if (successPopup) {
                const successModal = new bootstrap.Modal(successPopup);
                successModal.show();
            }

            const checkboxes = document.querySelectorAll('.resource-checkbox');

            checkboxes.forEach(function(checkbox) {
                // Get the resource id from the checkbox id
                const resourceId = checkbox.id.split('-')[1];
                const quantityInput = document.getElementById(`quantity-${resourceId}`);

                if (quantityInput) {
                    // Toggle the quantity input when the checkbox is checked or unchecked
                    checkbox.addEventListener('change', function() {
                        if (checkbox.checked) {
                            quantityInput.classList.remove('d-none');
                            quantityInput.setAttribute('required', 'required');
                        } else {
                            quantityInput.classList.add('d-none');
                            quantityInput.removeAttribute('required');
                        }
                    });

                    // Limit the quantity input between min and max values
                    quantityInput.addEventListener('input', function() {
                        const min = parseInt(this.min);
                        const max = parseInt(this.max);
                        const value = parseInt(this.value);

                        if (value < min) {
                            this.value = min;
                        } else if (value > max) {
                            this.value = max;
                        }
                    });
                }
            });
        });
    </script>
</body>

</html>