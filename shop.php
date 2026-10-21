<?php
if (session_status() === PHP_SESSION_NONE) session_start();
$userType = $_SESSION['user_type'] ?? '';
if (!isset($_SESSION['user_id']) || !in_array($userType, ['customer','farmer'], true)) {
    $_SESSION['after_login'] = 'shop.php';
    header('Location: customer_login.php'); exit;
}
require_once __DIR__ . '/db_connect.php';
$categories = ['All','Fruits','Vegetables','Grains & Pulses','Dairy Products'];
$products = [];
$sql = "SELECT p_id, farmer_id, p_name, description, price, stock_quantity, image_path, category FROM products ORDER BY p_name";
$result = $conn->query($sql);
if ($result) { while ($row = $result->fetch_assoc()) $products[] = $row; }
$page_title = 'The Market | AgroProducts';
include __DIR__ . '/header.php';
?>
<section class="market-hero">
  <div class="market-hero-copy"><span class="eyebrow">THE AGROPRODUCTS MARKET</span><h1>Good food.<br><em>Closer to home.</em></h1><p>Discover fresh produce and pantry staples from independent growers. Thoughtfully sourced, clearly priced.</p><div class="market-stats"><span><b><?= count($products) ?></b> listed products</span><span><b>4</b> market categories</span><span><b>Local</b> farmer-led</span></div></div>
  <div class="market-hero-art"><img src="images/banner.jpeg" alt="Fresh produce from the farm" onerror="this.style.display='none'"><div class="market-art-note"><span>✦</span><div><strong>Harvest to home</strong><small>Shop with intention</small></div></div></div>
</section>
<section class="market-shell">
  <div class="market-heading"><div><span class="eyebrow">CURATED FOR YOUR TABLE</span><h2>Explore the market</h2><p>Search by name, filter by category, and sort by what matters to you.</p></div><?php if ($userType === 'farmer'): ?><a class="btn btn-secondary" href="farmer_dashboard.php">← Back to farmer workspace</a><?php else: ?><a class="btn" href="cart.php">View basket <span aria-hidden="true">↗</span></a><?php endif; ?></div>
  <div class="market-toolbar"><label class="market-search"><span aria-hidden="true">⌕</span><input id="productSearch" type="search" placeholder="Search apples, grains, dairy…" aria-label="Search products"></label><label class="market-select"><span>Sort by</span><select id="productSort"><option value="featured">Featured</option><option value="price-asc">Price: low to high</option><option value="price-desc">Price: high to low</option><option value="name">Name A–Z</option></select></label></div>
  <div class="category-pills" id="categoryPills"><?php foreach ($categories as $i=>$category): ?><button type="button" class="category-pill <?= $i===0?'active':'' ?>" data-category="<?= htmlspecialchars($category, ENT_QUOTES, 'UTF-8') ?>"><?= htmlspecialchars($category) ?></button><?php endforeach; ?></div>
  <div class="market-result-line"><span id="resultCount"><?= count($products) ?> products</span><span>Prices shown in INR · Availability may change</span></div>
  <div class="market-grid" id="marketGrid">
  <?php foreach ($products as $p): $stock=(int)($p['stock_quantity'] ?? 0); $payload=['p_id'=>(int)$p['p_id'],'name'=>$p['p_name'],'price'=>(float)$p['price'],'image'=>$p['image_path'],'farmer_id'=>(int)$p['farmer_id']]; ?>
    <article class="market-card" data-name="<?= htmlspecialchars(mb_strtolower($p['p_name']), ENT_QUOTES, 'UTF-8') ?>" data-category="<?= htmlspecialchars($p['category'], ENT_QUOTES, 'UTF-8') ?>" data-price="<?= (float)$p['price'] ?>" data-id="<?= (int)$p['p_id'] ?>">
      <div class="market-card-image"><img loading="lazy" src="<?= htmlspecialchars($p['image_path'], ENT_QUOTES, 'UTF-8') ?>" alt="<?= htmlspecialchars($p['p_name'], ENT_QUOTES, 'UTF-8') ?>" onerror="this.src='images/logo.png'"><span class="market-category-tag"><?= htmlspecialchars($p['category']) ?></span><button class="wish-btn" type="button" aria-label="Add <?= htmlspecialchars($p['p_name'], ENT_QUOTES, 'UTF-8') ?> to wishlist" aria-pressed="false">♡</button></div>
      <div class="market-card-body"><div class="market-card-title"><h3><?= htmlspecialchars(ucwords($p['p_name']), ENT_QUOTES, 'UTF-8') ?></h3><span class="market-stock <?= $stock>0?'in-stock':'out-stock' ?>"><?= $stock>0 ? ($stock<4?'Low stock':'In stock') : 'Out of stock' ?></span></div><p><?= htmlspecialchars($p['description'] ?: 'Freshly listed by a local grower.', ENT_QUOTES, 'UTF-8') ?></p><div class="market-card-bottom"><div><strong>₹<?= number_format((float)$p['price'],2) ?></strong><small>per listed unit</small></div><?php if ($userType === 'customer'): ?><button type="button" class="btn market-add" <?= $stock<1?'disabled':'' ?> data-product='<?= htmlspecialchars(json_encode($payload, JSON_HEX_APOS|JSON_HEX_QUOT|JSON_HEX_TAG|JSON_HEX_AMP), ENT_QUOTES, 'UTF-8') ?>'><?= $stock>0?'Add to basket':'Unavailable' ?></button><?php else: ?><a class="btn btn-secondary market-add" href="farmer_dashboard.php">Farmer view</a><?php endif; ?></div></div>
    </article>
  <?php endforeach; ?>
  </div>
  <div id="marketEmpty" class="market-empty" hidden><span>⌕</span><h3>No matching produce</h3><p>Try another search or clear the category filter.</p><button class="btn btn-secondary" type="button" id="clearFilters">Clear filters</button></div>
