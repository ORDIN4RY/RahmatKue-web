<?php
require '../../../auth/koneksi.php';
require __DIR__ . '/../../../vendor/autoload.php';

use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: index.php');
    exit;
}

$user_id = $_POST['user_id'] ?? '';

if ($user_id === '') {
    die("ID user tidak valid.");
}

// === 1. Delete Profile ===
$client = new Client(['base_uri' => SUPABASE_URL]);

try {
    $client->delete('/rest/v1/profiles', [
        'headers' => [
            'apikey' => SUPABASE_KEY,
            'Authorization' => 'Bearer ' . SUPABASE_KEY
        ],
        'query' => [
            'id' => 'eq.' . $user_id
        ]
    ]);
} catch (Exception $e) {
    die("Gagal menghapus profile: " . $e->getMessage());
}

// === 2. Delete Auth User ===
try {
    $client->delete('/auth/v1/admin/users/' . $user_id, [
        'headers' => [
            'apikey' => SUPABASE_SERVICE_KEY,
            'Authorization' => 'Bearer ' . SUPABASE_SERVICE_KEY,
        ]
    ]);
} catch (Exception $e) {
    die("Gagal menghapus user auth: " . $e->getMessage());
}

header('Location: index.php?msg=deleted');
exit;
