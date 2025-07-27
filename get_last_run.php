<?php
require_once 'config.php';

// Handle preflight OPTIONS request
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    try {
        // Get the last run from Run table
        $stmt = $pdo->prepare("
            SELECT r.run_id, r.pose_id, r.status, r.executed_at,
                   p.motor1_angle, p.motor2_angle, p.motor3_angle, p.motor4_angle
            FROM Run r
            LEFT JOIN RobotPoses p ON r.pose_id = p.pose_id
            ORDER BY r.executed_at DESC
            LIMIT 1
        ");
        $stmt->execute();
        $lastRun = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($lastRun) {
            echo json_encode($lastRun);
        } else {
            echo json_encode(['message' => 'No runs found']);
        }
    } catch(PDOException $e) {
        http_response_code(500);
        echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
    }
} else {
    http_response_code(405);
    echo json_encode(['error' => 'Method not allowed']);
}
?> 