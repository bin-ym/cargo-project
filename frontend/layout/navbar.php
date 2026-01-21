<?php
require_once __DIR__ . '/../../backend/config/languages.php';
?>
<style>
    /* Navbar with gradient logo like Register page */
    nav {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 20px 5%;
        background: white;
        box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        position: fixed;
        width: 100%;
        top: 0;
        z-index: 100;
    }

    .logo {
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .logo-square {
        width: 50px;
        height: 50px;
        background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
        color: white;
        font-size: 20px;
        font-weight: 800;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 16px;
        box-shadow: 0 10px 15px -3px rgba(37, 99, 235, 0.3);
    }

    .logo-text {
        font-size: 1.5rem;
        font-weight: 800;
        color: var(--secondary);
    }

    .nav-links {
        display: flex;
        gap: 30px;
    }

    .nav-links a {
        text-decoration: none;
        color: #64748b;
        font-weight: 500;
        transition: 0.3s;
    }

    .nav-links a:hover {
        color: var(--primary);
    }

    .btn {
        padding: 10px 24px;
        border-radius: 8px;
        text-decoration: none;
        font-weight: 600;
        transition: 0.3s;
    }

    .btn-primary {
        background: var(--primary);
        color: white;
    }

    .btn-primary:hover {
        background: var(--primary-dark);
    }

    .btn-outline {
        border: 2px solid var(--primary);
        color: var(--primary);
    }

    .btn-outline:hover {
        background: var(--primary);
        color: white;
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
        <div class="logo-square">CT</div>
        <div class="logo-text">CargoConnect</div>
    </div>
    
    <?php if (!isset($simpleNavbar) || !$simpleNavbar): ?>
    <div class="nav-links">
        <a href="/cargo-project/index.php#features"><?= __('features') ?></a>
        <a href="/cargo-project/index.php#how-it-works"><?= __('how_it_works') ?></a>
        <a href="/cargo-project/frontend/public/about.php"><?= __('about') ?></a>
        <a href="/cargo-project/frontend/public/contact.php"><?= __('contact') ?></a>
    </div>
    <div style="display: flex; gap: 15px; align-items: center;">
        <div class="lang-switcher">
            <button class="lang-btn <?= $_SESSION['lang'] == 'en' ? 'active' : '' ?>" onclick="setLanguage('en')">EN</button>
            <button class="lang-btn <?= $_SESSION['lang'] == 'am' ? 'active' : '' ?>" onclick="setLanguage('am')">አማ</button>
        </div>
        <a href="/cargo-project/frontend/auth/login.php" class="btn btn-outline"><?= __('login') ?></a>
        <a href="/cargo-project/frontend/auth/register.php" class="btn btn-primary"><?= __('get_started') ?></a>
    </div>
    <?php else: ?>
    <div style="display: flex; gap: 15px; align-items: center;">
        <div class="lang-switcher">
            <button class="lang-btn <?= $_SESSION['lang'] == 'en' ? 'active' : '' ?>" onclick="setLanguage('en')">EN</button>
            <button class="lang-btn <?= $_SESSION['lang'] == 'am' ? 'active' : '' ?>" onclick="setLanguage('am')">አማ</button>
        </div>
        <a href="/cargo-project/index.php" class="btn btn-outline" style="border:none; color:#64748b;">&larr; <?= __('back_to_home') ?></a>
    </div>
    <?php endif; ?>
</nav>

<script>
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
