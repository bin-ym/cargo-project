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
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CargoConnect - Modern Logistics Platform</title>
    <script src="https://unpkg.com/feather-icons"></script>
    <link rel="stylesheet" href="frontend/css/public.css">
</head>
<body>
<!-- Navbar -->
<?php require_once __DIR__ . '/frontend/layout/navbar.php'; ?>

    <section class="hero">
        <div class="hero-content">
            <h1>Smart Logistics for a Moving World</h1>
            <p>Connect with trusted transporters, track your cargo in real-time with GPS, and manage your supply chain effortlessly.</p>
            <div style="display: flex; gap: 15px; flex-wrap: wrap;">
                <a href="frontend/auth/register.php" class="btn btn-primary">Start Shipping Now</a>
                <a href="#features" class="btn btn-outline">Learn More</a>
            </div>
        </div>
        <div class="hero-image">
            <img src="https://images.unsplash.com/photo-1586528116311-ad8dd3c8310d?ixlib=rb-4.0.3&auto=format&fit=crop&w=1000&q=80" alt="Logistics Dashboard">
        </div>
    </section>

    <section class="features" id="features">
        <div class="section-header">
            <h2>Why Choose CargoConnect?</h2>
            <p>Everything you need to manage your shipments in one place.</p>
        </div>
        <div class="grid">
            <div class="feature-card">
                <div class="icon-box"><i data-feather="map"></i></div>
                <h3>Real-Time GPS Tracking</h3>
                <p>Watch your cargo move on the map in real-time. Know exactly where your shipment is at any moment.</p>
            </div>
            <div class="feature-card">
                <div class="icon-box"><i data-feather="shield"></i></div>
                <h3>Secure Payments</h3>
                <p>Integrated with Chapa for secure, seamless payments. Pay only when you're ready.</p>
            </div>
            <div class="feature-card">
                <div class="icon-box"><i data-feather="truck"></i></div>
                <h3>Verified Transporters</h3>
                <p>Access a network of verified, reliable transporters ready to move your goods safely.</p>
            </div>
        </div>
    </section>

    <?php require_once __DIR__ . '/frontend/layout/footer.php'; ?>

    <script>
        feather.replace();
    </script>
</body>
</html>