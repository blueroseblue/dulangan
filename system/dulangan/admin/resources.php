<?php
session_start();
include('../connection.php');
include('include/notif.php');

$active = "resources";

if (!isset($_SESSION['admin_id'])) {
    header('location: index.php');
    exit();
}

$limit = 10;
$search = '';

// Handle search functionality
if (isset($_POST['search']) && !empty($_POST['search_name'])) {
    $search = mysqli_real_escape_string($conn, $_POST['search_name']);
}

// Get total number of items for pagination
$total_sql = $search ? 
    "SELECT COUNT(*) FROM resource_req WHERE fullname LIKE ?" : 
    "SELECT COUNT(*) FROM resource_req";

$stmt = mysqli_prepare($conn, $total_sql);
if ($search) {
    $param = '%' . $search . '%';
    mysqli_stmt_bind_param($stmt, 's', $param);
}
mysqli_stmt_execute($stmt);
$total_result = mysqli_stmt_get_result($stmt);
$total_items = mysqli_fetch_row($total_result)[0];
$total_pages = ceil($total_items / $limit);
$current_page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$current_page = max(1, min($current_page, $total_pages)); // Ensure valid page number
$offset = ($current_page - 1) * $limit;

// Fetch Resource Requests with LIMIT and OFFSET for pagination
$sql = $search ? 
    "SELECT * FROM resource_req WHERE fullname LIKE ? ORDER BY request_id DESC LIMIT ? OFFSET ?" : 
    "SELECT * FROM resource_req ORDER BY request_id DESC LIMIT ? OFFSET ?";

$stmt = mysqli_prepare($conn, $sql);
if ($search) {
    mysqli_stmt_bind_param($stmt, 'sii', $param, $limit, $offset);
} else {
    mysqli_stmt_bind_param($stmt, 'ii', $limit, $offset);
}
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../source/style.css">
    <link rel="icon" href="../image/logo.png">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://kit.fontawesome.com/866d550866.js" crossorigin="anonymous"></script>
    <title>Admin</title>
</head>

<body style="height: 100vh; background-color: #F5EDED">
    <div class="main-container d-flex">
        <?php include('include/sidebar.php'); ?>
        <div class="content min-vh-100 w-100 overflow-x-auto">
            <?php include('include/navbar.php'); ?>
            <div class="mainx p-3">
                <h2>Resources Request List</h2>
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
                
                <div class="table-responsive">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th style="text-align: center;">#</th>
                                <th>Fullname</th>
                                <th>Age</th>
                                <th>Purok</th>
                                <th>Resource</th>
                                <th>Purpose</th>
                                <th>Quantity</th>
                                <th>Borrow Date</th>
                                <th>Return Date</th>
                                <th>Req. Status</th>
                                <th>Pick-up Status</th>
                                <th>Return Status</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            if (!mysqli_num_rows($result)) {
                                echo '<tr class="text-center"><td colspan="99">No Records Yet</td></tr>';
                            } else {
                                $count = $offset + 1; // Start counting from offset
                                while ($row = mysqli_fetch_assoc($result)) {
                                    $req_status = $row['req_status'] == 0 ? "<div class='badge rounded-pill bg-warning'>Pending</div>" :
                                                ($row['req_status'] == 1 ? "<div class='badge rounded-pill bg-success'>Accepted</div>" :
                                                "<div class='badge rounded-pill bg-danger'>Rejected</div>");

                                    $pickup_status = $row['pickup_status'] == 0 ? "<div class='badge rounded-pill bg-warning'>Not Ready</div>" : 
                                                "<div class='badge rounded-pill bg-success'>Ready</div>"; 

                                    $return_status = $row['is_returned'] == 0 ? "<div class='badge rounded-pill bg-warning'>Pending</div>" :
                                                    ($row['is_returned'] == 1 ? "<div class='badge rounded-pill bg-danger'>Borrowed</div>" :
                                                    "<div class='badge rounded-pill bg-success'>Returned</div>");
                            ?>
                                    <tr>
                                        <td class="text-center"><?= $count ?></td>
                                        <td><?= htmlspecialchars($row['fullname']) ?></td>
                                        <td><?= htmlspecialchars($row['age']) ?></td>
                                        <td><?= htmlspecialchars($row['purok']) ?></td>
                                        <td><?= htmlspecialchars($row['resource']) ?></td>
                                        <td><?= htmlspecialchars($row['purpose']) ?></td>
                                        <td class="text-center"><?= htmlspecialchars($row['request_quantity']) ?></td>
                                        <td><?= htmlspecialchars($row['request_date']) ?></td>
                                        <td><?= htmlspecialchars($row['return_date']) ?></td>
                                        <td><?= $req_status ?></td>
                                        <td><?= $pickup_status ?></td>
                                        <td><?= $return_status ?></td>
                                        <td>
                                    <?php if ($row['is_returned'] == 1): ?>
                                    <a href="functions/return_res.php?id=<?= $row['request_id'] ?>&res_id=<?= $row['resource_id'] ?>&qnt=<?= $row['request_quantity'] ?>" class="btn btn-success btn-sm">Return</a>
                                    <?php else: ?>
                                    <a href="functions/accept_res.php?id=<?= $row['request_id'] ?>&res_id=<?= $row['resource_id'] ?>&qnt=<?= $row['request_quantity'] ?>" class="btn btn-success btn-sm <?= $row['req_status'] > 0 ? 'disabled' : '' ?>">Accept</a>
                                    <?php endif; ?>
                                    <button class="btn btn-danger btn-sm reject-btn" data-id="<?= $row['request_id'] ?>" data-name="<?= htmlspecialchars($row['fullname']) ?>" <?= $row['req_status'] > 0 ? 'disabled' : '' ?>>Reject</button>
                                    <button class="btn btn-warning btn-sm ready-pickup-btn" data-id="<?= $row['request_id'] ?>">Pick-Up</button>
                                    <a href="functions/notify.php?id=<?= $row['request_id'] ?>" class="btn btn-primary btn-sm">Notify</a>
                                </td>
                                    </tr>
                            <?php
                                    $count++;
                                }
                            }
                            ?>
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

  <!-- Reject Confirmation Modal -->
  <div class="modal fade" id="reject_modal" tabindex="-1" aria-labelledby="rejectModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title fs-5" id="rejectModalLabel">Reject Resource Request</h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p>Are you sure you want to reject this resource request?</p>
                    <form id="rejectForm" method="post" action="functions/reject_res.php">
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
                    url: 'functions/reject_res.php',
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

    // Ready to Pick-Up functionality
    $('.ready-pickup-btn').on('click', function() {
        var requestId = $(this).data('id');

        // Confirmation for the action
        if (confirm("Are you sure this request is ready for pick-up?")) {
            $.ajax({
                url: 'functions/ready_res.php',
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
