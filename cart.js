// Initialize cart from localStorage or empty array
let cart = JSON.parse(localStorage.getItem("cart")) || [];

// Function to update the cart count in the header
function updateCartCount() {
  const cartCount = document.getElementById("cart-count");
  cartCount.textContent = cart.reduce((total, item) => total + item.quantity, 0);
}

// Function to render the cart items in the cart container
function renderCart() {
  const cartContainer = document.getElementById("cart-items");
  cartContainer.innerHTML = "";

  // If cart is empty, display a message
  if (cart.length === 0) {
    cartContainer.innerHTML = "<p>Your cart is empty</p>";
    return;
  }

  // Loop through each item and create a cart item element
  cart.forEach(item => {
    const cartItem = document.createElement("div");
    cartItem.classList.add("cart-item");

    cartItem.innerHTML = `
      <div class="item-details">
        <img src="${item.image}" alt="${item.name}" />
        <div class="item-info">
          <p><strong>${item.name}</strong></p>
          <p>₹${item.price} × ${item.quantity} = ₹${(item.price * item.quantity).toFixed(2)}</p>
        </div>
      </div>
      <button class="remove-btn" onclick="removeFromCart('${item.name}')">Remove</button>
    `;
    cartContainer.appendChild(cartItem);
  });

  // Update the cart summary (subtotal, GST, total)
  updateSummary();
}

// Function to update the cart summary (subtotal, GST, total)
function updateSummary() {
  const subtotalElement = document.getElementById("subtotal");
  const gstElement = document.getElementById("gst");
  const deliveryElement = document.getElementById("delivery");
  const totalElement = document.getElementById("total");

  const subtotal = cart.reduce((total, item) => total + item.price * item.quantity, 0);
  const gst = subtotal * 0.05; // 5% GST
  const delivery = 0; // For now, delivery is set to ₹0.00, but you can change this later
  const total = subtotal + gst + delivery;

  // Update the summary text
  subtotalElement.textContent = `Subtotal: ₹${subtotal.toFixed(2)}`;
  gstElement.textContent = `GST (5%): ₹${gst.toFixed(2)}`;
  deliveryElement.textContent = `Delivery: ₹${delivery.toFixed(2)}`;
  totalElement.innerHTML = `<strong>Total: ₹${total.toFixed(2)}</strong>`;
}

// Function to remove an item from the cart
function removeFromCart(name) {
  const itemIndex = cart.findIndex(item => item.name === name);
  if (itemIndex !== -1) {
    cart.splice(itemIndex, 1);
    localStorage.setItem("cart", JSON.stringify(cart));
    renderCart(); // Re-render the cart after removal
    updateCartCount(); // Update the cart count in the header
  }
}

// Function to place an order (can be customized to handle order logic)
function placeOrder() {
  if (cart.length === 0) {
    alert("Your cart is empty. Please add items before placing an order.");
    return;
  }

  alert("Order placed successfully!");
  // Clear the cart after placing the order (optional)
  cart = [];
  localStorage.setItem("cart", JSON.stringify(cart));
  renderCart();
  updateCartCount();
}

// Initial render of the cart when the page loads
document.addEventListener("DOMContentLoaded", function () {
  renderCart();
  updateCartCount();
});
