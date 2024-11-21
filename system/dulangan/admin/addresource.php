<?php
session_start();
include('../connection.php'); // Your database connection file
include('include/notif.php');
$active = "addresource";

// Handle Add Resource
if (isset($_POST['add_resource'])) {
    $resource_name = mysqli_real_escape_string($conn, $_POST['name']);
    $quantity = (int)$_POST['quantity'];

    $sql = "INSERT INTO resources (name, quantity) VALUES ('$resource_name', '$quantity')";
    mysqli_query($conn, $sql);
    header('location: addresource.php');
    exit();
}

// Handle Edit Resource
if (isset($_POST['edit_resource'])) {
    $resource_id = (int)$_POST['resource_id'];
    $resource_name = mysqli_real_escape_string($conn, $_POST['resource_name']);
    $quantity = (int)$_POST['quantity'];

    $sql = "UPDATE resources SET name='$resource_name', quantity=$quantity WHERE resource_id=$resource_id";
    mysqli_query($conn, $sql);
    header('location: addresource.php');
    exit();
}

// Handle Delete Resource
if (isset($_GET['delete'])) {
    $resource_id = (int)$_GET['delete'];
    $sql = "DELETE FROM resources WHERE resource_id=$resource_id";
    mysqli_query($conn, $sql);
    header('location: addresource.php');
    exit();
}

// Pagination logic
$limit = 10; // Items per page
$total_sql = "SELECT COUNT(*) FROM resources"; // Get total count
$total_result = mysqli_query($conn, $total_sql);
$total_items = mysqli_fetch_row($total_result)[0];
$total_pages = ceil($total_items / $limit);
$current_page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$current_page = max(1, min($current_page, $total_pages)); // Ensure valid page number
$offset = ($current_page - 1) * $limit;

// Fetch Resources with LIMIT and OFFSET for pagination
$sql = "SELECT * FROM resources LIMIT $limit OFFSET $offset";
$result = mysqli_query($conn, $sql);
$resources = mysqli_fetch_all($result, MYSQLI_ASSOC);
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
    <title>Admin</title>
</head>
<body style="height: 100vh; background-color: #F5EDED">
    <div class="main-container d-flex">
        <?php include('include/sidebar.php'); ?>
        <div class="content min-vh-100 w-100 overflow-x-auto">
            <?php include('include/navbar.php'); ?>
            <div class="mainx p-3">
                <form action="" method="POST" class="mb-4">
                    <div class="mb-3">
                        <label for="resource_name" class="form-label">Resource Name</label>
                        <input type="text" class="form-control" name="name" required>
                    </div>
                    <div class="mb-3">
                        <label for="quantity" class="form-label">Quantity</label>
                        <input type="number" class="form-control" name="quantity" required>
                    </div>
                    <button type="submit" name="add_resource" class="btn btn-primary">Add Resource</button>
                </form>

                <!-- Resources Table -->
                <table class="table table-bordered" >
                    <thead>
                        <tr>
                            <th style="text-align: center;">#</th>
                            <th>Resource Name</th>
                            <th>Quantity</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($resources as $index => $resource) : ?>
                            <tr>
                                <td class="text-center"><?= ($current_page - 1) * $limit + $index + 1 ?></td>
                                <td><?= htmlspecialchars($resource['name']) ?></td>
                                <td><?= htmlspecialchars($resource['quantity']) ?></td>
                                <td>
                                    <button class="btn btn-warning btn-sm" data-bs-toggle="modal" data-bs-target="#editModal<?= $resource['resource_id'] ?>">Edit</button>
                                    <a href="?delete=<?= $resource['resource_id'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to delete this resource?');">Delete</a>
                                </td>
                            </tr>

                            <!-- Edit Modal -->
                            <div class="modal fade" id="editModal<?= $resource['resource_id'] ?>" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <form action="" method="POST">
                                            <div class="modal-header">
                                                <h5 class="modal-title" id="editModalLabel">Edit Resource</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                            </div>
                                            <div class="modal-body">
                                                <input type="hidden" name="resource_id" value="<?= $resource['resource_id'] ?>">
                                                <div class="mb-3">
                                                    <label for="resource_name" class="form-label">Resource Name</label>
                                                    <input type="text" class="form-control" name="resource_name" value="<?= htmlspecialchars($resource['name']) ?>" required>
                                                </div>
                                                <div class="mb-3">
                                                    <label for="quantity" class="form-label">Quantity</label>
                                                    <input type="number" class="form-control" name="quantity" value="<?= $resource['quantity'] ?>" required>
                                                </div>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                                <button type="submit" name="edit_resource" class="btn btn-primary">Save Changes</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </tbody>
                </table>
<!-- Pagination -->
<?php if ($total_pages > 1): ?>
        <nav aria-label="Page navigation example">
            <ul class="pagination">
                <li class="page-item <?= $current_page <= 1 ? 'disabled' : '' ?>">
                    <a class="page-link" href="?page=<?= $current_page - 1 ?>" aria-label="Previous">
                        <span aria-hidden="true">&laquo;</span>
                    </a>
                </li>
                <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                    <li class="page-item <?= $i == $current_page ? 'active' : '' ?>">
                        <a class="page-link" href="?page=<?= $i ?>"><?= $i ?></a>
                    </li>
                <?php endfor; ?>
                <li class="page-item <?= $current_page >= $total_pages ? 'disabled' : '' ?>">
                    <a class="page-link" href="?page=<?= $current_page + 1 ?>" aria-label="Next">
                        <span aria-hidden="true">&raquo;</span>
                    </a>
                </li>
            </ul>
        </nav>
    <?php endif; ?>
    </div>
    </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
