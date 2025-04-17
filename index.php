<?php
session_start();

if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header("Location: login.php");
    exit;
}

$page = isset($_GET['page']) ? $_GET['page'] : 'home';
$submitted = false;
$message = '';
$user_question = '';
$answer = '';

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "AILogdatabase";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['ask_question'])) {
    $user_question = htmlspecialchars($_POST['question'] ?? '');
    if ($user_question) {
        $sql = "SELECT answer FROM faq WHERE LOWER(question) LIKE ?";
        $stmt = $conn->prepare($sql);
        $search_term = "%" . strtolower($user_question) . "%";
        $stmt->bind_param("s", $search_term);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        $answer = $row ? $row['answer'] : "No answer found.";
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit'])) {
    $name = htmlspecialchars($_POST['name'] ?? '');
    $email = htmlspecialchars($_POST['email'] ?? '');
    $message_text = htmlspecialchars($_POST['message'] ?? '');

    if ($name && $email && $message_text) {
        // Email configuration
        $to = "tusharrisu2003@gmail.com"; // Retained as provided
        $subject = "New Contact Query from AI LogMaster";
        $body = "Name: $name\nEmail: $email\nQuery: $message_text\n";
        $headers = "From: $email\r\nReply-To: $email\r\n";

        // Send email
        if (mail($to, $subject, $body, $headers)) {
            $message = "Thanks, $name! Your query has been received and sent to our team. We will contact you at $email.";
            $submitted = true;
        } else {
            $message = "Sorry, $name! There was an error sending your query. Please try again later.";
        }
    } else {
        $message = "Please fill in all fields.";
    }
}

if (isset($_GET['logout'])) {
    session_destroy();
    header("Location: login.php");
    exit;
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AI LogMaster</title>
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: '#3498db',
                        secondary: '#2c3e50',
                        accent: '#2980b9',
                    },
                },
            },
        }
    </script>
    <style>
        .glassmorphism {
            background: rgba(255, 255, 255, 0.2);
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
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
<body class="font-['Inter'] bg-gradient-to-br from-indigo-200 via-purple-200 to-pink-200 text-gray-800 min-h-screen flex flex-col">
    <header class="bg-secondary text-white py-4 fixed w-full top-0 z-10 shadow-lg">
        <div class="max-w-7xl mx-auto flex justify-between items-center px-4 sm:px-6">
            <div class="text-2xl font-bold text-primary">AI LogMaster</div>
            <nav>
                <ul class="flex space-x-6">
                    <li><a href="?page=home" class="text-gray-300 hover:text-primary transition-colors duration-300 <?php echo $page === 'home' ? 'text-primary font-semibold' : ''; ?>">Home</a></li>
                    <li><a href="?page=features" class="text-gray-300 hover:text-primary transition-colors duration-300 <?php echo $page === 'features' ? 'text-primary font-semibold' : ''; ?>">Features</a></li>
                    <li><a href="?page=contact" class="text-gray-300 hover:text-primary transition-colors duration-300 <?php echo $page === 'contact' ? 'text-primary font-semibold' : ''; ?>">Contact</a></li>
                    <li><a href="dashboard.php" class="text-gray-300 hover:text-primary transition-colors duration-300 <?php echo basename($_SERVER['PHP_SELF']) === 'dashboard.php' ? 'text-primary font-semibold' : ''; ?>">Dashboard</a></li>
                    <li><a href="?logout=true" class="text-gray-300 hover:text-red-400 transition-colors duration-300">Logout</a></li>
                </ul>
            </nav>
        </div>
    </header>

    <main class="flex-grow mt-24 text-center px-4 sm:px-6 py-12">
        <?php if ($page === 'home'): ?>
            <div class="max-w-4xl mx-auto animate-slideIn">
                <h1 class="text-4xl font-bold mb-6 text-secondary">Welcome to AI LogMaster</h1>
                <div class="glassmorphism rounded-2xl shadow-xl p-8 mb-8 border border-white/30">
                    <p class="text-lg text-gray-700 mb-4">AI LogMaster is your ultimate solution for managing and analyzing server logs with cutting-edge artificial intelligence. Whether you're a system administrator or a developer, our platform simplifies log monitoring, provides real-time alerts, and offers actionable insights to keep your systems running smoothly.</p>
                    <p class="text-lg text-gray-600">Start exploring now or manage users in the dashboard!</p>
                </div>
                <div class="flex justify-center space-x-4 mb-10">
                    <button onclick="window.location.href='ask.php'" class="get-started bg-primary text-white px-8 py-3 rounded-xl font-semibold text-lg hover:bg-accent hover:scale-105 active:scale-95 transition-all duration-300 shadow-md">
                        Get Started
                    </button>
                    <a href="dashboard.php" class="bg-indigo-600 text-white px-8 py-3 rounded-xl font-semibold text-lg hover:bg-indigo-700 hover:scale-105 active:scale-95 transition-all duration-300 shadow-md">
                        View Dashboard
                    </a>
                </div>
                <div class="glassmorphism rounded-2xl shadow-xl p-8 border border-white/30">
                    <h2 class="text-2xl font-semibold mb-6 text-secondary">Key Features</h2>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div class="glassmorphism p-4 rounded-lg border-l-4 border-primary hover:shadow-lg hover:-translate-y-1 transition-all duration-300">Smart Log Analysis</div>
                        <div class="glassmorphism p-4 rounded-lg border-l-4 border-primary hover:shadow-lg hover:-translate-y-1 transition-all duration-300">Real-Time Alerts</div>
                        <div class="glassmorphism p-4 rounded-lg border-l-4 border-primary hover:shadow-lg hover:-translate-y-1 transition-all duration-300">Easy Log Management</div>
                        <div class="glassmorphism p-4 rounded-lg border-l-4 border-primary hover:shadow-lg hover:-translate-y-1 transition-all duration-300">Customizable Dashboards</div>
                        <div class="glassmorphism p-4 rounded-lg border-l-4 border-primary hover:shadow-lg hover:-translate-y-1 transition-all duration-300">Automated Reporting</div>
                    </div>
                </div>
            </div>
        <?php elseif ($page === 'features'): ?>
            <div class="max-w-4xl mx-auto animate-slideIn">
                <h1 class="text-4xl font-bold mb-6 text-secondary">Features</h1>
                <div class="glassmorphism rounded-2xl shadow-xl p-8 mb-8 border border-white/30">
                    <p class="text-lg text-gray-700 mb-4">AI LogMaster is designed to revolutionize how you manage and interpret server logs. Leveraging advanced AI technology, our platform offers a comprehensive suite of tools to enhance your system monitoring and maintenance processes.</p>
                    <p class="text-lg text-gray-600">Explore each feature below or check user activity in the dashboard!</p>
                </div>
                <div class="glassmorphism rounded-2xl shadow-xl p-8 border border-white/30">
                    <h2 class="text-2xl font-semibold mb-6 text-secondary">Key Features</h2>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div class="glassmorphism p-4 rounded-lg border-l-4 border-primary hover:shadow-lg hover:-translate-y-1 transition-all duration-300">Smart Log Analysis - Automatically detects patterns and anomalies in your logs using AI algorithms.</div>
                        <div class="glassmorphism p-4 rounded-lg border-l-4 border-primary hover:shadow-lg hover:-translate-y-1 transition-all duration-300">Real-Time Alerts - Get instant notifications for critical issues with customizable thresholds.</div>
                        <div class="glassmorphism p-4 rounded-lg border-l-4 border-primary hover:shadow-lg hover:-translate-y-1 transition-all duration-300">Easy Log Management - Streamlined interface to organize, filter, and archive logs efficiently.</div>
                        <div class="glassmorphism p-4 rounded-lg border-l-4 border-primary hover:shadow-lg hover:-translate-y-1 transition-all duration-300">Customizable Dashboards - Build tailored dashboards to visualize log data according to your needs.</div>
                        <div class="glassmorphism p-4 rounded-lg border-l-4 border-primary hover:shadow-lg hover:-translate-y-1 transition-all duration-300">Automated Reporting - Generate detailed reports on demand or schedule them for regular delivery.</div>
                        <div class="glassmorphism p-4 rounded-lg border-l-4 border-primary hover:shadow-lg hover:-translate-y-1 transition-all duration-300">Advanced Search Capabilities - Quickly find specific log entries with powerful search tools.</div>
                        <div class="glassmorphism p-4 rounded-lg border-l-4 border-primary hover:shadow-lg hover:-translate-y-1 transition-all duration-300">Integration with Third-Party Tools - Seamlessly connect with popular monitoring and analytics platforms.</div>
                        <div class="glassmorphism p-4 rounded-lg border-l-4 border-primary hover:shadow-lg hover:-translate-y-1 transition-all duration-300">Secure Data Handling - Ensure your log data is protected with top-tier encryption and access controls.</div>
                    </div>
                </div>
            </div>
        <?php elseif ($page === 'contact'): ?>
            <div class="max-w-md mx-auto animate-slideIn">
                <h1 class="text-4xl font-bold mb-6 text-secondary">Contact</h1>
                <?php if ($submitted): ?>
                    <div class="glassmorphism rounded-2xl shadow-xl p-6 text-center border border-white/30">
                        <p class="text-lg text-gray-700"><?php echo $message; ?></p>
                    </div>
                <?php else: ?>
                    <div class="glassmorphism rounded-2xl shadow-xl p-8 border border-white/30">
                        <form method="POST" class="space-y-4">
                            <div>
                                <input type="text" name="name" placeholder="Your Name" required 
                                       class="w-full p-3 bg-white/50 border border-gray-300 rounded-lg text-sm focus:border-primary focus:ring-2 focus:ring-primary/30 focus:outline-none transition-all duration-300 placeholder-gray-400">
                            </div>
                            <div>
                                <input type="email" name="email" placeholder="Your Email" required 
                                       class="w-full p-3 bg-white/50 border border-gray-300 rounded-lg text-sm focus:border-primary focus:ring-2 focus:ring-primary/30 focus:outline-none transition-all duration-300 placeholder-gray-400">
                            </div>
                            <div>
                                <textarea name="message" placeholder="Your Query" rows="4" required 
                                          class="w-full p-3 bg-white/50 border border-gray-300 rounded-lg text-sm focus:border-primary focus:ring-2 focus:ring-primary/30 focus:outline-none transition-all duration-300 placeholder-gray-400"></textarea>
                            </div>
                            <button type="submit" name="submit" 
                                    class="w-full p-3 bg-primary text-white rounded-lg text-sm font-semibold hover:bg-accent hover:scale-105 active:scale-95 transition-all duration-300 shadow-md">
                                Send
                            </button>
                        </form>
                    </div>
                <?php endif; ?>
            </div>
        <?php endif; ?>
    </main>

    <footer class="bg-secondary text-white text-center py-4 shadow-inner mt-auto">
        <p>© <?php echo date('Y'); ?> AI LogMaster</p>
    </footer>

    <script>
        // Redirect to ask.php when Get Started is clicked
        document.addEventListener('DOMContentLoaded', function() {
            const getStartedBtn = document.querySelector('.get-started');
            if (getStartedBtn) {
                getStartedBtn.addEventListener('click', function() {
                    window.location.href = 'ask.php';
                });
            }
        });
    </script>
</body>
</html>