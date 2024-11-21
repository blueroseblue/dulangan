<?php
session_start();
include('../../connection.php');

if (isset($_POST['id'])) {
    $id = $_POST['id'];
    $customMessage = isset($_POST['message']) ? $_POST['message'] : ''; // Get the custom message

    // Retrieve the phone number and fullname by joining resource_req and accounts table
    $sql = "
        SELECT a.contact_no, rr.fullname
        FROM resource_req rr
        JOIN accounts a ON rr.resident_id = a.account_id
        WHERE rr.request_id = '$id'
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

    // Update the resources request status in the database
    $sql_update = "UPDATE resource_req SET req_status = '2' WHERE request_id = '$id'";
    $result_update = mysqli_query($conn, $sql_update);

    if ($result_update) {
        $alert = 'Resource Request Rejected!';
        $alert_style = 'alert-success';

        // Send SMS notification using Semaphore API
        $ch = curl_init();
        $parameters = array(
            'apikey' => '485800cffee50c349559ffdde669afb1', // Replace with your actual API key
            'number' => $phone_number, // Use the phone number from the accounts table
            'message' => "Hello $fullname, your resource request has been rejected. " . $customMessage, // Personalized message
            'sendername' => 'DULANGAN' // Replace with your sender name if applicable
        );
        curl_setopt($ch, CURLOPT_URL, 'https://semaphore.co/api/v4/messages');
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($parameters));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $output = curl_exec($ch);
        curl_close($ch);
    } else {
        $alert = 'Something went wrong!';
        $alert_style = 'alert-warning';
    }

    // Redirect to the resource page with a message
    header("location: ../resources.php?alert=$alert&style=$alert_style");

} else {
    // Handle case where 'id' is not set in the POST request
    header('location: ../resources.php?alert=Invalid request&style=alert-warning');
}
?>
