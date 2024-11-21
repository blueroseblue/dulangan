<?php
session_start();
include('../connection.php');
include('include/notif.php');

$active = "accounts_staff";
$c = date('Y-m-d');
if (!isset($_SESSION['admin_id'])) {
    header('location: index.php');
}

$limit = 10; // Set the number of items per page
$search = '';

// Handle search functionality
if (isset($_POST['search']) && !empty($_POST['search_name'])) {
    $search = mysqli_real_escape_string($conn, $_POST['search_name']);
}

// Get total number of items for pagination
$total_sql = $search ? 
    "SELECT COUNT(*) FROM accounts WHERE fullname LIKE '%$search%'" : 
    "SELECT COUNT(*) FROM accounts";

$total_result = mysqli_query($conn, $total_sql);
$total_items = mysqli_fetch_row($total_result)[0];
$total_pages = ceil($total_items / $limit);
$current_page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$current_page = max(1, min($current_page, $total_pages)); // Ensure valid page number
$offset = ($current_page - 1) * $limit;

// Fetch Resident Accounts with LIMIT and OFFSET for pagination
$sql = $search ? 
    "SELECT * FROM accounts WHERE fullname LIKE '%$search%' ORDER BY account_id DESC LIMIT $limit OFFSET $offset" : 
    "SELECT * FROM accounts ORDER BY account_id DESC LIMIT $limit OFFSET $offset";

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
                <h2>Registered Residents</h2>
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
                                <th>Username</th>
                                <th>Contact No.</th>
                                <th>Valid ID</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            if (!mysqli_num_rows($result)) {
                                echo '<tr class="text-center"><td colspan="6">No Records Yet</td></tr>';
                            } else {
                                $count = $offset + 1; // Start counting from offset
                                while ($row = mysqli_fetch_assoc($result)) {
                            ?>
                                    <tr>
                                        <td class="text-center"><?= $count ?></td>
                                        <td><?= htmlspecialchars($row['fullname']) ?></td>
                                        <td><?= htmlspecialchars($row['username']) ?></td>
                                        <td><?= str_pad($row['contact_no'], 11, "0", STR_PAD_LEFT) ?></td>
                                        <td class="text-center">
                                            <button type="button" class="btn btn-info btn-sm" data-bs-toggle="modal" data-bs-target="#viewIDModal" data-id="<?= htmlspecialchars($row['valid_id']) ?>">
                                                View ID
                                            </button>
                                        </td>
                                        <td class="text-center" style="width: 250px;">
                                            <a href="accept.php?id=<?= $row['account_id'] ?>" class="btn btn-success btn-sm <?= ($row['is_accepted'] == 1 || $row['is_accepted'] == 2) ? 'disabled' : '' ?>">Accept</a>
                                            <a href="decline.php?id=<?= $row['account_id'] ?>" class="btn btn-warning btn-sm <?= ($row['is_accepted'] == 1 || $row['is_accepted'] == 2) ? 'disabled' : '' ?>">Decline</a>
                                            <a href="functions/delete.php?id=<?= $row['account_id'] ?>" class="btn btn-danger btn-sm">Delete</a>
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

    <!-- Modal HTML -->
    <div class="modal fade" id="viewIDModal" tabindex="-1" aria-labelledby="viewIDModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="viewIDModalLabel">Valid ID</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body text-center">
                    <img id="validIDImage" src="" alt="Valid ID" class="img-fluid" style="max-height: 400px; display:none;">
                    <p id="validIDLink" class="mt-3"></p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Modal for viewing valid ID
        document.addEventListener('DOMContentLoaded', function () {
            var viewIDModal = document.getElementById('viewIDModal');
            viewIDModal.addEventListener('show.bs.modal', function (event) {
                var button = event.relatedTarget;
                var validID = button.getAttribute('data-id');
                var validIDImage = document.getElementById('validIDImage');
                var validIDLink = document.getElementById('validIDLink');

                if (validID) {
                    var validIDPath = '../uploads/profile/' + validID;
                    var isImage = /\.(jpe?g|png|gif)$/i.test(validID);

                    if (isImage) {
                        validIDImage.src = validIDPath;
                        validIDImage.style.display = 'block';
                        validIDLink.style.display = 'none';
                    } else {
                        validIDImage.style.display = 'none';
                        validIDLink.innerHTML = `<a href="${validIDPath}" target="_blank">Download ID</a>`;
                        validIDLink.style.display = 'block';
                    }
                } else {
                    validIDImage.style.display = 'none';
                    validIDLink.innerHTML = 'No valid ID available';
                    validIDLink.style.display = 'block';
                }
            });
        });
    </script>

</body>
</html>
