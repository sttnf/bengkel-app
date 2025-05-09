<?php

$pageTitle = "Dashboard - Auto Service Management";
$pageHeader = "Dasbor";

$statuses = $statuses ?? [
    'completed' => 0,
    'pending' => 0,
    'in_progress' => 0,
    'cancelled' => 0
];

$recent_requests = $recent_requests ?? [];

$lowStockItems = $lower_stock_items ?? [];

$upcomingServices = [
    ['id' => 6, 'customer' => 'Olivia Martinez', 'vehicle' => 'BMW 3 Series (2021)', 'service' => 'Full Inspection', 'date' => '2025-05-06', 'time' => '10:00'],
    ['id' => 7, 'customer' => 'James Taylor', 'vehicle' => 'Audi A4 (2019)', 'service' => 'Oil Change', 'date' => '2025-05-06', 'time' => '13:30'],
    ['id' => 8, 'customer' => 'Emily Anderson', 'vehicle' => 'Mercedes C-Class (2020)', 'service' => 'Brake Service', 'date' => '2025-05-07', 'time' => '09:15']
];


$stats = [
    'pending' => ['label' => 'Pending', 'color' => 'amber', 'icon' => 'Clock'],
    'in_progress' => ['label' => 'In Progress', 'color' => 'blue', 'icon' => 'Wrench'],
    'completed' => ['label' => 'Completed', 'color' => 'emerald', 'icon' => 'Check'],
    'cancelled' => ['label' => 'Cancelled', 'color' => 'rose', 'icon' => 'X']
];

$quickActions = [
    ['link' => 'new-service-request.php', 'icon' => 'circle-plus', 'label' => 'New Request', 'color' => 'blue'],
    ['link' => 'new-customers.php', 'icon' => 'user-plus', 'label' => 'New Customer', 'color' => 'green'],
    ['link' => 'inventory-add.php', 'icon' => 'package-plus', 'label' => 'Add Inventory', 'color' => 'violet'],
    ['link' => 'reports.php', 'icon' => 'chart-candlestick', 'label' => 'View Reports', 'color' => 'indigo']
];

ob_start();
?>


<header class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2">
    <h1 class="text-2xl font-semibold text-gray-900 tracking-tight"><?= $pageHeader ?></h1>
    <p class="text-gray-500">Selamat datang kembali, <?= $_SESSION['user_name'] ?? 'User' ?> 👋</p>
</header>

<div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
    <div class="bg-white rounded-lg shadow-md overflow-hidden">
        <div class="px-4 py-5 sm:p-6">
            <h3 class="text-lg font-medium text-gray-900 mb-4">
                <i data-lucide="bar-chart-2" class="w-5 h-5 mr-2 inline-block"></i>
                Statistik
            </h3>
            <div class="grid grid-cols-2 gap-4">
                <?php foreach ($stats as $key => $meta): ?>
                    <div class="bg-gray-50 rounded-lg p-3 flex items-center justify-between text-sm">
                        <div>
                            <p class="text-gray-500 text-xs"><?= $meta['label'] ?></p>
                            <h3 class="font-semibold text-gray-800">
                                <?= $statuses[$key] ?? 0 ?>
                            </h3>
                        </div>
                        <div class="flex-shrink-0 ml-2">
                            <div class="inline-flex items-center justify-center w-8 h-8 rounded-full bg-<?= $meta['color'] ?>-100 text-<?= $meta['color'] ?>-600">
                                <i data-lucide="<?= strtolower($meta['icon']) ?>" class="w-4 h-4"></i>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-lg shadow-md overflow-hidden">
        <div class="px-4 py-5 sm:p-6">
            <h3 class="text-lg font-medium text-gray-900 mb-4">
                <i data-lucide="zap" class="w-5 h-5 mr-2 inline-block"></i>
                Aksi Cepat
            </h3>
            <div class="grid grid-cols-2 gap-4">
                <?php foreach ($quickActions as $action): ?>
                    <a href="<?= $action['link'] ?>"
                       class="bg-gray-50 rounded-lg p-3 flex items-center space-x-2 text-sm hover:bg-gray-100 transition-colors duration-150">
                        <div class="inline-flex items-center justify-center w-8 h-8 rounded-full bg-<?= $action['color'] ?>-100 text-<?= $action['color'] ?>-600">
                            <i data-lucide="<?= strtolower($action['icon']) ?>" class="w-4 h-4"></i>
                        </div>
                        <span class="font-medium text-gray-800"><?= $action['label'] ?></span>
                    </a>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</div>

