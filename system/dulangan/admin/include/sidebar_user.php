<div class="sidebar bg-darkblue p-3" id="side-nav">
    <div class="header-box d-flex justify-content-between align-items-center">
        <h1 class="fs-4 m-0">
            <img src="../image/logo.png" style="width: 30px; margin-left: 12px;" alt="">
            <span class="text-white" style="margin-left: 5px;">Dulangan</span>
        </h1>
    </div>
    <hr class="text-white">

    <!-- Sidebar Menu Items -->
    <ul class="list-unstyled">
        <!-- Dashboard -->
        <li class="<?= $active == "dashboard_staff" ? "active" : "" ?>">
            <a href="dashboard_staff.php" class="text-decoration-none px-3 py-3 d-block">
                <span><i class="me-2 fa-solid fa-house"></i>Dashboard</span>
            </a>
        </li>
        <!-- Accounts (with Notification) -->
        <li class=<?php if($active == "accounts_staff"){echo "active";} ?>>
            <a href="accounts_staff.php" class="text-decoration-none px-3 py-3 d-flex justify-content-between align-items-center">
                <span><i class="me-2 fa-solid fa-users"></i>Registered Accounts</span>
                <?php
                if($arow > 0){
                    ?>
                <span class="bg-danger text-white rounded-pill py-0 px-2 ms-2"><?=$anotif?></span>
                <?php
            }
            ?>
        </a>
        </li>
        <!-- Clearance Requests (with Notification) -->
        <li class="<?= $active == "clearance_staff" ? "active" : "" ?>">
            <a href="clearance_staff.php" class="text-decoration-none px-3 py-3 d-flex justify-content-between">
                <span><i class="me-2 fa-solid fa-envelope-open-text"></i>Clearance</span>
                <?php if ($crow > 0): ?>
                    <span class="bg-danger text-white rounded-pill py-0 px-2"><?= $cnotif ?></span>
                <?php endif; ?>
            </a>
        </li>

        <!-- Resource Requests (with Notification) -->
        <li class="<?= $active == "resources_staff" ? "active" : "" ?>">
            <a href="resources_staff.php" class="text-decoration-none px-3 py-3 d-flex justify-content-between">
                <span><i class="me-2 fa-solid fa-screwdriver-wrench"></i>Resources</span>
                <?php if ($rrow > 0): ?>
                    <span class="bg-danger text-white rounded-pill py-0 px-2"><?= $rnotif ?></span>
                <?php endif; ?>
            </a>
        </li>

        <!-- All Residents -->
        <li class="<?= $active == "residents_staff" ? "active" : "" ?>">
            <a href="residents_staff.php" class="text-decoration-none px-3 py-3 d-flex justify-content-between">
                <span><i class="me-2 fa-solid fa-users"></i>Residents</span>
            </a>
        </li>

        <!-- Add Resources -->
        <li class="<?= $active == "addresource_staff" ? "active" : "" ?>">
            <a href="addresource_staff.php" class="text-decoration-none px-3 py-3 d-flex justify-content-between">
                <span><i class="me-2 fa-solid fa-screwdriver-wrench"></i>Add Resources</span>
            </a>
        </li>
    </ul>
</div>