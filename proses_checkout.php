<?php
session_start();
require 'auth/koneksi.php';

// =========================
// VALIDASI USER
// =========================
$id_user = $_POST['id_user'] ?? null;
if (!$id_user) {
    error_log("ERROR: id_user tidak terkirim");
    die("ERROR: id_user tidak terkirim");
}

// =========================
// AMBIL INPUT
// =========================
$id_alamat          = $_POST['id_alamat'] ?? null;
$metode_pengambilan = $_POST['metode_pengambilan'] ?? null;
$waktu_selesai      = $_POST['waktu_selesai'] ?? null;
$catatan            = $_POST['catatan'] ?? '';
$id_voucher         = $_POST['id_voucher'] ?? null;

// =========================
// VALIDASI INPUT WAJIB
// =========================
if (!$metode_pengambilan) {
    error_log("ERROR: Metode pengambilan tidak dipilih");
    die("ERROR: Metode pengambilan wajib dipilih");
}

if (empty($id_alamat)) {
    error_log("ERROR: id_alamat tidak boleh null untuk metode '$metode_pengambilan'");
    die("ERROR: Alamat wajib dipilih untuk melanjutkan pesanan");
}

if (!$waktu_selesai) {
    error_log("ERROR: Waktu selesai tidak diisi");
    die("ERROR: Waktu selesai wajib diisi");
}

// =========================
// AMBIL ID KERANJANG
// =========================
$selected_ids = $_POST['selected_ids'] ?? '';
if (empty($selected_ids)) {
    error_log("ERROR: Tidak ada item dipilih");
    die("Tidak ada item dipilih");
}

$ids = array_filter(array_map('trim', explode(',', $selected_ids)));
if (empty($ids)) {
    error_log("ERROR: Keranjang tidak valid");
    die("Keranjang tidak valid");
}

// =========================
// AMBIL DATA KERANJANG
// =========================
$query = [
    "id_keranjang" => "in.(" . implode(',', $ids) . ")",
    "select" => "id_keranjang,id_user,jumlah,id_produk,id_paket,
                 produk(nama_produk,harga,foto_produk),
                 paket(nama_paket,harga_paket,foto_paket)"
];

$data = getSupabaseData("keranjang", $query);

if (!is_array($data) || empty($data)) {
    error_log("ERROR: Gagal load keranjang atau data kosong. Query: " . json_encode($query));
    die("Gagal load keranjang atau data kosong");
}

// =========================
// HITUNG TOTAL PRODUK
// =========================
$total_harga = 0;

foreach ($data as &$item) {

    $qty = (int)$item['jumlah'];

    if (!empty($item['id_paket'])) {
        $paket = $item['paket'];
        $harga = isset($paket[0])
            ? (float)$paket[0]['harga_paket']
            : (float)($paket['harga_paket'] ?? 0);
    } else {
        $produk = $item['produk'];
        $harga = isset($produk[0])
            ? (float)$produk[0]['harga']
            : (float)($produk['harga'] ?? 0);
    }

    $subtotal = $harga * $qty;

    // ✅ SIMPAN UNTUK INSERT DETAIL
    $item['_harga'] = $harga;
    $item['_subtotal'] = $subtotal;

    // ✅ TAMBAH TOTAL TRANSAKSI
    $total_harga += $subtotal;
}
unset($item); // WAJIB DI PHP


// Fallback dari POST jika perhitungan ulang gagal (untuk menghindari total_harga = 0)
if ($total_harga == 0) {
    $total_harga = $_POST['total_harga'] ?? 0;
    error_log("WARNING: Total harga dari perhitungan ulang 0, menggunakan fallback dari POST: $total_harga");
}

// Validasi total_harga tidak boleh 0
if ($total_harga <= 0) {
    error_log("ERROR: Total harga tidak valid: $total_harga");
    die("ERROR: Total harga tidak valid. Periksa data keranjang.");
}

// =========================
// TENTUKAN ONGKIR
// =========================
$ongkir = ($metode_pengambilan === "diantar") ? $_POST["ongkir"] : 0;
$total_harga += $ongkir;

// =========================
// DP MINIMAL 50% TOTAL
// =========================
$dp_minimal = ceil($total_harga * 0.5);

// Validasi dp_minimal tidak boleh 0
if ($dp_minimal <= 0) {
    error_log("ERROR: DP minimal tidak valid: $dp_minimal");
    die("ERROR: DP minimal tidak valid.");
}

