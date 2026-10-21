<?php
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] != 'farmer') {
    header("Location: farmer_login.php");
    exit();
}

$servername = "localhost";
$username = "root";
$password = "";
$shop_db = "shop_db";

$conn = new mysqli($servername, $username, $password, $shop_db);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$farmer_id = $_SESSION['user_id'];
$success_message = "";
$error_message = "";

// Handle update
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['update'])) {
    $product_id = $_POST['product_id'];
    $name = $_POST['name'];
    $description = $_POST['description'];
    $price = $_POST['price'];
    $quantity = $_POST['quantity'];

    $update_query = "UPDATE products SET p_name=?, description=?, price=?, stock_quantity=? WHERE p_id=? AND farmer_id=?";
    $stmt = $conn->prepare($update_query);
    $stmt->bind_param("ssdiii", $name, $description, $price, $quantity, $product_id, $farmer_id);
    if ($stmt->execute()) {
        $success_message = "✅ Product updated successfully.";
    }
}

// Handle delete
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['delete'])) {
    $product_id = $_POST['product_id'];

    // Debug: Check form submitted
    // error_log("Trying to delete product ID: $product_id");

    $check_query = $conn->prepare("SELECT COUNT(*) FROM order_items WHERE product_id = ?");
    $check_query->bind_param("i", $product_id);
    $check_query->execute();
    $check_query->bind_result($count);
    $check_query->fetch();
    $check_query->close();

    if ($count > 0) {
        $error_message = "❌ Cannot delete product. It is linked to existing orders.";
    } else {
        $delete_query = "DELETE FROM products WHERE p_id=? AND farmer_id=?";
        $stmt = $conn->prepare($delete_query);
        $stmt->bind_param("ii", $product_id, $farmer_id);
        if ($stmt->execute()) {
            $success_message = "🗑️ Product deleted successfully.";
        } else {
            $error_message = "❌ Failed to delete product.";
        }
    }
}

// Fetch products
$product_query = "SELECT * FROM products WHERE farmer_id=?";
$stmt = $conn->prepare($product_query);
$stmt->bind_param("i", $farmer_id);
$stmt->execute();
$product_result = $stmt->get_result();

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Manage Products</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #eef1f3;
            margin: 0;
            padding: 0;
        }

        .container {
            padding: 30px;
        }

        h2 {
            text-align: center;
        }

        .success {
            background-color: #d4edda;
            color: #155724;
            padding: 10px;
            margin-bottom: 15px;
            border-radius: 5px;
            text-align: center;
        }

        .error {
            background-color: #f8d7da;
            color: #721c24;
            padding: 10px;
            margin-bottom: 15px;
            border-radius: 5px;
            text-align: center;
        }

        .back-button {
            display: inline-block;
            margin-bottom: 15px;
            padding: 10px 20px;
            background-color: #333;
            color: white;
            text-decoration: none;
            border-radius: 5px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            background-color: #fff;
        }

        th,
        td {
            padding: 12px;
            border: 1px solid #ccc;
            text-align: center;
        }

        th {
            background-color: #f9f9f9;
        }

        input[type="text"],
        input[type="number"],
        textarea {
            width: 90%;
            padding: 5px;
        }

        input[type="submit"] {
            padding: 6px 14px;
            margin: 3px;
            background-color: #4CAF50;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }

        input[type="submit"]:hover {
            background-color: #45a049;
        }

        img {
            height: 80px;
        }
    </style>
    <link rel="stylesheet" href="style.css">
</head>

<body>
    <div class="container">
        <a href="farmer_dashboard.php" class="back-button">← Back to Dashboard</a>
        <h2>Manage Your Products</h2>

        <?php if ($success_message): ?>
            <div class="success"><?php echo $success_message; ?></div>
        <?php endif; ?>

        <?php if ($error_message): ?>
            <div class="error"><?php echo $error_message; ?></div>
        <?php endif; ?>

        <table>
            <tr>
                <th>Image</th>
                <th>Name</th>
                <th>Description</th>
                <th>Price (₹)</th>
                <th>Quantity</th>
                <th>Actions</th>
            </tr>
            <?php while ($row = $product_result->fetch_assoc()): ?>
                <tr>
                    <form method="post">
                        <td>
                            <img src="<?php echo htmlspecialchars($row['image_path']); ?>" alt="Product Image">
                        </td>
                        <td>
                            <input type="text" name="name" value="<?php echo htmlspecialchars($row['p_name']); ?>">
                        </td>
                        <td>
                            <textarea name="description"><?php echo htmlspecialchars($row['description']); ?></textarea>
                        </td>
                        <td>
                            <input type="number" step="0.01" name="price" value="<?php echo $row['price']; ?>">
                        </td>
                        <td>
                            <input type="number" name="quantity" value="<?php echo $row['stock_quantity']; ?>">
                        </td>
                        <td>
                            <input type="hidden" name="product_id" value="<?php echo $row['p_id']; ?>">
                            <input type="submit" name="update" value="Update">
                            <!-- Temporarily remove confirm() for debugging -->
                            <input type="submit" name="delete" value="Delete"
                                onclick="return confirm('Are you sure you want to delete this product?');">
                        </td>
                    </form>
                </tr>
            <?php endwhile; ?>
        </table>
    </div>
</body>

</html>