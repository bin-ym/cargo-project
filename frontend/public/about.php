<?php
// frontend/public/about.php
session_start();
require_once __DIR__ . '/../../backend/config/languages.php';
?>
<!DOCTYPE html>
<html lang="<?= $_SESSION['lang'] ?? 'en' ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= __('About us') ?></title>
    <script src="https://unpkg.com/feather-icons"></script>
    <link rel="stylesheet" href="../css/public.css">
</head>
<body>

<?php require_once __DIR__ . '/../layout/navbar.php'; ?>

<!-- Hero / Intro -->
<section class="hero hero-small">
    <div class="hero-content">
        <h1><?= __('About us') ?></h1>
        <p>
            <?= __('about_desc') ?>
        </p>
    </div>
</section>

<!-- Mission Section -->
<section class="features">
    <div class="section-header">
        <h2><?= __('Our mission') ?></h2>
        <p>
            <?= __('mission_desc') ?>
        </p>
    </div>

    <div class="grid">
        <div class="feature-card">
            <div class="icon-box">
                <i data-feather="target"></i>
            </div>
            <h3><?= __('efficiency') ?></h3>
            <p>
                <?= __('efficiency_desc') ?>
            </p>
        </div>

        <div class="feature-card">
            <div class="icon-box">
                <i data-feather="users"></i>
            </div>
            <h3><?= __('trust') ?></h3>
            <p>
                <?= __('trust_desc') ?>
            </p>
        </div>

        <div class="feature-card">
            <div class="icon-box">
                <i data-feather="trending-up"></i>
            </div>
            <h3><?= __('innovation') ?></h3>
            <p>
                <?= __('innovation_desc') ?>
            </p>
        </div>
    </div>
</section>

<!-- Vision Section -->
<section class="content-section">
    <div class="container vision-section">
        <h2><?= __('our_vision') ?></h2>
        <p>
            <?= __('vision_desc') ?>
        </p>
    </div>
</section>

<?php require_once __DIR__ . '/../layout/footer.php'; ?>

<script>
    feather.replace();
</script>

</body>
</html>