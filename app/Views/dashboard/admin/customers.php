<?php
$pageTitle = "Customers";
$pageHeader = "Customer Management";

$customers = [
    [
        'id' => 1,
        'name' => 'Andi Saputra',
        'email' => 'andi.saputra@gmail.com',
        'phone' => '081234567890',
        'vehicles' => ['Toyota Avanza 2020', 'Honda Beat 2021'],
        'service_count' => 5
    ],
    [
        'id' => 2,
        'name' => 'Rina Maharani',
        'email' => 'rina.maharani@yahoo.com',
        'phone' => '085678901234',
        'vehicles' => ['Suzuki Ertiga 2019'],
        'service_count' => 3
    ],
    [
        'id' => 3,
        'name' => 'Budi Santoso',
        'email' => 'budi.santoso@gmail.com',
        'phone' => '082112233445',
        'vehicles' => ['Daihatsu Xenia 2022', 'Yamaha NMAX 2020'],
        'service_count' => 7
    ],
];

ob_start();
?>

    <section class="p-4">
        <div class="flex items-center justify-between mb-6">
            <h1 class="text-2xl font-semibold text-gray-800">Customers</h1>
            <a href="new-customer.php"
               class="flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-sm">
                <i data-lucide="user-plus" class="w-4 h-4"></i> Add Customer
            </a>
        </div>

        <div class="overflow-x-auto bg-white rounded-xl shadow border">
            <table class="min-w-full text-sm">
                <thead class="bg-gray-100 text-gray-600 text-xs">
                <tr>
                    <th class="px-4 py-3 text-left">Name</th>
                    <th class="px-4 py-3 text-left hidden sm:table-cell">Email</th>
                    <th class="px-4 py-3 text-left hidden sm:table-cell">Phone</th>
                    <th class="px-4 py-3 text-left">Vehicles</th>
                    <th class="px-4 py-3 text-center">Services</th>
                    <th class="px-4 py-3 text-center">Actions</th>
                </tr>
                </thead>
                <tbody class="divide-y text-gray-700">
                <?php foreach ($customers as $customer): ?>
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-3 font-medium"> <?= $customer['name'] ?> </td>
                        <td class="px-4 py-3 hidden sm:table-cell"> <?= $customer['email'] ?> </td>
                        <td class="px-4 py-3 hidden sm:table-cell"> <?= $customer['phone'] ?> </td>
                        <td class="px-4 py-3">
                            <ul class="list-disc ml-5">
                                <?php foreach ($customer['vehicles'] as $v): ?>
                                    <li><?= $v ?></li>
                                <?php endforeach; ?>
                            </ul>
                        </td>
                        <td class="px-4 py-3 text-center"> <?= $customer['service_count'] ?> </td>
                        <td class="px-4 py-3 text-center">
                            <div class="flex justify-center gap-2">
                                <a href="customer-details.php?id=<?= $customer['id'] ?>"
                                   class="text-blue-600 hover:underline text-xs">View</a>
                                <a href="mailto:<?= $customer['email'] ?>"
                                   class="text-green-600 hover:underline text-xs">Email</a>
                                <a href="edit-customer.php?id=<?= $customer['id'] ?>"
                                   class="text-gray-600 hover:underline text-xs">Edit</a>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </section>

<?php
$content = ob_get_clean();
echo $content;
?>