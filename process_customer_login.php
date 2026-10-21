<?php
session_start(); // Start the session at the very beginning

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "customerreg_db"; // Make sure this matches your actual database name

// Create a new mysqli connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    // Set a session error message for connection failure
    $_SESSION['login_error'] = "Database connection failed. Please try again later.";
    header("Location: customer_login.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve form data
    $email = $_POST['email'];
    $entered_password = $_POST['password'];

    // Prepare the SQL statement to prevent SQL injection
    $sql = "SELECT id, full_name, password FROM customers WHERE email = ?";
    $stmt = $conn->prepare($sql);

    if ($stmt === false) {
        // Prepare error
        $_SESSION['login_error'] = "An error occurred. Please try again.";
        header("Location: customer_login.php");
        exit();
    }

    // Bind the email parameter
    $stmt->bind_param("s", $email);
    $stmt->execute();

    // Get the result set
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        // Fetch the user data
        $row = $result->fetch_assoc();
        $hashed_password_from_db = $row['password'];

        // Verify the password
        if (password_verify($entered_password, $hashed_password_from_db)) {
            // Password is correct, set session variables
            $_SESSION['user_id'] = $row['id'];
            $_SESSION['user_name'] = $row['full_name'];
            $_SESSION['user_type'] = 'customer';

            // Redirect to customer profile page
            header("Location: customer_profile.php");
            exit();
        } else {
            // Password does not match
            $_SESSION['login_error'] = "Invalid email or password";
            header("Location: customer_login.php");
            exit();
        }
    } else {
        // No user found with that email
        $_SESSION['login_error'] = "Invalid email or password";
        header("Location: customer_login.php");
        exit();
    }

    // Close the statement
    $stmt->close();
} else {
    // If script accessed directly without POST, redirect to login
    header("Location: customer_login.php");
    exit();
}

// Close the database connection
$conn->close();
?>