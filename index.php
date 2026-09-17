<?php

session_start();

if (!isset($_SESSION["login"]) || $_SESSION["login"] !== true) {
    header("Location: login.php");
    exit;
}

require_once "products.php";
require_once "functions.php";

$file = "products_data.json";

if (!file_exists($file)) {
    file_put_contents($file, json_encode($products, JSON_PRETTY_PRINT));
}

$dataProduk = json_decode(file_get_contents($file), true);

if (!is_array($dataProduk)) {
    $dataProduk = $products;
}

/* =========================
   DATA STATISTIK
========================= */

$totalProduk = count($dataProduk);
$totalStok = 0;
$produkStokSedikit = 0;
$totalNilaiStok = 0;

$elektronik = 0;
$aksesoris = 0;

foreach ($dataProduk as $produk) {

    $totalStok += (int) $produk["stok"];

    $totalNilaiStok += hitungTotalNilaiStok(
        $produk["harga"],
        $produk["stok"]
    );

    if ((int) $produk["stok"] < 3) {
        $produkStokSedikit++;
    }

    if ($produk["kategori"] === "Elektronik") {
        $elektronik++;
    }

    if ($produk["kategori"] === "Aksesoris") {
        $aksesoris++;
    }
}

if ($totalProduk > 0) {
    $persenElektronik = round(($elektronik / $totalProduk) * 100);
    $persenAksesoris = round(($aksesoris / $totalProduk) * 100);
} else {
    $persenElektronik = 0;
    $persenAksesoris = 0;
}

?>

<!DOCTYPE html>
<html lang="id">

