<?php
$pageTitle = 'Dashboard - Bengkel Kita';
include_once __DIR__ . '/../../Components/head.php';

$currentPath = strtok($_SERVER['REQUEST_URI'], '?');

$menu = [
    ['icon' => 'layout-dashboard', 'label' => 'Dashboard', 'link' => '/dashboard'],
    ['icon' => 'clipboard-list', 'label' => 'Service Requests', 'link' => '/dashboard/service-requests'],
    ['icon' => 'users', 'label' => 'Customers', 'link' => '/dashboard/customers'],
    ['icon' => 'boxes', 'label' => 'Inventory', 'link' => '/dashboard/inventory'],
    ['icon' => 'settings', 'label' => 'Technicians', 'link' => '/dashboard/technicians'],
    ['icon' => 'wrench', 'label' => 'Services', 'link' => '/dashboard/services'],
    ['icon' => 'bar-chart-2', 'label' => 'Analytics', 'link' => '/dashboard/analytics']
];

function isActive(string $link, string $current): bool
{
    return rtrim($link, '/') === rtrim($current, '/');
}

?>
<body class="bg-gray-50 font-sans">
<div x-data="{ sidebarOpen: false }" class="flex h-screen overflow-hidden">

    <div x-show="sidebarOpen" @click="sidebarOpen = false" @keydown.escape.window="sidebarOpen = false"
         class="fixed inset-0 bg-gray-600 bg-opacity-50 z-30 md:hidden transition-opacity duration-300" x-cloak></div>

    <aside :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'"
           class="fixed inset-y-0 left-0 z-40 w-64 bg-white border-r shadow-sm transform md:translate-x-0 md:static md:inset-0 transition-transform duration-300 ease-in-out">
        <div class="flex items-center justify-between h-16 border-b px-6">
                        <span class="text-lg font-semibold text-primary-700 flex items-center gap-2">
                            <i data-lucide="car" class="w-6 h-6"></i>
                            Bengkel Kita
                        </span>
            <button @click="sidebarOpen = false" class="text-gray-500 hover:text-gray-700 md:hidden">
                <i data-lucide="x" class="w-5 h-5"></i>
            </button>
        </div>

        <nav class="px-4 py-6 space-y-2">
            <?php foreach ($menu as $item): ?>
                <?php $active = isActive($item['link'], $currentPath); ?>
                <a href="<?= $item['link'] ?>" @click="window.innerWidth < 768 && (sidebarOpen = false)"
                   class="flex items-center gap-3 px-3 py-2 text-sm rounded-lg <?= $active ? 'bg-primary-100 text-primary-700' : 'text-gray-700 hover:bg-primary-100 hover:text-primary-700' ?>">
                    <i data-lucide="<?= $item['icon'] ?>" class="w-5 h-5"></i>
                    <?= $item['label'] ?>
                </a>
            <?php endforeach; ?>
        </nav>
    </aside>

    <div class="flex flex-col flex-1 w-0">
        <header class="flex items-center justify-between h-16 bg-white border-b p-6">
            <button @click="sidebarOpen = !sidebarOpen" class="text-gray-600 md:hidden">
                <i data-lucide="menu" class="w-6 h-6"></i>
            </button>
            <div x-data="{ dropdownOpen: false }" class="relative">
                <button @click="dropdownOpen = !dropdownOpen"
                        class="flex items-center gap-2 text-gray-700 hover:text-primary-600">
                    <span><?= $_SESSION['user_name'] ?? 'User' ?></span>
                    <i data-lucide="user-circle" class="w-6 h-6"></i>
                </button>
                <div x-show="dropdownOpen" @click.away="dropdownOpen = false" x-transition x-cloak
                     class="absolute right-0 mt-2 w-48 bg-white border rounded-md shadow-lg py-1 z-50">
                    <a href="profile.php" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Profile</a>
                    <a href="settings.php" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Settings</a>
                    <a href="logout.php" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Logout</a>
                </div>
            </div>
        </header>

        <main class="flex-1 space-y-6 overflow-y-auto bg-gray-100 py-8 px-4 sm:px-6 lg:px-8 font-sans antialiased text-gray-900">
            <?php if (isset($pageHeader)): ?>
                <div class="flex items-center justify-between mb-6">
                    <h1 class="text-2xl font-semibold text-gray-900"><?= $pageHeader ?></h1>
                    <?= $headerButton ?? '' ?>
                </div>
            <?php endif; ?>
            {{content}}
        </main>
    </div>
</div>

<script>
    lucide.createIcons();
</script>
</body>