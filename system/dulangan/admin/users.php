<?php
session_start();
include('../connection.php');
include('include/notif.php');

$active = "users";
$message = ''; // Initialize message as empty.

// Handle form submission for creating or updating a user
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Retrieve form data safely
    $name = isset($_POST['name']) ? $_POST['name'] : '';
    $username = isset($_POST['username']) ? $_POST['username'] : '';
    $password = isset($_POST['password']) ? $_POST['password'] : ''; // If empty, use the current password
    $type = isset($_POST['type']) ? $_POST['type'] : '';
    $admin_id = isset($_POST['admin_id']) ? $_POST['admin_id'] : null;

  // Validate fields (basic validation)
    if (empty($name) || empty($username) || empty($type)) {
        $message = "Please fill in all required fields.";
    } else {
        // Check if we are updating an existing user or creating a new one
        if ($admin_id) {
            // Updating an existing user
            $query = "UPDATE admin SET name = ?, username = ?, type = ? WHERE admin_id = ?";
            if (!empty($password)) {
                // If password is provided, hash and include it
                $passwordHash = password_hash($password, PASSWORD_BCRYPT);
                $query = "UPDATE admin SET name = ?, username = ?, password = ?, type = ? WHERE admin_id = ?";
                $stmt = $conn->prepare($query);
                $stmt->bind_param("sssii", $name, $username, $passwordHash, $type, $admin_id);
            } else {
                // If password is not provided, just update other fields
                $stmt = $conn->prepare($query);
                $stmt->bind_param("ssii", $name, $username, $type, $admin_id);
            }

            if ($stmt->execute()) {
                $message = "User updated successfully.";
            } else {
                $message = "Error updating user. Please try again.";
            }
        } else {
            // Creating a new user
            // Check if the username already exists
            $stmt = $conn->prepare("SELECT * FROM admin WHERE username = ?");
            $stmt->bind_param("s", $username);
            $stmt->execute();
            $result = $stmt->get_result();
            
            if ($result->num_rows > 0) {
                $message = "Username already exists.";
            } else {
                // Insert new user
                $passwordHash = password_hash($password, PASSWORD_BCRYPT);
                $stmt = $conn->prepare("INSERT INTO admin (name, username, password, type) VALUES (?, ?, ?, ?)");
                $stmt->bind_param("sssi", $name, $username, $passwordHash, $type);
                
                if ($stmt->execute()) {
                    $message = "User created successfully.";
                } else {
                    $message = "Error creating user. Please try again.";
                }
            }
        }
    }
}

// Handle delete request (Delete User)
if (isset($_GET['delete'])) {
    $delete_id = $_GET['delete'];
    $delete_stmt = $conn->prepare("DELETE FROM admin WHERE admin_id = ?");
    $delete_stmt->bind_param("i", $delete_id);
    
    if ($delete_stmt->execute()) {
        $message = "User deleted successfully.";
    } else {
        $message = "Error deleting user.";
    }
}

// Fetch user data for display
$type = array("", "Admin", "Brgy.Official");
$admin = $conn->query("SELECT * FROM admin");

// Pagination logic and Search Query
$limit = 10;
$search = '';
if (isset($_POST['search']) && !empty($_POST['search_name'])) {
    $search = mysqli_escape_string($conn, $_POST['search_name']);
    $total_sql = "SELECT COUNT(*) FROM admin WHERE name LIKE '%$search%'";
} else {
    $total_sql = "SELECT COUNT(*) FROM admin";
}

$total_result = mysqli_query($conn, $total_sql);
$total_items = mysqli_fetch_row($total_result)[0];
$total_pages = ceil($total_items / $limit);
$current_page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$current_page = max(1, min($current_page, $total_pages)); // Ensure valid page number
$offset = ($current_page - 1) * $limit;

// Fetch Admins with LIMIT and OFFSET for pagination
if ($search) {
    $sql = "SELECT * FROM admin WHERE name LIKE '%$search%' LIMIT $limit OFFSET $offset";
} else {
    $sql = "SELECT * FROM admin LIMIT $limit OFFSET $offset";
}

$result = mysqli_query($conn, $sql);
$admin = mysqli_fetch_all($result, MYSQLI_ASSOC);
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
    <script src="https://cdn.jsdelivr.net/npm/jquery@3.6.0/dist/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <title>Admin</title>
</head>

