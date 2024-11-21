<?php
session_start();
include('../connection.php');
include('include/notif.php');

if (isset($_POST['submit'])) {
    $fullname = $_POST['fullname'];
    $age = $_POST['age'];
    $purok = $_POST['purok'];
    $years = $_POST['years'];

    $sql = "INSERT INTO residents (fullname, age, purok, years) VALUES ('$fullname', '$age', '$purok', '$years')";
    $result = mysqli_query($conn, $sql);
    if ($result) {
        $success = 1;
        $addmsg = "Add Successful";
    }
}

$cdate = date("Y-m-d");
$active = "all";
if (!isset($_SESSION['admin_id'])) {
    header('location: index.php');
}

$limit = 10;
$search = '';
if (isset($_POST['search']) && !empty($_POST['search_name'])) {
    $search = mysqli_escape_string($conn, $_POST['search_name']);
    $total_sql = "SELECT COUNT(*) FROM residents WHERE fullname LIKE '%$search%'";
} else {
    $total_sql = "SELECT COUNT(*) FROM residents";
}

$total_result = mysqli_query($conn, $total_sql);
$total_items = mysqli_fetch_row($total_result)[0];
$total_pages = ceil($total_items / $limit);
$current_page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$current_page = max(1, min($current_page, $total_pages)); // Ensure valid page number
$offset = ($current_page - 1) * $limit;

// Fetch Residents with LIMIT and OFFSET for pagination
if ($search) {
    $sql = "SELECT * FROM residents WHERE fullname LIKE '%$search%' LIMIT $limit OFFSET $offset";
} else {
    $sql = "SELECT * FROM residents LIMIT $limit OFFSET $offset";
}

$result = mysqli_query($conn, $sql);
$resources = mysqli_fetch_all($result, MYSQLI_ASSOC);

