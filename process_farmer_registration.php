<?php
// Database connection details
$servername = "localhost";
$username = "root"; // Replace with your database username
$password = ""; // Replace with your database password
$dbname = "farmerreg_db"; // Replace with your database name (consider a different name than customerreg_db for clarity)

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Check if the request method is POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve form data
    $full_name = $_POST['full_name'];
    $email = $_POST['email'];
    $phone_number = $_POST['phone_number'];
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    $farm_address = $_POST['farm_address'];
    $city = $_POST['city'];
    $country = $_POST['country'];
    $farm_size = $_POST['farm_size']; // Assuming this is a text input for now
    $primary_crop = $_POST['primary_crop'];
    $farming_experience = $_POST['farming_experience']; // Assuming this is a text input for now
    $preferred_contact = $_POST['preferred_contact']; // Assuming this is from a select input

    // --- Basic Validation ---
    $errors = [];

    if (empty($full_name)) { $errors[] = "Full Name is required."; }
    if (empty($email)) { $errors[] = "Email is required."; }
    if (empty($phone_number)) { $errors[] = "Phone Number is required."; }
    if (empty($password)) { $errors[] = "Password is required."; }
    if (empty($confirm_password)) { $errors[] = "Confirm Password is required."; }
    if (empty($farm_address)) { $errors[] = "Farm Address is required."; }
    if (empty($city)) { $errors[] = "City is required."; }
    if (empty($country)) { $errors[] = "Country is required."; }
    if (empty($farm_size)) { $errors[] = "Farm Size is required."; }
    if (empty($primary_crop)) { $errors[] = "Primary Crop Grown is required."; }
    if (empty($farming_experience)) { $errors[] = "Years of Farming Experience is required."; }
    if (empty($preferred_contact)) { $errors[] = "Preferred Contact Method is required."; }


    // Validate email format
    if (!empty($email) && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Invalid email format.";
    }

    // Check if passwords match
    if (!empty($password) && $password !== $confirm_password) {
        $errors[] = "Passwords do not match.";
    }

    // You might want to add more specific validation for phone number, farm size (numeric), farming experience (numeric), etc.

    if (!empty($errors)) {
        // Display errors and exit
        foreach ($errors as $error) {
            echo "<p style='color: red;'>Error: " . htmlspecialchars($error) . "</p>";
        }
        $conn->close();
        exit;
    }

    // --- Sanitize Input ---
    $full_name = htmlspecialchars($full_name);
    $email = filter_var($email, FILTER_SANITIZE_EMAIL); // Cleaner email
    $phone_number = htmlspecialchars($phone_number);
    $farm_address = htmlspecialchars($farm_address);
    $city = htmlspecialchars($city);
    $country = htmlspecialchars($country);
    $farm_size = htmlspecialchars($farm_size); // Sanitize string input
    $primary_crop = htmlspecialchars($primary_crop);
    $farming_experience = htmlspecialchars($farming_experience); // Sanitize string input
    $preferred_contact = htmlspecialchars($preferred_contact); // Sanitize string input

    // Hash the password
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    // --- Database Operations ---

    // Check for existing email (assuming email is unique for farmers)
    $check_email_sql = "SELECT id FROM farmers WHERE email = ?";
    $stmt_check = $conn->prepare($check_email_sql);
    $stmt_check->bind_param("s", $email);
    $stmt_check->execute();
    $stmt_check->store_result();

    if ($stmt_check->num_rows > 0) {
        echo "<p style='color: red;'>Error: Email address is already registered.</p>";
        $stmt_check->close();
        $conn->close();
        exit;
    }
    $stmt_check->close();

    // Prepare and execute the INSERT statement for the 'farmers' table
    // Make sure your 'farmers' table has columns matching these names and types
    $insert_sql = "INSERT INTO farmers (full_name, email, phone_number, password, farm_address, city, country, farm_size, primary_crop, farming_experience, preferred_contact)
                   VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

    $stmt_insert = $conn->prepare($insert_sql);

    // Bind parameters (s = string, i = integer, d = double/float, b = blob)
    // Adjust types if your database schema uses different types (e.g., i for farm_size if it's numeric)
    $stmt_insert->bind_param("sssssssssss",
        $full_name,
        $email,
        $phone_number,
        $hashed_password, // Store the hashed password
        $farm_address,
        $city,
        $country,
        $farm_size,
        $primary_crop,
        $farming_experience,
        $preferred_contact
    );

    if ($stmt_insert->execute()) {
        // Registration successful
       // echo "Farmer registration successful!";
        // Redirect to the farmer login page
        header("Location: registration_success.html");
        exit; // Important to stop script execution after redirect
    } else {
        // Error during insertion
        echo "<p style='color: red;'>Error during registration: " . htmlspecialchars($stmt_insert->error) . "</p>";
    }

    $stmt_insert->close();

} else {
    // If the request method is not POST
    echo "<p style='color: red;'>Invalid request method.</p>";
}

// Close database connection
$conn->close();
?>