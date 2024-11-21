<?php
include('../../connection.php');

if (isset($_GET['id'])) {
    $id = $_GET['id'];

    // Retrieve the phone number and other necessary details
    $sql = "
        SELECT a.contact_no, rr.fullname, res.name
        FROM resource_req rr
        JOIN accounts a ON rr.resident_id = a.account_id
        JOIN resources res ON rr.resource_id = res.resource_id
        WHERE rr.request_id = '$id'
    ";
    $result = mysqli_query($conn, $sql);

    if ($result && mysqli_num_rows($result) > 0) {
        $row = mysqli_fetch_assoc($result);
        $phone_number = $row['contact_no']; // Phone number from the accounts table
        $fullname = $row['fullname']; // Fullname for personalized message
        $resource_name = $row['name']; // Resource name
    } else {
        // Handle case if no record is found
        header('location: ../resources.php?alert=No record found&style=alert-warning');
        exit();
    }

    // Send SMS reminder using Semaphore API
    $ch = curl_init();
    $parameters = array(
        'apikey' => '485800cffee50c349559ffdde669afb1', // Replace with your actual API key
        'number' => $phone_number, // Use the phone number from the accounts table
        'message' => "Hello $fullname, please return the borrowed resources: $resource_name.", // Personalized message
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

    // Redirect to the resources page with a success message
    header("location: ../resources.php?alert=SMS reminder sent successfully!&style=alert-success");

} else {
    // Handle case where required parameters are not set in the GET request
    header('location: ../resources.php?alert=Invalid request&style=alert-warning');
}
?>
