<?php
require 'config.php';

/* -------------------------------------------------
   1. Build Query
   ------------------------------------------------- */
$search = trim($_GET['search'] ?? '');
$gear   = $_GET['gear']   ?? '';
$fuel   = $_GET['fuel']   ?? '';
$sort   = $_GET['sort']   ?? 'low';

$where  = []; 
$params = [];

if ($search !== '') {
    $where[] = "name LIKE ?";
    $params[] = "%$search%";
}
if ($gear !== '' && in_array($gear, ['Manual', 'Automatic'])) {
    $where[] = "gear = ?";
    $params[] = $gear;
}
if ($fuel !== '' && in_array($fuel, ['Diesel', 'Petrol'])) {
    $where[] = "fuel = ?";
    $params[] = $fuel;
}

$order = ($sort === 'high') ? 'price_day DESC' : 'price_day ASC';

$sql = "SELECT * FROM cars";
if (!empty($where)) {
    $sql .= " WHERE " . implode(' AND ', $where);
}
$sql .= " ORDER BY $order";

/* -------------------------------------------------
   2. renderCarCard()
   ------------------------------------------------- */
function renderCarCard($car, $index = 0): string
{
    $baseImg = !empty($car['image'])
        ? 'uploads/' . basename($car['image'])
        : 'https://via.placeholder.com/300x200/cccccc/999999?text=' . urlencode($car['name']);

    $cacheBuster = '';
    $fullPath = $_SERVER['DOCUMENT_ROOT'] . '/' . $baseImg;
    if (file_exists($fullPath)) {
        $cacheBuster = '?v=' . filemtime($fullPath);
    }

    $imgUrl = $baseImg . $cacheBuster;
    $delay  = 100 + ($index % 8) * 80;

    ob_start(); ?>
    <div data-aos="fade-up" data-aos-delay="<?= $delay ?>" data-aos-duration="600"
         class="bg-white rounded-xl shadow-md overflow-hidden hover:shadow-xl transition transform hover:-translate-y-1">
        <div class="h-48 bg-gray-200 rounded-t-xl overflow-hidden">
            <img src="<?= htmlspecialchars($imgUrl) ?>"
                 alt="<?= htmlspecialchars($car['name']) ?>"
                 class="w-full h-full object-cover"
                 onerror="this.onerror=null; this.src='https://via.placeholder.com/300x200?text=No+Image'">
        </div>
        <div class="p-5">
            <h3 class="text-lg font-semibold text-gray-900 mb-2"><?= htmlspecialchars($car['name']) ?></h3>
            <div class="flex flex-wrap gap-2 text-sm text-gray-600 mb-4">
                <span class="flex items-center">
                    <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20"><path d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z"/></svg>
                    <?= (int)$car['seats'] ?> Seats
                </span>
                <span class="flex items-center">
                    <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20"><path d="M5 3h10a2 2 0 012 2v10a2 2 0 01-2 2H5a2 2 0 01-2-2V5a2 2 0 012-2z"/></svg>
                    <?= (int)$car['bags'] ?> Bags
                </span>
            </div>
            <div class="flex justify-between text-sm text-gray-500 mb-3">
                <span><?= htmlspecialchars($car['gear']) ?></span>
                <span><?= htmlspecialchars($car['fuel']) ?></span>
            </div>
            <div class="border-t pt-3">
                <div class="flex justify-between items-center mb-2">
                    <div>
                        <span class="text-2xl font-bold text-gold">MAD<?= number_format((float)$car['price_day']) ?></span>
                        <span class="text-sm text-gray-500">/day</span>
                    </div>
                    <a href="car-detail.php?id=<?= (int)$car['id'] ?>"
                       class="bg-gold hover:bg-gold-dark text-white text-sm font-medium py-2 px-4 rounded-lg transition">
                        View Details
                    </a>
                </div>
                <div class="text-xs text-gray-400">
                    Week: MAD<?= number_format((float)$car['price_week']) ?> |
                    Month: MAD<?= number_format((float)$car['price_month']) ?>
                </div>
            </div>
        </div>
    </div>
    <?php
    return ob_get_clean();
}

/* -------------------------------------------------
   3. AJAX Response
   ------------------------------------------------- */
if (isset($_GET['ajax']) && $_SERVER['REQUEST_METHOD'] === 'GET') {
    try {
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        $cars = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $html = '';
        foreach ($cars as $i => $c) {
            $html .= renderCarCard($c, $i);
        }

        header('Content-Type: application/json; charset=utf-8');
        echo json_encode(['html' => $html, 'count' => count($cars)]);
        exit;
    } catch (Throwable $e) {
        http_response_code(500);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode([
            'html' => '<p class="col-span-full text-center text-red-600">Server error.</p>',
            'count' => 0
        ]);
        exit;
    }
}

/* -------------------------------------------------
   4. Normal Page Load
   ------------------------------------------------- */
$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$cars = $stmt->fetchAll(PDO::FETCH_ASSOC);

include 'header.php';
?>

<!-- Hero Section -->
<section class="bg-gradient-to-br from-gold/10 to-white py-16" data-aos="fade-up" data-aos-duration="1000">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
        <h2 class="text-4xl md:text-5xl font-bold text-gray-900 mb-4">Rent Your Dream Car</h2>
        <p class="text-lg text-gray-600 mb-8 max-w-2xl mx-auto">Premium fleet, flexible plans, golden service.</p>
        <a href="#cars" class="bg-gold hover:bg-gold-dark text-white font-semibold py-3 px-8 rounded-full shadow-lg transition transform hover:scale-105">
            Browse Cars
        </a>
    </div>
