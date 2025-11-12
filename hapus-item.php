<?php
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['hapus_id'])) {
    $hapus_id = $_POST['hapus_id'];
    foreach ($_SESSION['cart'] as $key => $item) {
        if ($item['id'] == $hapus_id) {
            unset($_SESSION['cart'][$key]);
            $_SESSION['cart'] = array_values($_SESSION['cart']);
            break;
        }
    }
}

header("Location: pesan.php");
exit;
?>
