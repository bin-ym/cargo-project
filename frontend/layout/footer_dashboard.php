    </div> <!-- .dashboard-grid -->

<footer class="dashboard-footer">
    <div class="footer-card">
        <div class="footer-inner">
            <div class="footer-brand">
                <div class="footer-logo">CT</div>
                <h4>Cargo Transport</h4>
            </div>

            <div class="footer-socials">
                <a href="#"><i data-feather="facebook"></i></a>
                <a href="#"><i data-feather="twitter"></i></a>
                <a href="#"><i data-feather="instagram"></i></a>
                <a href="#"><i data-feather="linkedin"></i></a>
            </div>

            <div class="footer-copy">
                &copy; <?= date('Y') ?> Cargo Transport. All rights reserved.
            </div>
        </div>
    </div>
</footer>

<script>
    feather.replace();

    // Sidebar toggle for mobile
    function toggleSidebar() {
        document.getElementById('sidebar').classList.toggle('open');
    }

    // Sidebar dropdowns
    document.querySelectorAll(".menu-dropdown").forEach(btn => {
        btn.addEventListener("click", () => {
            btn.parentElement.classList.toggle("open");
        });
    });
</script>
</body>
</html>