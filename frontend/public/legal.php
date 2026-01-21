<?php
require_once __DIR__ . '/../../backend/config/languages.php';
?>
<!DOCTYPE html>
<html lang="<?= $_SESSION['lang'] ?? 'en' ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= __('legal') ?> - <?= __('app_name') ?></title>
    <script src="https://unpkg.com/feather-icons"></script>
    <link rel="stylesheet" href="../css/public.css">
</head>
<body>
<?php require_once __DIR__ . '/../layout/navbar.php'; ?>

<section class="page-header">
    <h1><?= __('legal') ?></h1>
    <p><?= __('legal_desc') ?></p>
</section>

<section class="content-section">
    <div class="container">
        <div class="legal-links">
            <ul>
                <li><a href="privacy_policy.php"><?= __('privacy_policy') ?></a></li>
                <li><a href="terms_of_service.php"><?= __('terms_of_service') ?></a></li>
            </ul>
        </div>
    </div>
</section>

<?php require_once __DIR__ . '/../layout/footer.php'; ?>
</body>
</html>
