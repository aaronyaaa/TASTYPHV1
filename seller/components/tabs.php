<!-- Modern Seller Tabs UI -->
<div class="seller-tabs-bar">
  <div class="tabs-scroll">
    <button class="tab-btn active" data-tab="products"><i class="fa-solid fa-cubes"></i><span>Products</span></button>
    <button class="tab-btn" data-tab="inventory"><i class="fa-solid fa-box-archive"></i><span>Inventory</span></button>
    <button class="tab-btn" data-tab="recipes"><i class="fa-solid fa-book"></i><span>Recipes</span></button>
    <button class="tab-btn" data-tab="categories"><i class="fa-solid fa-tags"></i><span>Categories</span></button>
    <button class="tab-btn" data-tab="hours"><i class="fa-solid fa-clock"></i><span>Hours</span></button>
  </div>
</div>
<div class="seller-tab-content">
  <div class="tab-pane-custom" id="tab-products">
    <?php include('products.php'); ?>
  </div>
  <div class="tab-pane-custom d-none" id="tab-inventory">
    <?php include('inventory.php'); ?>
  </div>
  <div class="tab-pane-custom d-none" id="tab-recipes">
    <?php include('recipes.php'); ?>
  </div>
  <div class="tab-pane-custom d-none" id="tab-categories">
    <?php include('categories.php'); ?>
  </div>
  <div class="tab-pane-custom d-none" id="tab-hours">
    <p class="lead">Business hours content goes here.</p>
  </div>
</div>
<script>
// Custom tab switching
const tabBtns = document.querySelectorAll('.tab-btn');
const tabPanes = document.querySelectorAll('.tab-pane-custom');
tabBtns.forEach(btn => {
  btn.addEventListener('click', function() {
    tabBtns.forEach(b => b.classList.remove('active'));
    this.classList.add('active');
    const tab = this.getAttribute('data-tab');
    tabPanes.forEach(pane => {
      if (pane.id === 'tab-' + tab) {
        pane.classList.remove('d-none');
      } else {
        pane.classList.add('d-none');
      }
    });
  });
});
</script>
