<?php

if (!isset($_SESSION['form_data'])) {
    $_SESSION['form_data'] = [
        'service_type' => '',
        'service_id' => '',
        'service_price' => '',
        'service_duration' => '',
        'date' => '',
        'time' => '',
        'vehicle_brand' => '',
        'vehicle_model' => '',
        'vehicle_year' => '',
        'plate_number' => '',
        'notes' => ''
    ];
}

// Get services from database and prepare for view
$serviceModel = new \App\Models\Service();

// Get user ID and initialize service request model
$userId = $_SESSION['user_id'] ?? null;

$serviceRequestModel = new \App\Models\ServiceRequest();

// Get date and service selection from session or form
$selectedDate = $_SESSION['form_data']['date'] = $_POST['date'] ?? $_SESSION['form_data']['date'] ?? date('Y-m-d');
$selectedServiceId = $_SESSION['form_data']['service_id'] = $_POST['service_id'] ?? $_SESSION['form_data']['service_id'] ?? null;

// Get available time slots based on service and date selection
$availableTimeSlots = $selectedServiceId ?
    $serviceRequestModel->getAvailableServiceTimes($selectedServiceId, $selectedDate) : [];

// Process form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_booking'])) {
    // Get service details

    $serviceData = $serviceModel->findById($_POST['service_id']);

    if ($serviceData) {
        // Store form data in session
        $_SESSION['form_data'] = [
            'service_id' => $_POST['service_id'] ?? '',
            'service_type' => $_POST['service_type'] ?? '',
            'service_price' => $serviceData['base_price'] ?? 0,
            'service_duration' => $serviceData['estimated_hours'] ?? 0,
            'date' => $_POST['date'] ?? '',
            'time' => $_POST['time'] ?? '',
            'vehicle_brand' => $_POST['vehicle_brand'] ?? '',
            'vehicle_model' => $_POST['vehicle_model'] ?? '',
            'vehicle_year' => $_POST['vehicle_year'] ?? '',
            'plate_number' => $_POST['plate_number'] ?? '',
            'notes' => $_POST['notes'] ?? ''
        ];

        // Create service request if user is logged in
        if ($userId) {
            $requestData = [
                'user_id' => $userId,
                'service_id' => $_POST['service_id'],
                'vehicle_brand' => $_POST['vehicle_brand'],
                'vehicle_model' => $_POST['vehicle_model'],
                'vehicle_year' => $_POST['vehicle_year'],
                'plate_number' => $_POST['plate_number'],
                'notes' => $_POST['notes'],
                'scheduled_date' => $_POST['date'],
                'scheduled_time' => $_POST['time'],
                'estimated_price' => $serviceData['base_price'],
                'status' => 'pending',
                'created_at' => date('Y-m-d H:i:s')
            ];

            $requestId = $serviceRequestModel->create($requestData);

            if ($requestId) {
                $_SESSION['last_request_id'] = $requestId;
                $_SESSION['form_data'] = []; // Clear form data
                header("Location: /booking-confirmation");
                exit();
            } else {
                $errorMessage = "Terjadi kesalahan saat menyimpan data. Silakan coba lagi.";
            }
        } else {
            $errorMessage = "Anda harus login untuk melakukan pemesanan.";
        }
    } else {
        $errorMessage = "Layanan tidak ditemukan. Silakan pilih layanan yang valid.";
    }
}

$formData = $_SESSION['form_data'];
?>

