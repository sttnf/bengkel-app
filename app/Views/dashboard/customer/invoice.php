<?php
$payment = array_merge([
    'id' => '',
    'payment_date' => date('Y-m-d H:i:s'),
    'user_name' => '',
    'user_email' => '',
    'service_name' => '',
    'vehicle_brand' => '',
    'vehicle_model' => '',
    'vehicle_year' => '',
    'license_plate' => '',
    'technician_name' => 'Belum ditentukan',
    'amount' => 0,
    'payment_method' => '',
    'status' => 'pending'
], $payment ?? []);

$title = "Invoice Pembayaran #" . $payment['id'];

$statusColorClass = match (strtolower($payment['status'])) {
    'completed', 'lunas' => 'text-green-600',
    'pending' => 'text-yellow-600',
    'failed', 'gagal' => 'text-red-600',
    default => 'text-gray-600',
};
?>

<style media="print">
    @media print {
        .no-print {
            display: none !important;
        }

        body {
            background-color: #fff;
        }

        .container {
            max-width: 100% !important;
        }
    }
</style>

<div class="bg-gray-100 min-h-screen py-8 px-4 sm:px-6 lg:px-8 font-sans antialiased">
    <div class="max-w-3xl mx-auto">
        <div class="mb-4 text-right no-print">
            <button onclick="window.print()"
                    class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-lg text-sm font-medium">
                <i data-lucide="printer" class="w-4 h-4 inline-block mr-1"></i> Cetak Invoice
            </button>
        </div>

        <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
            <div class="p-6 bg-gray-50 border-b">
                <div class="flex justify-between items-center">
                    <div>
                        <h1 class="text-2xl font-semibold text-gray-800 tracking-tight">Invoice</h1>
                        <p class="text-gray-500 text-sm">No. #<?= $payment['id'] ?></p>
                    </div>
                    <div class="text-right">
                        <p class="text-sm text-gray-600">Tanggal Pembayaran</p>
                        <p class="font-medium text-gray-800"><?= date('d M Y, H:i', strtotime($payment['payment_date'])) ?></p>
                    </div>
                </div>
            </div>

            <div class="p-6 space-y-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <h3 class="text-lg font-semibold text-gray-700 mb-2">Informasi Pelanggan</h3>
                        <p class="text-gray-700 font-medium"><?= htmlspecialchars($payment['user_name']) ?></p>
                        <p class="text-gray-600 text-sm"><?= htmlspecialchars($payment['user_email']) ?></p>
                    </div>
                    <div>
                        <h3 class="text-lg font-semibold text-gray-700 mb-2">Informasi Bengkel</h3>
                        <p class="text-gray-700 font-medium">Bengkel Kita</p>
                        <p class="text-gray-600 text-sm">Jl. In Aja Dulu No. 123</p>
                        <p class="text-gray-600 text-sm">Jakarta, Indonesia</p>
                        <p class="text-gray-600 text-sm">Telp: (021) 123-4567</p>
                    </div>
                </div>

                <div>
                    <h3 class="text-lg font-semibold text-gray-700 mb-2">Detail Servis</h3>
                    <div class="bg-gray-50 rounded-lg overflow-hidden border border-gray-200">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-100">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Deskripsi
                                </th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Harga
                                </th>
                            </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                            <tr>
                                <td class="px-6 py-4">
                                    <p class="text-sm font-medium text-gray-900"><?= htmlspecialchars($payment['service_name']) ?></p>
                                    <p class="text-gray-500 text-xs">
                                        Kendaraan: <?= htmlspecialchars($payment['vehicle_brand']) ?> <?= htmlspecialchars($payment['vehicle_model']) ?>
                                        (<?= htmlspecialchars($payment['vehicle_year']) ?>)</p>
                                    <p class="text-gray-500 text-xs">Nomor
                                        Plat: <?= htmlspecialchars($payment['license_plate']) ?></p>
                                    <p class="text-gray-500 text-xs">
                                        Teknisi: <?= htmlspecialchars($payment['technician_name'] ?? 'Belum ditentukan') ?></p>
                                </td>
                                <td class="px-6 py-4 text-right text-sm font-medium text-gray-900">
                                    Rp <?= number_format($payment['amount'], 0, ',', '.') ?>
                                </td>
                            </tr>
                            </tbody>
                            <tfoot class="bg-gray-50">
                            <tr>
                                <td class="px-6 py-3 text-right text-sm font-medium text-gray-500">Total</td>
                                <td class="px-6 py-3 text-right text-sm font-bold text-gray-900">
                                    Rp <?= number_format($payment['amount'], 0, ',', '.') ?>
                                </td>
                            </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>

                <div class="bg-gray-50 p-4 rounded-lg border border-gray-200">
                    <h3 class="text-sm font-medium text-gray-700 mb-2">Metode Pembayaran</h3>
                    <p class="text-gray-900 font-medium"><?= strtoupper(str_replace('_', ' ', $payment['payment_method'])) ?></p>
                    <p class="font-medium mt-2 <?= $statusColorClass ?>">Status: <?= ucfirst($payment['status']) ?></p>
                </div>

                <div class="border-t pt-4 text-sm text-center text-gray-500">
                    <p>Terima kasih telah menggunakan layanan Bengkel Kita.</p>
                    <p>Untuk pertanyaan, silakan hubungi (021) 123-4567 atau email ke info@kita.blue</p>
                </div>
            </div>
        </div>
    </div>
</div>