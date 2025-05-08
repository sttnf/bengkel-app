<?php
$pageTitle = "Dashboard Pengguna - Manajemen Servis Mobil";
$pageHeader = "Dashboard Pengguna";

// Profil pengguna statis
$userProfile = [
    'nama' => 'Rizky Maulana',
    'email' => 'rizky@example.com',
    'telepon' => '+62 812 3456 7890',
    'jumlah_kendaraan' => 2,
    'login_terakhir' => '2025-05-06 20:30',
];

$riwayatServis = [
    ['servis' => 'Rotasi Ban', 'tanggal' => '2025-04-20', 'kendaraan' => 'Toyota Corolla 2020'],
    ['servis' => 'Pemeriksaan Menyeluruh', 'tanggal' => '2025-03-15', 'kendaraan' => 'Honda Civic 2019'],
];

$riwayatPembayaran = [
    ['id' => 'P-12345', 'jumlah' => 250000, 'status' => 'selesai', 'tanggal' => '2025-05-03'],
    ['id' => 'P-12346', 'jumlah' => 100000, 'status' => 'selesai', 'tanggal' => '2025-04-22'],
];

$tindakanCepat = [
    ['link' => 'permintaan-servis-baru.php', 'ikon' => 'plus-circle', 'label' => 'Ajukan Servis', 'warna' => 'blue'],
    ['link' => 'kendaraan-saya.php', 'ikon' => 'car', 'label' => 'Kendaraan Saya', 'warna' => 'green'],
    ['link' => 'pengaturan-akun.php', 'ikon' => 'settings', 'label' => 'Pengaturan Akun', 'warna' => 'violet'],
    ['link' => 'bantuan.php', 'ikon' => 'life-buoy', 'label' => 'Bantuan', 'warna' => 'rose'],
];

$service_requests = $service_requests ?? [];

