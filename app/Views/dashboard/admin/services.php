<?php
$pageTitle = "Services - Auto Service Management";
$pageHeader = "Services";

$services = [
    ['id' => 'S-101', 'name' => 'Ganti Oli', 'description' => 'Ganti oli mesin standar', 'price' => 120000],
    ['id' => 'S-102', 'name' => 'Pemeriksaan Rem', 'description' => 'Cek kondisi sistem pengereman', 'price' => 80000],
    ['id' => 'S-103', 'name' => 'Rotasi Ban', 'description' => 'Rotasi posisi ban untuk merata', 'price' => 70000],
];

ob_start();
?>

<header >
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 ">
        <div>
            <h1 class="text-3xl font-bold tracking-tight text-gray-900"><?= $pageHeader ?></h1>
            <p class="mt-2 text-sm text-gray-600">
                Kelola layanan bengkel Anda dengan mudah. Lihat semua layanan yang tersedia, deskripsi, dan harga.
            </p>
        </div>

        <a href="add-inventory.php"
           class="inline-flex items-center px-4 py-2 text-sm bg-blue-600 text-white rounded hover:bg-blue-700 transition">
            <i data-lucide="plus-circle" class="w-4 h-4 mr-2"></i> Tambah Item
        </a>
    </div>
</header>

<div class="overflow-x-auto bg-white rounded-xl shadow-sm border">
    <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
        <tr>
            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">ID</th>
            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nama Layanan</th>
            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Deskripsi</th>
            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Harga</th>
            <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Aksi</th>
        </tr>
        </thead>
        <tbody class="divide-y divide-gray-100 text-sm">
        <?php foreach ($services as $service): ?>
            <tr>
                <td class="px-4 py-3 font-mono text-gray-600"><?= $service['id'] ?></td>
                <td class="px-4 py-3 font-medium text-gray-800"><?= $service['name'] ?></td>
                <td class="px-4 py-3 text-gray-600"><?= $service['description'] ?></td>
                <td class="px-4 py-3 text-gray-700">Rp <?= number_format($service['price'], 0, ',', '.') ?></td>
                <td class="px-4 py-3 text-right space-x-2">
                    <a href="view-service.php?id=<?= $service['id'] ?>" class="text-blue-600 hover:underline text-xs">Lihat</a>
                    <a href="edit-service.php?id=<?= $service['id'] ?>" class="text-indigo-600 hover:underline text-xs">Edit</a>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>

<?php
$content = ob_get_clean();
echo $content;
?>
