<?php
$fPad = (isset($footerCompact) && $footerCompact) ? '30px 0 15px' : '60px 0 30px';
?>
<footer style="background: #0f172a; color: #94a3b8; padding: <?= $fPad ?>;">
    <div class="container" style="max-width: 1200px; margin: 0 auto; padding: 0 20px;">
        <div style="display: grid; grid-template-columns: 2fr 1fr 1fr 1fr; gap: 40px; margin-bottom: 40px;">

            <!-- Brand -->
            <div>
                <h3 style="color: white; font-size: 1.5rem; font-weight: 700; margin-bottom: 20px;">
                    <?= __('CargoConnect') ?>
                </h3>

                <p style="line-height: 1.6; margin-bottom: 24px;">
                    <?= __('footer_brand_desc') ?>
                </p>

            </div>

            <!-- Company -->
            <div>
                <h4 style="color: white; font-weight: 600; margin-bottom: 20px;">
                    <?= __('company') ?>
                </h4>
                <ul style="list-style: none; padding: 0;">
                    <li style="margin-bottom: 12px;">
                        <a href="/cargo-project/frontend/public/about.php"
                           style="color: inherit; text-decoration: none; transition: color 0.2s;">
                            <?= __('About us') ?>
                        </a>
                    </li>
                    <li style="margin-bottom: 12px;">
                        <a href="/cargo-project/frontend/public/contact.php"
                           style="color: inherit; text-decoration: none; transition: color 0.2s;">
                            <?= __('Contact us') ?>
                        </a>
                    </li>
                </ul>
            </div>

            <!-- Services -->
            <div>
                <h4 style="color: white; font-weight: 600; margin-bottom: 20px;">
                    <?= __('services') ?>
                </h4>
                <ul style="list-style: none; padding: 0;">
                    <li style="margin-bottom: 12px;">
                        <a href="/cargo-project/frontend/public/services.php"
                           style="color: inherit; text-decoration: none; transition: color 0.2s;">
                            <?= __('services') ?>
                        </a>
                    </li>
                </ul>
            </div>

            <!-- Legal -->
            <div>
                <h4 style="color: white; font-weight: 600; margin-bottom: 20px;">
                    <?= __('legal') ?>
                </h4>
                <ul style="list-style: none; padding: 0;">
                    <li style="margin-bottom: 12px;">
                        <a href="/cargo-project/frontend/public/privacy_policy.php"
                           style="color: inherit; text-decoration: none; transition: color 0.2s;">
                            <?= __('privacy_policy') ?>
                        </a>
                    </li>
                    <li style="margin-bottom: 12px;">
                        <a href="/cargo-project/frontend/public/terms_of_service.php"
                           style="color: inherit; text-decoration: none; transition: color 0.2s;">
                            <?= __('terms_of_service') ?>
                        </a>
                    </li>
                </ul>
            </div>

        </div>

        <!-- Bottom -->
<div style="border-top: 1px solid #1e293b; padding-top: 30px; text-align: center; font-size: 0.9rem;">

    <!-- Social Media -->
    <div style="display: flex; justify-content: center; gap: 14px; margin-bottom: 16px;">
        <a href="#"
           style="
            width: 38px;
            height: 38px;
            border-radius: 50%;
            border: 1px solid #1e293b;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #94a3b8;
            transition: all 0.25s ease;
           "
           onmouseover="this.style.color='#1877f2'; this.style.transform='translateY(-3px)'"
           onmouseout="this.style.color='#94a3b8'; this.style.transform='translateY(0)'">
            <i data-feather="facebook"></i>
        </a>

        <a href="#"
           style="
            width: 38px;
            height: 38px;
            border-radius: 50%;
            border: 1px solid #1e293b;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #94a3b8;
            transition: all 0.25s ease;
           "
           onmouseover="this.style.color='#e4405f'; this.style.transform='translateY(-3px)'"
           onmouseout="this.style.color='#94a3b8'; this.style.transform='translateY(0)'">
            <i data-feather="instagram"></i>
        </a>

        <a href="#"
           style="
            width: 38px;
            height: 38px;
            border-radius: 50%;
            border: 1px solid #1e293b;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #94a3b8;
            transition: all 0.25s ease;
           "
           onmouseover="this.style.color='#0a66c2'; this.style.transform='translateY(-3px)'"
           onmouseout="this.style.color='#94a3b8'; this.style.transform='translateY(0)'">
            <i data-feather="linkedin"></i>
        </a>
    </div>

    &copy; <?= date('Y') ?> <?= __('CargoConnect') ?>. <?= __('all_rights_reserved') ?>
</div>

    </div>
</footer>

<script>
    if (typeof feather !== 'undefined') {
        feather.replace();
    }
</script>