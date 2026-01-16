<aside class="sidebar" id="sidebar">
    <div class="sidebar-header">
        <div class="logo">CT</div>
        <h3>Admin</h3>
    </div>
    <nav>
        <?php
        $currentPage = basename($_SERVER['PHP_SELF']);
        $requestsPages = ['requests.php', 'approved.php', 'pending.php'];
        $managementPages = ['customers.php', 'transporters.php', 'cargo_items.php'];

        $isRequestsOpen = in_array($currentPage, $requestsPages) ? 'open' : '';
        $isManagementOpen = in_array($currentPage, $managementPages) ? 'open' : '';
        ?>

        <a href="dashboard.php" class="menu-item <?= $currentPage=='dashboard.php'?'active':''?>">
            <i data-feather="home"></i> Dashboard
        </a>

        <div class="menu-group <?= $isManagementOpen ?>">
            <button class="menu-dropdown">
                <i data-feather="users"></i> Users
                <span class="arrow">▾</span>
            </button>
            <div class="submenu">
                <a href="customers.php" class="menu-item <?= $currentPage=='customers.php'?'active':'' ?>">Customers</a>
                <a href="transporters.php" class="menu-item <?= $currentPage=='transporters.php'?'active':'' ?>">Transporters</a>
            </div>
        </div>

        <div class="menu-group <?= $isRequestsOpen ?>">
            <a href="requests.php" class="menu-item <?= $currentPage=='requests.php'?'active':'' ?>">
                <i data-feather="package"></i> Requests
            </a>
        </div>

        <a href="earnings.php" class="menu-item <?= $currentPage=='earnings.php'?'active':'' ?>">
            <i data-feather="dollar-sign"></i> Earnings
        </a>

        <div class="menu-group">
            <a href="vehicles.php" class="menu-item <?= $currentPage=='vehicles.php'?'active':'' ?>">
                <i data-feather="truck"></i> Vehicles
            </a>
        </div>

        <a href="/cargo-project/backend/api/auth/logout.php" class="menu-item logout">
            <i data-feather="log-out"></i> Logout
        </a>
    </nav>
</aside>