if (isset($_POST['edit_resident'])) {
    $resident_id = $_POST['resident_id'];
    $fullname = $_POST['fullname'];
    $age = $_POST['age'];
    $purok = $_POST['purok'];
    $years = $_POST['years'];

    // Prepare and bind
    $stmt = $conn->prepare("UPDATE residents SET fullname=?, age=?, purok=?, years=? WHERE resident_id=?");
    $stmt->bind_param("sissi", $fullname, $age, $purok, $years, $resident_id);

    if ($stmt->execute()) {
        header("Location: residents.php?alert=Update Successful&style=alert-success");
        exit;
    } else {
        echo "Error: " . $stmt->error; // Debugging line
    }
}
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
        <?php include('include/sidebar.php') ?>
        <div class="content min-vh-100 w-100 overflow-x-auto">
            <?php include('include/navbar.php') ?>
            <div class="mainx p-3">
                <h2>Residents List</h2>
                <form action="" method="post">
    <div class="row mb-2">
        <!-- Use d-flex to align search bar and search button -->
        <div class="col-12 col-md-7 d-flex">
            <!-- Search bar -->
            <input type="search" class="form-control form-control-sm" name="search_name" value="<?= htmlspecialchars($search) ?>" placeholder="Search" style="flex: 1;">

            <!-- Search Button -->
            <button name="search" class="btn btn-primary btn-sm" style="margin-left: 10px;">Search</button>
        </div>

        <!-- Use col-md-5 for Add Resident button and push it to the right -->
        <div class="col-12 col-md-5 d-flex justify-content-end">
            <button type="button" class="btn btn-success btn-sm" data-bs-toggle="modal" data-bs-target="#staticBackdrop"><i class="fa fa-plus"></i> Add Resident</button>
        </div>
    </div>
        </form>
                <?php
                if (isset($_GET['alert']) && isset($_GET['style'])) {
                    $msg = $_GET['alert'];
                    $style = $_GET['style'];
                    echo "<div class='alert $style alert-dismissible fade show' role='alert'>
                            $msg
                            <button type='button' class='btn-close' data-bs-dismiss='alert' aria-label='Close'></button>
                          </div>";
                } elseif (!empty($success)) {
                    echo "<div class='alert alert-success alert-dismissible fade show' role='alert'>
                            $addmsg
                            <button type='button' class='btn-close' data-bs-dismiss='alert' aria-label='Close'></button>
                          </div>";
                }
                ?>
                <div class="table-responsive">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th style="text-align: center;">#</th>
                                <th>Fullname</th>
                                <th>Age</th>
                                <th>Purok</th>
                                <th>Year</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            if (empty($resources)) {
                                echo '<tr class="text-center"><td colspan="6">No Records Yet</td></tr>';
                            } else {
                                $count = $offset + 1; // Adjust for pagination offset
                                foreach ($resources as $row) {
                                    $years = (int)$cdate - (int)$row['years'];
                            ?>
                                    <tr>
                                        <td class="text-center"><?= $count ?></td>
                                        <td><?= htmlspecialchars($row['fullname']) ?></td>
                                        <td><?= htmlspecialchars($row['age']) ?></td>
                                        <td><?= htmlspecialchars($row['purok']) ?></td>
                                        <td><?= htmlspecialchars($years . " years") ?></td>
                                        <td class="text-center" style="width: 150px;">
                                            <a href="functions/delete_residents.php?id=<?= $row['resident_id'] ?>" class="btn btn-danger btn-sm">Delete</a>
                                            <button class="btn btn-warning btn-sm" data-bs-toggle="modal" data-bs-target="#editModal<?= $row['resident_id'] ?>">Edit</button>
                                        </td>
                                    </tr>

                                    <!-- Edit Modal -->
                                    <div class="modal fade" id="editModal<?= $row['resident_id'] ?>" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
                                        <div class="modal-dialog">
                                            <div class="modal-content">
                                                <form action="" method="POST">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title" id="editModalLabel">Edit Resident</h5>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                    </div>
                                                    <div class="modal-body">
                                                        <input type="hidden" name="resident_id" value="<?= $row['resident_id'] ?>">
                                                        <div class="mb-3">
                                                            <label for="fullname" class="form-label">Fullname:</label>
                                                            <input type="text" class="form-control" name="fullname" value="<?= htmlspecialchars($row['fullname']) ?>" required>
                                                        </div>
                                                        <div class="mb-3">
                                                            <label for="age" class="form-label">Age:</label>
                                                            <input type="number" class="form-control" name="age" value="<?= $row['age'] ?>" required>
                                                        </div>
                                                        <div class="mb-3">
                                                            <label for="purok" class="form-label">Purok:</label>
                                                            <select type="text" class="form-control" name="purok" value="<?= htmlspecialchars($row['purok']) ?>" required>
                                                            <option value="" selected disabled hidden>Select...</option>
                                                            <option value="1">1</option>
                                                            <option value="2">2</option>
                                                            <option value="3A">3A</option>
                                                            <option value="3B">3B</option>
                                                            <option value="4">4</option>
                                                            <option value="5">5</option>
                                                            <option value="6">6</option>
                                                            <option value="7">7</option>
                                                         </select>
                                                        </div>
                                                        <div class="mb-3">
                                                            <label for="years" class="form-label">Year:</label>
                                                            <input type="month" class="form-control" name="years" value="<?= htmlspecialchars($row['years']) ?>" required>
                                                        </div>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                                        <button type="submit" name="edit_resident" class="btn btn-primary">Save Changes</button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>

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

                <!-- Add Resident Modal -->
                <form action="" method="post">
                    <div class="modal fade" id="staticBackdrop" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h1 class="modal-title fs-5" id="staticBackdropLabel">Add Resident</h1>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    <label for="" class="form-label">Fullname:</label>
                                    <input type="text" class="form-control" placeholder="Enter fullname" name="fullname" required>
                                    <label for="" class="form-label">Age:</label>
                                    <input type="number" class="form-control" placeholder="Enter age" name="age" required>
                                    <label for="purok" class="form-label">Purok:</label>
                                                            <select type="text" class="form-control" name="purok" value="<?= htmlspecialchars($row['purok']) ?>" required>
                                                            <option value="" selected disabled hidden>Select...</option>
                                                            <option value="1">1</option>
                                                            <option value="2">2</option>
                                                            <option value="3A">3A</option>
                                                            <option value="3B">3B</option>
                                                            <option value="4">4</option>
                                                            <option value="5">5</option>
                                                            <option value="6">6</option>
                                                            <option value="7">7</option>
                                                         </select>
                                    <label for="" class="form-label">Year:</label>
                                    <input type="month" class="form-control" placeholder="Enter month start" name="years" required>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                    <button type="submit" name="submit" class="btn btn-primary">Submit</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const openbtn = document.getElementById('btn-open');
            const sidebar = document.getElementById('side-nav');

            openbtn.addEventListener('click', () => {
                sidebar.classList.toggle('active');
            });
        });
    </script>
</body>

</html>
