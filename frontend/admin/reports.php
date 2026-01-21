<?php
require_once __DIR__ . '/../../backend/config/session.php';

// Admin only
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: /cargo-project/frontend/auth/login.php");
    exit();
}

require_once __DIR__ . '/../layout/header_admin.php';
?>

<!-- Main content -->
<main class="main-content">
    <header class="topbar">
        <h2><?= __('reports_title') ?? 'Reports' ?></h2>
        <div class="user-info">
            <span><?= ucfirst($_SESSION['role']); ?></span>
        </div>
    </header>

    <div class="content">
        <!-- Controls -->
        <div class="card mb-4">
            <div class="card-body">
                <form id="reportFilter" class="form-inline" style="display: flex; gap: 1rem; flex-wrap: wrap; align-items: end;">
                    <div class="form-group">
                        <label>Report Type</label>
                        <select id="reportType" class="form-control">
                            <option value="system">System Overview</option>
                            <option value="financial">Financial (Money)</option>
                            <option value="user">User Statistics</option>
                            <option value="vehicle">Vehicle Usage</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Start Date</label>
                        <input type="date" id="startDate" class="form-control">
                    </div>
                    <div class="form-group">
                        <label>End Date</label>
                        <input type="date" id="endDate" class="form-control">
                    </div>
                    <button type="submit" class="btn btn-primary">Generate Report</button>
                    
                    <div style="margin-left: auto; display: flex; gap: 0.5rem;">
                        <button type="button" class="btn btn-secondary" onclick="exportPDF()">
                            <i data-feather="download"></i> PDF
                        </button>
                        <button type="button" class="btn btn-secondary" onclick="exportExcel()">
                            <i data-feather="file-text"></i> Excel
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Dynamic Content Container -->
        <div id="reportContent">
            <!-- Default System View -->
            <div class="stats-grid">
                <div class="stat-card">
                    <h3>Revenue</h3>
                    <p class="stat-number" id="totalRevenue">--</p>
                    <div class="stat-trend up">Period Total</div>
                </div>
                
                <div class="stat-card">
                    <h3>New Customers</h3>
                    <p class="stat-number" id="newCustomers">--</p>
                    <div class="stat-trend up">Signups</div>
                </div>

                <div class="stat-card">
                    <h3>New Transporters</h3>
                    <p class="stat-number" id="newTransporters">--</p>
                    <div class="stat-trend up">Signups</div>
                </div>
            </div>

            <div class="card mt-4">
                <div class="card-header">
                    <h3>Request Status Breakdown</h3>
                </div>
                <div class="card-body">
                    <div style="max-height: 400px; position: relative;">
                        <canvas id="mainChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>

<!-- Libraries -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/chart.js/3.9.1/chart.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.5.28/jspdf.plugin.autotable.min.js"></script>
<!-- ExcelJS for advanced export -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/exceljs/4.4.0/exceljs.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/FileSaver.js/2.0.5/FileSaver.min.js"></script>

<script>
    // ... existing init code ...
</script>

