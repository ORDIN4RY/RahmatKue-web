<?php
include 'auth/koneksi.php';

// ===== VALIDASI WAJIB =====
$id_user = $_POST['id_user'] ?? null;
if (!$id_user) {
    die("ERROR: id_user tidak terkirim");
}

$id_alamat          = $_POST['id_alamat'] ?? null;
$metode_pengambilan = $_POST['metode_pengambilan'] ?? null;
$waktu_selesai      = $_POST['waktu_selesai'] ?? null;
$catatan            = $_POST['catatan'] ?? '';
$id_voucher         = $_POST['id_voucher'] ?? null;

// ==========================
// HITUNG SUBTOTAL KERANJANG
// ==========================
$subtotal = 0;

if (isset($_POST['keranjang']) && is_array($_POST['keranjang'])) {
    foreach ($_POST['keranjang'] as $item) {
        $harga = $item['harga'] ?? 0;
        $qty   = $item['qty'] ?? 0;
        $subtotal += ($harga * $qty);
    }
}

// ==========================
// HITUNG ONGKIR + TOTAL
// ==========================
$ongkir = ($metode_pengambilan === "diantar") ? 25000 : 0;
$total  = $subtotal + $ongkir;

// ==========================
// JIKA METODE = DIAMBIL → ALAMAT = NULL
// ==========================
if ($metode_pengambilan === "diambil") {
    $id_alamat = null;
}

// =============================
// HAPUS ITEM KERANJANG YANG DICHECKOUT
// =============================
// =============================
// HAPUS ITEM KERANJANG YANG DICHECKOUT
// =============================
$selected_ids = $_POST['selected_ids'] ?? '';

if (!empty($selected_ids)) {

    $ids = explode(',', $selected_ids);

    foreach ($ids as $idKeranjang) {
        deleteSupabaseData('keranjang', 'id_keranjang', $idKeranjang);
    }
}



// ==========================
// HITUNG DP MINIMAL = SETENGAH TOTAL
// ==========================
$dp_minimal = ceil($total / 2);

// ==========================
// DATA TRANSAKSI
// ==========================
$dataTransaksi = [
    'id_user'            => $id_user,
    'id_alamat'          => $id_alamat,
    'metode_pengambilan' => $metode_pengambilan,
    'waktu_selesai'      => $waktu_selesai,
    'total_harga'        => $total,
    'dp_minimal'         => $dp_minimal,
    'catatan'            => $catatan,
    'id_voucher'         => $id_voucher,
    'status'             => "Menunggu Pembayaran"
];

// ==========================
// INSERT KE SUPABASE
// ==========================
$insert = insertSupabaseData('transaksi', $dataTransaksi);

// Validasi response
if (!is_array($insert)) {
    die("❌ Supabase error: Response bukan array");
}

if (isset($insert['message'])) {
    echo "❌ Gagal membuat transaksi:<br>";
    print_r($insert);
    exit;
}

if (empty($insert)) {
    die("❌ Supabase tidak mengembalikan data. Cek tabel anda.");
}

// Ambil ID transaksi
$idTransaksi = $insert[0]['id_transaksi'] ?? null;

if (!$idTransaksi) {
    echo "❌ Gagal mendapatkan ID transaksi:<br>";
    print_r($insert);
    exit;
}

// Redirect
header("Location: produk.php?id=" . $idTransaksi);
exit;
