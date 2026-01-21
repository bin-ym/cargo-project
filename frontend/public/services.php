<?php
require_once __DIR__ . '/../../backend/config/languages.php';
?>
<!DOCTYPE html>
<html lang="<?= $_SESSION['lang'] ?? 'en' ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= __('services_title') ?> - <?= __('app_name') ?></title>
    <script src="https://unpkg.com/feather-icons"></script>
    <link rel="stylesheet" href="../css/public.css">
    <style>
        .service-section { padding: 60px 0; border-bottom: 1px solid #f1f5f9; }
        .service-section:last-child { border-bottom: none; }
        .service-header { text-align: center; margin-bottom: 40px; }
        .service-header h2 { font-size: 2rem; color: #1e293b; margin-bottom: 10px; }
        .service-header p { color: #64748b; font-size: 1.1rem; }
        .service-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 30px; }
        .service-card { background: #fff; padding: 30px; border-radius: 16px; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05); border: 1px solid #e2e8f0; transition: transform 0.3s ease; }
        .service-card:hover { transform: translateY(-5px); }
        .service-icon { width: 48px; height: 48px; background: #eff6ff; border-radius: 12px; display: flex; align-items: center; justify-content: center; color: #3b82f6; margin-bottom: 20px; }
        .service-card h3 { font-size: 1.25rem; color: #1e293b; margin-bottom: 15px; }
        .service-card ul { list-style: none; padding: 0; }
        .service-card li { margin-bottom: 10px; color: #475569; display: flex; align-items: flex-start; gap: 10px; }
        .service-card li i { color: #10b981; margin-top: 4px; }
        .pricing-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 20px; margin-top: 30px; }
        .pricing-item { background: #f8fafc; padding: 20px; border-radius: 12px; border: 1px solid #e2e8f0; text-align: center; }
        .pricing-item h4 { color: #64748b; margin-bottom: 10px; font-size: 0.9rem; text-transform: uppercase; }
        .pricing-item p { font-size: 1.1rem; font-weight: 600; color: #1e293b; }
    </style>
</head>
<body>
<?php require_once __DIR__ . '/../layout/navbar.php'; ?>

<section class="page-header">
    <h1><?= __('services_title') ?></h1>
    <p><?= __('services_subtitle') ?></p>
</section>

<div class="container">
    <!-- For Customers -->
    <section id="customers" class="service-section">
        <div class="service-header">
            <h2><?= __('for_customers_title') ?></h2>
            <p><?= __('for_customers_desc') ?></p>
        </div>
        <div class="service-grid">
            <div class="service-card">
                <div class="service-icon"><i data-feather="user"></i></div>
                <h3><?= __('customer_services') ?></h3>
                <ul>
                    <li><i data-feather="check-circle"></i> <?= __('customer_service_1') ?></li>
                    <li><i data-feather="check-circle"></i> <?= __('customer_service_2') ?></li>
                    <li><i data-feather="check-circle"></i> <?= __('customer_service_3') ?></li>
                </ul>
            </div>
            <div class="service-card">
                <div class="service-icon"><i data-feather="map-pin"></i></div>
                <h3><?= __('anywhere_delivery') ?></h3>
                <p><?= __('anywhere_delivery_desc') ?></p>
            </div>
        </div>
    </section>

    <!-- For Transporters -->
    <section id="transporters" class="service-section">
        <div class="service-header">
            <h2><?= __('for_transporters_title') ?></h2>
            <p><?= __('for_transporters_desc') ?></p>
        </div>
        <div class="service-grid">
            <div class="service-card">
                <div class="service-icon"><i data-feather="truck"></i></div>
                <h3><?= __('transporter_benefits') ?></h3>
                <ul>
                    <li><i data-feather="check-circle"></i> <?= __('transporter_service_1') ?></li>
                    <li><i data-feather="check-circle"></i> <?= __('transporter_service_2') ?></li>
                    <li><i data-feather="check-circle"></i> <?= __('transporter_service_3') ?></li>
                </ul>
            </div>
            <div class="service-card">
                <div class="service-icon"><i data-feather="trending-up"></i></div>
                <h3><?= __('grow_with_us') ?></h3>
                <p><?= __('grow_with_us_desc') ?></p>
            </div>
        </div>
    </section>

    <!-- For Businesses -->
    <section id="businesses" class="service-section">
        <div class="service-header">
            <h2><?= __('for_businesses_title') ?></h2>
            <p><?= __('for_businesses_desc') ?></p>
        </div>
        <div class="service-grid">
            <div class="service-card">
                <div class="service-icon"><i data-feather="briefcase"></i></div>
                <h3><?= __('enterprise_solutions') ?></h3>
                <ul>
                    <li><i data-feather="check-circle"></i> <?= __('business_service_1') ?></li>
                    <li><i data-feather="check-circle"></i> <?= __('business_service_2') ?></li>
                    <li><i data-feather="check-circle"></i> <?= __('business_service_3') ?></li>
                </ul>
            </div>
            <div class="service-card">
                <div class="service-icon"><i data-feather="activity"></i></div>
                <h3><?= __('logistics_optimization') ?></h3>
                <p><?= __('logistics_optimization_desc') ?></p>
            </div>
        </div>
    </section>

    <!-- Pricing -->
    <section id="pricing" class="service-section">
        <div class="service-header">
            <h2><?= __('pricing_title') ?></h2>
            <p><?= __('pricing_subtitle') ?></p>
        </div>
        <div class="pricing-grid">
            <div class="pricing-item">
                <h4><?= __('base_rate') ?></h4>
                <p><?= __('pricing_base_rate') ?></p>
            </div>
            <div class="pricing-item">
                <h4><?= __('distance') ?></h4>
                <p><?= __('pricing_distance_rate') ?></p>
            </div>
            <div class="pricing-item">
                <h4><?= __('weight') ?></h4>
                <p><?= __('pricing_weight_rate') ?></p>
            </div>
            <div class="pricing-item">
                <h4><?= __('platform_fee') ?></h4>
                <p><?= __('pricing_commission') ?></p>
            </div>
        </div>
    </section>
</div>

<?php require_once __DIR__ . '/../layout/footer.php'; ?>
<script>feather.replace();</script>
</body>
</html>
