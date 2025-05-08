<?php
/**
 * Progress Steps Indicator
 * Shows the multi-step booking form progress
 */
?>

<div class="mb-8">
    <div class="flex items-center justify-between relative">
        <div class="absolute left-0 right-0 top-1/2 transform -translate-y-1/2 h-0.5 bg-gray-200"></div>
        <?php for ($i = 1; $i <= 4; $i++): ?>
            <div class="relative flex items-center justify-center w-10 h-10 rounded-full
                <?= $i === 1 ? 'bg-blue-600 text-white' : 'bg-white text-gray-400 border-2 border-gray-200' ?>"
                 id="step-indicator-<?= $i ?>">
                <span class="text-sm font-medium"><?= $i ?></span>
            </div>
        <?php endfor; ?>
    </div>
    <div class="flex justify-between mt-2">
        <span class="text-sm text-gray-500">Pilih Layanan</span>
        <span class="text-sm text-gray-500">Pilih Jadwal</span>
        <span class="text-sm text-gray-500">Info Kendaraan</span>
        <span class="text-sm text-gray-500">Konfirmasi</span>
    </div>
</div>