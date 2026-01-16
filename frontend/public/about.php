<?php
// frontend/public/about.php
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>About Us - CargoConnect</title>
    <script src="https://unpkg.com/feather-icons"></script>
    <link rel="stylesheet" href="../css/public.css">
</head>
<body>

<?php require_once __DIR__ . '/../layout/navbar.php'; ?>

<!-- Hero / Intro -->
<section class="hero hero-small">
    <div class="hero-content">
        <h1>About CargoConnect</h1>
        <p>
            CargoConnect is a modern logistics platform built to simplify cargo transportation,
            connect customers with trusted transporters, and bring transparency to logistics operations.
        </p>
    </div>
</section>

<!-- Mission Section -->
<section class="features">
    <div class="section-header">
        <h2>Our Mission</h2>
        <p>
            To make logistics smarter, faster, and more reliable for businesses and individuals.
        </p>
    </div>

    <div class="grid">
        <div class="feature-card">
            <div class="icon-box">
                <i data-feather="target"></i>
            </div>
            <h3>Efficiency</h3>
            <p>
                We eliminate delays and complexity by digitizing the entire shipping lifecycle —
                from request to delivery.
            </p>
        </div>

        <div class="feature-card">
            <div class="icon-box">
                <i data-feather="users"></i>
            </div>
            <h3>Trust</h3>
            <p>
                We work only with verified transporters, ensuring safety, accountability,
                and peace of mind.
            </p>
        </div>

        <div class="feature-card">
            <div class="icon-box">
                <i data-feather="trending-up"></i>
            </div>
            <h3>Innovation</h3>
            <p>
                By leveraging GPS tracking and smart systems, we push logistics into the future.
            </p>
        </div>
    </div>
</section>

<!-- Vision Section -->
<section class="content-section">
    <div class="container vision-section">
        <h2>Our Vision</h2>
        <p>
            We envision a future where moving goods is as simple as sending a message —
            transparent, trackable, and accessible to everyone.
        </p>
    </div>
</section>

<?php require_once __DIR__ . '/../layout/footer.php'; ?>

<script>
    feather.replace();
</script>

</body>
</html>