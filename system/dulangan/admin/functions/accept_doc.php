<?php
session_start();
include('../../connection.php');

if (isset($_GET['id'])) {
    $id = $_GET['id'];

    // Retrieve the phone number and fullname by joining clearance_req and accounts table
    $sql = "
        SELECT a.contact_no, cr.fullname
        FROM clearance_req cr
        JOIN accounts a ON cr.resident_id = a.account_id
        WHERE cr.request_id = '$id'
    ";
    $result = mysqli_query($conn, $sql);
    
    if ($result && mysqli_num_rows($result) > 0) {
        $row = mysqli_fetch_assoc($result);
        $phone_number = $row['contact_no']; // Phone number from the accounts table
        $fullname = $row['fullname']; // Fullname for personalized message
    } else {
        // Handle case if no record is found
        header('location: ../clearance.php?alert=No record found&style=alert-warning');
        exit();
    }

    // Update the clearance request status in the database
    $sql_update = "UPDATE clearance_req SET req_status = '1' WHERE request_id = '$id'";
    $result_update = mysqli_query($conn, $sql_update);

    if ($result_update) {
        $alert = 'Document Request Accepted!';
        $alert_style = 'alert-success';

        // Send SMS notification using Semaphore API
        $ch = curl_init();
        $parameters = array(
            'apikey' => '485800cffee50c349559ffdde669afb1', // Replace with your actual API key
            'number' => $phone_number, // Use the phone number from the accounts table
            'message' => "Hello, $fullname! Your document request has been accepted. Please wait for the admin to prepare your requested document.", // Personalized message
            'sendername' => 'DULANGAN' // Replace with your sender name if applicable
        );
        curl_setopt($ch, CURLOPT_URL, 'https://semaphore.co/api/v4/messages');
        curl_setopt($ch, CURLOPT_POST, 1);

        // Send the parameters set above with the request
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($parameters));

        // Receive response from server
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $output = curl_exec($ch);
        curl_close($ch);

        // Optionally, you can log or handle the response here
        // echo $output;

    } else {
        $alert = 'Something went wrong!';
        $alert_style = 'alert-warning';
    }

    // Redirect to the clearance page with a message
    header("location: ../clearance.php?alert=$alert&style=$alert_style");

} else {
    // Handle case where 'id' is not set in the GET request
    header('location: ../clearance.php?alert=Invalid request&style=alert-warning');
}
?>
