<?php
/**
 * Technicians Management Page
 *
 * Displays and manages technicians in the auto service system.
 */

// Configuration
$pageTitle = "Technicians - Auto Service Management";
$pageHeader = "Technicians";

// Data from Controller (Fallback to empty arrays)
$technicians = $technicians ?? [];
$categories = ['Mesin', 'Kelistrikan', 'Rem & Suspensi', 'Body & Paint', 'AC', 'Lainnya'];
$filterStatus = [
    'available' => 'Tersedia',
    'on_duty' => 'Sedang Bertugas',
    'off_duty' => 'Tidak Bertugas',
];
$pagination = $pagination ?? [];

// Pagination Defaults
$limit = $pagination['limit'] ?? 10;
$page = $pagination['page'] ?? 1;
$totalItems = $pagination['total'] ?? count($technicians);
$totalPages = ceil($totalItems / $limit);
$offset = ($page - 1) * $limit;

/**
 * Renders a status badge for a technician.
 *
 * @param string $status The technician's status.
 * @return string HTML for the status badge.
 */
function renderTechnicianStatusBadge(string $status): string
{
    $statusClasses = [
        'available' => 'bg-green-100 text-green-600',
        'on_duty' => 'bg-primary-100 text-primary-600', // Assuming 'primary' is defined
        'off_duty' => 'bg-gray-100 text-gray-600',
    ];

    $defaultClasses = 'bg-gray-100 text-gray-600';
    $classes = $statusClasses[$status] ?? $defaultClasses;

    return sprintf(
        '<span class="inline-flex items-center rounded-md px-2 py-1 text-xs font-medium %s">%s</span>',
        $classes,
        htmlspecialchars($filterStatus[$status] ?? 'Tidak Diketahui') // Use $filterStatus for display
    );
}

?>