// =========================
// INSERT DATA TRANSAKSI
// =========================
$dataTransaksi = [
    'id_user'           => $id_user,
    'id_alamat'         => $id_alamat,  // Sekarang selalu ada, tidak null
    'metode_pengambilan' => $metode_pengambilan,
    'waktu_selesai'     => $waktu_selesai,
    'total_harga'       => $total_harga,
    'dp_minimal'        => $dp_minimal,
    'catatan'           => $catatan,
    'ongkir'            => $ongkir,
    'id_voucher'        => $id_voucher,
    'status'            => "Menunggu Pembayaran"
];

$insert = insertSupabaseData("transaksi", $dataTransaksi);

if (!is_array($insert) || empty($insert[0]['id_transaksi'])) {
    error_log("ERROR: Gagal membuat transaksi. Response: " . json_encode($insert));
    die("Gagal membuat transaksi");
}

$idTransaksi = $insert[0]['id_transaksi'];

// =========================
// INSERT DETAIL TRANSAKSI (FINAL FIX)
// =========================
foreach ($data as $item) {

    $qty = (int)$item['jumlah'];

    // =========================
    // JIKA PAKET
    // =========================
    if (!empty($item['id_paket'])) {

        $harga = (int)($item['paket']['harga_paket'] ?? 0);
        $subtotal = $harga * $qty;

        $detail = [
            "id_transaksi" => $idTransaksi,
            "id_paket"     => $item['id_paket'],
            "jumlah"       => $qty,
            "subtotal"     => $subtotal
        ];

        $insertDetail = insertSupabaseData("detail_transaksi_paket", $detail);
    }

    // =========================
    // JIKA PRODUK
    // =========================
    else {

        $harga = (int)($item['produk']['harga'] ?? 0);
        $subtotal = $harga * $qty;

        $detail = [
            "id_transaksi" => $idTransaksi,
            "id_produk"    => $item['id_produk'],
            "jumlah"       => $qty,
            "subtotal"     => $subtotal
        ];

        $insertDetail = insertSupabaseData("detail_transaksi_produk", $detail);
    }

    // ✅ DEBUG WAJIB
    error_log("DETAIL FIX FINAL: " . json_encode($detail));

    if (!$insertDetail) {
        error_log("❌ GAGAL INSERT DETAIL: " . json_encode($detail));
    }
}

// =========================
// HAPUS KERANJANG
// =========================
foreach ($ids as $idKeranjang) {
    $delete = deleteSupabaseData("keranjang", "id_keranjang", $idKeranjang);
    if (!$delete) {
        error_log("WARNING: Gagal hapus keranjang ID: $idKeranjang");
        // Lanjutkan, tapi log error
    }
}

// ========================= 
// REDIRECT
// =========================
// header("Location: produk.php?id=" . $idTransaksi);
// exit;
// =========================
// GENERATE INVOICE XENDIT
// =========================

// API KEY STAGING
$apiKey = XENDIT_API_KEY;

// External ID = nomor transaksi
$external_id = $insert[0]['nomor_pesanan'] ?? $idTransaksi;

// Nominal yg harus dibayar (DP minimal)
$amount = $dp_minimal;

// Data customer
$customer = [
    "given_names" => $_SESSION['username'] ?? "User",
    "email" => $_SESSION['email'] ?? "usermail@gmail.com"
];

$dataInvoice = [
    "external_id" => $external_id,
    "amount" => $amount,
    "description" => "Pembayaran Pesanan #$external_id",
    "payment_methods" => ["QRIS"], // 🔥 hanya QRIS

    // CALLBACK → update status + insert pembayaran
    "callback_url" => "https://ordin4ry.my.id/callback/xendit.php",

    // Redirect setelah bayar
    "success_redirect_url" => "https://ordin4ry.my.id/rahmatbakery/produk.php?id=" . $idTransaksi,
    "failure_redirect_url" => "https://ordin4ry.my.id/rahmatbakery/produk.php?id=" . $idTransaksi,
];

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, "https://api.xendit.co/v2/invoices");
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($dataInvoice));
curl_setopt($ch, CURLOPT_USERPWD, $apiKey . ":");
curl_setopt($ch, CURLOPT_HTTPHEADER, ["Content-Type: application/json"]);

$response = curl_exec($ch);
curl_close($ch);

$invoice = json_decode($response, true);

if (!isset($invoice['invoice_url'])) {
    error_log("XENDIT ERROR: " . $response);
    die("Gagal membuat invoice pembayaran!");
}

// Simpan ke Supabase sebagai pembayaran menunggu (opsional)
insertSupabaseData("pembayaran", [
    "id_transaksi" => $idTransaksi,
    "nominal" => $amount,
    "metode" => "QRIS",
    "status" => "Menunggu Pembayaran"
]);

// 🔥 Langsung redirect ke halaman pembayaran Xendit
header("Location: " . $invoice['invoice_url']);
exit;
