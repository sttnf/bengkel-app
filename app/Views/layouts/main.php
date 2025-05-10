<?php
if (session_status() === PHP_SESSION_NONE) session_start();

function isLoggedIn(): bool
{
    return isset($_SESSION['user_id']);
}

function currentUser(): array
{
    return $_SESSION['user'] ?? ['name' => 'Tamu'];
}

?>

<!DOCTYPE html>
<html lang="id">
<?php include_once __DIR__ . '/../../Components/head.php'; ?>

<body class="bg-white text-gray-800 font-sans">
<nav class="fixed w-full top-0 z-50 backdrop-blur-md bg-white/80 border-b border-gray-200 shadow-sm"
     x-data="{ menuOpen: false, userOpen: false }">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex justify-between items-center h-16">

        <!-- Logo -->
        <a href="/" class="flex items-center gap-2 text-xl font-bold text-primary-600">
            <svg class="w-7 h-7 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path d="M19 17h2c.6 0 1-.4 1-1v-3c0-.9-.7-1.7-1.5-1.9C18.7 10.6 16 10 16 10s-1.3-1.4-2.2-2.3c-.5-.4-1.1-.7-1.8-.7H5c-.6 0-1.1.4-1.4.9L2.2 10.8A3.7 3.7 0 0 0 2 12v4c0 .6.4 1 1 1h2"
                      stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
            <span>Bengkel Kita</span>
        </a>

        <!-- Desktop Navigation -->
        <div class="hidden sm:flex items-center gap-6">
            <a href="/#services" class="text-gray-700 hover:text-primary-600 transition font-medium">Layanan</a>
            <a href="/#testimonials" class="text-gray-700 hover:text-primary-600 transition font-medium">Testimoni</a>
            <a href="/#contact" class="text-gray-700 hover:text-primary-600 transition font-medium">Kontak</a>
            <a href="/service"
               class="px-4 py-2 border border-primary-600 text-primary-600 rounded-full hover:bg-primary-50 transition font-medium">Service</a>

            <?php if (isLoggedIn()): ?>
                <div class="relative" @click.away="userOpen = false">
                    <button @click="userOpen = !userOpen"
                            class="flex items-center gap-2 group hover:bg-gray-100 px-3 py-2 rounded-full transition">
                        <div class="w-8 h-8 rounded-full bg-primary-600 text-white flex items-center justify-center font-bold shadow">
                            <?= strtoupper(substr(currentUser()['name'], 0, 1)) ?>
                        </div>
                        <span class="text-sm font-medium text-gray-700"><?= htmlspecialchars(currentUser()['name']) ?></span>
                        <svg class="w-4 h-4 text-gray-500 group-hover:text-primary-600" fill="none"
                             stroke="currentColor" viewBox="0 0 24 24">
                            <path d="M19 9l-7 7-7-7" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                    </button>
                    <div x-show="userOpen" x-transition x-cloak
                         class="absolute right-0 mt-2 w-44 bg-white rounded-lg shadow-lg py-2 z-50">
                        <a href="/dashboard" class="block px-4 py-2 hover:bg-gray-100 text-sm">Dashboard</a>
                        <form action="/logout" method="POST">
                            <button type="submit" class="w-full text-left px-4 py-2 hover:bg-gray-100 text-sm">Logout
                            </button>
                        </form>
                    </div>
                </div>
            <?php else: ?>
                <a href="/login"
                   class="px-4 py-2 bg-primary-600 text-white rounded-full hover:bg-primary-700 transition font-medium">Login</a>
            <?php endif; ?>
        </div>

        <!-- Mobile Toggle -->
        <button @click="menuOpen = !menuOpen" class="sm:hidden text-gray-700 hover:text-primary-600 transition">
            <svg x-show="!menuOpen" class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path d="M4 6h16M4 12h16M4 18h16" stroke-width="2"/>
            </svg>
            <svg x-show="menuOpen" x-cloak class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path d="M6 18L18 6M6 6l12 12" stroke-width="2"/>
            </svg>
        </button>
    </div>

    <!-- Mobile Menu -->
    <div x-show="menuOpen" x-transition x-cloak class="sm:hidden px-4 pb-6 pt-2 space-y-3">
        <a href="/#services" class="block text-gray-700 hover:text-primary-600 font-medium">Layanan</a>
        <a href="/#testimonials" class="block text-gray-700 hover:text-primary-600 font-medium">Testimoni</a>
        <a href="/#contact" class="block text-gray-700 hover:text-primary-600 font-medium">Kontak</a>
        <a href="/service" class="block text-primary-600 font-semibold">Service</a>
        <?php if (isLoggedIn()): ?>
            <a href="/dashboard" class="block text-gray-700">Dashboard</a>
            <form action="/logout.php" method="POST">
                <button class="block text-left w-full text-gray-700">Logout</button>
            </form>
        <?php else: ?>
            <a href="/login" class="block text-gray-700 font-medium">Login</a>
        <?php endif; ?>
    </div>
</nav>


<!-- Content -->
<main class="min-h-[calc(100vh-160px)] py-10">
    {{content}}
</main>

<!-- Footer -->
<footer class="bg-gray-900 text-gray-400 text-sm">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10 grid grid-cols-1 md:grid-cols-4 gap-10">
        <div>
            <h2 class="text-white text-lg font-semibold mb-3">Bengkel Kita</h2>
            <p>Solusi terpercaya untuk perawatan dan perbaikan kendaraan Anda.</p>
        </div>
        <div>
            <h3 class="text-white font-medium mb-3">Layanan</h3>
            <ul class="space-y-2">
                <li><a href="#" class="hover:text-white">Tune Up</a></li>
                <li><a href="#" class="hover:text-white">Servis Rem</a></li>
                <li><a href="#" class="hover:text-white">Servis AC</a></li>
                <li><a href="#" class="hover:text-white">Ganti Oli</a></li>
            </ul>
        </div>
        <div>
            <h3 class="text-white font-medium mb-3">Jam Operasional</h3>
            <ul class="space-y-2">
                <li>Senin - Jumat: 08.00 - 17.00</li>
                <li>Sabtu: 08.00 - 15.00</li>
                <li>Minggu: Tutup</li>
            </ul>
        </div>
        <div>
            <h3 class="text-white font-medium mb-3">Ikuti Kami</h3>
            <div class="flex gap-3">
                <a href="#" class="hover:text-white">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path d="M18 2h-3a5 5 0 00-5 5v3H7v4h3v8h4v-8h3l1-4h-4V7a1 1 0 011-1h3z"/>
                    </svg>
                </a>
            </div>
        </div>
    </div>
</footer>

<?php include_once __DIR__ . '/../../Components/toast.php'; ?>
<?php include_once __DIR__ . '/../../Components/script.php'; ?>
</body>
</html>
