<?php
$pageTitle = "Technicians - Auto Service Management";
$pageHeader = "Technicians";

$technicians = [
    ['id' => 'T-001', 'name' => 'Budi Santoso', 'specialty' => 'Mesin', 'status' => 'available'],
    ['id' => 'T-002', 'name' => 'Rina Setiawan', 'specialty' => 'Kelistrikan', 'status' => 'on_duty'],
    ['id' => 'T-003', 'name' => 'Andi Prasetyo', 'specialty' => 'Rem & Suspensi', 'status' => 'off_duty'],
];

function getTechnicianStatus($status)
{
    return match ($status) {
        'available' => '<span class="text-green-600 bg-green-100 px-2 py-1 text-xs rounded">Tersedia</span>',
        'on_duty' => '<span class="text-blue-600 bg-blue-100 px-2 py-1 text-xs rounded">Sedang Bertugas</span>',
        'off_duty' => '<span class="text-gray-600 bg-gray-100 px-2 py-1 text-xs rounded">Tidak Bertugas</span>',
        default => '<span class="text-gray-600 bg-gray-100 px-2 py-1 text-xs rounded">Tidak Diketahui</span>'
    };
}

ob_start();
?>

    <header >
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 ">
            <div>
                <h1 class="text-3xl font-bold tracking-tight text-gray-900"><?= $pageHeader ?></h1>
                <p class="mt-2 text-sm text-gray-600">
                    Kelola teknisi bengkel Anda dengan mudah. Lihat semua teknisi yang terdaftar, spesialisasi mereka,
                    dan
                    status terkini.
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
                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nama</th>
                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Spesialisasi</th>
                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Aksi</th>
            </tr>
            </thead>
            <tbody class="divide-y divide-gray-100 text-sm">
            <?php foreach ($technicians as $tech): ?>
                <tr>
                    <td class="px-4 py-3 font-mono text-gray-600"><?= $tech['id'] ?></td>
                    <td class="px-4 py-3 font-medium text-gray-800"><?= $tech['name'] ?></td>
                    <td class="px-4 py-3 text-gray-600"><?= $tech['specialty'] ?></td>
                    <td class="px-4 py-3"><?= getTechnicianStatus($tech['status']) ?></td>
                    <td class="px-4 py-3 text-right space-x-2">
                        <a href="view-technician.php?id=<?= $tech['id'] ?>"
                           class="text-blue-600 hover:underline text-xs">Lihat</a>
                        <a href="edit-technician.php?id=<?= $tech['id'] ?>"
                           class="text-indigo-600 hover:underline text-xs">Edit</a>
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