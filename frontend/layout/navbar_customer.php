<?php
require_once __DIR__ . '/../../backend/config/languages.php';
?>
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

.logo-link {
    display: flex;
    align-items: center;
    gap: 10px;
    text-decoration: none;
    color: var(--secondary);
    font-weight: 800;
    font-size: 1.5rem;
}

.logo-link img {
    width: 80px;
    height: 80px;
    object-fit: contain;
    border-radius: 8px;
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

/* Language Switcher */
.lang-switcher {
    display: flex;
    align-items: center;
    gap: 8px;
    margin-right: 15px;
}
.lang-btn {
    padding: 4px 8px;
    border: 1px solid #e2e8f0;
    border-radius: 6px;
    background: white;
    cursor: pointer;
    font-size: 0.85rem;
    font-weight: 600;
    color: #64748b;
    transition: all 0.2s;
}
.lang-btn.active {
    background: var(--primary);
    color: white;
    border-color: var(--primary);
}
</style>

<nav>
    <div class="logo">
        <a href="/cargo-project/frontend/customer/dashboard.php" class="logo-link">
            <img src="/cargo-project/frontend/public/logo.jpg" alt="CargoConnect Logo">
            <span>CargoConnect</span>
        </a>
    </div>

    <div class="nav-links">
        <a href="/cargo-project/frontend/customer/new_request.php"
           class="<?= basename($_SERVER['PHP_SELF']) == 'new_request.php' ? 'active' : '' ?>">
            <i data-feather="plus-circle" width="18"></i> <?= __('new_request') ?>
        </a>

        <a href="/cargo-project/frontend/customer/my_requests.php"
           class="<?= basename($_SERVER['PHP_SELF']) == 'my_requests.php' ? 'active' : '' ?>">
            <i data-feather="list" width="18"></i> <?= __('my_requests') ?>
        </a>

        <a href="/cargo-project/frontend/customer/notifications.php"
           class="<?= basename($_SERVER['PHP_SELF']) == 'notifications.php' ? 'active' : '' ?>">
            <i data-feather="bell" width="18"></i>
            <?= __('notifications') ?>
            <span id="notificationCount" class="notification-badge"></span>
        </a>

        <a href="/cargo-project/frontend/customer/profile.php"
           class="<?= basename($_SERVER['PHP_SELF']) == 'profile.php' ? 'active' : '' ?>">
            <i data-feather="user" width="18"></i> <?= __('profile') ?>
        </a>
    </div>

    <div class="user-menu">
        <div class="lang-switcher">
            <button class="lang-btn <?= $_SESSION['lang'] == 'en' ? 'active' : '' ?>" onclick="setLanguage('en')">EN</button>
            <button class="lang-btn <?= $_SESSION['lang'] == 'am' ? 'active' : '' ?>" onclick="setLanguage('am')">አማ</button>
        </div>
        <span style="font-weight:600; color:var(--secondary);">
            <?= __('hi') ?>, <?= htmlspecialchars($_SESSION['username'] ?? 'Customer'); ?>
        </span>
        <a href="/cargo-project/backend/api/auth/logout.php" class="btn-logout">
            <?= __('logout') ?>
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

async function setLanguage(lang) {
    try {
        const res = await fetch('/cargo-project/backend/api/auth/set_language.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ lang })
        });
        const data = await res.json();
        if (data.success) {
            window.location.reload();
        }
    } catch (err) {
        console.error('Failed to set language:', err);
    }
}
</script>