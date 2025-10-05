<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rahmat Kue</title>
    <link rel="icon" type="image/x-icon" href="assets/img/icon.png">
    <link rel="stylesheet" href="assets/css/pesan.css">
    <!-- <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous"> -->
</head>

<body>
    <?php include 'component/navbar.php'; ?>

    <section class="pesanan-section">
        <h2>Rahmat Kue, Tanggul Kulon, Tanggul, Jember</h2>
        <div class="header-pesanan">
            <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3949.3863001854224!2d113.44283507362647!3d-8.16377879186698!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x2dd68b2108ccc677%3A0x72818aefbb066be9!2sRAHMAT%20KUE!5e0!3m2!1sid!2sid!4v1759152354699!5m2!1sid!2sid" width="200" height="150" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
            <form action="" method="">
                <div class="card">
                    <div class="header">
                        <img src="https://cdn-icons-png.flaticon.com/512/2972/2972185.png" alt="icon">
                        <div>
                            <h1>Pengantaran</h1>
                            <p>Estimasi pengantaran dalam 20 menit</p>
                        </div>
                    </div>
                    <div class="form-group">
                        <input type="text" placeholder="Nama Penerima">
                    </div>
                    <div class="form-group">
                        <input type="text" placeholder="Alamat Penerima">
                    </div>
                    <div class="form-group">
                        <textarea placeholder="Detail Alamat"></textarea>
                    </div>
                </div>
            </form>
        </div>
    </section>

    <section class="pembelian">
        <center>
            <div class="card-pembelian">
                <h3>Ringkasan pembelian</h3>
                <div class="row-pembelian">
                    <span>Harga</span>
                    <span>Rp. 240.000</span>
                </div>
                <div class="row-pembelian">
                    <span>Biaya Pengiriman</span>
                    <span>Rp. 0</span>
                </div>
                <div class="row-pembelian">
                    <strong>Total pembelian</strong>
                    <strong>Rp. 240.000</strong>
                </div>
            </div>
        </center>
    </section>

    <section class="ringkasan-pembayaran">
        <center>
            <div class="card-pembayaran">
                <h3>Ringkasan Pembayaran</h3>
                <div class="row-pembayaran">
                    <span>Harga</span>
                    <span>Rp. 240.000</span>
                </div>
                <div class="row-pembayaran">
                    <span>Biaya Pengiriman</span>
                    <span>Rp. 0</span>
                </div>
                <div class="row-pembayaran">
                    <strong>Total Pembayaran</strong>
                    <strong>Rp. 240.000</strong>
                </div>
            </div>
        </center>
    </section>

    <?php include 'component/footer.php'; ?>

</body>

</html>