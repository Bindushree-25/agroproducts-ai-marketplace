<?php
session_start();
$error = '';
if (isset($_SESSION['login_error'])) {
    $error = $_SESSION['login_error'];
    unset($_SESSION['login_error']);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1.0"/>
<title>Customer Login</title>
<style>
body {
    font-family: Arial, sans-serif;
    background-color: #f4f4f4;
    display: flex;
    justify-content: center;
    align-items: center;
    min-height: 100vh;
    margin: 0;
}
.login-container {
    background-color: #fff;
    padding: 30px;
    border-radius: 8px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    width: 300px;
    text-align: center;
}
h2 {
    margin-bottom: 20px;
    color: #333;
}
.form-group {
    margin-bottom: 15px;
    text-align: left;
}
label {
    display: block;
    margin-bottom: 5px;
    color: #555;
}
input[type="email"],
input[type="password"] {
    width: 100%;
    padding: 10px;
    border: 1px solid #ccc;
    border-radius: 4px;
    box-sizing: border-box;
}
button {
    width: 100%;
    padding: 10px;
    background-color: #5cb85c; /* matching your theme color */
    color: white;
    border: none;
    border-radius: 4px;
    cursor: pointer;
    font-size: 16px;
    transition: background-color 0.3s ease;
}
button:hover {
    background-color: #4cae4c;
}
.footer {
    margin-top: 20px;
    font-size: 14px;
    color: #777;
}
.footer a {
    color: #5cb85c;
    text-decoration: none;
}
.footer a:hover {
    text-decoration: underline;
}
/* Error message styling to match theme */
.error-message {
    color: #d9534f; /* Bootstrap red for errors */
    margin-bottom: 15px;
    font-weight: bold;
    display: <?php echo $error ? 'block' : 'none'; ?>;
    font-size: 14px;
    padding: 10px;
    background-color: #f2dede; /* Light red background for error box */
    border: 1px solid #ebccd1;
    border-radius: 4px;
}
</style>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<div class="login-container">
    <h2>Customer Login</h2>

    <!-- Error message box -->
    <div class="error-message">
        <?php echo htmlspecialchars($error); ?>
    </div>

    <form action="process_customer_login.php" method="post">
        <div class="form-group">
            <label for="email">Email:</label>
            <input type="email" id="email" name="email" required>
        </div>
        <div class="form-group">
            <label for="password">Password:</label>
            <input type="password" id="password" name="password" required>
        </div>
        <button type="submit">Login</button>
    </form>

    <div class="footer">
        New user? <a href="customer_registration.html">Register here</a>
    </div>
</div>

</body>
</html>