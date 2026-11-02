<?php
$servername = "localhost";
$username = "root"; // XAMPP ka default username 'root' hota hai
$password = "";     // XAMPP ka default password khali (blank) hota hai
$dbname = "website_db";

// 1. Database se connection banayein
$conn = new mysqli($servername, $username, $password);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// 2. Database automatically create karna agar pehle se na ho
$sql_db = "CREATE DATABASE IF NOT EXISTS website_db";
$conn->query($sql_db);

// Database select karein
$conn->select_db($dbname);

// 3. Table automatically create karna agar pehle se na ho
$sql_table = "CREATE TABLE IF NOT EXISTS form_submissions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL,
    gender VARCHAR(20) NOT NULL,
    course VARCHAR(100) NOT NULL,
    message TEXT NOT NULL,
    submitted_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)";
$conn->query($sql_table);

// Add the new fields to tables created by earlier versions of the form.
foreach ([
    "gender" => "VARCHAR(20) NOT NULL DEFAULT ''",
    "course" => "VARCHAR(100) NOT NULL DEFAULT ''"
] as $column => $definition) {
    $result = $conn->query("SHOW COLUMNS FROM form_submissions LIKE '$column'");
    if ($result === FALSE) {
        die("Error checking table columns: " . $conn->error);
    }
    if ($result->num_rows === 0 && !$conn->query("ALTER TABLE form_submissions ADD COLUMN $column $definition")) {
        die("Error updating table: " . $conn->error);
    }
}

// 4. Form submit hone par data database me insert karna
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $conn->real_escape_string($_POST['name']);
    $email = $conn->real_escape_string($_POST['email']);
    $gender = $conn->real_escape_string($_POST['gender']);
    $course = $conn->real_escape_string($_POST['course']);
    $message = $conn->real_escape_string($_POST['message']);

    $sql_insert = "INSERT INTO form_submissions (name, email, gender, course, message) VALUES ('$name', '$email', '$gender', '$course', '$message')";

    if ($conn->query($sql_insert) === TRUE) {
        echo "<h2 style='color: green; text-align: center; margin-top: 20vh;'>Thank you! Form data successfully save ho gaya hai.</h2>";
        echo "<p style='text-align: center;'><a href='index.html'>Wapas Form par jayein</a></p>";
    } else {
        echo "Error: " . $sql_insert . "<br>" . $conn->error;
    }
}

$conn->close();
?>