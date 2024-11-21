<?php
include('../../connection.php');
if(isset($_GET['id'])){
    $id = $_GET['id'];
}
$sql = "UPDATE residents SET is_confirmed = '1' WHERE resident_id = '$id'";
$result = mysqli_query($conn, $sql);
if($result){
    $alert = 'Verification Successful!';
    $alert_style = 'alert-success';
}else{
    $alert = 'Verification Unsuccessful!';
    $alert_style = 'alert-warning';

}
header("location: ../residents.php?alert=$alert&style=$alert_style");
?>