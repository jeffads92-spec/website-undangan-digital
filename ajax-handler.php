<?php
// File: ajax-handler.php
require_once 'config.php';

header('Content-Type: application/json');

$action = $_POST['action'] ?? $_GET['action'] ?? '';

switch ($action) {
    case 'submit_rsvp':
        // Handle RSVP submission
        break;
        
    case 'submit_guestbook':
        // Handle guestbook submission
        break;
        
    case 'upload_gallery':
        // Handle gallery upload
        break;
        
    case 'get_rsvp_stats':
        // Get RSVP statistics
        break;
        
    default:
        echo json_encode(['success' => false, 'message' => 'Invalid action']);
}
?>
