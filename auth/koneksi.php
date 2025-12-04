<?php
require __DIR__ . '/../vendor/autoload.php';

use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;

// ✅ Supabase constants
define("SUPABASE_URL", "https://fsiuefdwcbdhunfhbiwl.supabase.co");
define("SUPABASE_KEY", "eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJpc3MiOiJzdXBhYmFzZSIsInJlZiI6ImZzaXVlZmR3Y2JkaHVuZmhiaXdsIiwicm9sZSI6ImFub24iLCJpYXQiOjE3NTk5MzM0NDMsImV4cCI6MjA3NTUwOTQ0M30.pSATGpW89fntkKRuF-qvC7wiO1oZsTruDd-1wMjOdIU");
define("SUPABASE_SERVICE_KEY", "eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJpc3MiOiJzdXBhYmFzZSIsInJlZiI6ImZzaXVlZmR3Y2JkaHVuZmhiaXdsIiwicm9sZSI6InNlcnZpY2Vfcm9sZSIsImlhdCI6MTc1OTkzMzQ0MywiZXhwIjoyMDc1NTA5NDQzfQ.Fuj3tINEzdkmIzJQ6YPegk--_AGPTN7HIiupCWM6mU4");
define("SUPABASE_STORAGE_URL", SUPABASE_URL . "/storage/v1/object/public");
define("XENDIT_API_KEY","xnd_development_OtdTZHUMLg2HaFdTEnMV0KAtXL5W1H1ZJMtYMLcOB1Z8SCvvoBSBbwz34dEKVmO");

$client = new Client([
    'base_uri' => SUPABASE_URL,
    'timeout' => 10,
    'headers' => [
        'apikey'        => SUPABASE_KEY,
        'Authorization' => 'Bearer ' . ($_SESSION['access_token'] ?? SUPABASE_KEY),
        'Content-Type'  => 'application/json',
    ]

]);


function getSupabasePublicUrl($bucket, $path)
{
    // pastikan sesuai struktur folder dan bucket di Supabase
    return SUPABASE_STORAGE_URL . "/$bucket/$path";
}

// ✅ Ambil data dari Supabase
function getSupabaseData($table)
{
    global $client;
    try {
        $response = $client->get("/rest/v1/$table?select=*");
        $data = json_decode($response->getBody(), true);
        return $data;
    } catch (Exception $e) {

        $msg = $e->getMessage();

        if (strpos($msg, "JWT expired") !== false) {
            echo "<script>
            alert('Sesi Anda telah berakhir, silakan login kembali.');
            window.location.href = 'auth/login.php';
        </script>";
            exit;
        }

        die("Error saat mengambil promo: " . $msg);

        echo "Error saat mengambil data dari Supabase: " . $e->getMessage();
        return [];
    }
}

// ✅ Insert data ke tabel Supabase
// function insertSupabaseData($table, $data)
// {
//     $url = SUPABASE_URL . "/rest/v1/" . $table;

//     $headers = [
//         "apikey: " . SUPABASE_KEY,
//         "Authorization: Bearer " . SUPABASE_KEY,
//         "Content-Type: application/json",
//         // ✅ Pastikan Supabase mengembalikan data hasil insert (bukan cuma status)
//         "Prefer: return=representation"
//     ];

//     $ch = curl_init($url);
//     curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
//     curl_setopt($ch, CURLOPT_POST, true);
//     curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
//     curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

//     $response = curl_exec($ch);
//     $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
//     $error = curl_error($ch);
//     curl_close($ch);

//     // ✅ Jika cURL error (misalnya koneksi gagal)
//     if ($error) {
//         error_log("cURL Error: " . $error);
//         return false;
//     }

//     // ✅ Cek status HTTP
//     if (in_array($httpCode, [200, 201])) {
//         $decoded = json_decode($response, true);
//         if (!empty($decoded)) {
//             // kembalikan data hasil insert (misal id_keranjang)
//             return $decoded[0];
//         }
//         return true;
//     } else {
//         // ❌ Simpan log error untuk debugging
//         error_log("Insert gagal ke tabel '$table'. HTTP Code: $httpCode. Response: $response");
//         return false;
//     }
// }

