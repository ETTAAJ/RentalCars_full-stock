<?php 
require 'config.php';

// Get car ID
$id = intval($_GET['id'] ?? 0);
if ($id <= 0) {
    header("Location: index.php");
    exit;
}

// Fetch current car
$stmt = $pdo->prepare("SELECT * FROM cars WHERE id = ?");
$stmt->execute([$id]);
$car = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$car) {
    header("Location: index.php");
    exit;
}

// Fetch 3 random similar cars (exclude current)
$stmt = $pdo->prepare("
    SELECT * FROM cars 
    WHERE id != ? 
    ORDER BY RAND() 
    LIMIT 3
");
$stmt->execute([$id]);
$similarCars = $stmt->fetchAll(PDO::FETCH_ASSOC);

/* -------------------------------------------------
   IMAGE HELPER – SAME LOGIC AS INDEX.PHP & ADMIN
   ------------------------------------------------- */
function carImageUrl($filename, $size = 'large') {
    if (empty($filename)) return '';
    $path = 'uploads/' . basename($filename);
    $full = $_SERVER['DOCUMENT_ROOT'] . '/' . $path;
    $v = file_exists($full) ? '?v=' . filemtime($full) : '';
    return $path . $v;
}
?>
<?php include 'header.php'; ?>

<main class="max-w-7xl mx-auto px-4 py-12">
  <!-- Main Car -->
  <div class="grid md:grid-cols-2 gap-10 mb-16">
    <!-- Image -->
    <div>
      <?php
      $imgSrc = carImageUrl($car['image']);
      $placeholder = 'https://via.placeholder.com/600x400?text=' . urlencode($car['name']);
      $src = $imgSrc ?: $placeholder;
      ?>
      <img src="<?= $src ?>" 
           alt="<?= htmlspecialchars($car['name']) ?>"
           class="w-full h-96 object-cover rounded-xl shadow-lg"
           onerror="this.src='https://via.placeholder.com/600x400?text=No+Image'">
    </div>

    <!-- Details -->
    <div>
      <h1 class="text-3xl font-bold text-gray-900 mb-4"><?= htmlspecialchars($car['name']) ?></h1>
      
      <div class="grid grid-cols-2 gap-4 mb-6 text-gray-700">
        <div class="flex items-center">
          <svg class="w-5 h-5 mr-2 text-gold" fill="currentColor" viewBox="0 0 20 20">
            <path d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z"/>
          </svg>
          <?= $car['seats'] ?> Seats
        </div>
        <div class="flex items-center">
          <svg class="w-5 h-5 mr-2 text-gold" fill="currentColor" viewBox="0 0 20 20">
            <path d="M5 3h10a2 2 0 012 2v10a2 2 0 01-2 2H5a2 2 0 01-2-2V5a2 2 0 012-2z"/>
          </svg>
          <?= $car['bags'] ?> Bags
        </div>
        <div><?= htmlspecialchars($car['gear']) ?> Gear</div>
        <div><?= htmlspecialchars($car['fuel']) ?> Fuel</div>
      </div>

      <div class="bg-gray-100 p-6 rounded-xl mb-6">
        <h3 class="font-semibold text-lg mb-3">Rental Prices</h3>
        <div class="space-y-2 text-lg">
          <div class="flex justify-between">
            <span>Per Day</span> 
            <span class="font-bold text-gold">MAD<?= number_format($car['price_day']) ?></span>
          </div>
          <div class="flex justify-between">
            <span>Per Week</span> 
            <span class="font-bold text-gold">MAD<?= number_format($car['price_week']) ?></span>
          </div>
          <div class="flex justify-between">
            <span>Per Month</span> 
            <span class="font-bold text-gold">MAD<?= number_format($car['price_month']) ?></span>
          </div>
        </div>
      </div>

      <a href="booking.php?id=<?= $car['id'] ?>" 
         class="block w-full text-center bg-gold hover:bg-gold-dark text-white font-bold py-4 rounded-xl transition shadow-lg">
        Book Now
      </a>
    </div>
  </div>

  <!-- Other Cars You Might Like -->
  <?php if (!empty($similarCars)): ?>
  <div class="mt-20">
    <h2 class="text-2xl font-bold text-gray-900 mb-8 text-center md:text-left">
      Other Cars You Might Like
    </h2>
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
      <?php foreach ($similarCars as $similar): ?>
        <?php
        $simImg = carImageUrl($similar['image']);
        $simPh  = 'https://via.placeholder.com/300x200?text=' . urlencode($similar['name']);
        $simSrc = $simImg ?: $simPh;
        ?>
        <div class="bg-white rounded-xl shadow-md overflow-hidden hover:shadow-xl transition transform hover:-translate-y-1">
          <div class="h-48 bg-gray-200 overflow-hidden">
            <img src="<?= $simSrc ?>" 
                 alt="<?= htmlspecialchars($similar['name']) ?>" 
                 class="w-full h-full object-cover"
                 onerror="this.src='https://via.placeholder.com/300x200?text=No+Image'">
          </div>
          <div class="p-5">
            <h3 class="text-lg font-semibold text-gray-900 mb-2"><?= htmlspecialchars($similar['name']) ?></h3>
            <div class="flex justify-between text-sm text-gray-500 mb-3">
              <span><?= htmlspecialchars($similar['gear']) ?></span>
              <span><?= htmlspecialchars($similar['fuel']) ?></span>
            </div>
            <div class="flex justify-between items-center">
              <div>
                <span class="text-xl font-bold text-gold">MAD<?= number_format($similar['price_day']) ?></span>
                <span class="text-xs text-gray-500">/day</span>
              </div>
              <a href="car-detail.php?id=<?= $similar['id'] ?>" 
                 class="bg-gold hover:bg-gold-dark text-white text-sm font-medium py-2 px-4 rounded-lg transition">
                View
              </a>
            </div>
          </div>
        </div>
      <?php endforeach; ?>
    </div>
  </div>
  <?php endif; ?>
</main>

<?php include 'footer.php'; ?>