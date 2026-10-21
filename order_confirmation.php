<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] != 'customer') {
  header("Location: customer_login.php");
  exit();
}

$order_id = isset($_GET['order_id']) ? intval($_GET['order_id']) : 0;
$customer_id = $_SESSION['user_id'];

// Connect to shop_db
$shop_conn = new mysqli("localhost", "root", "", "shop_db");
if ($shop_conn->connect_error) {
  die("Connection failed: " . $shop_conn->connect_error);
}

// Connect to customerreg_db
$customer_conn = new mysqli("localhost", "root", "", "customerreg_db");
if ($customer_conn->connect_error) {
  die("Connection failed: " . $customer_conn->connect_error);
}

// Fetch order
$order_sql = "SELECT * FROM orders WHERE order_id = ? AND customer_id = ?";
$stmt = $shop_conn->prepare($order_sql);
$stmt->bind_param("ii", $order_id, $customer_id);
$stmt->execute();
$order_result = $stmt->get_result();

if ($order_result->num_rows == 0) {
  echo "Order not found or unauthorized access.";
  exit();
}
$order = $order_result->fetch_assoc();

// Fetch order items
$item_sql = "SELECT * FROM order_items WHERE order_id = ?";
$item_stmt = $shop_conn->prepare($item_sql);
$item_stmt->bind_param("i", $order_id);
$item_stmt->execute();
$item_result = $item_stmt->get_result();

// Fetch customer details
$customer_sql = "SELECT full_name, address, phone_number FROM customers WHERE id = ?";
$cust_stmt = $customer_conn->prepare($customer_sql);
$cust_stmt->bind_param("i", $customer_id);
$cust_stmt->execute();
$cust_result = $cust_stmt->get_result();
$customer = $cust_result->fetch_assoc();
?>

<!DOCTYPE html>
<html>

<head>
  <title>Order Confirmation - AgroProducts</title>
  <style>
    body {
      font-family: 'Segoe UI', sans-serif;
      margin: 0;
      padding: 2rem;
      background: #fcfcfc;
    }

    .container {
      max-width: 900px;
      margin: auto;
      background: #fff;
      padding: 2rem;
      border-radius: 10px;
      box-shadow: 0 0 10px rgba(0, 0, 0, 0.08);
    }

    h2 {
      text-align: center;
      color: #2c5e31;
    }

    .section {
      margin-bottom: 2rem;
    }

    .section h3 {
      color: #444;
      margin-bottom: 0.5rem;
      border-bottom: 1px solid #ccc;
      padding-bottom: 0.5rem;
    }

    .details p {
      margin: 0.3rem 0;
      font-size: 1rem;
    }

    table {
      width: 100%;
      border-collapse: collapse;
      margin-top: 1rem;
    }

    th,
    td {
      padding: 10px;
      border: 1px solid #ddd;
      text-align: center;
    }

    th {
      background-color: #a3c293;
      color: white;
    }

    .summary {
      text-align: right;
      margin-top: 1rem;
      font-size: 1.05rem;
    }

    .summary div {
      margin: 0.4rem 0;
    }

    .back-link {
      display: block;
      text-align: center;
      margin-top: 2rem;
      text-decoration: none;
      color: #2c5e31;
      font-weight: bold;
    }
  </style>
    <link rel="stylesheet" href="style.css">
</head>

<body>
  <div class="container">
    <h2>🎉 Order Confirmed!</h2>

    <div class="section">
      <h3>Customer Details</h3>
      <div class="details">
        <p><strong>Name:</strong> <?= htmlspecialchars($customer['full_name']) ?></p>
        <p><strong>Address:</strong> <?= htmlspecialchars($customer['address']) ?></p>
        <p><strong>Phone:</strong> <?= htmlspecialchars($customer['phone_number']) ?></p>
      </div>
    </div>

    <div class="section">
      <h3>Order Summary</h3>
      <p><strong>Order ID:</strong> <?= $order['order_id'] ?></p>
      <p><strong>Date:</strong> <?= $order['order_date'] ?></p>

      <table>
        <thead>
          <tr>
            <th>Product</th>
            <th>Farmer ID</th>
            <th>Qty</th>
            <th>Price (₹)</th>
            <th>Total (₹)</th>
          </tr>
        </thead>
        <tbody>
          <?php
          $subtotal = 0;
          while ($item = $item_result->fetch_assoc()):
            $total = $item['quantity'] * $item['price'];
            $subtotal += $total;
            ?>
            <tr>
              <td><?= htmlspecialchars($item['product_name']) ?></td>
              <td><?= $item['farmer_id'] ?></td>
              <td><?= $item['quantity'] ?></td>
              <td><?= number_format($item['price'], 2) ?></td>
              <td><?= number_format($total, 2) ?></td>
            </tr>
          <?php endwhile; ?>
        </tbody>
      </table>

      <div class="summary">
        <div>Subtotal: ₹<?= number_format($subtotal, 2) ?></div>
        <div>GST (5%): ₹<?= number_format($subtotal * 0.05, 2) ?></div>
        <div>Delivery: ₹50.00</div>
        <div><strong>Total Paid: ₹<?= number_format($subtotal * 1.05 + 50, 2) ?></strong></div>
      </div>
    </div>

    <a href="index.php" class="back-link">← BACK HOME</a>
  </div>
</body>

</html>