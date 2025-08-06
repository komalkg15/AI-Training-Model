<?php
// Database configuration
$host = 'localhost';
$dbname = 'ai_training';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}

// Create tables if they don't exist
function createTables($pdo) {
    // Users table
    $pdo->exec("CREATE TABLE IF NOT EXISTS users (
        id INT AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(255) NOT NULL,
        email VARCHAR(255) UNIQUE NOT NULL,
        password VARCHAR(255) NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )");
    
    // Modules table
    $pdo->exec("CREATE TABLE IF NOT EXISTS modules (
        id INT AUTO_INCREMENT PRIMARY KEY,
        title VARCHAR(255) NOT NULL,
        description TEXT,
        ppt_content TEXT,
        video_content TEXT,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )");
    
    // User progress table
    $pdo->exec("CREATE TABLE IF NOT EXISTS user_progress (
        id INT AUTO_INCREMENT PRIMARY KEY,
        user_id INT,
        module_id INT,
        completed BOOLEAN DEFAULT FALSE,
        completed_at TIMESTAMP NULL,
        FOREIGN KEY (user_id) REFERENCES users(id),
        FOREIGN KEY (module_id) REFERENCES modules(id)
    )");
    
    // Videos table
    $pdo->exec("CREATE TABLE IF NOT EXISTS videos (
        id INT AUTO_INCREMENT PRIMARY KEY,
        title VARCHAR(255) NOT NULL,
        description TEXT,
        url VARCHAR(500),
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )");
    
    // Certificates table
    $pdo->exec("CREATE TABLE IF NOT EXISTS certificates (
        id INT AUTO_INCREMENT PRIMARY KEY,
        user_id INT,
        certificate_id VARCHAR(255) UNIQUE,
        completion_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (user_id) REFERENCES users(id)
    )");
}

