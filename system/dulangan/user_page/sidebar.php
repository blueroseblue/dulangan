<div class="sidebar bg-darkblue p-3" id="side-nav">
    <div class="header-box d-flex justify-content-between align-items-center">
    <h1 class="fs-4 m-0"><img src="../image/logo.png" style="width: 30px; margin-left: 12px;" alt=""><span class="text-white" style= "margin-left: 5px;">Dulangan</span></h1>
    </div>
    <hr class="text-white">
    <ul class="list-unstyled">
        <li class=<?php if($active == "home"){echo "active";} ?>>
            <a href="home.php" class="text-decoration-none px-3 py-3 d-block">
                <span><i class="me-2 fa-solid fa-house"></i>Dashboard</span>
            </a>
        </li>
        </li>
        <li class=<?php if($active == "certificate_form"){echo "active";} ?>>
            <a href="certificate_form.php" class="text-decoration-none px-3 py-3 d-flex justify-content-between">
                <span><i class="me-2 fa-solid fa-envelope-open-text"></i>Request Certificate</span>
            </a>
        </li>
        <li class=<?php if($active == "resources_form"){echo "active";} ?>>
            <a href="resources_form.php" class="text-decoration-none px-3 py-3 d-flex justify-content-between">
                <span><i class="me-2 fa-solid fa-screwdriver-wrench"></i>Request Resources</span>
            </a>
        </li>
        <li class=<?php if($active == "allrequest"){echo "active";} ?>>
            <a href="allrequest.php" class="text-decoration-none px-3 py-3 d-flex justify-content-between">
                <span><i class="me-2 fa-solid fa-list"></i>All Requests</span>
            </a>
       
    </ul>
</div>
