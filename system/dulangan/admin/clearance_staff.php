<?php
session_start();
include('../connection.php');
include('include/notif.php');

$active = "clearance_staff";

if (!isset($_SESSION['admin_id'])) {
    header('location: index.php');
}

$limit = 10;
$search = '';

if (isset($_POST['search']) && !empty($_POST['search_name'])) {
    $search = mysqli_escape_string($conn, $_POST['search_name']);
    $total_sql = "SELECT COUNT(*) FROM clearance_req WHERE fullname LIKE '%$search%'";
} else {
    $total_sql = "SELECT COUNT(*) FROM clearance_req";
}

// Pagination
$total_result = mysqli_query($conn, $total_sql);
$total_items = mysqli_fetch_row($total_result)[0];
$total_pages = ceil($total_items / $limit);
$current_page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$current_page = max(1, min($current_page, $total_pages)); // Ensure valid page number
$offset = ($current_page - 1) * $limit;

// Fetch Clearance Requests
if ($search) {
    $sql = "SELECT * FROM clearance_req WHERE fullname LIKE '%$search%' ORDER BY request_id DESC LIMIT $limit OFFSET $offset";
} else {
    $sql = "SELECT * FROM clearance_req ORDER BY request_id DESC LIMIT $limit OFFSET $offset";
}

