<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL); // Remember to turn this off in production

// Check if a session is already active before starting
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Check if the user is logged in as a farmer
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'farmer') {
    // Redirect to the farmer login page if not logged in or not a farmer
    header("Location: farmer_login.php"); // *** Make sure this is your farmer login page ***
    exit();
}

// User is logged in as a farmer, retrieve their information from the session
$farmer_id = $_SESSION['user_id'];

// --- Database Connection ---
$servername = "localhost";
$username = "root"; // Replace with your database username
$password = ""; // Replace with your database password
$dbname = "farmerreg_db"; // *** Make sure this is the database for farmers ***

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Prepare and execute SQL query to fetch farmer details
// Select all columns except 'password'
$sql = "SELECT id, full_name, email, phone_number, farm_address, city, country, farm_size, primary_crop, farming_experience, preferred_contact FROM farmers WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $farmer_id);
$stmt->execute();
$result = $stmt->get_result();

$farmer_details = $result->fetch_assoc();

$stmt->close();
$conn->close(); // Close the connection after fetching data
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Farmer Profile</title>

    <!-- Link to a Google Font (match AgroProducts font, e.g., 'Lora') -->
    <link href="https://fonts.googleapis.com/css2?family=Lora:wght@400;700&display=swap" rel="stylesheet" />

    <style>
        /* Theme colors: Beige & White */
        body {
            font-family: 'Lora', serif;
            background-color: #f5f1ea;
            /* Light beige background */
            color: #4a403a;
            /* Dark brown text */
            margin: 0;
            padding: 20px;
            min-height: 100vh;
        }

        .profile-container {
            background-color: #ffffff;
            padding: 40px;
            border-radius: 15px;
            box-shadow: 0 8px 25px rgba(74, 64, 58, 0.15);
            max-width: 600px;
            width: 100%;
            text-align: center;
            margin: 40px auto;
            opacity: 0;
            transform: translateY(20px);
            animation: fadeIn 0.6s ease-out forwards;
        }

        @keyframes fadeIn {
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .profile-header h1 {
            color: #7f675c;
            /* Medium brown */
            margin-bottom: 25px;
            font-size: 2.2em;
            font-weight: 700;
        }

        .profile-card {
            border-top: 2px solid #d7c4aa;
            /* Light beige border */
            padding-top: 25px;
            margin-top: 25px;
            text-align: left;
        }

        .profile-card h2 {
            color: #5b4a3c;
            /* Dark brown */
            margin-bottom: 25px;
            font-size: 1.6em;
            text-align: center;
            position: relative;
            padding-bottom: 10px;
            font-weight: 700;
        }

        .profile-card h2::after {
            content: '';
            position: absolute;
            left: 50%;
            bottom: 0;
            transform: translateX(-50%);
            width: 60px;
            height: 3px;
            background-color: #b39c82;
            /* Medium beige */
            border-radius: 2px;
        }

        .profile-details {
            margin-bottom: 25px;
        }

        .detail-item {
            display: flex;
            margin-bottom: 18px;
            padding-bottom: 18px;
            border-bottom: 1px solid #e2dcd3;
            /* Very light beige */
            align-items: center;
        }

        .detail-item:last-child {
            border-bottom: none;
            margin-bottom: 0;
            padding-bottom: 0;
        }

        .detail-label {
            font-weight: 700;
            color: #6d5a4f;
            /* Medium brown */
            flex: 1;
            padding-right: 15px;
            min-width: 150px;
        }

        .detail-value {
            color: #4a403a;
            /* Dark brown */
            flex: 2;
            word-break: break-word;
        }

        .profile-actions {
            margin-top: 40px;
            text-align: center;
        }

        .logout-button {
            display: inline-block;
            background-color: #c26a41;
            /* Soft terracotta */
            color: white;
            padding: 12px 25px;
            border-radius: 30px;
            text-decoration: none;
            font-size: 1.1em;
            font-weight: 700;
            transition: background-color 0.3s ease, transform 0.2s ease;
            border: none;
            cursor: pointer;
            margin: 0 5px;
        }

        .back-button {
            display: inline-block;
            background-color: #a1866f;
            /* Warm beige */
            color: white;
            padding: 12px 25px;
            border-radius: 30px;
            text-decoration: none;
            font-size: 1.1em;
            font-weight: 700;
            transition: background-color 0.3s ease, transform 0.2s ease;
            border: none;
            cursor: pointer;
            margin: 0 5px;
        }

        .logout-button:hover {
            background-color: #af5a2b;
            transform: translateY(-2px);
        }

        .back-button:hover {
            background-color: #8b7158;
            transform: translateY(-2px);
        }

        .logout-button:active,
        .back-button:active {
            transform: translateY(0);
        }
    </style>
    <link rel="stylesheet" href="style.css">
</head>

<body>
    <div class="profile-container">
        <div class="profile-header">
            <h1>Welcome, <?php echo htmlspecialchars($farmer_details['full_name']); ?>!</h1>
        </div>

        <div class="profile-card">
            <h2>Your Profile Details</h2>
            <?php if ($farmer_details): ?>
                <div class="profile-details">
                    <div class="detail-item">
                        <span class="detail-label">Farmer ID:</span>
                        <span class="detail-value"><?php echo htmlspecialchars($farmer_details['id']); ?></span>
                    </div>
                    <div class="detail-item">
                        <span class="detail-label">Full Name:</span>
                        <span class="detail-value"><?php echo htmlspecialchars($farmer_details['full_name']); ?></span>
                    </div>
                    <div class="detail-item">
                        <span class="detail-label">Email:</span>
                        <span class="detail-value"><?php echo htmlspecialchars($farmer_details['email']); ?></span>
                    </div>
                    <div class="detail-item">
                        <span class="detail-label">Phone Number:</span>
                        <span class="detail-value"><?php echo htmlspecialchars($farmer_details['phone_number']); ?></span>
                    </div>
                    <div class="detail-item">
                        <span class="detail-label">Farm Address:</span>
                        <span class="detail-value"><?php echo htmlspecialchars($farmer_details['farm_address']); ?></span>
                    </div>
                    <div class="detail-item">
                        <span class="detail-label">City:</span>
                        <span class="detail-value"><?php echo htmlspecialchars($farmer_details['city']); ?></span>
                    </div>
                    <div class="detail-item">
                        <span class="detail-label">Country:</span>
                        <span class="detail-value"><?php echo htmlspecialchars($farmer_details['country']); ?></span>
                    </div>
                    <div class="detail-item">
                        <span class="detail-label">Farm Size:</span>
                        <span class="detail-value"><?php echo htmlspecialchars($farmer_details['farm_size']); ?></span>
                    </div>
                    <div class="detail-item">
                        <span class="detail-label">Primary Crop:</span>
                        <span class="detail-value"><?php echo htmlspecialchars($farmer_details['primary_crop']); ?></span>
                    </div>
                    <div class="detail-item">
                        <span class="detail-label">Farming Experience:</span>
                        <span
                            class="detail-value"><?php echo htmlspecialchars($farmer_details['farming_experience']); ?></span>
                    </div>
                    <div class="detail-item">
                        <span class="detail-label">Preferred Contact:</span>
                        <span
                            class="detail-value"><?php echo htmlspecialchars($farmer_details['preferred_contact']); ?></span>
                    </div>
                </div>
            <?php else: ?>
                <p>Could not retrieve full profile details.</p>
            <?php endif; ?>

            <div class="profile-actions">
                <a href="logout.php" class="logout-button">Logout</a>
                <a href="index.php" class="back-button">Back to Home</a>
            </div>
        </div>
    </div> <!-- /profile-container -->
</body>

</html>