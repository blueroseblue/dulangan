<?php
session_start();
include('../connection.php');
if (!isset($_SESSION['id'])) {
    header('location: ../index.php');
}

if (isset($_GET['req_id'])) {
    $req_id = $_GET['req_id'];
}

if (isset($_POST['submit'])) {
    $fileName = $_FILES["proof"]["name"];
    $fileSize = $_FILES["proof"]["size"];
    $tmpName = $_FILES["proof"]["tmp_name"];

    $imageExtension = pathinfo($fileName, PATHINFO_EXTENSION);
    $imageExtension = strtolower($imageExtension);
    $validExtension = ['jpg', 'jpeg', 'png'];

    if (!empty($fileName)) {
        if (!in_array($imageExtension, $validExtension)) {
            $error = '
                    <div class="form-text text-danger">
                        Invalid Image Extension!
                    </div>';
        } elseif ($fileSize > 1000000) {
            $error = '
                    <div class="form-text text-danger">
                        File size is too large!
                    </div>';
        } else {
            $newImageName = uniqid() . '.' . $imageExtension;
            $uploadDir = '../admin/proof_payments/' . $newImageName;

            move_uploaded_file($tmpName, $uploadDir);

            $sql = "UPDATE clearance_req SET payment_receipt = '$newImageName' WHERE request_id = '$req_id'";
            $result = mysqli_query($conn, $sql);

            if ($result) {
            ?>
                <div id="success" class="modal" tabindex="-1">
                    <div class="modal-dialog modal-dialog-centered">
                        <div class="modal-content">
                            <div class="modal-body text-center">
                                <h3 class="text-success mb-3"><i class="fa-solid fa-circle-check"></i> Upload Successful <i class="fa-solid fa-circle-check"></i></h3>
                                <p>Your proof of payment has been uploaded. Please wait for the admin to confirm your request. Thank you.</p>
                                <a href="home.php" class="btn btn-success">Okay</a>
                            </div>
                        </div>
                    </div>
                </div>
            <?php
            }
        }
    } else {
        $error = '<div class="form-text text-danger">
                        Please upload your payment screenshot as proof of payment!
                    </div>';
    }
}
?>
<style>
.custom-margin {
    margin-top: 15px; /* Adjust as needed */
}
</style>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../source/style.css">
    <link rel="icon" href="../image/logo.png">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <script src="https://kit.fontawesome.com/866d550866.js" crossorigin="anonymous"></script>
    <title>Brgy. Dulangan Request System</title>

</head>

<body style = "background-color:  #37AFE1;">
<div class="container h-100 custom-margin">
        <div class="d-flex justify-content-center align-items-center h-100">
            <div class="p-4 bg-white rounded shadow w-50 text-center">
                <div class="d-flex justify-content-start mb-3">
                    <a href="certificate_form.php" class="btn btn-danger">
                        <i class="fa-solid fa-left-long"></i>
                    </a>
                </div>
                <h2>Scan QR Code</h2>
                <?php
                if(isset($_GET['price'])){
                    $price = $_GET['price'];
                    if($price == 80){
                        echo '<img src="../image/eighty.jpeg" alt="logo" class="img-fluid w-50 mb-4">';
                    }else{
                        echo '<img src="../image/onethirty.jpeg" alt="logo" class="img-fluid w-50 mb-4">';
                    }
                }
                ?>
                <form action="" method="post" enctype="multipart/form-data">
                    <label class="form-label" for="customFile">Upload Proof of Payment</label>
                    <input type="file" name="proof" class="form-control" id="customFile" accept="image/png, image/jpg, image/jpeg" />
                    <?php if(isset($error)){echo $error;}?>
                    <button type="submit" name="submit" class="btn btn-primary mt-2">Submit</button>
                </form>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
    <script>
        //modal show
        document.addEventListener('DOMContentLoaded', function() {
            const successPopup = document.getElementById('success');
            if (successPopup) {
                const successModal = new bootstrap.Modal(successPopup);
                successModal.show();
            }
        });
    </script>
</body>

</html>