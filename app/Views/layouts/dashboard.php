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

<body class="bg-gray-100 font-sans text-gray-900">
<div x-data="{ sidebarOpen: false }" class="flex h-screen overflow-hidden">

    <!-- Overlay -->
    <div x-show="sidebarOpen" @click="sidebarOpen = false" class="fixed inset-0 bg-black/40 z-30 md:hidden"
         x-cloak></div>

    <!-- Sidebar -->
    <aside :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'"
           class="fixed z-40 inset-y-0 left-0 w-64 bg-white border-r shadow-lg transform transition-transform duration-300 md:translate-x-0 md:static">
        <div class="flex items-center justify-between h-16 px-6 border-b">
            <span class="text-xl font-bold text-primary-700 flex items-center gap-2">
                <i data-lucide="car" class="w-6 h-6"></i> Bengkel Kita
            </span>
            <button @click="sidebarOpen = false" class="md:hidden text-gray-500 hover:text-gray-700">
                <i data-lucide="x" class="w-5 h-5"></i>
            </button>
        </div>

        <nav class="flex flex-col gap-2 px-4 py-6">
            <?php foreach ($menu as $item): ?>
                <?php $active = isActive($item['link'], $currentPath); ?>
                <a href="<?= $item['link'] ?>" @click="sidebarOpen = false"
                   class="flex items-center gap-3 px-4 py-2 text-sm font-medium rounded-xl transition
                   <?= $active ? 'bg-primary-100 text-primary-700' : 'text-gray-700 hover:bg-gray-100 hover:text-primary-600' ?>">
                    <i data-lucide="<?= $item['icon'] ?>" class="w-5 h-5"></i>
                    <?= $item['label'] ?>
                </a>
            <?php endforeach; ?>
        </nav>
    </aside>

    <!-- Main content -->
    <div class="flex-1 flex flex-col">
        <!-- Top Bar -->
        <header class="flex items-center justify-between px-6 h-16 bg-white border-b shadow-sm md:justify-end">
            <button @click="sidebarOpen = !sidebarOpen" class="text-gray-600 md:hidden">
                <i data-lucide="menu" class="w-6 h-6"></i>
            </button>

            <div x-data="{ open: false }" class="relative">
                <button @click="open = !open"
                        class="flex items-center gap-2 text-sm font-medium text-gray-700 hover:text-primary-600">
                    <span><?= $_SESSION['user']["name"] ?? 'User' ?></span>
                    <i data-lucide="chevron-down" class="w-4 h-4"></i>
                </button>

                <div x-show="open" @click.away="open = false" x-transition x-cloak
                     class="absolute right-0 w-48 h-fit bg-white border rounded-lg shadow-md z-50 py-1">
                    <form action="/logout" method="POST" class="h">
                        <button type="submit"
                                class="flex w-full items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                            <i data-lucide="log-out" class="w-4 h-4 mr-2"></i>
                            Logout
                        </button>
                    </form>
                </div>
            </div>
        </header>

        <!-- Content Area -->
        <main class="flex-1 overflow-y-auto px-4 sm:px-6 lg:px-8 py-8 space-y-6">
            <?php if (isset($pageHeader)): ?>
                <div class="flex items-center justify-between mb-6">
                    <h1 class="text-2xl font-semibold"><?= $pageHeader ?></h1>
                    <?= $headerButton ?? '' ?>
                </div>
            <?php endif; ?>
            {{content}}
        </main>
    </div>
</div>

<?php
include_once __DIR__ . '/../../Components/toast.php';
require_once __DIR__ . '/../../Components/script.php';
?>
</body>
