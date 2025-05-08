<?php

$pageTitle = "Permintaan Servis Saya";
$pageHeader = "Permintaan Servis";

$activeRequests = $active_requests ?? [];
$availableTechnicians = $available_technicians ?? [];

$requestHistory = [
    ['id' => 89, 'service' => 'Inspeksi Lengkap', 'status' => 'completed', 'date' => '2025-04-25', 'vehicle' => 'Toyota Corolla 2020'],
    ['id' => 75, 'service' => 'Ganti Aki', 'status' => 'completed', 'date' => '2025-03-18', 'vehicle' => 'Honda Jazz 2021'],
];

$statusMap = [
    'pending' => ['label' => 'Pending', 'color' => 'yellow', 'icon' => 'clock'],
    'in_progress' => ['label' => 'Diproses', 'color' => 'blue', 'icon' => 'loader-2'],
    'completed' => ['label' => 'Selesai', 'color' => 'green', 'icon' => 'check-circle'],
    'cancelled' => ['label' => 'Dibatalkan', 'color' => 'red', 'icon' => 'x-circle'],
];

// Proses form assign atau ubah status
//if ($_SERVER['REQUEST_METHOD'] === 'POST') {
//    $id = $_POST['id'] ?? null;
//    foreach ($activeRequests as &$req) {
//        if ($req['id'] == $id) {
//            if (isset($_POST['assign_mechanic_id'])) {
//                $req['assigned_mechanic_id'] = (int)$_POST['assign_mechanic_id'];
//            }
//            if (isset($_POST['status']) && array_key_exists($_POST['status'], $statusMap)) {
//                $req['status'] = $_POST['status'];
//            }
//        }
//    }
//    unset($req);
//}
//?>


<head>
    <style>
        /* Custom focus styles for better accessibility */
        .focus-ring {
            @apply focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-1;
        }

        /* Custom transition for buttons */
        .btn-transition {
            @apply transition-all duration-150 ease-in-out;
        }

        /* Auto-expanding card styles */
        .action-content {
            max-height: 0;
            overflow: hidden;
            transition: max-height 0.3s ease-out, opacity 0.2s ease-out, transform 0.2s ease-out;
            opacity: 0;
            transform: translateY(-8px);
        }

        .action-content.expanded {
            max-height: 500px; /* Large enough to contain content */
            opacity: 1;
            transform: translateY(0);
        }

        /* Rotate chevron icon */
        .action-toggle .chevron-icon {
            transition: transform 0.2s ease-out;
        }

        .action-toggle.active .chevron-icon {
            transform: rotate(180deg);
        }

        /* Active button state */
        .action-toggle.active {
            @apply bg-primary-50 border-primary-200 text-primary-700;
        }

        /* Toast notification */
        .toast {
            position: fixed;
            bottom: 1rem;
            right: 1rem;
            transform: translateY(100%);
            opacity: 0;
            transition: transform 0.3s ease, opacity 0.3s ease;
        }

        .toast.show {
            transform: translateY(0);
            opacity: 1;
        }

        /* Prefers reduced motion */
        @media (prefers-reduced-motion: reduce) {
            .action-content, .action-toggle .chevron-icon, .toast {
                transition: none;
            }
        }

        /* Custom scrollbar */
        ::-webkit-scrollbar {
            width: 8px;
            height: 8px;
        }

        ::-webkit-scrollbar-track {
            background: #f1f1f1;
            border-radius: 4px;
        }

        ::-webkit-scrollbar-thumb {
            background: #c1c1c1;
            border-radius: 4px;
        }

        ::-webkit-scrollbar-thumb:hover {
            background: #a1a1a1;
        }
    </style>
