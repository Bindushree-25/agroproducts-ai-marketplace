<?php
if (session_status() === PHP_SESSION_NONE) { session_start(); }
$page_title = 'Smart Produce Guide | AgroProducts';
include 'header.php';
?>
<div class="guide-shell">
  <section class="guide-hero">
    <span class="eyebrow">AgroProducts companion</span>
    <h1>Your smarter produce routine.</h1>
    <p>Choose a goal and a few preferences to get a tailored produce shortlist, storage reminders and simple meal ideas. This is a lightweight, rule-based demo feature that works offline—no API key required.</p>
  </section>
  <section class="guide-panel">
    <h2 style="text-align:left;margin-bottom:8px">Build your produce plan</h2>
    <p style="text-align:left;margin:0 0 24px">Select what matters today. Recommendations are general suggestions, not medical or nutrition advice.</p>
    <form id="produceGuideForm">
      <div class="guide-controls">
        <div><label for="goal">Your goal</label><select id="goal" name="goal"><option value="budget">Shop on a budget</option><option value="quick">Quick weekday meals</option><option value="storage">Reduce food waste</option><option value="variety">Add more variety</option></select></div>
        <div><label for="meal">Meal preference</label><select id="meal" name="meal"><option value="any">Anything flexible</option><option value="breakfast">Breakfast ideas</option><option value="lunch">Lunch ideas</option><option value="snack">Snacks and sides</option></select></div>
        <div><label for="household">Household size</label><select id="household" name="household"><option value="1">1 person</option><option value="2" selected>2 people</option><option value="4">3–4 people</option><option value="5">5+ people</option></select></div>
        <div><label for="time">Prep time available</label><select id="time" name="time"><option value="10">10 minutes</option><option value="20" selected>20 minutes</option><option value="40">40+ minutes</option></select></div>
      </div>
      <p style="text-align:left;margin:22px 0 12px"><button type="submit">Generate my produce plan ↗</button></p>
    </form>
    <div id="guideResult" class="guide-result" aria-live="polite" hidden></div>
  </section>
  <section class="guide-panel"><span class="eyebrow">A small habit, a real difference</span><h2 style="text-align:left">Keep produce fresher for longer</h2><div class="service-cards" style="grid-template-columns:repeat(3,minmax(0,1fr));margin-top:20px"><article class="feature-card"><div class="card-icon">🥕</div><h3>Store with care</h3><p>Keep produce dry where appropriate, and separate items that ripen quickly from delicate leafy greens.</p></article><article class="feature-card"><div class="card-icon">🗓️</div><h3>Plan before buying</h3><p>Check what you already have and plan two or three meals before adding more to your basket.</p></article><article class="feature-card"><div class="card-icon">♻️</div><h3>Use what you have</h3><p>Turn ripe vegetables into soups, stir-fries or cooked sides before they go to waste.</p></article></div></section>
</div>
<script src="smart-guide.js" defer></script>
<?php include 'footer.php'; ?>