<main class="grid grid-cols-1 lg:grid-cols-2 gap-6">


    <div class="bg-white rounded-lg shadow-md overflow-hidden">
        <div class="px-4 py-5 sm:p-6">
            <div class="flex items-center justify-between mb-3">
                <h3 class="text-lg font-medium text-gray-900">
                    <i data-lucide="list" class="w-5 h-5 mr-2 inline-block"></i>
                    Permintaan Servis Terbaru
                </h3>
                <a href="/dashboard/service-requests" class="text-blue-600 hover:underline text-sm">Lihat Semua</a>
            </div>
            <ul class="divide-y divide-gray-200">
                <?php foreach ($recent_requests as $request): ?>
                    <li class="py-4">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-y-2 md:gap-x-4 items-center">
                            <div>
                                <p class="text-sm font-semibold text-gray-800"><?= htmlspecialchars($request['service_name']) ?></p>
                                <p class="text-xs text-gray-500">
                                    Pelanggan: <?= htmlspecialchars($request['user_name']) ?></p>
                                <p class="text-xs text-gray-600"><?= htmlspecialchars($request['vehicle_brand']) ?> <?= htmlspecialchars($request['vehicle_model']) ?>
                                    (<?= htmlspecialchars($request['vehicle_year']) ?>)</p>
                                <p class="text-xs text-gray-600">
                                    Plat: <?= htmlspecialchars($request['license_plate']) ?></p>
                            </div>
                            <div class="md:text-right space-y-2">
                               <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
    bg-<?= isset($stats[$request['status']]) ? $stats[$request['status']]['color'] : 'gray' ?>-100
    text-<?= isset($stats[$request['status']]) ? $stats[$request['status']]['color'] : 'gray' ?>-800">
    <?= htmlspecialchars(ucfirst($request['status'])) ?>
</span>
                                <?php echo !empty($request['completion_datetime'])
                                    ? '<p class="text-xs text-gray-600">Selesai: ' . htmlspecialchars($request['completion_datetime']) . '</p>'
                                    : '<p class="text-xs text-gray-600">Dijadwalkan: ' . htmlspecialchars($request['scheduled_datetime']) . '</p>';
                                ?>
                                <a href="recentRequests-details.php?id=<?= $request['id'] ?>"
                                   class="inline-flex items-center px-3 py-2 text-xs font-medium text-blue-600 bg-blue-100 rounded-full hover:bg-blue-200">
                                    Lihat Detail
                                    <i data-lucide="arrow-right" class="w-4 h-4 ml-1"></i>
                                </a>
                            </div>
                        </div>
                    </li>
                <?php endforeach; ?>
                <?php if (empty($recent_requests)): ?>
                    <li class="py-3 text-gray-600 italic text-sm">Tidak ada data permintaan servis terbaru.</li>
                <?php endif; ?>
            </ul>
        </div>
    </div>

    <div class="bg-white rounded-lg shadow-md overflow-hidden">
        <div class="px-4 py-5 sm:p-6">
            <div class="flex items-center justify-between mb-3">
                <h3 class="text-lg font-medium text-gray-900">
                    <i data-lucide="package" class="w-5 h-5 mr-2 inline-block"></i>
                    Stok Rendah
                </h3>
                <a href="/dashboard/inventory" class="text-blue-600 hover:underline text-sm">Lihat Semua</a>
            </div>
            <ul class="divide-y divide-gray-200">
                <?php foreach ($lowStockItems as $item): ?>
                    <li class="py-3">
                        <div class="flex items-start justify-between">
                            <div>
                                <p class="text-sm font-semibold text-gray-800"><?= htmlspecialchars($item['name']) ?></p>
                                <p class="text-xs text-gray-500">Kategori: <?= htmlspecialchars($item['category']) ?> |
                                    Lokasi: <?= htmlspecialchars($item['location']) ?></p>
                                <p class="text-xs text-gray-500">Stok: <span
                                            class="text-red-600 font-medium"><?= $item['current_stock'] ?></span>
                                    (Minimal: <?= $item['reorder_level'] ?>)</p>
                                <p class="text-xs text-gray-500">
                                    Supplier: <?= htmlspecialchars($item['supplier']) ?></p>
                            </div>
                            <div class="ml-2 flex-shrink-0">
                                <a href="inventory-details.php?id=<?= $item['id'] ?>"
                                   class="inline-flex items-center px-3 py-1.5 text-xs font-medium text-blue-600 bg-blue-100 rounded-full hover:bg-blue-200">
                                    Lihat Detail
                                    <i data-lucide="arrow-right" class="w-4 h-4 ml-1"></i>
                                </a>
                            </div>
                        </div>
                    </li>
                <?php endforeach; ?>
                <?php if (empty($lowStockItems)): ?>
                    <li class="py-3 text-gray-600 italic text-sm">Tidak ada stok rendah saat ini.</li>
                <?php endif; ?>
            </ul>
        </div>
    </div>
</main>