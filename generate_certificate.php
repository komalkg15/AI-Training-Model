<?php
header('Content-Type: application/json');
require_once 'config.php';

$user_id = $_POST['user_id'] ?? 0;

if (!$user_id) {
    echo json_encode(['success' => false, 'message' => 'User ID required']);
    exit;
}

try {
    // Check if user has completed all modules
    $stmt = $pdo->prepare("
        SELECT COUNT(*) as total_modules,
               COUNT(up.id) as completed_modules
        FROM modules m
        LEFT JOIN user_progress up ON m.id = up.module_id AND up.user_id = ? AND up.completed = 1
    ");
    $stmt->execute([$user_id]);
    $progress = $stmt->fetch();
    
    if ($progress['completed_modules'] < $progress['total_modules']) {
        echo json_encode(['success' => false, 'message' => 'Complete all modules to get certificate']);
        exit;
    }
    
    // Check if certificate already exists
    $stmt = $pdo->prepare("SELECT certificate_id, completion_date FROM certificates WHERE user_id = ?");
    $stmt->execute([$user_id]);
    $existing = $stmt->fetch();
    
    if ($existing) {
        // Return existing certificate
        $stmt = $pdo->prepare("SELECT u.name as user_name, c.certificate_id, c.completion_date FROM certificates c JOIN users u ON c.user_id = u.id WHERE c.user_id = ?");
        $stmt->execute([$user_id]);
        $certificate = $stmt->fetch();
        
        echo json_encode(['success' => true, 'certificate' => $certificate]);
    } else {
        // Generate new certificate
        $certificate_id = 'CERT-' . strtoupper(uniqid());
        
        $stmt = $pdo->prepare("INSERT INTO certificates (user_id, certificate_id) VALUES (?, ?)");
        $stmt->execute([$user_id, $certificate_id]);
        
        // Get user info and certificate details
        $stmt = $pdo->prepare("SELECT u.name as user_name, c.certificate_id, c.completion_date FROM certificates c JOIN users u ON c.user_id = u.id WHERE c.user_id = ?");
        $stmt->execute([$user_id]);
        $certificate = $stmt->fetch();
        
        echo json_encode(['success' => true, 'certificate' => $certificate]);
    }
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Failed to generate certificate']);
}
?> 