<?php
header('Content-Type: application/json');
require_once 'config.php';

// Check admin access - for demo purposes, allow all requests
// In a real application, you'd use proper session management
$is_admin = true;

$action = $_GET['action'] ?? $_POST['action'] ?? '';

try {
    switch ($action) {
        case 'list':
            // List all videos
            $stmt = $pdo->query("SELECT id, title, description, url FROM videos ORDER BY id");
            $videos = $stmt->fetchAll(PDO::FETCH_ASSOC);
            echo json_encode(['success' => true, 'videos' => $videos]);
            break;
            
        case 'get':
            // Get single video
            $id = $_GET['id'] ?? 0;
            if (!$id) {
                echo json_encode(['success' => false, 'message' => 'Video ID required']);
                exit;
            }
            
            $stmt = $pdo->prepare("SELECT id, title, description, url FROM videos WHERE id = ?");
            $stmt->execute([$id]);
            $video = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($video) {
                echo json_encode(['success' => true, 'video' => $video]);
            } else {
                echo json_encode(['success' => false, 'message' => 'Video not found']);
            }
            break;
            
        case 'add':
            // Add new video
            $title = $_POST['title'] ?? '';
            $description = $_POST['description'] ?? '';
            $url = $_POST['url'] ?? '';
            
            if (!$title || !$description || !$url) {
                echo json_encode(['success' => false, 'message' => 'Title, description and URL are required']);
                exit;
            }
            
            $stmt = $pdo->prepare("INSERT INTO videos (title, description, url) VALUES (?, ?, ?)");
            $stmt->execute([$title, $description, $url]);
            
            echo json_encode(['success' => true, 'message' => 'Video added successfully']);
            break;
            
        case 'edit':
            // Edit video
            $id = $_POST['id'] ?? 0;
            $title = $_POST['title'] ?? '';
            $description = $_POST['description'] ?? '';
            $url = $_POST['url'] ?? '';
            
            if (!$id || !$title || !$description || !$url) {
                echo json_encode(['success' => false, 'message' => 'ID, title, description and URL are required']);
                exit;
            }
            
            $stmt = $pdo->prepare("UPDATE videos SET title = ?, description = ?, url = ? WHERE id = ?");
            $stmt->execute([$title, $description, $url, $id]);
            
            echo json_encode(['success' => true, 'message' => 'Video updated successfully']);
            break;
            
        case 'delete':
            // Delete video
            $id = $_POST['id'] ?? 0;
            
            if (!$id) {
                echo json_encode(['success' => false, 'message' => 'Video ID required']);
                exit;
            }
            
            $stmt = $pdo->prepare("DELETE FROM videos WHERE id = ?");
            $stmt->execute([$id]);
            
            echo json_encode(['success' => true, 'message' => 'Video deleted successfully']);
            break;
            
        default:
            echo json_encode(['success' => false, 'message' => 'Invalid action']);
            break;
    }
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
}
?> 