<?php
// ajax.php - Handle AJAX requests
require_once '../config.php';

header('Content-Type: application/json');

$action = $_GET['action'] ?? '';

switch ($action) {
    case 'submit_rsvp':
        $data = json_decode(file_get_contents('php://input'), true);
        
        $stmt = $pdo->prepare("INSERT INTO rsvp 
            (website_id, guest_name, email, attendance, guest_count, message) 
            VALUES (?, ?, ?, ?, ?, ?)");
        
        $success = $stmt->execute([
            $data['website_id'],
            sanitize($data['name']),
            sanitize($data['email']),
            $data['attendance'],
            $data['guest_count'],
            sanitize($data['message'])
        ]);
        
        echo json_encode(['success' => $success, 'message' => 'RSVP submitted']);
        break;
        
    case 'submit_guestbook':
        $data = json_decode(file_get_contents('php://input'), true);
        
        $stmt = $pdo->prepare("INSERT INTO guestbook 
            (website_id, author_name, message) 
            VALUES (?, ?, ?)");
        
        $success = $stmt->execute([
            $data['website_id'],
            sanitize($data['name']),
            sanitize($data['message'])
        ]);
        
        echo json_encode(['success' => $success, 'message' => 'Guestbook submitted']);
        break;
        
    default:
        echo json_encode(['success' => false, 'message' => 'Invalid action']);
}
?>
