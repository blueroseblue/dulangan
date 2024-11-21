<?php
$conn = mysqli_connect("localhost", "root", "", "brgy_system");
if  (mysqli_connect_errno()){
    echo "Failed to connect to MySql: ". mysqli_connect_errno();
}
?>