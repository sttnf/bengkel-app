<?php
// Initialize form data
$_SESSION['form_data'] = $_SESSION['form_data'] ?? [
    'service_type' => '', 'service_id' => '', 'service_price' => '',
    'service_duration' => '', 'date' => date('Y-m-d'), 'time' => '',
    'vehicle_brand' => '', 'vehicle_model' => '', 'vehicle_year' => '',
    'plate_number' => '', 'notes' => ''
];

// Fetch services and user data
$serviceModel = new \App\Models\Service();
$services = $serviceModel->findAll();
$userId = $_SESSION['user_id'] ?? null;
$formData = &$_SESSION['form_data'];

// Get available time slots
$serviceRequestModel = new \App\Models\ServiceRequest();
$availableTimeSlots = !empty($formData['service_id'])
    ? $serviceRequestModel->getAvailableServiceTimes($formData['service_id'], $formData['date'])
    : [];

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_booking'])) {
    $serviceData = $serviceModel->findById($_POST['service_id']);
    if ($serviceData) {
        $formData = array_merge($formData, $_POST, [
            'service_price' => $serviceData['base_price'],
            'service_duration' => $serviceData['estimated_hours']
        ]);

        if ($userId) {
            $requestId = $serviceRequestModel->create([
                'user_id' => $userId, 'service_id' => $_POST['service_id'],
                'vehicle_brand' => $_POST['vehicle_brand'], 'vehicle_model' => $_POST['vehicle_model'],
                'vehicle_year' => $_POST['vehicle_year'], 'vehicle_vin' => $_POST['vehicle_vin'] ?? '',
                'vehicle_color' => $_POST['vehicle_color'] ?? '', 'plate_number' => $_POST['plate_number'],
                'notes' => $_POST['notes'], 'scheduled_date' => $_POST['date'],
                'scheduled_time' => $_POST['time'], 'estimated_price' => $serviceData['base_price'],
                'status' => 'pending'
            ]);

            if ($requestId) {
                $_SESSION['last_request_id'] = $requestId;
                $_SESSION['form_data'] = [];
                $_SESSION['success_message'] = "Reservasi layanan Anda telah berhasil dikirim!";
                header("Location: /service");
                exit();
            } else {
                $errorMessage = "Gagal menyimpan data. Silakan coba lagi.";
            }
        } else {
            $errorMessage = "Anda harus login untuk memesan layanan.";
        }
    } else {
        $errorMessage = "Layanan tidak ditemukan.";
    }
}

// Fetch vehicle data
$vehicleId = $_GET['vehicle_id'] ?? null;
if ($vehicleId) {
    $vehicleModel = new \App\Models\Vehicle();
    $vehicleData = $vehicleModel->findOneBy(
        ['customer_vehicles.id' => $vehicleId, 'customer_vehicles.user_id' => $userId],
        [['type' => 'LEFT', 'table' => 'vehicles', 'on' => 'vehicles.id = customer_vehicles.vehicle_id']]
    );

    if ($vehicleData) {
        $formData = array_merge($formData, [
            'vehicle_brand' => $vehicleData['brand'], 'vehicle_model' => $vehicleData['model'],
            'vehicle_year' => $vehicleData['year'], 'plate_number' => $vehicleData['license_plate'],
            'vehicle_vin' => $vehicleData['vin_number'] ?? '', 'vehicle_color' => $vehicleData['color'] ?? ''
        ]);
    } else {
        $errorMessage = "Kendaraan tidak ditemukan.";
    }
}
?>

