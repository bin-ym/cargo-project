<?php
$currentPage = basename($_SERVER['PHP_SELF']);
?>

<aside class="sidebar">
    <div class="sidebar-header">
        <div class="logo">CT</div>
        <h3>Customer</h3>
    </div>

    <nav>
        <a href="dashboard.php" class="menu-item <?= $currentPage == 'dashboard.php' ? 'active' : '' ?>">
            <i data-feather="home"></i> Dashboard
        </a>

        <a href="my_requests.php" class="menu-item <?= $currentPage == 'my_requests.php' ? 'active' : '' ?>">
            <i data-feather="package"></i> My Requests
        </a>

        <a href="new_request.php" class="menu-item <?= $currentPage == 'new_request.php' ? 'active' : '' ?>">
            <i data-feather="plus-circle"></i> New Request
        </a>

        <a href="track_shipment.php" class="menu-item <?= $currentPage == 'track_shipment.php' ? 'active' : '' ?>">
            <i data-feather="map-pin"></i> Track Shipment
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