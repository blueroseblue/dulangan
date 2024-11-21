<?php
$sql = "SELECT * FROM admin WHERE username = '$username' AND password = '$hashed_password' LIMIT 1";
$result = mysqli_query($conn, $sql);

if (mysqli_num_rows($result) > 0) {
    $user = mysqli_fetch_assoc($result);
    
    // Check role and set appropriate session variable
    if ($user['role'] == 'admin') {
        $_SESSION['admin_id'] = $user['id'];  // Set session for admin
        header("Location: dashboard.php");  // Redirect to admin dashboard
    } elseif ($user['role'] == 'staff') {
        $_SESSION['staff_id'] = $user['id'];  // Set session for staff
        header("Location: dashboard_staff.php");  // Redirect to staff dashboard
    }
} else {
    echo "Invalid credentials.";
}
?>