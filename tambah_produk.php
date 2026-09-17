<?php

session_start();

if (!isset($_SESSION["login"]) || $_SESSION["login"] !== true) {
    header("Location: login.php");
    exit;
}

require_once "products.php";
require_once "functions.php";

$fileData = "products_data.json";

/* Membuat file JSON jika belum ada */
if (!file_exists($fileData)) {
    file_put_contents(
        $fileData,
        json_encode($products, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)
    );
}

/* Membaca data produk */
$dataProduk = json_decode(file_get_contents($fileData), true);

if (!is_array($dataProduk)) {
    $dataProduk = $products;
}

$error = "";

/* Proses tambah produk */
if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $id = trim($_POST["id"] ?? "");
    $nama = trim($_POST["nama"] ?? "");
    $kategori = trim($_POST["kategori"] ?? "");
    $harga = trim($_POST["harga"] ?? "");
    $stok = trim($_POST["stok"] ?? "");
    $deskripsi = trim($_POST["deskripsi"] ?? "");

    if (
        $id === "" ||
        $nama === "" ||
        $kategori === "" ||
        $harga === "" ||
        $stok === "" ||
        $deskripsi === ""
    ) {

        $error = "Semua data produk harus diisi.";

    } elseif (!is_numeric($id) || $id < 1) {

        $error = "ID produk harus berupa angka.";

    } elseif (!is_numeric($harga) || $harga < 0) {

        $error = "Harga produk harus berupa angka yang valid.";

    } elseif (!is_numeric($stok) || $stok < 0) {

        $error = "Stok produk harus berupa angka yang valid.";

    } else {

        $id = (int) $id;
        $harga = (int) $harga;
        $stok = (int) $stok;

        /* Cek ID produk */
        $idSudahAda = false;

        foreach ($dataProduk as $product) {

            if ((int) $product["id"] === $id) {
                $idSudahAda = true;
                break;
            }
        }

        if ($idSudahAda) {

            $error = "ID produk sudah digunakan. Silakan gunakan ID lain.";

        } else {

            $dataProduk[] = [
                "id" => $id,
                "nama" => $nama,
                "kategori" => $kategori,
                "harga" => $harga,
                "stok" => $stok,
                "deskripsi" => $deskripsi
            ];

            file_put_contents(
                $fileData,
                json_encode(
                    $dataProduk,
                    JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE
                )
            );

            header("Location: lihat_produk.php?success=1");
            exit;
        }
    }
}

?>

