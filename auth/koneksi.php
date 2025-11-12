<?php
require __DIR__ . '/../vendor/autoload.php';

use GuzzleHttp\Client;

// ✅ Supabase constants
define("SUPABASE_URL", "https://fsiuefdwcbdhunfhbiwl.supabase.co");
define("SUPABASE_KEY", "eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJpc3MiOiJzdXBhYmFzZSIsInJlZiI6ImZzaXVlZmR3Y2JkaHVuZmhiaXdsIiwicm9sZSI6ImFub24iLCJpYXQiOjE3NTk5MzM0NDMsImV4cCI6MjA3NTUwOTQ0M30.pSATGpW89fntkKRuF-qvC7wiO1oZsTruDd-1wMjOdIU");
define("SUPABASE_STORAGE_URL", SUPABASE_URL . "/storage/v1/object/public");

// ✅ Buat client Supabase (gunakan constant)
$client = new Client([
    'base_uri' => SUPABASE_URL,
    'headers' => [
        'apikey' => SUPABASE_KEY,
        'Authorization' => 'Bearer ' . SUPABASE_KEY,
        'Content-Type' => 'application/json',
    ]
]);

function getSupabasePublicUrl($bucket, $path) {
    // pastikan sesuai struktur folder dan bucket di Supabase
    return SUPABASE_STORAGE_URL . "/$bucket/$path";
}

// ✅ Ambil data dari Supabase
function getSupabaseData($table) {
    global $client;
    try {
        $response = $client->get("/rest/v1/$table?select=*");
        $data = json_decode($response->getBody(), true);
        return $data;
    } catch (Exception $e) {
        echo "Error saat mengambil data dari Supabase: " . $e->getMessage();
        return [];
    }
}

// ✅ Insert data ke tabel Supabase
function insertSupabaseData($table, $data) {
    $url = SUPABASE_URL . "/rest/v1/" . $table;

    $headers = [
        "apikey: " . SUPABASE_KEY,
        "Authorization: Bearer " . SUPABASE_KEY,
        "Content-Type: application/json",
        "Prefer: return=representation"
    ];

    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    // ✅ Terima status 200, 201, 204 sebagai berhasil
    if (in_array($httpCode, [200, 201, 204])) {
        return true;
    } else {
        error_log("Insert gagal. Kode: $httpCode, Respon: $response");
        return false;
    }
}


// ✅ Upload file ke Supabase Storage
function uploadToSupabaseStorage($bucket, $localPath, $destPath) {
    try {
        $client = new Client([
            'base_uri' => SUPABASE_URL . '/storage/v1/',
            'headers' => [
                'apikey' => SUPABASE_KEY,
                'Authorization' => 'Bearer ' . SUPABASE_KEY,
                'Content-Type' => 'application/octet-stream',
            ]
        ]);

        $response = $client->post("object/$bucket/$destPath", [
            'body' => file_get_contents($localPath),
        ]);

        if ($response->getStatusCode() === 200 || $response->getStatusCode() === 201) {
            // URL file publik (karena bucket public)
            return SUPABASE_STORAGE_URL . "/$bucket/$destPath";
        } else {
            return false;
        }
    } catch (Exception $e) {
        echo "Error saat upload ke storage: " . $e->getMessage();
        return false;
    }
}

/**
 * Cari data produk berdasarkan nama (mengandung keyword)
 */
function searchSupabaseProduk($keyword) {
    $client = new \GuzzleHttp\Client([
        'base_uri' => SUPABASE_URL,
        'timeout'  => 10,
    ]);

    try {
        // Gunakan operator ilike untuk pencarian tidak case-sensitive
        $response = $client->get(SUPABASE_URL . '/rest/v1/produk', [
            'headers' => [
                'apikey'        => SUPABASE_KEY,
                'Authorization' => 'Bearer ' . SUPABASE_KEY,
            ],
            'query' => [
                'nama_produk' => 'ilike.%' . $keyword . '%', // pencarian seperti LIKE %keyword%
                'select'      => '*'
            ],
        ]);

        $data = json_decode($response->getBody()->getContents(), true);
        return $data;
    } catch (Exception $e) {
        echo "Error saat mencari produk: " . $e->getMessage();
        return [];
    }
}

function deleteSupabaseData($table, $column, $value) {
    $url = SUPABASE_URL . "/rest/v1/" . $table . "?{$column}=eq." . urlencode($value);

    $headers = [
        "apikey: " . SUPABASE_KEY,
        "Authorization: Bearer " . SUPABASE_KEY,
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

    // Berhasil jika status 204 (No Content) atau 200
    return ($httpCode === 200 || $httpCode === 204);
}

