<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Optional: Set session timeout duration (e.g., 30 minutes)
$session_timeout = 1800; // seconds

// Check if session is active and not expired
if (isset($_SESSION['last_activity']) && (time() - $_SESSION['last_activity'] > $session_timeout)) {
    // Session expired
    session_unset();
    session_destroy();
    header("Location: farmer_login.php");
    exit();
}

// Update last activity time
$_SESSION['last_activity'] = time();

$servername = "localhost";
$username = "root"; // Your DB username
$password = ""; // Your DB password
$dbname = "farmerreg_db";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST['email']);
    $password_input = $_POST['password'];

    if (empty($email) || empty($password_input)) {
        $_SESSION['login_error'] = "Please enter both email and password.";
        header("Location: farmer_login.php");
        exit();
    }

    $sql = "SELECT id, full_name, password FROM farmers WHERE email = ?";
    $stmt = $conn->prepare($sql);

    if (!$stmt) {
        $_SESSION['login_error'] = "Database error. Please try again later.";
        header("Location: farmer_login.php");
        exit();
    }

    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $farmer = $result->fetch_assoc();
        if (password_verify($password_input, $farmer['password'])) {
            // Set session variables
            $_SESSION['user_id'] = $farmer['id'];
            $_SESSION['full_name'] = $farmer['full_name'];
            $_SESSION['user_type'] = 'farmer';

            // Initialize last activity timestamp
            $_SESSION['last_activity'] = time();

            $stmt->close();
            $conn->close();
            header("Location: farmer_profile.php");
            exit();
        } else {
            $_SESSION['login_error'] = "Invalid email or password.";
            $stmt->close();
            $conn->close();
            header("Location: farmer_login.php");
            exit();
        }
    } else {
        $_SESSION['login_error'] = "Invalid email or password.";
        $stmt->close();
        $conn->close();
        header("Location: farmer_login.php");
        exit();
    }
}

$conn->close();
?>
