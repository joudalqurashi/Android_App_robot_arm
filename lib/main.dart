import 'package:flutter/material.dart';
import 'package:http/http.dart' as http;
import 'dart:convert';
import 'package:url_launcher/url_launcher.dart';

void main() {
  runApp(const RobotArmApp());
}

class RobotArmApp extends StatelessWidget {
  const RobotArmApp({super.key});

  @override
  Widget build(BuildContext context) {
    return MaterialApp(
      title: 'Robot Arm Control Panel',
      theme: ThemeData(
        colorScheme: ColorScheme.fromSeed(seedColor: Colors.deepPurple),
        useMaterial3: true,
      ),
      home: const RobotArmControlPage(),
    );
  }
}

class RobotArmControlPage extends StatefulWidget {
  const RobotArmControlPage({super.key});

  @override
  State<RobotArmControlPage> createState() => _RobotArmControlPageState();
}

class _RobotArmControlPageState extends State<RobotArmControlPage> {
  // Motor angle values (0-180 degrees)
  double motor1Value = 98.0;
  double motor2Value = 77.0;
  double motor3Value = 108.0;
  double motor4Value = 79.0;

  // Default values for reset
  final double defaultMotor1 = 90.0;
  final double defaultMotor2 = 90.0;
  final double defaultMotor3 = 90.0;
  final double defaultMotor4 = 90.0;

  // Server URL - update this to your XAMPP server IP
  // Uncomment the appropriate line for your setup:
  final String baseUrl = 'http://192.168.0.160/robot_arm'; // For Android emulator
  // final String baseUrl = 'http://192.168.1.100/robot_arm'; // For real device
  // final String baseUrl = 'http://10.0.2.2/robot_arm'; // For Android emulator (alternative)
  // final String baseUrl = 'http://localhost/robot_arm'; // For local testing

  List<Map<String, dynamic>> savedPoses = [];

  @override
  void initState() {
    super.initState();
    loadSavedPoses();
  }

  // Load saved poses from database
  Future<void> loadSavedPoses() async {
    try {
      final response = await http.get(Uri.parse('$baseUrl/get_run_pose.php'));
      if (response.statusCode == 200) {
        final List<dynamic> data = json.decode(response.body);
        setState(() {
          savedPoses = data.cast<Map<String, dynamic>>();
        });
      }
    } catch (e) {
      // Error loading poses - could be logged to a proper logging service
    }
  }

  // Reset all motors to default values
  void resetMotors() {
    setState(() {
      motor1Value = defaultMotor1;
      motor2Value = defaultMotor2;
      motor3Value = defaultMotor3;
      motor4Value = defaultMotor4;
    });
  }

  // Save current pose to database
  Future<void> savePose() async {
    try {
      final response = await http.post(
        Uri.parse('$baseUrl/save_pose.php'),
        body: {
          'motor1': motor1Value.round().toString(),
          'motor2': motor2Value.round().toString(),
          'motor3': motor3Value.round().toString(),
          'motor4': motor4Value.round().toString(),
        },
      );
      
      if (response.statusCode == 200 && mounted) {
        ScaffoldMessenger.of(context).showSnackBar(
          const SnackBar(content: Text('Pose saved successfully!')),
        );
        loadSavedPoses(); // Reload the list
      }
    } catch (e) {
      if (mounted) {
        ScaffoldMessenger.of(context).showSnackBar(
          SnackBar(content: Text('Error saving pose: $e')),
        );
      }
    }
  }

  // Run current motor values
  Future<void> runMotors() async {
    try {
      final response = await http.post(
        Uri.parse('$baseUrl/run_motors.php'),
        body: {
          'motor1': motor1Value.round().toString(),
          'motor2': motor2Value.round().toString(),
          'motor3': motor3Value.round().toString(),
          'motor4': motor4Value.round().toString(),
        },
      );
      
      if (response.statusCode == 200) {
        // Update status to 0
        await updateStatus();
        if (mounted) {
          ScaffoldMessenger.of(context).showSnackBar(
            const SnackBar(content: Text('Motors running!')),
          );
        }
      }
    } catch (e) {
      if (mounted) {
        ScaffoldMessenger.of(context).showSnackBar(
          SnackBar(content: Text('Error running motors: $e')),
        );
      }
    }
  }

  // Update status to 0
  Future<void> updateStatus() async {
    try {
      await http.post(Uri.parse('$baseUrl/update_status.php'));
    } catch (e) {
      // Error updating status - could be logged to a proper logging service
    }
  }

  // Load a saved pose
  Future<void> loadPose(Map<String, dynamic> pose) async {
    setState(() {
      motor1Value = double.parse(pose['motor1_angle'].toString());
      motor2Value = double.parse(pose['motor2_angle'].toString());
      motor3Value = double.parse(pose['motor3_angle'].toString());
      motor4Value = double.parse(pose['motor4_angle'].toString());
    });
  }

  // Delete a saved pose
  Future<void> deletePose(int poseId) async {
    try {
      final response = await http.post(
        Uri.parse('$baseUrl/delete_pose.php'),
        body: {'pose_id': poseId.toString()},
      );
      
      if (response.statusCode == 200 && mounted) {
        ScaffoldMessenger.of(context).showSnackBar(
          const SnackBar(content: Text('Pose deleted successfully!')),
        );
        loadSavedPoses(); // Reload the list
      }
    } catch (e) {
      if (mounted) {
        ScaffoldMessenger.of(context).showSnackBar(
          SnackBar(content: Text('Error deleting pose: $e')),
        );
      }
    }
  }

