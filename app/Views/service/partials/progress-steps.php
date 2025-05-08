<?php
/**
 * Progress Steps Indicator
 * Displays the multi-step booking form progress
 */

$steps = [
    'Pilih Layanan',
    'Pilih Jadwal',
    'Info Kendaraan',
    'Konfirmasi'
];
?>

<div class="mb-8">
    <div class="flex items-center justify-between relative">
        <div class="absolute inset-x-0 top-1/2 -translate-y-1/2 h-0.5 bg-gray-200"></div>
        <?php foreach ($steps as $index => $step): ?>
            <div class="relative flex items-center justify-center w-10 h-10 rounded-full
                <?= $index === 0 ? 'bg-blue-600 text-white' : 'bg-white text-gray-400 border-2 border-gray-200' ?>"
                 id="step-indicator-<?= $index + 1 ?>">
                <span class="text-sm font-medium"><?= $index + 1 ?></span>
            </div>
        <?php endforeach; ?>
    </div>
    <div class="flex justify-between mt-2">
        <?php foreach ($steps as $step): ?>
            <span class="text-sm text-gray-500"><?= $step ?></span>
        <?php endforeach; ?>
    </div>
</div>