<?php
session_start();

// Initialize form data if not set
$_SESSION['form_data'] = $_SESSION['form_data'] ?? [
    'service_type' => '',
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

// Service options data
$services = [
    ['name' => 'Tune Up Mesin', 'description' => 'Perawatan rutin untuk performa optimal mesin kendaraan Anda', 'duration' => '2 jam', 'price' => 'Rp 500.000'],
    ['name' => 'Servis Rem', 'description' => 'Pemeriksaan dan perbaikan sistem pengereman untuk keamanan berkendara', 'duration' => '1.5 jam', 'price' => 'Rp 300.000'],
    ['name' => 'Servis AC', 'description' => 'Perawatan sistem pendingin untuk kenyamanan berkendara', 'duration' => '2 jam', 'price' => 'Rp 400.000'],
    ['name' => 'Ganti Oli', 'description' => 'Penggantian oli mesin berkualitas untuk performa optimal', 'duration' => '1 jam', 'price' => 'Rp 250.000']
];

// Time slots available
$time_slots = ['09:00', '10:00', '11:00', '13:00', '14:00', '15:00'];

// Handle final form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_booking'])) {
    // In a real application, you would save the data to a database here
    // Process all form data at once
    $_SESSION['form_data'] = [
        'service_type' => $_POST['service_type'] ?? '',
        'service_price' => $_POST['service_price'] ?? '',
        'service_duration' => $_POST['service_duration'] ?? '',
        'date' => $_POST['date'] ?? '',
        'time' => $_POST['time'] ?? '',
        'vehicle_brand' => $_POST['vehicle_brand'] ?? '',
        'vehicle_model' => $_POST['vehicle_model'] ?? '',
        'vehicle_year' => $_POST['vehicle_year'] ?? '',
        'plate_number' => $_POST['plate_number'] ?? '',
        'notes' => $_POST['notes'] ?? ''
    ];

    echo "<script>console.log(" . json_encode($_SESSION['form_data']) . ");</script>";

    exit;
}

// For pre-filling form fields
$formData = $_SESSION['form_data'];
?>

