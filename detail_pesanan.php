<?php
session_start();
include 'koneksi.php';

$query = mysqli_query($conn, "SELECT * FROM pemesanan ORDER BY id_pemesanan DESC");
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Data Pesanan</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/css/bootstrap.min.css">
</head>
<body>
<div class="container mt-5">
    <h2>Data Pesanan</h2>

    <?php if (isset($_GET['berhasil'])): ?>
        <div class="alert alert-success">Pembayaran dikonfirmasi!</div>
    <?php endif; ?>

    <table class="table table-bordered">
        <thead>
            <tr>
                <th>ID Pemesanan</th>
                <th>Nama Pemesan</th>
                <th>Total</th>
                <th>Status</th>
                <th>Bukti</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
        <?php while ($data = mysqli_fetch_assoc($query)): ?>
            <tr>
                <td><?php echo $data['id_pemesanan']; ?></td>
                <td><?php echo $data['nama_pemesan']; ?></td>
                <td>Rp <?php echo number_format($data['total_belanja'], 0, ',', '.'); ?></td>
                <td>
                    <?php
                    if ($data['status_pembayaran'] == 'menunggu_konfirmasi') {
                        echo "Menunggu Konfirmasi";
                    } elseif ($data['status_pembayaran'] == 'sudah_dibayar') {
                        echo "Sudah Dibayar";
                    } else {
                        echo "Belum Dibayar";
                    }
                    ?>
                </td>
                <td>
                    <?php
                    if (!empty($data['bukti_pembayaran'])) {
                        echo "<a href='" . $data['bukti_pembayaran'] . "' target='_blank'>Lihat Bukti</a>";
                    } else {
                        echo "-";
                    }
                    ?>
                </td>
                <td>
                    <?php if ($data['status_pembayaran'] == 'menunggu_konfirmasi'): ?>
                        <a href="konfirmasi.php?id=<?php echo $data['id_pemesanan']; ?>" class="btn btn-success btn-sm">Konfirmasi Pembayaran</a>
                    <?php elseif ($data['status_pembayaran'] == 'sudah_dibayar'): ?>
                        <span class="badge badge-success">Terkonfirmasi</span>
                    <?php else: ?>
                        <span class="badge badge-secondary">Belum Dibayar</span>
                    <?php endif; ?>
                </td>
            </tr>
        <?php endwhile; ?>
        </tbody>
    </table>
</div>
</body>
</html>