<!DOCTYPE html>
<html lang="id">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Tambah Produk - Product Information System</title>

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
            max-width: 650px;
        }

        /* ================= CONTENT ================= */

        .content-grid {
            display: grid;
            grid-template-columns: 1.6fr 0.8fr;
            gap: 22px;
        }

        .form-card,
        .info-card {
            background: #ffffff;
            border: 1px solid #e6dfd2;
            border-radius: 18px;
            box-shadow: 0 5px 18px rgba(41, 40, 36, 0.05);
        }

        .form-card {
            padding: 28px;
        }

        .info-card {
            padding: 25px;
            height: fit-content;
        }

        .card-title {
            margin-bottom: 25px;
        }

        .card-title h3 {
            color: #292824;
            font-size: 19px;
            margin-bottom: 5px;
        }

        .card-title p {
            color: #817b70;
            font-size: 12px;
            line-height: 1.6;
        }

        /* ================= FORM ================= */

        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 18px;
        }

        .form-group {
            margin-bottom: 19px;
        }

        .form-group label {
            display: block;
            font-size: 12px;
            font-weight: 600;
            color: #4c4841;
            margin-bottom: 8px;
        }

        .form-group input,
        .form-group select,
        .form-group textarea {
            width: 100%;
            border: 1px solid #ddd6ca;
            border-radius: 10px;
            padding: 12px 13px;
            background: #fbfaf7;
            color: #292824;
            font-size: 13px;
            outline: none;
            transition: 0.3s;
        }

        .form-group input:focus,
        .form-group select:focus,
        .form-group textarea:focus {
            border-color: #d5ae52;
            background: #ffffff;
        }

        .form-group textarea {
            resize: vertical;
            min-height: 110px;
            line-height: 1.6;
        }

        .form-group input::placeholder,
        .form-group textarea::placeholder {
            color: #aaa49a;
        }

        /* ================= ERROR ================= */

        .error {
            background: #f8e8e4;
            border: 1px solid #dfb9ae;
            color: #914d40;
            padding: 13px 16px;
            border-radius: 10px;
            margin-bottom: 22px;
            font-size: 12px;
        }

        /* ================= BUTTON ================= */

        .form-actions {
            display: flex;
            gap: 10px;
            margin-top: 5px;
            padding-top: 20px;
            border-top: 1px solid #eee8dd;
        }

        .btn-submit {
            border: none;
            background: #d5ae52;
            color: #292824;
            padding: 12px 20px;
            border-radius: 10px;
            font-size: 13px;
            font-weight: 600;
            cursor: pointer;
            transition: 0.3s;
        }

        .btn-submit:hover {
            background: #c49c43;
        }

        .btn-cancel {
            text-decoration: none;
            background: #f2eee6;
            color: #5f594f;
            padding: 12px 20px;
            border-radius: 10px;
            font-size: 13px;
            font-weight: 600;
            transition: 0.3s;
        }

        .btn-cancel:hover {
            background: #e7e0d4;
        }

        /* ================= INFO CARD ================= */

        .info-card h3 {
            color: #292824;
            font-size: 17px;
            margin-bottom: 7px;
        }

        .info-card > p {
            color: #817b70;
            font-size: 12px;
            line-height: 1.6;
            margin-bottom: 20px;
        }

        .info-item {
            padding: 15px;
            background: #f8f5ef;
            border-radius: 12px;
            margin-bottom: 11px;
        }

        .info-item strong {
            display: block;
            color: #292824;
            font-size: 13px;
            margin-bottom: 4px;
        }

        .info-item span {
            color: #817b70;
            font-size: 11px;
            line-height: 1.5;
        }

        .category-box {
            margin-top: 20px;
            padding-top: 20px;
            border-top: 1px solid #eee8dd;
        }

        .category-box h4 {
            font-size: 12px;
            color: #4c4841;
            margin-bottom: 12px;
        }

        .category-list {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
        }

        .category {
            background: #f4ead0;
            color: #9a762c;
            padding: 7px 11px;
            border-radius: 20px;
            font-size: 11px;
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

            .content-grid {
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

            .form-row {
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


            <a href="lihat_produk.php">

                <span class="menu-icon">▤</span>

                <span>Lihat Produk</span>

            </a>


            <a href="tambah_produk.php" class="active">

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

                <p>Tambah Produk</p>

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

                <h2>Tambah Produk</h2>

                <p>
                    Tambahkan informasi produk baru ke dalam
                    Product Information System.
                </p>

            </div>

        </section>


        <!-- CONTENT -->

        <div class="content-grid">


            <!-- FORM -->

            <div class="form-card">

                <div class="card-title">

                    <h3>Informasi Produk</h3>

                    <p>
                        Lengkapi data produk dengan benar sebelum
                        menambahkan produk ke dalam sistem.
                    </p>

                </div>


                <?php if ($error !== ""): ?>

                    <div class="error">

                        <?= htmlspecialchars($error) ?>

                    </div>

                <?php endif; ?>


                <form method="POST">


                    <div class="form-row">

                        <div class="form-group">

                            <label for="id">
                                ID Produk
                            </label>

                            <input
                                type="number"
                                id="id"
                                name="id"
                                min="1"
                                placeholder="Contoh: 5"
                                value="<?= htmlspecialchars($_POST["id"] ?? "") ?>"
                                required
                            >

                        </div>


                        <div class="form-group">

                            <label for="nama">
                                Nama Produk
                            </label>

                            <input
                                type="text"
                                id="nama"
                                name="nama"
                                placeholder="Masukkan nama produk"
                                value="<?= htmlspecialchars($_POST["nama"] ?? "") ?>"
                                required
                            >

                        </div>

                    </div>


                    <div class="form-row">

                        <div class="form-group">

                            <label for="kategori">
                                Kategori
                            </label>

                            <select
                                id="kategori"
                                name="kategori"
                                required
                            >

                                <option value="">
                                    Pilih Kategori
                                </option>

                                <option
                                    value="Elektronik"
                                    <?= (($_POST["kategori"] ?? "") === "Elektronik") ? "selected" : "" ?>
                                >
                                    Elektronik
                                </option>

                                <option
                                    value="Aksesoris"
                                    <?= (($_POST["kategori"] ?? "") === "Aksesoris") ? "selected" : "" ?>
                                >
                                    Aksesoris
                                </option>

                            </select>

                        </div>


                        <div class="form-group">

                            <label for="harga">
                                Harga
                            </label>

                            <input
                                type="number"
                                id="harga"
                                name="harga"
                                min="0"
                                placeholder="Contoh: 500000"
                                value="<?= htmlspecialchars($_POST["harga"] ?? "") ?>"
                                required
                            >

                        </div>

                    </div>


                    <div class="form-group">

                        <label for="stok">
                            Stok
                        </label>

                        <input
                            type="number"
                            id="stok"
                            name="stok"
                            min="0"
                            placeholder="Contoh: 10"
                            value="<?= htmlspecialchars($_POST["stok"] ?? "") ?>"
                            required
                        >

                    </div>


                    <div class="form-group">

                        <label for="deskripsi">
                            Deskripsi
                        </label>

                        <textarea
                            id="deskripsi"
                            name="deskripsi"
                            placeholder="Masukkan deskripsi produk"
                            required
                        ><?= htmlspecialchars($_POST["deskripsi"] ?? "") ?></textarea>

                    </div>


                    <div class="form-actions">

                        <button
                            type="submit"
                            class="btn-submit"
                        >
                            + Tambah Produk
                        </button>


                        <a
                            href="lihat_produk.php"
                            class="btn-cancel"
                        >
                            Batal
                        </a>

                    </div>

                </form>

            </div>


            <!-- INFORMASI -->

            <div class="info-card">

                <h3>Informasi Produk</h3>

                <p>
                    Pastikan setiap data produk yang dimasukkan
                    sudah sesuai dengan informasi yang tersedia.
                </p>


                <div class="info-item">

                    <strong>ID Produk</strong>

                    <span>
                        ID digunakan sebagai identitas setiap produk
                        dalam sistem.
                    </span>

                </div>


                <div class="info-item">

                    <strong>Nama Produk</strong>

                    <span>
                        Masukkan nama produk yang jelas dan mudah
                        dikenali.
                    </span>

                </div>


                <div class="info-item">

                    <strong>Harga</strong>

                    <span>
                        Masukkan harga produk dalam satuan Rupiah.
                    </span>

                </div>


                <div class="info-item">

                    <strong>Stok</strong>

                    <span>
                        Masukkan jumlah stok produk yang tersedia.
                    </span>

                </div>


                <div class="category-box">

                    <h4>Kategori Produk</h4>


                    <div class="category-list">

                        <span class="category">
                            Elektronik
                        </span>

                        <span class="category">
                            Aksesoris
                        </span>

                    </div>

                </div>

            </div>

        </div>


        <!-- FOOTER -->

        <footer>

            Product Information System • Sistem Informasi Produk

        </footer>

    </main>

</div>

</body>

</html>