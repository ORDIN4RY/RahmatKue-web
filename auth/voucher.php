<?php

require __DIR__ . '/../vendor/autoload.php';

use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;

function addVoucher($post, $files)
{
    global $client;

    try {
        // ==============================
        // 1. Upload foto (jika ada)
        // ==============================
        $fotoUrl = "https://fsiuefdwcbdhunfhbiwl.supabase.co/storage/v1/object/public/images/default/default%20voucher.jpg";

        if (isset($files['foto']) && $files['foto']['error'] === UPLOAD_ERR_OK) {

            $fileTemp = $files['foto']['tmp_name'];
            $fileName = uniqid() . "_" . $files['foto']['name'];

            $bucket = 'images'; // sesuaikan

            $uploadResponse = $client->post(
                SUPABASE_URL . "/storage/v1/object/$bucket/voucher/$fileName",
                [
                    'headers' => [
                        'apikey'        => SUPABASE_SERVICE_KEY,
                        'Authorization' => 'Bearer ' . SUPABASE_SERVICE_KEY,
                        'Content-Type'  => $files['foto']['type']
                    ],
                    'body' => file_get_contents($fileTemp)
                ]
            );

            if ($uploadResponse->getStatusCode() === 200) {
                $fotoUrl = SUPABASE_URL . "/storage/v1/object/public/$bucket/voucher/$fileName";
            }
        }

        // ==============================
        // 2. Insert ke tabel voucher
        // ==============================
        $voucherData = [
            "nama_voucher"        => $post['nama_voucher_add'],
            "deskripsi"           => $post['deskripsi'] ?? '',
            "tgl_mulai"           => $post['tgl_mulai'],
            "tgl_berakhir"        => $post['tgl_berakhir'],
            "poin_tukar"          => intval($post['poin_tukar'] ?? 0),
            "minimal_pembelian"   => intval($post['minimal_pembelian'] ?? 0),
            "persentase_potongan" => intval($post['persentase_potongan'] ?? 0),
            "maksimal_potongan"   => intval($post['maksimal_potongan'] ?? 0),
            "jenis_voucher"       => $post['jenis_voucher'],
            "foto"                => $fotoUrl,
        ];

        $insertResponse = $client->post(SUPABASE_URL . '/rest/v1/voucher', [
            'headers' => [
                'apikey'        => SUPABASE_SERVICE_KEY,
                'Authorization' => 'Bearer ' . SUPABASE_SERVICE_KEY,
                'Content-Type'  => 'application/json',
                'Prefer'        => 'return=representation'
            ],
            'body' => json_encode($voucherData)
        ]);

        $insertResult = json_decode($insertResponse->getBody()->getContents(), true);

        if (empty($insertResult) || !isset($insertResult[0]['id_voucher'])) {
            return [
                "success" => false,
                "error" => "Insert voucher gagal atau tidak mengembalikan ID."
            ];
        }

        $voucherId = $insertResult[0]['id_voucher'];

        // ==============================
        // 3. Insert kategori (Optional)
        // ==============================
        if (!empty($post['id_kategori'])) {

            foreach ($post['id_kategori'] as $katId) {

                $client->post(SUPABASE_URL . '/rest/v1/voucher_kategori', [
                    'headers' => [
                        'apikey'        => SUPABASE_SERVICE_KEY,
                        'Authorization' => 'Bearer ' . SUPABASE_SERVICE_KEY,
                        'Content-Type'  => 'application/json'
                    ],
                    'body' => json_encode([
                        "id_voucher" => $voucherId,
                        "id_kategori" => $katId
                    ])
                ]);
            }
        }

        return ["success" => true];

    } catch (RequestException $e) {
        if ($e->hasResponse()) {
            return [
                "success" => false,
                "error" => $e->getResponse()->getBody()->getContents()
            ];
        }
        return ["success" => false, "error" => $e->getMessage()];
    }
}

