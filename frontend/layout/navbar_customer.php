<style>
/* ===== Navbar ===== */
nav {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 15px 5%;
    background: white;
    box-shadow: 0 1px 3px rgba(0,0,0,0.1);
    position: fixed;
    width: 100%;
    top: 0;
    z-index: 100;
    box-sizing: border-box;
}

.logo {
    font-size: 1.5rem;
    font-weight: 800;
    color: var(--secondary);
    display: flex;
    align-items: center;
    gap: 8px;
}

.nav-links {
    display: flex;
    gap: 20px;
    align-items: center;
}

.nav-links a {
    text-decoration: none;
    color: #64748b;
    font-weight: 500;
    transition: 0.2s;
    display: flex;
    align-items: center;
    gap: 6px;
    position: relative;
}

.nav-links a:hover,
.nav-links a.active {
    color: var(--primary);
}

/* Notification badge */
.notification-badge {
    display: none;
    position: absolute;
    top: -6px;
    right: -10px;
    background: #dc2626;
    color: white;
    font-size: 11px;
    font-weight: 700;
    padding: 2px 7px;
    border-radius: 999px;
    line-height: 1;
    min-width: 18px;
    text-align: center;
    box-shadow: 0 0 0 2px white;
}

/* User menu */
.user-menu {
    display: flex;
    align-items: center;
    gap: 15px;
}

.btn-logout {
    padding: 8px 16px;
    border: 1px solid #e2e8f0;
    border-radius: 8px;
    color: #ef4444;
    font-weight: 600;
    text-decoration: none;
    transition: all 0.2s;
}

.btn-logout:hover {
    background: #fee2e2;
    border-color: #fecaca;
}
</style>

<nav>
    <div class="logo">
        <a href="/cargo-project/frontend/customer/dashboard.php" class="logo">
            <i data-feather="box"></i> CargoConnect
        </a>
    </div>

    <div class="nav-links">
        <a href="/cargo-project/frontend/customer/new_request.php"
           class="<?= basename($_SERVER['PHP_SELF']) == 'new_request.php' ? 'active' : '' ?>">
            <i data-feather="plus-circle" width="18"></i> New Request
        </a>

        <a href="/cargo-project/frontend/customer/my_requests.php"
           class="<?= basename($_SERVER['PHP_SELF']) == 'my_requests.php' ? 'active' : '' ?>">
            <i data-feather="list" width="18"></i> My Requests
        </a>

        <a href="/cargo-project/frontend/customer/notifications.php"
           class="<?= basename($_SERVER['PHP_SELF']) == 'notifications.php' ? 'active' : '' ?>">
            <i data-feather="bell" width="18"></i>
            Notifications
            <span id="notificationCount" class="notification-badge"></span>
        </a>

        <a href="/cargo-project/frontend/customer/profile.php"
           class="<?= basename($_SERVER['PHP_SELF']) == 'profile.php' ? 'active' : '' ?>">
            <i data-feather="user" width="18"></i> Profile
        </a>
    </div>

    <div class="user-menu">
        <span style="font-weight:600; color:var(--secondary);">
            Hi, <?= htmlspecialchars($_SESSION['username'] ?? 'Customer'); ?>
        </span>
        <a href="/cargo-project/backend/api/auth/logout.php" class="btn-logout">
            Logout
        </a>
    </div>
</nav>

<!-- Spacer for fixed navbar -->
<div style="height:80px;"></div>

<script>
feather.replace();

/* ===== Notification badge updater ===== */
async function updateNotificationBadge() {
    try {
        const res = await fetch('/cargo-project/backend/api/customer/notifications.php');
        const json = await res.json();

        if (!json.success) return;

        const unread = json.data.filter(n => n.is_read == 0).length;
        const badge = document.getElementById('notificationCount');

        if (!badge) return;

        if (unread > 0) {
            badge.style.display = 'inline-block';
            badge.innerText = unread;
        } else {
            badge.style.display = 'none';
        }

    } catch (e) {
        console.error('Notification badge error:', e);
    }
}

updateNotificationBadge();
setInterval(updateNotificationBadge, 30000);
</script>