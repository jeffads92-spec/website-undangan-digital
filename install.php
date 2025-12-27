<?php
// install.php - Database Setup Automation
require_once 'config.php';

echo "<h1>System Installation</h1>";

try {
    // 1. Koneksi tanpa nama database dulu untuk create DB jika belum ada
    $pdo_root = new PDO("mysql:host=".DB_HOST, DB_USER, DB_PASS);
    $pdo_root->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    $dbname = DB_NAME;
    $pdo_root->exec("CREATE DATABASE IF NOT EXISTS `$dbname` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
    echo "<p style='color:green'>✅ Database '$dbname' checked/created.</p>";
    
    // 2. Koneksi ke database yang baru dibuat
    $pdo = new PDO("mysql:host=".DB_HOST.";dbname=".DB_NAME, DB_USER, DB_PASS);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // 3. Array Struktur Tabel
    $tables = [
        "customers" => "CREATE TABLE IF NOT EXISTS customers (
            id INT PRIMARY KEY AUTO_INCREMENT,
            website_folder VARCHAR(100) UNIQUE,
            customer_name VARCHAR(100),
            email VARCHAR(100),
            phone VARCHAR(20),
            groom_name VARCHAR(100),
            bride_name VARCHAR(100),
            wedding_date DATETIME,
            location_name VARCHAR(200),
            location_address TEXT,
            location_lat DECIMAL(10,8),
            location_lng DECIMAL(11,8),
            package_type ENUM('basic','premium','platinum'),
            payment_status ENUM('pending','paid','expired') DEFAULT 'pending',
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            password_hash VARCHAR(255),
            admin_token VARCHAR(100)
        )",
        "rsvp" => "CREATE TABLE IF NOT EXISTS rsvp (
            id INT PRIMARY KEY AUTO_INCREMENT,
            website_id INT,
            guest_name VARCHAR(100),
            email VARCHAR(100),
            phone VARCHAR(20),
            attendance ENUM('yes','no','maybe'),
            guest_count INT DEFAULT 1,
            message TEXT,
            submitted_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (website_id) REFERENCES customers(id) ON DELETE CASCADE
        )",
        "guestbook" => "CREATE TABLE IF NOT EXISTS guestbook (
            id INT PRIMARY KEY AUTO_INCREMENT,
            website_id INT,
            author_name VARCHAR(100),
            message TEXT,
            approved BOOLEAN DEFAULT 1,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (website_id) REFERENCES customers(id) ON DELETE CASCADE
        )",
        "gallery" => "CREATE TABLE IF NOT EXISTS gallery (
            id INT PRIMARY KEY AUTO_INCREMENT,
            website_id INT,
            image_path VARCHAR(255),
            caption VARCHAR(200),
            display_order INT DEFAULT 0,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (website_id) REFERENCES customers(id) ON DELETE CASCADE
        )",
        "music" => "CREATE TABLE IF NOT EXISTS music (
        id INT PRIMARY KEY AUTO_INCREMENT,
        website_id INT,
        song_title VARCHAR(100),
        artist VARCHAR(100),
        file_path VARCHAR(255),
        is_active BOOLEAN DEFAULT 1,
        FOREIGN KEY (website_id) REFERENCES customers(id) ON DELETE CASCADE
        )",
        "website_settings" => "CREATE TABLE IF NOT EXISTS website_settings (
            id INT PRIMARY KEY AUTO_INCREMENT,
            website_id INT,
            setting_key VARCHAR(50),
            setting_value TEXT,
            FOREIGN KEY (website_id) REFERENCES customers(id) ON DELETE CASCADE
        )"
    ];

    // 4. Eksekusi Create Table
    foreach ($tables as $name => $sql) {
        $pdo->exec($sql);
        echo "<p style='color:green'>✅ Table '$name' ready.</p>";
    }

    echo "<h3>Installation Complete!</h3>";
    echo "<a href='index.php'>Go to Homepage</a>";

} catch (PDOException $e) {
    echo "<p style='color:red'>❌ Error: " . $e->getMessage() . "</p>";
}
?>
