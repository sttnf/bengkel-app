<?php
$pageTitle = "User Dashboard - Auto Service Management";
$pageHeader = "User Dashboard";

// Dummy Data
$userProfile = [
    'name' => 'Rizky Maulana',
    'email' => 'rizky@example.com',
    'phone' => '+62 812 3456 7890',
    'vehicles' => 2,
    'last_login' => '2025-05-06 20:30',
];

$currentServiceRequests = [
    ['id' => 1, 'service' => 'Oil Change', 'status' => 'pending', 'date' => '2025-05-05', 'vehicle' => 'Toyota Corolla 2020'],
    ['id' => 2, 'service' => 'Brake Replacement', 'status' => 'in_progress', 'date' => '2025-05-04', 'vehicle' => 'Honda Civic 2019'],
];

$serviceHistory = [
    ['service' => 'Tire Rotation', 'date' => '2025-04-20', 'vehicle' => 'Toyota Corolla 2020'],
    ['service' => 'Full Inspection', 'date' => '2025-03-15', 'vehicle' => 'Honda Civic 2019'],
];

$paymentHistory = [
    ['payment_id' => 'P-12345', 'amount' => 250000, 'status' => 'completed', 'date' => '2025-05-03'],
    ['payment_id' => 'P-12346', 'amount' => 100000, 'status' => 'completed', 'date' => '2025-04-22'],
];

$quickActions = [
    ['link' => 'new-service-request.php', 'icon' => 'plus-circle', 'label' => 'Request Service', 'color' => 'blue'],
    ['link' => 'vehicles.php', 'icon' => 'car', 'label' => 'My Vehicles', 'color' => 'green'],
    ['link' => 'account-settings.php', 'icon' => 'settings', 'label' => 'Account Settings', 'color' => 'violet'],
    ['link' => 'support.php', 'icon' => 'life-buoy', 'label' => 'Support', 'color' => 'rose'],
];

ob_start();
?>

<!-- User Overview -->
<div class="bg-white p-6 rounded-2xl shadow-sm border mb-6">
    <h2 class="text-xl font-semibold text-gray-800">Welcome, <?= $userProfile['name'] ?></h2>
    <p class="text-sm text-gray-500">Last login: <?= $userProfile['last_login'] ?></p>
</div>

<!-- Dashboard Grid -->
<div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

    <!-- Current Service Requests -->
    <div class="bg-white p-5 rounded-2xl shadow-sm border">
        <div class="flex justify-between items-center mb-3">
            <h3 class="text-lg font-semibold text-gray-700">Ongoing Service Requests</h3>
            <a href="current-service-requests.php" class="text-sm text-blue-600 hover:underline">View All</a>
        </div>
        <div class="space-y-3">
            <?php foreach ($currentServiceRequests as $request): ?>
                <?php $statusColor = match ($request['status']) {
                    'pending' => 'amber',
                    'in_progress' => 'blue',
                    'completed' => 'emerald',
                    default => 'gray'
                }; ?>
                <div class="p-3 bg-gray-50 border-l-4 border-<?= $statusColor ?>-500 rounded">
                    <div class="flex justify-between text-sm font-medium text-gray-800">
                        <?= $request['service'] ?> <span class="text-xs text-gray-400">#<?= $request['id'] ?></span>
                    </div>
                    <div class="text-sm text-gray-500">
                        <p><?= $request['vehicle'] ?></p>
                        <p><?= ucfirst(str_replace('_', ' ', $request['status'])) ?> - <?= $request['date'] ?></p>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>

    <!-- Service History -->
    <div class="bg-white p-5 rounded-2xl shadow-sm border">
        <h3 class="text-lg font-semibold text-gray-700 mb-3">Service History</h3>
        <div class="space-y-3">
            <?php foreach ($serviceHistory as $history): ?>
                <div class="p-3 bg-gray-50 border-l-4 border-green-500 rounded">
                    <div class="text-sm font-medium text-gray-800"><?= $history['service'] ?></div>
                    <div class="text-sm text-gray-500"><?= $history['vehicle'] ?> — <?= $history['date'] ?></div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>

    <!-- Payment History -->
    <div class="bg-white p-5 rounded-2xl shadow-sm border">
        <h3 class="text-lg font-semibold text-gray-700 mb-3">Payment History</h3>
        <div class="space-y-3">
            <?php foreach ($paymentHistory as $payment): ?>
                <div class="p-3 bg-gray-50 border-l-4 border-blue-500 rounded">
                    <div class="flex justify-between text-sm text-gray-800 font-medium">
                        Payment #<?= $payment['payment_id'] ?>
                        <span class="text-xs text-gray-400"><?= $payment['date'] ?></span>
                    </div>
                    <div class="text-sm text-gray-500">
                        <p>Rp <?= number_format($payment['amount'], 0, ',', '.') ?></p>
                        <p>Status: <?= ucfirst($payment['status']) ?></p>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="bg-white p-5 rounded-2xl shadow-sm border">
        <h3 class="text-lg font-semibold text-gray-700 mb-3">Quick Actions</h3>
        <div class="grid grid-cols-2 gap-4">
            <?php foreach ($quickActions as $action): ?>
                <a href="<?= $action['link'] ?>"
                   class="flex flex-col items-center justify-center bg-gray-50 rounded-lg p-4 hover:bg-gray-100 transition">
                    <div class="p-2 bg-<?= $action['color'] ?>-100 text-<?= $action['color'] ?>-600 rounded-full mb-2">
                        <i data-lucide="<?= $action['icon'] ?>" class="w-5 h-5"></i>
                    </div>
                    <span class="text-sm font-medium text-gray-700"><?= $action['label'] ?></span>
                </a>
            <?php endforeach; ?>
        </div>
    </div>

</div>

<?php
$content = ob_get_clean();
echo $content;
?>
