<?php
require 'config.php';

// --- 1. Get Car ---
$id = $_GET['id'] ?? 0;
$stmt = $pdo->prepare("SELECT * FROM cars WHERE id = ?");
$stmt->execute([$id]);
$car = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$car) {
    header("Location: index.php");
    exit;
}

$minDays = 3;
$pricePerDay = $car['price_day'];

/* -------------------------------------------------
   IMAGE HELPER – SAME AS EVERYWHERE
   ------------------------------------------------- */
function carImageUrl($filename) {
    if (empty($filename)) return '';
    $path = 'uploads/' . basename($filename);
    $full = $_SERVER['DOCUMENT_ROOT'] . '/' . $path;
    $v = file_exists($full) ? '?v=' . filemtime($full) : '';
    return $path . $v;
}
?>
<?php include 'header.php'; ?>

<main class="max-w-4xl mx-auto px-4 py-12">
  <h1 class="text-3xl font-bold text-center mb-8 text-gold">Complete Your Booking</h1>

  <div class="bg-white rounded-xl shadow-lg p-8">
    <div class="grid md:grid-cols-2 gap-8 mb-8">
      <!-- Car Info -->
      <div data-aos="fade-right">
        <h3 class="font-semibold text-lg mb-4"><?= htmlspecialchars($car['name']) ?></h3>

        <?php
        $imgSrc = carImageUrl($car['image']);
        $placeholder = 'https://via.placeholder.com/400x200?text=' . urlencode($car['name']);
        $src = $imgSrc ?: $placeholder;
        ?>
        <img src="<?= $src ?>"
             alt="<?= htmlspecialchars($car['name']) ?>"
             class="w-full h-48 object-cover rounded-lg shadow-md"
             onerror="this.src='https://via.placeholder.com/400x200?text=No+Image'">

        <p class="mt-3 text-sm text-gray-600">
          <span class="font-bold text-gold">MAD<?= number_format($pricePerDay) ?></span>/day
        </p>
        <p class="text-xs text-gray-500 mt-1">Minimum <?= $minDays ?> days required</p>
      </div>

      <!-- Booking Form -->
      <form id="booking-form" action="booking-process.php" method="POST" class="space-y-6" data-aos="fade-left">
        <input type="hidden" name="car_id" value="<?= $car['id'] ?>">

        <!-- Pickup Date -->
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-2">Pickup Date</label>
          <input type="date" name="pickup" id="pickup" required
                 min="<?= date('Y-m-d') ?>"
                 class="w-full p-3 border rounded-lg focus:ring-2 focus:ring-gold focus:border-gold transition">
        </div>

        <!-- Return Date -->
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-2">Return Date</label>
          <input type="date" name="return" id="return" required
                 class="w-full p-3 border rounded-lg focus:ring-2 focus:ring-gold focus:border-gold transition">
          <p id="date-error" class="text-red-600 text-xs mt-1 hidden">
            Return date must be at least <?= $minDays ?> days after pickup.
          </p>
        </div>

        <!-- Total Price (Live) -->
        <div class="bg-gold/5 p-4 rounded-lg">
          <p class="text-sm font-medium text-gray-700">Total Price:</p>
          <p id="total-price" class="text-2xl font-bold text-gold">MAD0</p>
          <p id="days-count" class="text-sm text-gray-600"></p>
        </div>

        <!-- Customer Info -->
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-2">Full Name</label>
          <input type="text" name="name" required placeholder="John Doe"
                 class="w-full p-3 border rounded-lg focus:ring-2 focus:ring-gold">
        </div>
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-2">Email</label>
          <input type="email" name="email" required placeholder="john@example.com"
                 class="w-full p-3 border rounded-lg focus:ring-2 focus:ring-gold">
        </div>
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-2">Phone</label>
          <input type="tel" name="phone" required placeholder="+212 6 00 00 00 00"
                 class="w-full p-3 border rounded-lg focus:ring-2 focus:ring-gold">
        </div>

        <!-- Submit -->
        <button type="submit"
                class="w-full bg-gold hover:bg-gold-dark text-white font-bold py-4 rounded-xl transition transform hover:scale-105 disabled:opacity-50 disabled:cursor-not-allowed"
                id="submit-btn" disabled>
          Confirm Booking
        </button>
      </form>
    </div>
  </div>
</main>

<?php include 'footer.php'; ?>

<!-- AOS + JS Logic -->
<script src="https://unpkg.com/aos@2.3.4/dist/aos.js"></script>
<script>
  AOS.init({ once: true });

  const pickupInput = document.getElementById('pickup');
  const returnInput = document.getElementById('return');
  const totalPriceEl = document.getElementById('total-price');
  const daysCountEl = document.getElementById('days-count');
  const errorEl = document.getElementById('date-error');
  const submitBtn = document.getElementById('submit-btn');

  const pricePerDay = <?= $pricePerDay ?>;
  const minDays = <?= $minDays ?>;

  function validateDates() {
    const pickup = new Date(pickupInput.value);
    const ret = new Date(returnInput.value);

    if (!pickupInput.value || !returnInput.value) {
      submitBtn.disabled = true;
      return;
    }

    const diffTime = ret - pickup;
    const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));

    if (diffDays < minDays || diffDays < 0) {
      errorEl.classList.remove('hidden');
      submitBtn.disabled = true;
      totalPriceEl.textContent = 'MAD0';
      daysCountEl.textContent = '';
      return;
    }

    errorEl.classList.add('hidden');
    const total = diffDays * pricePerDay;
    totalPriceEl.textContent = `MAD${total.toLocaleString()}`;
    daysCountEl.textContent = `${diffDays} day${diffDays > 1 ? 's' : ''}`;
    submitBtn.disabled = false;
  }

  // Set minimum return date when pickup changes
  pickupInput.addEventListener('change', () => {
    const minReturn = new Date(pickupInput.value);
    minReturn.setDate(minReturn.getDate() + minDays);
    returnInput.min = minReturn.toISOString().split('T')[0];
    validateDates();
  });

  returnInput.addEventListener('change', validateDates);

  // Initialize on load
  document.addEventListener('DOMContentLoaded', () => {
    const today = new Date().toISOString().split('T')[0];
    pickupInput.min = today;
    validateDates();
  });
</script>
</body>
</html>