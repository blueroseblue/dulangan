<?php
include('connection.php');

$sql = "SELECT * FROM table_example"; 

if(isset($_POST['Search']))
{
    if(!empty($_POST['firstname']))
    {
        $firstname = $_POST['firstname'];
        $sql = "SELECT * FROM table_example WHERE firstName LIKE '%$searchitem%'"; 
    }
    if(!empty($_POST['lastname']))
    {
        $lastname = $_POST['lastname'];
        $sql = "SELECT * FROM table_example WHERE lastname LIKE '%$searchitem%'"; 
    }
}
$result = mysqli_query($conn, $sql); 
$row = mysqli_fetch_assoc($result);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
    
</body>
</html>