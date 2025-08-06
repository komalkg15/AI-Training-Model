<?php
require_once 'config.php';

echo "Testing module content...\n";

$stmt = $pdo->prepare("SELECT id, title, ppt_content, video_content FROM modules WHERE id = 1");
$stmt->execute();
$module = $stmt->fetch(PDO::FETCH_ASSOC);

if ($module) {
    echo "Module found: " . $module['title'] . "\n";
    echo "PPT Content length: " . strlen($module['ppt_content']) . " characters\n";
    echo "Video Content length: " . strlen($module['video_content']) . " characters\n";
    echo "\nPPT Content preview:\n" . substr($module['ppt_content'], 0, 200) . "...\n";
    echo "\nVideo Content preview:\n" . substr($module['video_content'], 0, 200) . "...\n";
} else {
    echo "No modules found in database\n";
}
?> 