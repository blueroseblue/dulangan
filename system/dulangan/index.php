<?php
session_start();
if (isset($_SESSION['id'])) {
    header('location: user_page/home.php');
}

include('connection.php');
if (isset($_POST['login'])) {
    $username = filter_input(INPUT_POST, 'username', FILTER_SANITIZE_SPECIAL_CHARS);
    $password = filter_input(INPUT_POST, 'password', FILTER_SANITIZE_SPECIAL_CHARS);

    $sql = "SELECT * FROM accounts WHERE username = '$username'";
    $result = mysqli_query($conn, $sql);
    $row = mysqli_num_rows($result);

    if ($row > 0) {
        $user = mysqli_fetch_assoc($result);

        if (password_verify($password, $user['password'])) {
            if ($user['is_accepted'] == 1) {
                // Account accepted, proceed to user home page
                $_SESSION['id'] = $user["account_id"];
                header('Location: user_page/home.php');
            } elseif ($user['is_accepted'] == 0) {
                // Account not verified
                $notVerifiedModal = true;
            } elseif ($user['is_accepted'] == 2) {
                // Account declined
                $declinedModal = true;
            }
        } else {
            // Wrong password
            $error = '<div class="form-text text-danger">
                        Wrong Password!
                      </div>';
        }
    } else {
        // Wrong username
        $error = "<div class='form-text text-danger'>
                    Wrong Username!
                  </div>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="source/style.css">
    <link rel="icon" href="image/logo.png">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <title>Login</title>
</head>

<body style = "background-color: #37AFE1">
    <div class="container">
        <div class="row justify-content-center align-items-center mt-5">
            <div class="col-12 col-lg-6 text-center">
                <div>
                    <img src="image/logo.png" alt="logo" class="img-fluid" style="max-width: 200%; height:auto; margin-top: 100px">
                </div>
            </div>
            <div class="col-12 col-lg-5" style="margin-top: 100px">
                <div class="bg-white shadow rounded py-5 px-4">
                    <form action="" method="POST">
                        <div class="form">
                            <h1 class="mb-3">Login to Account</h1>
                            <?php if (isset($error)) {
                                echo $error;
                            } ?>
                            <label for="username" class="form-label">Username:</label>
                            <input class="form-control" type="text" name="username" id="username" placeholder="Enter Username" required>
                            <label for="password" class="form-label">Password:</label>
                            <input class="form-control" type="password" name="password" id="password" placeholder="Enter Password" required>
                            <button type="submit" name="login" class="btn btn-primary w-100 mt-3">Log In</button>
                            <a href="register.php" class="btn btn-success w-100 mt-3">Create an Account</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <?php if (isset($notVerifiedModal)) : ?>
        <div id="notVerifiedModal" class="modal fade" tabindex="-1" aria-labelledby="notVerifiedModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-body text-center">
                        <h3 class="text-warning mb-3"><i class="fa-solid fa-circle-exclamation"></i> Account Not Verified</h3>
                        <p>Your account is pending verification. Please wait for the admin to verify your account.</p>
                        <button class="btn btn-warning" data-bs-dismiss="modal">Close</button>
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?>

    <?php if (isset($declinedModal)) : ?>
        <div id="declinedModal" class="modal fade" tabindex="-1" aria-labelledby="declinedModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-body text-center">
                        <h3 class="text-danger mb-3"><i class="fa-solid fa-ban"></i> Account Declined</h3>
                        <p>Unfortunately, your account has been declined by the admin. Please contact support for more information.</p>
                        <button class="btn btn-danger" data-bs-dismiss="modal">Close</button>
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Trigger the respective modals if set
            <?php if (isset($notVerifiedModal)) : ?>
                var notVerifiedModal = new bootstrap.Modal(document.getElementById('notVerifiedModal'));
                notVerifiedModal.show();
            <?php endif; ?>
            <?php if (isset($declinedModal)) : ?>
                var declinedModal = new bootstrap.Modal(document.getElementById('declinedModal'));
                declinedModal.show();
            <?php endif; ?>
        });
    </script>
</body>

</html>