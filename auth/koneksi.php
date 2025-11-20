<?php
require __DIR__ . '/../vendor/autoload.php';

use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;

// ✅ Supabase constants
define("SUPABASE_URL", "https://fsiuefdwcbdhunfhbiwl.supabase.co");
define("SUPABASE_KEY", "eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJpc3MiOiJzdXBhYmFzZSIsInJlZiI6ImZzaXVlZmR3Y2JkaHVuZmhiaXdsIiwicm9sZSI6ImFub24iLCJpYXQiOjE3NTk5MzM0NDMsImV4cCI6MjA3NTUwOTQ0M30.pSATGpW89fntkKRuF-qvC7wiO1oZsTruDd-1wMjOdIU");
define("SUPABASE_SERVICE_KEY", "eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJpc3MiOiJzdXBhYmFzZSIsInJlZiI6ImZzaXVlZmR3Y2JkaHVuZmhiaXdsIiwicm9sZSI6InNlcnZpY2Vfcm9sZSIsImlhdCI6MTc1OTkzMzQ0MywiZXhwIjoyMDc1NTA5NDQzfQ.Fuj3tINEzdkmIzJQ6YPegk--_AGPTN7HIiupCWM6mU4");
define("SUPABASE_STORAGE_URL", SUPABASE_URL . "/storage/v1/object/public");

$client = new Client([
    'base_uri' => SUPABASE_URL,
    'timeout' => 10,
    'headers' => [
        'apikey' => SUPABASE_KEY,
        'Authorization' => 'Bearer ' . SUPABASE_KEY,
        'Content-Type' => 'application/json',
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
        echo "Error saat mengambil data dari Supabase: " . $e->getMessage();
        return [];
    }
}

// ✅ Insert data ke tabel Supabase
function insertSupabaseData($table, $data)
{
    $url = SUPABASE_URL . "/rest/v1/" . $table;

    $headers = [
        "apikey: " . SUPABASE_KEY,
        "Authorization: Bearer " . SUPABASE_KEY,
        "Content-Type: application/json",
        // ✅ Pastikan Supabase mengembalikan data hasil insert (bukan cuma status)
        "Prefer: return=representation"
    ];

    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $error = curl_error($ch);
    curl_close($ch);

    // ✅ Jika cURL error (misalnya koneksi gagal)
    if ($error) {
        error_log("cURL Error: " . $error);
        return false;
    }

    // ✅ Cek status HTTP
    if (in_array($httpCode, [200, 201])) {
        $decoded = json_decode($response, true);
        if (!empty($decoded)) {
            // kembalikan data hasil insert (misal id_keranjang)
            return $decoded[0];
        }
        return true;
    } else {
        // ❌ Simpan log error untuk debugging
        error_log("Insert gagal ke tabel '$table'. HTTP Code: $httpCode. Response: $response");
        return false;
    }
}

function updateSupabaseData($table, $column, $value, $data)
{
    $url = SUPABASE_URL . "/rest/v1/" . $table . "?{$column}=eq." . urlencode($value);

    $headers = [
        "apikey: " . SUPABASE_KEY,
        "Authorization: Bearer " . SUPABASE_KEY,
        "Content-Type: application/json",
        "Prefer: return=representation"
    ];

    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PATCH");
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $error = curl_error($ch);
    curl_close($ch);

    // ✅ Jika cURL error
    if ($error) {
        error_log("cURL Error: " . $error);
        return false;
    }

    // ✅ Cek status HTTP (200 atau 204 = berhasil)
    if (in_array($httpCode, [200, 204])) {
        return true;
    } else {
        error_log("Update gagal ke tabel '$table'. HTTP Code: $httpCode. Response: $response");
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
function uploadToSupabaseStorage($bucket, $localPath, $destPath)
{
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
function searchSupabaseProduk($keyword)
{
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

function deleteSupabaseData($table, $column, $value)
{
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

function banUserAuth($uid)
{
    try {
        $client = new Client();

        $response = $client->patch(SUPABASE_URL . '/auth/v1/admin/users/' . $uid, [
            'headers' => [
                'apikey' => SUPABASE_SERVICE_KEY,
                'Authorization' => 'Bearer ' . SUPABASE_SERVICE_KEY,
                'Content-Type' => 'application/json',
            ],
            'json' => [
                "ban_duration" => "1y"  // blokir selama 1 tahun
            ]
        ]);

        return true;
    } catch (Exception $e) {
        echo $e->getMessage();
        return false;
    }
}

function unbanUserAuth($uid)
{
    try {
        $client = new Client();

        $response = $client->patch(SUPABASE_URL . '/auth/v1/admin/users/' . $uid, [
            'headers' => [
                'apikey' => SUPABASE_SERVICE_KEY,
                'Authorization' => 'Bearer ' . SUPABASE_SERVICE_KEY,
                'Content-Type' => 'application/json',
            ],
            'json' => [
                "ban_duration" => "none"  // unban user
            ]
        ]);

        return true;
    } catch (Exception $e) {
        echo $e->getMessage();
        return false;
    }
}

