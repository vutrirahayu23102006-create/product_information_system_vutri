<?php

session_start();

if (!isset($_SESSION["login"]) || $_SESSION["login"] !== true) {
    header("Location: login.php");
    exit;
}

require_once "products.php";
require_once "functions.php";

/* Membuat file JSON jika belum ada */
$fileData = "products_data.json";

if (!file_exists($fileData)) {
    file_put_contents(
        $fileData,
        json_encode($products, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)
    );
}

/* Membaca data produk dari JSON */
$dataProduk = json_decode(file_get_contents($fileData), true);

if (!is_array($dataProduk)) {
    $dataProduk = $products;
}

/* Jika ada produk baru berhasil ditambahkan */
$success = isset($_GET["success"]) && $_GET["success"] == "1";

/* Statistik */
$totalProduk = count($dataProduk);
$totalStok = 0;
$stokMenipis = 0;
$totalNilaiStok = 0;

foreach ($dataProduk as $product) {

    $totalStok += $product["stok"];

    if ($product["stok"] < 3) {
        $stokMenipis++;
    }

    $totalNilaiStok += hitungTotalNilaiStok(
        $product["harga"],
        $product["stok"]
    );
}

?>

<!DOCTYPE html>
<html lang="id">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Lihat Produk - Product Information System</title>

    <style>

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: "Segoe UI", Arial, sans-serif;
        }

        body {
            background: #f5f1e8;
            color: #2f2d2a;
        }

        .container {
            display: flex;
            min-height: 100vh;
        }

        /* ================= SIDEBAR ================= */

        .sidebar {
            width: 250px;
            background: #292824;
            color: #f5f1e8;
            padding: 28px 20px;
            position: fixed;
            top: 0;
            left: 0;
            bottom: 0;
        }

        .brand {
            margin-bottom: 45px;
            padding-left: 8px;
        }

        .brand h2 {
            font-size: 20px;
            letter-spacing: 0.3px;
            color: #f0c96a;
            margin-bottom: 6px;
        }

        .brand p {
            font-size: 12px;
            color: #c8c2b6;
        }

        .menu-title {
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: 1.5px;
            color: #aaa49a;
            margin: 0 0 12px 8px;
        }

        .menu a {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 13px 14px;
            margin-bottom: 7px;
            text-decoration: none;
            color: #d8d3c9;
            border-radius: 10px;
            font-size: 14px;
            transition: 0.3s;
        }

        .menu a:hover {
            background: #3a3833;
            color: #ffffff;
        }

        .menu a.active {
            background: #d5ae52;
            color: #292824;
            font-weight: 600;
        }

        .menu-icon {
            width: 22px;
            text-align: center;
            font-size: 16px;
        }

        .logout {
            position: absolute;
            left: 20px;
            right: 20px;
            bottom: 25px;
        }

        .logout a {
            display: flex;
            align-items: center;
            gap: 12px;
            text-decoration: none;
            color: #d8d3c9;
            padding: 13px 14px;
            border-radius: 10px;
            font-size: 14px;
            transition: 0.3s;
        }

        .logout a:hover {
            background: #3a3833;
            color: #ffffff;
        }

        /* ================= MAIN ================= */

        .main {
            margin-left: 250px;
            width: calc(100% - 250px);
            padding: 30px 40px;
        }

        /* ================= TOPBAR ================= */

        .topbar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 35px;
        }

        .topbar-left h1 {
            font-size: 26px;
            color: #292824;
            margin-bottom: 5px;
        }

        .topbar-left p {
            font-size: 13px;
            color: #817b70;
        }

        .admin {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .admin-icon {
            width: 38px;
            height: 38px;
            border-radius: 50%;
            background: #d5ae52;
            display: flex;
            justify-content: center;
            align-items: center;
            color: #292824;
            font-weight: bold;
        }

        .admin-text {
            font-size: 13px;
            color: #555149;
        }

        .admin-text strong {
            display: block;
            color: #292824;
            font-size: 14px;
        }

        /* ================= PAGE HEADER ================= */

        .page-header {
            background: #292824;
            border-radius: 20px;
            padding: 30px 32px;
            margin-bottom: 28px;
            position: relative;
            overflow: hidden;
        }

        .page-header::after {
            content: "";
            position: absolute;
            width: 180px;
            height: 180px;
            border: 1px solid rgba(240, 201, 106, 0.25);
            border-radius: 50%;
            right: -50px;
            top: -80px;
        }

        .page-header-content {
            position: relative;
            z-index: 2;
        }

        .page-header small {
            color: #d5ae52;
            font-size: 11px;
            letter-spacing: 1.5px;
            text-transform: uppercase;
        }

        .page-header h2 {
            color: #ffffff;
            font-size: 28px;
            margin: 8px 0;
        }

        .page-header p {
            color: #c8c2b6;
            font-size: 14px;
            max-width: 600px;
        }

        /* ================= SUCCESS ================= */

        .success {
            background: #e4efdf;
            border: 1px solid #b7cfad;
            color: #46603e;
            padding: 14px 18px;
            border-radius: 12px;
            margin-bottom: 25px;
            font-size: 13px;
        }

        /* ================= STATISTIK ================= */

        .stats {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 18px;
            margin-bottom: 30px;
        }

        .stat-card {
            background: #ffffff;
            border-radius: 16px;
            padding: 22px;
            border: 1px solid #e6dfd2;
            box-shadow: 0 5px 18px rgba(41, 40, 36, 0.05);
        }

        .stat-card p {
            font-size: 12px;
            color: #817b70;
            margin-bottom: 8px;
        }

        .stat-card h3 {
            font-size: 25px;
            color: #292824;
        }

        .stat-card .gold {
            color: #b48b32;
        }

        /* ================= SECTION ================= */

        .section-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 18px;
        }

        .section-header h2 {
            font-size: 20px;
            color: #292824;
        }

        .section-header p {
            font-size: 12px;
            color: #817b70;
            margin-top: 4px;
        }

        .btn-add {
            text-decoration: none;
            background: #d5ae52;
            color: #292824;
            padding: 11px 18px;
            border-radius: 10px;
            font-size: 13px;
            font-weight: 600;
            transition: 0.3s;
        }

        .btn-add:hover {
            background: #c49c43;
        }

        /* ================= SEARCH ================= */

        .search-box {
            background: #ffffff;
            border: 1px solid #e6dfd2;
            border-radius: 12px;
            padding: 13px 16px;
            width: 100%;
            margin-bottom: 22px;
            outline: none;
            font-size: 13px;
            color: #292824;
        }

        .search-box:focus {
            border-color: #d5ae52;
        }

        /* ================= PRODUK ================= */

        .product-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 20px;
        }

        .product-card {
            background: #ffffff;
            border: 1px solid #e6dfd2;
            border-radius: 18px;
            padding: 22px;
            box-shadow: 0 5px 18px rgba(41, 40, 36, 0.05);
            transition: 0.3s;
        }

        .product-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 10px 25px rgba(41, 40, 36, 0.08);
        }

        .product-top {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 18px;
        }

        .product-number {
            width: 42px;
            height: 42px;
            background: #f4ead0;
            color: #9a762c;
            border-radius: 12px;
            display: flex;
            justify-content: center;
            align-items: center;
            font-weight: 700;
            font-size: 15px;
        }

        .category {
            background: #f2eee6;
            color: #665f54;
            padding: 6px 10px;
            border-radius: 20px;
            font-size: 11px;
        }

        .category.gold {
            background: #f4ead0;
            color: #9a762c;
        }

        .product-card h3 {
            font-size: 18px;
            color: #292824;
            margin-bottom: 7px;
        }

        .description {
            color: #817b70;
            font-size: 12px;
            line-height: 1.6;
            min-height: 38px;
            margin-bottom: 18px;
        }

        .product-info {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 12px;
            border-top: 1px solid #eee8dd;
            padding-top: 16px;
        }

        .info-box {
            background: #f8f5ef;
            border-radius: 10px;
            padding: 11px;
        }

        .info-box span {
            display: block;
            color: #8b8478;
            font-size: 10px;
            margin-bottom: 4px;
        }

        .info-box strong {
            font-size: 13px;
            color: #292824;
        }

        .stock-low {
            color: #a14f3f !important;
        }

        .stock-normal {
            color: #5e704e !important;
        }

        .total-value {
            margin-top: 12px;
            padding: 11px;
            background: #292824;
            border-radius: 10px;
            color: #f5f1e8;
            font-size: 11px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .total-value strong {
            color: #f0c96a;
            font-size: 13px;
        }

        /* ================= EMPTY ================= */

        .empty {
            background: #ffffff;
            border: 1px solid #e6dfd2;
            border-radius: 18px;
            padding: 45px;
            text-align: center;
            color: #817b70;
        }

        .empty h3 {
            color: #292824;
            margin-bottom: 8px;
        }

        .empty p {
            font-size: 13px;
        }

        /* ================= FOOTER ================= */

        footer {
            margin-top: 40px;
            text-align: center;
            color: #928b80;
            font-size: 11px;
            padding-bottom: 15px;
        }

        /* ================= RESPONSIVE ================= */

        @media (max-width: 1000px) {

            .stats {
                grid-template-columns: repeat(2, 1fr);
            }

            .product-grid {
                grid-template-columns: 1fr;
            }

        }

        @media (max-width: 700px) {

            .sidebar {
                width: 210px;
            }

            .main {
                margin-left: 210px;
                width: calc(100% - 210px);
                padding: 25px;
            }

            .topbar-left h1 {
                font-size: 21px;
            }

            .stats {
                grid-template-columns: 1fr;
            }

        }

    </style>

</head>

<body>

<div class="container">

    <!-- ================= SIDEBAR ================= -->

    <aside class="sidebar">

        <div class="brand">
            <h2>Product Information System</h2>
            <p>Sistem Informasi Produk</p>
        </div>

        <div class="menu-title">
            Menu Utama
        </div>

        <nav class="menu">

            <a href="index.php">
                <span class="menu-icon">▣</span>
                <span>Dashboard</span>
            </a>

            <a href="lihat_produk.php" class="active">
                <span class="menu-icon">▤</span>
                <span>Lihat Produk</span>
            </a>

            <a href="tambah_produk.php">
                <span class="menu-icon">＋</span>
                <span>Tambah Produk</span>
            </a>

        </nav>

        <div class="logout">

            <a href="logout.php">
                <span class="menu-icon">↪</span>
                <span>Keluar</span>
            </a>

        </div>

    </aside>


    <!-- ================= MAIN ================= -->

    <main class="main">

        <!-- TOPBAR -->

        <div class="topbar">

            <div class="topbar-left">

                <h1>Product Information System</h1>

                <p>Lihat Produk</p>

            </div>

            <div class="admin">

                <div class="admin-icon">
                    A
                </div>

                <div class="admin-text">

                    <strong>Admin</strong>

                    Pengelola Sistem

                </div>

            </div>

        </div>


        <!-- HEADER -->

        <section class="page-header">

            <div class="page-header-content">

                <small>DATA PRODUK</small>

                <h2>Lihat Produk</h2>

                <p>
                    Lihat dan kelola informasi produk yang tersedia
                    dalam Product Information System.
                </p>

            </div>

        </section>


        <?php if ($success): ?>

            <div class="success">
                Produk berhasil ditambahkan ke dalam Product Information System.
            </div>

        <?php endif; ?>


        <!-- STATISTIK -->

        <section class="stats">

            <div class="stat-card">

                <p>Total Produk</p>

                <h3>
                    <?= $totalProduk ?>
                </h3>

            </div>


            <div class="stat-card">

                <p>Total Stok</p>

                <h3>
                    <?= $totalStok ?>
                </h3>

            </div>


            <div class="stat-card">

                <p>Stok Menipis</p>

                <h3 class="gold">
                    <?= $stokMenipis ?>
                </h3>

            </div>


            <div class="stat-card">

                <p>Nilai Total Stok</p>

                <h3 class="gold">
                    Rp <?= number_format($totalNilaiStok, 0, ',', '.') ?>
                </h3>

            </div>

        </section>


        <!-- DAFTAR PRODUK -->

        <div class="section-header">

            <div>

                <h2>Data Produk</h2>

                <p>
                    Daftar produk yang tersedia dalam sistem.
                </p>

            </div>

            <a href="tambah_produk.php" class="btn-add">
                + Tambah Produk
            </a>

        </div>


        <!-- SEARCH -->

        <input
            type="text"
            id="searchProduct"
            class="search-box"
            placeholder="Cari produk berdasarkan nama..."
        >


        <!-- PRODUK -->

        <?php if ($totalProduk > 0): ?>

            <div class="product-grid" id="productGrid">

                <?php foreach ($dataProduk as $product): ?>

                    <?php

                    $totalProdukStok = hitungTotalNilaiStok(
                        $product["harga"],
                        $product["stok"]
                    );

                    $stokClass = $product["stok"] < 3
                        ? "stock-low"
                        : "stock-normal";

                    ?>

                    <div
                        class="product-card"
                        data-name="<?= strtolower(htmlspecialchars($product["nama"])) ?>"
                    >

                        <div class="product-top">

                            <div class="product-number">
                                <?= htmlspecialchars($product["id"]) ?>
                            </div>

                            <div class="category gold">
                                <?= htmlspecialchars($product["kategori"]) ?>
                            </div>

                        </div>


                        <h3>
                            <?= htmlspecialchars($product["nama"]) ?>
                        </h3>


                        <p class="description">
                            <?= htmlspecialchars($product["deskripsi"]) ?>
                        </p>


                        <div class="product-info">

                            <div class="info-box">

                                <span>Harga</span>

                                <strong>
                                    Rp <?= number_format(
                                        $product["harga"],
                                        0,
                                        ',',
                                        '.'
                                    ) ?>
                                </strong>

                            </div>


                            <div class="info-box">

                                <span>Stok</span>

                                <strong class="<?= $stokClass ?>">

                                    <?= htmlspecialchars($product["stok"]) ?> unit

                                    <?php if ($product["stok"] < 3): ?>
                                        • Menipis
                                    <?php endif; ?>

                                </strong>

                            </div>

                        </div>


                        <div class="total-value">

                            <span>
                                Nilai Total Stok
                            </span>

                            <strong>
                                Rp <?= number_format(
                                    $totalProdukStok,
                                    0,
                                    ',',
                                    '.'
                                ) ?>
                            </strong>

                        </div>

                    </div>

                <?php endforeach; ?>

            </div>


            <div
                id="noResult"
                class="empty"
                style="display: none; margin-top: 20px;"
            >

                <h3>Produk Tidak Ditemukan</h3>

                <p>
                    Tidak ada produk yang sesuai dengan pencarian.
                </p>

            </div>

        <?php else: ?>

            <div class="empty">

                <h3>Belum Ada Produk</h3>

                <p>
                    Belum ada data produk yang tersedia dalam sistem.
                </p>

            </div>

        <?php endif; ?>


        <!-- FOOTER -->

        <footer>

            Product Information System • Sistem Informasi Produk

        </footer>

    </main>

</div>


<!-- ================= SEARCH SCRIPT ================= -->

<script>

    const searchInput = document.getElementById("searchProduct");
    const productCards = document.querySelectorAll(".product-card");
    const noResult = document.getElementById("noResult");

    if (searchInput) {

        searchInput.addEventListener("keyup", function () {

            const keyword = this.value.toLowerCase().trim();

            let ditemukan = false;

            productCards.forEach(function (card) {

                const name = card.getAttribute("data-name");

                if (name.includes(keyword)) {

                    card.style.display = "block";

                    ditemukan = true;

                } else {

                    card.style.display = "none";

                }

            });


            if (noResult) {

                if (!ditemukan && keyword !== "") {

                    noResult.style.display = "block";

                } else {

                    noResult.style.display = "none";

                }

            }

        });

    }

</script>

</body>

</html>