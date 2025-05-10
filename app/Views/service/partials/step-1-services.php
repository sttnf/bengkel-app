<?php if (!empty($services)): ?>
    <div class="step-content" id="step-1">
        <div class="space-y-6">
            <h2 class="text-xl font-semibold">Pilih Layanan Servis</h2>
            <div class="grid gap-4">
                <?php foreach ($services as $service): ?>
                    <?php
                    $isSelected = isset($formData) && isset($formData['service_type']) && $formData['service_type'] === $service['name'];
                    ?>
                    <label class="service-option p-4 border rounded-lg cursor-pointer transition-all
                                                                                    <?= $isSelected ? 'border-primary-600 bg-primary-50' : 'border-gray-200 hover:border-primary-200' ?>">
                        <div class="flex items-start justify-between">
                            <div>
                                <h3 class="font-medium text-gray-900"><?= $service['name'] ?></h3>
                                <p class="mt-1 text-sm text-gray-500"><?= $service['description'] ?></p>
                                <div class="mt-2 flex items-center space-x-4">
                                                                                                <span class="text-sm text-gray-600">
                                                                                                    <svg class="lucide lucide-clock inline-block h-4 w-4 mr-1"
                                                                                                         xmlns="http://www.w3.org/2000/svg"
                                                                                                         width="24"
                                                                                                         height="24"
                                                                                                         viewBox="0 0 24 24"
                                                                                                         fill="none"
                                                                                                         stroke="currentColor"
                                                                                                         stroke-width="2"
                                                                                                         stroke-linecap="round"
                                                                                                         stroke-linejoin="round">
                                                                                                        <circle cx="12"
                                                                                                                cy="12"
                                                                                                                r="10"></circle>
                                                                                                        <polyline
                                                                                                                points="12 6 12 12 16 14"></polyline>
                                                                                                    </svg>
                                                                                                    <?= number_format((float)$service["estimated_hours"], 1, ',', '.') ?> jam
                                                                                                </span>
                                    <span class="font-medium text-primary-600">Rp <?= number_format((int)$service["base_price"], 0, ',', '.') ?></span>
                                </div>
                            </div>
                            <div class="flex items-center h-5">
                                <input type="radio" name="service_type" value="<?= $service['name'] ?>"
                                       data-id="<?= $service['id'] ?>"
                                       data-price="<?= $service["base_price"] ?>"
                                       data-duration="<?= $service["estimated_hours"] ?>" <?= $isSelected ? 'checked' : '' ?>
                                       class="w-5 h-5 text-primary-600 rounded-full border-gray-300">
                            </div>
                        </div>
                    </label>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
<?php else: ?>
    <p>Tidak ada layanan tersedia.</p>
<?php endif; ?>