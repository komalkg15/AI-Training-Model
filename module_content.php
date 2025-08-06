<?php
header('Content-Type: application/json');
require_once 'config.php';

$module_id = $_GET['id'] ?? 0;

if (!$module_id) {
    echo json_encode(['success' => false, 'message' => 'Module ID required']);
    exit;
}

try {
    $stmt = $pdo->prepare("SELECT id, title, description, ppt_content, video_content FROM modules WHERE id = ?");
    $stmt->execute([$module_id]);
    $module = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($module) {
        echo json_encode(['success' => true, 'module' => $module]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Module not found']);
    }
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Failed to load module']);
}
?> 