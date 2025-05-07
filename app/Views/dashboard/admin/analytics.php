<?php
$pageTitle = "Analytics Dashboard";
$pageHeader = "Analytics Overview";

// Dummy Summary Stats
$summaryStats = [
    ['label' => 'Total Requests', 'value' => 236],
    ['label' => 'Completed', 'value' => 198],
    ['label' => 'Pending', 'value' => 24],
    ['label' => 'In Progress', 'value' => 14],
];

// Dummy Revenue Stats
$revenueStats = [
    'totalRevenue' => 12750000, // in IDR
    'avgRevenuePerOrder' => 64419, // 12750000 / 198
    'highestSingleOrder' => 320000,
];

// Recent Activities
$recentActivities = [
    ['activity' => 'Completed oil change for Toyota Corolla 2020', 'date' => '2025-05-06'],
    ['activity' => 'New brake inspection request for Honda Jazz 2021', 'date' => '2025-05-05'],
    ['activity' => 'Battery replaced on Nissan Livina 2018', 'date' => '2025-05-04'],
    ['activity' => 'Full inspection completed on Toyota Innova 2016', 'date' => '2025-05-03'],
];

function formatIDR($amount)
{
    return "Rp " . number_format($amount, 0, ',', '.');
}

ob_start();
?>

<!-- Summary Cards -->
<div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
    <?php foreach ($summaryStats as $stat): ?>
        <div class="bg-white border rounded-xl shadow-sm p-4 text-center">
            <h3 class="text-sm text-gray-500 font-medium"><?= $stat['label'] ?></h3>
            <p class="text-2xl font-semibold text-blue-600"><?= $stat['value'] ?></p>
        </div>
    <?php endforeach; ?>
</div>

<!-- Revenue Section -->
<div class="bg-white rounded-xl shadow-sm border p-4 mb-6">
    <h2 class="text-lg font-semibold text-gray-700 mb-4">Revenue Summary</h2>
    <div class="grid md:grid-cols-3 gap-4">
        <div class="bg-gray-50 p-4 rounded-lg">
            <p class="text-sm text-gray-500">Total Revenue</p>
            <p class="text-xl font-semibold text-green-600"><?= formatIDR($revenueStats['totalRevenue']) ?></p>
        </div>
        <div class="bg-gray-50 p-4 rounded-lg">
            <p class="text-sm text-gray-500">Avg per Order</p>
            <p class="text-xl font-semibold text-green-600"><?= formatIDR($revenueStats['avgRevenuePerOrder']) ?></p>
        </div>
        <div class="bg-gray-50 p-4 rounded-lg">
            <p class="text-sm text-gray-500">Highest Order</p>
            <p class="text-xl font-semibold text-green-600"><?= formatIDR($revenueStats['highestSingleOrder']) ?></p>
        </div>
    </div>
</div>

<!-- Placeholder for Chart -->
<div class="bg-white rounded-xl shadow-sm border p-4 mb-6">
    <h2 class="text-lg font-semibold text-gray-700 mb-4">Monthly Trend</h2>
    <div class="h-48 flex items-center justify-center bg-gray-100 rounded text-gray-400 text-sm">
        Chart Placeholder (e.g., Chart.js or image)
    </div>
</div>

<!-- Recent Activities -->
<div class="bg-white rounded-xl shadow-sm border p-4">
    <h2 class="text-lg font-semibold text-gray-700 mb-4">Recent Activity</h2>
    <?php if (empty($recentActivities)): ?>
        <p class="text-sm text-gray-500">No recent activity.</p>
    <?php else: ?>
        <ul class="space-y-3">
            <?php foreach ($recentActivities as $item): ?>
                <li class="border-l-4 pl-4 py-2 bg-gray-50 rounded border-gray-300">
                    <div class="flex justify-between">
                        <p class="text-gray-700 text-sm"><?= $item['activity'] ?></p>
                        <span class="text-xs text-gray-400"><?= $item['date'] ?></span>
                    </div>
                </li>
            <?php endforeach; ?>
        </ul>
    <?php endif; ?>
</div>

<?php
$content = ob_get_clean();
echo $content;
?>
