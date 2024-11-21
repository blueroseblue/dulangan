<?php
include('../../connection.php');

if (isset($_GET['admin_id']) && is_numeric($_GET['admin_id'])) {
    $admin_id = $_GET['admin_id'];

    // Use prepared statements to prevent SQL injection
    $sql = "DELETE FROM admin WHERE admin_id = ?";
    $stmt = mysqli_prepare($conn, $sql);

    // Bind the parameter to the prepared statement
    mysqli_stmt_bind_param($stmt, "i", $admin_id);  // "i" is for integer

    // Execute the query
    if (mysqli_stmt_execute($stmt)) {
        $alert = 'Delete Successful!';
        $alert_style = 'alert-success';
    } else {
        $alert = 'Delete Unsuccessful!';
        $alert_style = 'alert-danger';
    }

    // Close the prepared statement
    mysqli_stmt_close($stmt);
} else {
    $alert = 'Invalid User ID!';
    $alert_style = 'alert-danger';
}

// Redirect with alert messages
header("Location: ../users.php?alert=" . urlencode($alert) . "&style=" . urlencode($alert_style));
exit;  // Make sure to stop further code execution
?>