function insertSupabaseData($table, $data)
{
    $client = new Client([
        'base_uri' => SUPABASE_URL
    ]);

    try {
        $response = $client->request('POST', "/rest/v1/$table?select=*", [
            'headers' => [
                'Authorization' => 'Bearer ' . SUPABASE_KEY,
                'apikey'        => SUPABASE_KEY,
                'Content-Type'  => 'application/json',
                'Prefer'        => 'return=representation'
            ],
            'body' => json_encode($data)
        ]);

        return json_decode($response->getBody(), true);
    } catch (Exception $e) {
        return ["error" => $e->getMessage()];
    }
}


// function updateSupabaseData($table, $column, $value, $data)
// {
//     $url = SUPABASE_URL . "/rest/v1/" . $table . "?{$column}=eq." . urlencode($value);

//     $headers = [
//         "apikey: " . SUPABASE_KEY,
//         "Authorization: Bearer " . SUPABASE_KEY,
//         "Content-Type: application/json",
//         "Prefer: return=representation"
//     ];

//     $ch = curl_init($url);
//     curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PATCH");
//     curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
//     curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
//     curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

//     $response = curl_exec($ch);
//     $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
//     $error = curl_error($ch);
//     curl_close($ch);

//     // ✅ Jika cURL error
//     if ($error) {
//         error_log("cURL Error: " . $error);
//         return false;
//     }

//     // ✅ Cek status HTTP (200 atau 204 = berhasil)
//     if (in_array($httpCode, [200, 204])) {
//         return true;
//     } else {
//         error_log("Update gagal ke tabel '$table'. HTTP Code: $httpCode. Response: $response");
//         return false;
//     }
// }

function updateSupabaseData($table, $filters = [], $data = [])
{
    global $SUPABASE_URL, $SUPABASE_SERVICE_KEY;

    $client = new Client([
        'base_uri' => $SUPABASE_URL,
        'headers' => [
            'apikey'        => $SUPABASE_SERVICE_KEY,
            'Authorization' => 'Bearer ' . $SUPABASE_SERVICE_KEY,
            'Content-Type'  => 'application/json'
        ]
    ]);

    // --- BENTUKKAN QUERY UPDATE ---
    $query = [];
    foreach ($filters as $field => $value) {
        $query[] = $field . '=eq.' . $value;
    }

    $url = "/rest/v1/$table?" . implode('&', $query);

    try {
        $response = $client->patch($url, [
            'body' => json_encode($data)
        ]);

        return json_decode($response->getBody(), true);
    } catch (Exception $e) {
        echo "ERROR UPDATE: " . $e->getMessage();
        return false;
    }
}



/**
 * Generate UUID v4 untuk primary key
 * @return string - UUID dalam format standar
 */
function generateUUID()
{
    return sprintf(
        '%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
        mt_rand(0, 0xffff),
        mt_rand(0, 0xffff),
        mt_rand(0, 0xffff),
        mt_rand(0, 0x0fff) | 0x4000,
        mt_rand(0, 0x3fff) | 0x8000,
        mt_rand(0, 0xffff),
        mt_rand(0, 0xffff),
        mt_rand(0, 0xffff)
    );
}


// ✅ Upload file ke Supabase Storage
// function uploadToSupabaseStorage($bucket, $localPath, $destPath)
// {
//     try {
//         $client = new Client([
//             'base_uri' => SUPABASE_URL . '/storage/v1/',
//             'headers' => [
//                 'apikey' => SUPABASE_KEY,
//                 'Authorization' => 'Bearer ' . SUPABASE_KEY,
//                 'Content-Type' => 'application/octet-stream',
//             ]
//         ]);

//         $response = $client->post("object/$bucket/$destPath", [
//             'body' => file_get_contents($localPath),
//         ]);

