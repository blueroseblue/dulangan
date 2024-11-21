<?php
include("connection.php");

$errors = []; // Array to hold error messages
$success = false; // Flag for success

if (isset($_POST['register'])) {
    $fullname = mysqli_real_escape_string($conn, $_POST['fullname']);
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $contactnumber = mysqli_real_escape_string($conn, $_POST['contact_number']);
    $password = mysqli_real_escape_string($conn, $_POST['password']);
    $cpassword = mysqli_real_escape_string($conn, $_POST['cpassword']);

    // Check if passwords match
    if ($password !== $cpassword) {
        $errors['cpassword'] = 'Confirm Password does not match.';
    }

    // Check if contact number is exactly 11 digits and starts with "09"
    if (strlen($contactnumber) != 11 || !is_numeric($contactnumber)) {
        $errors['contact_number'] = 'Contact number must be exactly 11 digits.';
    } elseif (substr($contactnumber, 0, 2) !== '09') {
        $errors['contact_number'] = 'Contact number must start with "09".';
    }

    // Check if username already exists
    $sql = "SELECT username FROM accounts WHERE username = '$username'";
    $result = mysqli_query($conn, $sql);
    if (mysqli_fetch_row($result)) {
        $errors['username'] = 'Username already taken!';
    }

    // Handle valid ID upload
    $valid_id = null;
    if (isset($_FILES['valid_id']) && $_FILES['valid_id']['error'] == 0) {
        $uploadDir = 'uploads/profile/';
        $valid_id = basename($_FILES['valid_id']['name']);
        $valid_id_tmp = $_FILES['valid_id']['tmp_name'];
        $valid_id_path = $uploadDir . $valid_id;

        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }

        if (!move_uploaded_file($valid_id_tmp, $valid_id_path)) {
            $errors['valid_id'] = 'Error uploading the Valid ID.';
        }
    } else {
        $errors['valid_id'] = 'Valid ID is required.';
    }

    // Handle profile picture upload
    $profile_pic = 'uploads/profile/default_profile.png';
    if (isset($_FILES['profile']) && $_FILES['profile']['error'] == 0) {
        $profile_pic = basename($_FILES['profile']['name']);
        $profile_pic_tmp = $_FILES['profile']['tmp_name'];
        $profile_pic_path = $uploadDir . $profile_pic;

        if (!move_uploaded_file($profile_pic_tmp, $profile_pic_path)) {
            $errors['profile'] = 'Error uploading the Profile Picture.';
        }
    }

    // Insert data into the database if no errors occurred
    if (empty($errors)) {
        $hash = password_hash($password, PASSWORD_DEFAULT);
        $sql = "INSERT INTO accounts (fullname, username, contact_no, password, valid_id, profile) VALUES (?,?,?,?,?,?)";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "ssisss", $fullname, $username, $contactnumber, $hash, $valid_id, $profile_pic);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
        mysqli_close($conn);
        $success = true; // Set success flag
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="source/style1.css">
    <link rel="icon" href="image/logo.png">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://kit.fontawesome.com/866d550866.js" crossorigin="anonymous"></script>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.3/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

    <title> Register </title>
</head>

<body style="background-color: #37AFE1; margin-bottom: 50px;">
    <div class="container">
        <div class="row justify-content-center align-items-center mt-5">
            <div class="col-12 col-lg-5">
                <div class="bg-white shadow rounded py-5 px-4">
                    <form action="" method="POST" enctype="multipart/form-data">
                        <h1 class="m-0" style="text-align: center">Create an Account</h1>

                        <div class="text-center mb-3" style="margin-top: 5px;">
                            <img id="profilePreview"
                                src="uploads/profile/default_profile.png"
                                alt="Profile Picture"
                                class="img-fluid rounded-circle"
                                style="width: 120px; height: 120px; margin-top: 10px;">
                        </div>

                        <div class="input_field">
                            <label class="form-label">Full name :</label>
                            <input type="text" name="fullname" class="form-control" required>
                        </div>

                        <div class="input_field">
                            <label class="form-label">User name :</label>
                            <input type="text" name="username" class="form-control" required>
                            <?php if (isset($errors['username'])): ?>
                                <div class="form-text text-danger"><?= $errors['username']; ?></div>
                            <?php endif; ?>
                        </div>

                        <div class="input_field">
                            <label class="form-label">Contact No :</label>
                            <input type="number" name="contact_number" class="form-control" required>
                            <div class="form-text text-danger" id="contactError"></div>
                            <?php if (isset($errors['contact_number'])): ?>
                                <div class="form-text text-danger"><?= $errors['contact_number']; ?></div>
                            <?php endif; ?>
                        </div>

                        <div class="input_field">
                            <label class="form-label">Password :</label>
                            <input type="password" name="password" class="form-control" required>
                        </div>

                        <div class="input_field">
                            <label class="form-label">Confirm Password:</label>
                            <input type="password" name="cpassword" class="form-control" required>
                            <?php if (isset($errors['cpassword'])): ?>
                                <div class="form-text text-danger"><?= $errors['cpassword']; ?></div>
                            <?php endif; ?>
                        </div>

                        <div class="input_field">
                            <label class="form-label">Upload Valid ID (PDF, JPG, PNG):</label>
                            <input type="file" name="valid_id" class="form-control" accept=".jpg,.jpeg,.png,.pdf" required>
                            <?php if (isset($errors['valid_id'])): ?>
                                <div class="form-text text-danger"><?= $errors['valid_id']; ?></div>
                            <?php endif; ?>
                        </div>

                        <div class="input_field">
                            <label class="form-label">Upload Profile Picture (JPG, PNG):</label>
                            <input type="file" id="profilePicInput" name="profile" class="form-control" accept=".jpg,.jpeg,.png">
                            <?php if (isset($errors['profile'])): ?>
                                <div class="form-text text-danger"><?= $errors['profile']; ?></div>
                            <?php endif; ?>
                        </div>

                        <button class="btn btn-primary w-100 mt-3" type="submit" name="register">Register</button>

                        <div class="form-text text-center">Already have an account? <a href="index.php">Login</a></div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <?php if ($success): ?>
        <div id="success" class="modal fade show" tabindex="-1" role="dialog" aria-modal="true" style="display: block;">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content">
                    <div class="modal-body text-center">
                        <h3 class="text-success mb-3">
                            <i class="fa-solid fa-circle-check"></i> Registration Successful
                            <i class="fa-solid fa-circle-check"></i>
                        </h3>
                        <p>Please wait for admin confirmation to verify your account.</p>
                        <a href="index.php" class="btn btn-success">Okay</a>
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?>


    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
    <script>
        const profilePicInput = document.getElementById('profilePicInput');
        const profilePreview = document.getElementById('profilePreview');

        function validateContactNumber(input) {
            const contactNumber = input.value;
            const errorDiv = document.getElementById('contactError');
            if (contactNumber.length > 11) {
                errorDiv.textContent = 'Contact number cannot exceed 11 digits.';
                input.setCustomValidity('Invalid contact number');
            } else {
                errorDiv.textContent = '';
                input.setCustomValidity('');
            }
        }

        profilePicInput.addEventListener('change', function(event) {
            const file = event.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    profilePreview.src = e.target.result;
                };
                reader.readAsDataURL(file);
            }
        });

        document.addEventListener('DOMContentLoaded', function() {
            const successPopup = document.getElementById('success');
            if (successPopup) {
                const successModal = new bootstrap.Modal(successPopup);
                successModal.show();
            }
        });
    </script>
</body>

</html>