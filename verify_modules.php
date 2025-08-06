<?php
header('Content-Type: application/json');
require_once 'config.php';

// The iframe pattern to look for
$iframe = '<iframe src="https://gamma.app/embed/zvxgkpopl5zhcc1"';

try {
    // Get all modules
    $stmt = $pdo->query("SELECT id, title FROM modules");
    $modules = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    $results = [];
    
    // Check each module's PPT content
    foreach ($modules as $module) {
        $contentStmt = $pdo->prepare("SELECT ppt_content FROM modules WHERE id = ?");
        $contentStmt->execute([$module['id']]);
        $content = $contentStmt->fetch(PDO::FETCH_ASSOC);
        
        // Count occurrences of iframe
        $count = substr_count($content['ppt_content'], $iframe);
        
        $results[] = [
            'id' => $module['id'],
            'title' => $module['title'],
            'iframe_count' => $count
        ];
    }
    
    echo json_encode([
        'success' => true, 
        'modules' => $results
    ]);
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false, 
        'message' => 'Error checking modules: ' . $e->getMessage()
    ]);
}
?>