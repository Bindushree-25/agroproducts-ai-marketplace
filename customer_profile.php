<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'customer') {
    header("Location: customer_login.php");
    exit();
}

$customer_id = $_SESSION['user_id'];

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "customerreg_db";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$sql = "SELECT id, full_name, email, phone_number, address, city, country, gender FROM customers WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $customer_id);
$stmt->execute();
$result = $stmt->get_result();

$customer_details = $result->fetch_assoc();

$stmt->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Customer Profile</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">

    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background: linear-gradient(to bottom, #fdf6e3, #fffdf8);
            /* soft beige gradient */
            margin: 0;
            padding: 40px 15px;
            color: #4a4a4a;
        }

        .profile-container {
            background-color: #fff8f0;
            /* very light beige */
            max-width: 700px;
            margin: auto;
            border-radius: 15px;
            box-shadow: 0 8px 24px rgba(209, 166, 108, 0.3);
            /* warm beige shadow */
            padding: 40px 50px;
            animation: fadeIn 0.6s ease-out forwards;
            opacity: 0;
            transform: translateY(20px);
        }

        @keyframes fadeIn {
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .profile-header h1 {
            text-align: center;
            color: #7a5c2c;
            /* dark warm beige */
            margin-bottom: 30px;
            font-weight: 600;
            font-size: 2.2em;
        }

        .profile-card h2 {
            text-align: center;
            font-size: 1.6em;
            margin-bottom: 30px;
            color: #8c6d38;
            position: relative;
            font-weight: 600;
        }

        .profile-card h2::after {
            content: "";
            position: absolute;
            left: 50%;
            bottom: -12px;
            transform: translateX(-50%);
            width: 70px;
            height: 4px;
            background-color: #d1a66c;
            border-radius: 2px;
        }

        .detail-item {
            display: flex;
            justify-content: space-between;
            padding: 14px 0;
            border-bottom: 1px solid #f0e5c9;
        }

        .detail-label {
            font-weight: 600;
            color: #6d5b2b;
        }

        .detail-value {
            color: #4a4a4a;
            text-align: right;
        }

        .profile-actions {
            margin-top: 40px;
            text-align: center;
        }

        .logout-button {
            background-color: #d1a66c;
            color: white;
            padding: 14px 30px;
            margin: 10px 15px;
            border: none;
            border-radius: 30px;
            font-size: 1.1em;
            font-weight: 600;
            cursor: pointer;
            transition: background-color 0.3s ease, transform 0.2s ease;
            text-decoration: none;
            display: inline-block;
            box-shadow: 0 4px 10px rgba(209, 166, 108, 0.4);
        }

        .logout-button:hover {
            background-color: #b88b50;
            transform: translateY(-2px);
        }

        .logout-button:active {
            transform: translateY(0);
        }
    </style>
    <link rel="stylesheet" href="style.css">
</head>

<body>
    <div class="profile-container">
        <div class="profile-header">
            <h1>Welcome, <?php echo htmlspecialchars($customer_details['full_name']); ?>!</h1>
        </div>

        <div class="profile-card">
            <h2>Your Profile Details</h2>
            <?php if ($customer_details): ?>
                <div class="profile-details">
                    <div class="detail-item">
                        <span class="detail-label">Customer ID:</span>
                        <span class="detail-value"><?php echo htmlspecialchars($customer_details['id']); ?></span>
                    </div>
                    <div class="detail-item">
                        <span class="detail-label">Full Name:</span>
                        <span class="detail-value"><?php echo htmlspecialchars($customer_details['full_name']); ?></span>
                    </div>
                    <div class="detail-item">
                        <span class="detail-label">Email:</span>
                        <span class="detail-value"><?php echo htmlspecialchars($customer_details['email']); ?></span>
                    </div>
                    <div class="detail-item">
                        <span class="detail-label">Phone Number:</span>
                        <span class="detail-value"><?php echo htmlspecialchars($customer_details['phone_number']); ?></span>
                    </div>
                    <div class="detail-item">
                        <span class="detail-label">Gender:</span>
                        <span class="detail-value"><?php echo htmlspecialchars($customer_details['gender']); ?></span>
                    </div>
                    <div class="detail-item">
                        <span class="detail-label">Address:</span>
                        <span class="detail-value"><?php echo htmlspecialchars($customer_details['address']); ?></span>
                    </div>
                    <div class="detail-item">
                        <span class="detail-label">City:</span>
                        <span class="detail-value"><?php echo htmlspecialchars($customer_details['city']); ?></span>
                    </div>
                    <div class="detail-item">
                        <span class="detail-label">Country:</span>
                        <span class="detail-value"><?php echo htmlspecialchars($customer_details['country']); ?></span>
                    </div>
                </div>
            <?php else: ?>
                <p>Could not retrieve full profile details.</p>
            <?php endif; ?>

            <div class="profile-actions">
                <a href="logout.php" class="logout-button">Logout</a>
                <a href="index.php" class="logout-button">Back to Home</a>
            </div>
        </div>
    </div>

</body>

</html>