</head>
<div class="max-w-5xl mx-auto">
    <!-- Header with navigation tabs -->
    <header class="mb-8">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-6">
            <div>
                <h1 class="text-3xl font-bold tracking-tight text-gray-900"><?= $pageHeader ?></h1>
                <p class="mt-2 text-sm text-gray-600">Kelola dan pantau status permintaan servis kendaraan Anda</p>
            </div>
            <div class="flex items-center gap-2">
                <a href="#"
                   class="inline-flex items-center justify-center h-10 px-4 py-2 text-sm font-medium text-white bg-primary-600 rounded-md hover:bg-primary-700 focus-ring btn-transition">
                    <i data-lucide="plus" class="w-4 h-4 mr-2"></i>
                    Buat Permintaan Baru
                </a>
            </div>
        </div>

        <div class="border-b border-gray-200">
            <nav class="-mb-px flex space-x-8" aria-label="Tabs">
                <a href="#"
                   class="border-primary-500 text-primary-600 whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">
                    Semua Permintaan
                </a>
                <a href="#"
                   class="border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">
                    Aktif
                </a>
                <a href="#"
                   class="border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">
                    Riwayat
                </a>
            </nav>
        </div>
    </header>

    <main class="space-y-8">
        <!-- Search and filter section -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4">
            <div class="flex flex-col sm:flex-row gap-4">
                <div class="relative flex-grow">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <i data-lucide="search" class="h-5 w-5 text-gray-400"></i>
                    </div>
                    <input type="text"
                           class="block w-full pl-10 pr-3 py-2 border border-gray-300 rounded-md leading-5 bg-white placeholder-gray-500 focus:outline-none focus:placeholder-gray-400 focus:ring-1 focus:ring-primary-500 focus:border-primary-500 sm:text-sm"
                           placeholder="Cari permintaan...">
                </div>
                <div class="flex gap-2">
                    <div class="relative">
                        <select class="block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-primary-500 focus:border-primary-500 sm:text-sm rounded-md">
                            <option>Semua Status</option>
                            <option>Pending</option>
                            <option>Diproses</option>
                            <option>Selesai</option>
                            <option>Dibatalkan</option>
                        </select>
                    </div>
                    <button type="button"
                            class="inline-flex items-center px-3 py-2 border border-gray-300 shadow-sm text-sm leading-4 font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500">
                        <i data-lucide="sliders" class="h-4 w-4"></i>
                    </button>
                </div>
            </div>
        </div>

        <?php if (!empty($activeRequests)): ?>
            <section class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200 flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <i data-lucide="activity" class="w-5 h-5 text-primary-500"></i>
                        <h2 class="text-lg font-semibold text-gray-900">Permintaan Aktif</h2>
                        <span class="ml-2 inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-primary-100 text-primary-800">
                                    <?= count($activeRequests) ?>
                                </span>
                    </div>
                </div>
                <ul class="divide-y divide-gray-200">
                    <?php foreach ($activeRequests as $req): ?>
                        <?php
                        $status = $statusMap[$req['status']];
                        $mechanicName = isset($req['technician_id']) ?
                            $availableTechnicians[$req['technician_id']]['name'] ?? null
                            : null;
                        $scheduledDate = date('d M Y', strtotime($req['scheduled_datetime']));
                        $scheduledTime = date('H:i', strtotime($req['scheduled_datetime']));
                        $requestId = $req['id'];
                        ?>
                        <li class="hover:bg-gray-50 transition-colors duration-150 action-card"
                            id="request-<?= $requestId ?>">
                            <div class="px-6 py-5">
                                <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-4">
                                    <!-- Request info -->
                                    <div class="flex-1 min-w-0">
                                        <div class="flex items-start">
                                            <div>
                                                <div class="flex items-center gap-2">
                                                    <h3 class="text-base font-medium text-gray-900">
                                                        <?= htmlspecialchars($req['service_name']) ?>
                                                    </h3>
                                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-<?= $status['color'] ?>-100 text-<?= $status['color'] ?>-800">
                                                                <i data-lucide="<?= $status['icon'] ?>"
                                                                   class="w-3 h-3 mr-1 <?= $status['icon'] === 'loader-2' ? 'animate-spin-slow' : '' ?>"></i>
                                                                <?= $status['label'] ?>
                                                            </span>
                                                </div>

                                                <?php if (isset($req['user_name'])): ?>
                                                    <p class="mt-1 text-sm text-gray-500">
                                                        <i data-lucide="user" class="inline-block w-3 h-3 mr-1"></i>
                                                        <?= htmlspecialchars($req['user_name']) ?>
                                                    </p>
                                                <?php endif; ?>

                                                <div class="mt-2 grid grid-cols-1 sm:grid-cols-2 gap-y-1 gap-x-4 text-sm text-gray-500">
                                                    <div class="flex items-center">
                                                        <i data-lucide="car"
                                                           class="flex-shrink-0 mr-1.5 h-4 w-4 text-gray-400"></i>
                                                        <span class="truncate"><?= htmlspecialchars($req['vehicle_brand']) ?> <?= htmlspecialchars($req['vehicle_model']) ?> (<?= htmlspecialchars($req['vehicle_year']) ?>)</span>
                                                    </div>
                                                    <div class="flex items-center">
                                                        <i data-lucide="tag"
                                                           class="flex-shrink-0 mr-1.5 h-4 w-4 text-gray-400"></i>
                                                        <span><?= htmlspecialchars($req['license_plate']) ?></span>
                                                    </div>
                                                    <div class="flex items-center">
                                                        <i data-lucide="calendar"
                                                           class="flex-shrink-0 mr-1.5 h-4 w-4 text-gray-400"></i>
                                                        <span><?= $scheduledDate ?> · <?= $scheduledTime ?></span>
                                                    </div>
                                                    <?php if ($mechanicName): ?>
                                                        <div class="flex items-center">
                                                            <i data-lucide="wrench"
                                                               class="flex-shrink-0 mr-1.5 h-4 w-4 text-gray-400"></i>
                                                            <span><?= htmlspecialchars($mechanicName) ?></span>
                                                        </div>
                                                    <?php endif; ?>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Action buttons -->
                                    <div class="flex flex-row sm:flex-col items-center sm:items-end gap-2 mt-2 sm:mt-0 w-full sm:w-auto justify-between">
                                        <button
                                                type="button"
                                                class="action-toggle inline-flex items-center justify-center h-9 px-3 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50 focus-ring btn-transition"
                                                aria-expanded="false"
                                                aria-controls="action-content-<?= $requestId ?>"
                                                data-request-id="<?= $requestId ?>"
                                        >
                                            <i data-lucide="settings" class="w-4 h-4 mr-1.5"></i>
                                            <span>Kelola</span>
                                            <i data-lucide="chevron-down" class="ml-1.5 h-4 w-4 chevron-icon"></i>
                                        </button>
                                    </div>
                                </div>

                                <!-- Expandable action content -->
                                <div id="action-content-<?= $requestId ?>"
                                     class="action-content mt-4 pt-4 border-t border-gray-100">
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                        <?php if (!in_array($req['status'], ['completed', 'cancelled'])): ?>
                                            <div class="space-y-4">
                                                <h4 class="text-sm font-medium text-gray-900">Kelola Permintaan</h4>

                                                <form method="POST"
                                                      class="space-y-4 bg-gray-50 p-4 rounded-lg border border-gray-200">
                                                    <input type="hidden" name="id" value="<?= $req['id'] ?>">

                                                    <?php if (!$mechanicName): ?>
                                                        <div>
                                                            <label for="assign_mechanic_<?= $req['id'] ?>"
                                                                   class="block text-sm font-medium text-gray-700 mb-1">
                                                                Pilih Mekanik
                                                            </label>
                                                            <div class="relative">
                                                                <select id="assign_mechanic_<?= $req['id'] ?>"
                                                                        name="assign_mechanic_id"
                                                                        class="block w-full px-4 py-2 rounded-md border-gray-300 text-sm focus:border-primary-500 focus:ring-primary-500">
                                                                    <option value="">Pilih Mekanik</option>
                                                                    <?php foreach ($availableTechnicians as $m): ?>
                                                                        <option value="<?= $m['id'] ?>"><?= htmlspecialchars($m['name']) ?></option>
                                                                    <?php endforeach; ?>
                                                                </select>
                                                            </div>
                                                        </div>
                                                    <?php endif; ?>

                                                    <div>
                                                        <label for="status_<?= $req['id'] ?>"
                                                               class="block text-sm font-medium text-gray-700 mb-1">
                                                            Update Status
                                                        </label>
                                                        <div class="relative">
                                                            <select id="status_<?= $req['id'] ?>" name="status"
                                                                    class="block w-full p-2 rounded-md border-gray-300 text-sm focus:border-primary-500 focus:ring-primary-500">
                                                                <?php foreach ($statusMap as $k => $info): ?>
                                                                    <option value="<?= $k ?>" <?= $k === $req['status'] ? 'selected' : '' ?>><?= $info['label'] ?></option>
                                                                <?php endforeach; ?>
                                                            </select>
                                                        </div>
                                                    </div>
                                                    <button type="submit" name="action"
                                                            value="update_status"
                                                            class="w-full inline-flex justify-center items-center h-9 px-3 text-sm font-medium text-white bg-primary-600 rounded-md hover:bg-primary-700 focus-ring btn-transition">
                                                        <i data-lucide="refresh-cw"
                                                           class="w-4 h-4 mr-1.5"></i>
                                                        Update Permintaan
                                                    </button>
                                                </form>
                                            </div>
                                        <?php endif; ?>

                                        <div class="space-y-4">
                                            <h4 class="text-sm font-medium text-gray-900">Tindakan Lainnya</h4>
                                            <div class="bg-gray-50 p-4 rounded-lg border border-gray-200 space-y-2">
                                                <a href="/dashboard/customer/request-detail.php?id=<?= $req['id'] ?>"
                                                   class="flex items-center justify-between w-full px-3 py-2 text-sm text-gray-700 hover:bg-white rounded-md btn-transition">
                                                            <span class="flex items-center gap-2">
                                                                <i data-lucide="file-text"
                                                                   class="h-4 w-4 text-gray-500"></i>
                                                                <span>Lihat Detail Lengkap</span>
                                                            </span>
                                                    <i data-lucide="arrow-right" class="h-4 w-4 text-gray-400"></i>
                                                </a>

                                                <a href="#"
                                                   class="flex items-center justify-between w-full px-3 py-2 text-sm text-gray-700 hover:bg-white rounded-md btn-transition">
                                                            <span class="flex items-center gap-2">
                                                                <i data-lucide="printer"
                                                                   class="h-4 w-4 text-gray-500"></i>
                                                                <span>Cetak Invoice</span>
                                                            </span>
                                                    <i data-lucide="arrow-right" class="h-4 w-4 text-gray-400"></i>
                                                </a>

                                                <?php if (!in_array($req['status'], ['completed', 'cancelled'])): ?>
                                                    <a href="#"
                                                       class="flex items-center justify-between w-full px-3 py-2 text-sm text-red-600 hover:bg-red-50 rounded-md btn-transition">
                                                                <span class="flex items-center gap-2">
                                                                    <i data-lucide="x-circle" class="h-4 w-4"></i>
                                                                    <span>Batalkan Permintaan</span>
                                                                </span>
                                                        <i data-lucide="arrow-right" class="h-4 w-4"></i>
                                                    </a>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </section>
        <?php endif; ?>

        <?php if (!empty($requestHistory)): ?>
            <section class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200 flex items-center">
                    <i data-lucide="history" class="w-5 h-5 text-gray-500 mr-2"></i>
                    <h2 class="text-lg font-semibold text-gray-900">Riwayat Permintaan</h2>
                    <span class="ml-2 inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                <?= count($requestHistory) ?>
                            </span>
                </div>
                <ul class="divide-y divide-gray-200">
                    <?php foreach ($requestHistory as $h): ?>
                        <?php $status = $statusMap[$h['status']]; ?>
                        <li class="hover:bg-gray-50 transition-colors duration-150">
                            <div class="px-6 py-4">
                                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                                    <div class="flex items-start">
                                        <div class="flex-shrink-0 mr-4">
                                            <div class="w-10 h-10 rounded-full bg-gray-100 flex items-center justify-center text-gray-500">
                                                <i data-lucide="<?= $status['icon'] ?>" class="w-5 h-5"></i>
                                            </div>
                                        </div>
                                        <div>
                                            <h3 class="text-base font-medium text-gray-900">
                                                <?= htmlspecialchars($h['service'] ?? $h['service_name'] ?? '') ?>
                                            </h3>
                                            <div class="mt-1 text-sm text-gray-500">
                                                <?= htmlspecialchars($h['vehicle'] ?? '') ?>
                                                <?php if (isset($h['date'])): ?>
                                                    <span class="mx-1">•</span>
                                                    <time datetime="<?= $h['date'] ?>"><?= date('d M Y', strtotime($h['date'])) ?></time>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="flex items-center gap-2 ml-14 sm:ml-0">
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-<?= $status['color'] ?>-100 text-<?= $status['color'] ?>-800">
                                                    <i data-lucide="<?= $status['icon'] ?>" class="w-3 h-3 mr-1"></i>
                                                    <?= $status['label'] ?>
                                                </span>
                                        <a href="/dashboard/customer/request-detail.php?id=<?= $h['id'] ?>"
                                           class="inline-flex items-center justify-center h-8 px-3 text-sm font-medium text-gray-700 bg-gray-100 rounded-md hover:bg-gray-200 focus-ring btn-transition">
                                            <span>Detail</span>
                                            <i data-lucide="arrow-right" class="ml-1 h-4 w-4"></i>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </section>
        <?php endif; ?>

        <?php if (empty($activeRequests) && empty($requestHistory)): ?>
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 px-6 py-12 text-center">
                <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-primary-100 text-primary-600 mb-6">
                    <i data-lucide="car" class="h-8 w-8"></i>
                </div>
                <h3 class="text-xl font-medium text-gray-900 mb-2">Belum ada permintaan servis</h3>
                <p class="text-sm text-gray-500 mb-6">Mulai dengan membuat permintaan servis baru untuk kendaraan
                    Anda</p>
                <a href="#"
                   class="inline-flex items-center justify-center h-10 px-4 text-sm font-medium text-white bg-primary-600 rounded-md hover:bg-primary-700 focus-ring btn-transition">
                    <i data-lucide="plus" class="w-4 h-4 mr-2"></i>
                    Buat Permintaan Baru
                </a>
            </div>
        <?php endif; ?>

        <!-- Pagination -->
        <?php if (!empty($activeRequests) || !empty($requestHistory)): ?>
            <div class="mt-6">
                <nav class="flex items-center justify-between border-t border-gray-200 px-4 sm:px-0 py-3"
                     aria-label="Pagination">
                    <div class="hidden sm:block">
                        <p class="text-sm text-gray-700">
                            Menampilkan <span class="font-medium">1</span> sampai <span
                                    class="font-medium">10</span> dari <span class="font-medium">20</span> hasil
                        </p>
                    </div>
                    <div class="flex flex-1 justify-between sm:justify-end">
                        <a href="#"
                           class="relative inline-flex items-center rounded-md bg-white px-3 py-2 text-sm font-medium text-gray-700 ring-1 ring-gray-300 hover:bg-gray-50 focus:z-20 focus:outline-offset-0">
                            Sebelumnya
                        </a>
                        <a href="#"
                           class="relative ml-3 inline-flex items-center rounded-md bg-white px-3 py-2 text-sm font-medium text-gray-700 ring-1 ring-gray-300 hover:bg-gray-50 focus:z-20 focus:outline-offset-0">
                            Selanjutnya
                        </a>
                    </div>
                </nav>
            </div>
        <?php endif; ?>
    </main>
