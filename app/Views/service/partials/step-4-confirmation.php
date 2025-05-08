<?php
/**
 * Step 4: Confirmation
 * Summary of booking details and confirmation step
 */
?>

<div class="step-content hidden" id="step-4">
    <div class="space-y-6">
        <h2 class="text-xl font-semibold">Konfirmasi Pesanan</h2>

        <?php
        $summaryBlocks = [
            [
                'title' => 'Detail Layanan',
                'fields' => [
                    ['label' => 'Layanan', 'id' => 'summary-service'],
                    ['label' => 'Harga', 'id' => 'summary-price'],
                    ['label' => 'Estimasi Waktu', 'id' => 'summary-duration']
                ]
            ],
            [
                'title' => 'Jadwal',
                'fields' => [
                    ['label' => 'Tanggal', 'id' => 'summary-date'],
                    ['label' => 'Waktu', 'id' => 'summary-time']
                ]
            ],
            [
                'title' => 'Kendaraan',
                'fields' => [
                    ['label' => 'Kendaraan', 'id' => 'summary-vehicle'],
                    ['label' => 'Nomor Plat', 'id' => 'summary-plate'],
                    ['label' => 'Tahun', 'id' => 'summary-year'],
                    ['label' => 'Warna', 'id' => 'summary-color'],
                    ['label' => 'Nomor Rangka (VIN)', 'id' => 'summary-vin']
                ],
                'notes' => true
            ]
        ];

        foreach ($summaryBlocks as $block): ?>
            <div class="bg-gray-50 p-4 rounded-lg">
                <h3 class="font-medium text-gray-900 mb-3"><?= $block['title'] ?></h3>
                <div class="space-y-2">
                    <?php foreach ($block['fields'] as $field): ?>
                        <div class="flex justify-between">
                            <span class="text-gray-600"><?= $field['label'] ?>:</span>
                            <span class="font-medium" id="<?= $field['id'] ?>">-</span>
                        </div>
                    <?php endforeach; ?>

                    <?php if (isset($block['notes']) && $block['notes']): ?>
                        <div class="pt-2 notes-container hidden">
                            <span class="text-gray-600 block mb-1">Catatan:</span>
                            <p class="text-gray-700 bg-white p-2 rounded border border-gray-200" id="summary-notes"></p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>