</section>

<!-- Filters & Cars -->
<section id="cars" class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <!-- RESPONSIVE FILTER SECTION -->
    <div data-aos="fade-up" data-aos-delay="200" data-aos-duration="800"
         class="bg-white p-4 sm:p-6 rounded-xl shadow-sm mb-6">
        <form id="filter-form" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-3">
            <!-- Search -->
            <input type="text" id="search" placeholder="Search car..."
                   value="<?= htmlspecialchars($search) ?>"
                   class="col-span-1 sm:col-span-2 lg:col-span-1 p-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-gold focus:border-transparent text-sm">

            <!-- Gear -->
            <select id="gear" class="p-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-gold text-sm">
                <option value="">All Gears</option>
                <option value="Manual" <?= $gear === 'Manual' ? 'selected' : '' ?>>Manual</option>
                <option value="Automatic" <?= $gear === 'Automatic' ? 'selected' : '' ?>>Automatic</option>
            </select>

            <!-- Fuel -->
            <select id="fuel" class="p-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-gold text-sm">
                <option value="">All Fuels</option>
                <option value="Diesel" <?= $fuel === 'Diesel' ? 'selected' : '' ?>>Diesel</option>
                <option value="Petrol" <?= $fuel === 'Petrol' ? 'selected' : '' ?>>Petrol</option>
            </select>

            <!-- Sort -->
            <select id="sort" class="p-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-gold text-sm">
                <option value="low" <?= $sort === 'low' ? 'selected' : '' ?>>Low to High</option>
                <option value="high" <?= $sort === 'high' ? 'selected' : '' ?>>High to Low</option>
            </select>

            <!-- Clear Button -->
            <a href="index.php" 
               class="col-span-1 sm:col-span-2 lg:col-span-1 bg-gray-200 hover:bg-gray-300 text-gray-700 font-medium py-3 px-4 rounded-lg transition text-center text-sm flex items-center justify-center">
                Clear All
            </a>
        </form>
    </div>

    <!-- Results Count -->
    <p id="results-count" class="text-sm text-gray-600 mb-4">
        <?= count($cars) ?> car<?= count($cars) !== 1 ? 's' : '' ?> found
    </p>

    <!-- Cars Grid -->
    <div id="cars-container" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
        <?php foreach ($cars as $i => $c): ?>
            <?= renderCarCard($c, $i) ?>
        <?php endforeach; ?>
    </div>
</section>

<?php include 'footer.php'; ?>

<!-- AOS + CSS + JS -->
<link href="https://unpkg.com/aos@2.3.4/dist/aos.css" rel="stylesheet">
<script src="https://unpkg.com/aos@2.3.4/dist/aos.js"></script>

<style>
    .spinner {
        width: 40px;
        height: 40px;
        border: 4px solid #f3f3f3;
        border-top: 4px solid #ca9d5e;
        border-radius: 50%;
        animation: spin 1s linear infinite;
        margin: 40px auto;
    }
    @keyframes spin {
        to { transform: rotate(360deg); }
    }

    /* Ensure form doesn't overflow */
    #filter-form {
        display: grid;
        gap: 0.75rem;
    }
    @media (max-width: 640px) {
        #filter-form > * {
            width: 100%;
        }
    }
</style>

<script>
    AOS.init({ once: true, duration: 800, easing: 'ease-out-quart' });

    // Elements
    const els = {
        search: document.getElementById('search'),
        gear:   document.getElementById('gear'),
        fuel:   document.getElementById('fuel'),
        sort:   document.getElementById('sort')
    };
    const container = document.getElementById('cars-container');
    const countEl   = document.getElementById('results-count');

    let debounceTimer = null;
    let isLoading = false;

    const fetchCars = () => {
        if (isLoading) return;
        isLoading = true;

        clearTimeout(debounceTimer);
        debounceTimer = setTimeout(() => {
            const params = new URLSearchParams({
                search: els.search.value.trim(),
                gear:   els.gear.value,
                fuel:   els.fuel.value,
                sort:   els.sort.value,
                ajax:   1
            });

            const fallbackHTML = container.innerHTML;
            container.innerHTML = '<div class="col-span-full flex justify-center"><div class="spinner"></div></div>';

            fetch(`index.php?${params}`, {
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            })
            .then(r => {
                if (!r.ok) throw new Error('Network error');
                return r.json();
            })
            .then(data => {
                container.innerHTML = data.html || '<p class="col-span-full text-center text-gray-500">No cars found.</p>';
                countEl.textContent = `${data.count} car${data.count !== 1 ? 's' : ''} found`;
                AOS.refreshHard();
            })
            .catch(err => {
                console.error(err);
                container.innerHTML = fallbackHTML;
                countEl.textContent = `${container.querySelectorAll('[data-aos]').length} car${container.querySelectorAll('[data-aos]').length !== 1 ? 's' : ''} found`;
            })
            .finally(() => {
                isLoading = false;
            });
        }, 300);
    };

    // Event Listeners
    els.search.addEventListener('input', fetchCars);
    els.gear.addEventListener('change', fetchCars);
    els.fuel.addEventListener('change', fetchCars);
    els.sort.addEventListener('change', fetchCars);

    // Optional: Auto-run on load if URL has filters
    document.addEventListener('DOMContentLoaded', () => {
        const hasFilters = ['search', 'gear', 'fuel', 'sort'].some(p => 
            new URLSearchParams(window.location.search).has(p)
        );
        if (hasFilters) {
            countEl.textContent = `${container.children.length} car${container.children.length !== 1 ? 's' : ''} found`;
        }
    });
</script>

</body>
</html>