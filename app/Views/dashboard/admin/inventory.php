<?php
$pageTitle = "Inventory - Auto Service Management";
$pageHeader = "Inventory";

$inventoryItems = [
    ['id' => 'I-001', 'name' => 'Oli Mesin 10W-40', 'category' => 'Pelumas', 'quantity' => 25, 'unit' => 'Liter', 'status' => 'available'],
    ['id' => 'I-002', 'name' => 'Filter Udara', 'category' => 'Filter', 'quantity' => 8, 'unit' => 'pcs', 'status' => 'low_stock'],
    ['id' => 'I-003', 'name' => 'Kampas Rem Depan', 'category' => 'Rem', 'quantity' => 0, 'unit' => 'set', 'status' => 'out_of_stock'],
    ['id' => 'I-004', 'name' => 'Aki Basah 12V', 'category' => 'Listrik', 'quantity' => 12, 'unit' => 'pcs', 'status' => 'available'],
];

function getStatusBadge($status)
{
    return match ($status) {
        'available' => '<span class="text-green-600 bg-green-100 px-2 py-1 text-xs rounded">Tersedia</span>',
        'low_stock' => '<span class="text-amber-600 bg-amber-100 px-2 py-1 text-xs rounded">Stok Menipis</span>',
        'out_of_stock' => '<span class="text-red-600 bg-red-100 px-2 py-1 text-xs rounded">Habis</span>',
        default => '<span class="text-gray-600 bg-gray-100 px-2 py-1 text-xs rounded">Tidak Diketahui</span>'
    };
}

ob_start();
?>

<div class="flex justify-between items-center mb-4">
    <h2 class="text-lg font-semibold text-gray-700">Daftar Inventaris</h2>
    <a href="add-inventory.php"
       class="inline-flex items-center px-4 py-2 text-sm bg-blue-600 text-white rounded hover:bg-blue-700 transition">
        <i data-lucide="plus-circle" class="w-4 h-4 mr-2"></i> Tambah Item
    </a>
</div>

<div class="overflow-x-auto bg-white rounded-xl shadow-sm border">
    <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
        <tr>
            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">ID</th>
            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nama Barang</th>
            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Kategori</th>
            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Jumlah</th>
            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
            <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Aksi</th>
        </tr>
        </thead>
        <tbody class="divide-y divide-gray-100 text-sm">
        <?php foreach ($inventoryItems as $item): ?>
            <tr>
                <td class="px-4 py-3 font-mono text-gray-600"><?= $item['id'] ?></td>
                <td class="px-4 py-3 font-medium text-gray-800"><?= $item['name'] ?></td>
                <td class="px-4 py-3 text-gray-600"><?= $item['category'] ?></td>
                <td class="px-4 py-3 text-gray-600"><?= $item['quantity'] . ' ' . $item['unit'] ?></td>
                <td class="px-4 py-3"><?= getStatusBadge($item['status']) ?></td>
                <td class="px-4 py-3 text-right space-x-2">
                    <a href="view-inventory.php?id=<?= $item['id'] ?>" class="text-blue-600 hover:underline text-xs">Lihat</a>
                    <a href="edit-inventory.php?id=<?= $item['id'] ?>" class="text-indigo-600 hover:underline text-xs">Edit</a>
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
