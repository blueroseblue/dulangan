<?php
session_start();
include('../../connection.php');

if (isset($_POST['id'])) {
    $id = $_POST['id'];

    // Check database connection
    if (!$conn) {
        echo json_encode(['success' => false, 'error' => 'Database connection failed.']);
        exit();
    }

    // Prepare the SQL statement to prevent SQL injection
    $stmt = $conn->prepare("
        SELECT a.contact_no, rs.fullname
        FROM resource_req rs
        JOIN accounts a ON rs.resident_id = a.account_id
        WHERE rs.request_id = ?
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

    // Update the pickup status in the database
    $sql_update = "UPDATE resource_req SET pickup_status = 1 WHERE request_id = ?";
    $stmt_update = $conn->prepare($sql_update);
    $stmt_update->bind_param('i', $id);
    $result_update = $stmt_update->execute();

    if ($result_update) {
        $alert = 'Resource is ready for pick-up!';
        $alert_style = 'alert-success';

        // Send SMS notification using Semaphore API
        $ch = curl_init();
        $parameters = array(
            'apikey' => '485800cffee50c349559ffdde669afb1', // Replace with your actual API key
            'number' => $phone_number,
            'message' => "Hello, $fullname! Your requested resource is now ready for pick-up at the barangay.",
            'sendername' => 'DULANGAN'
        );

        curl_setopt($ch, CURLOPT_URL, 'https://semaphore.co/api/v4/messages');
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($parameters));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $output = curl_exec($ch);
        curl_close($ch);

        // Check for curl errors
        if ($output === false) {
            echo json_encode(['success' => false, 'error' => 'Curl error: ' . curl_error($ch)]);
            exit();
        }

        // Decode API response
        $response = json_decode($output, true);
        if (isset($response['success']) && $response['success'] == true) {
            echo json_encode(['success' => true, 'message' => 'Resource is ready for pick-up!']);
        } else {
            $error_message = isset($response['message']) ? $response['message'] : 'Unknown error';
            echo json_encode(['success' => false, 'error' => "Failed to send SMS notification: $error_message"]);
        }

    } else {
        echo json_encode(['success' => false, 'error' => 'Database update failed: ' . $stmt_update->error]);
    }

} else {
    echo json_encode(['success' => false, 'error' => 'Invalid request']);
}
?>