<div x-data="{
    openModal: false,
    editItem: { id: '', name: '', specialization: '' },

    // Function to set the edit form data and open the modal
    setEditItem(item) {
        this.editItem = JSON.parse(JSON.stringify(item)); // Deep copy to avoid direct manipulation
        this.openModal = true;
    },

    // Function to reset the form and open the modal for adding a new item
    addItem() {
        this.editItem = { id: '', name: '', specialization: '' };
        this.openModal = true;
    },
}">
    <header class="mb-6">
        <div class="md:flex md:items-center md:justify-between gap-4">
            <div>
                <h1 class="text-3xl font-bold tracking-tight text-gray-900"><?= $pageHeader ?></h1>
                <p class="mt-2 text-sm text-gray-600">
                    Kelola teknisi bengkel Anda dengan mudah. Lihat semua teknisi yang terdaftar, spesialisasi mereka,
                    dan status terkini.
                </p>
            </div>
            <button @click="addItem()"
                    class="inline-flex items-center bg-primary-600 hover:bg-primary-700 text-white px-5 py-2.5 rounded-lg text-sm font-medium shadow-sm">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 mr-2" fill="none" viewBox="0 0 24 24"
                     stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                </svg>
                Tambah Teknisi
            </button>
        </div>
    </header>

    <form method="GET"
          class="bg-white p-6 rounded-xl shadow-sm border border-gray-100 mb-8 grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-5">
        <div class="relative">
            <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-gray-400" fill="none" viewBox="0 0 24 24"
                     stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round"
                          d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
            </div>
            <input type="text" name="search" placeholder="Cari berdasarkan nama"
                   value="<?= htmlspecialchars($_GET['search'] ?? '') ?>"
                   class="pl-10 w-full px-4 py-2.5 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-primary-200">
        </div>
        <select name="specialization"
                class="px-4 py-2.5 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-primary-200 appearance-none bg-no-repeat bg-right">
            <option value="">Semua Spesialisasi</option>
            <?php foreach ($categories as $category): ?>
                <option value="<?= $category ?>" <?= isset($_GET['specialization']) && $_GET['specialization'] === $category ? 'selected' : '' ?>>
                    <?= $category ?>
                </option>
            <?php endforeach; ?>
        </select>
        <select name="status"
                class="px-4 py-2.5 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-primary-200 appearance-none bg-no-repeat bg-right">
            <option value="">Semua Status</option>
            <?php foreach ($filterStatus as $key => $status): ?>
                <option value="<?= $key ?>" <?= isset($_GET['status']) && $_GET['status'] === $key ? 'selected' : '' ?>>
                    <?= $status ?>
                </option>
            <?php endforeach; ?>
        </select>
        <div class="flex gap-2">
            <input type="hidden" name="page" value="1">
            <input type="hidden" name="limit" value="<?= $limit ?>">
            <button type="submit"
                    class="flex-1 inline-flex items-center justify-center bg-primary-600 hover:bg-primary-700 text-white font-medium px-4 py-2.5 rounded-lg text-sm">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 mr-2" fill="none" viewBox="0 0 24 24"
                     stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round"
                          d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-2.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"/>
                </svg>
                Filter
            </button>
            <?php if (isset($_GET['search']) || isset($_GET['specialization']) || isset($_GET['status'])): ?>
                <a href="?"
                   class="inline-flex items-center justify-center bg-gray-100 hover:bg-gray-200 text-gray-700 font-medium px-4 py-2.5 rounded-lg text-sm">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 mr-2" fill="none" viewBox="0 0 24 24"
                         stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                    Reset
                </a>
            <?php endif; ?>
        </div>
    </form>

    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                <tr>
                    <th
                            class="px-4 sm:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        ID
                    </th>
                    <th
                            class="px-4 sm:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Nama
                    </th>
                    <th
                            class="px-4 sm:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Spesialisasi
                    </th>
                    <th
                            class="px-4 sm:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Status
                    </th>
                    <th
                            class="px-4 sm:px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Aksi
                    </th>
                </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                <?php if (empty($technicians)): ?>
                    <tr>
                        <td colspan="5" class="px-4 sm:px-6 py-8 text-center text-gray-500">
                            <div class="flex flex-col items-center justify-center">
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-12 h-12 text-gray-300 mb-2"
                                     fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                          d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                <p class="text-sm font-medium">Tidak ada teknisi ditemukan</p>
                                <p class="text-xs mt-1">Tambahkan teknisi baru untuk memulai</p>
                            </div>
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($technicians as $technician): ?>
                        <tr class="hover:bg-gray-50 transition duration-150">
                            <td class="px-4 sm:px-6 py-3 font-mono text-xs text-gray-500">
                                <?= $technician['id'] ?>
                            </td>
                            <td class="px-4 sm:px-6 py-3 font-medium text-gray-800">
                                <?= $technician['name'] ?>
                            </td>
                            <td class="px-4 sm:px-6 py-3 text-gray-600">
                                <?= $technician['specialization'] ?>
                            </td>
                            <td class="px-4 sm:px-6 py-3">
                                <?= renderTechnicianStatusBadge($technician['status']) ?>
                            </td>
                            <td class="px-4 sm:px-6 py-3 text-right">
                                <div class="flex items-center justify-end space-x-2 sm:space-x-3">

                                    <button
                                            @click="setEditItem(<?= htmlspecialchars(json_encode($technician), ENT_QUOTES, 'UTF-8') ?>)"
                                            title="Edit teknisi"
                                            class="text-primary-600 hover:text-primary-900 p-1.5 rounded-full hover:bg-primary-50">

                                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 24 24"
                                             fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                             stroke-linejoin="round">
                                            <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path>
                                            <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path>
                                        </svg>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
                </tbody>
            </table>
        </div>

        <div class="bg-white px-4 sm:px-6 py-4 border-t border-gray-200">
            <div class="md:flex md:items-center md:justify-between gap-4">
                <div class="text-sm text-gray-500 text-center md:text-left">
                    Menampilkan <span class="font-medium"><?= $offset + 1 ?></span> hingga
                    <span class="font-medium"><?= min($offset + $limit, $totalItems) ?></span> dari
                    <span class="font-medium"><?= $totalItems ?></span> teknisi
                </div>
                <div class="flex flex-wrap items-center justify-center gap-1 sm:gap-2">
                    <a href="?page=1&limit=<?= $limit ?>"
                       class="<?= ($page <= 1) ? 'pointer-events-none text-gray-400 bg-gray-50' : 'text-gray-700 hover:bg-gray-100' ?> px-2 sm:px-3 py-1.5 text-sm border border-gray-200 rounded flex items-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24"
                             stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/>
                        </svg>
                    </a>
                    <a href="?page=<?= max(1, $page - 1) ?>&limit=<?= $limit ?>"
                       class="<?= ($page <= 1) ? 'pointer-events-none text-gray-400 bg-gray-50' : 'text-gray-700 hover:bg-gray-100' ?> px-2 sm:px-3 py-1.5 text-sm border border-gray-200 rounded">
                        <span class="hidden sm:inline">Sebelumnya</span>
                        <span class="sm:hidden">&lt;</span>
                    </a>
                    <div class="hidden xs:flex flex-wrap items-center gap-1 sm:gap-2">
                        <?php
                        $startPage = max(1, min($page - 1, $totalPages - 2));
                        $endPage = min($totalPages, $startPage + 2);
                        for ($i = $startPage; $i <= $endPage; $i++):
                            ?>
                            <a href="?page=<?= $i ?>&limit=<?= $limit ?>"
                               class="<?= ($i == $page) ? 'bg-primary-100 text-primary-700 border-primary-300 font-medium' : 'text-gray-700 hover:bg-gray-100' ?> px-2.5 sm:px-3.5 py-1.5 text-sm border border-gray-200 rounded">
                                <?= $i ?>
                            </a>
                        <?php endfor; ?>
                    </div>
                    <span class="xs:hidden text-sm px-2.5 py-1.5">
                        <?= $page ?> / <?= $totalPages ?>
                    </span>
                    <a href="?page=<?= min($totalPages, $page + 1) ?>&limit=<?= $limit ?>"
                       class="<?= ($page >= $totalPages) ? 'pointer-events-none text-gray-400 bg-gray-50' : 'text-gray-700 hover:bg-gray-100' ?> px-2 sm:px-3 py-1.5 text-sm border border-gray-200 rounded">
                        <span class="hidden sm:inline">Berikutnya</span>
                        <span class="sm:hidden">&gt;</span>
                    </a>
                    <a href="?page=<?= $totalPages ?>&limit=<?= $limit ?>"
                       class="<?= ($page >= $totalPages) ? 'pointer-events-none text-gray-400 bg-gray-50' : 'text-gray-700 hover:bg-gray-100' ?> px-2 sm:px-3 py-1.5 text-sm border border-gray-200 rounded flex items-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24"
                             stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/>
                        </svg>
                    </a>
                    <div class="flex items-center ml-2 pl-2 sm:ml-4 sm:pl-4 border-l border-gray-200">
                        <span class="text-sm text-gray-500 mr-1 sm:mr-2 hidden xs:inline">Tampilkan:</span>
                        <select onchange="window.location = '?page=1&limit=' + this.value"
                                class="text-sm border border-gray-200 rounded px-1 sm:px-2 py-1.5 focus:outline-none focus:ring-1">
                            <?php foreach ([10, 25, 50, 100] as $value): ?>
                                <option value="<?= $value ?>" <?= $limit == $value ? 'selected' : '' ?>>
                                    <?= $value ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div x-show="openModal" x-transition
         class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50">
        <div class="bg-white rounded-lg p-6 w-full max-w-lg relative">
            <h2 class="text-lg font-semibold mb-4" x-text="editItem.id ? 'Edit Teknisi' : 'Tambah Teknisi'"></h2>
            <form action="save-technician.php" method="POST" class="space-y-4">
                <input type="hidden" name="id" :value="editItem.id ?? ''">
                <div>
                    <label class="block text-sm font-medium mb-1">Nama</label>
                    <input type="text" name="name" x-model="editItem.name" required
                           class="w-full border rounded px-3 py-2 text-sm">
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1">Spesialisasi</label>
                    <select name="specialization" x-model="editItem.specialization" required
                            class="w-full border rounded px-3 py-2 text-sm">
                        <option value="" disabled>Pilih Spesialisasi</option>
                        <?php foreach ($categories as $category): ?>
                            <option value="<?= $category ?>"
                                    :selected="editItem.specialization === '<?= $category ?>'">
                                <?= $category ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="flex justify-end space-x-2">
                    <button type="button" @click="openModal = false"
                            class="px-4 py-2 rounded border text-gray-700">Batal
                    </button>
                    <button type="submit"
                            class="px-4 py-2 rounded bg-primary-600 text-white hover:bg-primary-700">Simpan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
