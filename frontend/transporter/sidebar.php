<?php
require_once __DIR__ . '/../../backend/config/languages.php';
$currentPage = basename($_SERVER['PHP_SELF']);
?>

<aside class="sidebar">
    <div class="sidebar-header">
    <div class="logo">
        <img src="/cargo-project/frontend/public/logo.jpg" alt="Logo">
    </div>
    <h3><?= __('transporter') ?></h3>
</div>

    <nav>
        <a href="dashboard.php" class="menu-item <?= $currentPage == 'dashboard.php' ? 'active' : '' ?>">
            <i data-feather="home"></i> <?= __('dashboard') ?>
        </a>

        <a href="assignments.php" class="menu-item <?= $currentPage == 'assignments.php' ? 'active' : '' ?>">
            <i data-feather="truck"></i> <?= __('my_assignments') ?>
        </a>

        <a href="active_deliveries.php" class="menu-item <?= $currentPage == 'active_deliveries.php' ? 'active' : '' ?>">
            <i data-feather="navigation"></i> <?= __('active_deliveries') ?>
        </a>

        <a href="delivery_history.php" class="menu-item <?= $currentPage == 'delivery_history.php' ? 'active' : '' ?>">
            <i data-feather="clock"></i> <?= __('history') ?>
        </a>

        <a href="earnings.php" class="menu-item <?= $currentPage == 'earnings.php' ? 'active' : '' ?>">
            <i data-feather="dollar-sign"></i> <?= __('earnings') ?>
        </a>

        <a href="profile.php" class="menu-item <?= $currentPage == 'profile.php' ? 'active' : '' ?>">
            <i data-feather="user"></i> <?= __('profile') ?>
        </a>

        <a href="/cargo-project/backend/api/auth/logout.php" class="menu-item logout">
            <i data-feather="log-out"></i> <?= __('logout') ?>
        </a>

        <!-- Language Switcher -->
        <div class="lang-switcher-sidebar" style="padding: 15px; margin-top: auto; border-top: 1px solid #e2e8f0;">
            <div style="display: flex; gap: 10px; justify-content: center;">
                <button class="lang-btn <?= $_SESSION['lang'] == 'en' ? 'active' : '' ?>" onclick="setLanguage('en')" style="padding: 5px 10px; border: 1px solid #cbd5e1; border-radius: 6px; background: <?= $_SESSION['lang'] == 'en' ? 'var(--primary)' : 'white' ?>; color: <?= $_SESSION['lang'] == 'en' ? 'white' : '#64748b' ?>; cursor: pointer; font-weight: 600;">EN</button>
                <button class="lang-btn <?= $_SESSION['lang'] == 'am' ? 'active' : '' ?>" onclick="setLanguage('am')" style="padding: 5px 10px; border: 1px solid #cbd5e1; border-radius: 6px; background: <?= $_SESSION['lang'] == 'am' ? 'var(--primary)' : 'white' ?>; color: <?= $_SESSION['lang'] == 'am' ? 'white' : '#64748b' ?>; cursor: pointer; font-weight: 600;">አማ</button>
            </div>
        </div>
    </nav>
</aside>

<script>
feather.replace();

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