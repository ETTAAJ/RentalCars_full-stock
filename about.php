<?php include 'header.php'; ?>

<main class="max-w-7xl mx-auto px-4 py-16 text-center">
  <!-- Page Title -->
  <div data-aos="fade-up" data-aos-duration="800">
    <h1 class="text-4xl md:text-5xl font-bold text-gray-900 mb-6">About Us</h1>
    <p class="text-lg text-gray-600 max-w-3xl mx-auto mb-12">
      At GoldCar, we believe every journey should be exceptional. Founded with a passion for excellence, 
      we deliver premium car rental experiences that combine luxury, reliability, and unmatched customer care.
    </p>
  </div>

  <!-- Hero Image Section -->
  <div data-aos="fade-up" data-aos-delay="200" data-aos-duration="1000" class="mb-16 -mx-4 md:mx-0">
    <div class="relative overflow-hidden rounded-2xl shadow-2xl">
      <img 
        src="pub_img/GoldCar.png" 
        alt="GoldCar Premium Fleet - Luxury Car Rental Experience" 
        class="w-full h-96 md:h-[500px] object-cover transition-transform duration-700 hover:scale-105"
      >
      <div class="absolute inset-0 bg-gradient-to-t from-black/60 via-transparent to-transparent"></div>
      <div class="absolute bottom-0 left-0 right-0 p-8 text-white">
        <h2 class="text-2xl md:text-3xl font-bold mb-2">Driven by Excellence</h2>
        <p class="text-sm md:text-base opacity-90 max-w-2xl mx-auto">
          Over a decade of redefining luxury mobility with world-class vehicles and personalized service.
        </p>
      </div>
    </div>
  </div>

  <!-- Mission & Vision Grid -->
  <div class="grid md:grid-cols-2 gap-8 mb-16">
    <!-- Our Mission -->
    <div data-aos="fade-right" data-aos-delay="100" data-aos-duration="700"
         class="bg-white p-8 rounded-xl shadow-lg hover:shadow-xl transition transform hover:-translate-y-2">
      <div class="w-16 h-16 bg-gold rounded-full mx-auto mb-6 flex items-center justify-center animate-pulse">
        <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
        </svg>
      </div>
      <h3 class="font-bold text-xl mb-3 text-gray-800">Our Mission</h3>
      <p class="text-gray-600">
        To provide seamless, safe, and stylish transportation solutions that empower your travels — 
        whether for business, leisure, or adventure.
      </p>
    </div>

    <!-- Our Vision -->
    <div data-aos="fade-left" data-aos-delay="300" data-aos-duration="700"
         class="bg-white p-8 rounded-xl shadow-lg hover:shadow-xl transition transform hover:-translate-y-2">
      <div class="w-16 h-16 bg-gold rounded-full mx-auto mb-6 flex items-center justify-center animate-pulse">
        <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
        </svg>
      </div>
      <h3 class="font-bold text-xl mb-3 text-gray-800">Our Vision</h3>
      <p class="text-gray-600">
        To redefine car rental by setting the gold standard in service, innovation, and customer satisfaction worldwide.
      </p>
    </div>
  </div>

  <!-- Values Grid (3 Columns) -->
  <div class="grid md:grid-cols-3 gap-8">
    <!-- Excellence -->
    <div data-aos="zoom-in" data-aos-delay="100" data-aos-duration="600"
         class="bg-white p-8 rounded-xl shadow-lg hover:shadow-xl transition transform hover:-translate-y-2">
      <div class="w-16 h-16 bg-gold rounded-full mx-auto mb-4 flex items-center justify-center animate-pulse">
        <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"/>
        </svg>
      </div>
      <h3 class="font-bold text-xl mb-2 text-gray-800">Excellence</h3>
      <p class="text-gray-600">We strive for perfection in every detail.</p>
    </div>

    <!-- Integrity -->
    <div data-aos="zoom-in" data-aos-delay="300" data-aos-duration="600"
         class="bg-white p-8 rounded-xl shadow-lg hover:shadow-xl transition transform hover:-translate-y-2">
      <div class="w-16 h-16 bg-gold rounded-full mx-auto mb-4 flex items-center justify-center animate-pulse">
        <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
        </svg>
      </div>
      <h3 class="font-bold text-xl mb-2 text-gray-800">Integrity</h3>
      <p class="text-gray-600">Honesty and transparency in all we do.</p>
    </div>

    <!-- Customer First -->
    <div data-aos="zoom-in" data-aos-delay="500" data-aos-duration="600"
         class="bg-white p-8 rounded-xl shadow-lg hover:shadow-xl transition transform hover:-translate-y-2">
      <div class="w-16 h-16 bg-gold rounded-full mx-auto mb-4 flex items-center justify-center animate-pulse">
        <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                d="M14 10h4.764a2 2 0 011.789 2.894l-3.5 7A2 2 0 0115.263 21h-4.017a2 2 0 01-1.789-1.106l-3.5-7a2 2 0 011.789-2.894H10"/>
        </svg>
      </div>
      <h3 class="font-bold text-xl mb-2 text-gray-800">Customer First</h3>
      <p class="text-gray-600">Your satisfaction is our top priority.</p>
    </div>
  </div>

  <!-- Call to Action -->
  <div data-aos="fade-up" data-aos-delay="700" data-aos-duration="800" class="mt-16">
    <a href="index.php" class="inline-block bg-gold text-white font-semibold px-8 py-4 rounded-full hover:bg-yellow-600 transition transform hover:scale-105 shadow-lg">
      Explore Our Fleet
    </a>
  </div>
</main>

<?php include 'footer.php'; ?>

<!-- AOS Animation Library -->
<link href="https://unpkg.com/aos@2.3.4/dist/aos.css" rel="stylesheet">
<script src="https://unpkg.com/aos@2.3.4/dist/aos.js"></script>
<script>
  AOS.init({
    once: true,
    duration: 800,
    easing: 'ease-out'
  });
</script>