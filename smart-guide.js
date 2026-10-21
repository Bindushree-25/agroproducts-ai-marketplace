document.addEventListener('DOMContentLoaded', function () {
  const form = document.getElementById('produceGuideForm');
  const result = document.getElementById('guideResult');
  if (!form || !result) return;
  const plans = {
    budget: {title:'A value-first basket',items:['Choose a small set of versatile staples such as potatoes, onions and carrots.','Compare the displayed unit prices and buy only quantities you can use.','Use lentils or beans already in your pantry to round out meals.'],meal:'Try a one-pot vegetable and lentil dish, with leftovers saved for another meal.',storage:'Store dry staples in sealed containers and keep a short list of what is running low.'},
    quick: {title:'A quick weekday basket',items:['Pick one fast-cooking vegetable such as capsicum or spinach.','Add tomatoes or onions as a flexible base for several dishes.','Choose fruit that is ready to eat for an easy side or snack.'],meal:'For a quick meal, sauté chopped vegetables with a pantry staple and serve with rice, bread or another base you already have.',storage:'Wash and cut only what you need; keep the rest stored appropriately.'},
    storage: {title:'A waste-aware basket',items:['Check your fridge and use the most ripe items first.','Choose ingredients that can be used in more than one meal.','Buy delicate leafy produce in smaller quantities if you cannot use it soon.'],meal:'Plan a flexible soup, stir-fry or cooked vegetable side around the items that need using first.',storage:'Keep a visible “use first” section and label leftovers with the date.'},
    variety: {title:'A colourful, varied basket',items:['Choose produce in a few different colours and textures.','Try one familiar item and one item you do not buy often.','Include both vegetables and fruit that suit your household and budget.'],meal:'Build a plate with a familiar base, a cooked vegetable and a fresh fruit or vegetable side.',storage:'Store each item according to its needs; avoid leaving cut produce uncovered.'}
  };
  form.addEventListener('submit', function (event) {
    event.preventDefault();
    const data = new FormData(form);
    const plan = plans[data.get('goal')] || plans.budget;
    const people = Number(data.get('household')) || 2;
    const minutes = Number(data.get('time')) || 20;
    const meal = data.get('meal');
    let mealText = plan.meal;
    if (meal === 'breakfast') mealText = 'Breakfast idea: pair fruit with an everyday breakfast staple you enjoy; prepare only what you will eat.';
    if (meal === 'lunch') mealText = 'Lunch idea: use vegetables in a simple rice bowl, wrap, salad or one-pot dish, depending on your preferences.';
    if (meal === 'snack') mealText = 'Snack idea: keep ready-to-eat fruit or prepared vegetable sticks available, with a dip or accompaniment if you like.';
    if (minutes <= 10) mealText += ' Keep preparation simple: choose washed, ready-to-use ingredients and a no-fuss combination.';
    if (minutes >= 40) mealText += ' With extra time, prepare a larger batch and safely refrigerate portions for later.';
    const list = plan.items.map(item => '<li>' + item.replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;') + '</li>').join('');
    result.innerHTML = '<h3>' + plan.title + '</h3><p><strong>Household:</strong> ' + (people === 5 ? '5+ people' : people + (people === 1 ? ' person' : ' people')) + ' · <strong>Prep time:</strong> ' + (minutes >= 40 ? '40+ minutes' : minutes + ' minutes') + '</p><ul>' + list + '</ul><p><strong>Meal idea:</strong> ' + mealText + '</p><p><strong>Storage reminder:</strong> ' + plan.storage + '</p><p><small>Demo recommendation generated locally from your selections; no data is sent to a server.</small></p>';
    result.hidden = false;
    result.scrollIntoView({behavior:'smooth',block:'nearest'});
  });
});
