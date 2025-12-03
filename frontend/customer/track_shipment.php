<?php
require_once __DIR__ . '/../../backend/config/session.php';
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'customer') {
    header("Location: /cargo-project/frontend/auth/login.php");
    exit();
}
require_once __DIR__ . '/../layout/header_customer.php';
?>

<div class="dashboard">
    <?php include 'sidebar.php'; ?>
    <main class="main-content">
        <header class="topbar">
            <h2>Track Shipment</h2>
            <div class="user-info">
                <span><?= htmlspecialchars($_SESSION['full_name'] ?? 'Customer') ?></span>
            </div>
        </header>

        <div class="content">
            <div class="recent-activity" style="max-width: 600px;">
                <h3>Enter Tracking Number</h3>
                <div style="margin-top: 20px; margin-bottom: 30px;">
                    <input type="text" placeholder="e.g., CT-2847" style="width: 100%; padding: 14px; border: 1px solid #e2e8f0; border-radius: 8px; font-size: 16px;">
                    <button class="btn btn-primary" style="margin-top: 15px;">Track Shipment</button>
                </div>
            </div>

            <div class="recent-activity" style="max-width: 600px; margin-top: 25px;">
                <h3>Shipment Status: #CT-2847</h3>
                <div style="margin-top: 25px;">
                    <div style="position: relative; padding-left: 40px;">
                        <div style="position: absolute; left: 15px; top: 0; bottom: 0; width: 2px; background: #e2e8f0;"></div>
                        
                        <div style="margin-bottom: 30px; position: relative;">
                            <div style="position: absolute; left: -32px; width: 12px; height: 12px; background: #16a34a; border-radius: 50%; border: 3px solid white; box-shadow: 0 0 0 2px #16a34a;"></div>
                            <h4 style="color: #0f172a; margin-bottom: 5px;">In Transit</h4>
                            <p style="color: #64748b; font-size: 14px;">Your package is on the way</p>
                            <span style="color: #94a3b8; font-size: 13px;">2 hours ago</span>
                        </div>

                        <div style="margin-bottom: 30px; position: relative;">
                            <div style="position: absolute; left: -32px; width: 12px; height: 12px; background: #16a34a; border-radius: 50%; border: 3px solid white; box-shadow: 0 0 0 2px #16a34a;"></div>
                            <h4 style="color: #0f172a; margin-bottom: 5px;">Picked Up</h4>
                            <p style="color: #64748b; font-size: 14px;">Package collected from Addis Ababa</p>
                            <span style="color: #94a3b8; font-size: 13px;">5 hours ago</span>
                        </div>

                        <div style="margin-bottom: 30px; position: relative;">
                            <div style="position: absolute; left: -32px; width: 12px; height: 12px; background: #16a34a; border-radius: 50%; border: 3px solid white; box-shadow: 0 0 0 2px #16a34a;"></div>
                            <h4 style="color: #0f172a; margin-bottom: 5px;">Request Approved</h4>
                            <p style="color: #64748b; font-size: 14px;">Your request has been approved</p>
                            <span style="color: #94a3b8; font-size: 13px;">1 day ago</span>
                        </div>

                        <div style="position: relative;">
                            <div style="position: absolute; left: -32px; width: 12px; height: 12px; background: #e2e8f0; border-radius: 50%; border: 3px solid white;"></div>
                            <h4 style="color: #0f172a; margin-bottom: 5px;">Request Submitted</h4>
                            <p style="color: #64748b; font-size: 14px;">Request created successfully</p>
                            <span style="color: #94a3b8; font-size: 13px;">2 days ago</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>
</div>

<?php require_once __DIR__ . '/../layout/footer.php'; ?>
