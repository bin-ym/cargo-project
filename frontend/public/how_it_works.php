<?php
// frontend/public/how_it_works.php
session_start();
require_once __DIR__ . '/../../backend/config/languages.php';
?>
<!DOCTYPE html>
<html lang="<?= $_SESSION['lang'] ?? 'en' ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= __('how_it_works_title') ?></title>
    <script src="https://unpkg.com/feather-icons"></script>
    <link rel="stylesheet" href="../css/public.css">
    <style>
        .workflow-section {
            padding: 60px 5%;
        }
        .workflow-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 30px;
            margin-top: 40px;
        }
        .workflow-card {
            background: white;
            padding: 30px;
            border-radius: 16px;
            box-shadow: 0 4px 6px -1px rgba(0,0,0,0.1);
            text-align: center;
            transition: transform 0.3s;
        }
        .workflow-card:hover {
            transform: translateY(-5px);
        }
        .step-number {
            width: 40px;
            height: 40px;
            background: var(--primary);
            color: white;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 800;
            margin: 0 auto 20px;
        }
        .workflow-card h3 {
            margin-bottom: 15px;
            color: var(--secondary);
        }
        .workflow-card p {
            color: #64748b;
            line-height: 1.6;
        }
        .user-type-toggle {
            display: flex;
            justify-content: center;
            gap: 20px;
            margin-bottom: 40px;
        }
        .type-btn {
            padding: 12px 30px;
            border-radius: 30px;
            border: 2px solid var(--primary);
            background: transparent;
            color: var(--primary);
            font-weight: 700;
            cursor: pointer;
            transition: 0.3s;
        }
        .type-btn.active {
            background: var(--primary);
            color: white;
        }
    </style>
</head>
<body>

<?php require_once __DIR__ . '/../layout/navbar.php'; ?>

<section class="hero hero-small">
    <div class="hero-content">
        <h1><?= __('how_it_works_header') ?></h1>
        <p><?= __('how_it_works_subtitle') ?></p>
    </div>
</section>

<section class="workflow-section">
    <div class="user-type-toggle">
        <button class="type-btn active" onclick="showWorkflow('customer')"><?= __('for_customers') ?></button>
        <button class="type-btn" onclick="showWorkflow('transporter')"><?= __('for_transporters') ?></button>
    </div>

    <div id="customer-workflow" class="workflow-grid">
        <div class="workflow-card">
            <div class="step-number">1</div>
            <div class="icon-box"><i data-feather="edit-3"></i></div>
            <h3><?= __('step_1_customer') ?></h3>
            <p><?= __('step_1_customer_desc') ?></p>
        </div>
        <div class="workflow-card">
            <div class="step-number">2</div>
            <div class="icon-box"><i data-feather="credit-card"></i></div>
            <h3><?= __('step_2_customer') ?></h3>
            <p><?= __('step_2_customer_desc') ?></p>
        </div>
        <div class="workflow-card">
            <div class="step-number">3</div>
            <div class="icon-box"><i data-feather="map-pin"></i></div>
            <h3><?= __('step_3_customer') ?></h3>
            <p><?= __('step_3_customer_desc') ?></p>
        </div>
        <div class="workflow-card">
            <div class="step-number">4</div>
            <div class="icon-box"><i data-feather="check-circle"></i></div>
            <h3><?= __('step_4_customer') ?></h3>
            <p><?= __('step_4_customer_desc') ?></p>
        </div>
    </div>

    <div id="transporter-workflow" class="workflow-grid" style="display: none;">
        <div class="workflow-card">
            <div class="step-number">1</div>
            <div class="icon-box"><i data-feather="search"></i></div>
            <h3><?= __('step_1_transporter') ?></h3>
            <p><?= __('step_1_transporter_desc') ?></p>
        </div>
        <div class="workflow-card">
            <div class="step-number">2</div>
            <div class="icon-box"><i data-feather="truck"></i></div>
            <h3><?= __('step_2_transporter') ?></h3>
            <p><?= __('step_2_transporter_desc') ?></p>
        </div>
        <div class="workflow-card">
            <div class="step-number">3</div>
            <div class="icon-box"><i data-feather="refresh-cw"></i></div>
            <h3><?= __('step_3_transporter') ?></h3>
            <p><?= __('step_3_transporter_desc') ?></p>
        </div>
        <div class="workflow-card">
            <div class="step-number">4</div>
            <div class="icon-box"><i data-feather="dollar-sign"></i></div>
            <h3><?= __('step_4_transporter') ?></h3>
            <p><?= __('step_4_transporter_desc') ?></p>
        </div>
    </div>
</section>

<?php require_once __DIR__ . '/../layout/footer.php'; ?>

<script>
    feather.replace();

    function showWorkflow(type) {
        const customerWorkflow = document.getElementById('customer-workflow');
        const transporterWorkflow = document.getElementById('transporter-workflow');
        const btns = document.querySelectorAll('.type-btn');

        if (type === 'customer') {
            customerWorkflow.style.display = 'grid';
            transporterWorkflow.style.display = 'none';
            btns[0].classList.add('active');
            btns[1].classList.remove('active');
        } else {
            customerWorkflow.style.display = 'none';
            transporterWorkflow.style.display = 'grid';
            btns[0].classList.remove('active');
            btns[1].classList.add('active');
        }
        feather.replace();
    }
</script>

</body>
</html>
