// Function to update the cart count displayed on the cart icon
function updateCartCount() {
  const cartCountElement = document.getElementById("cart-count");
  if (cartCountElement) {
    // Get the cart data from localStorage
    const cart = JSON.parse(localStorage.getItem("cart")) || [];
    // Calculate the total quantity of items in the cart
    const totalQuantity = cart.reduce((total, item) => total + item.quantity, 0);
    // Set the text content of the cart count element to the total quantity
    cartCountElement.textContent = totalQuantity;
  } else {
    console.error("Cart count element not found!");
  }
}

// Function to add an item to the cart
function addToCart(name, price, image) {
  // Retrieve the cart from localStorage
  const cart = JSON.parse(localStorage.getItem("cart")) || [];

  // Check if the item is already in the cart
  const index = cart.findIndex(item => item.name === name);
  if (index !== -1) {
    // If the item is already in the cart, increase the quantity
    cart[index].quantity += 1;
  } else {
    // If it's a new item, add it to the cart with quantity 1
    cart.push({ name, price, image, quantity: 1 });
  }

  // Save the updated cart back to localStorage
  localStorage.setItem("cart", JSON.stringify(cart));

  // Update the cart count display
  updateCartCount();
}

// Update the cart count when the page loads
document.addEventListener("DOMContentLoaded", function () {
  updateCartCount();
});