</section>
<div class="market-toast" id="marketToast" role="status" aria-live="polite"></div>
<script>
(function(){
 const grid=document.getElementById('marketGrid'), cards=[...grid.querySelectorAll('.market-card')], search=document.getElementById('productSearch'), sort=document.getElementById('productSort'), count=document.getElementById('resultCount'), empty=document.getElementById('marketEmpty'); let category='All';
 function render(){const q=search.value.trim().toLowerCase();let visible=cards.filter(c=>(category==='All'||c.dataset.category===category)&&c.dataset.name.includes(q));visible.sort((a,b)=>sort.value==='price-asc'?+a.dataset.price-+b.dataset.price:sort.value==='price-desc'?+b.dataset.price-+a.dataset.price:sort.value==='name'?a.dataset.name.localeCompare(b.dataset.name):+a.dataset.id-+b.dataset.id);cards.forEach(c=>c.hidden=true);visible.forEach(c=>{c.hidden=false;grid.appendChild(c)});count.textContent=visible.length+' product'+(visible.length===1?'':'s');empty.hidden=visible.length>0;}
 search.addEventListener('input',render);sort.addEventListener('change',render);document.querySelectorAll('.category-pill').forEach(b=>b.addEventListener('click',()=>{category=b.dataset.category;document.querySelectorAll('.category-pill').forEach(x=>x.classList.toggle('active',x===b));render()}));document.getElementById('clearFilters').addEventListener('click',()=>{search.value='';sort.value='featured';category='All';document.querySelectorAll('.category-pill').forEach((x,i)=>x.classList.toggle('active',i===0));render()});
 const wish=JSON.parse(localStorage.getItem('agro_wishlist')||'[]');document.querySelectorAll('.wish-btn').forEach(b=>{const id=b.closest('.market-card').dataset.id;b.setAttribute('aria-pressed',String(wish.includes(id)));b.textContent=wish.includes(id)?'♥':'♡';b.addEventListener('click',()=>{let w=JSON.parse(localStorage.getItem('agro_wishlist')||'[]');const on=w.includes(id);w=on?w.filter(x=>x!==id):[...w,id];localStorage.setItem('agro_wishlist',JSON.stringify(w));b.textContent=on?'♡':'♥';b.setAttribute('aria-pressed',String(!on));toast(on?'Removed from saved items':'Saved to your wishlist')})});
 function toast(msg){const t=document.getElementById('marketToast');t.textContent=msg;t.classList.add('show');setTimeout(()=>t.classList.remove('show'),2300)}
 document.querySelectorAll('.market-add[data-product]').forEach(b=>b.addEventListener('click',()=>{try{const p=JSON.parse(b.dataset.product);let cart=JSON.parse(localStorage.getItem('cart')||'[]');const existing=cart.find(x=>Number(x.p_id)===Number(p.p_id));if(existing)existing.quantity=(Number(existing.quantity)||0)+1;else cart.push({...p,quantity:1});localStorage.setItem('cart',JSON.stringify(cart));toast(p.name+' added to your basket')}catch(e){toast('Could not update basket. Refresh and try again.')}}));render();
})();
</script>
<?php include __DIR__ . '/footer.php'; if(isset($conn) && $conn instanceof mysqli) $conn->close(); ?>
