<?php
session_start();
?>
<!DOCTYPE html>
<html class="no-js" lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Keranjang - Freshly.id</title>
    <link href="https://fonts.googleapis.com/css?family=Cairo:400,600,700&amp;display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css?family=Poppins:600&amp;display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css?family=Playfair+Display:400i,700i" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css?family=Ubuntu&amp;display=swap" rel="stylesheet">
    <link rel="shortcut icon" type="image/x-icon" href="assets/images/favicon.png" />
    <link rel="stylesheet" href="assets/css/bootstrap.min.css">
    <link rel="stylesheet" href="assets/css/animate.min.css">
    <link rel="stylesheet" href="assets/css/font-awesome.min.css">
    <link rel="stylesheet" href="assets/css/nice-select.css">
    <link rel="stylesheet" href="assets/css/slick.min.css">
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="assets/css/main-color.css">
    <style>
        .login-button a {
            font-weight: 600;
            color: #347928;
            border: 1px solid #347928;
            transition: all 0.3s ease;
        }

        .login-button a:hover {
            background-color: #347928;
            color: #fff;
            text-decoration: none;
        }
    </style>
</head>

<body class="biolife-body">

    <!-- Preloader -->
    <div id="biof-loading">
        <div class="biof-loading-center">
            <div class="biof-loading-center-absolute">
                <div class="dot dot-one"></div>
                <div class="dot dot-two"></div>
                <div class="dot dot-three"></div>
            </div>
        </div>
    </div>

    <!-- HEADER -->
    <header id="header" class="header-area style-01 layout-03">
        <div class="header-middle biolife-sticky-object ">
            <div class="container">
                <div class="row">
                    <div class="col-lg-3 col-md-3 col-6 d-flex align-items-center">
                        <a href="index.php" class="biolife-logo"><img src="assets/images/favicon.png" alt="biolife logo"><b style="font-size: 190% ; color: black;">Freshly.id</b></a>
                    </div>
                    <div class="col-lg-6 col-md-6 d-none d-md-block text-center">
                        <div class="primary-menu">
                            <ul>
                                <li class="menu-item"><a href="index.php">Beranda</a></li>
                                <li>
                                    <a href="belanja.php">Belanja</a>
                                </li>
                                <li class="menu-item"><a href="contact.php">Hubungi Kami</a></li>
                            </ul>
                        </div>
                    </div>
                    <div class="col-lg-3 col-md-3 col-6 d-flex justify-content-end align-items-center">
                        <div class="biolife-cart-info">
                            <div class="mobile-search"></div>
                            <div class="login-button">
                                <a href="login.php" class="btn btn-sm btn-outline-primary" style="padding: 6px 15px; border-radius: 20px;">Login</a>
                            </div>
                            <div class="mobile-menu-toggle">
                                <a class="btn-toggle" data-object="open-mobile-menu" href="javascript:void(0)">
                                    <span></span>
                                    <span></span>
                                    <span></span>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </header>

    <!--Hero Section-->
    <div class="hero-section hero-background">
        <h1 class="page-title">Belanja</h1>
    </div>

    <!--Navigation section-->
    <div class="container">
        <nav class="biolife-nav">
            <ul>
                <li class="nav-item"><a href="index.php" class="permal-link">Beranda</a></li>
                <li class="nav-item"><span class="current-page">Keranjang</span></li>
            </ul>
        </nav>
    </div>

    <div class="page-contain shopping-cart">

        <!-- Main content -->
        <div id="main-content" class="main-content">
            <div class="container">
                <!--Cart Table-->
                <div class="shopping-cart-container">
                    <div class="row">
                        <div class="col-lg-9 col-md-12 col-sm-12 col-xs-12">
                            <h3 class="box-title">Keranjang Anda</h3>
                            <form class="shopping-cart-form" method="post">
                                <table class="shop_table cart-form">
                                    <thead>
                                        <tr>
                                            <th class="product-name">Nama Produk</th>
                                            <th class="product-price">Harga</th>
                                            <th class="product-quantity">Jumlah</th>
                                            <th class="product-subtotal">Total</th>
                                        </tr>
                                    </thead>

                                    <?php

                                    include 'admin/koneksi.php';

                                    if (!isset($_SESSION['id_user'])) {
                                        // kalau belum login, redirect (opsional)
                                        header("Location: login.php");
                                        exit;
                                    }

                                    $id_user = $_SESSION['id_user'];

                                    // Cek jika tombol update ditekan
                                    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_keranjang'])) {
                                        $berhasil_update = false;
                                        $gagal_update = false;

                                        foreach ($_POST as $key => $value) {
                                            if (strpos($key, 'qty') === 0) {
                                                $id_pesanan = str_replace('qty', '', $key);
                                                $id_pesanan = mysqli_real_escape_string($koneksi, $id_pesanan);
                                                $qty = intval($value);

                                                if ($qty > 0) {
                                                    // Ambil id_produk dan stok dari pesanan
                                                    $get_produk = mysqli_query($koneksi, "
                                                        SELECT pr.id_produk, pr.stok 
                                                        FROM tb_pesanan p
                                                        JOIN tb_produk pr ON p.id_produk = pr.id_produk
                                                        WHERE p.id_pesanan = '$id_pesanan' AND p.id_user = '$id_user'
                                                    ");
                                                    $produk = mysqli_fetch_assoc($get_produk);

                                                    if ($produk) {
                                                        $stok = intval($produk['stok']);

                                                        if ($qty <= $stok) {
                                                            // Update qty
                                                            $query = "UPDATE tb_pesanan SET qty = $qty WHERE id_pesanan = '$id_pesanan' AND id_user = '$id_user'";
                                                            if (mysqli_query($koneksi, $query)) {
                                                                $berhasil_update = true;
                                                            }
                                                        } else {
                                                            // Qty melebihi stok
                                                            $gagal_update = true;
                                                        }
                                                    }
                                                }
                                            }
                                        }

                                        // Tampilkan alert berdasarkan hasil
                                        if ($berhasil_update && !$gagal_update) {
                                            echo "<script>alert('Jumlah produk berhasil diperbarui!');</script>";
                                        } elseif ($gagal_update && !$berhasil_update) {
                                            echo "<script>alert('Gagal memperbarui: jumlah melebihi stok produk!');</script>";
                                        } elseif ($berhasil_update && $gagal_update) {
                                            echo "<script>alert('Sebagian produk berhasil diperbarui. Beberapa jumlah melebihi stok!');</script>";
                                        }
                                    }

                                    // Ambil ulang data setelah (atau sebelum) update
                                    $query = "SELECT p.*, pr.nm_produk, pr.harga, pr.gambar 
          FROM tb_pesanan p 
          JOIN tb_produk pr ON p.id_produk = pr.id_produk 
          WHERE p.id_user = '$id_user'";
                                    $result = mysqli_query($koneksi, $query);

                                    $subtotal = 0;
                                    ?>

                                    <tbody>
                                        <?php while ($row = mysqli_fetch_assoc($result)) : ?>
                                            <?php $total_item = $row['harga'] * $row['qty']; ?>
                                            <tr class="cart_item">
                                                <td class="product-thumbnail" data-title="Product Name">
                                                    <a class="prd-thumb" href="#">
                                                        <figure><img width="113" height="113" src="admin/produk_img/<?= $row['gambar']; ?>" alt="<?= $row['nm_produk']; ?>"></figure>
                                                    </a>
                                                    <a class="prd-name" href="#"><?= $row['nm_produk']; ?></a>
                                                    <div class="action">
                                                        <a href="hapus_item.php?id=<?= $row['id_pesanan']; ?>" class="remove"><i class="fa fa-trash-o" aria-hidden="true"></i></a>
                                                    </div>
                                                </td>
                                                <td class="product-price" data-title="Price">
                                                    <div class="price price-contain">
                                                        <ins><span class="price-amount"><span class="currencySymbol">Rp. </span><?= number_format($row['harga'], 0, ',', '.'); ?></span></ins>
                                                    </div>
                                                </td>
                                                <td class="product-quantity" data-title="Quantity">
                                                    <div class="quantity-box type1">
                                                        <div class="qty-input">
                                                            <input type="number" name="qty<?= $row['id_pesanan']; ?>" value="<?= $row['qty']; ?>" data-max_value="20" data-min_value="1" data-step="1">
                                                            <a type="button" class="qty-btn btn-up"><i class="fa fa-caret-up" aria-hidden="true"></i></a>
                                                            <a type="button" class="qty-btn btn-down"><i class="fa fa-caret-down" aria-hidden="true"></i></a>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td class="product-subtotal" data-title="Total">
                                                    <div class="price price-contain">
                                                        <ins><span class="price-amount"><span class="currencySymbol">Rp. </span><?= number_format($total_item, 0, ',', '.'); ?></span></ins>
                                                    </div>
                                                </td>
                                            </tr>
                                            <?php $subtotal += $total_item; ?>
                                        <?php endwhile; ?>

                                        <tr class="cart_item wrap-buttons">
                                            <td class="wrap-btn-control" colspan="4">
                                                <a class="btn back-to-shop" href="belanja.php">Kembali</a>
                                                <button class="btn btn-update" type="submit" name="update_keranjang">Perbarui Keranjang</button>
                                                <button class="btn btn-clear" type="reset">Hapus Semua</button>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </form>
                        </div>
                        <div class="col-lg-3 col-md-12 col-sm-12 col-xs-12">
                            <?php
                            // Hitung diskon
                            if ($subtotal > 500000) {
                                $diskon = $subtotal * 0.08;
                            } elseif ($subtotal > 150000) {
                                $diskon = $subtotal * 0.05;
                            } else {
                                $diskon = 0;
                            }

                            $total_bayar = $subtotal - $diskon;
                            ?>
                            <div class="shpcart-subtotal-block">
                                <div class="subtotal-line">
                                    <b class="stt-name">Subtotal</b>
                                    <span class="stt-price">Rp. <?= number_format($subtotal, 0, ',', '.'); ?></span>
                                </div>
                                <div class="subtotal-line">
                                    <b class="stt-name">Diskon</b>
                                    <span class="stt-price">Rp. <?= number_format($diskon, 0, ',', '.'); ?></span>
                                </div>
                                <div class="subtotal-line">
                                    <b class="stt-name">Total Bayar</b>
                                    <span class="stt-price">Rp. <?= number_format($total_bayar, 0, ',', '.'); ?></span>
                                </div>
                                <div class="tax-fee">
                                </div>
                                <div class="btn-checkout">
                                    <a href="#" class="btn checkout" onclick="checkout()">Check out</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <script>
                    function checkout() {
                        if (confirm("Yakin ingin checkout sekarang?")) {
                            fetch('checkout.php', {
                                    method: 'POST'
                                })
                                .then(response => response.json())
                                .then(data => {
                                    alert(data.message);
                                    if (data.success) {
                                        window.location.href = "belanja.php";
                                    }
                                })
                                .catch(error => {
                                    alert("Terjadi kesalahan saat proses checkout.");
                                    console.error(error);
                                });
                        }
                    }
                </script>



                <!--Related Product-->
                <div class="product-related-box single-layout">
                    <div class="biolife-title-box lg-margin-bottom-26px-im">
                    </div>
                    <ul class="products-list biolife-carousel nav-center-02 nav-none-on-mobile" data-slick='{"rows":1,"arrows":true,"dots":false,"infinite":false,"speed":400,"slidesMargin":0,"slidesToShow":5, "responsive":[{"breakpoint":1200, "settings":{ "slidesToShow": 4}},{"breakpoint":992, "settings":{ "slidesToShow": 3, "slidesMargin":20}},{"breakpoint":768, "settings":{ "slidesToShow": 2, "slidesMargin":10}}]}'>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <!-- FOOTER -->
    <footer id="footer" class="footer layout-03">
        <div class="footer-content background-footer-03">
            <div class="container">
                <div class="row">
                    <div class="col-lg-4 col-md-4 col-sm-9">
                        <section class="footer-item">
                            <a href="index.php" class="biolife-logo"><img src="assets/images/favicon.png" alt="biolife logo"><b style="font-size: 190% ; color: black;">Freshly.id</b></a>
                            <div class="footer-phone-info">
                                <i class="biolife-icon icon-head-phone"></i>
                                <p class="r-info">
                                    <span>Ada Pertanyaan ?</span>
                                    <span>0812-1500-8533</span>
                                </p>
                            </div>
                        </section>
                    </div>
                    <div class="col-lg-4 col-md-4 col-sm-6 md-margin-top-5px sm-margin-top-50px xs-margin-top-40px">
                        <section class="footer-item">

                        </section>
                    </div>
                    <div class="col-lg-4 col-md-4 col-sm-6 md-margin-top-5px sm-margin-top-50px xs-margin-top-40px">
                        <section class="footer-item">
                            <h3 class="section-title">Layanan Transportasi</h3>
                            <div class="contact-info-block footer-layout xs-padding-top-10px">
                                <ul class="contact-lines">
                                    <li>
                                        <p class="info-item">
                                            <i class="biolife-icon icon-location"></i>
                                            <b class="desc">Cepu-Blora, Jawa Tengah. Indonesia</b>
                                        </p>
                                    </li>
                                    <li>
                                        <p class="info-item">
                                            <i class="biolife-icon icon-phone"></i>
                                            <b class="desc">Telepon: 0812-1500-8533</b>
                                        </p>
                                    </li>
                                    <li>
                                        <p class="info-item">
                                            <i class="biolife-icon icon-letter"></i>
                                            <b class="desc">Email: Freshlyid@gmail.com</b>
                                        </p>
                                    </li>
                                    <li>
                                        <p class="info-item">
                                            <i class="biolife-icon icon-clock"></i>
                                            <b class="desc">Jam Buka: Setiap hari, Mulai Pukul 08:00</b>
                                        </p>
                                    </li>
                                </ul>
                            </div>
                            <div class="biolife-social inline">
                                <ul class="socials">
                                    <li><a href="https://instagram.com/dea.salsa.503/" title="instagram" class="socail-btn"><i class="fa fa-instagram" aria-hidden="true"></i></a></li>
                                    <li><a href="https://instagram.com/meaffq/" title="instagram" class="socail-btn"><i class="fa fa-instagram" aria-hidden="true"></i></a></li>
                                </ul>
                            </div>
                        </section>
                    </div>
                </div>
                <div class="row">
                    <div class="col-xs-12">
                        <div class="separator sm-margin-top-70px xs-margin-top-40px"></div>
                    </div>
                    <div class="col-lg-6 col-sm-6 col-xs-12">
                        <div class="copy-right-text">
                            <p><a href="templateshub.net">&copy; Copyright <strong><span>2025</span></strong>. All Rights Reserved</a></p>
                        </div>

                    </div>
                    <div class="col-lg-6 col-sm-6 col-xs-12">
                        <div class="payment-methods">
                            <ul>
                                <li><a href="#" class="payment-link"><img src="assets/images/card1.jpg" width="51" height="36" alt=""></a></li>
                                <li><a href="#" class="payment-link"><img src="assets/images/card2.jpg" width="51" height="36" alt=""></a></li>
                                <li><a href="#" class="payment-link"><img src="assets/images/card3.jpg" width="51" height="36" alt=""></a></li>
                                <li><a href="#" class="payment-link"><img src="assets/images/card4.jpg" width="51" height="36" alt=""></a></li>
                                <li><a href="#" class="payment-link"><img src="assets/images/card5.jpg" width="51" height="36" alt=""></a></li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </footer>

    <!--Footer For Mobile-->
    <div class="mobile-footer">
        <div class="mobile-footer-inner">
            <div class="mobile-block block-menu-main">
                <a class="menu-bar menu-toggle btn-toggle" data-object="open-mobile-menu" href="javascript:void(0)">
                    <span class="fa fa-bars"></span>
                    <span class="text">Menu</span>
                </a>
            </div>
            <div class="mobile-block block-sidebar">
                <a class="menu-bar filter-toggle btn-toggle" data-object="open-mobile-filter" href="javascript:void(0)">
                    <i class="fa fa-sliders" aria-hidden="true"></i>
                    <span class="text">Sidebar</span>
                </a>
            </div>
            <div class="mobile-block block-minicart">
                <a class="link-to-cart" href="#">
                    <span class="fa fa-shopping-bag" aria-hidden="true"></span>
                    <span class="text">Cart</span>
                </a>
            </div>
            <div class="mobile-block block-global">
                <a class="menu-bar myaccount-toggle btn-toggle" data-object="global-panel-opened" href="javascript:void(0)">
                    <span class="fa fa-globe"></span>
                    <span class="text">Global</span>
                </a>
            </div>
        </div>
    </div>

    <div class="mobile-block-global">
        <div class="biolife-mobile-panels">
            <span class="biolife-current-panel-title">Global</span>
            <a class="biolife-close-btn" data-object="global-panel-opened" href="#">&times;</a>
        </div>
        <div class="block-global-contain">
            <div class="glb-item my-account">
                <b class="title">My Account</b>
                <ul class="list">
                    <li class="list-item"><a href="#">Login/register</a></li>
                    <li class="list-item"><a href="#">Wishlist <span class="index">(8)</span></a></li>
                    <li class="list-item"><a href="#">Checkout</a></li>
                </ul>
            </div>
            <div class="glb-item currency">
                <b class="title">Currency</b>
                <ul class="list">
                    <li class="list-item"><a href="#">€ EUR (Euro)</a></li>
                    <li class="list-item"><a href="#">$ USD (Dollar)</a></li>
                    <li class="list-item"><a href="#">£ GBP (Pound)</a></li>
                    <li class="list-item"><a href="#">¥ JPY (Yen)</a></li>
                </ul>
            </div>
            <div class="glb-item languages">
                <b class="title">Language</b>
                <ul class="list inline">
                    <li class="list-item"><a href="#"><img src="assets/images/languages/us.jpg" alt="flag" width="24" height="18"></a></li>
                    <li class="list-item"><a href="#"><img src="assets/images/languages/fr.jpg" alt="flag" width="24" height="18"></a></li>
                    <li class="list-item"><a href="#"><img src="assets/images/languages/ger.jpg" alt="flag" width="24" height="18"></a></li>
                    <li class="list-item"><a href="#"><img src="assets/images/languages/jap.jpg" alt="flag" width="24" height="18"></a></li>
                </ul>
            </div>
        </div>
    </div>

    <!-- Scroll Top Button -->
    <a class="btn-scroll-top"><i class="biolife-icon icon-left-arrow"></i></a>

    <script src="assets/js/jquery-3.4.1.min.js"></script>
    <script src="assets/js/bootstrap.min.js"></script>
    <script src="assets/js/jquery.countdown.min.js"></script>
    <script src="assets/js/jquery.nice-select.min.js"></script>
    <script src="assets/js/jquery.nicescroll.min.js"></script>
    <script src="assets/js/slick.min.js"></script>
    <script src="assets/js/biolife.framework.js"></script>
    <script src="assets/js/functions.js"></script>
</body>

</html>