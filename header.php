<?php require 'config.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>GoldCar Rental</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <script>
    tailwind.config = {
      theme: {
        extend: {
          colors: { gold: '#FFD700', 'gold-dark': '#E6C200' }
        }
      }
    }
  </script>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
  <style>
    body { font-family: 'Inter', sans-serif; }
    .sidebar {
      transition: transform 0.3s ease-in-out;
    }
    .sidebar.open  { transform: translateX(0); }
    .sidebar.closed { transform: translateX(-100%); }
  </style>
</head>
<body class="bg-gray-50 text-gray-800">

  <!-- Mobile Sidebar -->
  <div id="mobile-sidebar"
       class="fixed inset-y-0 left-0 w-64 bg-white shadow-xl z-50 sidebar closed lg:hidden">
    <div class="p-6">
      <div class="flex justify-between items-center mb-8">
        <!-- Logo in Sidebar -->
        <a href="index.php" class="flex items-center space-x-2">
          <img src="pub_img/GoldCar.png" alt="GoldCar Logo" class="w-10 h-10 rounded-[50%]" >
          <span class="text-2xl font-bold text-gold">GoldCar</span>
        </a>
        <button id="close-sidebar" class="text-gray-600">
          <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M6 18L18 6M6 6l12 12"/>
          </svg>
        </button>
      </div>
      <nav class="space-y-4">
        <a href="index.php" class="block text-gray-700 hover:text-gold transition">Home</a>
        <a href="index.php#cars" class="block text-gray-700 hover:text-gold transition">Cars</a>
        <a href="about.php" class="block text-gray-700 hover:text-gold transition">About</a>
        <a href="contact.php" class="block text-gray-700 hover:text-gold transition">Contact</a>
      </nav>
    </div>
  </div>

  <!-- Overlay (click to close) -->
  <div id="sidebar-overlay"
       class="fixed inset-0 bg-black bg-opacity-50 z-40 hidden lg:hidden"></div>

  <!-- Header -->
  <header class="bg-white shadow-sm sticky top-0 z-30">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4 flex justify-between items-center">
      <!-- Logo (Desktop & Mobile) -->
      <a href="index.php" class="flex items-center space-x-2">
        <img src="pub_img/GoldCar.png" alt="GoldCar Logo" class="w-10 h-10 rounded-[50%]" >
        <span class="text-2xl font-bold text-gold hidden sm:inline">GoldCar</span>
      </a>

      <!-- Desktop Nav -->
      <nav class="hidden lg:flex space-x-8">
        <a href="index.php" class="text-gray-700 hover:text-gold transition">Home</a>
        <a href="index.php#cars" class="text-gray-700 hover:text-gold transition">Cars</a>
        <a href="about.php" class="text-gray-700 hover:text-gold transition">About</a>
        <a href="contact.php" class="text-gray-700 hover:text-gold transition">Contact</a>
      </nav>

      <!-- Mobile Hamburger -->
      <button id="open-sidebar" class="lg:hidden text-gold">
        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M4 6h16M4 12h16M4 18h16"/>
        </svg>
      </button>
    </div>
  </header>

  <!-- JavaScript: Sidebar Toggle -->
  <script>
    const sidebar     = document.getElementById('mobile-sidebar');
    const overlay     = document.getElementById('sidebar-overlay');
    const openBtn     = document.getElementById('open-sidebar');
    const closeBtn    = document.getElementById('close-sidebar');

    openBtn.addEventListener('click', () => {
      sidebar.classList.replace('closed', 'open');
      overlay.classList.remove('hidden');
    });

    const closeSidebar = () => {
      sidebar.classList.replace('open', 'closed');
      overlay.classList.add('hidden');
    };

    closeBtn.addEventListener('click', closeSidebar);
    overlay.addEventListener('click', closeSidebar);
  </script>