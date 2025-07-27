-- Robot Arm Control Database Setup
-- Run this in your XAMPP MySQL database

-- Create database (if not exists)
CREATE DATABASE IF NOT EXISTS robot_arm_db;
USE robot_arm_db;

-- RobotPoses table to store saved poses (renamed from Position to avoid reserved keyword)
CREATE TABLE IF NOT EXISTS RobotPoses (
    pose_id INT AUTO_INCREMENT PRIMARY KEY,
    motor1_angle INT NOT NULL CHECK (motor1_angle >= 0 AND motor1_angle <= 180),
    motor2_angle INT NOT NULL CHECK (motor2_angle >= 0 AND motor2_angle <= 180),
    motor3_angle INT NOT NULL CHECK (motor3_angle >= 0 AND motor3_angle <= 180),
    motor4_angle INT NOT NULL CHECK (motor4_angle >= 0 AND motor4_angle <= 180),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Run table to track executed poses
CREATE TABLE IF NOT EXISTS Run (
    run_id INT AUTO_INCREMENT PRIMARY KEY,
    pose_id INT,
    status INT DEFAULT 0, -- 0 = completed, 1 = running
    executed_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (pose_id) REFERENCES RobotPoses(pose_id) ON DELETE SET NULL
);

-- Insert some sample data
INSERT INTO RobotPoses (motor1_angle, motor2_angle, motor3_angle, motor4_angle) VALUES
(137, 55, 90, 26),
(90, 115, 90, 59),
(98, 77, 108, 79),
(46, 75, 107, 83);

-- Insert sample run records
INSERT INTO Run (pose_id, status) VALUES
(1, 0),
(2, 0),
(3, 0),
(4, 0); 