// Initialize database with sample data
function initializeData($pdo) {
    // Check if modules already exist
    $stmt = $pdo->query("SELECT COUNT(*) FROM modules");
    if ($stmt->fetchColumn() == 0) {
        // Insert sample modules
        $modules = [
            [
                'title' => 'AI Fundamentals',
                'description' => 'Introduction to Artificial Intelligence concepts and principles',
                'ppt_content' => '<h3>AI Fundamentals</h3><p>This module covers the basics of AI including machine learning, neural networks, and deep learning.</p><ul><li>What is AI?</li><li>Types of AI</li><li>Machine Learning Basics</li><li>Neural Networks</li></ul>',
                'video_content' => '<h3>AI Fundamentals Video</h3><p>Watch this comprehensive video on AI fundamentals.</p><iframe width="560" height="315" src="https://www.youtube.com/embed/dQw4w9WgXcQ" frameborder="0" allowfullscreen></iframe>'
            ],
            [
                'title' => 'AI in Insurance',
                'description' => 'How AI is transforming the insurance industry',
                'ppt_content' => '<h3>AI in Insurance</h3><p>Explore how AI is revolutionizing insurance through automation, risk assessment, and customer service.</p><ul><li>Claims Processing</li><li>Risk Assessment</li><li>Customer Service</li><li>Fraud Detection</li></ul>',
                'video_content' => '<h3>AI in Insurance Video</h3><p>Learn about AI applications in the insurance sector.</p><iframe width="560" height="315" src="https://www.youtube.com/embed/dQw4w9WgXcQ" frameborder="0" allowfullscreen></iframe>'
            ],
            [
                'title' => 'AI in Compliance',
                'description' => 'AI applications in regulatory compliance and governance',
                'ppt_content' => '<h3>AI in Compliance</h3><p>Discover how AI is helping organizations meet regulatory requirements and maintain compliance.</p><ul><li>Regulatory Monitoring</li><li>Document Analysis</li><li>Risk Management</li><li>Audit Automation</li></ul>',
                'video_content' => '<h3>AI in Compliance Video</h3><p>Understanding AI in compliance and governance.</p><iframe width="560" height="315" src="https://www.youtube.com/embed/dQw4w9WgXcQ" frameborder="0" allowfullscreen></iframe>'
            ],
            [
                'title' => 'Machine Learning Basics',
                'description' => 'Core concepts and algorithms in machine learning',
                'ppt_content' => '<h3>Machine Learning Basics</h3><p>Learn the fundamental concepts of machine learning and its applications.</p><ul><li>Supervised Learning</li><li>Unsupervised Learning</li><li>Reinforcement Learning</li><li>Model Evaluation</li></ul>',
                'video_content' => '<h3>Machine Learning Video</h3><p>Comprehensive guide to machine learning fundamentals.</p><iframe width="560" height="315" src="https://www.youtube.com/embed/dQw4w9WgXcQ" frameborder="0" allowfullscreen></iframe>'
            ],
            [
                'title' => 'AI Ethics and Governance',
                'description' => 'Ethical considerations and governance frameworks for AI',
                'ppt_content' => '<h3>AI Ethics and Governance</h3><p>Understanding the ethical implications and governance requirements for AI systems.</p><ul><li>Bias and Fairness</li><li>Transparency</li><li>Accountability</li><li>Privacy Protection</li></ul>',
                'video_content' => '<h3>AI Ethics Video</h3><p>Exploring ethical considerations in AI development and deployment.</p><iframe width="560" height="315" src="https://www.youtube.com/embed/dQw4w9WgXcQ" frameborder="0" allowfullscreen></iframe>'
            ]
        ];
        
        $stmt = $pdo->prepare("INSERT INTO modules (title, description, ppt_content, video_content) VALUES (?, ?, ?, ?)");
        foreach ($modules as $module) {
            $stmt->execute([$module['title'], $module['description'], $module['ppt_content'], $module['video_content']]);
        }
    }
    
    // Check if videos already exist
    $stmt = $pdo->query("SELECT COUNT(*) FROM videos");
    if ($stmt->fetchColumn() == 0) {
        // Insert sample videos
        $videos = [
            ['title' => 'What is AI?', 'description' => 'Introduction to Artificial Intelligence', 'url' => 'https://www.youtube.com/watch?v=dQw4w9WgXcQ'],
            ['title' => 'Machine Learning Explained', 'description' => 'Understanding machine learning concepts', 'url' => 'https://www.youtube.com/watch?v=dQw4w9WgXcQ'],
            ['title' => 'Neural Networks', 'description' => 'How neural networks work', 'url' => 'https://www.youtube.com/watch?v=dQw4w9WgXcQ'],
            ['title' => 'Deep Learning Basics', 'description' => 'Introduction to deep learning', 'url' => 'https://www.youtube.com/watch?v=dQw4w9WgXcQ'],
            ['title' => 'AI in Business', 'description' => 'AI applications in business', 'url' => 'https://www.youtube.com/watch?v=dQw4w9WgXcQ'],
            ['title' => 'Natural Language Processing', 'description' => 'Understanding NLP', 'url' => 'https://www.youtube.com/watch?v=dQw4w9WgXcQ'],
            ['title' => 'Computer Vision', 'description' => 'AI for image recognition', 'url' => 'https://www.youtube.com/watch?v=dQw4w9WgXcQ'],
            ['title' => 'AI Ethics', 'description' => 'Ethical considerations in AI', 'url' => 'https://www.youtube.com/watch?v=dQw4w9WgXcQ'],
            ['title' => 'AI Future Trends', 'description' => 'Future of AI technology', 'url' => 'https://www.youtube.com/watch?v=dQw4w9WgXcQ'],
            ['title' => 'AI Implementation', 'description' => 'How to implement AI in organizations', 'url' => 'https://www.youtube.com/watch?v=dQw4w9WgXcQ']
        ];
        
        $stmt = $pdo->prepare("INSERT INTO videos (title, description, url) VALUES (?, ?, ?)");
        foreach ($videos as $video) {
            $stmt->execute([$video['title'], $video['description'], $video['url']]);
        }
    }
}

// Initialize database
createTables($pdo);
initializeData($pdo);
?> 