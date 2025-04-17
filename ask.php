<?php
session_start();

if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header("Location: login.php");
    exit;
}

$user_question = '';
$answer = '';
$admin_message = '';
$new_question = '';
$new_answer = '';

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "AILogdatabase";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Handle question submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['ask_question'])) {
        $user_question = htmlspecialchars($_POST['question'] ?? '');
        if ($user_question) {
            $sql = "SELECT answer FROM faq WHERE LOWER(question) LIKE ?";
            $stmt = $conn->prepare($sql);
            $search_term = "%" . strtolower($user_question) . "%";
            $stmt->bind_param("s", $search_term);
            $stmt->execute();
            $result = $stmt->get_result();
            $row = $result->fetch_assoc();
            $answer = $row ? $row['answer'] : "No answer found. Would you like to add this question to our database?";
        }
    }
    
    // Handle adding new Q&A
    if (isset($_POST['add_question'])) {
        $new_question = htmlspecialchars($_POST['new_question'] ?? '');
        $new_answer = htmlspecialchars($_POST['new_answer'] ?? '');
        
        if ($new_question && $new_answer) {
            $sql = "INSERT INTO faq (question, answer) VALUES (?, ?)";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("ss", $new_question, $new_answer);
            
            if ($stmt->execute()) {
                $admin_message = "Question added successfully!";
                $new_question = '';
                $new_answer = '';
            } else {
                $admin_message = "Error adding question: " . $conn->error;
            }
        } else {
            $admin_message = "Both question and answer are required.";
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
    <title>Ask - AI LogMaster</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        .bg-gradient-custom {
            background: linear-gradient(135deg, #e0eafc 0%, #cfdef3 100%);
        }
        .answer-container {
            background: rgba(255, 255, 255, 0.9);
            backdrop-filter: blur(10px);
        }
        .admin-panel {
            background: rgba(255, 255, 255, 0.95);
        }
        .tab-active {
            border-bottom: 3px solid #3b82f6;
            color: #3b82f6;
            font-weight: 500;
        }
    </style>
</head>
<body class="bg-gradient-custom min-h-screen">
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
                    <a href="logout.php" class="text-gray-300 hover:text-red-400 transition-colors">Logout</a>
                </nav>
            </div>
        </div>
    </header>

    <main class="pt-24 pb-16 px-4 sm:px-6 lg:px-8">
        <div class="max-w-4xl mx-auto">
            <!-- Tab Navigation -->
            <div class="flex border-b border-gray-200 mb-8">
                <button onclick="switchTab('ask')" id="ask-tab" class="tab-active px-4 py-2 text-sm font-medium">
                    Ask a Question
                </button>
                <button onclick="switchTab('add')" id="add-tab" class="px-4 py-2 text-sm font-medium text-gray-500 hover:text-gray-700">
                    Add New Q&A
                </button>
            </div>

            <!-- Ask Question Tab -->
            <div id="ask-section" class="space-y-6">
                <div class="text-center">
                    <h1 class="text-3xl font-bold text-gray-800 mb-2">Ask Our Knowledge Base</h1>
                    <p class="text-lg text-gray-600">Get answers to your questions instantly</p>
                </div>

                <form method="POST" class="mb-8">
                    <div class="flex flex-col sm:flex-row gap-4">
                        <input 
                            type="text" 
                            name="question" 
                            placeholder="Type your question here..." 
                            value="<?php echo $user_question; ?>"
                            class="flex-grow px-6 py-4 rounded-xl border-2 border-blue-400 focus:border-blue-500 focus:ring-2 focus:ring-blue-200 shadow-sm"
                            required
                        >
                        <button 
                            type="submit" 
                            name="ask_question"
                            class="px-8 py-4 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-xl shadow-lg hover:shadow-xl transition-all"
                        >
                            Ask
                        </button>
                    </div>
                </form>

                <?php if ($answer): ?>
                    <div class="answer-container rounded-2xl p-6 shadow-2xl mb-8">
                        <div class="flex items-start">
                            <div class="flex-shrink-0 h-10 w-10 rounded-full bg-blue-100 flex items-center justify-center">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                                </svg>
                            </div>
                            <div class="ml-4">
                                <h3 class="text-lg font-medium text-gray-900 mb-2">Answer:</h3>
                                <div class="prose max-w-none text-gray-700">
                                    <?php echo nl2br($answer); ?>
                                </div>
                                <?php if (strpos($answer, 'add this question') !== false): ?>
                                    <div class="mt-4">
                                        <a href="#add-section" onclick="switchTab('add')" class="text-blue-600 hover:text-blue-800 font-medium">
                                            Click here to add this question with an answer
                                        </a>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Add Q&A Tab -->
            <div id="add-section" class="hidden space-y-6">
                <div class="text-center">
                    <h1 class="text-3xl font-bold text-gray-800 mb-2">Add New Question & Answer</h1>
                    <p class="text-lg text-gray-600">Contribute to our knowledge base</p>
                </div>

                <?php if ($admin_message): ?>
                    <div class="<?php echo strpos($admin_message, 'success') !== false ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'; ?> p-4 rounded-lg">
                        <?php echo $admin_message; ?>
                    </div>
                <?php endif; ?>

                <form method="POST" class="space-y-4">
                    <div>
                        <label for="new_question" class="block text-sm font-medium text-gray-700 mb-1">Question</label>
                        <textarea 
                            id="new_question" 
                            name="new_question" 
                            rows="2"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500"
                            required
                        ><?php echo $new_question ?: $user_question; ?></textarea>
                    </div>
                    
                    <div>
                        <label for="new_answer" class="block text-sm font-medium text-gray-700 mb-1">Answer</label>
                        <textarea 
                            id="new_answer" 
                            name="new_answer" 
                            rows="4"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500"
                            required
                        ><?php echo $new_answer; ?></textarea>
                    </div>

                    <div class="flex justify-end space-x-3">
                        <button 
                            type="button"
                            onclick="switchTab('ask')"
                            class="px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50"
                        >
                            Cancel
                        </button>
                        <button 
                            type="submit" 
                            name="add_question"
                            class="px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700"
                        >
                            Add Q&A
                        </button>
                    </div>
                </form>
            </div>
        </div>
        
    </main>

    <footer class="bg-gray-800 text-white py-4 fixed bottom-0 w-full shadow-lg">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <p>&copy; <?php echo date('Y'); ?> AI LogMaster. All rights reserved.</p>
        </div>
    </footer>

    <script>
        function switchTab(tabName) {
            // Hide all sections
            document.getElementById('ask-section').classList.add('hidden');
            document.getElementById('add-section').classList.add('hidden');
            
            // Remove active class from all tabs
            document.getElementById('ask-tab').classList.remove('tab-active', 'text-blue-600');
            document.getElementById('ask-tab').classList.add('text-gray-500');
            document.getElementById('add-tab').classList.remove('tab-active', 'text-blue-600');
            document.getElementById('add-tab').classList.add('text-gray-500');
            
            // Show selected section and mark tab as active
            document.getElementById(tabName + '-section').classList.remove('hidden');
            document.getElementById(tabName + '-tab').classList.add('tab-active', 'text-blue-600');
            document.getElementById(tabName + '-tab').classList.remove('text-gray-500');
            
            // If coming from "no answer found" suggestion, focus on question field
            if (tabName === 'add') {
                document.getElementById('new_question').focus();
            }
        }
    </script>
</body>
</html>