<div class="max-w-3xl mx-auto py-12 px-4">
    <div class="text-center mb-8">
        <h1 class="text-3xl font-bold text-gray-900">Pesan Servis</h1>
        <p class="mt-2 text-gray-600">Isi formulir di bawah untuk memesan layanan servis</p>
    </div>

    <?php if (isset($errorMessage)): ?>
        <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">
            <span class="block sm:inline"><?= $errorMessage ?></span>
        </div>
    <?php endif; ?>

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

    <div class="bg-white rounded-lg shadow-sm p-6">
        <form id="bookingForm" method="POST">
            <!-- Step 1: Service Selection -->
            <?php if (!empty($services)): ?>
                <div class="step-content" id="step-1">
                    <div class="space-y-6">
                        <h2 class="text-xl font-semibold">Pilih Layanan Servis</h2>
                        <div class="grid gap-4">
                            <?php foreach ($services as $service): ?>
                                <?php
                                $duration_value = (float)str_replace(' jam', '', $service['duration']);
                                $price_value = (int)str_replace(['Rp ', '.'], '', $service['price']);
                                $is_selected = $formData['service_type'] == $service['name'];
                                ?>
                                <label class="service-option p-4 border rounded-lg cursor-pointer transition-all
                                <?= $is_selected ? 'border-blue-600 bg-blue-50' : 'border-gray-200 hover:border-blue-200' ?>">
                                    <div class="flex items-start justify-between">
                                        <div>
                                            <h3 class="font-medium text-gray-900"><?= $service['name'] ?></h3>
                                            <p class="mt-1 text-sm text-gray-500"><?= $service['description'] ?></p>
                                            <div class="mt-2 flex items-center space-x-4">
                                            <span class="text-sm text-gray-600">
                                                <svg class="lucide lucide-clock inline-block h-4 w-4 mr-1"
                                                     xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                                     viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                                     stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                    <circle cx="12" cy="12" r="10"></circle>
                                                    <polyline points="12 6 12 12 16 14"></polyline>
                                                </svg>
                                                <?= $service['duration'] ?>
                                            </span>
                                                <span class="font-medium text-blue-600"><?= $service['price'] ?></span>
                                            </div>
                                        </div>
                                        <div class="flex items-center h-5">
                                            <input type="radio" name="service_type" value="<?= $service['name'] ?>"
                                                   data-id="<?= $service['id'] ?>"
                                                   data-price="<?= $price_value ?>"
                                                   data-duration="<?= $duration_value ?>" <?= $is_selected ? 'checked' : '' ?>
                                                   class="w-5 h-5 text-blue-600 rounded-full border-gray-300">
                                        </div>
                                    </div>
                                </label>
                            <?php endforeach; ?>
                        </div>
                    </div>
                    <div class="mt-8 flex justify-end">
                        <button type="button"
                                class="next-step px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 disabled:opacity-50 disabled:cursor-not-allowed flex items-center"
                                disabled>
                            Lanjut
                            <svg class="lucide lucide-chevron-right h-5 w-5 ml-2" xmlns="http://www.w3.org/2000/svg"
                                 width="24" height="24" viewBox="0 0 24 24" fill="none"
                                 stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="m9 18 6-6-6-6"></path>
                            </svg>
                        </button>
                    </div>
                </div>
            <?php else: ?>
                <p>Tidak ada layanan tersedia.</p>
            <?php endif; ?>

            <!-- Step 2: Schedule Selection -->
            <div class="step-content hidden" id="step-2">
                <div class="space-y-6">
                    <h2 class="text-xl font-semibold">Pilih Jadwal Servis</h2>
                    <div class="space-y-4">
                        <div>
                            <label for="date" class="block text-sm font-medium text-gray-700 mb-2">Tanggal
                                Servis</label>
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
                                    <p class="col-span-3 text-gray-500">Silakan pilih layanan dan tanggal terlebih
                                        dahulu untuk melihat waktu yang tersedia.</p>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="mt-8 flex justify-between">
                    <button type="button"
                            class="prev-step flex items-center px-6 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50">
                        <svg class="lucide lucide-chevron-left h-5 w-5 mr-2" xmlns="http://www.w3.org/2000/svg"
                             width="24" height="24" viewBox="0 0 24 24" fill="none"
                             stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="m15 18-6-6 6-6"></path>
                        </svg>
                        Kembali
                    </button>
                    <button type="button"
                            class="next-step flex items-center px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 disabled:opacity-50 disabled:cursor-not-allowed">
                        Lanjut
                        <svg class="lucide lucide-chevron-right h-5 w-5 ml-2" xmlns="http://www.w3.org/2000/svg"
                             width="24" height="24" viewBox="0 0 24 24" fill="none"
                             stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="m9 18 6-6-6-6"></path>
                        </svg>
                    </button>
                </div>
            </div>

            <!-- Step 3: Vehicle Information -->
            <div class="step-content hidden" id="step-3">
                <div class="space-y-6">
                    <h2 class="text-xl font-semibold">Informasi Kendaraan</h2>
                    <div class="space-y-4">
                        <?php
                        $vehicleFields = [
                            ['id' => 'vehicle_brand', 'label' => 'Merek Kendaraan', 'placeholder' => 'Contoh: Toyota'],
                            ['id' => 'vehicle_model', 'label' => 'Model Kendaraan', 'placeholder' => 'Contoh: Avanza'],
                            ['id' => 'vehicle_year', 'label' => 'Tahun Kendaraan', 'placeholder' => 'Contoh: 2020'],
                            ['id' => 'plate_number', 'label' => 'Nomor Plat', 'placeholder' => 'Contoh: B 1234 CD'],
                        ];

                        foreach ($vehicleFields as $field): ?>
                            <div>
                                <label for="<?= $field['id'] ?>" class="block text-sm font-medium text-gray-700 mb-2">
                                    <?= $field['label'] ?>
                                </label>
                                <input type="text" id="<?= $field['id'] ?>" name="<?= $field['id'] ?>"
                                       value="<?= htmlspecialchars($formData[$field['id']]) ?>"
                                       placeholder="<?= $field['placeholder'] ?>"
                                       class="block w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500"
                                       required>
                            </div>
                        <?php endforeach; ?>

                        <div>
                            <label for="notes" class="block text-sm font-medium text-gray-700 mb-2">Catatan
                                Tambahan</label>
                            <textarea id="notes" name="notes"
                                      placeholder="Tambahkan catatan khusus tentang kendaraan Anda"
                                      class="block w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500"
                                      rows="4"><?= htmlspecialchars($formData['notes']) ?></textarea>
                        </div>
                    </div>
                </div>
                <div class="mt-8 flex justify-between">
                    <button type="button"
                            class="prev-step flex items-center px-6 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50">
                        <svg class="lucide lucide-chevron-left h-5 w-5 mr-2" xmlns="http://www.w3.org/2000/svg"
                             width="24" height="24" viewBox="0 0 24 24" fill="none"
                             stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="m15 18-6-6 6-6"></path>
                        </svg>
                        Kembali
                    </button>
                    <button type="button"
                            class="next-step flex items-center px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                        Lanjut
                        <svg class="lucide lucide-chevron-right h-5 w-5 ml-2" xmlns="http://www.w3.org/2000/svg"
                             width="24" height="24" viewBox="0 0 24 24" fill="none"
                             stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="m9 18 6-6-6-6"></path>
                        </svg>
                    </button>
                </div>
            </div>

            <!-- Step 4: Confirmation -->
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
                                ['label' => 'Nomor Plat', 'id' => 'summary-plate']
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
                                        <p class="text-gray-700 bg-white p-2 rounded border border-gray-200"
                                           id="summary-notes"></p>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
                <div class="mt-8 flex justify-between">
                    <button type="button"
                            class="prev-step flex items-center px-6 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50">
                        <svg class="lucide lucide-chevron-left h-5 w-5 mr-2" xmlns="http://www.w3.org/2000/svg"
                             width="24" height="24" viewBox="0 0 24 24" fill="none"
                             stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="m15 18-6-6 6-6"></path>
                        </svg>
                        Kembali
                    </button>
                    <button type="submit" name="submit_booking"
                            class="flex items-center px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                        <svg class="lucide lucide-check h-5 w-5 mr-2" xmlns="http://www.w3.org/2000/svg" width="24"
                             height="24" viewBox="0 0 24 24" fill="none"
                             stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <polyline points="20 6 9 17 4 12"></polyline>
                        </svg>
                        Pesan Sekarang
                    </button>
                </div>
            </div>

            <!-- Hidden fields -->
            <input type="hidden" name="service_id" id="service_id"
                   value="<?= htmlspecialchars((string)$formData['service_id']) ?>">
            <input type="hidden" name="service_price" id="service_price"
                   value="<?= htmlspecialchars((string)$formData['service_price']) ?>">
            <input type="hidden" name="service_duration" id="service_duration"
                   value="<?= htmlspecialchars((string)$formData['service_duration']) ?>">
        </form>
    </div>
</div>