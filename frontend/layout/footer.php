<?php
$fPad = (isset($footerCompact) && $footerCompact) ? '30px 0 15px' : '60px 0 30px';
?>
<footer style="background: #0f172a; color: #94a3b8; padding: <?= $fPad ?>;">
    <div class="container" style="max-width: 1200px; margin: 0 auto; padding: 0 20px;">
        <div style="display: grid; grid-template-columns: 2fr 1fr 1fr 1fr; gap: 40px; margin-bottom: 40px;">
            <!-- Brand -->
            <div>
                <h3 style="color: white; font-size: 1.5rem; font-weight: 700; margin-bottom: 20px;">Cargo Connect</h3>
                <p style="line-height: 1.6; margin-bottom: 20px;">
                    Connecting businesses with reliable transporters across Ethiopia. Fast, secure, and transparent logistics for everyone.
                </p>
                <div style="display: flex; gap: 15px;">
                    <a href="#" style="color: #94a3b8; transition: color 0.2s;"><i data-feather="facebook"></i></a>
                    <a href="#" style="color: #94a3b8; transition: color 0.2s;"><i data-feather="twitter"></i></a>
                    <a href="#" style="color: #94a3b8; transition: color 0.2s;"><i data-feather="instagram"></i></a>
                </div>
            </div>

            <!-- Links -->
            <div>
                <h4 style="color: white; font-weight: 600; margin-bottom: 20px;">Company</h4>
                <ul style="list-style: none; padding: 0;">
                    <li style="margin-bottom: 12px;"><a href="/cargo-project/frontend/public/about.php" style="color: inherit; text-decoration: none; transition: color 0.2s;">About Us</a></li>
                    <li style="margin-bottom: 12px;"><a href="/cargo-project/frontend/public/contact.php" style="color: inherit; text-decoration: none; transition: color 0.2s;">Contact Us</a></li>
                    <li style="margin-bottom: 12px;"><a href="#" style="color: inherit; text-decoration: none; transition: color 0.2s;">Careers</a></li>
                </ul>
            </div>

            <div>
                <h4 style="color: white; font-weight: 600; margin-bottom: 20px;">Services</h4>
                <ul style="list-style: none; padding: 0;">
                    <li style="margin-bottom: 12px;"><a href="#" style="color: inherit; text-decoration: none; transition: color 0.2s;">For Transporters</a></li>
                    <li style="margin-bottom: 12px;"><a href="#" style="color: inherit; text-decoration: none; transition: color 0.2s;">For Businesses</a></li>
                    <li style="margin-bottom: 12px;"><a href="#" style="color: inherit; text-decoration: none; transition: color 0.2s;">Pricing</a></li>
                </ul>
            </div>

            <div>
                <h4 style="color: white; font-weight: 600; margin-bottom: 20px;">Legal</h4>
                <ul style="list-style: none; padding: 0;">
                    <li style="margin-bottom: 12px;"><a href="#" style="color: inherit; text-decoration: none; transition: color 0.2s;">Privacy Policy</a></li>
                    <li style="margin-bottom: 12px;"><a href="#" style="color: inherit; text-decoration: none; transition: color 0.2s;">Terms of Service</a></li>
                </ul>
            </div>
        </div>

        <div style="border-top: 1px solid #1e293b; padding-top: 30px; text-align: center; font-size: 0.9rem;">
            &copy; <?= date('Y') ?> Cargo Connect. All rights reserved.
        </div>
    </div>
</footer>

<script>
    feather.replace();

    // Mobile Sidebar Toggle
    const sidebarToggle = document.getElementById('sidebarToggle');
    const sidebar = document.querySelector('.sidebar');
    
    if (sidebarToggle && sidebar) {
        sidebarToggle.addEventListener('click', () => {
            sidebar.classList.toggle('open');
        });

        // Close sidebar when clicking outside on mobile
        document.addEventListener('click', (e) => {
            if (window.innerWidth <= 768 && 
                !sidebar.contains(e.target) && 
                !sidebarToggle.contains(e.target) && 
                sidebar.classList.contains('open')) {
                sidebar.classList.remove('open');
            }
        });
    }
</script>
</body>
</html>