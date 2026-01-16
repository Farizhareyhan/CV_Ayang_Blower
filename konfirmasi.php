<?php
include 'koneksi.php';
$id = $_GET['id'];

mysqli_query($koneksi, "UPDATE pemesanan SET status_pembayaran = 'sudah_dibayar' WHERE id_pemesanan = '$id'");

header("Location: pesanan.php?berhasil=1");
exit;
?>
