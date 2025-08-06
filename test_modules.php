<?php
header('Content-Type: application/json');
require_once 'config.php';

try {
    // Get all modules
    $stmt = $pdo->query("SELECT id, title, description FROM modules ORDER BY id");
    $modules = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // For test data, always show 0% progress
    foreach ($modules as &$module) {
        $module['progress'] = 0; // Always show 0% progress in test mode
    }
    
    echo json_encode(['success' => true, 'modules' => $modules]);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Failed to load test modules: ' . $e->getMessage()]);
}
?>