<?php
$fPad = (isset($footerCompact) && $footerCompact) ? '24px 0 16px' : '48px 0 24px';
?>

<footer class="dashboard-footer" style="padding: <?= $fPad ?>; width: 100%; background: var(--surface);">
    <style>
        .dashboard-footer {
            margin-top: auto;
        }

        /* Full-width container wrapper */
        .footer-wrapper {
            width: 100%;
            max-width: 1200px;  /* internal content max width */
            margin: 0 auto;
            padding: 0 20px;    /* padding like public footer */
            box-sizing: border-box;
        }

        .footer-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
            gap: 40px;
            margin-bottom: 32px;
        }

        /* Brand */
        .footer-brand {
            grid-column: span 2;
        }

        .footer-brand h3 {
            font-weight: 800;
            font-size: 20px;
            color: var(--slate-900);
            margin-bottom: 14px;
        }

        .footer-brand p {
            color: var(--text-muted);
            font-size: 14px;
            line-height: 1.7;
            max-width: 360px;
        }

        /* Section titles */
        .footer-links h5,
        .footer-contact h5 {
            font-weight: 700;
            color: var(--slate-900);
            margin-bottom: 16px;
            font-size: 13px;
            text-transform: uppercase;
            letter-spacing: 0.08em;
            text-align: center;
        }

        /* Quick links centered */
        .footer-links {
            text-align: center;
        }

        .footer-links ul {
            list-style: none;
            padding: 0;
            margin: 0;
            display: flex;
            flex-direction: column;
            gap: 10px;
            align-items: center;
        }

        .footer-links a {
            color: var(--text-muted);
            text-decoration: none;
            font-size: 14px;
            transition: all 0.2s ease;
            padding: 4px 10px;
            border-radius: 6px;
        }

        .footer-links a:hover {
            color: var(--primary-600);
            background: rgba(99, 102, 241, 0.08);
        }

        /* Contact */
        .footer-contact {
            text-align: center;
        }

        .footer-contact div {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            font-size: 14px;
            color: var(--text-muted);
            margin-bottom: 10px;
        }

        .footer-contact a {
            color: inherit;
            text-decoration: none;
        }

        .footer-contact a:hover {
            color: var(--primary-600);
        }

        /* Bottom */
        .footer-bottom {
            border-top: 1px solid var(--border);
            padding-top: 24px;
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 14px;
        }

        .footer-bottom div {
            color: var(--text-muted);
            font-size: 13px;
            font-weight: 500;
            text-align: center;
        }

        /* Socials */
        .footer-socials {
            display: flex;
            gap: 14px;
        }

        .footer-socials a {
            width: 36px;
            height: 36px;
            border-radius: 50%;
            border: 1px solid var(--border);
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--text-muted);
            transition: all 0.25s ease;
        }

        .footer-socials a:hover {
            color: #fff;
            background: var(--primary-gradient);
            border-color: transparent;
            transform: translateY(-3px);
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.12);
        }

        @media (max-width: 768px) {
            .footer-brand {
                grid-column: span 1;
                text-align: center;
            }

            .footer-brand p {
                margin: 0 auto;
            }
        }
    </style>

    <div class="footer-wrapper">

        <div class="footer-grid">

            <!-- Brand -->
            <div class="footer-brand">
                <h3><?= __('CargoConnect') ?></h3>
                <p><?= __('footer_brand_desc') ?></p>
            </div>

            <!-- Quick Links -->
            <div class="footer-links">
                <h5><?= __('quick_links') ?></h5>
                <ul>
                    <li><a href="/cargo-project/frontend/customer/dashboard.php"><?= __('dashboard') ?></a></li>
                    <li><a href="/cargo-project/frontend/customer/new_request.php"><?= __('new_request') ?></a></li>
                    <li><a href="/cargo-project/frontend/customer/my_requests.php"><?= __('my_shipments') ?></a></li>
                </ul>
            </div>

            <!-- Contact -->
            <div class="footer-contact">
                <h5><?= __('contact') ?></h5>
                <div><i data-feather="mail"></i><a href="mailto:support@cargoconnect.et">support@cargoconnect.et</a></div>
                <div><i data-feather="phone"></i><a href="tel:+251911000000">+251 911 000 000</a></div>
            </div>

        </div>

        <!-- Bottom -->
        <div class="footer-bottom">
            <div class="footer-socials">
                <a href="#"><i data-feather="facebook"></i></a>
                <a href="#"><i data-feather="instagram"></i></a>
                <a href="#"><i data-feather="linkedin"></i></a>
            </div>
            <div>
                &copy; <?= date('Y') ?> <?= __('CargoConnect') ?>. <?= __('all_rights_reserved') ?>
            </div>
        </div>

    </div>
</footer>

<script>
    if (typeof feather !== 'undefined') feather.replace();
</script>