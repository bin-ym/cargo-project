<?php
require_once __DIR__ . '/../../backend/config/languages.php';
?>
<!DOCTYPE html>
<html lang="<?= $_SESSION['lang'] ?? 'en' ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= __('privacy_policy') ?> - <?= __('app_name') ?></title>
    <script src="https://unpkg.com/feather-icons"></script>
    <link rel="stylesheet" href="../css/public.css">
    <style>
        .policy-section { margin-bottom: 30px; }
        .policy-section h2 { color: #1e293b; font-size: 1.5rem; margin-bottom: 15px; border-bottom: 2px solid #e2e8f0; padding-bottom: 10px; }
        .policy-section p { line-height: 1.7; color: #475569; margin-bottom: 15px; }
        .policy-section ul { padding-left: 20px; color: #475569; margin-bottom: 15px; }
        .policy-section li { margin-bottom: 8px; line-height: 1.6; }
    </style>
</head>
<body>
<?php require_once __DIR__ . '/../layout/navbar.php'; ?>

<section class="page-header">
    <h1><?= __('privacy_policy') ?></h1>
    <p><?= __('last_updated') ?>: <?= date('F d, Y') ?></p>
</section>

<section class="content-section">
    <div class="container" style="max-width: 800px;">
        <div class="policy-section">
            <h2>1. <?= __('pp_intro_title') ?></h2>
            <p><?= __('pp_intro_text') ?></p>
        </div>

        <div class="policy-section">
            <h2>2. <?= __('pp_info_collect_title') ?></h2>
            <p><?= __('pp_info_collect_text') ?></p>
            <ul>
                <li><?= __('pp_collect_personal') ?></li>
                <li><?= __('pp_collect_shipment') ?></li>
                <li><?= __('pp_collect_location') ?></li>
                <li><?= __('pp_collect_payment') ?></li>
            </ul>
        </div>

        <div class="policy-section">
            <h2>3. <?= __('pp_usage_title') ?></h2>
            <p><?= __('pp_usage_text') ?></p>
            <ul>
                <li><?= __('pp_usage_service') ?></li>
                <li><?= __('pp_usage_tracking') ?></li>
                <li><?= __('pp_usage_comm') ?></li>
            </ul>
        </div>

        <div class="policy-section">
            <h2>4. <?= __('pp_sharing_title') ?></h2>
            <p><?= __('pp_sharing_text') ?></p>
        </div>

        <div class="policy-section">
            <h2>5. <?= __('pp_security_title') ?></h2>
            <p><?= __('pp_security_text') ?></p>
        </div>
    </div>
</section>

<?php require_once __DIR__ . '/../layout/footer.php'; ?>
</body>
</html>
