<?php
session_start();
require 'auth/koneksi.php';

// Cek apakah user sudah login
$id_user = $_SESSION['user_id'] ?? $_SESSION['id_user'] ?? null;

if (empty($id_user)) {
    $_SESSION['message'] = "❌ Anda harus login terlebih dahulu.";
    $redirect = $_POST['redirect'] ?? 'produk.php';
    header("Location: login.php?redirect=" . urlencode($redirect));
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_produk = $_POST['id_produk'] ?? null;
    $quantity = (int)($_POST['quantity'] ?? 1);
    $size = $_POST['size'] ?? null;
    $wording = $_POST['wording'] ?? null;

    if (!$id_produk || $quantity <= 0) {
        $_SESSION['message'] = "❌ Data produk tidak valid.";
        header("Location: produk.php");
        exit;
    }

    try {
        // 1. Cek apakah produk sudah ada di keranjang user
        $response = $client->get("/rest/v1/keranjang", [
            'query' => [
                'id_user' => 'eq.' . $id_user,
                'id_produk' => 'eq.' . $id_produk,
                'select' => '*'
            ]
        ]);

        $existing_cart = json_decode($response->getBody(), true);

        if (!empty($existing_cart)) {
            // 2. Jika sudah ada, update quantity
            $cart_item = $existing_cart[0];
            $new_quantity = $cart_item['jumlah'] + $quantity;

            $update_response = $client->patch("/rest/v1/keranjang", [
                'query' => ['id_keranjang' => 'eq.' . $cart_item['id_keranjang']],
                'json' => [
                    'jumlah' => $new_quantity
                ],
                'headers' => [
                    'Prefer' => 'return=minimal'
                ]
            ]);

            if ($update_response->getStatusCode() === 204) {
                // Simpan size dan wording ke session untuk checkout nanti
                if ($size !== null || $wording !== null) {
                    if (!isset($_SESSION['cart_options'])) {
                        $_SESSION['cart_options'] = [];
                    }
                    $_SESSION['cart_options'][$cart_item['id_keranjang']] = [
                        'size' => $size,
                        'wording' => $wording
                    ];
                }
                $_SESSION['message'] = "✅ Jumlah produk di keranjang berhasil diperbarui!";
            } else {
                throw new Exception("Gagal memperbarui keranjang");
            }
        } else {
            // 3. Jika belum ada, insert baru
            $cart_data = [
                'id_user' => $id_user,
                'id_produk' => $id_produk,
                'jumlah' => $quantity,
                'id_paket' => null // Set null jika tidak ada paket
            ];

            $insert_response = $client->post("/rest/v1/keranjang", [
                'json' => $cart_data,
                'headers' => [
                    'Prefer' => 'return=representation'
                ]
            ]);

            if ($insert_response->getStatusCode() === 201) {
                // Simpan size dan wording ke session untuk checkout nanti
                if ($size !== null || $wording !== null) {
                    $inserted_data = json_decode($insert_response->getBody(), true);
                    if (!empty($inserted_data)) {
                        if (!isset($_SESSION['cart_options'])) {
                            $_SESSION['cart_options'] = [];
                        }
                        $_SESSION['cart_options'][$inserted_data[0]['id_keranjang']] = [
                            'size' => $size,
                            'wording' => $wording
                        ];
                    }
                }
                $_SESSION['message'] = "✅ Produk berhasil ditambahkan ke keranjang!";
            } else {
                throw new Exception("Gagal menambahkan ke keranjang");
            }
        }

        // 4. Redirect sesuai tombol yang ditekan
        if (isset($_POST['buy_now'])) {
            header("Location: keranjang.php");
            exit;
        } else {
            $redirect_url = $_POST['redirect'] ?? 'produk.php';
            header("Location: " . $redirect_url);
            exit;
        }
    } catch (Exception $e) {
        $_SESSION['message'] = "❌ Error: " . $e->getMessage();
        $redirect_url = $_POST['redirect'] ?? 'produk.php';
        header("Location: " . $redirect_url);
        exit;
    }
} else {
    header("Location: produk.php");
    exit;
}
