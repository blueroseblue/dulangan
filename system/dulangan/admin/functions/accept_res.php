<?php
include('../../connection.php');

if(isset($_GET['id']) && isset($_GET['res_id']) && isset($_GET['qnt'])) {
    $id = $_GET['id'];
    $res_id = $_GET['res_id'];
    $qnt = $_GET['qnt'];

    // Retrieve the phone number by joining resource_req and accounts table
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
        header('location: ../resources.php?alert=No record found&style=alert-warning');
        exit();
    }

    // Update the resource request status in the database
    $sql_update = "UPDATE resource_req SET req_status = '1', is_returned = '1' WHERE request_id = '$id'";
    $result_update = mysqli_query($conn, $sql_update);

    if($result_update) {
        $alert = 'Resource Request Accepted!';
        $alert_style = 'alert-success';

        // Update the resources on_borrow value
        $sql_borrow = "SELECT on_borrow FROM resources WHERE resource_id = $res_id";
        $result_borrow = mysqli_query($conn, $sql_borrow);
        $row_borrow = mysqli_fetch_assoc($result_borrow);

        $on_borrow = $row_borrow['on_borrow'] + $qnt;

        $sql_update_resource = "UPDATE resources SET is_borrowed = '1', on_borrow = '$on_borrow' WHERE resource_id = '$res_id'";
        mysqli_query($conn, $sql_update_resource);

        // Send SMS notification using Semaphore API
        $ch = curl_init();
        $parameters = array(
            'apikey' => '485800cffee50c349559ffdde669afb1', // Replace with your actual API key
            'number' => $phone_number, // Use the phone number from the accounts table
            'message' => "Hello, $fullname! Your resource request has been accepted. Please wait for the admin to prepare your requested resource.", // Personalized message
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

    // Redirect to the resources page with a message
    header("location: ../resources.php?alert=$alert&style=$alert_style");

} else {
    // Handle case where required parameters are not set in the GET request
    header('location: ../resources.php?alert=Invalid request&style=alert-warning');
}
?>
