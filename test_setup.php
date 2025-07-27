<?php
// Test script to verify Robot Arm setup
header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');

$response = [
    'status' => 'success',
    'message' => 'Robot Arm PHP API is working!',
    'timestamp' => date('Y-m-d H:i:s'),
    'php_version' => PHP_VERSION,
    'server' => $_SERVER['SERVER_SOFTWARE'] ?? 'Unknown',
    'database_status' => 'unknown'
];

// Test database connection
try {
    require_once 'config.php';
    $response['database_status'] = 'connected';
    
    // Test query
    $stmt = $pdo->prepare("SELECT COUNT(*) as count FROM robotposes");
    $stmt->execute();
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    $response['poses_count'] = $result['count'];
    
} catch(Exception $e) {
    $response['database_status'] = 'error: ' . $e->getMessage();
}

echo json_encode($response, JSON_PRETTY_PRINT);
?> 