//         if ($response->getStatusCode() === 200 || $response->getStatusCode() === 201) {
//             // URL file publik (karena bucket public)
//             return SUPABASE_STORAGE_URL . "/$bucket/$destPath";
//         } else {
//             return false;
//         }
//     } catch (Exception $e) {
//         echo "Error saat upload ke storage: " . $e->getMessage();
//         return false;
//     }
// }

function uploadToSupabaseStorage($folder, $localPath, $fileName)
{
    global $SUPABASE_URL, $SUPABASE_SERVICE_KEY;

    $bucket = "images"; // FIX: bucket-mu hanya 1 yaitu "images"

    if (!file_exists($localPath)) {
        echo "File tidak ditemukan: $localPath";
        return false;
    }

    // Path dalam storage
    $uploadPath = "$bucket/$folder/$fileName"; // contoh: images/produk/a.png

    $client = new Client([
        'base_uri' => $SUPABASE_URL,
        'headers' => [
            'Authorization' => "Bearer {$SUPABASE_SERVICE_KEY}",
            'apikey'        => $SUPABASE_SERVICE_KEY,
        ]
    ]);

    $mime = mime_content_type($localPath);
    $fileData = file_get_contents($localPath);

    try {
        $response = $client->request(
            'POST',
            "/storage/v1/object/$uploadPath",
            [
                'headers' => [
                    'Content-Type'   => $mime
                ],
                'body' => $fileData
            ]
        );

        return $response->getStatusCode() === 200 || $response->getStatusCode() === 201;
    } catch (Exception $e) {
        echo "Error saat upload ke storage: " . $e->getMessage();
        return false;
    }
}

/**
 * Cari data produk berdasarkan nama (mengandung keyword)
 */
function searchSupabaseProduk($keyword)
{
    global $client;

    $response = $client->get("/rest/v1/produk", [
        'query' => [
            'select' => 'id_produk,nama_produk,harga,foto_produk,varian',
            'nama_produk' => 'ilike.%' . $keyword . '%'
        ]
    ]);

    return json_decode($response->getBody(), true);
}

function searchSupabaseAll($keyword)
{
    global $client;

    $keywordFilter = 'ilike.%' . $keyword . '%';

    /* ======================
       1. CARI PRODUK
    ====================== */
    $responseProduk = $client->get("/rest/v1/produk", [
        'query' => [
            'select' => 'id_produk,nama_produk,harga,foto_produk,varian',
            'nama_produk' => $keywordFilter
        ]
    ]);

    $produk = json_decode($responseProduk->getBody(), true);


    /* ======================
       2. CARI PAKET
    ====================== */
    $responsePaket = $client->get("/rest/v1/paket", [
        'query' => [
            'select' => 'id_paket,nama_paket,harga_paket,foto_paket,deskripsi',
            'nama_paket' => $keywordFilter
        ]
    ]);

    $paket = json_decode($responsePaket->getBody(), true);


    /* RETURN GABUNGAN */
    return [
        "produk" => $produk,
        "paket"  => $paket
    ];
}



function deleteSupabaseData($table, $column, $value)
{
    $url = SUPABASE_URL . "/rest/v1/" . $table . "?{$column}=eq." . urlencode($value);

    $headers = [
        "apikey: " . SUPABASE_SERVICE_KEY,
        "Authorization: Bearer " . SUPABASE_SERVICE_KEY,
        "Content-Type: application/json",
        "Prefer: return=representation"
    ];

    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "DELETE");
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    return ($httpCode === 200 || $httpCode === 204);
}


