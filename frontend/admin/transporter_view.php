<?php require_once __DIR__ . '/../layout/header_admin.php'; ?>

<div class="dashboard">
    <?php include 'sidebar.php'; ?>

    <main class="main-content">
    
        <header class="topbar">
            <h2>Transporter Details</h2>
        </header>

        <div class="content">
            <div class="details-box">
                <h3>Transporter Information</h3>
                <p><strong>Name:</strong> Bekele Transport</p>
                <p><strong>Email:</strong> bekele@gmail.com</p>
                <p><strong>Phone:</strong> 0911223344</p>
                <p><strong>Status:</strong> <span class="badge approved">Active</span></p>

                <h3 style="margin-top:20px;">Vehicle</h3>
                <p><strong>Type:</strong> Isuzu FSR</p>
                <p><strong>Plate:</strong> ABC-12345</p>
                <p><strong>Capacity:</strong> 3000kg</p>

                <div class="actions">
                    <button class="btn btn-danger">Suspend</button>
                </div>
            </div>

            <h3 style="margin-top:20px;">Assigned Deliveries</h3>

            <div class="table-wrapper">
                <table class="table-modern">
                    <tbody>
                        <tr>
                            <td>#77</td>
                            <td>Addis Ababa</td>
                            <td>Jimma</td>
                            <td><span class="badge pending">In Transit</span></td>
                            <td><a class="btn-small">Open</a></td>
                        </tr>
                    </tbody>
                </table>
            </div>

        </div>

    </main>
</div>

<?php require_once __DIR__ . '/../layout/footer.php'; ?>