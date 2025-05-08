<?php
$_SESSION['form_data'] = $_SESSION['form_data'] ?? [
    'service_type' => '', 'service_id' => '', 'service_price' => '',
    'service_duration' => '', 'date' => '', 'time' => '',
    'vehicle_brand' => '', 'vehicle_model' => '', 'vehicle_year' => '',
    'plate_number' => '', 'notes' => ''
];

$serviceModel = new \App\Models\Service();
$services = $serviceModel->findAll();
$userId = $_SESSION['user_id'] ?? null;
$serviceRequestModel = new \App\Models\ServiceRequest();

$formData = &$_SESSION['form_data'];
$formData['date'] = $_POST['date'] ?? $formData['date'] ?? date('Y-m-d');
$formData['service_id'] = $_POST['service_id'] ?? $formData['service_id'] ?? null;

$availableTimeSlots = $formData['service_id']
    ? $serviceRequestModel->getAvailableServiceTimes($formData['service_id'], $formData['date'])
    : [];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_booking'])) {
    $serviceData = $serviceModel->findById($_POST['service_id']);


    if ($serviceData) {
        $formData = array_merge($formData, [
            'service_id' => $_POST['service_id'],
            'service_type' => $_POST['service_type'],
            'service_price' => $serviceData['base_price'],
            'service_duration' => $serviceData['estimated_hours'],
            'date' => $_POST['date'],
            'time' => $_POST['time'],
            'vehicle_brand' => $_POST['vehicle_brand'],
            'vehicle_model' => $_POST['vehicle_model'],
            'vehicle_year' => $_POST['vehicle_year'],
            'plate_number' => $_POST['plate_number'],
            'notes' => $_POST['notes']
        ]);

        if ($userId) {
            $requestId = $serviceRequestModel->create([
                'user_id' => $userId,
                'service_id' => $_POST['service_id'],
                'vehicle_brand' => $_POST['vehicle_brand'],
                'vehicle_model' => $_POST['vehicle_model'],
                'vehicle_year' => $_POST['vehicle_year'],
                'vehicle_vin' => $_POST['vehicle_vin'],
                'vehicle_color' => $_POST['vehicle_color'],
                'plate_number' => $_POST['plate_number'],
                'notes' => $_POST['notes'],
                'scheduled_date' => $_POST['date'],
                'scheduled_time' => $_POST['time'],
                'estimated_price' => $serviceData['base_price'],
                'status' => 'pending',
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
        $errorMessage = "Layanan tidak ditemukan. Silakan pilih layanan yang valid.";
    }
}
?>

<div class="max-w-3xl mx-auto py-12 px-4">
    <div class="text-center mb-8">
        <h1 class="text-3xl font-bold text-gray-900">Pesan Layanan</h1>
        <p class="mt-2 text-gray-600">Isi formulir di bawah untuk memesan layanan</p>
    </div>

    <?php if (isset($_SESSION['success_message'])): ?>
        <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative" role="alert">
            <span class="block sm:inline"><?= $_SESSION['success_message'] ?></span>
        </div>
        <?php unset($_SESSION['success_message']); ?>
    <?php endif; ?>

    <?php if (isset($errorMessage)): ?>
        <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">
            <span class="block sm:inline"><?= $errorMessage ?></span>
        </div>
    <?php endif; ?>

    <?php if (!$userId): ?>
        <div class="fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center">
            <div class="bg-white p-8 rounded-lg max-w-md w-full">
                <div class="text-center">
                    <h2 class="mt-2 text-xl font-bold">Login Diperlukan</h2>
                    <p class="mt-2 text-gray-600">Anda harus login untuk memesan layanan.</p>
                    <div class="mt-4">
                        <a href="/login?redirect=<?= urlencode($_SERVER['REQUEST_URI']) ?>"
                           class="inline-block px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">
                            Login Sekarang
                        </a>
                        <a href="/register"
                           class="inline-block ml-2 px-4 py-2 border border-gray-300 text-gray-700 rounded hover:bg-gray-50">
                            Daftar
                        </a>
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?>

    <?php include 'partials/progress-steps.php'; ?>

    <div class="bg-white rounded-lg shadow-sm p-6">
        <form id="bookingForm" method="POST">
            <?php include 'partials/step-1-services.php'; ?>
            <?php include 'partials/step-2-schedule.php'; ?>
            <?php include 'partials/step-3-vehicle.php'; ?>
            <?php include 'partials/step-4-confirmation.php'; ?>

            <div class="mt-8 flex justify-between">
                <button type="button" id="prevStepBtn"
                        class="prev-step hidden px-6 py-2 border text-gray-700 rounded-lg hover:bg-gray-50">
                    <svg class="inline-block h-5 w-5 mr-2" xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                         viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                         stroke-linejoin="round">
                        <path d="m15 18-6-6 6-6"></path>
                    </svg>
                    Kembali
                </button>
                <button type="button" id="nextStepBtn"
                        class="next-step px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 disabled:opacity-50">
                    Lanjut
                    <svg class="inline-block h-5 w-5 ml-2" xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                         viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                         stroke-linejoin="round">
                        <path d="m9 18 6-6-6-6"></path>
                    </svg>
                </button>
                <button type="submit" id="submitBtn" name="submit_booking"
                        class="submit-step hidden px-6 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700">
                    Pesan Sekarang
                    <svg class="inline-block h-5 w-5 ml-2" xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                         viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                         stroke-linejoin="round">
                        <polyline points="20 6 9 17 4 12"></polyline>
                    </svg>
                </button>
            </div>

            <input type="hidden" name="service_id" id="service_id"
                   value="<?= htmlspecialchars($formData['service_id'] ?? '') ?>">
            <input type="hidden" name="service_price" id="service_price"
                   value="<?= htmlspecialchars((string)($formData['service_price'] ?? '')) ?>">
            <input type="hidden" name="service_duration" id="service_duration"
                   value="<?= htmlspecialchars((string)($formData['service_duration'] ?? '')) ?>">
        </form>
    </div>
</div>

<script src="/js/service/booking-form.js"></script>