ob_start();
?>
    <div class="container mx-auto px-4 py-8">

        <!-- Selamat Datang -->
        <div class="bg-white p-6 rounded-xl shadow-md border mb-6">
            <h2 class="text-2xl font-bold text-gray-800">Halo, <?= htmlspecialchars($userProfile['nama']) ?></h2>
            <p class="text-sm text-gray-500">Login terakhir: <?= htmlspecialchars($userProfile['login_terakhir']) ?></p>
        </div>

        <!-- Grid Utama -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

            <!-- Permintaan Servis -->
            <div class="bg-white p-5 rounded-xl shadow-md border">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-lg font-bold text-gray-700">Servis Berlangsung</h3>
                    <a href="daftar-servis.php" class="text-sm text-blue-600 hover:underline">Lihat Semua</a>
                </div>
                <div class="space-y-3">
                    <?php if (empty($service_requests)): ?>
                        <div class="bg-gray-50 border-l-4 border-gray-300 p-4 rounded text-sm text-gray-500">
                            Tidak ada servis yang sedang berlangsung.
                        </div>
                    <?php else: ?>
                        <?php foreach ($service_requests as $request): ?>
                            <?php
                            $warna = match ($request['status']) {
                                'pending' => 'amber',
                                'in_progress' => 'blue',
                                'completed' => 'green',
                                default => 'gray'
                            };
                            ?>
                            <form method="post" name="form-<?= $request['id'] ?>">
                                <div class="bg-gray-50 border-l-4 border-<?= $warna ?>-500 p-4 rounded">
                                    <div class="flex justify-between items-center font-semibold text-gray-800 mb-1">
                                        <span><?= htmlspecialchars($request['service_name']) ?></span>
                                        <span class="text-xs text-gray-400">#<?= $request['id'] ?></span>
                                    </div>
                                    <p class="text-sm text-gray-600"><?= htmlspecialchars($request['vehicle_brand']) ?>
                                        <?= htmlspecialchars($request['vehicle_model']) ?>
                                        (<?= $request['vehicle_year'] ?>)
                                        — <?= htmlspecialchars($request['license_plate']) ?></p>
                                    <p class="text-sm text-gray-500 capitalize">
                                        Status: <?= str_replace('_', ' ', $request['status']) ?></p>
                                    <p class="text-sm text-gray-500">
                                        Jadwal: <?= date('d M Y, H:i', strtotime($request['scheduled_datetime'])) ?></p>

                                    <?php if (empty($request['has_payment'])): ?>
                                        <a href="/dashboard/customer/payment?id=<?= $request['id'] ?>"
                                           class="inline-block mt-2 px-3 py-1 text-sm text-white bg-red-500 rounded hover:bg-red-600">
                                            Bayar Sekarang
                                        </a>
                                    <?php else: ?>
                                        <?php
                                        // Get payment ID for this service request
                                        $paymentModel = new \App\Models\Payment();
                                        $paymentData = $paymentModel->getByRequestId($request['id']);

                                        $paymentId = $paymentData['id'] ?? null;
                                        ?>
                                        <a href="dashboard/customer/invoice?id=<?= $paymentId ?>"
                                           class="inline-block mt-2 px-3 py-1 text-sm text-white bg-green-500 rounded hover:bg-green-600">
                                            Lihat Invoice
                                        </a>
                                    <?php endif; ?>
                                </div>
                            </form>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Riwayat Servis -->
            <div class="bg-white p-5 rounded-xl shadow-md border">
                <h3 class="text-lg font-bold text-gray-700 mb-3">Riwayat Servis</h3>
                <div class="space-y-3">
                    <?php foreach ($riwayatServis as $riwayat): ?>
                        <div class="p-4 bg-gray-50 border-l-4 border-green-500 rounded">
                            <p class="font-medium text-gray-800"><?= htmlspecialchars($riwayat['servis']) ?></p>
                            <p class="text-sm text-gray-500"><?= htmlspecialchars($riwayat['kendaraan']) ?>
                                — <?= $riwayat['tanggal'] ?></p>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <!-- Riwayat Pembayaran -->
            <div class="bg-white p-5 rounded-xl shadow-md border">
                <h3 class="text-lg font-bold text-gray-700 mb-3">Riwayat Pembayaran</h3>
                <div class="space-y-3">
                    <?php foreach ($riwayatPembayaran as $bayar): ?>
                        <div class="p-4 bg-gray-50 border-l-4 border-blue-500 rounded">
                            <div class="flex justify-between font-medium text-gray-800">
                                <span>Pembayaran #<?= htmlspecialchars($bayar['id']) ?></span>
                                <span class="text-xs text-gray-400"><?= $bayar['tanggal'] ?></span>
                            </div>
                            <p class="text-sm text-gray-500">Rp <?= number_format($bayar['jumlah'], 0, ',', '.') ?> —
                                Status: <?= ucfirst($bayar['status']) ?></p>
                            <a href="/invoice?id=<?= substr($bayar['id'], 2) ?>"
                               class="inline-block mt-2 px-3 py-1 text-sm text-white bg-blue-500 rounded hover:bg-blue-600">
                                Lihat Invoice
                            </a>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <!-- Tindakan Cepat -->
            <div class="bg-white p-5 rounded-xl shadow-md border">
                <h3 class="text-lg font-bold text-gray-700 mb-3">Tindakan Cepat</h3>
                <div class="grid grid-cols-2 gap-4">
                    <?php foreach ($tindakanCepat as $t): ?>
                        <a href="<?= htmlspecialchars($t['link']) ?>"
                           class="flex flex-col items-center justify-center bg-gray-50 rounded-lg p-4 hover:bg-gray-100 transition">
                            <div class="p-2 bg-<?= $t['warna'] ?>-100 text-<?= $t['warna'] ?>-600 rounded-full mb-2">
                                <i data-lucide="<?= $t['ikon'] ?>" class="w-5 h-5"></i>
                            </div>
                            <span class="text-sm font-medium text-gray-700"><?= htmlspecialchars($t['label']) ?></span>
                        </a>
                    <?php endforeach; ?>
                </div>
            </div>

        </div>
    </div>

<?php
echo ob_get_clean();
?>