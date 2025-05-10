<?php
$pageTitle = "Dashboard - Auto Service Management";
$pageHeader = "Dashboard";

$serviceRequestStats = [
    'pending' => 12,
    'confirmed' => 8,
    'in_progress' => 15,
    'completed' => 45,
    'cancelled' => 3
];

$recentRequests = [
    ['id' => 1, 'customer' => 'John Smith', 'vehicle' => 'Toyota Camry (2020)', 'service' => 'Oil Change', 'date' => '2025-05-05', 'status' => 'pending'],
    ['id' => 2, 'customer' => 'Emma Johnson', 'vehicle' => 'Honda Civic (2019)', 'service' => 'Brake Replacement', 'date' => '2025-05-04', 'status' => 'in_progress'],
    ['id' => 3, 'customer' => 'Michael Brown', 'vehicle' => 'Ford F-150 (2021)', 'service' => 'Tire Rotation', 'date' => '2025-05-03', 'status' => 'completed'],
    ['id' => 4, 'customer' => 'Sophia Davis', 'vehicle' => 'Nissan Altima (2018)', 'service' => 'Engine Diagnostics', 'date' => '2025-05-03', 'status' => 'confirmed'],
    ['id' => 5, 'customer' => 'William Wilson', 'vehicle' => 'Chevrolet Malibu (2020)', 'service' => 'A/C Service', 'date' => '2025-05-02', 'status' => 'pending']
];

$upcomingServices = [
    ['id' => 6, 'customer' => 'Olivia Martinez', 'vehicle' => 'BMW 3 Series (2021)', 'service' => 'Full Inspection', 'date' => '2025-05-06', 'time' => '10:00'],
    ['id' => 7, 'customer' => 'James Taylor', 'vehicle' => 'Audi A4 (2019)', 'service' => 'Oil Change', 'date' => '2025-05-06', 'time' => '13:30'],
    ['id' => 8, 'customer' => 'Emily Anderson', 'vehicle' => 'Mercedes C-Class (2020)', 'service' => 'Brake Service', 'date' => '2025-05-07', 'time' => '09:15']
];

$lowStockItems = [
    ['id' => 1, 'name' => 'Oil Filter', 'current_stock' => 3, 'reorder_level' => 5],
    ['id' => 2, 'name' => 'Brake Pads (Front)', 'current_stock' => 2, 'reorder_level' => 5],
    ['id' => 3, 'name' => 'Wiper Blades', 'current_stock' => 4, 'reorder_level' => 10]
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
    ['link' => 'reports.php', 'icon' => 'chart-candlestick', 'label' => 'View Reports', 'color' => 'primary']
];

ob_start();
?>
<div class="grid grid-cols-1 md:grid-cols-4 gap-4">
    <?php foreach ($stats as $key => $meta): ?>
        <div class="bg-white rounded-2xl p-4 shadow-sm border">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500 font-medium"><?= $meta['label'] ?></p>
                    <h3 class="text-xl font-bold text-<?= $meta['color'] ?>-600"><?= $serviceRequestStats[$key] ?></h3>
                </div>
                <div class="p-2 rounded-full bg-<?= $meta['color'] ?>-100">
                    <i data-lucide="<?= strtolower($meta['icon']) ?>"
                       class="text-<?= $meta['color'] ?>-600 w-5 h-5"></i>
                </div>
            </div>
        </div>
    <?php endforeach; ?>
</div>

