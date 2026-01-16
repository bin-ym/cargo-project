<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact Us - CargoConnect</title>
    <script src="https://unpkg.com/feather-icons"></script>
    <link rel="stylesheet" href="../css/public.css">
</head>
<body>

<?php require_once __DIR__ . '/../layout/navbar.php'; ?>

<section class="page-header">
    <h1>Contact Us</h1>
    <p>We're here to help with your logistics needs.</p>
</section>

<section class="content-section">
    <div class="container contact-grid">

        <!-- Contact Info -->
        <div class="contact-info">
            <h3>Contact Information</h3>

            <div>
                <i data-feather="map-pin"></i>
                <span>Bole Road, Addis Ababa, Ethiopia</span>
            </div>

            <div>
                <i data-feather="phone"></i>
                <span>+251 911 234 567</span>
            </div>

            <div>
                <i data-feather="mail"></i>
                <span>support@cargoconnect.com</span>
            </div>
        </div>

        <!-- Contact Form -->
        <div class="contact-form">
            <h3>Send us a Message</h3>

            <form method="POST" action="#">
                <div class="form-row">
                    <input type="text" name="first_name" placeholder="First Name" required>
                    <input type="text" name="last_name" placeholder="Last Name" required>
                </div>

                <input type="email" name="email" placeholder="Email Address" required>
                <textarea name="message" rows="4" placeholder="Your Message" required></textarea>

                <button type="submit" class="btn btn-primary">
                    Send Message
                </button>
            </form>
        </div>

    </div>
</section>

<?php require_once __DIR__ . '/../layout/footer.php'; ?>

<script>
    feather.replace();
</script>

</body>
</html>