$result = mysqli_query($conn, $sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../source/style.css">
    <link rel="icon" href="../image/logo.png">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://kit.fontawesome.com/866d550866.js" crossorigin="anonymous"></script>
    <title>Staff</title>
</head>
<body style="height: 100vh; background-color: #F5EDED">
    <div class="main-container d-flex">
        <?php include('include/sidebar_user.php'); ?>
        <div class="content min-vh-100 w-100 overflow-x-auto">
            <?php include('include/navbar_user.php'); ?>
            <div class="mainx p-3">
                <h2>Clearance Request List</h2>
                <form action="" method="post">
    <div class="row mb-2">
        <!-- Use d-flex to align items on the same row -->
        <div class="col-12 col-md-6 d-flex">
            <!-- Search bar -->
            <input type="search" class="form-control form-control-sm" name="search_name" value="<?= htmlspecialchars($search) ?>" placeholder="Search" style="flex: 1;">

            <!-- Search Button -->
            <button name="search" class="btn btn-primary btn-sm" style="margin-left: 10px;">Search</button>
</div>
                    </div>
                </form>

                <?php if (isset($_GET['alert']) && isset($_GET['style'])): ?>
                    <div class='alert <?= htmlspecialchars($_GET['style']) ?> alert-dismissible fade show' role='alert'>
                        <?= htmlspecialchars($_GET['alert']) ?>
                        <button type='button' class='btn-close' data-bs-dismiss='alert' aria-label='Close'></button>
                    </div>
                <?php endif; ?>

                <div class="table-responsive">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th style="text-align: center;">#</th>
                                <th>Fullname</th>
                                <th>Age</th>
                                <th>Status</th>
                                <th>Purok</th>
                                <th>Document</th>
                                <th>Purpose</th>
                                <th>Price</th>
                                <th>Payment Type</th>
                                <th>Request Date</th>
                                <th>Req. Status</th>
                                <th>Pick-up Status</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (mysqli_num_rows($result) === 0): ?>
                                <tr class="text-center"><td colspan="99">No Records Yet</td></tr>
                            <?php else: ?>
                                <?php $count = $offset + 1; ?>
                                <?php while ($row = mysqli_fetch_assoc($result)): ?>
                                    <tr>
                                        <td class="text-center"><?= $count ?></td>
                                        <td><?= htmlspecialchars($row['fullname']) ?></td>
                                        <td><?= htmlspecialchars($row['age']) ?></td>
                                        <td><?= htmlspecialchars($row['status']) ?></td>
                                        <td><?= htmlspecialchars($row['purok']) ?></td>
                                        <td><?= htmlspecialchars($row['document']) ?></td>
                                        <td><?= htmlspecialchars($row['purpose']) ?></td>
                                        <td><?= htmlspecialchars($row['price']) ?></td>
                                        <td class="text-center">
                                            <?php if ($row['payment_type'] == 'online'): ?>
                                                <button class='btn btn-primary btn-sm proof-btn' data-id="<?= $row['request_id'] ?>" data-bs-toggle='modal' data-bs-target='#proof_modal'><?= htmlspecialchars($row['payment_type']) ?></button>
                                            <?php else: ?>
                                                <div class='badge rounded-pill bg-success'><?= htmlspecialchars($row['payment_type']) ?></div>
                                            <?php endif; ?>
                                        </td>
                                        <td><?= htmlspecialchars($row['request_date']) ?></td>
                                        <td>
                                            <div class='badge rounded-pill <?= $row['req_status'] == 0 ? 'bg-warning' : ($row['req_status'] == 1 ? 'bg-success' : 'bg-danger') ?>'>
                                                <?= $row['req_status'] == 0 ? 'Pending' : ($row['req_status'] == 1 ? 'Accepted' : 'Rejected') ?>
                                            </div>
                                        </td>
                                        <td>
                                            <div class='badge rounded-pill <?= $row['pickup_status'] == 0 ? 'bg-warning' : 'bg-success' ?>'>
                                                <?= $row['pickup_status'] == 0 ? 'Not Ready' : 'Ready' ?>
                                            </div>
                                            </td>
                                            <td>
                                            <a href="functions/accept_doc.php?id=<?= $row['request_id'] ?>" class="btn btn-success btn-sm <?= $row['req_status'] > 0 ? 'disabled' : '' ?>">Accept</a>
                                            <button class="btn btn-danger btn-sm reject-btn" data-id="<?= $row['request_id'] ?>" data-name="<?= htmlspecialchars($row['fullname']) ?>" <?= $row['req_status'] > 0 ? 'disabled' : '' ?>>Reject</button>
                                            <button class="btn btn-warning btn-sm ready-pickup-btn" data-id="<?= $row['request_id'] ?>">Pick-Up</button>
                                        </td>
                                    </tr>
                                    <?php $count++; ?>
                                <?php endwhile; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <?php if ($total_pages > 1): ?>
                    <nav aria-label="Page navigation example">
                        <ul class="pagination">
                            <li class="page-item <?= $current_page <= 1 ? 'disabled' : '' ?>">
                                <a class="page-link" href="?page=<?= $current_page - 1 ?>&search_name=<?= urlencode($search) ?>" aria-label="Previous">
                                    <span aria-hidden="true">&laquo;</span>
                                </a>
                            </li>
                            <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                                <li class="page-item <?= $i == $current_page ? 'active' : '' ?>">
                                    <a class="page-link" href="?page=<?= $i ?>&search_name=<?= urlencode($search) ?>"><?= $i ?></a>
                                </li>
                            <?php endfor; ?>
                            <li class="page-item <?= $current_page >= $total_pages ? 'disabled' : '' ?>">
                                <a class="page-link" href="?page=<?= $current_page + 1 ?>&search_name=<?= urlencode($search) ?>" aria-label="Next">
                                    <span aria-hidden="true">&raquo;</span>
                                </a>
                            </li>
                        </ul>
                    </nav>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Payment Proof Modal -->
    <div class="modal fade" id="proof_modal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title fs-5" id="exampleModalLabel">Payment Proof</h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <img src="" class="img-fluid" id="proof_img">
                    <h2 class="fw-bold text-center mt-2 d-none" id="note">No Proof Uploaded Yet</h2>
                </div>
            </div>
        </div>
    </div>

    <!-- Reject Confirmation Modal -->
    <div class="modal fade" id="reject_modal" tabindex="-1" aria-labelledby="rejectModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title fs-5" id="rejectModalLabel">Reject Document Request</h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p>Are you sure you want to reject this document request?</p>
                    <form id="rejectForm" method="post" action="functions/reject_doc.php">
                        <input type="hidden" name="request_id" id="request_id">
                        <input type="hidden" name="fullname" id="fullname">
                    </form>
                    <div class="mb-3">
                        <label for="reject-message" class="form-label">Message (optional)</label>
                        <textarea class="form-control" id="reject-message" rows="3" placeholder="Optional message to the resident"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-danger" id="confirm_reject">Reject</button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        $(document).ready(function() {
            // Show the reject modal
            $('.reject-btn').on('click', function() {
                var requestId = $(this).data('id');
                var fullname = $(this).data('name');
                $('#request_id').val(requestId);
                $('#fullname').val(fullname);
                $('#reject_modal').modal('show');
            });

            // Confirm reject action
            $('#confirm_reject').on('click', function() {
                var requestId = $('#request_id').val();
                var rejectMessage = $('#reject-message').val();

                $.ajax({
                    url: 'functions/reject_doc.php',
                    type: 'POST',
                    data: {
                        id: requestId,
                        message: rejectMessage
                    },
                    success: function(response) {
                        alert('Request rejected successfully');
                        $('#reject_modal').modal('hide');
                        location.reload(); // Reload the page to reflect changes
                    },
                    error: function(xhr, status, error) {
                        console.log('Error:', error);
                    }
                });
            });

            // Show payment proof modal
            $(document).on('click', '.proof-btn', function() {
                var id = $(this).data('id');
                $.ajax({
                    url: 'functions/fetch_proof.php',
                    type: 'POST',
                    data: { id: id },
                    success: function(response) {
                        var data = JSON.parse(response);
                        $('#proof_img').attr('src', 'proof_payments/' + data.payment_receipt);
                        $('#proof_modal').modal('show');
                    },
                    error: function(xhr, status, error) {
                        console.log('Error:', error);
                    }
                });
            });
 // Ready to Pick-Up functionality
 $('.ready-pickup-btn').on('click', function() {
        var requestId = $(this).data('id');

        // Confirmation for the action
        if (confirm("Are you sure this request is ready for pick-up?")) {
            $.ajax({
                url: 'functions/ready_doc.php',
                type: 'POST',
                data: { id: requestId },
                dataType: 'json', // Expect a JSON response
                success: function(response) {
                    console.log(response);
                    location.reload(); // Reload the page to reflect changes
                },
                error: function(xhr, status, error) {
                    console.error('AJAX Error: ' + status + ': ' + error); // Log any AJAX errors
                }
            });
        }
    });
});
</script>
</body>
</html>