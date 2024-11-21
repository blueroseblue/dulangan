<?php
include('../../connection.php');
if(isset($_GET['id'])&&isset($_GET['res_id'])&&isset($_GET['qnt'])){
    $id = $_GET['id'];
    $res_id = $_GET['res_id'];
    $qnt = $_GET['qnt'];
}
$sql = "UPDATE resource_req SET is_returned = '2' WHERE request_id = '$id'";
$result = mysqli_query($conn, $sql);
if($result){
    $alert = 'Resource has been returned!';
    $alert_style = 'alert-success';
}else{
    $alert = 'Something went wrong!';
    $alert_style = 'alert-warning';

}
$sql = "SELECT on_borrow FROM resources WHERE resource_id = $res_id";
$result = mysqli_query($conn, $sql);
$row = mysqli_fetch_assoc($result);

$on_borrow = $row['on_borrow'];
$on_borrow = $on_borrow - $qnt;

$sql = "UPDATE resources SET is_borrowed = 0, on_borrow = $on_borrow  WHERE resource_id = '$res_id'";
$result = mysqli_query($conn, $sql);

header("location: ../resources.php?alert=$alert&style=$alert_style");
?>