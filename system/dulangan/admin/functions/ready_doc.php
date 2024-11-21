<?php
session_start();
include('../../connection.php');

if (isset($_POST['id'])) {
    $id = $_POST['id'];

    // Prepare the SQL statement to prevent SQL injection
    $stmt = $conn->prepare("
        SELECT a.contact_no, rd.fullname
        FROM clearance_req rd
        JOIN accounts a ON rd.resident_id = a.account_id
        WHERE rd.request_id = ?
    ");
    $stmt->bind_param('i', $id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result && $result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $phone_number = $row['contact_no'];
        $fullname = $row['fullname'];
    } else {
        echo json_encode(['success' => false, 'error' => 'No record found']);
        exit();
    }

    // Update the pick-up status in the database
    $sql_update = "UPDATE clearance_req SET pickup_status = '1' WHERE request_id = ?";
    $stmt_update = $conn->prepare($sql_update);
    $stmt_update->bind_param('i', $id);
    $result_update = $stmt_update->execute();

    if ($result_update) {
        $alert = 'Document Request Accepted!';
        $alert_style = 'alert-success';
        
        // Send SMS notification using Semaphore API
        $ch = curl_init();
        $parameters = array(
            'apikey' => '485800cffee50c349559ffdde669afb1', // Replace with your actual API key
            'number' => $phone_number,
            'message' => "Hello, $fullname! Your requested document is now ready for pick-up at the barangay.",
            'sendername' => 'DULANGAN'
        );
        curl_setopt($ch, CURLOPT_URL, 'https://semaphore.co/api/v4/messages');
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($parameters));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $output = curl_exec($ch);
        curl_close($ch);

        // Check for errors in sending the SMS
        if ($output === false) {
            echo json_encode(['success' => false, 'error' => 'Failed to send SMS notification.']);
            exit();
        }

        echo json_encode(['success' => true, 'message' => 'Document is ready for pick-up!']);
    } else {
        echo json_encode(['success' => false, 'error' => 'Database update failed.']);
    }

} else {
    echo json_encode(['success' => false, 'error' => 'Invalid request']);
}
?>
