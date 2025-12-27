<?php
// cron-cleanup.php - Membersihkan file temp dan session lama
require_once 'config.php';

// 1. Bersihkan file temp uploads yang lebih lama dari 24 jam
$uploadDir = UPLOADS_PATH;
$files = glob($uploadDir . '*');
$now = time();

foreach ($files as $file) {
    if (is_file($file)) {
        if ($now - filemtime($file) >= 24 * 3600) { // 24 jam
            unlink($file);
            echo "Deleted temp file: $file<br>";
        }
    }
}

// 2. Bersihkan customer yang statusnya 'pending' lebih dari 3 hari (belum bayar)
try {
    $stmt = $pdo->prepare("SELECT id, website_folder FROM customers WHERE payment_status = 'pending' AND created_at < (NOW() - INTERVAL 3 DAY)");
    $stmt->execute();
    $expired_customers = $stmt->fetchAll();

    foreach ($expired_customers as $cust) {
        // Hapus folder website
        $folder = SITES_PATH . $cust['website_folder'];
        if (is_dir($folder)) {
            // Recursive delete function would be needed here, simple rmdir for now
            // exec("rm -rf $folder"); // Warning: be careful with system commands
        }
        
        // Hapus dari DB
        $pdo->prepare("DELETE FROM customers WHERE id = ?")->execute([$cust['id']]);
        echo "Deleted expired customer: " . $cust['website_folder'] . "<br>";
    }
} catch (Exception $e) {
    echo "Error cleaning DB: " . $e->getMessage();
}

echo "Cleanup finished at " . date('Y-m-d H:i:s');
?>
