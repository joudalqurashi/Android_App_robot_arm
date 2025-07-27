<?php
require_once 'config.php';

// Handle preflight OPTIONS request
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        // Update the most recent run record status to 0 (completed)
        $stmt = $pdo->prepare("UPDATE Run SET status = 0 WHERE run_id = (SELECT run_id FROM (SELECT run_id FROM Run ORDER BY executed_at DESC LIMIT 1) AS temp)");
        $stmt->execute();
        
        if ($stmt->rowCount() > 0) {
            echo json_encode(['success' => 'Status updated to 0 successfully']);
        } else {
            echo json_encode(['message' => 'No run records found to update']);
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