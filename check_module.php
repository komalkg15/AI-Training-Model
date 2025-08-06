<?php
header('Content-Type: application/json');
require_once 'config.php';

try {
    $stmt = $pdo->prepare("SELECT id, title, SUBSTRING(ppt_content, 1, 500) AS ppt_preview FROM modules WHERE id = 1");
    $stmt->execute();
    $module = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($module) {
        echo json_encode(['success' => true, 'module' => $module]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Module not found']);
    }
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Failed to load module: ' . $e->getMessage()]);
}
?>