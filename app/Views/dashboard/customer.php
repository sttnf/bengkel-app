<?php

$pageTitle = "Dasbor";
$pageHeader = "Dasbor";

$activeServices = $active_requests ?? [];
$serviceHistory = $history_requests ?? [];
$vehicles = $vehicles ?? [];

ob_start();
?>

    <div class="bg-gray-100 min-h-screen py-8 px-4 sm:px-6 lg:px-8 font-sans antialiased text-gray-900">
        <div class="max-w-6xl mx-auto">
            <header class="py-4 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2">
                <h1 class="text-2xl font-semibold tracking-tight"><?= $pageHeader ?></h1>
                <p class="text-gray-500">Selamat datang kembali, <?= htmlspecialchars($_SESSION['user_name']) ?> 👋</p>
            </header>

            <main class="mt-6 grid grid-cols-1 md:grid-cols-2 gap-6">

                <?php if (!empty($vehicles)): ?>
                    <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                        <div class="p-4 sm:p-6">
                            <h2 class="text-lg font-medium text-gray-900 mb-3 flex items-center space-x-2">
                                <i data-lucide="car" class="w-5 h-5"></i>
                                <span>Kendaraan Anda</span>
                            </h2>
                            <ul class="space-y-2">
                                <?php foreach ($vehicles as $vehicle): ?>
                                    <li class="bg-gray-50 rounded-lg border border-gray-200 p-3 flex items-center justify-between text-sm">
                                        <div>
                                            <h3 class="font-semibold text-gray-800"><?= htmlspecialchars($vehicle['brand']) ?> <?= htmlspecialchars($vehicle['model']) ?></h3>
                                            <p class="text-gray-500 text-xs"><?= htmlspecialchars($vehicle['year']) ?>
                                                | <?= htmlspecialchars($vehicle['license_plate']) ?></p>
                                        </div>
                                        <div class="flex flex-shrink-0 ml-2 space-x-1">
                                            <a href="/service?vehicle_id=<?= $vehicle['id'] ?>"
                                               class="inline-flex items-center px-2 py-1 font-medium text-primary-600 bg-primary-100 rounded-lg hover:bg-primary-200 text-xs">
                                                <i data-lucide="wrench" class="w-4 h-4 mr-1"></i>
                                            </a>
                                            <a href="edit-kendaraan.php?id=<?= $vehicle['id'] ?>"
                                               class="inline-flex items-center px-2 py-1 font-medium text-gray-600 bg-gray-100 rounded-lg hover:bg-gray-200 text-xs">
                                                <i data-lucide="edit" class="w-4 h-4"></i>
                                            </a>
                                            <a href="hapus-kendaraan.php?id=<?= $vehicle['id'] ?>"
                                               class="inline-flex items-center px-2 py-1 font-medium text-red-600 bg-red-100 rounded-lg hover:bg-red-200 text-xs">
                                                <i data-lucide="trash-2" class="w-4 h-4"></i>
                                            </a>
                                        </div>
                                    </li>
                                <?php endforeach; ?>
                            </ul>
                            <?php if (count($vehicles) > 3): ?>
                                <div class="mt-3">
                                    <a href="kendaraan-saya.php" class="text-primary-600 hover:underline text-sm">Lihat
                                        Semua</a>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endif; ?>

                <?php if (!empty($activeServices)): ?>
                    <div class="bg-white rounded-lg shadow-sm border border-gray-200 md:col-span-2 lg:col-span-1">
                        <div class="p-4 sm:p-6">
                            <div class="flex items-center justify-between mb-3">
                                <h2 class="text-lg font-medium text-gray-900 flex items-center space-x-2">
                                    <i data-lucide="activity" class="w-5 h-5"></i>
                                    <span>Servis Aktif</span>
                                    <span class="inline-block py-1 px-2 rounded-full text-xs font-semibold bg-primary-100 text-primary-600 ml-2"><?= count($activeServices) ?></span>
                                </h2>
                            </div>
                            <ul class="space-y-4">
                                <?php foreach ($activeServices as $service): ?>
                                    <?php
                                    $statusColor = match ($service['status']) {
                                        'pending' => 'yellow',
                                        'in_progress' => 'blue',
                                        'completed' => 'green',
                                        default => 'gray',
                                    };
                                    ?>
                                    <li class="bg-gray-50 rounded-lg border border-gray-200 p-4">
                                        <div class="flex justify-between items-center mb-2">
                                            <h3 class="font-semibold text-gray-800"><?= htmlspecialchars($service['service_name']) ?></h3>
                                            <span class="inline-block px-2 py-1 text-xs font-medium rounded-full bg-<?= $statusColor ?>-100 text-<?= $statusColor ?>-600">
                                                <?= ucfirst(str_replace('_', ' ', $service['status'])) ?>
                                            </span>
                                        </div>
                                        <p class="text-gray-500 text-sm"><?= $service['vehicle_brand'] ?> <?= $service['vehicle_model'] ?>
                                            (<?= $service['vehicle_year'] ?>)</p>
                                        <p class="text-gray-600 text-xs">
                                            Dijadwalkan: <?= date('d M Y, H:i', strtotime($service['scheduled_datetime'])) ?></p>
                                        <?php if (!empty($service['mechanic_name'])): ?>
                                            <p class="text-gray-600 text-xs">
                                                Mekanik: <?= htmlspecialchars($service['mechanic_name']) ?></p>
                                        <?php endif; ?>
                                        <div class="mt-4 flex w-full ">
                                            <a href="/dashboard/customer/<?= empty($service['has_payment']) ? 'payment' : 'invoice' ?>?id=<?= $service['id'] ?>"
                                               class="w-full text-center justify-center inline-flex items-center px-3 py-2 text-sm font-medium text-white bg-<?= empty($service['has_payment']) ? 'red' : 'green' ?>-600 rounded-lg hover:bg-<?= empty($service['has_payment']) ? 'red' : 'green' ?>-700">
                                                <?= empty($service['has_payment']) ? 'Bayar' : 'Detail' ?>
                                                <i data-lucide="<?= empty($service['has_payment']) ? 'credit-card' : 'eye' ?>"
                                                   class="w-4 h-4 ml-1"></i>
                                            </a>
                                        </div>
                                    </li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    </div>
                <?php endif; ?>

                <?php if (!empty($serviceHistory)): ?>
                    <div class="bg-white rounded-lg shadow-sm border border-gray-200 md:col-span-2">
                        <div class="p-4 sm:p-6">
                            <div class="flex items-center justify-between mb-3">
                                <h2 class="text-lg font-medium text-gray-900 flex items-center space-x-2">
                                    <i data-lucide="history" class="w-5 h-5"></i>
                                    <span>Riwayat Servis</span>
                                    <span class="inline-block py-1 px-2 rounded-full text-xs font-semibold bg-gray-100 text-gray-600 ml-2"><?= count($serviceHistory) ?></span>
                                </h2>
                            </div>
                            <ul class="space-y-3">
                                <?php foreach ($serviceHistory as $item): ?>
                                    <li class="bg-gray-50 rounded-lg border border-gray-200 p-3">
                                        <div class="grid grid-cols-1 md:grid-cols-2 gap-y-2 md:gap-x-4 items-start">
                                            <div>
                                                <h3 class="font-semibold text-gray-800 text-sm"><?= htmlspecialchars($item['service_name']) ?></h3>
                                                <p class="text-gray-500 text-xs"><?= $item['vehicle_brand'] ?> <?= $item['vehicle_model'] ?>
                                                    (<?= $item['vehicle_year'] ?>)</p>
                                                <p class="text-gray-500 text-xs">Selesai
                                                    pada: <?= date('d M Y', strtotime($item['completion_datetime'] ?? $item['scheduled_datetime'])) ?></p>
                                            </div>
                                            <div class="md:text-right">
                                                <span class="inline-block px-2 py-1 text-xs font-medium rounded-full bg-gray-100 text-gray-600">
                                                    <?= ucfirst($item['status']) ?>
                                                </span>
                                                <?php if (empty($item['has_payment'])): ?>
                                                    <a href="/dashboard/customer/payment?id=<?= $item['id'] ?>"
                                                       class="inline-flex items-center mt-2 px-3 py-2 text-sm font-medium text-white bg-red-600 rounded-lg hover:bg-red-700">
                                                        Bayar <i data-lucide="credit-card"
                                                                 class="w-4 h-4 ml-1"></i>
                                                    </a>
                                                <?php elseif (!empty($item['has_payment'])): ?>
                                                    <a href="/dashboard/customer/invoice?id=<?= $item['id'] ?>"
                                                       class="inline-flex items-center mt-2 text-sm text-primary-600 hover:text-primary700">
                                                        Invoice <i data-lucide="file-text"
                                                                   class="w-4 h-4 ml-1"></i>
                                                    </a>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    </li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    </div>
                <?php endif; ?>

                <?php if (empty($vehicles) && empty($activeServices) && empty($serviceHistory)): ?>
                    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 md:col-span-2">
                        <p class="text-gray-600 italic text-center">Belum ada data yang ditampilkan.</p>
                    </div>
                <?php endif; ?>

            </main>
        </div>
    </div>

<?php
echo ob_get_clean();
?>