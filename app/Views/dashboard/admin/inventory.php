<?php
$pageTitle = "Inventory Management";
$pageHeader = "Inventory Items";

$categories = ['Pelumas', 'Filter', 'Rem', 'Aki/Battery', 'Ignition', 'Body Parts', 'Suspension', 'Electrical', 'Lainnya'];
$inventoryItems = $inventory_items ?? [];
?>

<div x-data="{ openModal: false, editItem: {} }">
    <!-- Header -->
    <header class="mb-6">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div>
                <h1 class="text-3xl font-bold"><?= $pageHeader ?></h1>
                <p class="mt-2 text-sm text-gray-600">Kelola dan pantau status inventaris</p>
            </div>
            <button @click="openModal = true; editItem = {}"
                    class="bg-primary-600 text-white px-4 py-2 rounded text-sm flex items-center">
                <i data-lucide="plus" class="w-4 h-4 mr-1"></i> Tambah Item
            </button>
        </div>
    </header>

    <!-- Filters -->
    <form class="bg-white p-4 rounded-lg shadow-sm border mb-6 grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-4">
        <input type="text" placeholder="Cari nama / part number" class="px-3 py-2 border rounded text-sm">
        <select class="px-3 py-2 border rounded text-sm">
            <option value="">Semua Kategori</option>
            <?php foreach ($categories as $c): ?>
                <option value="<?= $c ?>"><?= $c ?></option>
            <?php endforeach; ?>
        </select>
        <select class="px-3 py-2 border rounded text-sm">
            <option value="">Semua Status</option>
            <option value="available">Tersedia</option>
            <option value="low_stock">Stok Rendah</option>
            <option value="out_of_stock">Habis</option>
        </select>
        <button type="submit"
                class="bg-gray-100 hover:bg-gray-200 px-3 py-2 rounded text-sm flex items-center justify-center">
            <i data-lucide="filter" class="w-4 h-4 mr-1"></i> Filter
        </button>
    </form>

    <!-- Inventory Table -->
    <div class="bg-white rounded-lg shadow-sm border overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50 text-xs font-medium text-gray-500 uppercase">
            <tr>
                <th class="px-4 py-3 text-left">Part No</th>
                <th class="px-4 py-3 text-left">Nama</th>
                <th class="px-4 py-3 text-left">Kategori</th>
                <th class="px-4 py-3 text-left">Stok</th>
                <th class="px-4 py-3 text-left">Harga</th>
                <th class="px-4 py-3 text-left">Status</th>
                <th class="px-4 py-3 text-left">Aksi</th>
            </tr>
            </thead>
            <tbody class="divide-y divide-gray-200 bg-white text-sm">
            <?php foreach ($inventoryItems as $item): ?>
                <tr class="hover:bg-gray-50">
                    <td class="px-4 py-3 font-mono text-gray-600"><?= $item['part_number'] ?></td>
                    <td class="px-4 py-3">
                        <div class="font-medium text-gray-900"><?= $item['name'] ?></div>
                        <div class="text-xs text-gray-500"><?= $item['supplier'] ?></div>
                    </td>
                    <td class="px-4 py-3 text-gray-500"><?= $item['category'] ?></td>
                    <td class="px-4 py-3 text-gray-500"><?= $item['current_stock'] ?> <?= $item['unit'] ?></td>
                    <td class="px-4 py-3 text-gray-500">Rp <?= number_format($item['unit_price'], 0, ',', '.') ?></td>
                    <td class="px-4 py-3">
                                                       <span class="px-2 py-1 text-xs font-medium rounded-full
                                                           <?= match (true) {
                                                           $item['current_stock'] <= 0 => 'bg-red-100 text-red-800',
                                                           $item['current_stock'] <= $item['reorder_level'] => 'bg-yellow-100 text-yellow-800',
                                                           default => 'bg-green-100 text-green-800',
                                                       } ?>">
                                                           <?= match (true) {
                                                               $item['current_stock'] <= 0 => 'Out of Stock',
                                                               $item['current_stock'] <= $item['reorder_level'] => 'Low Stock',
                                                               default => 'Available',
                                                           } ?>
                                                       </span>
                    </td>
                    <td class="px-4 py-3 flex items-center justify-center">
                        <!-- Edit button -->
                        <button @click="editItem = <?= json_encode($item, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT) ?>; openModal = true"
                                title="Edit item"
                                class="text-blue-600 hover:text-blue-900 p-1 rounded hover:bg-blue-50">
                            <i data-lucide="edit" class="w-4 h-4"></i>
                        </button>

                        <!-- Delete form -->
                        <form method="POST"
                              class="inline-block"
                              onsubmit="return confirm('Are you sure you want to delete this item?')">
                            <input type="hidden" name="action" value="delete">
                            <input type="hidden" name="id" value="<?= $item['id'] ?>">
                            <button type="submit"
                                    title="Delete item"
                                    class="text-red-600 hover:text-red-900 p-1 rounded hover:bg-red-50">
                                <i data-lucide="trash-2" class="w-4 h-4"></i>
                            </button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <!-- Modal -->
    <div x-show="openModal" x-cloak class="fixed inset-0 z-50 bg-black bg-opacity-50 flex items-center justify-center">
        <div @click.away="openModal = false" class="bg-white p-6 rounded-lg max-w-md w-full mx-4 shadow-lg">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-semibold" x-text="editItem?.name ? 'Edit Item' : 'Tambah Item Baru'"></h3>
                <button @click="openModal = false" class="text-gray-400 hover:text-gray-600">
                    <i data-lucide="x" class="w-5 h-5"></i>
                </button>
            </div>
            <form method="POST" class="space-y-4">
                <input type="hidden" id="id" name="id" :value="editItem.id || ''">
                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Nama Item</label>
                    <input id="name" name="name" type="text" class="w-full border px-3 py-2 rounded text-sm"
                           :value="editItem.name || ''" required>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label for="part_number" class="block text-sm font-medium text-gray-700 mb-1">Part
                            Number</label>
                        <input id="part_number" name="part_number" type="text"
                               class="w-full border px-3 py-2 rounded text-sm" :value="editItem.part_number || ''">
                    </div>
                    <div>
                        <label for="category" class="block text-sm font-medium text-gray-700 mb-1">Kategori</label>
                        <select id="category" name="category" class="w-full border px-3 py-2 rounded text-sm"
                                :value="editItem.category || ''">
                            <option value="">Pilih Kategori</option>
                            <?php foreach ($categories as $c): ?>
                                <option value="<?= $c ?>"><?= $c ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
                <div class="grid grid-cols-3 gap-4">
                    <div>
                        <label for="current_stock" class="block text-sm font-medium text-gray-700 mb-1">Stok</label>
                        <input id="current_stock" name="current_stock" type="number"
                               class="w-full border px-3 py-2 rounded text-sm" :value="editItem.current_stock || 0"
                               required>
                    </div>
                    <div>
                        <label for="unit" class="block text-sm font-medium text-gray-700 mb-1">Unit</label>
                        <input id="unit" name="unit" type="text" class="w-full border px-3 py-2 rounded text-sm"
                               :value="editItem.unit || ''" required>
                    </div>
                    <div>
                        <label for="reorder_level" class="block text-sm font-medium text-gray-700 mb-1">Reorder</label>
                        <input id="reorder_level" name="reorder_level" type="number"
                               class="w-full border px-3 py-2 rounded text-sm" :value="editItem.reorder_level || 0">
                    </div>
                </div>
                <div>
                    <label for="unit_price" class="block text-sm font-medium text-gray-700 mb-1">Harga Satuan
                        (Rp)</label>
                    <input id="unit_price" name="unit_price" type="number"
                           class="w-full border px-3 py-2 rounded text-sm" :value="editItem.unit_price || 0" required>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label for="supplier" class="block text-sm font-medium text-gray-700 mb-1">Supplier</label>
                        <input id="supplier" name="supplier" type="text" class="w-full border px-3 py-2 rounded text-sm"
                               :value="editItem.supplier || ''">
                    </div>
                    <div>
                        <label for="location" class="block text-sm font-medium text-gray-700 mb-1">Lokasi</label>
                        <input id="location" name="location" type="text" class="w-full border px-3 py-2 rounded text-sm"
                               :value="editItem.location || ''">
                    </div>
                </div>
                <div class="flex space-x-3 pt-4">
                    <button type="button" @click="openModal = false"
                            class="flex-1 bg-gray-100 text-gray-700 py-2 px-4 rounded hover:bg-gray-200">Batal
                    </button>
                    <button type="submit"
                            class="flex-1 bg-primary-600 text-white py-2 px-4 rounded hover:bg-primary-700">Simpan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>