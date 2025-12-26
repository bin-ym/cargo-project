<?php
// File: C:\xampp\htdocs\cargo-project\index.php
session_start();

// If logged in, redirect to dashboard
if (isset($_SESSION['user_id']) && isset($_SESSION['role'])) {
    $redirects = [
        'customer'    => '/cargo-project/frontend/customer/dashboard.php',
        'transporter' => '/cargo-project/frontend/transporter/dashboard.php',
        'admin'       => '/cargo-project/frontend/admin/dashboard.php',
        'manager'     => '/cargo-project/frontend/manager/dashboard.php',
        'finance'     => '/cargo-project/frontend/finance/dashboard.php'
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
    <style>
        :root {
            --primary: #2563eb;
            --primary-dark: #1d4ed8;
            --secondary: #0f172a;
            --accent: #f59e0b;
        }
        * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Inter', sans-serif; }
        body { color: #334155; line-height: 1.6; }
        
        /* Navbar */
        nav {
            display: flex; justify-content: space-between; align-items: center;
            padding: 20px 5%; background: white; box-shadow: 0 1px 3px rgba(0,0,0,0.1);
            position: fixed; width: 100%; top: 0; z-index: 100;
        }
        .logo { font-size: 1.5rem; font-weight: 800; color: var(--secondary); display: flex; align-items: center; gap: 8px; }
        .nav-links { display: flex; gap: 30px; }
        .nav-links a { text-decoration: none; color: #64748b; font-weight: 500; transition: 0.3s; }
        .nav-links a:hover { color: var(--primary); }
        .btn { padding: 10px 24px; border-radius: 8px; text-decoration: none; font-weight: 600; transition: 0.3s; }
        .btn-primary { background: var(--primary); color: white; }
        .btn-primary:hover { background: var(--primary-dark); }
        .btn-outline { border: 2px solid var(--primary); color: var(--primary); }
        .btn-outline:hover { background: var(--primary); color: white; }

        /* Hero */
        .hero {
            padding: 160px 5% 100px;
            background: linear-gradient(135deg, #eff6ff 0%, #ffffff 100%);
            display: flex; align-items: center; gap: 50px;
        }
        .hero-content { flex: 1; }
        .hero h1 { font-size: 3.5rem; line-height: 1.2; color: var(--secondary); margin-bottom: 20px; }
        .hero p { font-size: 1.25rem; color: #64748b; margin-bottom: 40px; }
        .hero-image { flex: 1; }
        .hero-image img { width: 100%; border-radius: 20px; box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1); }

        /* Features */
        .features { padding: 100px 5%; background: white; }
        .section-header { text-align: center; margin-bottom: 60px; }
        .section-header h2 { font-size: 2.5rem; color: var(--secondary); margin-bottom: 15px; }
        .grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 40px; }
        .feature-card { padding: 30px; border-radius: 12px; background: #f8fafc; transition: 0.3s; }
        .feature-card:hover { transform: translateY(-5px); }
        .icon-box { width: 50px; height: 50px; background: #dbeafe; color: var(--primary); display: flex; align-items: center; justify-content: center; border-radius: 12px; margin-bottom: 20px; }

        @media (max-width: 768px) {
            .hero { flex-direction: column; padding-top: 120px; text-align: center; }
            .hero h1 { font-size: 2.5rem; }
            .nav-links { display: none; }
        }
    </style>
</head>
<body>

    <nav>
        <div class="logo">
            <i data-feather="box"></i> CargoConnect
        </div>
        <div class="nav-links">
            <a href="#features">Features</a>
            <a href="#how-it-works">How it Works</a>
            <a href="#pricing">Pricing</a>
        </div>
        <div style="display: flex; gap: 15px;">
            <a href="frontend/auth/login.php" class="btn btn-outline">Log In</a>
            <a href="frontend/auth/register.php" class="btn btn-primary">Get Started</a>
        </div>
    </nav>

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

    <script>
        feather.replace();
    </script>
</body>
</html>