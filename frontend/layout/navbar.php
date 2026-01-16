<style>
    /* Navbar with gradient logo like Register page */
    nav {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 20px 5%;
        background: white;
        box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        position: fixed;
        width: 100%;
        top: 0;
        z-index: 100;
    }

    .logo {
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .logo-square {
        width: 50px;
        height: 50px;
        background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
        color: white;
        font-size: 20px;
        font-weight: 800;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 16px;
        box-shadow: 0 10px 15px -3px rgba(37, 99, 235, 0.3);
    }

    .logo-text {
        font-size: 1.5rem;
        font-weight: 800;
        color: var(--secondary);
    }

    .nav-links {
        display: flex;
        gap: 30px;
    }

    .nav-links a {
        text-decoration: none;
        color: #64748b;
        font-weight: 500;
        transition: 0.3s;
    }

    .nav-links a:hover {
        color: var(--primary);
    }

    .btn {
        padding: 10px 24px;
        border-radius: 8px;
        text-decoration: none;
        font-weight: 600;
        transition: 0.3s;
    }

    .btn-primary {
        background: var(--primary);
        color: white;
    }

    .btn-primary:hover {
        background: var(--primary-dark);
    }

    .btn-outline {
        border: 2px solid var(--primary);
        color: var(--primary);
    }

    .btn-outline:hover {
        background: var(--primary);
        color: white;
    }
</style>

<nav>
    <div class="logo">
        <div class="logo-square">CT</div>
        <div class="logo-text">CargoConnect</div>
    </div>
    
    <?php if (!isset($simpleNavbar) || !$simpleNavbar): ?>
    <div class="nav-links">
        <a href="/cargo-project/index.php#features">Features</a>
        <a href="/cargo-project/index.php#how-it-works">How it Works</a>
        <a href="/cargo-project/frontend/public/about.php">About</a>
        <a href="/cargo-project/frontend/public/contact.php">Contact</a>
    </div>
    <div style="display: flex; gap: 15px;">
        <a href="/cargo-project/frontend/auth/login.php" class="btn btn-outline">Log In</a>
        <a href="/cargo-project/frontend/auth/register.php" class="btn btn-primary">Get Started</a>
    </div>
    <?php else: ?>
    <div style="display: flex; gap: 15px;">
        <a href="/cargo-project/index.php" class="btn btn-outline" style="border:none; color:#64748b;">&larr; Back to Home</a>
    </div>
    <?php endif; ?>
</nav>