function updateVoucher($id_voucher, $post, $files)
{
    global $client;

    try {
        // ===============================
        // 1. Cek voucher lama untuk ambil foto lama (opsional)
        // ===============================
        $old = $client->get(SUPABASE_URL . "/rest/v1/voucher?id_voucher=eq.$id_voucher&select=foto", [
            'headers' => [
                'apikey' => SUPABASE_SERVICE_KEY,
                'Authorization' => 'Bearer ' . SUPABASE_SERVICE_KEY
            ]
        ]);

        $oldData = json_decode($old->getBody()->getContents(), true);
        $oldFoto = $oldData[0]['foto'] ?? null;

        $fotoUrl = $oldFoto;

        // ===============================
        // 2. Kalau ada foto baru, upload
        // ===============================
        if (!empty($files['foto']["name"])) {

            $fileTemp = $files['foto']['tmp_name'];
            $fileName = uniqid() . "_" . $files['foto']['name'];

            $bucket = "images";

            $client->post(
                SUPABASE_URL . "/storage/v1/object/$bucket/voucher/$fileName",
                [
                    'headers' => [
                        'apikey'        => SUPABASE_SERVICE_KEY,
                        'Authorization' => 'Bearer ' . SUPABASE_SERVICE_KEY,
                        'Content-Type'  => $files['foto']['type']
                    ],
                    'body' => file_get_contents($fileTemp)
                ]
            );

            $fotoUrl = SUPABASE_URL . "/storage/v1/object/public/$bucket/voucher/$fileName";
        }

        // ===============================
        // 3. Update data voucher
        // ===============================
        $payload = [
            "nama_voucher"        => $post['nama_voucher_edit'],
            "kode_voucher"        => $post['kode_voucher'] ?? null,
            "tgl_mulai"           => $post['tgl_mulai'],
            "tgl_berakhir"        => $post['tgl_berakhir'],
            "deskripsi"           => $post['deskripsi'] ?? "",
            "poin_tukar"          => (int)$post['poin_tukar'],
            "minimal_pembelian"   => (int)$post['minimal_pembelian'],
            "persentase_potongan" => (int)$post['persentase_potongan'],
            "maksimal_potongan"   => (int)$post['maksimal_potongan'],
            "jenis_voucher"       => $post['jenis_voucher'],
            "foto"                => $fotoUrl,
        ];

        $update = $client->patch(
            SUPABASE_URL . "/rest/v1/voucher?id_voucher=eq.$id_voucher",
            [
                'headers' => [
                    'apikey'        => SUPABASE_SERVICE_KEY,
                    'Authorization' => 'Bearer ' . SUPABASE_SERVICE_KEY,
                    'Content-Type'  => 'application/json',
                    'Prefer'        => 'return=representation'
                ],
                'body' => json_encode($payload)
            ]
        );

        // ===============================
        // 4. Update kategori
        // ===============================

        // Hapus kategori lama
        $client->delete(
            SUPABASE_URL . "/rest/v1/voucher_kategori?id_voucher=eq.$id_voucher",
            [
                'headers' => [
                    'apikey'        => SUPABASE_SERVICE_KEY,
                    'Authorization' => 'Bearer ' . SUPABASE_SERVICE_KEY
                ]
            ]
        );

        // Insert kategori baru
        if (!empty($post['id_kategori'])) {
            foreach ($post['id_kategori'] as $kategoriId) {
                $client->post(
                    SUPABASE_URL . "/rest/v1/voucher_kategori",
                    [
                        'headers' => [
                            'apikey'        => SUPABASE_SERVICE_KEY,
                            'Authorization' => 'Bearer ' . SUPABASE_SERVICE_KEY,
                            'Content-Type'  => 'application/json'
                        ],
                        'body' => json_encode([
                            "id_voucher"  => $id_voucher,
                            "id_kategori" => $kategoriId
                        ])
                    ]
                );
            }
        }

        return ["success" => true];

    } catch (Exception $e) {
        return ["success" => false, "error" => $e->getMessage()];
    }
}

function deleteVoucher($id_voucher)
{
    global $client;

    try {
        // ===============================
        // 1. Ambil foto untuk dihapus dari storage
        // ===============================
        $old = $client->get(
            SUPABASE_URL . "/rest/v1/voucher?id_voucher=eq.$id_voucher&select=foto",
            [
                'headers' => [
                    'apikey'        => SUPABASE_SERVICE_KEY,
                    'Authorization' => 'Bearer ' . SUPABASE_SERVICE_KEY
                ]
            ]
        );

        $oldData = json_decode($old->getBody()->getContents(), true);
        $fotoUrl = $oldData[0]["foto"] ?? null;

        // Ekstrak file name dari URL
        if ($fotoUrl && strpos($fotoUrl, "/voucher/") !== false) {
            $path = explode("/voucher/", $fotoUrl)[1];
        }

        // ===============================
        // 2. Hapus kategori relasi
        // ===============================
        $client->delete(
            SUPABASE_URL . "/rest/v1/voucher_kategori?id_voucher=eq.$id_voucher",
            [
                'headers' => [
                    'apikey'        => SUPABASE_SERVICE_KEY,
                    'Authorization' => 'Bearer ' . SUPABASE_SERVICE_KEY
                ]
            ]
        );

        // ===============================
        // 3. Hapus voucher
        // ===============================
        $client->delete(
            SUPABASE_URL . "/rest/v1/voucher?id_voucher=eq.$id_voucher",
            [
                'headers' => [
                    'apikey'        => SUPABASE_SERVICE_KEY,
                    'Authorization' => 'Bearer ' . SUPABASE_SERVICE_KEY
                ]
            ]
        );

        // ===============================
        // 4. Hapus file foto dari storage (opsional)
        // ===============================
        if (!empty($path)) {
            $client->delete(
                SUPABASE_URL . "/storage/v1/object/images/voucher/$path",
                [
                    'headers' => [
                        'apikey'        => SUPABASE_SERVICE_KEY,
                        'Authorization' => 'Bearer ' . SUPABASE_SERVICE_KEY
                    ]
                ]
            );
        }

        return ["success" => true];

    } catch (Exception $e) {
        return ["success" => false, "error" => $e->getMessage()];
    }
}

?>