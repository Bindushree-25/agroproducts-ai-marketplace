<?php
/**
 * Creates an order using server-trusted product prices and farmer IDs.
 * This academic demo records an order as Pending; it does not process payments.
 */
if (session_status() === PHP_SESSION_NONE) { session_start(); }
header('Content-Type: application/json; charset=utf-8');

function respond(int $status, array $payload): never {
    http_response_code($status);
    echo json_encode($payload, JSON_UNESCAPED_UNICODE);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') respond(405, ['success'=>false, 'message'=>'POST required.']);
if (($_SESSION['user_type'] ?? '') !== 'customer' || empty($_SESSION['user_id'])) {
    respond(401, ['success'=>false, 'message'=>'Please sign in with a customer account.']);
}
$raw = file_get_contents('php://input');
$data = json_decode($raw, true);
if (!is_array($data) || empty($data['items']) || !is_array($data['items']) || count($data['items']) > 50) {
    respond(400, ['success'=>false, 'message'=>'Your cart is empty or invalid.']);
}

$requested = [];
foreach ($data['items'] as $item) {
    $id = filter_var($item['product_id'] ?? null, FILTER_VALIDATE_INT, ['options'=>['min_range'=>1]]);
    $qty = filter_var($item['quantity'] ?? null, FILTER_VALIDATE_INT, ['options'=>['min_range'=>1, 'max_range'=>99]]);
    if (!$id || !$qty) respond(400, ['success'=>false, 'message'=>'A product or quantity is invalid. Please refresh your cart.']);
    $requested[$id] = ($requested[$id] ?? 0) + $qty;
    if ($requested[$id] > 99) respond(400, ['success'=>false, 'message'=>'Quantity exceeds the allowed limit.']);
}

$pdo = null;
try {
    // Default local XAMPP credentials; change in config.local.php for a custom setup.
    $configPath = __DIR__ . '/config.local.php';
    $config = file_exists($configPath) ? require $configPath : [];
    $host = $config['db_host'] ?? '127.0.0.1';
    $user = $config['db_user'] ?? 'root';
    $pass = $config['db_pass'] ?? '';
    $pdo = new PDO("mysql:host={$host};dbname=shop_db;charset=utf8mb4", $user, $pass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
    ]);
    $pdo->beginTransaction();
    $ids = array_keys($requested);
    $marks = implode(',', array_fill(0, count($ids), '?'));
    $stmt = $pdo->prepare("SELECT p_id, p_name, price, stock_quantity, farmer_id FROM products WHERE p_id IN ($marks) FOR UPDATE");
    $stmt->execute($ids);
    $products = [];
    foreach ($stmt->fetchAll() as $p) $products[(int)$p['p_id']] = $p;
    if (count($products) !== count($ids)) throw new DomainException('One or more products are no longer available. Refresh the shop and try again.');

    $total = 0.0;
    $items = [];
    foreach ($requested as $id => $qty) {
        $p = $products[$id];
        if ($p['stock_quantity'] !== null && $qty > (int)$p['stock_quantity']) {
            throw new DomainException('Not enough stock for ' . $p['p_name'] . '. Please reduce the quantity.');
        }
        $price = (float)$p['price'];
        if ($price < 0) throw new DomainException('A product has an invalid price.');
        $total += $price * $qty;
        $items[] = ['product_id'=>$id, 'product_name'=>$p['p_name'], 'quantity'=>$qty, 'price'=>$price, 'farmer_id'=>(int)$p['farmer_id']];
    }

    $orderStmt = $pdo->prepare("INSERT INTO orders (customer_id, order_date, total_amount, status) VALUES (?, NOW(), ?, 'Pending')");
    $orderStmt->execute([(int)$_SESSION['user_id'], round($total, 2)]);
    $orderId = (int)$pdo->lastInsertId();
    $itemStmt = $pdo->prepare('INSERT INTO order_items (order_id, product_id, product_name, quantity, price, farmer_id) VALUES (?, ?, ?, ?, ?, ?)');
    $stockStmt = $pdo->prepare('UPDATE products SET stock_quantity = stock_quantity - ? WHERE p_id = ? AND stock_quantity IS NOT NULL');
    foreach ($items as $item) {
        $itemStmt->execute([$orderId, $item['product_id'], $item['product_name'], $item['quantity'], $item['price'], $item['farmer_id']]);
        if ($products[$item['product_id']]['stock_quantity'] !== null) $stockStmt->execute([$item['quantity'], $item['product_id']]);
    }
    $pdo->commit();
    respond(200, ['success'=>true, 'message'=>'Order placed. Payment is not collected by this demo.', 'order_id'=>$orderId]);
} catch (DomainException $e) {
    if ($pdo instanceof PDO && $pdo->inTransaction()) $pdo->rollBack();
    respond(409, ['success'=>false, 'message'=>$e->getMessage()]);
} catch (Throwable $e) {
    if ($pdo instanceof PDO && $pdo->inTransaction()) $pdo->rollBack();
    error_log('[AgroProducts order] ' . $e->getMessage());
    respond(500, ['success'=>false, 'message'=>'Order could not be saved. Check that MySQL is running and the AgroProducts databases are imported.']);
}
