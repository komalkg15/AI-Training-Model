<?php
header('Content-Type: application/json');
require_once 'config.php';

$user_id = $_POST['user_id'] ?? 0;
$module_id = $_POST['module_id'] ?? 0;

if (!$user_id || !$module_id) {
    echo json_encode(['success' => false, 'message' => 'User ID and Module ID required']);
    exit;
}

try {
    // Check if already completed
    $stmt = $pdo->prepare("SELECT id FROM user_progress WHERE user_id = ? AND module_id = ?");
    $stmt->execute([$user_id, $module_id]);
    $existing = $stmt->fetch();
    
    if ($existing) {
        // Update existing record
        $stmt = $pdo->prepare("UPDATE user_progress SET completed = 1, completed_at = NOW() WHERE user_id = ? AND module_id = ?");
        $stmt->execute([$user_id, $module_id]);
    } else {
        // Insert new record
        $stmt = $pdo->prepare("INSERT INTO user_progress (user_id, module_id, completed, completed_at) VALUES (?, ?, 1, NOW())");
        $stmt->execute([$user_id, $module_id]);
    }
    
    echo json_encode(['success' => true, 'message' => 'Module completed successfully']);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Failed to complete module']);
}
?> 