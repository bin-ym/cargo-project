<?php
require_once __DIR__ . '/../../backend/config/session.php';

// Admin only
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: /cargo-project/frontend/auth/login.php");
    exit();
}

require_once __DIR__ . '/../layout/header_admin.php';
?>

<style>
    /* Form Message styles */
    .form-message {
        padding: 12px 16px;
        border-radius: 8px;
        font-size: 14px;
        display: none;
        line-height: 1.5;
        margin-bottom: 20px;
        width: 100%;
    }
    .form-message.error {
        background-color: #fef2f2;
        color: #991b1b;
        border: 1px solid #fecaca;
        display: block;
    }
    .form-message.success {
        background-color: #f0fdf4;
        color: #166534;
        border: 1px solid #bbf7d0;
        display: block;
    }
</style>

<!-- Main content -->
<main class="main-content">
    <header class="topbar">
        <h2><?= __('reports_title') ?></h2>
        <div class="user-info">
            <span><?= ucfirst($_SESSION['role']); ?></span>
        </div>
    </header>

    <div class="content">
        <div id="reportMessage" class="form-message"></div>
        <!-- Controls -->
        <div class="card mb-4">
            <div class="card-body">
                <form id="reportFilter" class="form-inline" style="display: flex; gap: 1rem; flex-wrap: wrap; align-items: end;">
                    <div class="form-group">
                        <label><?= __('report_type') ?></label>
                        <select id="reportType" class="form-control">
                            <option value="system"><?= __('system_overview') ?></option>
                            <option value="financial"><?= __('financial_report') ?></option>
                            <option value="user"><?= __('user_statistics') ?></option>
                            <option value="vehicle"><?= __('vehicle_usage') ?></option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label><?= __('start_date') ?></label>
                        <input type="date" id="startDate" class="form-control">
                    </div>
                    <div class="form-group">
                        <label><?= __('end_date') ?></label>
                        <input type="date" id="endDate" class="form-control">
                    </div>
                    <button type="submit" class="btn btn-primary"><?= __('generate_report') ?></button>
                    
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
                    <h3><?= __('revenue') ?></h3>
                    <p class="stat-number" id="totalRevenue">--</p>
                    <div class="stat-trend up"><?= __('period_total') ?></div>
                </div>
                
                <div class="stat-card">
                    <h3><?= __('customers') ?></h3>
                    <p class="stat-number" id="newCustomers">--</p>
                    <div class="stat-trend up"><?= __('signups') ?></div>
                </div>
 
                <div class="stat-card">
                    <h3><?= __('transporters') ?></h3>
                    <p class="stat-number" id="newTransporters">--</p>
                    <div class="stat-trend up"><?= __('signups') ?></div>
                </div>
            </div>
 
            <div class="card mt-4">
                <div class="card-header">
                    <h3><?= __('request_status_breakdown') ?></h3>
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
<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/3.9.1/chart.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.5.28/jspdf.plugin.autotable.min.js"></script>
<!-- ExcelJS for advanced export -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/exceljs/4.4.0/exceljs.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/FileSaver.js/2.0.5/FileSaver.min.js"></script>

