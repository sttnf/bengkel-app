<?php

// Only start the session if one hasn't been started already
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
// Function to check if a user is logged in (based on your session variable)
function isLoggedIn(): bool
{
    return isset($_SESSION['user_id']); // Replace 'user_id' with your actual session key
}

// Function to generate the navigation links
function generateNavLinks(): string
{
    $navLinks = '';

    $navLinks .= '<a href="/#services" class="text-gray-500 hover:text-gray-900">Layanan</a>';
    $navLinks .= '<a href="/#testimonials" class="text-gray-500 hover:text-gray-900">Testimoni</a>';
    $navLinks .= '<a href="/#contact" class="text-gray-500 hover:text-gray-900">Kontak</a>';
    $navLinks .= '<a class="border border-blue-600 text-blue-600 px-4 py-2 rounded-lg hover:bg-blue-600 hover:text-white transition-colors" href="/service" data-discover="true">Service</a>';

    if (isLoggedIn()) {
        $navLinks .= '<a class="bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700 transition-colors" href="/dashboard" data-discover="true">Dashboard</a>';
    } else {
        $navLinks .= '<a class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition-colors" href="/login" data-discover="true">Login</a>';
    }

    return $navLinks;
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bengkel Kita - Perbaikan Kendaraan Terbaik</title>
    <link rel="canonical" href="https://bengkel.kita.blue">
    <link rel="icon" href="https://cdn.kita.blue/kita/logo.png">
    <meta name="description" content="Bengkel Kita adalah tempat perbaikan kendaraan terbaik di Indonesia">
    <meta property="og:title" content="Bengkel Kita - Perbaikan Kendaraan Terbaik">
    <meta property="og:description" content="Bengkel Kita adalah tempat perbaikan kendaraan terbaik di Indonesia">
    <meta property="og:image" content="https://cdn.kita.blue/kita/thumbnail.png">
    <meta property="og:type" content="website">
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="Bengkel Kita - Perbaikan Kendaraan Terbaik">
    <meta name="twitter:description" content="Bengkel Kita adalah tempat perbaikan kendaraan terbaik di Indonesia">
    <meta name="twitter:image" content="https://cdn.kita.blue/kita/thumbnail.png">
    <link rel="canonical" href="https://bengkel.kita.blue" data-rh="true">
    <link rel="icon" href="https://cdn.kita.blue/kita/logo.png" data-rh="true">
    <meta name="description" content="Bengkel Kita adalah tempat perbaikan kendaraan terbaik di Indonesia"
          data-rh="true">
    <meta property="og:title" content="Bengkel Kita - Perbaikan Kendaraan Terbaik" data-rh="true">
    <meta property="og:description" content="Bengkel Kita adalah tempat perbaikan kendaraan terbaik di Indonesia"
          data-rh="true">
    <meta property="og:image" content="https://cdn.kita.blue/kita/thumbnail.png" data-rh="true">
    <meta property="og:type" content="website" data-rh="true">
    <meta name="twitter:card" content="summary_large_image" data-rh="true">
    <meta name="twitter:title" content="Bengkel Kita - Perbaikan Kendaraan Terbaik" data-rh="true">
    <meta name="twitter:description" content="Bengkel Kita adalah tempat perbaikan kendaraan terbaik di Indonesia"
          data-rh="true">
    <meta name="twitter:image" content="https://cdn.kita.blue/kita/thumbnail.png" data-rh="true">
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 min-h-screen">
<nav class="sticky top-0 w-full bg-white shadow-sm z-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16"><a class="flex items-center" href="/" data-discover="true">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                     stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                     class="lucide lucide-car h-8 w-8 text-blue-600">
                    <path d="M19 17h2c.6 0 1-.4 1-1v-3c0-.9-.7-1.7-1.5-1.9C18.7 10.6 16 10 16 10s-1.3-1.4-2.2-2.3c-.5-.4-1.1-.7-1.8-.7H5c-.6 0-1.1.4-1.4.9l-1.4 2.9A3.7 3.7 0 0 0 2 12v4c0 .6.4 1 1 1h2"></path>
                    <circle cx="7" cy="17" r="2"></circle>
                    <path d="M9 17h6"></path>
                    <circle cx="17" cy="17" r="2"></circle>
                </svg>
                <span class="ml-2 text-xl font-bold text-gray-900">Bengkel Kita</span></a>
            <div class="flex items-center sm:hidden">
                <button class="text-gray-500 hover:text-gray-600">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                         fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                         stroke-linejoin="round" class="lucide lucide-menu h-6 w-6">
                        <line x1="4" x2="20" y1="12" y2="12"></line>
                        <line x1="4" x2="20" y1="6" y2="6"></line>
                        <line x1="4" x2="20" y1="18" y2="18"></line>
                    </svg>
                </button>
            </div>
            <div class="hidden sm:flex sm:items-center sm:space-x-4">
                <?php echo generateNavLinks(); ?>
            </div>
        </div>
    </div>
</nav>

<main class=" min-h-screen">
    {{content}}
</main>

<footer class="bg-gray-800">
    <div class="max-w-7xl mx-auto py-12 px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
            <div>
                <div class="flex items-center">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                         fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                         stroke-linejoin="round" class="lucide lucide-car h-8 w-8 text-blue-500">
                        <path d="M19 17h2c.6 0 1-.4 1-1v-3c0-.9-.7-1.7-1.5-1.9C18.7 10.6 16 10 16 10s-1.3-1.4-2.2-2.3c-.5-.4-1.1-.7-1.8-.7H5c-.6 0-1.1.4-1.4.9l-1.4 2.9A3.7 3.7 0 0 0 2 12v4c0 .6.4 1 1 1h2"></path>
                        <circle cx="7" cy="17" r="2"></circle>
                        <path d="M9 17h6"></path>
                        <circle cx="17" cy="17" r="2"></circle>
                    </svg>
                    <span class="ml-2 text-xl font-bold text-white">Bengkel Kita</span></div>
                <p class="mt-4 text-gray-400">Solusi terpercaya untuk perawatan kendaraan Anda</p></div>
            <div><h3 class="text-white font-semibold mb-4">Layanan</h3>
                <ul class="space-y-2">
                    <li><a href="#" class="text-gray-400 hover:text-white">Tune Up</a></li>
                    <li><a href="#" class="text-gray-400 hover:text-white">Servis Rem</a></li>
                    <li><a href="#" class="text-gray-400 hover:text-white">Servis AC</a></li>
                    <li><a href="#" class="text-gray-400 hover:text-white">Ganti Oli</a></li>
                </ul>
            </div>
            <div><h3 class="text-white font-semibold mb-4">Jam Operasional</h3>
                <ul class="space-y-2">
                    <li class="text-gray-400">Senin - Jumat: 08.00 - 17.00</li>
                    <li class="text-gray-400">Sabtu: 08.00 - 15.00</li>
                    <li class="text-gray-400">Minggu: Tutup</li>
                </ul>
            </div>
            <div><h3 class="text-white font-semibold mb-4">Ikuti Kami</h3>
                <div class="flex space-x-4"><a href="#" class="text-gray-400 hover:text-white">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                             fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                             stroke-linejoin="round" class="lucide lucide-facebook h-6 w-6">
                            <path d="M18 2h-3a5 5 0 0 0-5 5v3H7v4h3v8h4v-8h3l1-4h-4V7a1 1 0 0 1 1-1h3z"></path>
                        </svg>
                    </a><a href="#" class="text-gray-400 hover:text-white">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                             fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                             stroke-linejoin="round" class="lucide lucide-instagram h-6 w-6">
                            <rect width="20" height="20" x="2" y="2" rx="5" ry="5"></rect>
                            <path d="M16 11.37A4 4 0 1 1 12.63 8 4 4 0 0 1 16 11.37z"></path>
                            <line x1="17.5" x2="17.51" y1="6.5" y2="6.5"></line>
                        </svg>
                    </a><a href="#" class="text-gray-400 hover:text-white">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                             fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                             stroke-linejoin="round" class="lucide lucide-twitter h-6 w-6">
                            <path d="M22 4s-.7 2.1-2 3.4c1.6 10-9.4 17.3-18 11.6 2.2.1 4.4-.6 6-2C3 15.5.5 9.6 3 5c2.2 2.6 5.6 4.1 9 4-.9-4.2 4-6.6 7-3.8 1.1 0 3-1.2 3-1.2z"></path>
                        </svg>
                    </a></div>
            </div>
        </div>
        <div class="mt-8 pt-8 border-t border-gray-700"><p class="text-center text-gray-400">©
                <?= date('Y') ?>
                Bengkel Kita. All rights reserved.</p></div>
    </div>
</footer>
</body>
</html>