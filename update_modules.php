<?php
header('Content-Type: application/json');
require_once 'config.php';

// The iframe to add to all modules
$iframe = '<iframe src="https://gamma.app/embed/zvxgkpopl5zhcc1" style="width: 700px; max-width: 100%; height: 450px" allow="fullscreen" title="AI Training & Course Details: Your Path to Mastering Artificial Intelligence"></iframe>';

try {
    // Get all modules
    $stmt = $pdo->query("SELECT id, ppt_content FROM modules");
    $modules = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    $updateCount = 0;
    
    // Update each module's PPT content
    foreach ($modules as $module) {
        // Add the iframe to the beginning of the PPT content
        $newPptContent = $iframe . "\n\n" . $module['ppt_content'];
        
        // Update the module
        $updateStmt = $pdo->prepare("UPDATE modules SET ppt_content = ? WHERE id = ?");
        $updateStmt->execute([$newPptContent, $module['id']]);
        
        $updateCount++;
    }
    
    echo json_encode([
        'success' => true, 
        'message' => "Successfully updated $updateCount modules with the iframe."
    ]);
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false, 
        'message' => 'Error updating modules: ' . $e->getMessage()
    ]);
}
?>