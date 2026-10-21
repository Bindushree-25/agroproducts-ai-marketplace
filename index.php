<?php
if (session_status() === PHP_SESSION_NONE) { session_start(); }
$page_title = 'AgroProducts | From Farm to Table';
include 'header.php';
?>
<section id="hero" class="banner">
  <img src="images/banner.jpeg" alt="Fresh farm produce ready for the market" class="banner-image">
  <div class="banner-text">
    <span class="eyebrow">A more thoughtful way to shop</span>
    <h1>Closer to the farm.<br>Better on your table.</h1>
    <p>Discover fresh produce and everyday essentials through a marketplace designed to bring farmers and customers closer together.</p>
    <div class="hero-actions"><a class="btn btn-gold" href="shop.php">Explore the harvest <span aria-hidden="true">→</span></a><a class="btn btn-secondary" href="farmer_registration.html">I'm a farmer</a></div>
  </div>
</section>
<div class="trust-strip" aria-label="Marketplace values">
  <div class="trust-item"><strong>Farm-to-table connection</strong><span>Discover produce from local sellers</span></div>
  <div class="trust-item"><strong>One simple marketplace</strong><span>Browse, compare and build your cart</span></div>
  <div class="trust-item"><strong>Made for both sides</strong><span>Customer shopping and farmer tools</span></div>
</div>
<section id="services" class="services">
  <span class="eyebrow">One platform, two communities</span><h2>Fresh choices. Stronger connections.</h2>
  <p>Whether you're stocking your kitchen or bringing your harvest to market, AgroProducts gives you a simple place to start.</p>
  <div class="service-cards">
    <article class="service-card"><div class="card-icon" aria-hidden="true">🥬</div><h3>Shop the harvest</h3><p>Explore available produce and everyday essentials in one place.</p><a class="service-card-link" href="shop.php">Browse products →</a></article>
    <article class="service-card"><div class="card-icon" aria-hidden="true">🌱</div><h3>Grow your business</h3><p>Farmers can access their workspace and manage product listings.</p><a class="service-card-link" href="farmer_dashboard.php">Farmer workspace →</a></article>
    <article class="service-card"><div class="card-icon" aria-hidden="true">✦</div><h3>Smart produce guide</h3><p>Get practical storage, meal-planning and shopping suggestions.</p><a class="service-card-link" href="smart_guide.php">Try the guide →</a></article>
  </div>
</section>
<section id="how-it-works" class="how-it-works">
  <span class="eyebrow">Simple by design</span><h2>From discovery to doorstep</h2>
  <div class="how-it-works-container">
    <article class="step"><span class="step-number">STEP 01</span><h3>Discover</h3><p>Explore product listings and choose the items that fit your needs.</p></article>
    <article class="step"><span class="step-number">STEP 02</span><h3>Connect</h3><p>Use customer and farmer accounts to keep the marketplace organised.</p></article>
    <article class="step"><span class="step-number">STEP 03</span><h3>Manage</h3><p>Use the existing cart and order workflow to move through your purchase.</p></article>
  </div>
</section>
<section id="about" class="about">
  <span class="eyebrow">Our philosophy</span><h2>A marketplace with purpose</h2>
  <p>AgroProducts is a database-driven marketplace concept built around a simple idea: make the connection between growers and customers easier to navigate.</p>
  <p>The project brings together separate customer and farmer journeys, product listings, account sessions and cart/order workflows in a single PHP and MySQL application.</p>
</section>
<section class="closing-cta"><span class="eyebrow">Start exploring</span><h2>Bring a little more farm into your day.</h2><p>Browse the catalogue or explore the Smart Produce Guide for helpful, practical suggestions.</p><div class="hero-actions" style="justify-content:center"><a class="btn btn-gold" href="shop.php">Shop products</a><a class="btn btn-secondary" href="smart_guide.php">Open Smart Guide</a></div></section>
<?php include 'footer.php'; ?>
