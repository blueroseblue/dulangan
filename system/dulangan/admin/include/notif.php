<?php
//clearance
$csql = "SELECT * FROM clearance_req WHERE req_status = 0";
$cresult = mysqli_query($conn, $csql);
$crow = mysqli_num_rows($cresult);
$cnotif = str_pad($crow, 2, "0", STR_PAD_LEFT);
//resources
$rsql = "SELECT * FROM resource_req WHERE req_status = 0";
$rresult = mysqli_query($conn, $rsql);
$rrow = mysqli_num_rows($rresult);
$rnotif = str_pad($rrow, 2, "0", STR_PAD_LEFT);
//accounts
$asql = "SELECT * FROM accounts WHERE is_accepted = 0";
$aresult = mysqli_query($conn, $asql);
$arow = mysqli_num_rows($aresult);
$anotif = str_pad($arow, 2, "0", STR_PAD_LEFT);
?>