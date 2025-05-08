<?php
$title = "Pembayaran Servis";
$layout = "main";
?>

<div class="bg-gray-100 py-12">
    <div class="max-w-md mx-auto bg-white shadow-lg rounded-lg overflow-hidden">
        <div class="px-6 py-8">
            <h2 class="text-2xl font-semibold text-gray-800 mb-6">Pembayaran Servis</h2>

            <?php if (!empty($error)): ?>
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                    <?= htmlspecialchars($error) ?>
                </div>
            <?php endif; ?>

            <div class="mb-6">
                <h3 class="text-lg font-semibold text-gray-700 mb-2">Detail Servis</h3>
                <div class="bg-gray-50 rounded-md p-4 space-y-2 text-sm text-gray-600">
                    <?php
                    $details = [
                        'Jenis Servis' => $service['service_name'] ?? '-',
                        'Kendaraan' => ($service['vehicle_brand'] ?? '-') . ' ' . ($service['vehicle_model'] ?? '') . ' (' . ($service['vehicle_year'] ?? '-') . ')',
                        'Nomor Plat' => $service['license_plate'] ?? '-',
                        'Status' => str_replace('_', ' ', $service['status'] ?? '-'),
                        'Jadwal' => isset($service['scheduled_datetime']) ? date('d M Y, H:i', strtotime($service['scheduled_datetime'])) : '-',
                        'Total Biaya' => 'Rp ' . (isset($service['price']) ? number_format((float)$service['price'], 0, ',', '.') : '0')
                    ];
                    foreach ($details as $label => $value): ?>
                        <div class="flex justify-between">
                            <span class="text-gray-500"><?= $label ?></span>
                            <span class="font-medium text-gray-800"><?= htmlspecialchars($value) ?></span>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <form method="post" class="space-y-4">
                <input type="hidden" name="service_id" value="<?= $service['id'] ?? '' ?>" type="number">
                <input type="hidden" id="real_amount" name="amount"
                       value="<?= htmlspecialchars($service['price'] ?? '0') ?>">

                <div>
                    <label for="formatted_amount" class="block text-sm font-medium text-gray-700 mb-2">Jumlah
                        Pembayaran</label>
                    <div class="relative rounded-md shadow-sm">
                        <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-500">Rp</span>
                        <input type="text" id="formatted_amount"
                               class="pl-10 pr-3 py-2 block w-full rounded-md border-gray-300 focus:ring-blue-500 focus:border-blue-500"
                               value="<?= number_format((float)($service['price'] ?? 0), 0, ',', '.') ?>">
                    </div>
                </div>

                <div>
                    <label for="payment_method" class="block text-sm font-medium text-gray-700 mb-2">Metode
                        Pembayaran</label>
                    <select id="payment_method" name="payment_method" required
                            class="block w-full bg-white border-gray-300 px-4 py-2 rounded shadow focus:ring-blue-500">
                        <option value="cash">Tunai</option>
                        <option value="qris">QRIS</option>
                    </select>
                </div>

                <div class="flex justify-end items-center pt-4 gap-4">
                    <a href="/dashboard"
                       class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border-gray-300 rounded-md shadow hover:bg-gray-50">←
                        Kembali</a>
                    <button type="submit"
                            class="px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-md shadow hover:bg-blue-700">
                        Bayar Sekarang →
                    </button>
                </div>
            </form>

            <script>
                document.addEventListener('DOMContentLoaded', () => {
                    const formattedAmountInput = document.getElementById('formatted_amount');
                    const realAmountInput = document.getElementById('real_amount');

                    formattedAmountInput.addEventListener('input', () => {
                        const value = formattedAmountInput.value.replace(/\D/g, '');
                        realAmountInput.value = value;
                        formattedAmountInput.value = new Intl.NumberFormat('id-ID').format(value);
                    });
                });
            </script>
        </div>
    </div>
</div>