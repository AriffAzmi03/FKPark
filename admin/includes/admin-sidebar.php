<div id="layoutSidenav_nav">
    <nav class="sb-sidenav accordion sb-sidenav-dark" id="sidenavAccordion">
        <div class="sb-sidenav-menu">
            <div class="nav">

                <!-- Main Section -->
                <div class="sb-sidenav-menu-heading">Main</div>
                <a class="nav-link" href="admin-dashboard.php">
                    <div class="sb-nav-link-icon"><i class="fa-solid fa-table-columns"></i></div>
                    Dashboard
                </a>
                
                <!-- Features Section -->
                <div class="sb-sidenav-menu-heading">Features</div>

                <!-- User -->
                <a class="nav-link collapsed" href="#" data-bs-toggle="collapse" data-bs-target="#collapseUser" aria-expanded="false" aria-controls="collapseUser">
                    <div class="sb-nav-link-icon"><i class="fa-solid fa-users"></i></div>
                    User
                    <div class="sb-sidenav-collapse-arrow"><i class="fas fa-angle-down"></i></div>
                </a>
                <div class="collapse" id="collapseUser" aria-labelledby="headingUser" data-bs-parent="#sidenavAccordion">
                    <nav class="sb-sidenav-menu-nested nav">
                        <a class="nav-link" href="admin-add-user.php">User Registration</a>
                        <a class="nav-link" href="admin-manage-user.php">Manage User</a>
                    </nav>
                </div>

                <!-- Vehicle -->
                <a class="nav-link collapsed" href="#" data-bs-toggle="collapse" data-bs-target="#collapseVehicle" aria-expanded="false" aria-controls="collapseVehicle">
                    <div class="sb-nav-link-icon"><i class="fa-solid fa-car"></i></i></div>
                    Vehicle
                    <div class="sb-sidenav-collapse-arrow"><i class="fas fa-angle-down"></i></div>
                </a>
                <div class="collapse" id="collapseVehicle" aria-labelledby="headingVehicle" data-bs-parent="#sidenavAccordion">
                    <nav class="sb-sidenav-menu-nested nav">
                        <a class="nav-link" href="admin-add-vehicle.php">Vehicle Registration</a>
                        <a class="nav-link" href="admin-manage-vehicle.php">Manage Vehicle</a>
                    </nav>
                </div>

                <!-- Parking Space -->
                <a class="nav-link collapsed" href="#" data-bs-toggle="collapse" data-bs-target="#collapseParking" aria-expanded="false" aria-controls="collapseParking">
                    <div class="sb-nav-link-icon"><i class="fa-solid fa-square-parking"></i></div>
                    Parking Space
                    <div class="sb-sidenav-collapse-arrow"><i class="fas fa-angle-down"></i></div>
                </a>
                <div class="collapse" id="collapseParking" aria-labelledby="headingParking" data-bs-parent="#sidenavAccordion">
                    <nav class="sb-sidenav-menu-nested nav">
                        <a class="nav-link" href="admin-create-park.php">Add Parking Space</a>
                        <a class="nav-link" href="admin-manage-area.php">Manage Parking Area</a>
                    </nav>
                </div>

                <!-- Booking -->
                <a class="nav-link collapsed" href="#" data-bs-toggle="collapse" data-bs-target="#collapseBooking" aria-expanded="false" aria-controls="collapseBooking">
                    <div class="sb-nav-link-icon"><i class="fa-solid fa-tags"></i></div>
                    Booking
                    <div class="sb-sidenav-collapse-arrow"><i class="fas fa-angle-down"></i></div>
                </a>
                <div class="collapse" id="collapseBooking" aria-labelledby="headingBooking" data-bs-parent="#sidenavAccordion">
                    <nav class="sb-sidenav-menu-nested nav">
                        <a class="nav-link" href="admin-manage-orders.php">Book Parking</a>
                    </nav>
                </div>

                <!-- Summon -->
                <a class="nav-link collapsed" href="#" data-bs-toggle="collapse" data-bs-target="#collapseSummon" aria-expanded="false" aria-controls="collapseSummon">
                    <div class="sb-nav-link-icon"><i class="fa-solid fa-tags"></i></div>
                    Summon
                    <div class="sb-sidenav-collapse-arrow"><i class="fas fa-angle-down"></i></div>
                </a>
                <div class="collapse" id="collapseSummon" aria-labelledby="headingSummon" data-bs-parent="#sidenavAccordion">
                    <nav class="sb-sidenav-menu-nested nav">
                        <a class="nav-link" href="admin-manage-summon.php">Manage Summon</a>
                    </nav>
                </div>

            </div>
        </div>
        
        <!-- Sidenav Footer -->
        <div class="sb-sidenav-footer">
            <div class="small">Logged in as:</div>
            <p>Admin</p>
        </div>
    </nav>
</div>

