<?php
header('Content-Type: application/json');
require_once 'config.php';

// Accept both POST and GET methods for user_id
$user_id = $_POST['user_id'] ?? $_GET['user_id'] ?? 0;

if (!$user_id) {
    echo json_encode(['success' => false, 'message' => 'User ID required']);
    exit;
}

try {
    // Get user progress for all modules
    $stmt = $pdo->prepare("
        SELECT m.id, m.title, 
               CASE WHEN up.completed = 1 THEN 100 
                    WHEN up.completed = 0 THEN 0
                    WHEN up.completed IS NULL THEN 0
                    ELSE 0 END as percentage
        FROM modules m
        LEFT JOIN user_progress up ON m.id = up.module_id AND up.user_id = ?
        ORDER BY m.id
    ");
    $stmt->execute([$user_id]);
    $progress = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Convert percentage to integer to ensure proper comparison in frontend
    foreach ($progress as &$p) {
        $p['percentage'] = (int)$p['percentage'];
    }
    
    // Debug: Log the progress data
    error_log('Progress data for user ' . $user_id . ': ' . json_encode($progress));
    
    echo json_encode(['success' => true, 'progress' => $progress]);
} catch (Exception $e) {
    error_log('Error loading progress: ' . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Failed to load progress: ' . $e->getMessage()]);
}
?>