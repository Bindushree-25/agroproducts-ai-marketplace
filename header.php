<?php
if (session_status() === PHP_SESSION_NONE) { session_start(); }
$page_title = $page_title ?? 'AgroProducts | From Farm to Table';
$is_logged_in = isset($_SESSION['user_id']);
$user_type = $_SESSION['user_type'] ?? '';
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="theme-color" content="#173f32">
<meta name="description" content="AgroProducts connects customers with local farmers for fresh produce and a more transparent farm-to-table experience.">
<title><?php echo htmlspecialchars($page_title, ENT_QUOTES, 'UTF-8'); ?></title>
<link rel="stylesheet" href="style.css">
</head>
<body>
<header class="site-header">
  <a class="logo" href="index.php" aria-label="AgroProducts home"><img src="images/logo.png" alt=""><span class="brand-wordmark">AgroProducts<small>Rooted in freshness</small></span></a>
  <nav class="nav-links" aria-label="Main navigation">
    <a href="index.php#hero">Home</a><a href="shop.php">Marketplace</a><a href="ai_studio.php">AI Studio</a><a href="smart_guide.php">Smart Guide</a><?php if ($user_type === 'farmer'): ?><a href="farmer_dashboard.php">Farmer workspace</a><?php elseif ($user_type === 'customer'): ?><a href="customer_profile.php">My account</a><?php endif; ?><a href="index.php#about">Our Story</a>
  </nav>
  <div class="header-actions">
    <div class="login-container">
      <button type="button" class="login-trigger" id="loginTrigger" aria-label="Account menu" aria-expanded="false" style="background:transparent;padding:0;box-shadow:none"><img src="images/login.png" alt="Account" class="login-icon"></button>
      <div id="loginDropdown" class="dropdown-content">
      <?php if ($is_logged_in): ?>
        <?php if ($user_type === 'customer'): ?><a href="customer_profile.php">My customer profile</a><?php elseif ($user_type === 'farmer'): ?><a href="farmer_profile.php">My farmer profile</a><?php endif; ?>
        <?php if ($user_type === 'farmer'): ?><a href="farmer_dashboard.php">Farmer dashboard</a><a href="manage_products.php">Manage products</a><a href="ai_studio.php">AI business insights</a><?php elseif ($user_type === 'customer'): ?><a href="shop.php">Browse produce</a><a href="cart.php">My basket</a><a href="ai_studio.php">Smart shopping assistant</a><?php endif; ?>
        <a href="logout.php">Logout</a>
      <?php else: ?>
        <a href="customer_login.php">Customer login</a><a href="farmer_login.php">Farmer login</a><a href="customer_registration.html">Create customer account</a><a href="farmer_registration.html">Join as a farmer</a>
      <?php endif; ?>
      </div>
    </div>
    <a class="btn" href="shop.php">Explore produce <span aria-hidden="true">↗</span></a>
  </div>
</header>
<main>
<script>
document.addEventListener('DOMContentLoaded', function () {
  const trigger = document.getElementById('loginTrigger');
  const menu = document.getElementById('loginDropdown');
  if (!trigger || !menu) return;
  trigger.addEventListener('click', function (event) {
    event.stopPropagation();
    const isOpen = menu.classList.toggle('show');
    trigger.setAttribute('aria-expanded', String(isOpen));
  });
  document.addEventListener('click', function (event) {
    if (!menu.contains(event.target) && !trigger.contains(event.target)) {
      menu.classList.remove('show');
      trigger.setAttribute('aria-expanded', 'false');
    }
  });
  document.addEventListener('keydown', function (event) {
    if (event.key === 'Escape') { menu.classList.remove('show'); trigger.setAttribute('aria-expanded', 'false'); }
  });
});
</script>
