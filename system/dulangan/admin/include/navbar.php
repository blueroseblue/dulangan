<?php   
if (isset($_POST['save'])) {
    $password = mysqli_real_escape_string($conn, $_POST['password']);
    $cpassword = mysqli_real_escape_string($conn, $_POST['cpassword']);

    if ($password == $cpassword) {
        $hash = password_hash($password, PASSWORD_DEFAULT);
        $sql = "UPDATE `admin` SET password = ?";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, 's', $hash);
        if(mysqli_stmt_execute($stmt)){
            // Logout the user after password change
            session_unset(); // Unset all session variables
            session_destroy(); // Destroy the session

            ?>
                <div id="success" class="modal" tabindex="-1">
                    <div class="modal-dialog modal-dialog-centered modal-sm">
                        <div class="modal-content">
                            <div class="modal-body text-center">
                                <h4 class="text-success mb-3"><i class="fa-solid fa-circle-check"></i> Change Password Successful <i class="fa-solid fa-circle-check"></i></h4>
                                <p>Admin Password has been changed. You will be logged out.</p>
                                <a href="index.php" class="btn btn-success">Okay</a>
                            </div>
                        </div>
                    </div>
                </div>
            <?php
        } else {
            ?>
                <div id="success" class="modal" tabindex="-1">
                    <div class="modal-dialog modal-dialog-centered modal-sm">
                        <div class="modal-content">
                            <div class="modal-body text-center">
                                <h4 class="text-danger mb-3"><i class="fa-solid fa-circle-check"></i> Change Password Unsuccessful <i class="fa-solid fa-circle-check"></i></h4>
                                <p>Something went wrong.</p>
                                <a href="index.php" class="btn btn-success">Okay</a>
                            </div>
                        </div>
                    </div>
                </div>
            <?php
        }
    } else {
        ?>
            <div id="success" class="modal" tabindex="-1">
                <div class="modal-dialog modal-dialog-centered modal-sm">
                    <div class="modal-content">
                        <div class="modal-body text-center">
                            <h4 class="text-danger mb-3"><i class="fa-solid fa-circle-check"></i> Password Error <i class="fa-solid fa-circle-check"></i></h4>
                            <p>Confirm Password Mismatch!</p>
                            <a href="index.php" class="btn btn-success">Okay</a>
                        </div>
                    </div>
                </div>
            </div>
        <?php
    }
}
?>

<nav class="navbar bg-darkblue navbar-expand-lg">
    <div class="container-fluid">
        <a class="navbar-brand text-white" href="#">Dulangan Brgy. System</a>
        <div class="d-flex">
            <div class="dropstart">
                <button class="btn text-white" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                    <i class="fa-solid fa-user"></i>
                </button>
                <ul class="dropdown-menu">
                    <li><a class="dropdown-item" href="functions/logout.php">Logout</a></li>
                    <li><button class="dropdown-item" data-bs-toggle="modal" data-bs-target="#changepassword">Change Password</button></li>
                </ul>
            </div>
            <button class="btn d-block d-md-none text-white" id="btn-open"><i class="fa-solid fa-bars"></i></button>
        </div>
    </div>
</nav>

<form action="" method="post">
    <div class="modal fade" id="changepassword" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-sm">
            <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title fs-5" id="exampleModalLabel">Change Password</h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="input_field">
                        <label class="form-label">Password :</label>
                        <input type="password" name="password" class="form-control" value="<?php if (isset($password)) {echo $password;} ?>" required>
                    </div>
                    <div class="input_field">
                        <label class="form-label">Confirm Password:</label>
                        <input type="password" name="cpassword" class="form-control" value="<?php if (isset($cpassword)) {echo $cpassword;} ?>" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" name="save" class="btn btn-primary">Save changes</button>
                </div>
            </div>
        </div>
    </div>
</form>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const successPopup = document.getElementById('success');
        if (successPopup) {
            const successModal = new bootstrap.Modal(successPopup);
            successModal.show();
        }
    });
</script>
