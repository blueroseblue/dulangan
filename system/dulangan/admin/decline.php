<?php
// Start session and include database connection
session_start();
include('../connection.php');

// Check if the user is logged in and has an account ID
if (!isset($_SESSION['admin_id'])) {
    header('location: index.php');
    exit;
}

// Check if the account ID is provided via GET
if (isset($_GET['id'])) {
    $account_id = $_GET['id'];

    // Update the `is_accepted` field to 1
    $sql = "UPDATE accounts SET is_accepted = 2 WHERE account_id = ?";
    $stmt = mysqli_prepare($conn, $sql);

    if ($stmt) {
        mysqli_stmt_bind_param($stmt, 'i', $account_id);

        if (mysqli_stmt_execute($stmt)) {
            // Check how many rows were affected
            if (mysqli_stmt_affected_rows($stmt) > 0) {

                // Retrieve the user's phone number from the database
                $phoneQuery = "SELECT contact_no FROM accounts WHERE account_id = ?";
                $phoneStmt = mysqli_prepare($conn, $phoneQuery);
                mysqli_stmt_bind_param($phoneStmt, 'i', $account_id);

                if (mysqli_stmt_execute($phoneStmt)) {
                    mysqli_stmt_bind_result($phoneStmt, $phone);
                    mysqli_stmt_fetch($phoneStmt);
                    
                    // Send SMS using Semaphore API
                    $ch = curl_init();
                    $parameters = array(
                        'apikey' => '485800cffee50c349559ffdde669afb1', // Replace with your actual API KEY
                        'number' => $phone, // User's phone number
                        'message' => 'Your Account has Been Declined',
                        'sendername' => 'DULANGAN'
                    );
                    curl_setopt($ch, CURLOPT_URL, 'https://semaphore.co/api/v4/messages');
                    curl_setopt($ch, CURLOPT_POST, 1);
                    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($parameters));
                    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                    $output = curl_exec($ch);
                    curl_close($ch);

                    // Show the server response (optional)
                    echo $output;

                    // Redirect after SMS is sent
                    header('location: accounts.php?status=declined');
                    exit;

                } else {
                    echo "Error fetching phone number: " . mysqli_stmt_error($phoneStmt);
                }

                mysqli_stmt_close($phoneStmt);

            } else {
                echo "No rows were updated. Please check if the account ID exists and if `is_accepted` is already set to 2.";
            }
        } else {
            echo "Error executing query: " . mysqli_stmt_error($stmt);
        }

        mysqli_stmt_close($stmt);
    } else {
        echo "Error preparing query: " . mysqli_error($conn);
    }
} else {
    echo "No account ID provided.";
}

// Close the database connection
mysqli_close($conn);
?>
