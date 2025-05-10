<?php
$pageTitle = "Daftar Pelanggan";
$pageHeader = "Manajemen Pelanggan";

$customers = $customers ?? []; // Assuming $customers is passed from the controller

?>


<!-- Header with navigation tabs -->
<header >
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 ">
        <div>
            <h1 class="text-3xl font-bold tracking-tight text-gray-900"><?= $pageHeader ?></h1>
            <p class="mt-2 text-sm text-gray-600">
                Teliti pelanggan yang telah mendaftar dan melakukan pemesanan di bengkel kita. Anda dapat melihat detail
            </p>
        </div>
    </div>
</header>

<div class="flex flex-col gap-4">

    <!-- Search and filter section -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4">
        <div class="flex flex-col sm:flex-row gap-4">
            <div class="relative flex-grow">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <i data-lucide="search" class="h-5 w-5 text-gray-400"></i>
                </div>
                <input type="text"
                       class="block w-full pl-10 pr-3 py-2 border border-gray-300 rounded-md leading-5 bg-white placeholder-gray-500 focus:outline-none focus:placeholder-gray-400 focus:ring-1 focus:ring-primary-500 focus:border-primary-500 sm:text-sm"
                       placeholder="Cari pelanggan...">
            </div>
        </div>
    </div>

    <?php if (!empty($customers)): ?>
        <section class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200 flex items-center">
                <i data-lucide="users" class="w-5 h-5 text-gray-500 mr-2"></i>
                <h2 class="text-lg font-semibold text-gray-900">Customers</h2>
                <span class="ml-2 inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                <?= count($customers) ?>
                            </span>
            </div>
            <ul class="divide-y divide-gray-200">
                <?php foreach ($customers as $customer): ?>
                    <li class="hover:bg-gray-50 transition-colors duration-150">
                        <div class="px-6 py-4">
                            <div class="flex justify-between items-center">
                                <div class="flex items-start">
                                    <div>
                                        <h3 class="text-base font-medium text-gray-900">
                                            <?= htmlspecialchars($customer['name'] ?? '') ?>
                                        </h3>
                                        <h2 class="text-xs text-gray-700">
                                            <?= htmlspecialchars($customer['email'] ?? '') ?>
                                        </h2>
                                        <div class="mt-1 text-sm text-gray-500">
                                                <span>
                                                    <?= htmlspecialchars($customer['phone_number'] ?? '') ?>
                                                </span>
                                        </div>
                                    </div>
                                </div>
                                <div class="flex items-center gap-2 ml-14 sm:ml-0">

                                    <a href="/dashboard/customer/request-detail.php?id=<?= $customer['id'] ?>"
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
</div>