<script>
    feather.replace();

    const end = new Date();
    const start = new Date();
    start.setDate(start.getDate() - 30);
    
    document.getElementById('endDate').valueAsDate = end;
    document.getElementById('startDate').valueAsDate = start;

    let currentChart = null;
    let currentData = null; 

    // ... loadReports and updateUI ...

    // Rewritten Excel Export using ExcelJS
    async function exportExcel() {
        if (!currentData) return;
        
        const type = document.getElementById('reportType').value;
        const workbook = new ExcelJS.Workbook();
        const sheet = workbook.addWorksheet('Report');
        
        // --- Styles ---
        const headerStyle = {
            font: { name: 'Arial', family: 4, size: 16, underline: false, bold: true, color: { argb: 'FF10B981' } }
        };
        const subHeaderStyle = {
            font: { name: 'Arial', size: 12, bold: true, color: { argb: 'FF334155' } }
        };
        const tableHeaderStyle = {
            font: { bold: true, color: { argb: 'FFFFFFFF' } },
            fill: { type: 'pattern', pattern: 'solid', fgColor: { argb: 'FF3B82F6' } },
            alignment: { horizontal: 'center' }
        };

        // --- Metadata ---
        sheet.addRow(['CargoConnect Admin Report']).font = headerStyle.font;
        sheet.addRow([`Type: ${type.toUpperCase()}`]).font = subHeaderStyle.font;
        sheet.addRow([`Generated: ${new Date().toLocaleString()}`]);
        sheet.addRow([`Period: ${document.getElementById('startDate').value} to ${document.getElementById('endDate').value}`]);
        sheet.addRow([]); // spacer

        // --- Chart Embedding ---
        const canvas = document.getElementById('mainChart');
        if (canvas) {
            const chartDataUrl = canvas.toDataURL('image/png', 1.0);
            const imageId = workbook.addImage({
                base64: chartDataUrl,
                extension: 'png',
            });
            // Add image to sheet (approx 400x200 pixels)
            sheet.addImage(imageId, {
                tl: { col: 0, row: 5 }, // Top-left anchor
                ext: { width: 500, height: 300 }
            });
            
            // Move cursor down past image
            for(let i=0; i<16; i++) sheet.addRow([]); 
        }

        // --- Data Tables ---
        let rowIndex = sheet.lastRow ? sheet.lastRow.number + 1 : 20;

        if (type === 'system') {
            addStyledRow(sheet, ['Summary Statistics'], subHeaderStyle);
            sheet.addRow(['Metric', 'Value']);
            sheet.addRow(['Revenue', `${Number(currentData.revenue).toLocaleString()} ETB`]);
            sheet.addRow(['New Customers', currentData.new_customers]);
            sheet.addRow(['New Transporters', currentData.new_transporters]);
            sheet.addRow([]);

            addStyledRow(sheet, ['Request Status Breakdown'], subHeaderStyle);
            const headers = ['Status', 'Count'];
            addStyledHeader(sheet, headers, tableHeaderStyle);
            
            const rows = [
                ['Pending', currentData.breakdown.pending || 0],
                ['Approved', currentData.breakdown.approved || 0],
                ['Rejected', currentData.breakdown.rejected || 0],
                ['Completed', currentData.breakdown.completed || 0]
            ];
            rows.forEach(r => sheet.addRow(r));

        } else if (type === 'financial') {
            addStyledRow(sheet, ['Financial Summary'], subHeaderStyle);
            const totalRev = currentData.revenue_over_time.reduce((sum, d) => sum + parseFloat(d.daily_total), 0);
            sheet.addRow(['Total Revenue', `${totalRev.toLocaleString()} ETB`]);
            sheet.addRow([]);

            addStyledRow(sheet, ['Transactions Log'], subHeaderStyle);
            const headers = ['ID', 'Customer', 'Amount', 'Status', 'Date'];
            addStyledHeader(sheet, headers, tableHeaderStyle);
            
            if (currentData.transactions) {
                currentData.transactions.forEach(t => {
                    sheet.addRow([t.id, t.customer, t.price, t.status, new Date(t.updated_at).toLocaleDateString()]);
                });
            }

        } else if (type === 'user') {
            addStyledRow(sheet, ['Top Customers'], subHeaderStyle);
            addStyledHeader(sheet, ['Name', 'Requests', 'Total Spent'], tableHeaderStyle);
            currentData.top_customers.forEach(c => {
                sheet.addRow([c.full_name, c.total_requests, c.total_spent]);
            });
            sheet.addRow([]);

            addStyledRow(sheet, ['Top Transporters'], subHeaderStyle);
            addStyledHeader(sheet, ['Name', 'Deliveries'], tableHeaderStyle);
            currentData.top_transporters.forEach(t => {
                sheet.addRow([t.full_name, t.total_deliveries]);
            });

        } else if (type === 'vehicle') {
            addStyledRow(sheet, ['Vehicle Usage Statistics'], subHeaderStyle);
            addStyledHeader(sheet, ['Vehicle Type', 'Usage Count'], tableHeaderStyle);
            Object.entries(currentData.vehicle_usage || {}).forEach(([v, c]) => {
                sheet.addRow([v, c]);
            });
        }

        // Auto-width columns
        sheet.columns.forEach(column => {
            column.width = 25;
        });

        // Write Buffer
        const buffer = await workbook.xlsx.writeBuffer();
        saveAs(new Blob([buffer]), `CargoConnect_${type}_Report.xlsx`);
    }

    function addStyledRow(sheet, data, style) {
        const row = sheet.addRow(data);
        if (style.font) row.font = style.font;
    }

    function addStyledHeader(sheet, headers, style) {
        const row = sheet.addRow(headers);
        row.eachCell((cell) => {
            cell.font = style.font;
            cell.fill = style.fill;
            cell.alignment = style.alignment;
        });
    }

    document.getElementById('reportFilter').addEventListener('submit', (e) => {
        e.preventDefault();
        loadReports();
    });

    document.getElementById('reportFilter').addEventListener('submit', (e) => {
        e.preventDefault();
        loadReports();
    });

    // Initial Load
    loadReports();

    // Mobile Sidebar
    function toggleSidebar() {
        document.querySelector('.sidebar').classList.toggle('open');
    }
</script>

</body>
</html>
