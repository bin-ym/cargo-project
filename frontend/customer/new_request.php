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
            <h2>New Cargo Request</h2>
            <div class="user-info">
                <span><?= htmlspecialchars($_SESSION['full_name'] ?? 'Customer') ?></span>
            </div>
        </header>

        <div class="content">
            <div class="recent-activity" style="max-width: 700px;">
                <h3>Request Details</h3>
                <form id="requestForm" style="margin-top: 20px;">
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 20px;">
                        <div>
                            <label style="display: block; margin-bottom: 8px; font-weight: 600; color: #334155;">Pickup Location</label>
                            <input type="text" id="pickup_location" placeholder="Enter pickup location" style="width: 100%; padding: 12px; border: 1px solid #e2e8f0; border-radius: 8px;" required>
                        </div>
                        <div>
                            <label style="display: block; margin-bottom: 8px; font-weight: 600; color: #334155;">Dropoff Location</label>
                            <input type="text" id="dropoff_location" placeholder="Enter dropoff location" style="width: 100%; padding: 12px; border: 1px solid #e2e8f0; border-radius: 8px;" required>
                        </div>
                    </div>
                    <div style="margin-bottom: 20px;">
                        <label style="display: block; margin-bottom: 8px; font-weight: 600; color: #334155;">Pickup Date</label>
                        <input type="date" id="pickup_date" style="width: 100%; padding: 12px; border: 1px solid #e2e8f0; border-radius: 8px;" required>
                    </div>
                    
                    <h4 style="margin: 30px 0 15px; color: #0f172a;">Cargo Items</h4>
                    <div style="margin-bottom: 20px;">
                        <label style="display: block; margin-bottom: 8px; font-weight: 600; color: #334155;">Item Name</label>
                        <input type="text" id="item_name" placeholder="e.g., Electronics" style="width: 100%; padding: 12px; border: 1px solid #e2e8f0; border-radius: 8px;" required>
                    </div>
                    <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 20px; margin-bottom: 20px;">
                        <div>
                            <label style="display: block; margin-bottom: 8px; font-weight: 600; color: #334155;">Quantity</label>
                            <input type="number" id="quantity" value="1" style="width: 100%; padding: 12px; border: 1px solid #e2e8f0; border-radius: 8px;" required>
                        </div>
                        <div>
                            <label style="display: block; margin-bottom: 8px; font-weight: 600; color: #334155;">Weight</label>
                            <input type="text" id="weight" placeholder="e.g., 10kg" style="width: 100%; padding: 12px; border: 1px solid #e2e8f0; border-radius: 8px;">
                        </div>
                        <div>
                            <label style="display: block; margin-bottom: 8px; font-weight: 600; color: #334155;">Category</label>
                            <select id="category" style="width: 100%; padding: 12px; border: 1px solid #e2e8f0; border-radius: 8px;">
                                <option>Electronics</option>
                                <option>Furniture</option>
                                <option>Hardware</option>
                                <option>Other</option>
                            </select>
                        </div>
                    </div>
                    <div style="margin-bottom: 20px;">
                        <label style="display: block; margin-bottom: 8px; font-weight: 600; color: #334155;">Description</label>
                        <textarea id="description" rows="3" placeholder="Additional details..." style="width: 100%; padding: 12px; border: 1px solid #e2e8f0; border-radius: 8px;"></textarea>
                    </div>
                    
                    <button type="submit" class="btn btn-primary">Submit Request</button>
                </form>
            </div>
        </div>

        <script>
            // Set minimum date to today
            const dateInput = document.getElementById('pickup_date');
            const today = new Date().toISOString().split('T')[0];
            dateInput.min = today;

            document.getElementById('requestForm').addEventListener('submit', async (e) => {
                e.preventDefault();
                
                const btn = e.target.querySelector('button[type="submit"]');
                const originalText = btn.innerText;
                btn.innerText = 'Submitting...';
                btn.disabled = true;

                const payload = {
                    pickup_location: document.getElementById('pickup_location').value,
                    dropoff_location: document.getElementById('dropoff_location').value,
                    pickup_date: document.getElementById('pickup_date').value,
                    items: [{
                        item_name: document.getElementById('item_name').value,
                        quantity: document.getElementById('quantity').value,
                        weight: document.getElementById('weight').value,
                        category: document.getElementById('category').value,
                        description: document.getElementById('description').value
                    }]
                };

                try {
                    const response = await fetch('/cargo-project/backend/api/customer/create_request.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json'
                        },
                        body: JSON.stringify(payload)
                    });

                    const result = await response.json();

                    if (result.success) {
                        alert('Request submitted successfully!');
                        window.location.href = 'dashboard.php'; // Redirect to dashboard or requests list
                    } else {
                        alert('Error: ' + result.error);
                    }
                } catch (error) {
                    console.error('Error:', error);
                    alert('An error occurred while submitting the request.');
                } finally {
                    btn.innerText = originalText;
                    btn.disabled = false;
                }
            });
        </script>
    </main>
</div>

<?php require_once __DIR__ . '/../layout/footer.php'; ?>
