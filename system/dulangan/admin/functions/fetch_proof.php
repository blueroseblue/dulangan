<?php
include('../../connection.php');
if(isset($_POST['id'])){
    $id = intval($_POST['id']);

    $proof_sql = "SELECT * FROM clearance_req WHERE request_id = '$id'";
    $proof_result = mysqli_query($conn, $proof_sql);

    if($proof_result){
        $proof_row = mysqli_fetch_assoc($proof_result);
        echo json_encode($proof_row);
    }else{
        echo json_encode(['error' => 'No records found']);
    }
}
?>