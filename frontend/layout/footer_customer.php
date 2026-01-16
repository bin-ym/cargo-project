<footer class="dashboard-footer">
    <style>
        .dashboard-footer {
            margin-top: auto;
            padding-top: 40px;
        }

        .dashboard-footer .footer-content {
            background: var(--surface);
            border-radius: var(--radius-lg);
            border: 1px solid var(--border);
            padding: 24px;
            box-shadow: var(--shadow);
        }

        .footer-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 24px;
            margin-bottom: 24px;
        }

        .footer-brand {
            grid-column: span 2;
            margin-left: 50px;
        }

        .brand-header {
            display: flex;
            align-items: center;
            gap: 12px;
            margin-bottom: 16px;
        }

        .brand-icon {
            width: 32px;
            height: 32px;
            background: var(--primary-gradient);
            border-radius: var(--radius-sm);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: 700;
        }

        .footer-brand h4 {
            font-weight: 700;
            color: var(--slate-900);
            margin: 0;
        }

        .footer-brand p {
            color: var(--text-muted);
            font-size: 14px;
            line-height: 1.6;
            max-width: 300px;
        }

        .footer-links h5,
        .footer-contact h5 {
            font-weight: 700;
            color: var(--slate-900);
            margin-bottom: 16px;
            font-size: 14px;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }

        .footer-links ul {
            list-style: none;
            padding: 0;
            margin: 0;
            display: flex;
            flex-direction: column;
            gap: 8px;
        }

        .footer-links a {
            color: var(--text-muted);
            text-decoration: none;
            font-size: 14px;
            transition: color 0.2s;
        }

        .footer-links a:hover {
            color: var(--primary-600);
        }

        .footer-contact div {
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 14px;
        }

        .footer-contact div a {
            color: var(--text-muted);
            text-decoration: none;
            transition: color 0.2s;
        }

        .footer-contact div a:hover {
            color: var(--primary-600);
        }

        .footer-bottom {
            border-top: 1px solid var(--border);
            padding-top: 24px;
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 12px;
        }

        .footer-bottom div,
        .footer-bottom a {
            color: var(--text-muted);
            font-size: 13px;
            font-weight: 500;
            transition: color 0.2s;
        }

        .footer-bottom a:hover {
            color: var(--primary-600);
        }

        .footer-socials {
            display: flex;
            gap: 16px;
        }

        @media (max-width: 768px) {
            .footer-brand {
                grid-column: span 1;
            }
        }
    </style>

    <div class="footer-content">
        <div class="footer-grid">
            <!-- Brand & Mission -->
            <div class="footer-brand">
                <div class="brand-header">
                    <div class="brand-icon">C</div>
                    <h4>Cargo Connect</h4>
                </div>
                <p>
                    Connecting businesses with reliable transporters across Ethiopia. Fast, secure, and transparent logistics.
                </p>
            </div>

            <!-- Quick Links -->
            <div class="footer-links">
                <h5>Quick Links</h5>
                <ul>
                    <li><a href="/cargo-project/frontend/customer/dashboard.php">Dashboard</a></li>
                    <li><a href="/cargo-project/frontend/customer/new_request.php">New Request</a></li>
                    <li><a href="/cargo-project/frontend/customer/my_requests.php">My Shipments</a></li>
                </ul>
            </div>

            <!-- Contact Info -->
            <div class="footer-contact">
                <h5>Contact</h5>
                <div><i data-feather="mail"></i><a href="mailto:support@cargoconnect.et">support@cargoconnect.et</a></div>
                <div><i data-feather="phone"></i><a href="tel:+251911000000">+251 911 000 000</a></div>
            </div>
        </div>

        <!-- Bottom Bar Centered -->
        <div class="footer-bottom">
            <div class="footer-socials">
                <a href="#"><i data-feather="facebook"></i></a>
                <a href="#"><i data-feather="twitter"></i></a>
                <a href="#"><i data-feather="instagram"></i></a>
                <a href="#"><i data-feather="linkedin"></i></a>
            </div>
            <div>&copy; <?= date('Y') ?> Cargo Connect. All rights reserved.</div>
        </div>
    </div>
</footer>

<script>
    if (typeof feather !== 'undefined') feather.replace();
</script>