<?php
// Include your database connection code
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "customerreg_db";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve form data
    $full_name = $_POST['full_name'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    $phone_number = $_POST['phone_number'];
    $address = $_POST['address'];
    $city = $_POST['city'];
    $country = $_POST['country'];
    $gender = $_POST['gender'];

    // Basic validation
    if (empty($full_name) || empty($email) || empty($password) || empty($confirm_password)) {
        echo "Full Name, Email, Password, and Confirm Password are required.";
        $conn->close();
        exit;
    }

    // Validate email format
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo "Invalid email format.";
        $conn->close();
        exit;
    }

    // Check if passwords match
    if ($password !== $confirm_password) {
        echo "Passwords do not match.";
        $conn->close();
        exit;
    }

    // Sanitize input
    $full_name = htmlspecialchars($full_name);
    $email = filter_var($email, FILTER_SANITIZE_EMAIL);
    $phone_number = htmlspecialchars($phone_number);
    $address = htmlspecialchars($address);
    $city = htmlspecialchars($city);
    $country = htmlspecialchars($country);
    // Gender from select is generally safe, but could be validated against allowed values

    // Hash the password
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    // Check for existing email
    $check_email_sql = "SELECT id FROM customers WHERE email = ?";
    $stmt_check = $conn->prepare($check_email_sql);
    $stmt_check->bind_param("s", $email);
    $stmt_check->execute();
    $stmt_check->store_result();

    if ($stmt_check->num_rows > 0) {
        echo "Email address is already registered.";
        $stmt_check->close();
        $conn->close();
        exit;
    }
    $stmt_check->close();

    // Prepare and execute the INSERT statement
    $insert_sql = "INSERT INTO customers (full_name, email, password, phone_number, address, city, country, gender)
                   VALUES (?, ?, ?, ?, ?, ?, ?, ?)";

    $stmt_insert = $conn->prepare($insert_sql);
    $stmt_insert->bind_param("ssssssss", $full_name, $email, $hashed_password, $phone_number, $address, $city, $country, $gender);

    if ($stmt_insert->execute()) {
       // echo "Registration successful!";
        // Optional: Redirect to login page
         header("Location: registration_success.html");
         exit;
    } else {
        echo "Error: " . $stmt_insert->error;
    }

    $stmt_insert->close();
} else {
    echo "Invalid request method.";
}

// Close database connection
$conn->close();
?>