<head>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Dashboard - Product Information System</title>

    <style>

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: "Segoe UI", Arial, sans-serif;
        }

        body {
            background: #f5f2eb;
            color: #292725;
        }

        /* =========================
           SIDEBAR
        ========================= */

        .sidebar {
            position: fixed;
            left: 0;
            top: 0;

            width: 245px;
            height: 100vh;

            background: #242321;

            padding: 28px 18px;

            display: flex;
            flex-direction: column;

            z-index: 10;
        }

        .brand {
            padding: 8px 13px 38px;
        }

        .brand-mark {
            width: 40px;
            height: 40px;

            border-radius: 12px;

            background: #d9b875;
            color: #242321;

            display: flex;
            align-items: center;
            justify-content: center;

            font-weight: 800;
            font-size: 18px;

            margin-bottom: 14px;
        }

        .brand h2 {
            color: #f5eee1;
            font-size: 18px;
            font-weight: 700;
        }

        .brand p {
            color: #8e8c88;
            font-size: 10px;

            margin-top: 5px;

            letter-spacing: 1.2px;
            text-transform: uppercase;
        }

        .menu-title {
            color: #77746f;

            font-size: 10px;

            letter-spacing: 1.5px;

            text-transform: uppercase;

            padding: 0 13px;

            margin-bottom: 10px;
        }

        .menu {
            display: flex;
            flex-direction: column;

            gap: 5px;
        }

        .menu a {
            display: flex;
            align-items: center;

            gap: 13px;

            padding: 13px;

            border-radius: 11px;

            text-decoration: none;

            color: #aaa8a3;

            font-size: 13px;

            transition: 0.25s;
        }

        .menu a:hover {
            background: #302f2c;

            color: #dfbd7b;

            transform: translateX(3px);
        }

        .menu a.active {
            background: #d9b875;

            color: #242321;

            font-weight: 600;

            box-shadow: 0 8px 20px rgba(217,184,117,0.13);
        }

        .menu-icon {
            width: 24px;

            text-align: center;

            font-size: 15px;
        }

        .sidebar-bottom {
            margin-top: auto;
        }

        .sidebar-divider {
            height: 1px;

            background: #393835;

            margin: 15px 10px 17px;
        }

        .logout {
            display: flex;

            align-items: center;

            justify-content: center;

            gap: 8px;

            padding: 11px;

            border-radius: 10px;

            background: #302f2c;

            color: #aaa8a3;

            text-decoration: none;

            font-size: 12px;

            transition: 0.2s;
        }

        .logout:hover {
            background: #3a3834;

            color: #dfbd7b;
        }


        /* =========================
           MAIN
        ========================= */

        .main {
            margin-left: 245px;

            min-height: 100vh;

            padding: 27px 38px 45px;
        }


        /* =========================
           HEADER
        ========================= */

        .header {
            height: 52px;

            display: flex;

            align-items: center;

            justify-content: space-between;

            margin-bottom: 25px;
        }

        .header-left small {
            display: block;

            color: #97938c;

            font-size: 11px;

            margin-bottom: 4px;
        }

        .header-left strong {
            font-size: 14px;

            font-weight: 600;
        }

        .user {
            display: flex;

            align-items: center;

            gap: 10px;
        }

        .avatar {
            width: 38px;
            height: 38px;

            border-radius: 50%;

            background: #e3d0a9;

            display: flex;

            align-items: center;
            justify-content: center;

            color: #5c4826;

            font-weight: 700;

            font-size: 13px;
        }

        .user-text small {
            display: block;

            color: #96918a;

            font-size: 9px;

            text-transform: uppercase;

            letter-spacing: 0.7px;
        }

        .user-text strong {
            font-size: 12px;
        }


        /* =========================
           WELCOME
        ========================= */

        .welcome {
            position: relative;

            overflow: hidden;

            min-height: 235px;

            background: #2b2a27;

            border-radius: 23px;

            padding: 38px 40px;

            color: white;

            display: flex;

            align-items: center;

            box-shadow: 0 16px 40px rgba(36,35,33,0.12);

            margin-bottom: 23px;
        }

        .welcome-content {
            position: relative;

            z-index: 2;

            max-width: 610px;
        }

        .welcome-label {
            display: inline-flex;

            align-items: center;

            gap: 7px;

            color: #dfbd7b;

            font-size: 10px;

            letter-spacing: 1.5px;

            margin-bottom: 15px;
        }

        .welcome-label::before {
            content: "";

            width: 23px;
            height: 1px;

            background: #dfbd7b;
        }

        .welcome h1 {
            font-size: 34px;

            line-height: 1.18;

            letter-spacing: -1px;

            margin-bottom: 13px;
        }

        .welcome h1 span {
            color: #dfbd7b;
        }

        .welcome p {
            color: #bdbab4;

            font-size: 13px;

            line-height: 1.7;

            max-width: 520px;
        }

        .welcome-decoration {
            position: absolute;

            width: 330px;
            height: 330px;

            border-radius: 50%;

            border: 1px solid rgba(223,189,123,0.18);

            right: -100px;
            top: -130px;
        }

        .welcome-decoration::before {
            content: "";

            position: absolute;

            width: 220px;
            height: 220px;

            border-radius: 50%;

            border: 1px solid rgba(223,189,123,0.13);

            left: 54px;
            top: 54px;
        }

        .welcome-decoration::after {
            content: "✦";

            position: absolute;

            color: #dfbd7b;

            font-size: 46px;

            left: 134px;
            top: 125px;

            opacity: 0.65;
        }


        /* =========================
           STATISTIK
        ========================= */

        .stats {
            display: grid;

            grid-template-columns: repeat(4, 1fr);

            gap: 15px;

            margin-bottom: 25px;
        }

        .stat {
            background: #ffffff;

            border: 1px solid #ebe5d9;

            border-radius: 16px;

            padding: 19px;

            box-shadow: 0 7px 22px rgba(41,39,35,0.045);

            transition: 0.25s;
        }

        .stat:hover {
            transform: translateY(-3px);

            box-shadow: 0 12px 28px rgba(41,39,35,0.08);
        }

        .stat-head {
            display: flex;

            align-items: center;

            justify-content: space-between;

            margin-bottom: 18px;
        }

        .stat-icon {
            width: 37px;
            height: 37px;

            border-radius: 10px;

            background: #f1e9d8;

            color: #9d7737;

            display: flex;

            align-items: center;

            justify-content: center;

            font-size: 15px;

            font-weight: 700;
        }

        .stat-dot {
            width: 5px;
            height: 5px;

            background: #d9b875;

            border-radius: 50%;
        }

        .stat-number {
            font-size: 24px;

            font-weight: 700;

            letter-spacing: -0.5px;

            margin-bottom: 4px;
        }

        .stat-label {
            color: #8b8882;

            font-size: 11px;
        }

        .stat-description {
            margin-top: 12px;

            color: #a17c3e;

            font-size: 9px;
        }


        /* =========================
           KONTEN BAWAH
        ========================= */

        .content-grid {
            display: grid;

            grid-template-columns: 1.45fr 1fr;

            gap: 18px;
        }

        .panel {
            background: #ffffff;

            border: 1px solid #ebe5d9;

            border-radius: 18px;

            padding: 24px;

            box-shadow: 0 7px 22px rgba(41,39,35,0.045);
        }

        .panel-header {
            display: flex;

            align-items: center;

            justify-content: space-between;

            margin-bottom: 21px;
        }

        .panel-title h2 {
            font-size: 17px;

            margin-bottom: 4px;
        }

        .panel-title p {
            color: #99958e;

            font-size: 10px;
        }

        .panel-badge {
            background: #f2eadb;

            color: #987337;

            padding: 6px 9px;

            border-radius: 20px;

            font-size: 9px;

            font-weight: 600;
        }


        /* =========================
           MENU
        ========================= */

        .menu-actions {
            display: grid;

            grid-template-columns: 1fr 1fr;

            gap: 12px;
        }

        .action {
            position: relative;

            min-height: 130px;

            padding: 19px;

            border-radius: 14px;

            text-decoration: none;

            overflow: hidden;

            transition: 0.25s;
        }

        .action:hover {
            transform: translateY(-3px);
        }

        .action.lihat {
            background: #292825;

            color: white;
        }

        .action.tambah {
            background: #f1e5ce;

            color: #292825;
        }

        .action-icon {
            width: 34px;
            height: 34px;

            border-radius: 9px;

            display: flex;

            align-items: center;

            justify-content: center;

            margin-bottom: 17px;

            font-size: 15px;
        }

        .lihat .action-icon {
            background: #44413c;

            color: #dfbd7b;
        }

        .tambah .action-icon {
            background: #dfc58f;

            color: #5d4825;
        }

        .action h3 {
            font-size: 14px;

            margin-bottom: 5px;
        }

        .action p {
            font-size: 10px;

            line-height: 1.5;

            opacity: 0.65;

            max-width: 190px;
        }

        .action-arrow {
            position: absolute;

            right: 17px;

            bottom: 15px;

            font-size: 16px;
        }


        /* =========================
           KATEGORI
        ========================= */

        .category {
            margin-bottom: 21px;
        }

        .category:last-child {
            margin-bottom: 0;
        }

        .category-top {
            display: flex;

            justify-content: space-between;

            margin-bottom: 8px;
        }

        .category-name {
            font-size: 11px;

            font-weight: 600;
        }

        .category-percent {
            font-size: 10px;

            color: #98948d;
        }

        .progress {
            width: 100%;

            height: 7px;

            background: #eeeae2;

            border-radius: 20px;

            overflow: hidden;
        }

        .progress-bar {
            height: 100%;

            background: #c9a866;

            border-radius: 20px;
        }

        .category-info {
            margin-top: 7px;

            color: #a09c95;

            font-size: 9px;
        }


        /* =========================
           INFORMASI STOK
        ========================= */

        .stock-info {
            margin-top: 18px;

            padding: 13px 14px;

            border-radius: 11px;

            background: #f7f3eb;

            border: 1px solid #eee6d7;

            display: flex;

            gap: 10px;

            align-items: flex-start;
        }

        .stock-icon {
            color: #ae843e;

            font-size: 13px;
        }

        .stock-text {
            font-size: 10px;

            line-height: 1.5;

            color: #77736c;
        }


        /* =========================
           FOOTER
        ========================= */

        .footer {
            text-align: center;

            color: #aaa59c;

            font-size: 9px;

            margin-top: 28px;
        }


        /* =========================
           RESPONSIVE
        ========================= */

        @media (max-width: 1050px) {

            .stats {
                grid-template-columns: 1fr 1fr;
            }

            .content-grid {
                grid-template-columns: 1fr;
            }

        }

        @media (max-width: 780px) {

            .sidebar {
                width: 210px;
            }

            .main {
                margin-left: 210px;

                padding: 25px;
            }

            .welcome h1 {
                font-size: 28px;
            }

        }

        @media (max-width: 620px) {

            .sidebar {
                display: none;
            }

            .main {
                margin-left: 0;

                padding: 20px;
            }

            .stats {
                grid-template-columns: 1fr;
            }

            .menu-actions {
                grid-template-columns: 1fr;
            }

            .welcome {
                padding: 30px 25px;
            }

            .welcome h1 {
                font-size: 25px;
            }

            .user-text {
                display: none;
            }

        }

    </style>

