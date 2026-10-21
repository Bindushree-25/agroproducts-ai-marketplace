<?php
// Start session
session_start();

// Check if user is logged in and is a farmer
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] != 'farmer') {
    header("Location: farmer_login.php");
    exit();
}

// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$shop_db = "shop_db";

$conn = new mysqli($servername, $username, $password, $shop_db);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$message = ""; // For showing success/failure messages

// Handle the product upload process
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $product_name = $_POST['product_name'];
    $description = $_POST['description'];
    $price = $_POST['price'];
    $stock_quantity = $_POST['stock_quantity'];
    $image = $_FILES['image']['name'];
    $image_temp = $_FILES['image']['tmp_name'];
    $category = $_POST['category'];

    $target_dir = "images/";
    $target_file = $target_dir . basename($image);

    $image_check = getimagesize($image_temp);
    if ($image_check !== false) {
        if (move_uploaded_file($image_temp, $target_file)) {
            $sql = "INSERT INTO products (farmer_id, p_name, description, price, stock_quantity, image_path, category)
                    VALUES (?, ?, ?, ?, ?, ?, ?)";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("issdsss", $_SESSION['user_id'], $product_name, $description, $price, $stock_quantity, $target_file, $category);

            if ($stmt->execute()) {
                $message = "<div class='alert success'>✅ Product uploaded successfully!</div>";
            } else {
                $message = "<div class='alert error'>❌ Failed to upload product. Please try again.</div>";
            }
        } else {
            $message = "<div class='alert error'>❌ Failed to upload image. Please try again.</div>";
        }
    } else {
        $message = "<div class='alert error'>❌ Please upload a valid image file.</div>";
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Upload Product</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;500;600&display=swap" rel="stylesheet" />
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Poppins', sans-serif;
        }

        body {
            background: #f2f2f2;
        }

        .header {
            background-color: #2e8b57;
            color: white;
            padding: 20px;
            text-align: center;
            font-size: 26px;
            font-weight: 600;
        }

        .form-container {
            max-width: 600px;
            background: white;
            margin: 40px auto;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1);
        }

        .form-container h2 {
            text-align: center;
            color: #2e8b57;
            margin-bottom: 25px;
        }

        label {
            font-weight: 500;
            margin-top: 15px;
            display: block;
        }

        input,
        textarea,
        select {
            width: 100%;
            padding: 10px 14px;
            margin-top: 5px;
            border: 1px solid #ccc;
            border-radius: 6px;
            font-size: 15px;
        }

        input[type="file"] {
            padding: 5px;
        }

        button {
            margin-top: 25px;
            width: 100%;
            padding: 12px;
            background-color: #2e8b57;
            border: none;
            color: white;
            font-size: 17px;
            font-weight: 500;
            border-radius: 6px;
            cursor: pointer;
            transition: background 0.3s ease;
        }

        button:hover {
            background-color: #276c46;
        }

        .back-button {
            display: block;
            width: fit-content;
            margin: 0 auto 30px;
            background: #555;
            color: #fff;
            padding: 10px 20px;
            text-decoration: none;
            border-radius: 6px;
        }

        .alert {
            max-width: 600px;
            margin: 20px auto;
            padding: 15px;
            text-align: center;
            border-radius: 6px;
            font-weight: 500;
            font-size: 16px;
        }

        .success {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }

        .error {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }

        .footer {
            background-color: #2e8b57;
            color: white;
            text-align: center;
            padding: 15px;
            position: relative;
            bottom: 0;
            width: 100%;
            margin-top: 40px;
        }
    </style>
    <link rel="stylesheet" href="style.css">
</head>

<body>

    <div class="header">Upload New Product</div>

    <?php echo $message; ?>

    <div class="form-container">
        <h2>Product Upload Form</h2>
        <form action="upload_product.php" method="POST" enctype="multipart/form-data">
            <label for="product_name">Product Name</label>
            <input type="text" name="product_name" id="product_name" required />

            <label for="description">Description</label>
            <textarea name="description" id="description" rows="4" required></textarea>

            <label for="price">Price (in INR)</label>
            <input type="number" name="price" id="price" step="0.01" required />

            <label for="category">Category</label>
            <select name="category" required>
                <option value="Fruits">Fruits</option>
                <option value="Vegetables">Vegetables</option>
                <option value="Grains & Pulses">Grains & Pulses</option>
                <option value="Dairy Products">Dairy Products</option>
            </select>

            <label for="stock_quantity">Stock Quantity</label>
            <input type="number" name="stock_quantity" id="stock_quantity" required />

            <label for="image">Product Image</label>
            <input type="file" name="image" id="image" accept="image/*" required />

            <button type="submit">Upload Product</button>
        </form>
    </div>

    <div class="container">
        <a href="farmer_dashboard.php" class="back-button">← Back to Dashboard</a>
    </div>

    <div class="footer">
        <p>© 2025 AgroProducts. All Rights Reserved.</p>
    </div>

</body>

</html>