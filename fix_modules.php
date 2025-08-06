<?php
header('Content-Type: application/json');
require_once 'config.php';

// The iframe pattern to look for
$iframe = '<iframe src="https://gamma.app/embed/zvxgkpopl5zhcc1" style="width: 700px; max-width: 100%; height: 450px" allow="fullscreen" title="AI Training & Course Details: Your Path to Mastering Artificial Intelligence"></iframe>';

try {
    // Get all modules
    $stmt = $pdo->query("SELECT id, ppt_content FROM modules");
    $modules = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    $updateCount = 0;
    
    // Update each module's PPT content
    foreach ($modules as $module) {
        // Check if iframe appears twice
        $count = substr_count($module['ppt_content'], $iframe);
        
        if ($count > 1) {
            // Remove one instance of the iframe
            $pos = strpos($module['ppt_content'], $iframe);
            $newPptContent = substr_replace($module['ppt_content'], '', $pos, strlen($iframe));
            
            // Update the module
            $updateStmt = $pdo->prepare("UPDATE modules SET ppt_content = ? WHERE id = ?");
            $updateStmt->execute([$newPptContent, $module['id']]);
            
            $updateCount++;
        }
    }
    
    echo json_encode([
        'success' => true, 
        'message' => "Successfully fixed $updateCount modules by removing duplicate iframes."
    ]);
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false, 
        'message' => 'Error fixing modules: ' . $e->getMessage()
    ]);
}
?>