</head>


<body>


<!-- SIDEBAR -->

<aside class="sidebar">

    <div class="brand">

        <div class="brand-mark">
            P
        </div>

        <h2>
            Product Information System
        </h2>

        <p>
            Sistem Informasi Produk
        </p>

    </div>


    <div class="menu-title">
        Menu Utama
    </div>


    <nav class="menu">

        <a href="index.php" class="active">

            <span class="menu-icon">
                ⌂
            </span>

            Dashboard

        </a>


        <a href="lihat_produk.php">

            <span class="menu-icon">
                ▤
            </span>

            Lihat Produk

        </a>


        <a href="tambah_produk.php">

            <span class="menu-icon">
                ＋
            </span>

            Tambah Produk

        </a>

    </nav>


    <div class="sidebar-bottom">

        <div class="sidebar-divider"></div>

        <a href="logout.php" class="logout">

            <span>
                ↪
            </span>

            Keluar

        </a>

    </div>

</aside>



<!-- MAIN -->

<main class="main">


    <!-- HEADER -->

    <header class="header">

        <div class="header-left">

            <small>
                Product Information System
            </small>

            <strong>
                Dashboard
            </strong>

        </div>


        <div class="user">

            <div class="avatar">
                A
            </div>

            <div class="user-text">

                <small>
                    Pengguna
                </small>

                <strong>
                    Admin
                </strong>

            </div>

        </div>

    </header>



    <!-- WELCOME -->

    <section class="welcome">

        <div class="welcome-content">

            <div class="welcome-label">
                SISTEM INFORMASI PRODUK
            </div>


            <h1>
                Selamat Datang di
                <span>Product Information System.</span>
            </h1>


            <p>
                Kelola informasi produk dengan mudah,
                pantau jumlah persediaan, dan tambahkan
                data produk melalui sistem yang terorganisir.
            </p>

        </div>


        <div class="welcome-decoration"></div>

    </section>



    <!-- STATISTIK -->

    <section class="stats">


        <div class="stat">

            <div class="stat-head">

                <div class="stat-icon">
                    ▤
                </div>

                <div class="stat-dot"></div>

            </div>

            <div class="stat-number">
                <?= $totalProduk; ?>
            </div>

            <div class="stat-label">
                Total Produk
            </div>

            <div class="stat-description">
                Produk terdaftar
            </div>

        </div>


        <div class="stat">

            <div class="stat-head">

                <div class="stat-icon">
                    ◫
                </div>

                <div class="stat-dot"></div>

            </div>

            <div class="stat-number">
                <?= $totalStok; ?>
            </div>

            <div class="stat-label">
                Total Stok
            </div>

            <div class="stat-description">
                Unit tersedia
            </div>

        </div>


        <div class="stat">

            <div class="stat-head">

                <div class="stat-icon">
                    !
                </div>

                <div class="stat-dot"></div>

            </div>

            <div class="stat-number">
                <?= $produkStokSedikit; ?>
            </div>

            <div class="stat-label">
                Stok Menipis
            </div>

            <div class="stat-description">
                Perlu diperhatikan
            </div>

        </div>


        <div class="stat">

            <div class="stat-head">

                <div class="stat-icon">
                    Rp
                </div>

                <div class="stat-dot"></div>

            </div>

            <div
                class="stat-number"
                style="font-size:19px;"
            >
                Rp <?= number_format($totalNilaiStok, 0, ',', '.'); ?>
            </div>

            <div class="stat-label">
                Nilai Total Stok
            </div>

            <div class="stat-description">
                Nilai persediaan
            </div>

        </div>


    </section>



    <!-- KONTEN BAWAH -->

    <section class="content-grid">


        <!-- MENU UTAMA -->

        <div class="panel">

            <div class="panel-header">

                <div class="panel-title">

                    <h2>
                        Menu Utama
                    </h2>

                    <p>
                        Kelola data produk
                    </p>

                </div>

                <div class="panel-badge">
                    2 MENU
                </div>

            </div>


            <div class="menu-actions">


                <a
                    href="lihat_produk.php"
                    class="action lihat"
                >

                    <div class="action-icon">
                        ▤
                    </div>

                    <h3>
                        Lihat Produk
                    </h3>

                    <p>
                        Melihat seluruh data produk
                        yang tersimpan di dalam sistem.
                    </p>

                    <div class="action-arrow">
                        →
                    </div>

                </a>


                <a
                    href="tambah_produk.php"
                    class="action tambah"
                >

                    <div class="action-icon">
                        ＋
                    </div>

                    <h3>
                        Tambah Produk
                    </h3>

                    <p>
                        Menambahkan data produk baru
                        ke dalam sistem.
                    </p>

                    <div class="action-arrow">
                        →
                    </div>

                </a>


            </div>

        </div>



        <!-- INFORMASI PRODUK -->

        <div class="panel">

            <div class="panel-header">

                <div class="panel-title">

                    <h2>
                        Informasi Produk
                    </h2>

                    <p>
                        Berdasarkan kategori
                    </p>

                </div>

                <div class="panel-badge">
                    <?= $totalProduk; ?> PRODUK
                </div>

            </div>


            <!-- ELEKTRONIK -->

            <div class="category">

                <div class="category-top">

                    <span class="category-name">
                        Elektronik
                    </span>

                    <span class="category-percent">
                        <?= $persenElektronik; ?>%
                    </span>

                </div>


                <div class="progress">

                    <div
                        class="progress-bar"
                        style="width: <?= $persenElektronik; ?>%;"
                    ></div>

                </div>


                <div class="category-info">
                    <?= $elektronik; ?> produk elektronik
                </div>

            </div>


            <!-- AKSESORIS -->

            <div class="category">

                <div class="category-top">

                    <span class="category-name">
                        Aksesoris
                    </span>

                    <span class="category-percent">
                        <?= $persenAksesoris; ?>%
                    </span>

                </div>


                <div class="progress">

                    <div
                        class="progress-bar"
                        style="width: <?= $persenAksesoris; ?>%;"
                    ></div>

                </div>


                <div class="category-info">
                    <?= $aksesoris; ?> produk aksesoris
                </div>

            </div>


            <!-- INFORMASI STOK -->

            <div class="stock-info">

                <div class="stock-icon">
                    !
                </div>

                <div class="stock-text">

                    <?php if ($produkStokSedikit > 0): ?>

                        Terdapat
                        <strong>
                            <?= $produkStokSedikit; ?>
                        </strong>
                        produk dengan stok kurang dari
                        3 unit.

                    <?php else: ?>

                        Semua produk memiliki stok
                        yang mencukupi.

                    <?php endif; ?>

                </div>

            </div>


        </div>


    </section>



    <div class="footer">
        Product Information System • Sistem Informasi Produk
    </div>


</main>


</body>

</html>