</div>
</div>

<!-- Toast notification -->
<div id="toast" class="toast max-w-sm bg-white rounded-lg shadow-lg border border-gray-200 p-4">
    <div class="flex items-center">
        <div class="flex-shrink-0">
            <i data-lucide="check-circle" class="h-5 w-5 text-green-500"></i>
        </div>
        <div class="ml-3">
            <p class="text-sm font-medium text-gray-900" id="toast-message">
                Berhasil memperbarui status
            </p>
        </div>
        <div class="ml-auto pl-3">
            <div class="-mx-1.5 -my-1.5">
                <button type="button"
                        class="inline-flex rounded-md p-1.5 text-gray-500 hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500"
                        onclick="hideToast()">
                    <span class="sr-only">Tutup</span>
                    <i data-lucide="x" class="h-4 w-4"></i>
                </button>
            </div>
        </div>
    </div>
</div>

<script>
    // Handle expanding/collapsing action cards
    document.addEventListener('DOMContentLoaded', function () {
        // Get all action toggle buttons
        const actionToggles = document.querySelectorAll('.action-toggle');

        // Add click event listener to each toggle button
        actionToggles.forEach(toggle => {
            toggle.addEventListener('click', function () {
                const requestId = this.getAttribute('data-request-id');
                const contentEl = document.getElementById(`action-content-${requestId}`);

                // Close all other expanded cards first
                document.querySelectorAll('.action-content.expanded').forEach(content => {
                    if (content.id !== `action-content-${requestId}`) {
                        content.classList.remove('expanded');

                        // Find and update the associated toggle button
                        const otherToggle = document.querySelector(`[data-request-id="${content.id.replace('action-content-', '')}"]`);
                        if (otherToggle) {
                            otherToggle.classList.remove('active');
                            otherToggle.setAttribute('aria-expanded', 'false');
                        }
                    }
                });

                // Toggle the current card
                const isExpanded = contentEl.classList.contains('expanded');

                if (isExpanded) {
                    // Collapse
                    contentEl.classList.remove('expanded');
                    this.classList.remove('active');
                    this.setAttribute('aria-expanded', 'false');
                } else {
                    // Expand
                    contentEl.classList.add('expanded');
                    this.classList.add('active');
                    this.setAttribute('aria-expanded', 'true');

                    // Scroll into view if needed (with a small delay to allow animation)
                    setTimeout(() => {
                        const cardRect = document.getElementById(`request-${requestId}`).getBoundingClientRect();
                        const contentRect = contentEl.getBoundingClientRect();

                        // If the bottom of the expanded content is below viewport
                        if (contentRect.bottom > window.innerHeight) {
                            // Smooth scroll to show the full content
                            window.scrollBy({
                                top: Math.min(contentRect.bottom - window.innerHeight + 40, contentRect.height),
                                behavior: 'smooth'
                            });
                        }
                    }, 50);
                }
            });
        });

        // Add loading state to buttons when clicked
        const forms = document.querySelectorAll('form');
        forms.forEach(form => {
            form.addEventListener('submit', function (e) {
                // For demo purposes, prevent actual form submission
                // e.preventDefault();

                const buttons = this.querySelectorAll('button[type="submit"]');
                buttons.forEach(button => {
                    // Save original content
                    const originalContent = button.innerHTML;
                    // Replace with loading indicator
                    button.innerHTML = '<svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg> Processing...';
                    // Disable the button
                    button.disabled = true;
                    // Add loading class
                    button.classList.add('opacity-75');

                    // For demo purposes, show success toast after 1 second
                    setTimeout(() => {
                        showToast("Berhasil memperbarui permintaan");
                    }, 1000);
                });
            });
        });

        // Close expanded cards when clicking outside
        document.addEventListener('click', function (event) {
            if (!event.target.closest('.action-card')) {
                document.querySelectorAll('.action-content.expanded').forEach(content => {
                    content.classList.remove('expanded');

                    // Find and update the associated toggle button
                    const toggle = document.querySelector(`[data-request-id="${content.id.replace('action-content-', '')}"]`);
                    if (toggle) {
                        toggle.classList.remove('active');
                        toggle.setAttribute('aria-expanded', 'false');
                    }
                });
            }
        });

        // Handle keyboard accessibility
        actionToggles.forEach(toggle => {
            toggle.addEventListener('keydown', function (e) {
                if (e.key === 'Enter' || e.key === ' ') {
                    e.preventDefault();
                    this.click();
                } else if (e.key === 'Escape') {
                    const requestId = this.getAttribute('data-request-id');
                    const contentEl = document.getElementById(`action-content-${requestId}`);

                    if (contentEl.classList.contains('expanded')) {
                        contentEl.classList.remove('expanded');
                        this.classList.remove('active');
                        this.setAttribute('aria-expanded', 'false');
                    }
                }
            });
        });
    });

    // Toast functions
    function showToast(message) {
        const toast = document.getElementById('toast');
        const toastMessage = document.getElementById('toast-message');

        toastMessage.textContent = message;
        toast.classList.add('show');

        // Auto hide after 3 seconds
        setTimeout(() => {
            hideToast();
        }, 3000);
    }

    function hideToast() {
        const toast = document.getElementById('toast');
        toast.classList.remove('show');
    }
</script>