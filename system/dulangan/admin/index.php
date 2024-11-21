<?php
session_start();
include('../connection.php');

// Redirect if already logged in
if (isset($_SESSION['admin_id'])) {
    header('Location: dashboard.php');  // Admin Dashboard
    exit();
} elseif (isset($_SESSION['admin_id'])) {
    header('Location: dashboard_staff.php');  // User Dashboard
    exit();
}

if (isset($_POST['login'])) {
    $username = filter_input(INPUT_POST, 'username', FILTER_SANITIZE_SPECIAL_CHARS);
    $password = filter_input(INPUT_POST, 'password', FILTER_SANITIZE_SPECIAL_CHARS);

    // Check if the user exists in the admin table
    $sql = "SELECT * FROM admin WHERE username = '$username'";
    $result = mysqli_query($conn, $sql);
    $row = mysqli_num_rows($result);

    if ($row > 0) {
        // User found in admin table
        $admin = mysqli_fetch_assoc($result);

        // Check password
        if (password_verify($password, $admin['password'])) {
            // Store the session variable to identify the user
            $_SESSION['admin_id'] = $admin['admin_id'];  // For Admin
            $_SESSION['admin_id'] = $admin['admin_id'];  // For Regular User (if user_id is admin_id)

            // Redirect based on the user's admin_id (use admin_id = 1 for main admin, or similar condition)
            if ($admin['admin_id'] == 1) {
                // Main admin
                header('Location: dashboard.php');  // Redirect to admin dashboard
            } else {
                // Regular user
                header('Location: dashboard_staff.php');  // Redirect to user dashboard
            }
            exit();
        } else {
            $error = "Incorrect username or password!";
        }
    } else {
        $error = "Incorrect username or password!";
    }

    mysqli_close($conn);
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
    <title>Login</title>
</head>
<body style="background-color:#37AFE1;">
    <div class="container">
        <div class="row justify-content-center align-items-center mt-5">
            <div class="col-12 col-lg-6 text-center">
                <div>
                    <img src="../image/logo.png" alt="logo" class="img-fluid" style="max-width: 200%; height:auto; margin-top: 100px">
                </div>
            </div>
            <div class="col-12 col-lg-5" style="margin-top: 100px">
                <div class="bg-white shadow rounded py-5 px-4">
                    <form action="" method="POST">
                        <div class="form">
                            <h1 class="mb-3">Login Admin</h1>
                            <?php if(isset($error)) { echo "<div class='form-text text-danger'>$error</div>"; } ?>
                            <label for="username" class="form-label">Username:</label>
                            <input class="form-control" type="text" name="username" id="username" placeholder="Enter Username" required>
                            <label for="password" class="form-label">Password:</label>
                            <input class="form-control" type="password" name="password" id="password" placeholder="Enter Password" required>
                            <button type="submit" name="login" class="btn btn-primary w-100 mt-3">Log In</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
</body>
</html>
