<?php
require_once __DIR__ . '/../../backend/config/languages.php';
?>
<aside class="sidebar" id="sidebar">
    <div class="sidebar-header">
    <div class="logo">
        <img src="/cargo-project/frontend/public/logo.jpg" alt="Logo">
    </div>
    <h3><?= __('admin') ?></h3>
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
            <i data-feather="home"></i> <?= __('dashboard') ?>
        </a>

        <div class="menu-group <?= $isManagementOpen ?>">
            <button class="menu-dropdown">
                <i data-feather="users"></i> <?= __('users') ?>
                <span class="arrow">▾</span>
            </button>
            <div class="submenu">
                <a href="customers.php" class="menu-item <?= $currentPage=='customers.php'?'active':'' ?>"><?= __('customers') ?></a>
                <a href="transporters.php" class="menu-item <?= $currentPage=='transporters.php'?'active':'' ?>"><?= __('transporters') ?></a>
            </div>
        </div>

        <div class="menu-group <?= $isRequestsOpen ?>">
            <a href="requests.php" class="menu-item <?= $currentPage=='requests.php'?'active':'' ?>">
                <i data-feather="package"></i> <?= __('requests') ?>
            </a>
        </div>

        <a href="earnings.php" class="menu-item <?= $currentPage=='earnings.php'?'active':'' ?>">
            <i data-feather="dollar-sign"></i> <?= __('earnings') ?>
        </a>

        <div class="menu-group">
            <a href="vehicles.php" class="menu-item <?= $currentPage=='vehicles.php'?'active':'' ?>">
                <i data-feather="truck"></i> <?= __('vehicles') ?>
            </a>
        </div>

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
document.addEventListener('DOMContentLoaded', () => {
    const dropdowns = document.querySelectorAll('.menu-dropdown');
    
    dropdowns.forEach(btn => {
        btn.addEventListener('click', (e) => {
            const group = btn.parentElement;
            group.classList.toggle('open');
            
            // Close other dropdowns (optional, but good for UX)
            document.querySelectorAll('.menu-group').forEach(otherGroup => {
                if (otherGroup !== group) {
                    otherGroup.classList.remove('open');
                }
            });
        });
    });
});

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
