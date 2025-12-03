<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Footer</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        .footer {
            background: linear-gradient(135deg, #6d4c41 0%, #5d4037 50%, #4e342e 100%);
            margin-top: 7rem;
            padding: 4rem 2rem 0;
            color: white;
        }

        .footer-container {
            max-width: 1400px;
            margin: 0 auto;
        }

        .footer-main {
            display: grid;
            grid-template-columns: auto 1fr 1.2fr 1.5fr;
            gap: 1.5rem; /* Mengurangi gap dari 2rem ke 1.5rem untuk mengurangi jarak antar kolom */
            padding-bottom: 2.5rem;
            border-bottom: 1px solid rgba(255, 255, 255, 0.2);
            align-items: start;
        }

        /* About Section */
        .footer-about {
            display: flex;
            flex-direction: column;
            gap: 1rem;
            padding-top: 0.5rem;
        }

        .footer-halal {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 0.5rem;
        }

        .footer-halal img {
            width: 70px;
            height: auto;
            filter: brightness(1.1);
        }

        .footer-halal span {
            font-size: 0.7rem;
            color: rgba(255, 255, 255, 0.8);
            text-align: center;
            line-height: 1.3;
        }

        .app-btn-footer {
            display: flex;
            align-items: center;
            gap: 0.6rem;
            background: rgba(255, 255, 255, 0.1);
            padding: 0.5rem 0.8rem;
            border-radius: 8px;
            text-decoration: none;
            color: white;
            transition: all 0.3s ease;
            border: 2px solid rgba(255, 255, 255, 0.2);
            width: fit-content;
            margin: 0 auto;
        }

        .app-btn-footer:hover {
            background: rgba(255, 255, 255, 0.2);
            border-color: #ffccbc;
        }

        .app-btn-footer i {
            font-size: 1.3rem;
        }

        .app-btn-footer .app-btn-text {
            display: flex;
            flex-direction: column;
        }

        .app-btn-footer .app-btn-text small {
            font-size: 0.6rem;
            opacity: 0.9;
        }

        .app-btn-footer .app-btn-text span {
            font-size: 0.8rem;
            font-weight: 600;
        }

        /* Quick Links */
        .footer-links h4 {
            font-size: 1.3rem;
            margin-bottom: 1.2rem;
            color: #ffccbc;
            font-weight: 600;
        }

        .footer-links ul {
            list-style: none;
        }

        .footer-links ul li {
            margin-bottom: 0.8rem;
        }

        .footer-links ul li a {
            color: rgba(255, 255, 255, 0.85);
            text-decoration: none;
            transition: all 0.3s ease;
            display: inline-block;
        }

        .footer-links ul li a:hover {
            color: #ffccbc;
            transform: translateX(5px);
        }

        /* Contact Section */
        .footer-contact h4 {
            font-size: 1.3rem;
            margin-bottom: 1.2rem;
            color: #ffccbc;
            font-weight: 600;
        }

        .contact-item {
            display: flex;
            align-items: flex-start;
            gap: 1rem;
            margin-bottom: 1.2rem;
        }

        .contact-item i {
            color: #ffccbc;
            font-size: 1.2rem;
            margin-top: 0.2rem;
            min-width: 20px;
        }

        .contact-item p {
            color: rgba(255, 255, 255, 0.85);
            line-height: 1.6;
            margin: 0;
        }

        .contact-item a {
            color: rgba(255, 255, 255, 0.85);
            text-decoration: none;
            transition: color 0.3s ease;
        }

        .contact-item a:hover {
            color: #ffccbc;
        }

        /* Maps & Social Section */
        .footer-maps-social {
            display: grid;
            grid-template-columns: auto 1fr;
            gap: 1.5rem;
            align-items: start;
        }

        .footer-social-icons {
            display: flex;
            flex-direction: column;
            gap: 0.7rem;
            padding-top: 0.5rem;
        }

        .social-icons {
            display: grid;
            grid-template-columns: repeat(3, 1fr); /* Mengubah menjadi grid dengan 3 kolom untuk membuat 2 baris (baris 1: 3 ikon, baris 2: 2 ikon) */
            gap: 0.6rem;
        }

        .social-icons a {
            width: 48px; /* Memperbesar dari 36px ke 48px */
            height: 48px; /* Memperbesar dari 36px ke 48px */
            display: flex;
            align-items: center;
            justify-content: center;
            background: rgba(255, 255, 255, 0.1);
            color: white;
            border-radius: 50%;
            font-size: 1.2rem; /* Memperbesar dari 0.95rem ke 1.2rem */
            transition: all 0.3s ease;
            text-decoration: none;
            border: 2px solid transparent;
        }

        .social-icons a:hover {
            background: #ffccbc;
            color: #5d4037;
            transform: scale(1.1);
            border-color: #ffccbc;
        }

        .footer-maps {
            display: flex;
            flex-direction: column;
        }

        .footer-maps h4 {
            font-size: 1.2rem;
            margin-bottom: 0.7rem;
            color: #ffccbc;
            font-weight: 600;
        }

        .footer-maps iframe {
            border-radius: 10px;
            box-shadow: 0 6px 15px rgba(0, 0, 0, 0.3);
            width: 100%;
            height: 160px;
            border: 2px solid rgba(255, 255, 255, 0.1);
        }

        /* Quote Section */
        .footer-quote {
            text-align: center;
            padding: 2.5rem 1rem;
        }

        .footer-quote p {
            font-size: 1.3rem;
            font-style: italic;
            color: rgba(255, 255, 255, 0.95);
            margin: 0 0 0.8rem 0;
            line-height: 1.7;
            font-weight: 300;
        }

        .footer-quote span {
            font-size: 1rem;
            color: #ffccbc;
            font-weight: 600;
        }

        /* Bottom */
        .footer-bottom {
            text-align: center;
            padding: 1.5rem 0;
            border-top: 1px solid rgba(255, 255, 255, 0.2);
            background: rgba(0, 0, 0, 0.2);
        }

        .footer-bottom p {
            margin: 0;
            color: rgba(255, 255, 255, 0.8);
            font-size: 0.9rem;
        }

        /* Responsive */
        @media (max-width: 1024px) {
            .footer-main {
                grid-template-columns: 1fr 1fr;
                gap: 2rem;
            }

            .footer-maps iframe {
                height: 180px;
            }
        }

        @media (max-width: 768px) {
            .footer {
                padding: 3rem 1.5rem 0;
            }

            .footer-main {
                grid-template-columns: 1fr;
                gap: 2rem;
            }

            .footer-about h3 {
                font-size: 1.5rem;
            }

            .footer-quote p {
                font-size: 1.1rem;
            }

            .footer-maps iframe {
                height: 220px;
            }

            .social-icons {
                grid-template-columns: repeat(2, 1fr); /* Di mobile, ubah ke 2 kolom untuk 3 baris jika perlu */
            }
        }
    </style>
</head>

<body>
    <footer class="footer">
        <div class="footer-container">
            <div class="footer-main">
                <!-- About Section -->
                <div class="footer-about">
                    <div class="footer-halal">
                        <img src="assets/img/logo-halal.png" alt="Logo Halal Indonesia">
                        <span>Terjamin Halal<br>& Higienis</span>
                    </div>
                    <a href="#" class="app-btn-footer">
                        <i class="fab fa-google-play"></i>
                        <div class="app-btn-text">
                            <small>Download di</small>
                            <span>Google Play</span>
                        </div>
                    </a>
                </div>

                <!-- Quick Links -->
                <div class="footer-links">
                    <h4>Menu Cepat</h4>
                    <ul>
                        <li><a href="index.php">Beranda</a></li>
                        <li><a href="produk.php">Produk Kami</a></li>
                        <li><a href="kustom.php">Kustom Pesanan</a></li>
                        <li><a href="keranjang.php">Keranjang</a></li>
                        <li><a href="profil.php">Profil</a></li>
                    </ul>
                </div>

                <!-- Contact -->
                <div class="footer-contact">
                    <h4>Hubungi Kami</h4>
                    <div class="contact-item">
                        <i class="fas fa-map-marker-alt"></i>
                        <p>Kampung Baru, Gembongan, Krajan, Tanggul Kulon, Kec. Tanggul, Kabupaten Jember, Jawa Timur 68155</p>
                    </div>
                    <div class="contact-item">
                        <i class="fas fa-phone"></i>
                        <p><a href="tel:+628123456789">+62 812-3456-7890</a></p>
                    </div>
                    <div class="contact-item">
                        <i class="fas fa-envelope"></i>
                        <p><a href="mailto:info@rahmatkue.com">info@rahmatkue.com</a></p>
                    </div>
                </div>

                <!-- Google Maps & Social Media -->
                <div class="footer-maps-social">
                    <div class="footer-social-icons">
                        <div class="social-icons">
                            <a href="#" aria-label="Instagram" title="Instagram">
                                <i class="fab fa-instagram"></i>
                            </a>
                            <a href="#" aria-label="TikTok" title="TikTok">
                                <i class="fab fa-tiktok"></i>
                            </a>
                            <a href="#" aria-label="Facebook" title="Facebook">
                                <i class="fab fa-facebook"></i>
                            </a>
                            <a href="#" aria-label="YouTube" title="YouTube">
                                <i class="fab fa-youtube"></i>
                            </a>
                            <a href="#" aria-label="WhatsApp" title="WhatsApp">
                                <i class="fab fa-whatsapp"></i>
                            </a>
                        </div>
                    </div>

                    <div class="footer-maps">
                        <h4><i class="fas fa-map-marked-alt"></i> Lokasi Kami</h4>
                        <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3949.3863001854224!2d113.44283507362647!3d-8.16377879186698!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x2dd68b2108ccc677%3A0x72818aefbb066be9!2sRAHMAT%20KUE!5e0!3m2!1sid!2sid!4v1759152354699!5m2!1sid!2sid" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
                    </div>
                </div>
            </div>

            <!-- Quote -->
            <div class="footer-quote">
                <p>"Hidup itu seperti adonan, harus sabar dan penuh cinta untuk jadi sempurna."</p>
                <span>- Rahmat Kue -</span>
            </div>
        </div>

        <!-- Copyright -->
        <div class="footer-bottom">
            <p>© 2025 Rahmat Kue. All Rights Reserved. | Made in Jember</p>
        </div>
    </footer>
</body>

</html>