<body style="height: 100vh; background-color: #F5EDED">
    <div class="main-container d-flex">
        <?php include('include/sidebar.php'); ?>
        <div class="content min-vh-100 w-100 overflow-x-auto">
            <?php include('include/navbar.php') ?>
            <div class="mainx p-3">
                
                    <h2> User List </h2>
                    <!-- Search Form and New User Button Side by Side -->
                    <div class="row mb-3">
                        <!-- Search Form (Taking up 8 columns) -->
                        <div class="col-12 col-md-7 d-flex">
                            <form action="users.php" method="POST" class="w-100">
                                <div class="d-flex w-100">
                                    <input type="text" name="search_name" class="form-control form-control-sm" placeholder="Search" value="<?= $search ?>" style="flex: 1; margin-right: 10px;">
                                    <button type="submit" name="search" class="btn btn-primary btn-sm">Search</button>
                                </div>
                            </form>
                        </div>
                        
                        <!-- New User Button (Aligned Right) -->
                        <div class="col-12 col-md-5 d-flex justify-content-end">
                            <button class="btn btn-success btn-sm" id="new_user"><i class="fa fa-plus"></i> New User</button>
                        </div>
                    </div>
               <!-- Display Success or Error Message -->
                <?php if (!empty($message) && !isset($_POST['search_name'])): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <?= $message ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
                <?php endif; ?>
                <!-- User List Table -->
                <div class="table-responsive">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th class="text-center">#</th>
                                <th>Name</th>
                                <th>Username</th>
                                <th>Type</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php
                            if (empty($admin)) {
                                echo '<tr class="text-center"><td colspan="5">No Records Found</td></tr>';
                            } else {
                                $i = $offset + 1; // Adjust for pagination offset
                                foreach ($admin as $row): ?>
                                    <tr>
                                        <td class="text-center"><?= $i++ ?></td>
                                        <td><?= htmlspecialchars($row['name']) ?></td>
                                        <td><?= htmlspecialchars($row['username']) ?></td>
                                        <td><?= $type[$row['type']] ?></td>
                                        <td class="text-center" style="width: 150px;">
                                            <button class="btn btn-warning btn-sm edit_user" data-bs-toggle="modal" data-bs-target="#editModal<?= $row['admin_id'] ?>">Edit</button>
                                            <a href="users.php?delete=<?= $row['admin_id'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to delete this user?')">Delete</a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php } ?>
                        </tbody>
                    </table>

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
                                <li class="page-item <?= $i == $current_page ? 'active' : '' ?>"><a class="page-link" href="?page=<?= $i ?>&search_name=<?= urlencode($search) ?>"><?= $i ?></a></li>
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
    </div>

    <!-- Modal for New User Form -->
    <div class="modal fade" id="newUserModal" tabindex="-1" aria-labelledby="newUserModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form action="users.php" method="POST">
                    <div class="modal-header">
                        <h5 class="modal-title" id="newUserModalLabel">Create New User</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="name" class="form-label">Name:</label>
                            <input type="text" class="form-control" name="name" required>
                        </div>
                        <div class="mb-3">
                            <label for="username" class="form-label">Username:</label>
                            <input type="text" class="form-control" name="username" required>
                        </div>
                        <div class="mb-3">
                            <label for="password" class="form-label">Password:</label>
                            <input type="password" class="form-control" name="password" required>
                        </div>
                        <div class="mb-3">
                            <label for="type" class="form-label">User Type:</label>
                            <select class="form-control" name="type" required>
                                <option value="" selected disabled hidden>Select...</option>
                                <option value="2">Brgy.Official</option>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Save User</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal for Editing User -->
    <?php foreach ($admin as $row): ?>
        <div class="modal fade" id="editModal<?= $row['admin_id'] ?>" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <form action="users.php" method="POST">
                        <div class="modal-header">
                            <h5 class="modal-title" id="editModalLabel">Edit User</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <input type="hidden" name="admin_id" value="<?= $row['admin_id'] ?>">
                            <div class="mb-3">
                                <label for="name" class="form-label">Name:</label>
                                <input type="text" class="form-control" name="name" value="<?= htmlspecialchars($row['name']) ?>" required>
                            </div>
                            <div class="mb-3">
                                <label for="username" class="form-label">Username:</label>
                                <input type="text" class="form-control" name="username" value="<?= htmlspecialchars($row['username']) ?>" required>
                            </div>
                            <div class="mb-3">
                                <label for="password" class="form-label">Password:</label>
                                <input type="password" class="form-control" name="password" placeholder="Leave blank to keep current password">
                            </div>
                            <div class="mb-3">
                                <label for="type" class="form-label">User Type:</label>
                                <select class="form-control" name="type" required>
                                    <option value="" selected disabled hidden>Select...</option>
                                    <option value="1" <?= $row['type'] == 1 ? 'selected' : '' ?>>Admin</option>
                                </select>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                            <button type="submit" class="btn btn-primary">Save Changes</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    <?php endforeach; ?>

    <script>
        // Trigger the modal when the "New User" button is clicked
        $('#new_user').click(function() {
            $('#newUserModal').modal('show');
        });
    </script>
</body>

</html>
