<?php
include 'koneksi.php';

$id = $_GET['id'];

mysqli_query($koneksi, "DELETE FROM pemesanan WHERE id_pemesanan = '$id'");

header("Location: pesanan.php?hapus=1");
exit;
?>
