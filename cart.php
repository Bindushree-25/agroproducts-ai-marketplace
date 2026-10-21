<?php
session_start();
// Check if user is logged in and is a customer
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] != 'customer') {
    header("Location: customer_login.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Your Cart - AgroProducts</title>
    <style>
        body {
            font-family: 'Segoe UI', sans-serif;
            background: #fdfdfb;
            color: #333;
            margin: 0;
            padding: 1rem;
        }

        h1 {
            color: #2c5e31;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 1rem;
            background: white;
            box-shadow: 0 0 5px rgba(0, 0, 0, 0.1);
        }

        th,
        td {
            padding: 12px;
            border: 1px solid #ddd;
            text-align: center;
            vertical-align: middle;
        }

        th {
            background-color: #a3c293;
            color: white;
        }

        img {
            width: 60px;
            height: 60px;
            object-fit: cover;
            border-radius: 4px;
        }

        .quantity-controls button {
            background-color: #4CAF50;
            color: white;
            border: none;
            padding: 5px 10px;
            cursor: pointer;
            margin: 0 2px;
            border-radius: 3px;
            font-weight: bold;
        }

        .remove-btn {
            background-color: #e74c3c;
            border: none;
            color: white;
            padding: 5px 10px;
            cursor: pointer;
            border-radius: 3px;
        }

        .summary {
            margin-top: 1rem;
            text-align: right;
            font-size: 1.1rem;
            max-width: 400px;
            margin-left: auto;
            background: white;
            padding: 1rem;
            box-shadow: 0 0 5px rgba(0, 0, 0, 0.1);
            border-radius: 6px;
        }

        .summary div {
            margin-bottom: 0.5rem;
        }

        .place-order-btn {
            margin-top: 1rem;
            background-color: #4CAF50;
            color: white;
            border: none;
            padding: 12px 20px;
            font-size: 1.1rem;
            cursor: pointer;
            border-radius: 5px;
            float: right;
        }

        .empty-cart {
            text-align: center;
            margin-top: 3rem;
            font-size: 1.2rem;
            color: #777;
        }
    </style>
    <link rel="stylesheet" href="style.css">
</head>

<body>

    <h1>Your Cart</h1>

    <table id="cart-table" style="display:none;">
        <thead>
            <tr>
                <th>Product</th>
                <th>Name</th>
                <th>Price (₹)</th>
                <th>Quantity</th>
                <th>Total (₹)</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody id="cart-body"></tbody>
    </table>

    <div id="empty-message" class="empty-cart">Your cart is empty.</div>

    <div class="summary" id="summary" style="display:none;">
        <div>Subtotal: ₹<span id="subtotal">0.00</span></div>
        <div>GST (5%): ₹<span id="gst">0.00</span></div>
        <div>Delivery Charges: ₹<span id="delivery">50.00</span></div>
        <div><strong>Total: ₹<span id="total">0.00</span></strong></div>
        <button class="place-order-btn" id="place-order-btn">Place Order</button>
    </div>

    <script>
        // Load cart from localStorage or empty array if none
        function loadCart() {
            let cart = localStorage.getItem("cart");
            return cart ? JSON.parse(cart) : [];
        }

        // Save cart to localStorage
        function saveCart(cart) {
            localStorage.setItem("cart", JSON.stringify(cart));
        }

        // Render cart items and summary
        function renderCart() {
            const cart = loadCart();
            const tbody = document.getElementById("cart-body");
            const table = document.getElementById("cart-table");
            const emptyMsg = document.getElementById("empty-message");
            const summaryDiv = document.getElementById("summary");

            tbody.innerHTML = "";

            if (cart.length === 0) {
                table.style.display = "none";
                summaryDiv.style.display = "none";
                emptyMsg.style.display = "block";
                return;
            }

            table.style.display = "table";
            summaryDiv.style.display = "block";
            emptyMsg.style.display = "none";

            let subtotal = 0;

            cart.forEach((item, index) => {
                // Ensure price is a number
                const price = parseFloat(item.price);
                const quantity = parseInt(item.quantity);
                const total = price * quantity;
                subtotal += total;

                const tr = document.createElement("tr");

                tr.innerHTML = `
                <td><img src="${item.image}" alt="${item.name}"></td>
                <td>${item.name}</td>
                <td>${price.toFixed(2)}</td>
                <td class="quantity-controls">
                    <button onclick="changeQuantity(${index}, -1)">-</button>
                    ${quantity}
                    <button onclick="changeQuantity(${index}, 1)">+</button>
                </td>
                <td>${total.toFixed(2)}</td>
                <td><button class="remove-btn" onclick="removeItem(${index})">Remove</button></td>
            `;

                tbody.appendChild(tr);
            });

            const gst = subtotal * 0.05;
            const delivery = 50.00;
            const total = subtotal + gst + delivery;

            document.getElementById("subtotal").textContent = subtotal.toFixed(2);
            document.getElementById("gst").textContent = gst.toFixed(2);
            document.getElementById("delivery").textContent = delivery.toFixed(2);
            document.getElementById("total").textContent = total.toFixed(2);
        }

        // Change quantity of item at index by delta (+1 or -1)
        function changeQuantity(index, delta) {
            let cart = loadCart();
            cart[index].quantity += delta;
            if (cart[index].quantity <= 0) {
                cart.splice(index, 1);
            }
            saveCart(cart);
            renderCart();
        }

        // Remove item at index
        function removeItem(index) {
            let cart = loadCart();
            cart.splice(index, 1);
            saveCart(cart);
            renderCart();
        }

        // Place order button click handler - sends AJAX POST to save_order.php
        document.getElementById("place-order-btn").addEventListener("click", () => {
            const cart = loadCart();

            if (cart.length === 0) {
                alert("Your cart is empty.");
                return;
            }

            // Map cart items to expected keys by save_order.php
            const orderItems = cart.map(item => {
                return {
                    product_id: parseInt(item.p_id),  // Use p_id from item
                    product_name: item.name,
                    quantity: parseInt(item.quantity),
                    price: parseFloat(item.price),
                    farmer_id: item.farmer_id !== undefined ? parseInt(item.farmer_id) : 0  // fallback to 0
                };
            });

            fetch('save_order.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ items: orderItems })
            })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        localStorage.removeItem("cart");
                        window.location.href = "order_confirmation.php?order_id=" + data.order_id;
                    }
                    else {
                        alert("Failed to place order: " + (data.message || "Unknown error"));
                    }
                })
                .catch(err => {
                    console.error('Fetch error:', err);
                    alert("An error occurred while placing the order. Please try again.");
                });
        });

        // Initialize cart display on page load
        renderCart();
    </script>

</body>

</html>