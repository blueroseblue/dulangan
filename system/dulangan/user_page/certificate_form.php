<?php
session_start();
include('../connection.php');
if (!isset($_SESSION['id'])) {
    header('location: ../index.php');
}

$sql = "SELECT * FROM accounts WHERE account_id = '" . $_SESSION["id"] . "'";
$result = mysqli_query($conn, $sql);
$row = mysqli_fetch_assoc($result);

$profile_pic = '../uploads/profile/default_profile.png';

$active = "certificate_form";

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

if (isset($_POST['submit'])) {
    $account_id = $_SESSION["id"];
    $fullname = mysqli_real_escape_string($conn, $_POST['fullname']);
    $age = mysqli_real_escape_string($conn, $_POST['age']);
    $status = mysqli_real_escape_string($conn, $_POST['status']);
    $purok = mysqli_real_escape_string($conn, $_POST['purok']);
    echo $clearance = mysqli_real_escape_string($conn, $_POST['clearance']);
    $price = mysqli_real_escape_string($conn, $_POST['price']);
    $payment_type = mysqli_real_escape_string($conn, $_POST['payment_type']);
    $payment_receipt = "none";

    // Other clearance 
    if ($clearance == 'other') {
        $clearance = mysqli_real_escape_string($conn, $_POST['other_clearance']);
    } 

    // Handle Purpose
    if ($clearance == 'business_clearance') {
        $purpose = mysqli_real_escape_string($conn, $_POST['bp_purpose']);
    } else {
        $purpose = mysqli_real_escape_string($conn, $_POST['purpose']);
    }

    if ($purpose == 'others') {
        $purpose = mysqli_real_escape_string($conn, $_POST['other_purpose']);
    }
    echo $purpose;

    // Insert into database without valid_id
    $sql = "INSERT INTO `clearance_req`(`resident_id`, `fullname`, `age`, `status`, `purok`, `document`, `purpose`, `price`, `payment_type`, `payment_receipt`) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "isssssssss", $account_id, $fullname, $age, $status, $purok, $clearance, $purpose, $price, $payment_type, $payment_receipt);

    if (mysqli_stmt_execute($stmt)) {
        $latest_id = mysqli_insert_id($conn);

        $online_msg = "Please proceed to payment through GCash.";
        $walkin_msg = "Please wait for the admin to confirm your request. Thank you!";
        $online_link = "payment.php?req_id=$latest_id&price=$price";
        $walkin_link = "home.php";

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
        <div id="success" class="modal" tabindex="-1">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-body text-center">
                        <h3 class="text-success mb-3"><i class="fa-solid fa-circle-check"></i> Request Successful <i class="fa-solid fa-circle-check"></i></h3>
                        <p><?= $payment_type == 'online' ? $online_msg : $walkin_msg ?></p>
                        <a href="<?= $payment_type == 'online' ? $online_link : $walkin_link ?>" class="btn btn-success">Okay</a>
                    </div>
                </div>
            </div>
        </div>
    <?php
        mysqli_stmt_close($stmt);
        mysqli_close($conn);
    } else {
    ?>
        <div id="success" class="modal" tabindex="-1">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-body text-center">
                        <h3 class="text-danger mb-3"><i class="fa-solid fa-circle-check"></i> Request Unsuccessful <i class="fa-solid fa-circle-check"></i></h3>
                        <p>Something went wrong</p>
                        <a href="" class="btn btn-success">Okay</a>
                    </div>
                </div>
            </div>
        </div>
<?php
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
                <h2>Certificate Request</h2>
                <p>Please fill out the form below!</p>
            </div>
            <form action="" method="POST">
                <div class="form">
                    <label for="fullname" class="form-label">Fullname <span class="text-danger">*</span>:</label>
                    <input class="form-control mb-2" type="text" name="fullname" id="fullname" value="<?= $row['fullname'] ?>" required readonly>
                    <div class="row">
                        <div class="col-12 col-lg-4">
                            <label for="age" class="form-label">Age <span class="text-danger">*</span>:</label>
                            <input class="form-control" type="number" name="age" id="age" min="14" required>
                            <div id="ageError" class="text-danger"></div>
                        </div>
                        <div class="col-12 col-lg-4">
                            <label for="status" class="form-label">Status <span class="text-danger">*</span>:</label>
                            <select class="form-control" type="text" name="status" id="status" required>
                                <option value="" selected disabled hidden>Select...</option>
                                <option value="single">Single</option>
                                <option value="married">Married</option>
                                <option value="widowed">Widowed</option>
                            </select>
                        </div>
                        <div class="col-12 col-lg-4">
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
                    <h3>Certificate<span class="text-danger">*</span>:</h3>
                    <div class="row px-0 px-lg-5">
    <div class="col-12 col-lg-4">
        <div class="form-check">
            <input class="form-check-input radio-outline" type="radio" name="clearance" id="barangay_clearance" value="barangay_clearance" required>
            <label class="form-check-label" for="barangay_clearance">Barangay Clearance</label>
        </div>
    </div>
    <div class="col-12 col-lg-4">
        <div class="form-check">
            <input class="form-check-input radio-outline" type="radio" name="clearance" id="indigency" value="indigency">
            <label class="form-check-label" for="indigency">Indigency</label>
        </div>
    </div>
    <div class="col-12 col-lg-4">
        <div class="form-check">
            <input class="form-check-input radio-outline" type="radio" name="clearance" id="residency" value="residency">
            <label class="form-check-label" for="residency">Residency</label>
        </div>
    </div>
    <div class="col-12 col-lg-4">
        <div class="form-check">
            <input class="form-check-input radio-outline" type="radio" name="clearance" id="relationship" value="relationship">
            <label class="form-check-label" for="relationship">Relationship</label>
        </div>
    </div>
    <div class="col-12 col-lg-4">
        <div class="form-check">
            <input class="form-check-input radio-outline" type="radio" name="clearance" id="certification" value="certification">
            <label class="form-check-label" for="certification">Certification</label>
        </div>
    </div>
    <div class="col-12 col-lg-4">
        <div class="form-check">
            <input class="form-check-input radio-outline" type="radio" name="clearance" id="business_clearance" value="business_clearance">
            <label class="form-check-label" for="business_clearance">Business Clearance</label>
        </div>
    </div>
    <div class="col-12 col-lg-4">
    <div class="form-check">
        <input class="form-check-input radio-outline" type="radio" name="clearance" id="other" value="other">
        <label class="form-check-label" for="other">Others</label>
        <input class="form-control d-none" type="text" name="other_clearance" id="other_clearance" placeholder="Enter other clearance">
    </div>
</div>
</div>
<hr>
<h3>Purpose<span class="text-danger">*</span>:</h3>
<div class="row px-0 px-lg-5" id="purposerow">
    <div class="col-12 col-lg-4">
        <div class="form-check">
            <input class="form-check-input radio-outline" type="radio" name="purpose" id="financial" value="financial" required>
            <label class="form-check-label" for="financial">Financial</label>
        </div>
    </div>
    <div class="col-12 col-lg-4">
        <div class="form-check">
            <input class="form-check-input radio-outline" type="radio" name="purpose" id="employment" value="employment">
            <label class="form-check-label" for="employment">Employment</label>
        </div>
    </div>
    <div class="col-12 col-lg-4">
        <div class="form-check">
            <input class="form-check-input radio-outline" type="radio" name="purpose" id="loan" value="loan">
            <label class="form-check-label" for="loan">Loan</label>
        </div>
    </div>
    <div class="col-12 col-lg-4">
        <div class="form-check">
            <input class="form-check-input radio-outline" type="radio" name="purpose" id="burial" value="burial">
            <label class="form-check-label" for="burial">Burial</label>
        </div>
    </div>
    <div class="col-12 col-lg-4">
        <div class="form-check">
            <input class="form-check-input radio-outline" type="radio" name="purpose" id="medical" value="medical">
            <label class="form-check-label" for="medical">Medical</label>
        </div>
    </div>
    <div class="col-12 col-lg-4">
        <div class="form-check">
            <input class="form-check-input radio-outline" type="radio" name="purpose" id="scholarship" value="scholarship">
            <label class="form-check-label" for="scholarship">Scholarship</label>
        </div>
    </div>
    <div class="col-12 col-lg-4">
        <div class="form-check">
            <input class="form-check-input radio-outline" type="radio" name="purpose" id="legal" value="legal">
            <label class="form-check-label" for="legal">Legal</label>
        </div>
    </div>
    <div class="col-12 col-lg-4">
        <div class="form-check">
            <input class="form-check-input radio-outline" type="radio" name="purpose" id="educational_assistance" value="educational_assistance">
            <label class="form-check-label" for="educational_assistance">Educational Assistance</label>
        </div>
    </div>
    <div class="col-12 col-lg-4">
        <div class="form-check">
            <input class="form-check-input radio-outline" type="radio" name="purpose" id="comelec" value="comelec">
            <label class="form-check-label" for="comelec">Comelec</label>
        </div>
    </div>
    <div class="col-12 col-lg-4">
        <div class="form-check">
            <input class="form-check-input radio-outline" type="radio" name="purpose" id="pantawid_program" value="pantawid_program">
            <label class="form-check-label" for="pantawid_program">Pantawid Program</label>
        </div>
    </div>
    <div class="col-12 col-lg-4">
        <div class="form-check">
            <input class="form-check-input radio-outline" type="radio" name="purpose" id="pwd" value="pwd">
            <label class="form-check-label" for="pwd">PWD</label>
        </div>
    </div>
    <div class="col-12 col-lg-4">
        <div class="form-check">
            <input class="form-check-input radio-outline" type="radio" name="purpose" id="solo_parent" value="solo_parent">
            <label class="form-check-label" for="solo_parent">Solo Parent</label>
        </div>
    </div>
    <div class="col-12 col-lg-4">
        <div class="form-check ">
            <input class="form-check-input radio-outline" type="radio" name="purpose" id="first_time_job_seeker" value="first_time_job_seeker">
            <label class="form-check-label" for="first_time_job_seeker">First Time Job Seeker</label>
        </div>
    </div>
    <div class="col-12 col-lg-4">
        <div class="form-check">
            <input class="form-check-input radio-outline" type="radio" name="purpose" id="others" value="others">
            <label class="form-check-label" for="others">Others</label>
            <input class="form-control d-none" type="text" name="other_purpose" id="other_purpose" placeholder="Enter other purpose">
        </div>
    </div>
</div>
<div id="business_clearance_purpose" class="d-none">
    <input class="form-control" type="text" name="bp_purpose" id="bp_purpose" placeholder="Enter purpose for business permit">
</div>

<hr>
<h3>Payment<span class="text-danger">*</span>:</h3>
<div class="row">
    <div class="col-12 col-lg-6">
        <label for="price" class="form-label">Price <span class="text-danger">*</span>:</label>
        <input class="form-control" type="number" name="price" id="price" value="80" readonly>
    </div>
    <div class="col-12 col-lg-6">
        <label for="status" class="form-label">Payment Type <span class="text-danger">*</span>:</label>
        <select class="form-control" name="payment_type" id="status" required>
            <option value="" selected disabled hidden>Select...</option>
            <option id="online" value="online">Gcash</option>
            <option id="walkin" value="walkin">Walk-in</option>
        </select>
    </div>
</div>
<button type="submit" name="submit" class="btn btn-primary w-100 mt-3">Submit</button>
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

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>

    <script>
    const option_online = document.getElementById('online');
    const option_walkin = document.getElementById('walkin');
    const others_radio = document.getElementById('others');
    const other_radio = document.getElementById('other');
    const other_clearance_input = document.getElementById('other_clearance');
    const other_purpose = document.getElementById('other_purpose');
    const bp_radio = document.getElementById('business_clearance');
    const bp_purpose = document.getElementById('business_clearance_purpose');
    const req_radio = document.getElementById('financial');
    const purpose_row = document.getElementById('purposerow');
    const radios = document.querySelectorAll('input[name="clearance"]');
    const purpose_radios = document.querySelectorAll('input[name="purpose"]');
    const price = document.getElementById('price');

    // All free purposes
    const scholarship_radio = document.getElementById('scholarship');
    const medical_radio = document.getElementById('medical');
    const burial_radio = document.getElementById('burial');
    const comelec_radio = document.getElementById('comelec');
    const pantawid_program_radio = document.getElementById('pantawid_program');
    const pwd = document.getElementById('pwd');
    const solo_parent_radio = document.getElementById('solo_parent');
    const educational_assistance_radio = document.getElementById('educational_assistance');
    const first_time_job_seeker = document.getElementById('first time job seeker'); 

    // Handle clearance radio changes
    radios.forEach(radio => {
        radio.addEventListener('change', () => {
            if (radio.id === 'other') {
                // Show the text box if "Others" is selected
                other_clearance_input.classList.remove('d-none');
                other_clearance_input.setAttribute('required', 'required');
            } else {
                // Hide the text box for all other selections
                other_clearance_input.classList.add('d-none');
                other_clearance_input.removeAttribute('required');
            }

            if (bp_radio.checked) {
                bp_purpose.classList.remove('d-none');
                bp_purpose.setAttribute('required', 'required');
                purpose_row.classList.add('d-none');
                req_radio.removeAttribute('required');
                price.value = 130;
            } else {
                bp_purpose.classList.add('d-none');
                bp_purpose.removeAttribute('required');
                purpose_row.classList.remove('d-none');
                req_radio.setAttribute('required', 'required');
                price.value = 80;
            }
        });
    });

    // Handle purpose radio changes
    purpose_radios.forEach(pur_radio => {
        pur_radio.addEventListener('change', () => {
            if (!others_radio.checked) {
                other_purpose.classList.add('d-none');
                other_purpose.removeAttribute('required');
            } else if (others_radio.checked) {
                other_purpose.classList.remove('d-none');
                other_purpose.setAttribute('required', 'required');
            }

            if (scholarship_radio.checked || medical_radio.checked || burial_radio.checked || comelec_radio.checked || pantawid_program_radio.checked || pwd.checked || solo_parent_radio.checked || first_time_job_seeker.checked || educational_assistance.checked) {
                price.value = 0;
                option_online.setAttribute('disabled', '');
                option_online.setAttribute('hidden', '');
                option_walkin.setAttribute('selected', '');
            } else {
                price.value = 80;
                option_online.removeAttribute('disabled');
                option_online.removeAttribute('hidden');
                option_walkin.removeAttribute('selected');
            }
        });
    });

    // Modal show
    document.addEventListener('DOMContentLoaded', function() {
        const successPopup = document.getElementById('success');
        if (successPopup) {
            const successModal = new bootstrap.Modal(successPopup);
            successModal.show();
        }
    });
</script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