<script>
    function showReportMessage(msg, type = 'error') {
        const el = document.getElementById('reportMessage');
        el.textContent = msg;
        el.className = 'form-message ' + type;
        el.style.display = 'block';
    }

    function clearReportMessage() {
        document.getElementById('reportMessage').style.display = 'none';
    }

    async function loadReports() {
        const type = document.getElementById('reportType').value;
        const start = document.getElementById('startDate').value;
        const end = document.getElementById('endDate').value;

        try {
            clearReportMessage();
            const res = await fetch(`../../backend/api/admin/get_reports_data.php?type=${type}&start_date=${start}&end_date=${end}`);
            
            if (!res.ok) {
                const text = await res.text();
                let errMsg = `Error ${res.status}: `;
                try {
                    const errorJson = JSON.parse(text);
                    errMsg += errorJson.error || res.statusText;
                } catch(e) {
                    errMsg += res.statusText || 'Server error';
                }
                showReportMessage(errMsg);
                return;
            }

            const json = await res.json();
            if (json.success) {
                currentData = json.data;
                if (typeof Chart === 'undefined') {
                    showReportMessage('Chart.js library failed to load. Please check your internet connection.');
                    return;
                }
                updateUI(type, json.data);
            } else {
                showReportMessage(json.error || 'Failed to load report data');
            }
        } catch (err) {
            console.error(err);
            showReportMessage('Error processing report data: ' + err.message);
        }
    }

    function updateUI(type, data) {
        const content = document.getElementById('reportContent');
        
        // Update Stats Bar (if applicable)
        if (type === 'system') {
            document.getElementById('totalRevenue').innerText = `${Number(data.revenue).toLocaleString()} ETB`;
            document.getElementById('newCustomers').innerText = data.new_customers;
            document.getElementById('newTransporters').innerText = data.new_transporters;
        }

        // Handle Chart
        const ctx = document.getElementById('mainChart').getContext('2d');
        if (currentChart) currentChart.destroy();

        let chartConfig = {
            type: 'bar',
            data: {
                labels: [],
                datasets: [{
                    label: 'Count',
                    data: [],
                    backgroundColor: '#3b82f6'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false
            }
        };

        if (type === 'system') {
            chartConfig.data.labels = data.breakdown ? Object.keys(data.breakdown) : [];
            chartConfig.data.datasets[0].data = data.breakdown ? Object.values(data.breakdown) : [];
            chartConfig.data.datasets[0].label = 'Request Status';
            chartConfig.data.datasets[0].backgroundColor = ['#ef4444', '#22c55e', '#f59e0b', '#3b82f6'];
        } else if (type === 'financial') {
            chartConfig.type = 'line';
            chartConfig.data.labels = (data.revenue_over_time || []).map(d => d.date);
            chartConfig.data.datasets[0].data = (data.revenue_over_time || []).map(d => d.daily_total);
            chartConfig.data.datasets[0].label = 'Daily Revenue (ETB)';
            chartConfig.data.datasets[0].borderColor = '#10b981';
            chartConfig.data.datasets[0].fill = true;
            chartConfig.data.datasets[0].backgroundColor = 'rgba(16, 185, 129, 0.1)';
        } else if (type === 'user') {
            chartConfig.data.labels = (data.top_customers || []).map(c => c.full_name);
            chartConfig.data.datasets[0].data = (data.top_customers || []).map(c => c.total_spent);
            chartConfig.data.datasets[0].label = 'Top Customers Spends';
        } else if (type === 'vehicle') {
            chartConfig.type = 'pie';
            chartConfig.data.labels = data.vehicle_usage ? Object.keys(data.vehicle_usage) : [];
            chartConfig.data.datasets[0].data = data.vehicle_usage ? Object.values(data.vehicle_usage) : [];
            chartConfig.data.datasets[0].backgroundColor = ['#3b82f6', '#10b981', '#f59e0b', '#ef4444', '#8b5cf6'];
        }

        currentChart = new Chart(ctx, chartConfig);
    }

    async function exportPDF() {
        if (!currentData) return;
        const { jsPDF } = window.jspdf;
        const doc = new jsPDF();
        const type = document.getElementById('reportType').value;

        doc.setFontSize(20);
        doc.text("CargoConnect Admin Report", 14, 22);
        doc.setFontSize(12);
        doc.text(`Type: ${type.toUpperCase()}`, 14, 30);
        doc.text(`Period: ${document.getElementById('startDate').value} to ${document.getElementById('endDate').value}`, 14, 38);

        // Add Chart Image
        // Add Chart Image (with white background fix)
        const canvas = document.getElementById('mainChart');
        
        // Create a temporary canvas to draw the chart with a solid background
        const tempCanvas = document.createElement('canvas');
        tempCanvas.width = canvas.width;
        tempCanvas.height = canvas.height;
        const tempCtx = tempCanvas.getContext('2d');
        tempCtx.fillStyle = '#ffffff';
        tempCtx.fillRect(0, 0, tempCanvas.width, tempCanvas.height);
        tempCtx.drawImage(canvas, 0, 0);
        
        const imgData = tempCanvas.toDataURL('image/png');
        doc.addImage(imgData, 'PNG', 15, 45, 180, 100);

        // AutoTable for data
        let tableData = [];
        let headers = [];

        if (type === 'system') {
            headers = [["Status", "Count"]];
            tableData = Object.entries(currentData.breakdown);
        } else if (type === 'financial') {
            headers = [["Date", "Revenue (ETB)"]];
            tableData = currentData.revenue_over_time.map(d => [d.date, d.daily_total]);
        } else if (type === 'user') {
            headers = [["Name", "Requests", "Spent"]];
            tableData = currentData.top_customers.map(c => [c.full_name, c.total_requests, c.total_spent]);
        } else if (type === 'vehicle') {
            headers = [["Vehicle", "Usage"]];
            tableData = Object.entries(currentData.vehicle_usage);
        }

        doc.autoTable({
            startY: 155,
            head: headers,
            body: tableData,
        });

        doc.save(`CargoConnect_${type}_Report.pdf`);
    }
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

    // Initial Load via loadReports() triggered at bottom

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
        sheet.addRow(["<?= __('reports_title') ?>"]).font = headerStyle.font;
        sheet.addRow([`<?= __('report_type') ?>: ${type.toUpperCase()}`]).font = subHeaderStyle.font;
        sheet.addRow([`<?= __('generated') ?>: ${new Date().toLocaleString()}`]);
        sheet.addRow([`<?= __('period') ?>: ${document.getElementById('startDate').value} <?= __('to') ?> ${document.getElementById('endDate').value}`]);
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
            addStyledRow(sheet, ["<?= __('summary_statistics') ?>"], subHeaderStyle);
            sheet.addRow(["<?= __('metric') ?>", "<?= __('value') ?>"]);
            sheet.addRow(["<?= __('revenue') ?>", `${Number(currentData.revenue).toLocaleString()} ETB`]);
            sheet.addRow(["<?= __('customers') ?>", currentData.new_customers]);
            sheet.addRow(["<?= __('transporters') ?>", currentData.new_transporters]);
            sheet.addRow([]);

            addStyledRow(sheet, ["<?= __('request_status_breakdown') ?>"], subHeaderStyle);
            const headers = ["<?= __('status') ?>", "<?= __('count') ?>"];
            addStyledHeader(sheet, headers, tableHeaderStyle);
            
            const rows = [
                ['Pending', currentData.breakdown.pending || 0],
                ['Approved', currentData.breakdown.approved || 0],
                ['Rejected', currentData.breakdown.rejected || 0],
                ['Completed', currentData.breakdown.completed || 0]
            ];
            rows.forEach(r => sheet.addRow(r));

        } else if (type === 'financial') {
            addStyledRow(sheet, ["<?= __('financial_summary') ?>"], subHeaderStyle);
            const totalRev = currentData.revenue_over_time.reduce((sum, d) => sum + parseFloat(d.daily_total), 0);
            sheet.addRow(["<?= __('revenue') ?> (<?= __('total') ?>)", `${totalRev.toLocaleString()} ETB`]);
            sheet.addRow([]);

            addStyledRow(sheet, ["<?= __('transactions_log') ?>"], subHeaderStyle);
            const headers = ["<?= __('id') ?>", "<?= __('customer') ?>", "<?= __('amount') ?>", "<?= __('status') ?>", "<?= __('date') ?>"];
            addStyledHeader(sheet, headers, tableHeaderStyle);
            
            if (currentData.transactions) {
                currentData.transactions.forEach(t => {
                    sheet.addRow([t.id, t.customer, t.price, t.status, new Date(t.updated_at).toLocaleDateString()]);
                });
            }

        } else if (type === 'user') {
            addStyledRow(sheet, ["<?= __('top_customers') ?>"], subHeaderStyle);
            addStyledHeader(sheet, ["<?= __('full_name') ?>", "<?= __('requests') ?>", "<?= __('total_spent') ?>"], tableHeaderStyle);
            currentData.top_customers.forEach(c => {
                sheet.addRow([c.full_name, c.total_requests, c.total_spent]);
            });
            sheet.addRow([]);

            addStyledRow(sheet, ["<?= __('top_transporters') ?>"], subHeaderStyle);
            addStyledHeader(sheet, ["<?= __('full_name') ?>", "<?= __('deliveries') ?>"], tableHeaderStyle);
            currentData.top_transporters.forEach(t => {
                sheet.addRow([t.full_name, t.total_deliveries]);
            });

        } else if (type === 'vehicle') {
            addStyledRow(sheet, ["<?= __('vehicle_usage_stats') ?>"], subHeaderStyle);
            addStyledHeader(sheet, ["<?= __('vehicle_type') ?>", "<?= __('usage_count') ?>"], tableHeaderStyle);
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

    // Initial Load
    loadReports();

    // Mobile Sidebar
    function toggleSidebar() {
        document.querySelector('.sidebar').classList.toggle('open');
    }
</script>

</body>
</html>
