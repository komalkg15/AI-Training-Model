<?php
header('Content-Type: application/json');
require_once 'config.php';

try {
    // Get all videos
    $stmt = $pdo->query("SELECT id, title, description, url FROM videos ORDER BY id");
    $videos = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Set the same video link for all videos
    foreach ($videos as &$video) {
        $video['url'] = 'https://youtu.be/eiKkZNmaJYk?si=u_vqwJQ28Wxs2chh';
    }
    
    echo json_encode(['success' => true, 'videos' => $videos]);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Failed to load videos']);
}
?>