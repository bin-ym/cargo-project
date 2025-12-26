<?php
$currentPage = basename($_SERVER['PHP_SELF']);

// Define which pages belong to which group
$requestsPages = ['requests.php', 'approved.php', 'pending.php', 'view_request.php'];
$managementPages = ['customers.php', 'customer_view.php', 'transporters.php', 'transporter_view.php', 'cargo_items.php', 'order_items.php'];

$isRequestsOpen = in_array($currentPage, $requestsPages) ? 'open' : '';
$isManagementOpen = in_array($currentPage, $managementPages) ? 'open' : '';
?>

<aside class="sidebar">
    <div class="sidebar-header">
        <div class="logo">CT</div>
        <h3>Admin</h3>
    </div>

    <nav>

        <a href="dashboard.php" class="menu-item <?= $currentPage == 'dashboard.php' ? 'active' : '' ?>">
            <i data-feather="home"></i> Dashboard
        </a>
        
        <!-- Users -->
        <div class="menu-group <?= $isManagementOpen ?>">
            <button class="menu-dropdown">
                <i data-feather="users"></i> Users
                <span class="arrow">▾</span>
            </button>

            <div class="submenu">
                <a href="customers.php" class="menu-item <?= $currentPage == 'customers.php' ? 'active' : '' ?>">Customers</a>
                <a href="transporters.php" class="menu-item <?= $currentPage == 'transporters.php' ? 'active' : '' ?>">Transporters</a>
                <!-- <a href="cargo_items.php" class="menu-item <?= $currentPage == 'cargo_items.php' ? 'active' : '' ?>">Cargo Items</a> -->
                <!-- <a href="order_items.php" class="menu-item <?= $currentPage == 'order_items.php' ? 'active' : '' ?>">Order Items</a> -->
            </div>
        </div>

        <!-- Requests Dropdown -->
        <div class="menu-group <?= $isRequestsOpen ?>">
            <!-- <button class="menu-dropdown">
                <i data-feather="file-text"></i> Requests
                <span class="arrow">▾</span>
            </button> -->
            
            <!-- <div class="submenu"> -->
                <a href="requests.php" class="menu-item <?= $currentPage == 'requests.php' ? 'active' : '' ?>">
                    <i data-feather="package"></i> Requests
                </a>
                <!-- <a href="approved.php" class="menu-item <?= $currentPage == 'approved.php' ? 'active' : '' ?>">Approved</a> -->
                <!-- <a href="pending.php" class="menu-item <?= $currentPage == 'pending.php' ? 'active' : '' ?>">Pending</a> -->
            <!-- </div> -->
        </div>

        <a href="earnings.php" class="menu-item <?= $currentPage == 'earnings.php' ? 'active' : '' ?>">
            <i data-feather="dollar-sign"></i> Earnings
        </a>

        <a href="vehicles.php" class="menu-item <?= $currentPage == 'vehicles.php' ? 'active' : '' ?>">
            <i data-feather="truck"></i> Vehicles
        </a>



        <a href="/cargo-project/backend/api/auth/logout.php" class="menu-item logout">
            <i data-feather="log-out"></i> Logout
        </a>

    </nav>
</aside>

<script>
document.querySelectorAll(".menu-dropdown").forEach(btn => {
    btn.addEventListener("click", () => {
        btn.parentElement.classList.toggle("open");
    });
});

feather.replace();
</script>