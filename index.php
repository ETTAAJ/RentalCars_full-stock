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
   2. renderCarCard() – GLASS + RECT IMAGE + MAD BESIDE /day
   ------------------------------------------------- */
function renderCarCard($car, $index = 0): string
{
    $baseImg = !empty($car['image'])
        ? 'uploads/' . basename($car['image'])
        : 'https://via.placeholder.com/600x338/cccccc/999999?text=' . urlencode($car['name']);

    $cacheBuster = '';
    $fullPath = $_SERVER['DOCUMENT_ROOT'] . '/' . $baseImg;
    if (file_exists($fullPath)) {
        $cacheBuster = '?v=' . filemtime($fullPath);
    }

    $imgUrl = $baseImg . $cacheBuster;
    $delay  = 100 + ($index % 8) * 80;

    ob_start(); ?>
    <div data-aos="fade-up" data-aos-delay="<?= $delay ?>" data-aos-duration="700"
         class="group relative bg-white/80 backdrop-blur-sm rounded-3xl overflow-hidden shadow-lg hover:shadow-2xl 
                transition-all duration-500 transform hover:-translate-y-2 hover:scale-[1.02] 
                border border-white/30 flex flex-col h-full">

        <!-- RECTANGULAR RESPONSIVE IMAGE (16:9) -->
        <div class="relative w-full pt-[56.25%] bg-gray-100 overflow-hidden">
            <img src="<?= htmlspecialchars($imgUrl) ?>"
                 alt="<?= htmlspecialchars($car['name']) ?>"
                 class="absolute inset-0 w-full h-full object-cover object-center transition-transform duration-500 group-hover:scale-105"
                 onerror="this.onerror=null; this.src='https://via.placeholder.com/600x338/cccccc/999999?text=No+Image'; 
                          this.classList.add('object-contain','p-8');">
        </div>

        <!-- Card Body -->
        <div class="px-5 pb-5 sm:px-6 sm:pb-6 flex-1 flex flex-col">
            <h3 class="text-xl sm:text-2xl font-extrabold text-gray-800 mb-2 text-center line-clamp-1">
                <?= htmlspecialchars($car['name']) ?>
            </h3>

            <!-- Specs Icons -->
            <div class="flex justify-center gap-6 sm:gap-8 text-gray-600 mb-4 text-xs sm:text-sm">
                <div class="flex flex-col items-center">
                    <svg class="w-5 h-5 mb-1 text-gold" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z"/>
                    </svg>
                    <span class="font-medium"><?= (int)$car['seats'] ?> Seats</span>
                </div>
                <div class="flex flex-col items-center">
                    <svg class="w-5 h-5 mb-1 text-gold" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M5 3h10a2 2 0 012 2v10a2 2 0 01-2 2H5a2 2 0 01-2-2V5a2 2 0 012-2z"/>
                    </svg>
                    <span class="font-medium"><?= (int)$car['bags'] ?> Bags</span>
                </div>
            </div>

            <!-- Transmission & Fuel -->
            <div class="flex justify-center gap-4 text-xs text-gray-500 mb-5 font-medium">
                <span class="px-3 py-1 bg-gray-100 rounded-full"><?= htmlspecialchars($car['gear']) ?></span>
                <span class="px-3 py-1 bg-gray-100 rounded-full"><?= htmlspecialchars($car['fuel']) ?></span>
            </div>

            <!-- LUXURY PRICE – MAD BESIDE /day -->
            <div class="flex flex-col items-center mt-4 mb-3">
                <div class="flex items-baseline gap-2">
                    <span class="text-4xl sm:text-5xl font-extrabold text-gray-900">
                        <?= number_format((float)$car['price_day']) ?>
                    </span>
                    <span class="inline-flex items-center gap-1 px-3 py-1 text-xs font-semibold text-white bg-gradient-to-r from-gold to-yellow-600 rounded-full shadow-md animate-pulse">
                        <span>MAD</span>
                        <span>/day</span>
                    </span>
                </div>
                <div class="flex gap-3 mt-3 text-xs text-gray-500 font-medium">
                    <span class="px-3 py-1 bg-gray-100 rounded-full border border-gray-200">
                        Week: <strong class="text-gray-700">MAD<?= number_format((float)$car['price_week']) ?></strong>
                    </span>
                    <span class="px-3 py-1 bg-gray-100 rounded-full border border-gray-200">
                        Month: <strong class="text-gray-700">MAD<?= number_format((float)$car['price_month']) ?></strong>
                    </span>
                </div>
            </div>

            <!-- CTA Button -->
            <div class="mt-auto">
                <a href="car-detail.php?id=<?= (int)$car['id'] ?>"
                   class="block w-full text-center bg-gradient-to-r from-gold to-yellow-600 hover:from-yellow-600 hover:to-orange-500 
                          text-white font-bold py-3 px-6 rounded-2xl shadow-lg transition-all duration-300 
                          transform hover:scale-105 active:scale-95">
                    View Details
                </a>
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

    els.search.addEventListener('input', fetchCars);
    els.gear.addEventListener('change', fetchCars);
    els.fuel.addEventListener('change', fetchCars);
    els.sort.addEventListener('change', fetchCars);

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