<?php
$pageTitle = "My Service Requests";
$pageHeader = "Service Requests";

$activeRequests = [
    ['id' => 101, 'service' => 'Oil Change', 'status' => 'pending', 'date' => '2025-05-06', 'vehicle' => 'Toyota Corolla 2020'],
    ['id' => 102, 'service' => 'Brake Inspection', 'status' => 'in_progress', 'date' => '2025-05-05', 'vehicle' => 'Honda Jazz 2021'],
];

$requestHistory = [
    ['id' => 89, 'service' => 'Full Inspection', 'status' => 'completed', 'date' => '2025-04-25', 'vehicle' => 'Toyota Corolla 2020'],
    ['id' => 75, 'service' => 'Battery Replacement', 'status' => 'completed', 'date' => '2025-03-18', 'vehicle' => 'Honda Jazz 2021'],
];

function getStatusBadge($status)
{
    return match ($status) {
        'pending' => '<span class="text-amber-600 bg-amber-100 text-xs font-medium px-2 py-1 rounded-full">Pending</span>',
        'in_progress' => '<span class="text-blue-600 bg-blue-100 text-xs font-medium px-2 py-1 rounded-full">In Progress</span>',
        'completed' => '<span class="text-green-600 bg-green-100 text-xs font-medium px-2 py-1 rounded-full">Completed</span>',
        default => '<span class="text-gray-600 bg-gray-100 text-xs font-medium px-2 py-1 rounded-full">Unknown</span>',
    };
}

ob_start();
?>

<!-- Action Button -->
<div class="flex justify-end mb-4">
    <a href="new-service-request.php"
       class="inline-flex items-center px-4 py-2 bg-blue-600 text-white text-sm rounded-lg hover:bg-blue-700 transition">
        <i data-lucide="plus-circle" class="w-4 h-4 mr-2"></i> New Request
    </a>
</div>

<!-- Active Requests -->
<div class="bg-white rounded-xl shadow-sm border p-4 mb-6">
    <h2 class="text-lg font-semibold text-gray-700 mb-4">Active Requests</h2>
    <?php if (empty($activeRequests)): ?>
        <p class="text-sm text-gray-500">No active service requests.</p>
    <?php else: ?>
        <div class="space-y-3">
            <?php foreach ($activeRequests as $request): ?>
                <div class="border-l-4 pl-4 py-2 bg-gray-50 rounded border-blue-400">
                    <div class="flex justify-between">
                        <div>
                            <h3 class="font-medium text-gray-800"><?= $request['service'] ?></h3>
                            <p class="text-sm text-gray-500"><?= $request['vehicle'] ?> — <?= $request['date'] ?></p>
                        </div>
                        <div><?= getStatusBadge($request['status']) ?></div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

<!-- Request History -->
<div class="bg-white rounded-xl shadow-sm border p-4">
    <h2 class="text-lg font-semibold text-gray-700 mb-4">Request History</h2>
    <?php if (empty($requestHistory)): ?>
        <p class="text-sm text-gray-500">No service history available.</p>
    <?php else: ?>
        <div class="space-y-3">
            <?php foreach ($requestHistory as $history): ?>
                <div class="border-l-4 pl-4 py-2 bg-gray-50 rounded border-green-400">
                    <div class="flex justify-between">
                        <div>
                            <h3 class="font-medium text-gray-800"><?= $history['service'] ?></h3>
                            <p class="text-sm text-gray-500"><?= $history['vehicle'] ?> — <?= $history['date'] ?></p>
                        </div>
                        <div><?= getStatusBadge($history['status']) ?></div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

<?php
$content = ob_get_clean();
echo $content;
?>