function deleteProdukDenganRelasi($id_produk)
{
    // Hapus dulu relasi di detail_paket
    $urlDetail = SUPABASE_URL . "/rest/v1/detail_paket?id_produk=eq." . urlencode($id_produk);

    $headers = [
        "apikey: " . SUPABASE_SERVICE_KEY,
        "Authorization: Bearer " . SUPABASE_SERVICE_KEY,
        "Content-Type: application/json",
        "Prefer: return=representation"
    ];

    // DELETE detail_paket
    $ch = curl_init($urlDetail);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "DELETE");
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

    $responseDetail = curl_exec($ch);
    $httpCodeDetail = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    // Jika gagal hapus relasi, hentikan
    if (!in_array($httpCodeDetail, [200, 204])) {
        return [
            "success" => false,
            "message" => "Gagal menghapus relasi detail_paket",
            "debug" => $responseDetail
        ];
    }

    // Setelah relasi terhapus → hapus produk
    $urlProduk = SUPABASE_URL . "/rest/v1/produk?id_produk=eq." . urlencode($id_produk);

    $ch2 = curl_init($urlProduk);
    curl_setopt($ch2, CURLOPT_CUSTOMREQUEST, "DELETE");
    curl_setopt($ch2, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch2, CURLOPT_HTTPHEADER, $headers);

    $responseProduk = curl_exec($ch2);
    $httpCodeProduk = curl_getinfo($ch2, CURLINFO_HTTP_CODE);
    curl_close($ch2);

    if (in_array($httpCodeProduk, [200, 204])) {
        return [
            "success" => true,
            "message" => "Produk berhasil dihapus",
            "debug" => $responseProduk
        ];
    }

    return [
        "success" => false,
        "message" => "Gagal menghapus produk",
        "debug" => $responseProduk
    ];
}

// function deletePaket($id_paket)
//     {
//         // UUID tidak pakai kutip
//         $url = SUPABASE_URL . "/rest/v1/paket?id_paket=eq.$id_paket";

//         $headers = [
//             "apikey: " . SUPABASE_SERVICE_KEY,
//             "Authorization: Bearer " . SUPABASE_SERVICE_KEY,
//             "Content-Type: application/json",
//             "Prefer: return=representation, count=exact"
//         ];

//         $ch = curl_init($url);
//         curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "DELETE");
//         curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
//         curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

//         $response = curl_exec($ch);
//         $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
//         curl_close($ch);

//         error_log("DELETE URL: $url");
//         error_log("RESPONSE: $response");
//         error_log("HTTP: $httpCode");


//         return [
//             "success" => ($httpCode === 200 || $httpCode === 204),
//             "message" => "HTTP $httpCode",
//             "debug" => $response
//         ];
//     }


// Profil
function getRiwayatPesanan($id_user)
{
    global $client;

    try {
        $response = $client->get('/rest/v1/transaksi', [
            'query' => [
                'select' => '*,alamat(*)',
                'id_user' => 'eq.' . $id_user,
                'order' => 'created_at.desc'
            ]
        ]);

        return json_decode($response->getBody(), true);
    } catch (Exception $e) {
        return [];
    }
}

function getAlamatCheckout($id_user)
{
    global $client;

    try {
        $response = $client->get('/rest/v1/alamat', [
            'headers' => [
                'apikey'        => SUPABASE_KEY,
                'Authorization' => 'Bearer ' . $_SESSION['access_token']
            ],
            'query' => [
                'select' => '*',
                'id_user' => 'eq.' . $id_user
            ]
        ]);

        $data = json_decode($response->getBody(), true);
        error_log("getAlamatCheckout: id_user=$id_user, data=" . json_encode($data));  // Log hasil
        return $data;
    } catch (Exception $e) {
        error_log("ERROR getAlamatCheckout: " . $e->getMessage());  // Log error
        return [];
    }
}


function getAlamatRumah($id_user)
{
    // Pastikan id_user tidak kosong
    if (!$id_user) {
        return null;
    }

    $url = "https://fsiuefdwcbdhunfhbiwl.supabase.co/rest/v1/alamat";
    $client = new \GuzzleHttp\Client();

    try {
        $response = $client->request('GET', $url, [
            'headers' => [
                'apikey'        => SUPABASE_KEY,
                'Authorization' => 'Bearer ' . SUPABASE_KEY
            ],
            'query' => [
                'id_user' => "eq.$id_user",
                'select'  => 'alamat_rumah'
            ]
        ]);

        $data = json_decode($response->getBody(), true);

        // Jika tidak ada data
        if (empty($data)) {
            return null;
        }

        return $data[0]['alamat_rumah'];
    } catch (Exception $e) {
        echo "Error mengambil alamat: " . $e->getMessage();
        return null;
    }
}


