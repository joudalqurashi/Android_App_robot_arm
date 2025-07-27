<?php
// Force PHP processing and set headers
header('Content-Type: text/html; charset=UTF-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

// Include database configuration
require_once 'config.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Robot Arm State Engine</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px;
            background-color: #f5f5f5;
        }
        .container {
            max-width: 1200px;
            margin: 0 auto;
            background-color: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        h1 {
            color: #333;
            text-align: center;
            margin-bottom: 30px;
        }
        .section {
            margin-bottom: 30px;
        }
        .section h2 {
            color: #666;
            border-bottom: 2px solid #ddd;
            padding-bottom: 10px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
        }
        th, td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        th {
            background-color: #f8f9fa;
            font-weight: bold;
            color: #333;
        }
        tr:hover {
            background-color: #f5f5f5;
        }
        .status-running {
            color: #ffc107;
            font-weight: bold;
        }
        .status-completed {
            color: #28a745;
            font-weight: bold;
        }
        .motor-values {
            font-family: monospace;
            background-color: #f8f9fa;
            padding: 5px;
            border-radius: 3px;
        }
        .refresh-btn {
            background-color: #007bff;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 5px;
            cursor: pointer;
            margin-bottom: 20px;
        }
        .refresh-btn:hover {
            background-color: #0056b3;
        }
        .error {
            color: #dc3545;
            background-color: #f8d7da;
            padding: 10px;
            border-radius: 5px;
            margin: 10px 0;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>🤖 Robot Arm State Engine</h1>
        
        <button class="refresh-btn" onclick="location.reload()">🔄 Refresh Data</button>
        
        <div class="section">
            <h2>📊 Current State Engine Corners</h2>
            <table>
                <thead>
                    <tr>
                        <th>Pose ID</th>
                        <th>Motor 1 (°)</th>
                        <th>Motor 2 (°)</th>
                        <th>Motor 3 (°)</th>
                        <th>Motor 4 (°)</th>
                        <th>Created At</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    try {
                        $stmt = $pdo->prepare("SELECT pose_id, motor1_angle, motor2_angle, motor3_angle, motor4_angle, created_at FROM RobotPoses ORDER BY created_at DESC");
                        $stmt->execute();
                        $poses = $stmt->fetchAll(PDO::FETCH_ASSOC);
                        
                        if (empty($poses)) {
                            echo "<tr><td colspan='6'>No poses found in database</td></tr>";
                        } else {
                            foreach ($poses as $pose) {
                                echo "<tr>";
                                echo "<td><strong>Pose " . $pose['pose_id'] . "</strong></td>";
                                echo "<td>" . $pose['motor1_angle'] . "°</td>";
                                echo "<td>" . $pose['motor2_angle'] . "°</td>";
                                echo "<td>" . $pose['motor3_angle'] . "°</td>";
                                echo "<td>" . $pose['motor4_angle'] . "°</td>";
                                echo "<td>" . date('Y-m-d H:i:s', strtotime($pose['created_at'])) . "</td>";
                                echo "</tr>";
                            }
                        }
                    } catch(PDOException $e) {
                        echo "<tr><td colspan='6' class='error'>Error loading data: " . $e->getMessage() . "</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>
        
        <div class="section">
            <h2>🏃‍♂️ Run History & Status</h2>
            <table>
                <thead>
                    <tr>
                        <th>Run ID</th>
                        <th>Pose ID</th>
                        <th>Motor Values</th>
                        <th>Status</th>
                        <th>Executed At</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    try {
                        $stmt = $pdo->prepare("
                            SELECT r.run_id, r.pose_id, r.status, r.executed_at,
                                   p.motor1_angle, p.motor2_angle, p.motor3_angle, p.motor4_angle
                            FROM Run r
                            LEFT JOIN RobotPoses p ON r.pose_id = p.pose_id
                            ORDER BY r.executed_at DESC
                        ");
                        $stmt->execute();
                        $runs = $stmt->fetchAll(PDO::FETCH_ASSOC);
                        
                        if (empty($runs)) {
                            echo "<tr><td colspan='5'>No run history found</td></tr>";
                        } else {
                            foreach ($runs as $run) {
                                echo "<tr>";
                                echo "<td><strong>Run " . $run['run_id'] . "</strong></td>";
                                echo "<td>Pose " . ($run['pose_id'] ?? 'N/A') . "</td>";
                                
                                if ($run['motor1_angle'] !== null) {
                                    echo "<td class='motor-values'>" . 
                                         $run['motor1_angle'] . "°, " . 
                                         $run['motor2_angle'] . "°, " . 
                                         $run['motor3_angle'] . "°, " . 
                                         $run['motor4_angle'] . "°</td>";
                                } else {
                                    echo "<td>N/A</td>";
                                }
                                
                                $statusClass = $run['status'] == 1 ? 'status-running' : 'status-completed';
                                $statusText = $run['status'] == 1 ? '🔄 Running' : '✅ Completed';
                                echo "<td class='$statusClass'>$statusText</td>";
                                
                                echo "<td>" . date('Y-m-d H:i:s', strtotime($run['executed_at'])) . "</td>";
                                echo "</tr>";
                            }
                        }
                    } catch(PDOException $e) {
                        echo "<tr><td colspan='5' class='error'>Error loading data: " . $e->getMessage() . "</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>
        
        <div class="section">
            <h2>📈 Statistics</h2>
            <?php
            try {
                $stmt = $pdo->prepare("SELECT COUNT(*) as total_poses FROM RobotPoses");
                $stmt->execute();
                $totalPoses = $stmt->fetch(PDO::FETCH_ASSOC)['total_poses'];
                
                $stmt = $pdo->prepare("SELECT COUNT(*) as total_runs FROM Run");
                $stmt->execute();
                $totalRuns = $stmt->fetch(PDO::FETCH_ASSOC)['total_runs'];
                
                $stmt = $pdo->prepare("SELECT COUNT(*) as completed_runs FROM Run WHERE status = 0");
                $stmt->execute();
                $completedRuns = $stmt->fetch(PDO::FETCH_ASSOC)['completed_runs'];
                
                echo "<p><strong>Total Saved Poses:</strong> $totalPoses</p>";
                echo "<p><strong>Total Executions:</strong> $totalRuns</p>";
                echo "<p><strong>Completed Executions:</strong> $completedRuns</p>";
                
                if ($totalRuns > 0) {
                    $successRate = round(($completedRuns / $totalRuns) * 100, 1);
                    echo "<p><strong>Success Rate:</strong> $successRate%</p>";
                }
                
            } catch(PDOException $e) {
                echo "<p class='error'>Error loading statistics: " . $e->getMessage() . "</p>";
            }
            ?>
        </div>
        
        <div class="section">
            <h2>🔧 System Information</h2>
            <p><strong>PHP Version:</strong> <?php echo PHP_VERSION; ?></p>
            <p><strong>Server:</strong> <?php echo $_SERVER['SERVER_SOFTWARE'] ?? 'Unknown'; ?></p>
            <p><strong>Database:</strong> MySQL (robot_arm_db)</p>
            <p><strong>Last Updated:</strong> <?php echo date('Y-m-d H:i:s'); ?></p>
        </div>
    </div>
</body>
</html> 