<div class="max-w-3xl mx-auto py-12">
    <div class="text-center mb-8">
        <h1 class="text-3xl font-bold text-gray-900">Pesan Servis</h1>
        <p class="mt-2 text-gray-600">Isi formulir di bawah untuk memesan layanan servis</p>
    </div>

    <!-- Progress Steps -->
    <div class="mb-8">
        <div class="flex items-center justify-between relative">
            <div class="absolute left-0 right-0 top-1/2 transform -translate-y-1/2 h-0.5 bg-gray-200"></div>
            <div class="relative flex items-center justify-center w-10 h-10 rounded-full bg-blue-600 text-white"
                 id="step-indicator-1">
                <span class="text-sm font-medium">1</span>
            </div>
            <div class="relative flex items-center justify-center w-10 h-10 rounded-full bg-white text-gray-400 border-2 border-gray-200"
                 id="step-indicator-2">
                <span class="text-sm font-medium">2</span>
            </div>
            <div class="relative flex items-center justify-center w-10 h-10 rounded-full bg-white text-gray-400 border-2 border-gray-200"
                 id="step-indicator-3">
                <span class="text-sm font-medium">3</span>
            </div>
            <div class="relative flex items-center justify-center w-10 h-10 rounded-full bg-white text-gray-400 border-2 border-gray-200"
                 id="step-indicator-4">
                <span class="text-sm font-medium">4</span>
            </div>
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
            <div class="step-content" id="step-1">
                <div class="space-y-6">
                    <h2 class="text-xl font-semibold">Pilih Layanan Servis</h2>
                    <div class="grid gap-4">
                        <?php foreach ($services as $service): ?>
                            <?php
                            $duration_value = trim(str_replace('jam', '', $service['duration']));
                            $price_value = trim(str_replace('Rp ', '', $service['price']));
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
                                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                                     viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                                     stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                                                     class="lucide lucide-clock inline-block h-4 w-4 mr-1">
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
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                             stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                             class="lucide lucide-chevron-right h-5 w-5 ml-2">
                            <path d="m9 18 6-6-6-6"></path>
                        </svg>
                    </button>
                </div>
            </div>

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
                            <div class="grid grid-cols-3 gap-4">
                                <?php foreach ($time_slots as $time): ?>
                                    <?php $is_selected = $formData['time'] == $time; ?>
                                    <label class="time-slot px-4 py-3 border rounded-lg flex items-center justify-center cursor-pointer <?= $is_selected ? 'border-blue-600 bg-blue-50' : 'border-gray-200 hover:border-blue-200' ?>">
                                        <input type="radio" name="time"
                                               value="<?= $time ?>" <?= $is_selected ? 'checked' : '' ?>
                                               class="sr-only">
                                        <span><?= $time ?></span>
                                    </label>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="mt-8 flex justify-between">
                    <button type="button"
                            class="prev-step flex items-center px-6 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                             stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                             class="lucide lucide-chevron-left h-5 w-5 mr-2">
                            <path d="m15 18-6-6 6-6"></path>
                        </svg>
                        Kembali
                    </button>
                    <button type="button"
                            class="next-step flex items-center px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 disabled:opacity-50 disabled:cursor-not-allowed">
                        Lanjut
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                             stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                             class="lucide lucide-chevron-right h-5 w-5 ml-2">
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
                        <div>
                            <label for="vehicle_brand" class="block text-sm font-medium text-gray-700 mb-2">Merek
                                Kendaraan</label>
                            <input type="text" id="vehicle_brand" name="vehicle_brand"
                                   value="<?= htmlspecialchars($formData['vehicle_brand']) ?>"
                                   placeholder="Contoh: Toyota"
                                   class="block w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500"
                                   required>
                        </div>

                        <div>
                            <label for="vehicle_model" class="block text-sm font-medium text-gray-700 mb-2">Model
                                Kendaraan</label>
                            <input type="text" id="vehicle_model" name="vehicle_model"
                                   value="<?= htmlspecialchars($formData['vehicle_model']) ?>"
                                   placeholder="Contoh: Avanza"
                                   class="block w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500"
                                   required>
                        </div>

                        <div>
                            <label for="vehicle_year" class="block text-sm font-medium text-gray-700 mb-2">Tahun
                                Kendaraan</label>
                            <input type="text" id="vehicle_year" name="vehicle_year"
                                   value="<?= htmlspecialchars($formData['vehicle_year']) ?>" placeholder="Contoh: 2020"
                                   class="block w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500"
                                   required>
                        </div>

                        <div>
                            <label for="plate_number" class="block text-sm font-medium text-gray-700 mb-2">Nomor
                                Plat</label>
                            <input type="text" id="plate_number" name="plate_number"
                                   value="<?= htmlspecialchars($formData['plate_number']) ?>"
                                   placeholder="Contoh: B 1234 CD"
                                   class="block w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500"
                                   required>
                        </div>

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
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                             stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                             class="lucide lucide-chevron-left h-5 w-5 mr-2">
                            <path d="m15 18-6-6 6-6"></path>
                        </svg>
                        Kembali
                    </button>
                    <button type="button"
                            class="next-step flex items-center px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                        Lanjut
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                             stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                             class="lucide lucide-chevron-right h-5 w-5 ml-2">
                            <path d="m9 18 6-6-6-6"></path>
                        </svg>
                    </button>
                </div>
            </div>

            <!-- Step 4: Confirmation -->
            <div class="step-content hidden" id="step-4">
                <div class="space-y-6">
                    <h2 class="text-xl font-semibold">Konfirmasi Pesanan</h2>

                    <div class="bg-gray-50 p-4 rounded-lg">
                        <h3 class="font-medium text-gray-900 mb-3">Detail Layanan</h3>
                        <div class="space-y-2">
                            <div class="flex justify-between">
                                <span class="text-gray-600">Layanan:</span>
                                <span class="font-medium" id="summary-service">-</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600">Harga:</span>
                                <span class="font-medium" id="summary-price">-</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600">Estimasi Waktu:</span>
                                <span class="font-medium" id="summary-duration">-</span>
                            </div>
                        </div>
                    </div>

                    <div class="bg-gray-50 p-4 rounded-lg">
                        <h3 class="font-medium text-gray-900 mb-3">Jadwal</h3>
                        <div class="space-y-2">
                            <div class="flex justify-between">
                                <span class="text-gray-600">Tanggal:</span>
                                <span class="font-medium" id="summary-date">-</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600">Waktu:</span>
                                <span class="font-medium" id="summary-time">-</span>
                            </div>
                        </div>
                    </div>

                    <div class="bg-gray-50 p-4 rounded-lg">
                        <h3 class="font-medium text-gray-900 mb-3">Kendaraan</h3>
                        <div class="space-y-2">
                            <div class="flex justify-between">
                                <span class="text-gray-600">Kendaraan:</span>
                                <span class="font-medium" id="summary-vehicle">-</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600">Nomor Plat:</span>
                                <span class="font-medium" id="summary-plate">-</span>
                            </div>
                            <div class="pt-2 notes-container hidden">
                                <span class="text-gray-600 block mb-1">Catatan:</span>
                                <p class="text-gray-700 bg-white p-2 rounded border border-gray-200"
                                   id="summary-notes"></p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="mt-8 flex justify-between">
                    <button type="button"
                            class="prev-step flex items-center px-6 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                             stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                             class="lucide lucide-chevron-left h-5 w-5 mr-2">
                            <path d="m15 18-6-6 6-6"></path>
                        </svg>
                        Kembali
                    </button>
                    <button type="submit" name="submit_booking"
                            class="flex items-center px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                             stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                             class="lucide lucide-check h-5 w-5 mr-2">
                            <polyline points="20 6 9 17 4 12"></polyline>
                        </svg>
                        Pesan Sekarang
                    </button>
                </div>
            </div>

            <!-- Hidden fields for storing data -->
            <input type="hidden" name="service_price" id="service_price"
                   value="<?= htmlspecialchars($formData['service_price']) ?>">
            <input type="hidden" name="service_duration" id="service_duration"
                   value="<?= htmlspecialchars($formData['service_duration']) ?>">
        </form>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        // Current step tracker
        let currentStep = 1;

        // All step elements
        const steps = document.querySelectorAll('.step-content');
        const stepIndicators = document.querySelectorAll('[id^="step-indicator-"]');

        // Navigation buttons
        const nextButtons = document.querySelectorAll('.next-step');
        const prevButtons = document.querySelectorAll('.prev-step');

        // Form elements
        const serviceOptions = document.querySelectorAll('input[name="service_type"]');
        const timeSlots = document.querySelectorAll('input[name="time"]');
        const dateInput = document.getElementById('date');

        // Hidden fields
        const servicePriceInput = document.getElementById('service_price');
        const serviceDurationInput = document.getElementById('service_duration');

        // Summary elements
        const summaryService = document.getElementById('summary-service');
        const summaryPrice = document.getElementById('summary-price');
        const summaryDuration = document.getElementById('summary-duration');
        const summaryDate = document.getElementById('summary-date');
        const summaryTime = document.getElementById('summary-time');
        const summaryVehicle = document.getElementById('summary-vehicle');
        const summaryPlate = document.getElementById('summary-plate');
        const summaryNotes = document.getElementById('summary-notes');
        const notesContainer = document.querySelector('.notes-container');

        // Form navigation
        function goToStep(step) {
            // Hide all steps
            steps.forEach(s => s.classList.add('hidden'));

            // Reset all step indicators
            stepIndicators.forEach((indicator, idx) => {
                if (idx + 1 < step) {
                    // Completed steps
                    indicator.classList.add('bg-blue-600', 'text-white');
                    indicator.classList.remove('bg-white', 'text-gray-400', 'border-2', 'border-gray-200');
                } else if (idx + 1 === step) {
                    // Current step
                    indicator.classList.add('bg-blue-600', 'text-white');
                    indicator.classList.remove('bg-white', 'text-gray-400', 'border-2', 'border-gray-200');
                } else {
                    // Future steps
                    indicator.classList.remove('bg-blue-600', 'text-white');
                    indicator.classList.add('bg-white', 'text-gray-400', 'border-2', 'border-gray-200');
                }
            });

            // Show the current step
            document.getElementById(`step-${step}`).classList.remove('hidden');
            currentStep = step;

            // If on the final step, update summary
            if (step === 4) {
                updateSummary();
            }
        }

        // Event listeners for navigation buttons
        nextButtons.forEach(button => {
            button.addEventListener('click', function () {
                // Validate current step before proceeding
                if (validateStep(currentStep)) {
                    goToStep(currentStep + 1);
                }
            });
        });

        prevButtons.forEach(button => {
            button.addEventListener('click', function () {
                goToStep(currentStep - 1);
            });
        });

        // Step validation
        function validateStep(step) {
            switch (step) {
                case 1:
                    // Check if a service is selected
                    return Array.from(serviceOptions).some(option => option.checked);

                case 2:
                    // Check if date and time are selected
                    return dateInput.value && Array.from(timeSlots).some(slot => slot.checked);

                case 3:
                    // Check vehicle information
                    const vehicleBrand = document.getElementById('vehicle_brand').value;
                    const vehicleModel = document.getElementById('vehicle_model').value;
                    const vehicleYear = document.getElementById('vehicle_year').value;
                    const plateNumber = document.getElementById('plate_number').value;

                    return vehicleBrand && vehicleModel && vehicleYear && plateNumber;

                default:
                    return true;
            }
        }

        // Update summary information
        function updateSummary() {
            // Service details
            const selectedService = document.querySelector('input[name="service_type"]:checked');
            if (selectedService) {
                summaryService.textContent = selectedService.value;
                summaryPrice.textContent = `Rp ${parseInt(selectedService.dataset.price).toLocaleString('id-ID')}`;
                summaryDuration.textContent = `${selectedService.dataset.duration} jam`;
            }

            // Schedule details
            if (dateInput.value) {
                summaryDate.textContent = new Date(dateInput.value).toLocaleDateString('id-ID');
            }

            const selectedTime = document.querySelector('input[name="time"]:checked');
            if (selectedTime) {
                summaryTime.textContent = selectedTime.value;
            }

            // Vehicle details
            const vehicleBrand = document.getElementById('vehicle_brand').value;
            const vehicleModel = document.getElementById('vehicle_model').value;
            const vehicleYear = document.getElementById('vehicle_year').value;
            const plateNumber = document.getElementById('plate_number').value;
            const notes = document.getElementById('notes').value;

            if (vehicleBrand && vehicleModel && vehicleYear) {
                summaryVehicle.textContent = `${vehicleBrand} ${vehicleModel} (${vehicleYear})`;
            }

            if (plateNumber) {
                summaryPlate.textContent = plateNumber;
            }

            if (notes) {
                summaryNotes.textContent = notes;
                notesContainer.classList.remove('hidden');
            } else {
                notesContainer.classList.add('hidden');
            }
        }

        // Service selection handling
        serviceOptions.forEach(option => {
            option.addEventListener('change', function () {
                // Update hidden fields
                servicePriceInput.value = this.dataset.price;
                serviceDurationInput.value = this.dataset.duration;

                // Update styling
                document.querySelectorAll('.service-option').forEach(card => {
                    card.classList.remove('border-blue-600', 'bg-blue-50');
                    card.classList.add('border-gray-200', 'hover:border-blue-200');
                });

                this.closest('.service-option').classList.add('border-blue-600', 'bg-blue-50');
                this.closest('.service-option').classList.remove('border-gray-200', 'hover:border-blue-200');

                // Enable next button
                nextButtons[0].disabled = false;
            });
        });

        // Time slot selection handling
        timeSlots.forEach(slot => {
            slot.addEventListener('change', function () {
                // Update styling
                document.querySelectorAll('.time-slot').forEach(card => {
                    card.classList.remove('border-blue-600', 'bg-blue-50');
                    card.classList.add('border-gray-200', 'hover:border-blue-200');
                });

                this.closest('.time-slot').classList.add('border-blue-600', 'bg-blue-50');
                this.closest('.time-slot').classList.remove('border-gray-200', 'hover:border-blue-200');

                // Check if we can enable next button
                nextButtons[1].disabled = !(dateInput.value && Array.from(timeSlots).some(s => s.checked));
            });
        });

        // Date input handling
        dateInput.addEventListener('change', function () {
            // Check if we can enable next button
            nextButtons[1].disabled = !(this.value && Array.from(timeSlots).some(s => s.checked));
        });

        // Setup initial button states
        nextButtons[0].disabled = !Array.from(serviceOptions).some(option => option.checked);
        nextButtons[1].disabled = !(dateInput.value && Array.from(timeSlots).some(s => s.checked));

        // Setup validation for vehicle information inputs
        const vehicleInputs = ['vehicle_brand', 'vehicle_model', 'vehicle_year', 'plate_number'];
        vehicleInputs.forEach(id => {
            document.getElementById(id).addEventListener('input', function () {
                nextButtons[2].disabled = !validateStep(3);
            });
        });
    });
</script>