function getSupabaseDataByID($table, $column, $value)
{
    global $client, $SUPABASE_URL, $SUPABASE_KEY;

    try {
        $response = $client->get("$SUPABASE_URL/rest/v1/$table", [
            'headers' => [
                'apikey' => $SUPABASE_KEY,
                'Authorization' => "Bearer $SUPABASE_KEY",
            ],
            'query' => [
                $column => "eq.$value",
                'select' => '*'
            ]
        ]);

        return json_decode($response->getBody(), true);
    } catch (Exception $e) {
        return false;
    }
}

function getPaket()
{
    global $client;

    $response = $client->get("/rest/v1/paket", [
        'query' => [
            'select' => 'id_paket,nama_paket,harga_paket,deskripsi,foto_paket,diskon'
        ]
    ]);

    return json_decode($response->getBody(), true);
}


function getProdukByKategori($kategoriDipilih)
{
    global $client;

    /* ========================
       1. Jika kategori = Semua
       → Produk + Paket
    ========================== */
    if ($kategoriDipilih === "Semua") {

        // Ambil semua produk
        $produk = json_decode($client->get("/rest/v1/produk", [
            'query' => ['select' => 'id_produk,nama_produk,harga,foto_produk,varian']
        ])->getBody(), true);

        // Ambil semua paket
        $paket = getPaket();

        // Gabungkan keduanya
        return [
            "produk" => $produk,
            "paket"  => $paket
        ];
    }

    /* ========================
       2. Jika kategori = Paket
       → Tampilkan paket saja
    ========================== */
    if ($kategoriDipilih === "Paket") {
        return [
            "produk" => [],
            "paket"  => getPaket()
        ];
    }

    /* ========================
       3. Jika kategori biasa
       → Produk by kategori
    ========================== */

    $respKategori = $client->get("/rest/v1/kategori", [
        'query' => [
            'nama_kategori' => 'eq.' . $kategoriDipilih,
            'select' => 'id_kategori'
        ]
    ]);

    $kategoriData = json_decode($respKategori->getBody(), true);

    if (empty($kategoriData)) {
        return ["produk" => [], "paket" => []];
    }

    $id_kategori = $kategoriData[0]['id_kategori'];

    $produk = json_decode($client->get("/rest/v1/produk", [
        'query' => [
            'id_kategori' => 'eq.' . $id_kategori,
            'select' => 'id_produk,nama_produk,harga,foto_produk,varian'
        ]
    ])->getBody(), true);

    return [
        "produk" => $produk,
        "paket"  => []  // kategori biasa tidak menampilkan paket
    ];
}


function getWadah()
{
    return getSupabaseData(
        'wadah?select=id_wadah,nama_wadah,deskripsi,foto_wadah,kapasitas,harga_wadah,varian'
    ) ?? [];
}


