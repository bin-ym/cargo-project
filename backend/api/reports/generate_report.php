<?php
require_once __DIR__ . '/../../config/session.php';
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../../vendor/autoload.php'; // Composer autoload for TCPDF

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    die("Access denied");
}

$db = Database::getConnection();
$reportType = $_GET['report_type'] ?? '';

$pdf = new TCPDF();
$pdf->AddPage();
$pdf->SetFont('helvetica', '', 12);

$html = '';

// Helper function for alternating row colors
$rowColor = ['#f6f6f6', '#ffffff'];
$rowIndex = 0;

switch ($reportType) {

    case 'users':
        $stmt = $db->query("SELECT id, username, email, role, created_at FROM users");
        $users = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $html .= '<h2 style="text-align:center;">Users Report</h2>';
        $html .= '<table cellpadding="6" cellspacing="0" style="width:100%; border:1px solid #ccc;">';
        $html .= '<tr style="background-color:#4CAF50; color:white; text-align:center;">
                    <th>ID</th><th>Username</th><th>Email</th><th>Role</th><th>Created</th>
                  </tr>';
        foreach ($users as $u) {
            $bg = $rowColor[$rowIndex % 2];
            $html .= "<tr style='background-color:$bg;'>
                        <td align='center'>{$u['id']}</td>
                        <td>{$u['username']}</td>
                        <td>{$u['email']}</td>
                        <td>{$u['role']}</td>
                        <td>{$u['created_at']}</td>
                      </tr>";
            $rowIndex++;
        }
        $html .= '</table>';
        break;

    case 'requests':
        $stmt = $db->query("
            SELECT cr.id, u.full_name AS customer, cr.pickup_location, cr.dropoff_location, cr.price, cr.status, cr.created_at
            FROM cargo_requests cr
            JOIN users u ON cr.customer_id = u.id
        ");
        $requests = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $html .= '<h2 style="text-align:center;">Cargo Requests Report</h2>';
        $html .= '<table cellpadding="6" cellspacing="0" style="width:100%; border:1px solid #ccc;">';
        $html .= '<tr style="background-color:#4CAF50; color:white; text-align:center;">
                    <th>#</th><th>Customer</th><th>Pickup</th><th>Dropoff</th><th>Price</th><th>Status</th><th>Created</th>
                  </tr>';
        foreach ($requests as $i => $r) {
            $bg = $rowColor[$rowIndex % 2];
            $html .= "<tr style='background-color:$bg;'>
                        <td align='center'>".($i+1)."</td>
                        <td>{$r['customer']}</td>
                        <td>{$r['pickup_location']}</td>
                        <td>{$r['dropoff_location']}</td>
                        <td align='right'>".number_format($r['price'], 2)." ETB</td>
                        <td align='center'>{$r['status']}</td>
                        <td align='center'>{$r['created_at']}</td>
                      </tr>";
            $rowIndex++;
        }
        $html .= '</table>';
        break;

    case 'revenue':
        $total = $db->query("SELECT SUM(price) FROM cargo_requests WHERE status='approved'")->fetchColumn();
        $html .= '<h2 style="text-align:center;">Revenue Report</h2>';
        $html .= "<p style='font-size:14pt; text-align:center;'>Total Approved Revenue: <b>".number_format($total,2)." ETB</b></p>";
        break;

    case 'shipments':
        $stmt = $db->query("SELECT * FROM shipments");
        $shipments = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $html .= '<h2 style="text-align:center;">Shipments Report</h2>';
        $html .= '<table cellpadding="6" cellspacing="0" style="width:100%; border:1px solid #ccc;">';
        $html .= '<tr style="background-color:#4CAF50; color:white; text-align:center;">
                    <th>ID</th><th>Request ID</th><th>Transporter ID</th><th>Status</th><th>Assigned At</th>
                  </tr>';
        foreach ($shipments as $s) {
            $bg = $rowColor[$rowIndex % 2];
            $html .= "<tr style='background-color:$bg;'>
                        <td align='center'>{$s['id']}</td>
                        <td align='center'>{$s['request_id']}</td>
                        <td align='center'>{$s['transporter_id']}</td>
                        <td align='center'>{$s['status']}</td>
                        <td align='center'>{$s['assigned_at']}</td>
                      </tr>";
            $rowIndex++;
        }
        $html .= '</table>';
        break;

    default:
        die("Invalid report type");
}

$pdf->writeHTML($html, true, false, true, false, '');
$pdf->Output("report_{$reportType}.pdf",'I');