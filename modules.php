<?php
header('Content-Type: application/json');
require_once 'config.php';

try {
    // Get all modules
    $stmt = $pdo->query("SELECT id, title, description FROM modules ORDER BY id");
    $modules = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Get user ID from request if available
    $user_id = $_GET['user_id'] ?? null;
    
    if ($user_id) {
        // Get user progress for each module
        $stmt = $pdo->prepare("
            SELECT m.id, 
                   CASE WHEN up.completed = 1 THEN 100 
                        WHEN up.completed = 0 THEN 0
                        WHEN up.completed IS NULL THEN 0
                        ELSE 0 END as progress
            FROM modules m
            LEFT JOIN user_progress up ON m.id = up.module_id AND up.user_id = ?
            ORDER BY m.id
        ");
        $stmt->execute([$user_id]);
        $progress = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Merge progress with modules
        foreach ($modules as &$module) {
            $moduleProgress = array_filter($progress, function($p) use ($module) {
                return $p['id'] == $module['id'];
            });
            // Ensure progress is an integer
            $module['progress'] = !empty($moduleProgress) ? (int)reset($moduleProgress)['progress'] : 0;
        }
    } else {
        // For visitors or demo, show 0% progress by default
        foreach ($modules as &$module) {
            $module['progress'] = 0; // Show 0% progress for new users
        }
    }
    
    // Debug: Log the modules data before sending
    error_log('Modules data: ' . json_encode($modules));
    
    echo json_encode(['success' => true, 'modules' => $modules]);
} catch (Exception $e) {
    error_log('Error loading modules: ' . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Failed to load modules: ' . $e->getMessage()]);
}
?>