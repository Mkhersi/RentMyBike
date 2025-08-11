<!-- views/index.html -->
<?php include_once __DIR__ . '/_header.php'; ?>

<section class="welcome-banner" aria-label="Welcome">
  <div class="banner-text">
    <h1>Ready to find your next bike?</h1>
    <p>A business platform for customers and bike owners to  enjoy</p>
  </div>
</section>

<section class="controls">
  <label for="sort" class="sr-only">Sort</label>
  <select id="sort" name="sort" aria-label="Sort results">
    <option value="newest">Newest</option>
    <option value="price-asc">Price: Low to High</option>
    <option value="price-desc">Price: High to Low</option>
  </select>
</section>

<section class="browse-bikes">
  <h1>Browse Bikes</h1>

  <!-- Search form -->
  <form id="searchForm" style="margin-bottom:20px;">
    <input
      type="text"
      id="searchInput"
      name="q"
      placeholder="Search by make or model…"
      style="padding:8px; width:300px;"
    />
    <button type="submit" style="padding:8px 12px;">Search</button>
  </form>

  <div class="bike-grid" id="bikeGrid">
    <p>Loading bikes…</p>
  </div>
</section>

<script src="../assets/js/bikes.js"></script>

<?php include_once __DIR__ . '/_footer.php'; ?>
