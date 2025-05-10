<?php
$pageTitle = "Analytics Dashboard";
$pageHeader = "Analytics Overview";

$analytics = $analytics ?? [];

function formatIDR($amount): string
{
    return 'Rp ' . number_format($amount, 0, ',', '.');
}

function getSummaryStats(array $analytics): array
{
    return [
        ['label' => 'Total Requests', 'value' => $analytics['services']['total'] ?? 0, 'icon' => 'clipboard-check'],
        ['label' => 'Completed', 'value' => $analytics['services']['completed'] ?? 0, 'icon' => 'check-circle'],
        ['label' => 'Pending', 'value' => $analytics['services']['pending'] ?? 0, 'icon' => 'clock'],
        ['label' => 'In Progress', 'value' => $analytics['services']['in_progress'] ?? 0, 'icon' => 'cog'],
    ];
}

function getRevenueStats(array $analytics): array
{
    $totalRevenue = $analytics['revenue']['total'] ?? 0;
    $completedServices = $analytics['services']['completed'] ?? 0;

    return [
        'totalRevenue' => $totalRevenue,
        'avgRevenuePerOrder' => $completedServices > 0 ? $totalRevenue / $completedServices : 0,
        'highestSingleOrder' => $analytics['revenue']['monthly'][0]['total_revenue'] ?? 0,
    ];
}

function getMonthlyRevenueData(array $analytics): array
{
    $monthlyData = $analytics['revenue']['monthly'] ?? [];
    return [
        'labels' => array_map(fn($item) => date('M Y', strtotime($item['month'])), $monthlyData),
        'values' => array_map(fn($item) => $item['total_revenue'], $monthlyData),
    ];
}

$summaryStats = getSummaryStats($analytics);
$revenueStats = getRevenueStats($analytics);
$monthlyRevenueData = getMonthlyRevenueData($analytics);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title><?= $pageTitle ?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" crossorigin="anonymous">
</head>
<body class="bg-gray-50 font-sans text-gray-900">
<div class="max-w-7xl mx-auto p-6 space-y-6">
    <header>
        <h1 class="text-3xl font-bold"><?= $pageHeader ?></h1>
        <p class="text-sm text-gray-600 mt-1">Kelola data analitik bengkel Anda dengan mudah dan efisien.</p>
    </header>

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <?php foreach ($summaryStats as $stat): ?>
            <div class="bg-white rounded-2xl border shadow-sm p-5 flex items-center gap-4 hover:shadow-md transition">
                <div class="text-2xl text-<?= match ($stat['label']) {
                    'Completed' => 'green-500',
                    'Pending' => 'yellow-500',
                    'In Progress' => 'blue-500',
                    default => 'indigo-500'
                } ?>">
                    <i class="fa-solid fa-<?= $stat['icon'] ?>"></i>
                </div>
                <div>
                    <h3 class="text-sm font-medium text-gray-500"><?= $stat['label'] ?></h3>
                    <p class="text-xl font-semibold"><?= $stat['value'] ?></p>
                </div>
            </div>
        <?php endforeach; ?>
    </div>

    <section class="bg-white rounded-2xl border shadow-sm p-6">
        <h2 class="text-xl font-semibold mb-4 flex items-center gap-2">
            <i class="fa-solid fa-coins text-yellow-500"></i> Ringkasan Pendapatan
        </h2>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div class="bg-gray-50 p-4 rounded-lg">
                <p class="text-sm text-gray-500 mb-1"><i class="fa-solid fa-sack-dollar text-green-500 mr-1"></i> Total Revenue</p>
                <p class="text-lg font-semibold text-green-600"><?= formatIDR($revenueStats['totalRevenue']) ?></p>
            </div>
            <div class="bg-gray-50 p-4 rounded-lg">
                <p class="text-sm text-gray-500 mb-1"><i class="fa-solid fa-calculator text-blue-500 mr-1"></i> Avg per Order</p>
                <p class="text-lg font-semibold text-green-600"><?= formatIDR($revenueStats['avgRevenuePerOrder']) ?></p>
            </div>
            <div class="bg-gray-50 p-4 rounded-lg">
                <p class="text-sm text-gray-500 mb-1"><i class="fa-solid fa-arrow-up-right-dots text-indigo-500 mr-1"></i> Highest Order</p>
                <p class="text-lg font-semibold text-green-600"><?= formatIDR($revenueStats['highestSingleOrder']) ?></p>
            </div>
        </div>
    </section>

    <section class="bg-white rounded-2xl border shadow-sm p-6">
        <h2 class="text-xl font-semibold mb-4 flex items-center gap-2">
            <i class="fa-solid fa-chart-line text-blue-500"></i> Tren Bulanan
        </h2>
        <div class="relative h-72">
            <canvas id="monthlyRevenueChart"></canvas>
        </div>
    </section>
</div>

<script>
    new Chart(document.getElementById('monthlyRevenueChart'), {
        type: 'line',
        data: {
            labels: <?= json_encode($monthlyRevenueData['labels']) ?>,
            datasets: [{
                label: 'Pendapatan Bulanan (Rp)',
                data: <?= json_encode($monthlyRevenueData['values']) ?>,
                fill: true,
                borderColor: 'rgb(59, 130, 246)',
                backgroundColor: 'rgba(59, 130, 246, 0.1)',
                tension: 0.3,
                pointRadius: 4,
                pointHoverRadius: 6,
                pointBackgroundColor: 'rgb(59, 130, 246)'
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: { legend: { display: false } },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: { callback: value => 'Rp ' + value.toLocaleString('id-ID') }
                }
            }
        }
    });
</script>
</body>
</html>