  // Open HTML page with state engine corners
  Future<void> openStateEnginePage() async {
    final url = Uri.parse('$baseUrl/state_engine.html');
    if (await canLaunchUrl(url)) {
      await launchUrl(url, mode: LaunchMode.externalApplication);
    } else {
      if (mounted) {
        ScaffoldMessenger.of(context).showSnackBar(
          const SnackBar(content: Text('Could not open state engine page')),
        );
      }
    }
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: Colors.grey[50],
      appBar: AppBar(
        title: Text(
          'Robot Arm Control Panel',
          style: TextStyle(color: Colors.grey[800], fontWeight: FontWeight.bold),
        ),
        backgroundColor: Colors.white,
        elevation: 0,
        actions: [
          IconButton(
            icon: const Icon(Icons.web, color: Colors.deepPurple),
            onPressed: openStateEnginePage,
            tooltip: 'View State Engine Page',
          ),
        ],
      ),
      body: Padding(
        padding: const EdgeInsets.all(16.0),
        child: Column(
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            // Motor Controls Section
            Expanded(
              flex: 2,
              child: Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  // Motor 1
                  _buildMotorSlider('Motor 1', motor1Value, (value) {
                    setState(() => motor1Value = value);
                  }),
                  const SizedBox(height: 16),
                  
                  // Motor 2
                  _buildMotorSlider('Motor 2', motor2Value, (value) {
                    setState(() => motor2Value = value);
                  }),
                  const SizedBox(height: 16),
                  
                  // Motor 3
                  _buildMotorSlider('Motor 3', motor3Value, (value) {
                    setState(() => motor3Value = value);
                  }),
                  const SizedBox(height: 16),
                  
                  // Motor 4
                  _buildMotorSlider('Motor 4', motor4Value, (value) {
                    setState(() => motor4Value = value);
                  }),
                  const SizedBox(height: 24),
                  
                  // Action Buttons
                  Row(
                    children: [
                      Expanded(
                        child: ElevatedButton(
                          onPressed: resetMotors,
                          style: ElevatedButton.styleFrom(
                            backgroundColor: Colors.grey[200],
                            foregroundColor: Colors.grey[800],
                            padding: const EdgeInsets.symmetric(vertical: 12),
                          ),
                          child: const Text('Reset'),
                        ),
                      ),
                      const SizedBox(width: 12),
                      Expanded(
                        child: ElevatedButton(
                          onPressed: savePose,
                          style: ElevatedButton.styleFrom(
                            backgroundColor: Colors.deepPurple,
                            foregroundColor: Colors.white,
                            padding: const EdgeInsets.symmetric(vertical: 12),
                          ),
                          child: const Text('Save Pose'),
                        ),
                      ),
                      const SizedBox(width: 12),
                      Expanded(
                        child: ElevatedButton(
                          onPressed: runMotors,
                          style: ElevatedButton.styleFrom(
                            backgroundColor: Colors.green,
                            foregroundColor: Colors.white,
                            padding: const EdgeInsets.symmetric(vertical: 12),
                          ),
                          child: const Text('Run'),
                        ),
                      ),
                    ],
                  ),
                ],
              ),
            ),
            
            const SizedBox(height: 24),
            
            // Saved Poses Section
            Expanded(
              flex: 1,
              child: Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  Text(
                    'Saved Poses:',
                    style: TextStyle(
                      fontSize: 18,
                      fontWeight: FontWeight.bold,
                      color: Colors.grey[800],
                    ),
                  ),
                  const SizedBox(height: 12),
                  Expanded(
                    child: ListView.builder(
                      itemCount: savedPoses.length,
                      itemBuilder: (context, index) {
                        final pose = savedPoses[index];
                        return Card(
                          margin: const EdgeInsets.only(bottom: 8),
                          child: Padding(
                            padding: const EdgeInsets.all(12),
                            child: Row(
                              children: [
                                Expanded(
                                  child: Text(
                                    'Pose ${pose['pose_id']}: ${pose['motor1_angle']}, ${pose['motor2_angle']}, ${pose['motor3_angle']}, ${pose['motor4_angle']}',
                                    style: TextStyle(
                                      color: Colors.grey[800],
                                      fontSize: 14,
                                    ),
                                  ),
                                ),
                                IconButton(
                                  icon: const Icon(Icons.play_arrow, color: Colors.black),
                                  onPressed: () => loadPose(pose),
                                  tooltip: 'Load Pose',
                                ),
                                IconButton(
                                  icon: const Icon(Icons.delete, color: Colors.red),
                                  onPressed: () => deletePose(pose['pose_id']),
                                  tooltip: 'Delete Pose',
                                ),
                              ],
                            ),
                          ),
                        );
                      },
                    ),
                  ),
                ],
              ),
            ),
          ],
        ),
      ),
    );
  }

  Widget _buildMotorSlider(String label, double value, ValueChanged<double> onChanged) {
    return Column(
      crossAxisAlignment: CrossAxisAlignment.start,
      children: [
        Text(
          '$label: ${value.round()}',
          style: TextStyle(
            color: Colors.grey[800],
            fontSize: 16,
            fontWeight: FontWeight.w500,
          ),
        ),
        Slider(
          value: value,
          min: 0,
          max: 180,
          divisions: 180,
          activeColor: Colors.deepPurple,
          inactiveColor: Colors.grey[300],
          onChanged: onChanged,
        ),
      ],
    );
  }
}
