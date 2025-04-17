<?php
// Database connection parameters
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "AIlogdatabase";

$conn = new mysqli($servername, $username, $password);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Create database
// $sql = "CREATE DATABASE IF NOT EXISTS $dbname";
// if ($conn->query($sql) === TRUE) {
//     echo "Database '$dbname' created or already exists successfully.<br>";
// } else {
//     echo "Error creating database: " . $conn->error . "<br>";
// }

// Select the database
$conn->select_db($dbname);

// Create faq table
// $sql = "CREATE TABLE IF NOT EXISTS faq (
//     id INT AUTO_INCREMENT PRIMARY KEY,
//     question VARCHAR(255) NOT NULL,
//     answer TEXT NOT NULL
// )";
// if ($conn->query($sql) === TRUE) {
//     echo "Table 'faq' created or already exists successfully.<br>";
// } else {
//     echo "Error creating table: " . $conn->error . "<br>";
// }

// // Create users table
// $sql = "CREATE TABLE IF NOT EXISTS users (
//     id INT AUTO_INCREMENT PRIMARY KEY,
//     email VARCHAR(255) NOT NULL UNIQUE,
//     password VARCHAR(255) NOT NULL
// )";

// if ($conn->query($sql) === TRUE) {
//     echo "Table 'users' created or already exists successfully.<br>";
// } else {
//     echo "Error creating table: " . $conn->error . "<br>";
// }

// Insert sample data into faq
$sql = "INSERT INTO faq (question, answer) VALUES
    ('what is ai logmaster', 'AI LogMaster manages server logs.'),
    ('how does it work', 'It analyzes logs with AI.')";
if ($conn->query($sql) === TRUE) {
    echo "Sample data inserted into 'faq' successfully.<br>";
} else {
    echo "Error inserting data into 'faq': " . $conn->error . "<br>";
}

$conn->close();
echo "Database setup completed!";
?>