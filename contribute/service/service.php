<?php
// Koneksi ke database
$host = "localhost";
$user = "root"; // sesuaikan
$pass = "";     // sesuaikan
$db   = "dbservis";

$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}

// Ambil data servis
$sql = "SELECT servis.*, 
               users.name AS user_name, 
               layanan.name AS layanan_name, 
               montir.name AS montir_name
        FROM servis
        LEFT JOIN users ON servis.user_id = users.id
        LEFT JOIN layanan ON servis.layanan_id = layanan.id
        LEFT JOIN montir ON servis.montir_id = montir.id
        ORDER BY servis.created_at DESC";

$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Data Servis</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>
<div class="container mt-4">
    <h2>Data Servis</h2>
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>User</th>
                <th>Montir</th>
                <th>Layanan</th>
                <th>Kendaraan</th>
                <th>Jadwal</th>
                <th>Keluhan</th>
                <th>Status</th>
                <th>Waktu Buat</th>
            </tr>
        </thead>
        <tbody>
            <?php if ($result->num_rows > 0): ?>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?= htmlspecialchars($row['user_name']) ?></td>
                        <td><?= htmlspecialchars($row['montir_name']) ?></td>
                        <td><?= htmlspecialchars($row['layanan_name']) ?></td>
                        <td><?= htmlspecialchars($row['vehicle']) ?></td>
                        <td><?= htmlspecialchars($row['schedule']) ?></td>
                        <td><?= htmlspecialchars($row['description']) ?></td>
                        <td><?= htmlspecialchars($row['status']) ?></td>
                        <td><?= htmlspecialchars($row['created_at']) ?></td>
                    </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr><td colspan="8">Tidak ada data.</td></tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>
</body>
</html>

<?php $conn->close(); ?>
