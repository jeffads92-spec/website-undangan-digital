<?php
// ajax.php - Handler Pusat untuk Frontend & Backend
require_once 'config.php';

// Set header JSON
header('Content-Type: application/json');

// Ambil action
$action = $_GET['action'] ?? $_POST['action'] ?? '';

// Start session jika belum
if (session_status() === PHP_SESSION_NONE) session_start();

try {
    switch ($action) {
        // --- PUBLIC ACTIONS (Tanpa Login) ---
        
        case 'submit_rsvp':
            $input = json_decode(file_get_contents('php://input'), true);
            if (!$input) throw new Exception("Invalid input data");

            $stmt = $pdo->prepare("INSERT INTO rsvp (website_id, guest_name, email, phone, attendance, guest_count, message) VALUES (?, ?, ?, ?, ?, ?, ?)");
            $stmt->execute([
                $input['website_id'],
                sanitize($input['name']),
                sanitize($input['email']),
                sanitize($input['phone']),
                $input['attendance'],
                $input['guestCount'],
                sanitize($input['message'])
            ]);
            echo json_encode(['success' => true, 'message' => 'Terima kasih, konfirmasi Anda telah diterima.']);
            break;

        case 'submit_guestbook':
            $input = json_decode(file_get_contents('php://input'), true);
            if (!$input) throw new Exception("Invalid input data");

            // Cek spam/bad words sederhana
            if (strlen($input['message']) < 2) throw new Exception("Pesan terlalu pendek");

            $stmt = $pdo->prepare("INSERT INTO guestbook (website_id, author_name, message, approved) VALUES (?, ?, ?, 1)");
            $stmt->execute([
                $input['website_id'],
                sanitize($input['name']),
                sanitize($input['message'])
            ]);
            echo json_encode(['success' => true, 'message' => 'Ucapan berhasil dikirim.']);
            break;

        // --- ADMIN ACTIONS (Wajib Login) ---
        
        case 'delete_rsvp':
            checkAdminAuth();
            $id = $_GET['id'];
            $stmt = $pdo->prepare("DELETE FROM rsvp WHERE id = ? AND website_id = ?");
            $stmt->execute([$id, $_SESSION['customer_id']]);
            echo json_encode(['success' => true]);
            break;

        case 'delete_message':
            checkAdminAuth();
            $id = $_GET['id'];
            $stmt = $pdo->prepare("DELETE FROM guestbook WHERE id = ? AND website_id = ?");
            $stmt->execute([$id, $_SESSION['customer_id']]);
            echo json_encode(['success' => true]);
            break;

        case 'delete_photo':
            checkAdminAuth();
            $id = $_GET['id'];
            
            // Ambil path file dulu untuk dihapus
            $stmt = $pdo->prepare("SELECT image_path FROM gallery WHERE id = ? AND website_id = ?");
            $stmt->execute([$id, $_SESSION['customer_id']]);
            $photo = $stmt->fetch();
            
            if ($photo) {
                $filepath = SITES_PATH . $_SESSION['website_folder'] . '/' . $photo['image_path'];
                if (file_exists($filepath)) unlink($filepath);
                
                $pdo->prepare("DELETE FROM gallery WHERE id = ?")->execute([$id]);
            }
            echo json_encode(['success' => true]);
            break;

        case 'upload_gallery':
            checkAdminAuth();
            
            if (!isset($_FILES['images'])) throw new Exception("Tidak ada file yang dipilih");
            
            $website_id = $_POST['website_id'];
            $caption = $_POST['caption'] ?? '';
            $folder = $_SESSION['website_folder'];
            $target_dir = SITES_PATH . $folder . "/images/gallery/";
            
            if (!file_exists($target_dir)) mkdir($target_dir, 0755, true);
            
            $uploaded = 0;
            foreach($_FILES['images']['tmp_name'] as $key => $tmp_name) {
                $file_name = time() . '_' . $_FILES['images']['name'][$key];
                $target_file = $target_dir . $file_name;
                $db_path = "images/gallery/" . $file_name;
                
                if (move_uploaded_file($tmp_name, $target_file)) {
                    $stmt = $pdo->prepare("INSERT INTO gallery (website_id, image_path, caption) VALUES (?, ?, ?)");
                    $stmt->execute([$website_id, $db_path, $caption]);
                    $uploaded++;
                }
            }
            
            echo json_encode(['success' => true, 'message' => "$uploaded foto berhasil diupload"]);
            break;

        case 'save_settings':
            checkAdminAuth();
            $input = json_decode(file_get_contents('php://input'), true);
            $website_id = $input['website_id'];
            $settings = $input['settings'];
            
            // Hapus setting lama dan insert baru (simplifikasi)
            $pdo->prepare("DELETE FROM website_settings WHERE website_id = ?")->execute([$website_id]);
            
            $stmt = $pdo->prepare("INSERT INTO website_settings (website_id, setting_key, setting_value) VALUES (?, ?, ?)");
            foreach ($settings as $key => $val) {
                $valStr = $val ? '1' : '0';
                $stmt->execute([$website_id, $key, $valStr]);
            }
            echo json_encode(['success' => true]);
            break;
            
        case 'backup_data':
            checkAdminAuth();
            // Redirect logic handled in admin.php link, but if called via AJAX:
            echo json_encode(['success' => false, 'message' => 'Gunakan link langsung untuk download backup']);
            break;

        default:
            throw new Exception("Action '$action' tidak dikenal");
    }
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}

// Helper untuk cek login admin
function checkAdminAuth() {
    if (!isset($_SESSION['customer_id'])) {
        echo json_encode(['success' => false, 'message' => 'Unauthorized']);
        exit;
    }
}
?>