function checkoutUser($id_user, $access_token)
{
    global $client;

    // --- 1. Ambil keranjang user ---
    try {
        $response = $client->get('/rest/v1/keranjang', [
            'headers' => [
                'apikey'        => SUPABASE_KEY,
                'Authorization' => 'Bearer ' . $access_token
            ],
            'query' => [
                'id_user' => 'eq.' . $id_user,
                'select'  => 'id_keranjang,id_produk,id_paket,jumlah,
                              produk(harga), paket(harga_paket)'
            ]
        ]);
        $items = json_decode($response->getBody(), true);
    } catch (Exception $e) {
        return ['success' => false, 'message' => 'Gagal mengambil keranjang: ' . $e->getMessage()];
    }

    if (empty($items)) {
        return ['success' => false, 'message' => 'Keranjang kosong'];
    }

    // --- 2. Hitung total harga ---
    $total_harga = 0;

    foreach ($items as $item) {
        if (!empty($item['paket'])) {
            $total_harga += $item['paket']['harga_paket'] * $item['jumlah'];
        } else {
            $total_harga += $item['produk']['harga'] * $item['jumlah'];
        }
    }

    // --- 3. Hitung DP minimal (50%) ---
    $dp_minimal = ceil($total_harga * 0.5);

    // --- 4. Ambil alamat utama user ---
    $alamatUtama = getAlamatCheckout($id_user);
    $id_alamat = !empty($alamatUtama) ? $alamatUtama[0]['id_alamat'] : null;

    // --- 5. Buat transaksi baru ---
    $id_transaksi = generateUUID();

    $dataTransaksi = [
        'id_transaksi'      => $id_transaksi,
        'id_user'           => $id_user,
        'id_alamat'         => $id_alamat,
        'metode_pengambilan' => 'diambil', // default
        'total_harga'       => $total_harga,
        'dp_minimal'        => $dp_minimal,
        'status'            => 'Menunggu Pembayaran',
        'tanggal'           => date('Y-m-d H:i:s')
    ];

    $insert = insertSupabaseData('transaksi', $dataTransaksi);

    if (!$insert) {
        return ['success' => false, 'message' => 'Gagal membuat transaksi'];
    }

    // --- 6. Insert detail item ---
    foreach ($items as $item) {
        $harga = !empty($item['paket'])
            ? $item['paket']['harga_paket']
            : $item['produk']['harga'];

        $detailData = [
            'id_detail'     => generateUUID(),
            'id_transaksi'  => $id_transaksi,
            'id_produk'     => $item['id_produk'],
            'id_paket'      => $item['id_paket'],
            'jumlah'        => $item['jumlah'],
            'subtotal'      => $harga * $item['jumlah']
        ];

        insertSupabaseData('detail_transaksi', $detailData);
    }

    // --- 7. Hapus keranjang ---
    try {
        $client->delete('/rest/v1/keranjang?id_user=eq.' . $id_user, [
            'headers' => [
                'apikey'        => SUPABASE_KEY,
                'Authorization' => 'Bearer ' . $access_token
            ]
        ]);
    } catch (Exception $e) {
        return ['success' => false, 'message' => 'Transaksi berhasil, tetapi gagal hapus keranjang.'];
    }

    return [
        'success' => true,
        'message' => 'Checkout berhasil',
        'id_transaksi' => $id_transaksi
    ];
}


function banUser($uid)
{
    try {
        $client = new Client();

        $response = $client->patch(SUPABASE_URL . '/rest/v1/profiles?id=eq.' . $uid, [
            'headers' => [
                'apikey' => SUPABASE_SERVICE_KEY,
                'Authorization' => 'Bearer ' . SUPABASE_SERVICE_KEY,
                'Content-Type' => 'application/json',
                'Prefer' => 'return=representation'
            ],
            'json' => [
                "is_blocked" => true
            ]
        ]);

        return json_decode($response->getBody(), true);
    } catch (RequestException $e) {
        return [
            "error" => $e->getMessage(),
            "response" => $e->getResponse() ? $e->getResponse()->getBody()->getContents() : null
        ];
    }
}

function unbanUser($uid)
{
    try {
        $client = new Client();

        $response = $client->patch(SUPABASE_URL . '/rest/v1/profiles?id=eq.' . $uid, [
            'headers' => [
                'apikey' => SUPABASE_SERVICE_KEY,
                'Authorization' => 'Bearer ' . SUPABASE_SERVICE_KEY,
                'Content-Type' => 'application/json',
                'Prefer' => 'return=representation'
            ],
            'json' => [
                "is_blocked" => false
            ]
        ]);

        return json_decode($response->getBody(), true);
    } catch (RequestException $e) {
        return [
            "error" => $e->getMessage(),
            "response" => $e->getResponse() ? $e->getResponse()->getBody()->getContents() : null
        ];
    }
}
