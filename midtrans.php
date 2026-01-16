<?php
// midtrans.php
// Memicu transaksi Midtrans dan menghasilkan Snap Token dengan order_id unik

require_once 'vendor/autoload.php';
include 'koneksi.php';
session_start();

\Midtrans\Config::$serverKey = 'Mid-server-cNo8mT5sRxGIF9VrtfjBnot4';
\Midtrans\Config::$isProduction = false;
\Midtrans\Config::$isSanitized = true;
\Midtrans\Config::$is3ds = true;

$id_user = $_SESSION['id_user'];
$id_pemesanan = $_POST['id_pemesanan'];
$total = $_POST['total'];

// Ambil data user
$user = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT * FROM user WHERE id_user = '$id_user'"));

// Buat order_id yang unik setiap kali transaksi
$order_id = 'ORDER-' . $id_pemesanan . '-' . time();

$transaction_details = [
    'order_id' => $order_id,
    'gross_amount' => $total
];

$customer_details = [
    'first_name' => $user['nama_lengkap'],
    'phone' => $user['hp']
];

$params = [
    'transaction_details' => $transaction_details,
    'customer_details' => $customer_details
];

$snapToken = \Midtrans\Snap::getSnapToken($params);

// Simpan snap_token dan order_id ke database (opsional)
mysqli_query($koneksi, "UPDATE pemesanan SET snap_token = '$snapToken', midtrans_order_id = '$order_id' WHERE id_pemesanan = '$id_pemesanan'");

echo $snapToken;