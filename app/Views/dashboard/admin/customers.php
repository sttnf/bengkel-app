<?php
$pageTitle = "Daftar Pelanggan";
$pageHeader = "Manajemen Pelanggan";

$customers = $customers ?? [];
?>

<header class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
    <div>
        <h1 class="text-3xl font-bold text-gray-900"><?= $pageHeader ?></h1>
        <p class="mt-2 text-sm text-gray-600">
            Teliti pelanggan yang telah mendaftar dan melakukan pemesanan di bengkel kita.
        </p>
    </div>
</header>

<div class="flex flex-col gap-4">
    <div class="bg-white rounded-lg shadow-sm border p-4">
        <div class="relative flex-grow">
            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                <i data-lucide="search" class="h-5 w-5 text-gray-400"></i>
            </div>
            <input type="text" id="searchInput"
                   class="block w-full pl-10 pr-3 py-2 border rounded-md placeholder-gray-500 focus:ring-primary-500 focus:border-primary-500 sm:text-sm"
                   placeholder="Cari pelanggan..." onkeyup="filterCustomers()">
        </div>
    </div>

    <?php if (!empty($customers)): ?>
        <section class="bg-white rounded-lg shadow-sm border overflow-hidden">
            <div class="px-6 py-4 border-b flex items-center">
                <i data-lucide="users" class="w-5 h-5 text-gray-500 mr-2"></i>
                <h2 class="text-lg font-semibold text-gray-900">Customers</h2>
                <span class="ml-2 px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                <?= count($customers) ?>
                            </span>
            </div>
            <ul id="customerList" class="divide-y divide-gray-200">
                <?php foreach ($customers as $customer): ?>
                    <li class="customer-item hover:bg-gray-50">
                        <div class="px-6 py-4 flex justify-between items-center">
                            <div>
                                <h3 class="text-base font-medium text-gray-900"><?= htmlspecialchars($customer['name'] ?? '') ?></h3>
                                <h2 class="text-xs text-gray-700"><?= htmlspecialchars($customer['email'] ?? '') ?></h2>
                                <div class="mt-1 text-sm text-gray-500"><?= htmlspecialchars($customer['phone_number'] ?? '') ?></div>
                            </div>
                            <a href="/dashboard/customer/detail/<?= $customer['id'] ?>"
                               class="inline-flex items-center h-8 px-3 text-sm font-medium text-gray-700 bg-gray-100 rounded-md hover:bg-gray-200">
                                Detail
                                <i data-lucide="arrow-right" class="ml-1 h-4 w-4"></i>
                            </a>
                        </div>
                    </li>
                <?php endforeach; ?>
            </ul>
        </section>
    <?php endif; ?>
</div>

<script>
    function filterCustomers() {
        const searchInput = document.getElementById('searchInput').value.toLowerCase();
        const customers = document.querySelectorAll('#customerList .customer-item');

        customers.forEach(customer => {
            const name = customer.querySelector('h3').textContent.toLowerCase();
            const email = customer.querySelector('h2').textContent.toLowerCase();
            const phone = customer.querySelector('.text-sm.text-gray-500').textContent.toLowerCase();

            customer.style.display = (name.includes(searchInput) || email.includes(searchInput) || phone.includes(searchInput)) ? "" : "none";
        });
    }
</script>