<div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
    <div class="rounded-2xl bg-white shadow-sm border">
        <div class="flex items-center justify-between px-4 py-3 border-b">
            <h2 class="text-base font-semibold text-gray-700">Recent Service Requests</h2>
            <a href="service-requests.php" class="text-sm text-primary-600 hover:underline">View All</a>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full text-sm text-left">
                <thead class="text-xs text-gray-400 uppercase">
                <tr>
                    <th class="px-4 py-2">Customer</th>
                    <th class="px-4 py-2">Service</th>
                    <th class="px-4 py-2">Date</th>
                    <th class="px-4 py-2">Status</th>
                </tr>
                </thead>
                <tbody class="text-gray-700">
                <?php foreach ($recentRequests as $req): ?>
                    <?php $status = $req['status'];
                    $color = $stats[$status]['color'] ?? 'gray'; ?>
                    <tr class="border-t">
                        <td class="px-4 py-2">
                            <div class="font-medium"><?= $req['customer'] ?></div>
                            <div class="text-xs text-gray-500"><?= $req['vehicle'] ?></div>
                        </td>
                        <td class="px-4 py-2"><?= $req['service'] ?></td>
                        <td class="px-4 py-2"><?= $req['date'] ?></td>
                        <td class="px-4 py-2">
                            <span class="inline-block px-2 py-1 rounded-full bg-<?= $color ?>-100 text-<?= $color ?>-700 text-xs font-medium">
                                <?= ucfirst($status) ?>
                            </span>
                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

    <div class="rounded-2xl bg-white shadow-sm border">
        <div class="px-4 py-3 border-b">
            <h2 class="text-base font-semibold text-gray-700">Upcoming Services</h2>
        </div>
        <div class="p-4 space-y-3">
            <?php foreach ($upcomingServices as $service): ?>
                <div class="p-3 bg-gray-50 border-l-4 border-primary-500 rounded">
                    <div class="flex justify-between text-sm">
                        <div class="text-gray-800 font-medium">
                            <?= $service['time'] ?> - <?= $service['service'] ?>
                        </div>
                        <div class="text-xs text-gray-400">#<?= $service['id'] ?></div>
                    </div>
                    <div class="mt-1 text-sm text-gray-500">
                        <p><?= $service['customer'] ?></p>
                        <p><?= $service['vehicle'] ?></p>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
    <div class="rounded-2xl bg-white shadow-sm border">
        <div class="flex items-center justify-between px-4 py-3 border-b">
            <h2 class="text-base font-semibold text-gray-700">Low Stock Items</h2>
            <a href="inventory.php" class="text-sm text-primary-600 hover:underline">View Inventory</a>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full text-sm text-left">
                <thead class="text-xs text-gray-400 uppercase">
                <tr>
                    <th class="px-4 py-2">Item</th>
                    <th class="px-4 py-2">Current</th>
                    <th class="px-4 py-2">Reorder</th>
                    <th class="px-4 py-2">Action</th>
                </tr>
                </thead>
                <tbody class="text-gray-700">
                <?php foreach ($lowStockItems as $item): ?>
                    <tr class="border-t">
                        <td class="px-4 py-2 font-medium"><?= $item['name'] ?></td>
                        <td class="px-4 py-2">
                            <span class="inline-block bg-rose-100 text-rose-700 px-2 py-1 rounded-full text-xs font-semibold">
                                <?= $item['current_stock'] ?>
                            </span>
                        </td>
                        <td class="px-4 py-2"><?= $item['reorder_level'] ?></td>
                        <td class="px-4 py-2">
                            <a href="inventory-order.php?id=<?= $item['id'] ?>"
                               class="text-primary-600 hover:underline text-sm">Reorder</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

    <div class="rounded-2xl bg-white shadow-sm border">
        <div class="px-4 py-3 border-b">
            <h2 class="text-base font-semibold text-gray-700">Quick Actions</h2>
        </div>
        <div class="grid grid-cols-2 gap-4 p-4">
            <?php foreach ($quickActions as $a): ?>
                <a href="<?= $a['link'] ?>"
                   class="flex flex-col items-center justify-center bg-gray-50 rounded-lg p-4 hover:bg-gray-100">
                    <div class="p-2 bg-<?= $a['color'] ?>-100 text-<?= $a['color'] ?>-600 rounded-full mb-2">
                        <i data-lucide="<?= strtolower($a['icon']) ?>" class="w-5 h-5"></i>
                    </div>
                    <span class="text-sm font-medium text-gray-700"><?= $a['label'] ?></span>
                </a>
            <?php endforeach; ?>
        </div>
    </div>
</div>
