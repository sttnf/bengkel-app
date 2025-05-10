<?php
$pageTitle = "Service Management";
$pageHeader = "Services";
$categories = ['Maintenance', 'Repair', 'Diagnostics', 'Bodywork', 'Electrical', 'Other'];
$services = $services ?? [];

// Initialize pagination variables with defaults if not provided
$pagination = $pagination ?? [];
$limit = $pagination['limit'] ?? 10;
$page = $pagination['page'] ?? 1;
$totalServices = $pagination['total'] ?? count($services);
$totalPages = $pagination['page_count'] ?? ceil($totalServices / $limit);
$offset = ($page - 1) * $limit;
?>

<div x-data="{
            openModal: false,
            editItem: {},
            setEditItem(item) {
                this.editItem = JSON.parse(JSON.stringify(item));
                this.openModal = true;
            }
        }">
    <!-- Header -->
    <header class="mb-8">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div>
                <h1 class="text-3xl font-bold text-gray-800"><?= $pageHeader ?></h1>
                <p class="mt-1 text-sm text-gray-500">Mengelola dan memantau penawaran layanan</p>
            </div>
            <button @click="openModal = true; editItem = {}"
                    class="bg-primary-600 hover:bg-primary-700 text-white px-5 py-2.5 rounded-lg text-sm font-medium flex items-center shadow-sm">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 mr-2" viewBox="0 0 24 24" fill="none"
                     stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <line x1="12" y1="5" x2="12" y2="19"></line>
                    <line x1="5" y1="12" x2="19" y2="12"></line>
                </svg>
                Tambah Layanan
            </button>
        </div>
    </header>

    <!-- Filters -->
    <form method="GET"
          class="bg-white p-6 rounded-xl shadow-sm border border-gray-100 mb-8 grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-5">
        <div class="relative">
            <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-gray-400" viewBox="0 0 24 24" fill="none"
                     stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <circle cx="11" cy="11" r="8"></circle>
                    <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
                </svg>
            </div>
            <input type="text" name="search" placeholder="Search service name"
                   value="<?= htmlspecialchars($_GET['search'] ?? '') ?>"
                   class="pl-10 w-full px-4 py-2.5 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-primary-200">
        </div>

        <select name="category"
                class="px-4 py-2.5 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-primary-200 appearance-none bg-no-repeat bg-right">
            <option value="">Semua Ketegori</option>
            <?php foreach ($categories as $c): ?>
                <option value="<?= $c ?>" <?= isset($_GET['category']) && $_GET['category'] === $c ? 'selected' : '' ?>><?= $c ?></option>
            <?php endforeach; ?>
        </select>

        <select name="est_time"
                class="px-4 py-2.5 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-primary-200 appearance-none bg-no-repeat bg-right">
            <option value="">Estimasi Waktu</option>
            <option value="1" <?= isset($_GET['est_time']) && $_GET['est_time'] == '1' ? 'selected' : '' ?>>Dibawah 1
                Jam
            </option>
            <option value="3" <?= isset($_GET['est_time']) && $_GET['est_time'] == '3' ? 'selected' : '' ?>>1-3 Jam
            </option>
            <option value="5" <?= isset($_GET['est_time']) && $_GET['est_time'] == '5' ? 'selected' : '' ?>>3-5 Jam
            </option>
            <option value="10" <?= isset($_GET['est_time']) && $_GET['est_time'] == '10' ? 'selected' : '' ?>>5+ Jam
            </option>
        </select>

        <input type="hidden" name="page" value="1">
        <input type="hidden" name="limit" value="<?= $limit ?>">

        <div class="flex gap-2">
            <button type="submit"
                    class="flex-1 bg-primary-600 hover:bg-primary-700 text-white font-medium px-4 py-2.5 rounded-lg text-sm flex items-center justify-center">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 mr-2" viewBox="0 0 24 24" fill="none"
                     stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <polygon points="22 3 2 3 10 12.46 10 19 14 21 14 12.46 22 3"></polygon>
                </svg>
                Filter
            </button>

            <?php if (isset($_GET['search']) || isset($_GET['category']) || isset($_GET['est_time'])): ?>
                <a href="?"
                   class="bg-gray-100 hover:bg-gray-200 text-gray-700 font-medium px-4 py-2.5 rounded-lg text-sm flex items-center justify-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 mr-2" viewBox="0 0 24 24" fill="none"
                         stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <line x1="18" y1="6" x2="6" y2="18"></line>
                        <line x1="6" y1="6" x2="18" y2="18"></line>
                    </svg>
                    Reset
                </a>
            <?php endif; ?>
        </div>
    </form>

    <!-- Services Table -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <!-- Responsive table container -->
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead>
                <tr class="bg-gray-50">
                    <th class="px-4 sm:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        ID
                    </th>
                    <th class="px-4 sm:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Nama Layanan
                    </th>
                    <th class="px-4 sm:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider hidden sm:table-cell">
                        Kategori
                    </th>
                    <th class="px-4 sm:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider hidden md:table-cell">
                        Harga Dasar
                    </th>
                    <th class="px-4 sm:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider hidden md:table-cell">
                        Estimasi Waktu
                    </th>
                    <th class="px-4 sm:px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Aksi
                    </th>
                </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200 overflow-x-auto">
                <?php if (empty($services)): ?>
                    <tr>
                        <td colspan="6" class="px-4 sm:px-6 py-8 text-center text-gray-500">
                            <div class="flex flex-col items-center justify-center">
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-12 h-12 text-gray-300 mb-2"
                                     viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1"
                                     stroke-linecap="round" stroke-linejoin="round">
                                    <circle cx="12" cy="12" r="10"></circle>
                                    <line x1="12" y1="8" x2="12" y2="12"></line>
                                    <line x1="12" y1="16" x2="12.01" y2="16"></line>
                                </svg>
                                <p class="text-sm font-medium">No services found</p>
                                <p class="text-xs mt-1">Add new services to get started</p>
                            </div>
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($services as $service): ?>
                        <tr class="hover:bg-gray-50 transition duration-150">
                            <td class="px-4 sm:px-6 py-3 whitespace-nowrap font-mono text-xs text-gray-500"><?= $service['id'] ?></td>
                            <td class="px-4 sm:px-6 py-3">
                                <div class="font-medium text-gray-900 mb-0.5"><?= $service['name'] ?></div>
                                <div class="text-xs text-gray-500 line-clamp-1"><?= $service['description'] ?></div>
                                <!-- Mobile-only info -->
                                <div class="sm:hidden mt-1 flex flex-col gap-1">
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-primary-100 text-primary-800 w-fit">
                                    <?= $service['category'] ?>
                                </span>
                                    <span class="text-xs text-gray-700">Rp <?= number_format($service['base_price'], 2, ',', '.') ?></span>
                                    <span class="text-xs text-gray-700 flex items-center">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-3 h-3 text-gray-400 mr-1"
                                         viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                         stroke-linecap="round" stroke-linejoin="round">
                                        <circle cx="12" cy="12" r="10"></circle>
                                        <polyline points="12 6 12 12 16 14"></polyline>
                                    </svg>
                                    <?= $service['estimated_hours'] ?> jam
                                </span>
                                </div>
                            </td>
                            <td class="px-4 sm:px-6 py-3 hidden sm:table-cell">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-primary-100 text-primary-800">
                                <?= $service['category'] ?>
                            </span>
                            </td>
                            <td class="px-4 sm:px-6 py-3 text-gray-700 font-medium hidden md:table-cell">
                                Rp <?= number_format($service['base_price'], 2, ',', '.') ?>
                            </td>
                            <td class="px-4 sm:px-6 py-3 hidden md:table-cell">
                                <div class="flex items-center text-gray-700">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-gray-400 mr-1.5"
                                         viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                         stroke-linecap="round" stroke-linejoin="round">
                                        <circle cx="12" cy="12" r="10"></circle>
                                        <polyline points="12 6 12 12 16 14"></polyline>
                                    </svg>
                                    <?= $service['estimated_hours'] ?> jam
                                </div>
                            </td>
                            <td class="px-4 sm:px-6 py-3 text-center">
                                <div class="flex items-center justify-center space-x-2 sm:space-x-3">
                                    <button @click="setEditItem(<?= htmlspecialchars(json_encode($service), ENT_QUOTES, 'UTF-8') ?>)"
                                            title="Edit service"
                                            class="text-primary-600 hover:text-primary-900 p-1.5 rounded-full hover:bg-primary-50">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 24 24"
                                             fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                             stroke-linejoin="round">
                                            <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path>
                                            <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path>
                                        </svg>
                                    </button>
                                    <form method="POST"
                                          onsubmit="return confirm('Are you sure you want to delete this service?')">
                                        <input type="hidden" name="action" value="delete">
                                        <input type="hidden" name="id" value="<?= $service['id'] ?>">
                                        <button type="submit" title="Delete service"
                                                class="text-red-600 hover:text-red-900 p-1.5 rounded-full hover:bg-red-50">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 24 24"
                                                 fill="none" stroke="currentColor" stroke-width="2"
                                                 stroke-linecap="round" stroke-linejoin="round">
                                                <polyline points="3 6 5 6 21 6"></polyline>
                                                <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path>
                                                <line x1="10" y1="11" x2="10" y2="17"></line>
                                                <line x1="14" y1="11" x2="14" y2="17"></line>
                                            </svg>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="bg-white px-4 sm:px-6 py-4 border-t border-gray-200">
            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                <div class="text-sm text-gray-500 text-center md:text-left">
                    Showing <span class="font-medium"><?= $offset + 1 ?></span> to
                    <span class="font-medium"><?= min($offset + $limit, $totalServices) ?></span> of
                    <span class="font-medium"><?= $totalServices ?></span> services
                </div>

                <div class="flex flex-wrap items-center justify-center gap-1 sm:gap-2">
                    <!-- First page -->
                    <a href="?page=1&limit=<?= $limit ?>"
                       class="<?= ($page <= 1) ? 'pointer-events-none text-gray-400 bg-gray-50' : 'text-gray-700 hover:bg-gray-100' ?> px-2 sm:px-3 py-1.5 text-sm border border-gray-200 rounded flex items-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 24 24" fill="none"
                             stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <polyline points="11 17 6 12 11 7"></polyline>
                            <polyline points="18 17 13 12 18 7"></polyline>
                        </svg>
                    </a>

                    <!-- Previous page -->
                    <a href="?page=<?= max(1, $page - 1) ?>&limit=<?= $limit ?>"
                       class="<?= ($page <= 1) ? 'pointer-events-none text-gray-400 bg-gray-50' : 'text-gray-700 hover:bg-gray-100' ?> px-2 sm:px-3 py-1.5 text-sm border border-gray-200 rounded">
                        <span class="hidden sm:inline">Previous</span>
                        <span class="sm:hidden">&lt;</span>
                    </a>

                    <!-- Page numbers - hide on smallest screens -->
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

                    <!-- Current page indicator for smallest screens -->
                    <span class="xs:hidden text-sm px-2.5 py-1.5">
                    <?= $page ?> / <?= $totalPages ?>
                </span>

                    <!-- Next page -->
                    <a href="?page=<?= min($totalPages, $page + 1) ?>&limit=<?= $limit ?>"
                       class="<?= ($page >= $totalPages) ? 'pointer-events-none text-gray-400 bg-gray-50' : 'text-gray-700 hover:bg-gray-100' ?> px-2 sm:px-3 py-1.5 text-sm border border-gray-200 rounded">
                        <span class="hidden sm:inline">Next</span>
                        <span class="sm:hidden">&gt;</span>
                    </a>

                    <!-- Last page -->
                    <a href="?page=<?= $totalPages ?>&limit=<?= $limit ?>"
                       class="<?= ($page >= $totalPages) ? 'pointer-events-none text-gray-400 bg-gray-50' : 'text-gray-700 hover:bg-gray-100' ?> px-2 sm:px-3 py-1.5 text-sm border border-gray-200 rounded flex items-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 24 24" fill="none"
                             stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <polyline points="13 17 18 12 13 7"></polyline>
                            <polyline points="6 17 11 12 6 7"></polyline>
                        </svg>
                    </a>

                    <!-- Items per page selector -->
                    <div class="flex items-center ml-2 pl-2 sm:ml-4 sm:pl-4 border-l border-gray-200">
                        <span class="text-sm text-gray-500 mr-1 sm:mr-2 hidden xs:inline">Show:</span>
                        <select onchange="window.location = '?page=1&limit=' + this.value"
                                class="text-sm border border-gray-200 rounded px-1 sm:px-2 py-1.5 focus:outline-none focus:ring-1">
                            <?php foreach ([10, 25, 50, 100] as $val): ?>
                                <option value="<?= $val ?>" <?= $limit == $val ? 'selected' : '' ?>><?= $val ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal -->
    <div x-show="openModal"
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0 transform scale-95"
         x-transition:enter-end="opacity-100 transform scale-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100 transform scale-100"
         x-transition:leave-end="opacity-0 transform scale-95"
         class="fixed inset-0 z-50 overflow-y-auto flex items-center justify-center"
         style="display: none;">
        <div class="fixed inset-0 bg-black bg-opacity-50" @click="openModal = false"></div>

        <div class="relative bg-white rounded-xl max-w-md w-full mx-4 shadow-xl p-0 z-10">
            <div class="border-b p-5">
                <div class="flex justify-between items-center">
                    <h3 class="text-lg font-semibold text-gray-800"
                        x-text="editItem.id ? 'Edit Layanan' : 'Tambah Layanan Baru'"></h3>
                    <button @click="openModal = false" class="text-gray-400 hover:text-gray-600 focus:outline-none">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 24 24" fill="none"
                             stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <line x1="18" y1="6" x2="6" y2="18"></line>
                            <line x1="6" y1="6" x2="18" y2="18"></line>
                        </svg>
                    </button>
                </div>
            </div>

            <form method="POST" class="p-5 space-y-5">
                <input type="hidden" name="action" :value="editItem.id ? 'update' : 'create'">
                <input type="hidden" id="id" name="id" :value="editItem.id || ''">

                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Nama Layanan</label>
                    <input id="name" name="name" type="text"
                           class="w-full border border-gray-200 px-4 py-2.5 rounded-lg text-sm focus:ring-2"
                           x-model="editItem.name" required>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label for="category" class="block text-sm font-medium text-gray-700 mb-1">Kategori</label>
                        <select id="category" name="category"
                                class="w-full border border-gray-200 px-4 py-2.5 rounded-lg text-sm focus:ring-2"
                                x-model="editItem.category" required>
                            <option value="">Pilih Kategori</option>
                            <?php foreach ($categories as $c): ?>
                                <option value="<?= $c ?>"><?= $c ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div>
                        <label for="base_price" class="block text-sm font-medium text-gray-700 mb-1">Harga (Rp)</label>
                        <input id="base_price" name="base_price" type="number" step="0.01" min="0"
                               class="w-full border border-gray-200 px-4 py-2.5 rounded-lg text-sm focus:ring-2"
                               x-model="editItem.base_price" required>
                    </div>
                </div>

                <div>
                    <label for="estimated_hours" class="block text-sm font-medium text-gray-700 mb-1">Estimasi Waktu
                        (jam)</label>
                    <input id="estimated_hours" name="estimated_hours" type="number" step="0.5" min="0.5"
                           class="w-full border border-gray-200 px-4 py-2.5 rounded-lg text-sm focus:ring-2"
                           x-model="editItem.estimated_hours" required>
                </div>

                <div>
                    <label for="description" class="block text-sm font-medium text-gray-700 mb-1">Deskripsi</label>
                    <textarea id="description" name="description" rows="3"
                              class="w-full border border-gray-200 px-4 py-2.5 rounded-lg text-sm focus:ring-2 resize-none"
                              x-model="editItem.description"></textarea>
                </div>

                <div class="flex space-x-3 pt-4">
                    <button type="button" @click="openModal = false"
                            class="flex-1 bg-gray-100 hover:bg-gray-200 text-gray-700 font-medium py-2.5 rounded-lg">
                        Batal
                    </button>
                    <button type="submit"
                            class="flex-1 bg-primary-600 hover:bg-primary-700 text-white font-medium py-2.5 rounded-lg">
                        Simpan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>