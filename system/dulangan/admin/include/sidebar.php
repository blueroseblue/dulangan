<div class="sidebar bg-darkblue p-3" id="side-nav">
    <div class="header-box d-flex justify-content-between align-items-center">
    <h1 class="fs-4 m-0"><img src="../image/logo.png" style="width: 30px; margin-left: 12px;" alt=""><span class="text-white" style= "margin-left: 5px;">Dulangan</span></h1>
    </div>
    <hr class="text-white">
    <ul class="list-unstyled">
        <li class=<?php if($active == "dashboard"){echo "active";} ?>>
            <a href="dashboard.php" class="text-decoration-none px-3 py-3 d-block">
                <span><i class="me-2 fa-solid fa-house"></i>Dashboard</span>
            </a>
        </li>
        <li class=<?php if($active == "residents"){echo "active";} ?>>
            <a href="accounts.php" class="text-decoration-none px-3 py-3 d-flex justify-content-between align-items-center">
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
        <li class=<?php if($active == "clearance"){echo "active";} ?>>
            <a href="clearance.php" class="text-decoration-none px-3 py-3 d-flex justify-content-between align-items-center">
                <span><i class="me-2 fa-solid fa-envelope-open-text"></i>Clearance</span>
                <?php
                if($crow > 0){
                    ?>
                    <span class="bg-danger text-white rounded-pill py-0 px-2 ms-2"><?=$cnotif?></span>
                    <?php
                }
                ?>
            </a>
        </li>
        <li class=<?php if($active == "resources"){echo "active";} ?>>
            <a href="resources.php" class="text-decoration-none px-3 py-3 d-flex justify-content-between">
                <span><i class="me-2 fa-solid fa-screwdriver-wrench"></i>Resources</span>
                <?php
                if($rrow > 0){
                    ?>
                    <span class="bg-danger text-white rounded-pill py-0 px-2 ms-2"><?=$rnotif?></span>
                    <?php
                }
                ?>
            </a>
        </li>
        <li class=<?php if($active == "all"){echo "active";} ?>>
            <a href="residents.php" class="text-decoration-none px-3 py-3 d-flex justify-content-between">
                <span><i class="me-2 fa-solid fa-users"></i>Residents</span>
            </a>
        </li>
    
        <li class=<?php if($active == "addresource"){echo "active";} ?>>
            <a href="addresource.php" class="text-decoration-none px-3 py-3 d-flex justify-content-between">
                <span><i class="me-2 fa-solid fa-screwdriver-wrench"></i>Add Resources</span>
            </a>
        </li>
        <li class=<?php if($active == "users"){echo "active";} ?>>
            <a href="users.php" class="text-decoration-none px-3 py-3 d-flex justify-content-between">
                <span><i class="me-2 fa-solid fa-users"></i>Users</span>
            </a>
    </ul>
</div>

