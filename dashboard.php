<?php
session_start();

// Check if user is logged in
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header("Location: login.php");
    exit;
}
// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "AILogdatabase";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch all users
$sql = "SELECT id, email FROM users";
$result = $conn->query($sql);
$users = [];
if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $users[] = $row;
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Dashboard - AI LogMaster</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        .bg-gradient-custom {
            background: linear-gradient(135deg, #e0eafc 0%, #cfdef3 100%);
        }
        .dashboard-card {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
        }
        @keyframes slideIn {
            from { opacity: 0; transform: translateY(-20px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .animate-slideIn {
            animation: slideIn 0.6s ease-out;
        }
    </style>
</head>
<body class="bg-gradient-custom min-h-screen font-['Inter']">
    <header class="bg-gray-800 text-white fixed w-full shadow-xl z-10">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center py-4">
                <div class="flex items-center">
                    <span class="text-blue-400 text-2xl font-bold ml-2">AI LogMaster</span>
                </div>
                <nav class="hidden md:flex space-x-8">
                    <a href="index.php?page=home" class="text-gray-300 hover:text-blue-400 transition-colors">Home</a>
                    <a href="index.php?page=features" class="text-gray-300 hover:text-blue-400 transition-colors">Features</a>
                    <a href="index.php?page=contact" class="text-gray-300 hover:text-blue-400 transition-colors">Contact</a>
                    <a href="?logout=true" class="text-gray-300 hover:text-red-400 transition-colors">Logout</a>
                </nav>
            </div>
        </div>
    </header>

    <main class="pt-24 pb-16 px-4 sm:px-6 lg:px-8">
        <div class="max-w-6xl mx-auto animate-slideIn">
            <div class="dashboard-card rounded-2xl shadow-2xl p-8 mb-8 border border-white/30">
                <h1 class="text-3xl font-bold text-gray-800 mb-6">User Access Dashboard</h1>
                <p class="text-gray-600 mb-6">View and manage users with login rights to the system</p>
                
                <div class="overflow-x-auto">
                    <table class="min-w-full bg-white rounded-lg overflow-hidden">
                        <thead class="bg-gray-100">
                            <tr>
                                <th class="py-3 px-4 text-left text-sm font-semibold text-gray-700">ID</th>
                                <th class="py-3 px-4 text-left text-sm font-semibold text-gray-700">Email</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            <?php if (!empty($users)): ?>
                                <?php foreach ($users as $user): ?>
                                    <tr class="hover:bg-gray-50 transition-colors">
                                        <td class="py-3 px-4 text-gray-600"><?php echo htmlspecialchars($user['id']); ?></td>
                                        <td class="py-3 px-4 text-gray-600"><?php echo htmlspecialchars($user['email']); ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="2" class="py-4 px-4 text-center text-gray-500">No users found</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
            
            <div class="text-center">
                <a href="index.php?page=features" class="inline-flex items-center px-6 py-3 border border-transparent text-base font-medium rounded-xl shadow-sm text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-all duration-300">
                    Back to Features
                </a>
            </div>
        </div>
    </main>

    <footer class="bg-gray-800 text-white py-4 shadow-lg mt-auto">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <p>© <?php echo date('Y'); ?> AI LogMaster. All rights reserved.</p>
        </div>
    </footer>
</body>
</html>