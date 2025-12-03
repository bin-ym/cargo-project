<?php
$currentPage = basename($_SERVER['PHP_SELF']);
?>

<aside class="sidebar">
    <div class="sidebar-header">
        <div class="logo">CT</div>
        <h3>Transporter</h3>
    </div>

    <nav>
        <a href="dashboard.php" class="menu-item <?= $currentPage == 'dashboard.php' ? 'active' : '' ?>">
            <i data-feather="home"></i> Dashboard
        </a>

        <a href="assignments.php" class="menu-item <?= $currentPage == 'assignments.php' ? 'active' : '' ?>">
            <i data-feather="truck"></i> My Assignments
        </a>

        <a href="active_deliveries.php" class="menu-item <?= $currentPage == 'active_deliveries.php' ? 'active' : '' ?>">
            <i data-feather="navigation"></i> Active Deliveries
        </a>

        <a href="delivery_history.php" class="menu-item <?= $currentPage == 'delivery_history.php' ? 'active' : '' ?>">
            <i data-feather="clock"></i> History
        </a>

        <a href="earnings.php" class="menu-item <?= $currentPage == 'earnings.php' ? 'active' : '' ?>">
            <i data-feather="dollar-sign"></i> Earnings
        </a>

        <a href="profile.php" class="menu-item <?= $currentPage == 'profile.php' ? 'active' : '' ?>">
            <i data-feather="user"></i> Profile
        </a>

        <a href="/cargo-project/backend/api/auth/logout.php" class="menu-item logout">
            <i data-feather="log-out"></i> Logout
        </a>
    </nav>
</aside>

<script>
feather.replace();
</script>