<?php
include('../../connection.php');
if(isset($_GET['id'])){
    $id = $_GET['id'];
}
$sql = "DELETE FROM residents WHERE resident_id = '$id'";
$result = mysqli_query($conn, $sql);
if($result){
    $alert = 'Delete Successful!';
    $alert_style = 'alert-success';
}else{
    $alert = 'Delete Unsuccessful!';
    $alert_style = 'alert-danger';

}
header("location: ../residents.php?alert=$alert&style=$alert_style");
?>