<div class="bg-gray-50 py-10 px-4 sm:px-6 lg:px-8">
    <div class="max-w-2xl mx-auto">
        <div class="text-center mb-8">
            <h1 class="text-2xl font-semibold">Pesan Layanan</h1>
            <p class="mt-1 text-gray-500">Atur jadwal servis kendaraan Anda dengan mudah</p>
        </div>

        <?php if (isset($_SESSION['success_message'])): ?>
            <div class="mb-4 bg-green-50 border-l-4 border-green-500 text-green-700 p-3 rounded-md">
                <?= $_SESSION['success_message'] ?>
            </div>
            <?php unset($_SESSION['success_message']); ?>
        <?php endif; ?>

        <?php if (isset($errorMessage)): ?>
            <div class="mb-4 bg-red-50 border-l-4 border-red-500 text-red-700 p-3 rounded-md">
                <?= $errorMessage ?>
            </div>
        <?php endif; ?>

        <?php if (!$userId): ?>
            <div class="fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center">
                <div class="bg-white p-6 rounded-lg shadow-md max-w-md w-full">
                    <h2 class="text-lg font-semibold">Login Diperlukan</h2>
                    <p class="mt-2 text-gray-500">Anda harus login untuk melanjutkan pemesanan.</p>
                    <div class="mt-4 flex justify-center space-x-3">
                        <a href="/login?redirect=<?= urlencode($_SERVER['REQUEST_URI']) ?>"
                           class="px-4 py-2 bg-primary-500 text-white rounded-md hover:bg-primary-600">Login</a>
                        <a href="/register"
                           class="px-4 py-2 border border-gray-300 text-gray-600 rounded-md hover:bg-gray-100">Daftar</a>
                    </div>
                </div>
            </div>
        <?php endif; ?>

        <?php include 'partials/progress-steps.php'; ?>

        <div class="mt-6 bg-white rounded-lg shadow-sm border p-4">
            <?php if (isset($vehicleData)): ?>
                <div class="mb-4">
                    <h2 class="text-lg font-semibold">Kendaraan</h2>
                    <p class="text-gray-600"><span
                                class="font-medium">Merek:</span> <?= htmlspecialchars($vehicleData['brand']) ?></p>
                    <p class="text-gray-600"><span
                                class="font-medium">Model:</span> <?= htmlspecialchars($vehicleData['model']) ?></p>
                    <p class="text-gray-600"><span
                                class="font-medium">Tahun:</span> <?= htmlspecialchars($vehicleData['year']) ?></p>
                    <p class="text-gray-600"><span
                                class="font-medium">Plat:</span> <?= htmlspecialchars($vehicleData['license_plate']) ?>
                    </p>
                    <?php if (!empty($vehicleData['vin_number'])): ?>
                        <p class="text-gray-600"><span
                                    class="font-medium">VIN:</span> <?= htmlspecialchars($vehicleData['vin_number']) ?>
                        </p>
                    <?php endif; ?>
                </div>
                <hr class="my-4">
            <?php endif; ?>

            <form id="bookingForm" method="POST" class="space-y-4">
                <?php include 'partials/step-1-services.php'; ?>
                <?php include 'partials/step-2-schedule.php'; ?>
                <?php include 'partials/step-3-vehicle.php'; ?>
                <?php include 'partials/step-4-confirmation.php'; ?>

                <div class="mt-6 flex justify-between">
                    <button type="button" id="prevStepBtn"
                            class="prev-step hidden px-4 py-2 border text-gray-600 rounded-md hover:bg-gray-100">Kembali
                    </button>
                    <button type="button" id="nextStepBtn"
                            class="next-step px-4 py-2 bg-primary-500 text-white rounded-md hover:bg-primary-600">Lanjut
                    </button>
                    <button type="submit" id="submitBtn" name="submit_booking"
                            class="submit-step hidden px-4 py-2 bg-green-500 text-white rounded-md hover:bg-green-600">
                        Pesan
                    </button>
                </div>

                <input type="hidden" name="service_id" id="service_id"
                       value="<?= htmlspecialchars($formData['service_id'] ?? '') ?>">
                <input type="hidden" name="service_price" id="service_price"
                       value="<?= htmlspecialchars($formData['service_price'] ?? '') ?>">
                <input type="hidden" name="service_duration" id="service_duration"
                       value="<?= htmlspecialchars($formData['service_duration'] ?? '') ?>">
            </form>
        </div>
    </div>
</div>

<script src="/js/service/booking-form.js"></script>