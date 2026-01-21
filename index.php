<?php
// File: C:\xampp\htdocs\cargo-project\index.php
session_start();

// If logged in, redirect to dashboard
if (isset($_SESSION['user_id']) && isset($_SESSION['role'])) {
    $redirects = [
        'customer'    => '/cargo-project/frontend/customer/dashboard.php',
        'transporter' => '/cargo-project/frontend/transporter/dashboard.php',
        'admin'       => '/cargo-project/frontend/admin/dashboard.php',
    ];
    if (isset($redirects[$_SESSION['role']])) {
        header("Location: " . $redirects[$_SESSION['role']]);
        exit();
    }
}
require_once __DIR__ . '/backend/config/languages.php';
?>
<!DOCTYPE html>
<html lang="<?= $_SESSION['lang'] ?? 'en' ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= __('app_title_home') ?></title>
    <script src="https://unpkg.com/feather-icons"></script>
    <link rel="stylesheet" href="frontend/css/public.css">
</head>
<body>
<!-- Navbar -->
<?php require_once __DIR__ . '/frontend/layout/navbar.php'; ?>

    <section class="hero">
        <div class="hero-content">
            <h1><?= __('hero_title') ?></h1>
            <p><?= __('hero_subtitle') ?></p>
            <div style="display: flex; gap: 15px; flex-wrap: wrap;">
                <a href="frontend/auth/register.php" class="btn btn-primary"><?= __('start_shipping') ?></a>
                <a href="#features" class="btn btn-outline"><?= __('learn_more') ?></a>
            </div>
        </div>
        <div class="hero-image">
            <img src="https://images.unsplash.com/photo-1586528116311-ad8dd3c8310d?ixlib=rb-4.0.3&auto=format&fit=crop&w=1000&q=80" alt="<?= __('dashboard') ?>">
        </div>
    </section>

    <section class="features" id="features">
        <div class="section-header">
            <h2><?= __('why_choose_us') ?></h2>
            <p><?= __('features_subtitle') ?></p>
        </div>
        <div class="grid">
            <div class="feature-card">
                <div class="icon-box"><i data-feather="map"></i></div>
                <h3><?= __('gps_tracking') ?></h3>
                <p><?= __('gps_tracking_desc') ?></p>
            </div>
            <div class="feature-card">
                <div class="icon-box"><i data-feather="shield"></i></div>
                <h3><?= __('secure_payments') ?></h3>
                <p><?= __('secure_payments_desc') ?></p>
            </div>
            <div class="feature-card">
                <div class="icon-box"><i data-feather="truck"></i></div>
                <h3><?= __('verified_transporters') ?></h3>
                <p><?= __('verified_transporters_desc') ?></p>
            </div>
        </div>
    </section>

    <?php require_once __DIR__ . '/frontend/layout/footer.php'; ?>

    <script>
        feather.replace();
    </script>
</body>
</html>