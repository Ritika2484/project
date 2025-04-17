<?php
session_start();

$signup_error = '';
$signup_success = '';

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "AILogdatabase";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['signup_submit'])) {
    $email = htmlspecialchars($_POST['email']);
    $password = htmlspecialchars($_POST['password']);

    if (empty($email) || empty($password)) {
        $signup_error = "Email and password are required.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $signup_error = "Invalid email format.";
    } else {
        $sql = "SELECT email FROM users WHERE email = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $email);
        $stmt->execute();
        if ($stmt->get_result()->num_rows > 0) {
            $signup_error = "Email already exists.";
        } else {
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            $sql = "INSERT INTO users (email, password) VALUES (?, ?)";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("ss", $email, $hashed_password);
            if ($stmt->execute()) {
                $signup_success = "Signup successful! You can now <a href='login.php' class='underline font-medium hover:text-indigo-600 transition-colors'>login</a>.";
            } else {
                $signup_error = "Signup failed: " . $conn->error;
            }
        }
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign Up - AI LogMaster</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        .bg-auth {
            background-image: linear-gradient(rgba(0, 0, 0, 0.5), rgba(0, 0, 0, 0.5)), url('https://source.unsplash.com/random/1920x1080/?technology,abstract');
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
        }
        .form-grid {
            background: linear-gradient(135deg, rgba(255,255,255,0.9) 0%, rgba(246,247,249,0.9) 100%);
            backdrop-filter: blur(8px);
        }
    </style>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: {
                            50: '#f0f9ff',
                            100: '#e0f2fe',
                            500: '#6366f1',
                            600: '#4f46e5',
                            700: '#4338ca',
                        },
                        gradient: {
                            start: '#667eea',
                            end: '#764ba2'
                        }
                    },
                    fontFamily: {
                        sans: ['Inter', 'sans-serif'],
                    },
                    boxShadow: {
                        'xl': '0 20px 25px -5px rgba(0, 0, 0, 0.2), 0 10px 10px -5px rgba(0, 0, 0, 0.1)',
                        '2xl': '0 25px 50px -12px rgba(0, 0, 0, 0.3)',
                        '3xl': '0 35px 60px -15px rgba(0, 0, 0, 0.35)'
                    }
                }
            }
        }
    </script>
</head>
<body class="min-h-screen bg-auth flex items-center justify-center p-4">
    <div class="max-w-md w-full form-grid rounded-2xl shadow-3xl overflow-hidden border border-white border-opacity-20">
        <div class="p-8 space-y-6">
            <div class="text-center">
                <h1 class="text-3xl font-bold text-gray-800">Join AI LogMaster</h1>
                <p class="mt-2 text-gray-600">
                    Create your account to get started
                </p>
            </div>

            <?php if ($signup_error): ?>
                <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 rounded-lg shadow-sm">
                    <p><?php echo $signup_error; ?></p>
                </div>
            <?php endif; ?>

            <?php if ($signup_success): ?>
                <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 rounded-lg shadow-sm">
                    <p><?php echo $signup_success; ?></p>
                </div>
            <?php else: ?>
                <form class="space-y-5" method="POST">
                    <div class="space-y-2">
                        <label for="email" class="block text-sm font-medium text-gray-700">Email Address</label>
                        <input id="email" name="email" type="email" required
                            class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition-all shadow-sm"
                            placeholder="your@email.com"
                            value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>">
                    </div>
                    
                    <div class="space-y-2">
                        <label for="password" class="block text-sm font-medium text-gray-700">Password</label>
                        <input id="password" name="password" type="password" required
                            class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition-all shadow-sm"
                            placeholder="••••••••">
                    </div>

                    <button type="submit" name="signup_submit"
                        class="w-full bg-gradient-to-r from-primary-600 to-primary-700 hover:from-primary-700 hover:to-primary-800 text-white font-medium py-3 px-4 rounded-xl transition-all duration-300 shadow-lg hover:shadow-xl transform hover:-translate-y-0.5 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2">
                        Create Account
                    </button>
                </form>
            <?php endif; ?>

            <div class="text-center text-sm text-gray-600 pt-2">
                <p>Already have an account? <a href="login.php" class="text-primary-600 hover:text-primary-800 font-medium transition-colors">Sign in here</a></p>
            </div>
        </div>
        
        <div class="bg-gradient-to-r from-primary-500 to-purple-600 h-2 w-full"></div>
    </div>
</body>
</html>