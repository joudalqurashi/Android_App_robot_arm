<?php
require_once 'config.php';

// Handle preflight OPTIONS request
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get motor values from POST data
    $motor1 = isset($_POST['motor1']) ? intval($_POST['motor1']) : 0;
    $motor2 = isset($_POST['motor2']) ? intval($_POST['motor2']) : 0;
    $motor3 = isset($_POST['motor3']) ? intval($_POST['motor3']) : 0;
    $motor4 = isset($_POST['motor4']) ? intval($_POST['motor4']) : 0;
    
    // Validate motor values (0-180 degrees)
    if ($motor1 < 0 || $motor1 > 180 || $motor2 < 0 || $motor2 > 180 || 
        $motor3 < 0 || $motor3 > 180 || $motor4 < 0 || $motor4 > 180) {
        http_response_code(400);
        echo json_encode(['error' => 'Motor values must be between 0 and 180 degrees']);
        exit;
    }
    
    try {
        // First, check if this pose already exists in RobotPoses table
        $stmt = $pdo->prepare("SELECT pose_id FROM RobotPoses WHERE motor1_angle = ? AND motor2_angle = ? AND motor3_angle = ? AND motor4_angle = ?");
        $stmt->execute([$motor1, $motor2, $motor3, $motor4]);
        $existingPose = $stmt->fetch(PDO::FETCH_ASSOC);
        
        $poseId = null;
        if ($existingPose) {
            $poseId = $existingPose['pose_id'];
        } else {
            // Create new pose if it doesn't exist
            $stmt = $pdo->prepare("INSERT INTO RobotPoses (motor1_angle, motor2_angle, motor3_angle, motor4_angle) VALUES (?, ?, ?, ?)");
            $stmt->execute([$motor1, $motor2, $motor3, $motor4]);
            $poseId = $pdo->lastInsertId();
        }
        
        // Create run record with status 1 (running)
        $stmt = $pdo->prepare("INSERT INTO Run (pose_id, status) VALUES (?, 1)");
        $stmt->execute([$poseId]);
        
        // Here you would typically send commands to the actual robot arm hardware
        // For now, we'll simulate the robot arm movement
        
        echo json_encode([
            'success' => 'Motors running',
            'pose_id' => $poseId,
            'motor1' => $motor1,
            'motor2' => $motor2,
            'motor3' => $motor3,
            'motor4' => $motor4,
            'message' => 'Robot arm executing pose...'
        ]);
    } catch(PDOException $e) {
        http_response_code(500);
        echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
    }
} else {
    http_response_code(405);
    echo json_encode(['error' => 'Method not allowed']);
}
?> 