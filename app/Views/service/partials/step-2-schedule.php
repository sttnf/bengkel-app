<?php
/**
 * Step 2: Schedule Selection
 * Date and time selection for service appointment
 */
?>

<div class="step-content hidden" id="step-2">
    <div class="space-y-6">
        <h2 class="text-xl font-semibold">Pilih Jadwal Servis</h2>
        <div class="space-y-4">
            <div>
                <label for="date" class="block text-sm font-medium text-gray-700 mb-2">Tanggal Servis</label>
                <input type="date" id="date" name="date" value="<?= $formData['date'] ?>"
                       min="<?= date('Y-m-d') ?>"
                       class="block w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500"
                       required>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Waktu Servis</label>
                <div class="grid grid-cols-3 gap-4" id="time-slots-container">
                    <?php if (!empty($availableTimeSlots)): ?>
                        <?php foreach ($availableTimeSlots as $slot): ?>
                            <?php
                            $time = date('H:i', strtotime($slot['time']));
                            $is_selected = $formData['time'] == $slot['time'];
                            ?>
                            <label class="time-slot px-4 py-3 border rounded-lg flex items-center justify-center cursor-pointer
                                <?= $is_selected ? 'border-blue-600 bg-blue-50' : 'border-gray-200 hover:border-blue-200' ?>">
                                <input type="radio" name="time"
                                       value="<?= $slot['time'] ?>" <?= $is_selected ? 'checked' : '' ?>
                                       class="sr-only">
                                <span><?= $time ?></span>
                            </label>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <p class="col-span-3 text-gray-500">Silakan pilih layanan dan tanggal terlebih dahulu untuk
                            melihat waktu yang tersedia.</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

</div>