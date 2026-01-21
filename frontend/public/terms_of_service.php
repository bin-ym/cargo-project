<?php
require_once __DIR__ . '/../../backend/config/languages.php';
?>
<!DOCTYPE html>
<html lang="<?= $_SESSION['lang'] ?? 'en' ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= __('terms_of_service') ?> - <?= __('app_name') ?></title>
    <script src="https://unpkg.com/feather-icons"></script>
    <link rel="stylesheet" href="../css/public.css">
    <style>
        .terms-section { margin-bottom: 30px; }
        .terms-section h2 { color: #1e293b; font-size: 1.5rem; margin-bottom: 15px; border-bottom: 2px solid #e2e8f0; padding-bottom: 10px; }
        .terms-section p { line-height: 1.7; color: #475569; margin-bottom: 15px; }
        .terms-section ul { padding-left: 20px; color: #475569; margin-bottom: 15px; }
        .terms-section li { margin-bottom: 8px; line-height: 1.6; }
    </style>
</head>
<body>
<?php require_once __DIR__ . '/../layout/navbar.php'; ?>

<section class="page-header">
    <h1><?= __('terms_of_service') ?></h1>
    <p><?= __('last_updated') ?>: <?= date('F d, Y') ?></p>
</section>

<section class="content-section">
    <div class="container" style="max-width: 800px;">
        <div class="terms-section">
            <h2>1. <?= __('tos_acceptance_title') ?></h2>
            <p><?= __('tos_acceptance_text') ?></p>
        </div>

        <div class="terms-section">
            <h2>2. <?= __('tos_service_title') ?></h2>
            <p><?= __('tos_service_text') ?></p>
        </div>

        <div class="terms-section">
            <h2>3. <?= __('tos_user_resp_title') ?></h2>
            <p><?= __('tos_user_resp_text') ?></p>
            <ul>
                <li><?= __('tos_resp_accuracy') ?></li>
                <li><?= __('tos_resp_prohibited') ?></li>
                <li><?= __('tos_resp_security') ?></li>
            </ul>
        </div>

        <div class="terms-section">
            <h2>4. <?= __('tos_payment_title') ?></h2>
            <p><?= __('tos_payment_text') ?></p>
        </div>

        <div class="terms-section">
            <h2>5. <?= __('tos_liability_title') ?></h2>
            <p><?= __('tos_liability_text') ?></p>
        </div>

        <div class="terms-section">
            <h2>6. <?= __('tos_governing_law_title') ?></h2>
            <p><?= __('tos_governing_law_text') ?></p>
        </div>
    </div>
</section>

<?php require_once __DIR__ . '/../layout/footer.php'; ?>
</body>
</html>
