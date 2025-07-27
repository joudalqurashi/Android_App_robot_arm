<?php
require_once 'config.php';

// Handle preflight OPTIONS request
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    try {
        // Get all saved poses from RobotPoses table
        $stmt = $pdo->prepare("SELECT pose_id, motor1_angle, motor2_angle, motor3_angle, motor4_angle, created_at FROM RobotPoses ORDER BY created_at DESC");
        $stmt->execute();
        $poses = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        echo json_encode($poses);
    } catch(PDOException $e) {
        http_response_code(500);
        echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
    }
} else {
    http_response_code(405);
    echo json_encode(['error' => 'Method not allowed']);
}
?> 