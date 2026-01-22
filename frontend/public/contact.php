<?php
// frontend/public/contact.php
session_start();
require_once __DIR__ . '/../../backend/config/languages.php';
?>
<!DOCTYPE html>
<html lang="<?= $_SESSION['lang'] ?? 'en' ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= __('Contact us_title') ?></title>
    <script src="https://unpkg.com/feather-icons"></script>
    <link rel="stylesheet" href="../css/public.css">
    <style>
        .form-message {
            padding: 12px 16px;
            border-radius: 8px;
            font-size: 14px;
            display: none;
            line-height: 1.5;
            margin-bottom: 20px;
        }
        .form-message.error {
            background-color: #fef2f2;
            color: #991b1b;
            border: 1px solid #fecaca;
            display: block;
        }
        .form-message.success {
            background-color: #f0fdf4;
            color: #166534;
            border: 1px solid #bbf7d0;
            display: block;
        }
    </style>
</head>
<body>

<?php require_once __DIR__ . '/../layout/navbar.php'; ?>

<section class="page-header">
    <h1><?= __('Contact us') ?></h1>
    <p><?= __('Contact_us_desc') ?></p>
</section>

<section class="content-section">
    <div class="container contact-grid">

        <!-- Contact Info -->
        <div class="contact-info">
            <h3><?= __('contact_info_h3') ?></h3>

            <div>
                <i data-feather="map-pin"></i>
                <span><?= __('contact_address') ?></span>
            </div>

            <div>
                <i data-feather="phone"></i>
                <span><?= __('contact_phone') ?></span>
            </div>

            <div>
                <i data-feather="mail"></i>
                <span><?= __('contact_email') ?></span>
            </div>
        </div>

        <!-- Contact Form -->
        <div class="contact-form">
            <h3><?= __('send_message_h3') ?></h3>
            <div id="contactMessage" class="form-message"></div>
            <form id="contactForm">
                <div class="form-row">
                    <input type="text" name="first_name" placeholder="<?= __('first_name_placeholder') ?>" required>
                    <input type="text" name="last_name" placeholder="<?= __('last_name_placeholder') ?>" required>
                </div>

                <input type="email" name="email" placeholder="<?= __('email_placeholder') ?>" required>
                <textarea name="message" rows="4" placeholder="<?= __('message_placeholder') ?>" required></textarea>

                <button type="submit" class="btn btn-primary">
                    <?= __('send_message_btn') ?>
                </button>
            </form>
        </div>

    </div>
</section>

<?php require_once __DIR__ . '/../layout/footer.php'; ?>

<script>
    feather.replace();

    function showContactMessage(msg, type = 'error') {
        const el = document.getElementById('contactMessage');
        el.textContent = msg;
        el.className = 'form-message ' + type;
        el.style.display = 'block';
    }

    function clearContactMessage() {
        document.getElementById('contactMessage').style.display = 'none';
    }

    // Redefine total shortcuts
    function showSuccess(msg) { showContactMessage(msg, 'success'); }
    function showError(msg) { showContactMessage(msg, 'error'); }

    document.getElementById('contactForm').addEventListener('submit', async function(e) {
        e.preventDefault();
        const btn = this.querySelector('button[type="submit"]');
        const originalText = btn.textContent;
        btn.disabled = true;
        btn.textContent = 'Sending...';

        const formData = new FormData(this);
        const data = Object.fromEntries(formData.entries());

        try {
            const response = await fetch('../../backend/api/public/submit_contact.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify(data)
            });

            const result = await response.json();
            
            if (result.success) {
                showSuccess('Message sent successfully!');
                this.reset();
            } else {
                showError(result.error || 'Failed to send message');
            }
        } catch (error) {
            console.error('Error:', error);
            showError('An error occurred. Please try again.');
        } finally {
            btn.disabled = false;
            btn.textContent = originalText;
        }
    });
</script>

</body>
</html>