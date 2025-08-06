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
            // List all modules
            $stmt = $pdo->query("SELECT id, title, description, ppt_content, video_content FROM modules ORDER BY id");
            $modules = $stmt->fetchAll(PDO::FETCH_ASSOC);
            echo json_encode(['success' => true, 'modules' => $modules]);
            break;
            
        case 'get':
            // Get single module
            $id = $_GET['id'] ?? 0;
            if (!$id) {
                echo json_encode(['success' => false, 'message' => 'Module ID required']);
                exit;
            }
            
            $stmt = $pdo->prepare("SELECT id, title, description, ppt_content, video_content FROM modules WHERE id = ?");
            $stmt->execute([$id]);
            $module = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($module) {
                echo json_encode(['success' => true, 'module' => $module]);
            } else {
                echo json_encode(['success' => false, 'message' => 'Module not found']);
            }
            break;
            
        case 'add':
            // Add new module
            $title = $_POST['title'] ?? '';
            $description = $_POST['description'] ?? '';
            $ppt_content = $_POST['ppt_content'] ?? '';
            $video_content = $_POST['video_content'] ?? '';
            
            if (!$title || !$description) {
                echo json_encode(['success' => false, 'message' => 'Title and description are required']);
                exit;
            }
            
            $stmt = $pdo->prepare("INSERT INTO modules (title, description, ppt_content, video_content) VALUES (?, ?, ?, ?)");
            $stmt->execute([$title, $description, $ppt_content, $video_content]);
            
            echo json_encode(['success' => true, 'message' => 'Module added successfully']);
            break;
            
        case 'edit':
            // Edit module
            $id = $_POST['id'] ?? 0;
            $title = $_POST['title'] ?? '';
            $description = $_POST['description'] ?? '';
            $ppt_content = $_POST['ppt_content'] ?? '';
            $video_content = $_POST['video_content'] ?? '';
            
            if (!$id || !$title || !$description) {
                echo json_encode(['success' => false, 'message' => 'ID, title and description are required']);
                exit;
            }
            
            $stmt = $pdo->prepare("UPDATE modules SET title = ?, description = ?, ppt_content = ?, video_content = ? WHERE id = ?");
            $stmt->execute([$title, $description, $ppt_content, $video_content, $id]);
            
            echo json_encode(['success' => true, 'message' => 'Module updated successfully']);
            break;
            
        case 'delete':
            // Delete module
            $id = $_POST['id'] ?? 0;
            
            if (!$id) {
                echo json_encode(['success' => false, 'message' => 'Module ID required']);
                exit;
            }
            
            // First delete related user progress
            $stmt = $pdo->prepare("DELETE FROM user_progress WHERE module_id = ?");
            $stmt->execute([$id]);
            
            // Then delete the module
            $stmt = $pdo->prepare("DELETE FROM modules WHERE id = ?");
            $stmt->execute([$id]);
            
            echo json_encode(['success' => true, 'message' => 'Module deleted successfully']);
            break;
            
        default:
            echo json_encode(['success' => false, 'message' => 'Invalid action']);
            break;
    }
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
}
?> 