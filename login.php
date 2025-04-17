<?php
// Start session at the very beginning
session_start();

// Initialize error variable
$login_error = '';

// Database connection parameters
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "AILogdatabase";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Handle login submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['login_submit'])) {
    $email = htmlspecialchars($_POST['email']);
    $password = htmlspecialchars($_POST['password']);

    $sql = "SELECT password FROM users WHERE email = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $row = $result->fetch_assoc();
        if (password_verify($password, $row['password'])) {
            $_SESSION['logged_in'] = true;
            $_SESSION['email'] = $email;
            // Redirect to home page
            header("Location: index.php?page=home");
            exit; // Ensure script stops after redirection
        } else {
            $login_error = "Invalid password.";
        }
    } else {
        $login_error = "Email not found.";
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - AI LogMaster</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        @keyframes slideIn {
            from { opacity: 0; transform: translateY(-30px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .animate-slideIn {
            animation: slideIn 0.6s ease-out;
        }
        .glassmorphism {
            background: rgba(255, 255, 255, 0.2);
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
        }
    </style>
</head>
<body class="min-h-screen bg-gradient-to-br from-indigo-200 via-purple-200 to-pink-200 flex items-center justify-center p-4 font-sans">
    <div class="glassmorphism p-8 rounded-2xl shadow-2xl w-full max-w-sm animate-slideIn border border-white/30">
        <div class="text-center mb-6">
            <h1 class="text-3xl font-bold text-gray-800 tracking-tight">AI LogMaster</h1>
            <p class="text-gray-600 text-sm mt-1">Sign in to your account</p>
        </div>
        <form method="POST">
            <div class="mb-5">
                <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                <input type="email" name="email" id="email" placeholder="Enter your email" required 
                       class="w-full p-3 bg-white/50 border border-gray-300 rounded-lg text-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-300 focus:outline-none transition-all duration-300 placeholder-gray-400">
            </div>
            <div class="mb-6">
                <label for="password" class="block text-sm font-medium text-gray-700 mb-1">Password</label>
                <input type="password" name="password" id="password" placeholder="Enter your password" required 
                       class="w-full p-3 bg-white/50 border border-gray-300 rounded-lg text-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-300 focus:outline-none transition-all duration-300 placeholder-gray-400">
            </div>
            <button type="submit" name="login_submit" 
                    class="w-full p-3 bg-indigo-600 text-white rounded-lg text-sm font-semibold hover:bg-indigo-700 hover:scale-105 active:scale-95 transition-all duration-300 shadow-md">
                Sign In
            </button>
        </form>
        <?php if ($login_error): ?>
            <p class="text-red-500 text-xs mt-4 text-center bg-red-100/50 py-2 rounded-lg"><?php echo $login_error; ?></p>
        <?php endif; ?>
        <p class="text-center mt-4 text-sm text-gray-600">
            Don't have an account? 
            <a href="signup.php" class="text-indigo-600 font-medium hover:text-indigo-800 transition-colors">Sign up</a>
        </p>
    </div>
</body>
</html>