<?php
/**
 * Step 3: Vehicle Information
 * Collect vehicle details from the customer
 */
?>

<div class="step-content hidden" id="step-3">
    <div class="space-y-6">
        <h2 class="text-xl font-semibold">Informasi Kendaraan</h2>
        <div class="space-y-4">
            <?php
            $vehicleFields = [
                ['id' => 'vehicle_brand', 'label' => 'Merek Kendaraan', 'placeholder' => 'Contoh: Toyota'],
                ['id' => 'vehicle_model', 'label' => 'Model Kendaraan', 'placeholder' => 'Contoh: Avanza'],
                ['id' => 'vehicle_year', 'label' => 'Tahun Kendaraan', 'placeholder' => 'Contoh: 2020', 'type' => 'number'],
                ['id' => 'plate_number', 'label' => 'Nomor Plat', 'placeholder' => 'Contoh: B 1234 CD'],
                ['id' => 'vehicle_vin', 'label' => 'Nomor Rangka (VIN)', 'placeholder' => 'Contoh: 1HGCM82633A123456', 'required' => false],
                ['id' => 'vehicle_color', 'label' => 'Warna Kendaraan', 'placeholder' => 'Contoh: Hitam'],
            ];

            foreach ($vehicleFields as $field): ?>
                <div class="mb-4">
                    <label for="<?= $field['id'] ?>" class="block text-sm font-medium text-gray-700 mb-2">
                        <?= $field['label'] ?>
                    </label>
                    <input
                            type="<?= $field['type'] ?? 'text' ?>"
                            id="<?= $field['id'] ?>"
                            name="<?= $field['id'] ?>"
                            value="<?= htmlspecialchars($formData[$field['id']] ?? '') ?>"
                            placeholder="<?= $field['placeholder'] ?>"
                            class="block w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500"
                            required>
                </div>
            <?php endforeach; ?>

            <!-- Notes Section -->
            <div class="mb-4">
                <label for="notes" class="block text-sm font-medium text-gray-700 mb-2">Catatan Tambahan</label>
                <textarea
                        id="notes"
                        name="notes"
                        placeholder="Tambahkan catatan khusus tentang kendaraan Anda"
                        class="block w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500"
                        rows="4"><?= htmlspecialchars($formData['notes'] ?? '') ?></textarea>
            </div>
        </div>
    </div>
</div>
