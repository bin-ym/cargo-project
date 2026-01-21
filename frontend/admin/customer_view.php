<?php 
session_start();
require_once __DIR__ . '/../../backend/config/session.php';
require_once __DIR__ . '/../layout/header_admin.php';
$customerId = $_GET['id'] ?? 0;
if ($customerId && !is_numeric($customerId)) {
    $customerId = Security::decryptId($customerId);
}
if (!$customerId) {
    header("Location: customers.php");
    exit();
}
?>

<div class="dashboard">
    <main class="main-content">

        <header class="topbar"><h2>Customer Details</h2></header>

        <div class="content">

            <div class="details-box">
                <h3>Customer Information</h3>
                <p><strong>Name:</strong> John Doe</p>
                <p><strong>Email:</strong> john@gmail.com</p>
                <p><strong>Phone:</strong> 0912345678</p>
                <p><strong>Status:</strong> <span class="badge approved">Active</span></p>

                <div class="actions">
                    <button class="btn btn-danger">Deactivate</button>
                </div>
            </div>

            <h3 style="margin-top:20px;">Customer Orders</h3>

            <div class="table-wrapper">
                <table class="table-modern">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>From</th>
                            <th>To</th>
                            <th>Status</th>
                            <th>View</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>#101</td>
                            <td>Addis Ababa</td>
                            <td>Gondar</td>
                            <td><span class="badge pending">Pending</span></td>
                            <td><a href="view_request.php?id=101" class="btn-small">Open</a></td>
                        </tr>
                    </tbody>
                </table>
            </div>
            
            
        </div>

    </main>
</div>