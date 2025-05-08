<!-- app/Views/dashboard/customer/invoice.php -->
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
?>

<style type="text/css" media="print">
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

<div class="container mx-auto px-4 py-8" id="invoice-container">
    <div class="max-w-3xl mx-auto">
        <div class="mb-4 text-right no-print">
            <button onclick="window.print()" class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-lg">
                <i data-lucide="printer" class="w-4 h-4 inline-block mr-1"></i> Print Invoice
            </button>
        </div>

        <div class="bg-white rounded-lg shadow-md overflow-hidden">
            <div class="p-6 bg-gray-50 border-b">
                <div class="flex justify-between items-center">
                    <div>
                        <h1 class="text-2xl font-bold text-gray-800">Invoice</h1>
                        <p class="text-gray-500">No. #<?= $payment['id'] ?></p>
                    </div>
                    <div class="text-right">
                        <p class="text-sm text-gray-600">Tanggal Pembayaran</p>
                        <p class="font-medium"><?= date('d M Y, H:i', strtotime($payment['payment_date'])) ?></p>
                    </div>
                </div>
            </div>

            <div class="p-6 space-y-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <h3 class="text-lg font-semibold text-gray-700 mb-3">Informasi Pelanggan</h3>
                        <p class="text-gray-700 font-medium"><?= htmlspecialchars($payment['user_name']) ?></p>
                        <p class="text-gray-600"><?= htmlspecialchars($payment['user_email']) ?></p>
                    </div>
                    <div>
                        <h3 class="text-lg font-semibold text-gray-700 mb-3">Informasi Bengkel</h3>
                        <p class="text-gray-700 font-medium">Bengkel Kita</p>
                        <p class="text-gray-600">Jl. In Aja Dulu No. 123</p>
                        <p class="text-gray-600">Jakarta, Indonesia</p>
                        <p class="text-gray-600">Telp: (021) 123-4567</p>
                    </div>
                </div>

                <div>
                    <h3 class="text-lg font-semibold text-gray-700 mb-3">Detail Servis</h3>
                    <div class="bg-gray-50 rounded-lg overflow-hidden border">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-100">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Deskripsi
                                </th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Harga</th>
                            </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                            <tr>
                                <td class="px-6 py-4">
                                    <p class="text-sm font-medium text-gray-900"><?= htmlspecialchars($payment['service_name']) ?></p>
                                    <p class="text-sm text-gray-500">
                                        Kendaraan: <?= htmlspecialchars($payment['vehicle_brand']) ?> <?= htmlspecialchars($payment['vehicle_model']) ?>
                                        (<?= htmlspecialchars($payment['vehicle_year']) ?>)</p>
                                    <p class="text-sm text-gray-500">Nomor
                                        Plat: <?= htmlspecialchars($payment['license_plate']) ?></p>
                                    <p class="text-sm text-gray-500">
                                        Teknisi: <?= htmlspecialchars($payment['technician_name'] ?? 'Belum ditentukan') ?></p>
                                </td>
                                <td class="px-6 py-4 text-right text-sm font-medium">
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

                <div class="bg-gray-50 p-4 rounded-lg">
                    <h3 class="text-sm font-medium text-gray-700 mb-2">Metode Pembayaran</h3>
                    <p class="text-gray-900 font-medium"><?= strtoupper(str_replace('_', ' ', $payment['payment_method'])) ?></p>
                    <p class="text-green-600 font-medium mt-2">Status: <?= ucfirst($payment['status']) ?></p>
                </div>

                <div class="border-t pt-4 text-sm text-center text-gray-500">
                    <p>Terima kasih telah menggunakan layanan Bengkel Kita.</p>
                    <p>Untuk pertanyaan, silakan hubungi (021) 123-4567 atau email ke info@kita.blue</p>
                </div>
            </div>
        </div>
    </div>
</div>