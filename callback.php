<?php
require_once 'vendor/autoload.php';
include 'koneksi.php';

\Midtrans\Config::$serverKey = 'ISI_SERVER_KEY_MU';
\Midtrans\Config::$isProduction = false;

$json = file_get_contents('php://input');
$notif = json_decode($json);

$transaction_status = $notif->transaction_status;
$order_id = $notif->order_id; // contoh: ORDER-58-1719982000
$id_pemesanan = explode('-', $order_id)[1]; // ambil angka pemesanan

if ($transaction_status == 'settlement' || $transaction_status == 'capture') {
    mysqli_query($koneksi, "UPDATE pemesanan SET status_pembayaran = 'sudah_dibayar' WHERE id_pemesanan = '$id_pemesanan'");
}
