<?php
require_once 'config.php';

// Handle preflight OPTIONS request
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get pose_id from POST data
    $poseId = isset($_POST['pose_id']) ? intval($_POST['pose_id']) : 0;
    
    if ($poseId <= 0) {
        http_response_code(400);
        echo json_encode(['error' => 'Invalid pose ID']);
        exit;
    }
    
    try {
        // Delete the pose from RobotPoses table
        $stmt = $pdo->prepare("DELETE FROM RobotPoses WHERE pose_id = ?");
        $stmt->execute([$poseId]);
        
        if ($stmt->rowCount() > 0) {
            echo json_encode(['success' => 'Pose deleted successfully']);
        } else {
            echo json_encode(['error' => 'Pose not found']);
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