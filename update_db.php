<?php
require_once __DIR__ . '/backend/config/database.php';

try {
    $database = new Database();
    $db = $database->getConnection();

    $sql = "ALTER TABLE users ADD COLUMN must_change_password BOOLEAN DEFAULT FALSE AFTER role";
    $db->exec($sql);
    echo "Successfully added must_change_password column to users table.\n";

} catch(PDOException $e) {
    if (strpos($e->getMessage(), "Duplicate column name") !== false) {
        echo "Column already exists.\n";
    } else {
        echo "Error: " . $e->getMessage() . "\n";
    }
}
?>
