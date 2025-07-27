# 🤖 Robot Arm Control System

A complete Flutter Android application for controlling a 4-servo robot arm with MySQL database integration and PHP backend.

## 📋 Features

- **4 Motor Control**: Individual sliders for each servo motor (0-180 degrees)
- **Pose Management**: Save, load, and delete robot arm poses
- **Real-time Control**: Reset, save, and run motor positions
- **Database Integration**: MySQL database with Position and Run tables
- **Web Interface**: PHP page showing state engine corners and run history
- **Modern UI**: Clean, intuitive interface matching the design requirements



## 📱 Screenshots
the database 
<img width="1919" height="883" alt="image" src="https://github.com/user-attachments/assets/b45805dc-7aec-431d-845e-a293b4073261" />


<img width="1434" height="869" alt="image" src="https://github.com/user-attachments/assets/5045a011-4bbf-4350-b6e7-6f7996d9bc0d" />
<img width="1542" height="457" alt="image" src="https://github.com/user-attachments/assets/da640176-3a69-45da-862b-52bbfc4114b1" />

<img width="540" height="1200" alt="image" src="https://github.com/user-attachments/assets/496f745c-dec3-4bc4-9e94-7269c11a85e3" />

<img width="540" height="1200" alt="image" src="https://github.com/user-attachments/assets/1e7af406-8505-4311-99b8-3f436e6019e7" />



# now we will run the app and test it ... -->

<img width="540" height="1200" alt="image" src="https://github.com/user-attachments/assets/815f37b9-b979-4454-a09a-a821832bdcc0" />

<img width="540" height="1200" alt="image" src="https://github.com/user-attachments/assets/b9c31376-415e-4d87-92b8-2fc350305c94" />


<img width="540" height="1200" alt="image" src="https://github.com/user-attachments/assets/adc14b4e-9ddd-44c9-b211-240271d0ad11" />





## 🏗️ System Architecture

```
Flutter App (Android) ↔ PHP Backend ↔ MySQL Database
                              ↓
                        Web Interface (state_engine.php)
```

## 📁 Project Structure

```
app/
├── lib/
│   └── main.dart                 # Flutter application
├── php/
│   ├── config.php               # Database configuration
│   ├── database.sql             # Database setup script
│   ├── get_run_pose.php         # Retrieve saved poses
│   ├── save_pose.php            # Save new poses
│   ├── run_motors.php           # Execute motor movements
│   ├── update_status.php        # Update run status to 0
│   ├── delete_pose.php          # Delete saved poses
│   └── state_engine.php         # Web interface
├── pubspec.yaml                 # Flutter dependencies
└── README.md                    # This file
```

## 🚀 Setup Instructions

### 1. Database Setup (XAMPP)

1. **Start XAMPP**:
   - Start Apache and MySQL services
   - Open phpMyAdmin: `http://localhost/phpmyadmin`

2. **Create Database**:
   - Import `php/database.sql` into phpMyAdmin
   - Or run the SQL commands manually

3. **Database Tables**:
   - **RobotPoses**: Stores saved poses (pose_id, motor1_angle, motor2_angle, motor3_angle, motor4_angle, created_at)
   - **Run**: Tracks executed poses (run_id, pose_id, status, executed_at)

### 2. PHP Backend Setup

1. **Copy PHP Files**:
   - Copy the `php/` folder to your XAMPP htdocs directory
   - Example: `C:\xampp\htdocs\robot_arm\`

2. **Configure Database**:
   - Edit `php/config.php` if needed (default: localhost, root, no password)

3. **Test Backend**:
   - Visit: `http://localhost/robot_arm/state_engine.php`
   - Should display the web interface

### 3. Flutter App Setup

1. **Install Dependencies**:
   ```bash
   flutter pub get
   ```

2. **Configure Server URL**:
   - Edit `lib/main.dart` line 47-48
   - For Android emulator: `http://10.0.2.2/robot_arm`
   - For real device: `http://YOUR_COMPUTER_IP/robot_arm`

3. **Run the App**:
   ```bash
   flutter run
   ```

## 🎮 Usage

### Flutter App Interface

1. **Motor Control**:
   - Use sliders to adjust each motor (0-180°)
   - Real-time value display

2. **Action Buttons**:
   - **Reset**: Restore default angles (90° each)
   - **Save Pose**: Store current motor values
   - **Run**: Execute current motor positions

3. **Saved Poses**:
   - View all saved poses from database
   - **Load** (▶️): Apply saved angles to sliders
   - **Delete** (🗑️): Remove pose from database

4. **Web Interface**:
   - Tap web icon in app bar
   - Opens state_engine.php in browser

### Web Interface Features

- **Current State Engine Corners**: All saved poses
- **Run History**: Execution records with status
- **Statistics**: Success rate and execution counts
- **Real-time Updates**: Refresh button for latest data

## 🔧 API Endpoints

| Endpoint | Method | Purpose |
|----------|--------|---------|
| `get_run_pose.php` | GET | Retrieve all saved poses |
| `save_pose.php` | POST | Save new pose |
| `run_motors.php` | POST | Execute motor movement |
| `update_status.php` | POST | Set run status to 0 |
| `delete_pose.php` | POST | Delete saved pose |

## 📱 App Features

### Motor Control
- 4 individual sliders (0-180°)
- Real-time value display
- Smooth interaction

### Pose Management
- Save current motor positions
- Load previously saved poses
- Delete unwanted poses
- Automatic database sync

### Status Tracking
- Run history with timestamps
- Status indicators (running/completed)
- Success rate statistics

## 🎨 UI Design

- **Clean Interface**: Minimalist design with purple accents
- **Responsive Layout**: Adapts to different screen sizes
- **Intuitive Controls**: Easy-to-use sliders and buttons
- **Visual Feedback**: SnackBar notifications for actions

## 🔒 Security Features

- Input validation (0-180° range)
- SQL injection prevention (prepared statements)
- CORS headers for cross-origin requests
- Error handling and user feedback

## 🛠️ Customization

### Motor Default Values
Edit lines 37-40 in `lib/main.dart`:
```dart
final double defaultMotor1 = 90.0;
final double defaultMotor2 = 90.0;
final double defaultMotor3 = 90.0;
final double defaultMotor4 = 90.0;
```

### Server Configuration
Edit lines 47-48 in `lib/main.dart`:
```dart
final String baseUrl = 'http://10.0.2.2/robot_arm'; // Emulator
// final String baseUrl = 'http://192.168.1.100/robot_arm'; // Real device
```

### Database Settings
Edit `php/config.php`:
```php
$host = 'localhost';
$dbname = 'robot_arm_db';
$username = 'root';
$password = '';
```

## 🐛 Troubleshooting

### Common Issues

1. **Connection Error**:
   - Check XAMPP is running
   - Verify server URL in app
   - Test PHP files in browser

2. **Database Error**:
   - Ensure MySQL is running
   - Check database exists
   - Verify table structure

3. **App Not Loading Poses**:
   - Check network permissions
   - Verify PHP file paths
   - Test API endpoints manually

### Debug Steps

1. Check XAMPP logs: `C:\xampp\apache\logs\error.log`
2. Test PHP files directly in browser
3. Use Flutter debug console for app errors
4. Verify database connection in phpMyAdmin

## 📄 License

This project is created for educational and development purposes.

## 🤝 Contributing

Feel free to submit issues and enhancement requests!

**Made with ❤️ for robotics enthusiasts**


