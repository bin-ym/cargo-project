<?php
// test_reports.php
$_SESSION['user_id'] = 1; // Fake login
$_SESSION['role'] = 'admin';

// Include the API script but we need to prevent it from exiting if we can.
// Or just run it via command line and see output.
// Actually, it's easier to just run: php get_reports_data.php
?>
