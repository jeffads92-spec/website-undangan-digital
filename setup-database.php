<?php
// File: setup-database.php
require_once 'config.php';

$sql = "
-- Jalankan SQL untuk buat tabel
-- (Ambil dari kode sebelumnya yang ada CREATE TABLE)

-- Setelah buat tabel, tambahkan dummy data untuk testing
INSERT INTO templates (name, folder_path, thumbnail) 
VALUES ('Classic Wedding', 'templates/classic/', 'templates/classic/thumb.jpg');
";

try {
    $pdo->exec($sql);
    echo "Database setup berhasil!";
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?>
