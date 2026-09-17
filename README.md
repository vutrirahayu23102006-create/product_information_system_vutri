# Product Information System

Mini Project 1 - Product Information System.

## Deskripsi

Product Information System merupakan sistem informasi sederhana
yang digunakan untuk menyimpan, mengolah, dan menampilkan data produk.

Sistem ini dibuat menggunakan PHP dengan konsep pemisahan
Data Layer, Processing Layer, dan Presentation Layer.

## Fitur

- Login dan Logout
- Dashboard
- Melihat data produk
- Menambahkan produk
- Menghitung nilai total stok
- Menampilkan informasi stok produk
- Pencarian produk berdasarkan nama
- Peringatan untuk produk dengan stok menipis

## Data Produk

Data produk terdiri dari:

- ID Produk
- Nama Produk
- Kategori
- Harga
- Stok
- Deskripsi

## Struktur Project

- `products.php` : menyimpan data produk
- `functions.php` : berisi fungsi pengolahan data
- `index.php` : halaman Dashboard
- `lihat_produk.php` : halaman untuk melihat data produk
- `tambah_produk.php` : halaman untuk menambahkan produk
- `login.php` : halaman login
- `logout.php` : proses logout
- `products_data.json` : menyimpan data produk
- `README.md` : dokumentasi project

## Teknologi

- PHP
- HTML
- CSS
- JavaScript
- JSON

## Konsep Pengembangan

Project menggunakan konsep pemisahan bagian sistem menjadi:

### Data Layer

Data produk disimpan dalam `products.php` dan
`products_data.json`.

### Processing Layer

Pengolahan data dilakukan melalui fungsi
`hitungTotalNilaiStok()` pada `functions.php`.

### Presentation Layer

Tampilan sistem dibuat pada halaman PHP seperti
`index.php`, `lihat_produk.php`, dan `tambah_produk.php`.

## Cara Menjalankan

1. Pastikan XAMPP sudah terpasang.
2. Aktifkan Apache pada XAMPP.
3. Simpan project di folder `htdocs`.
4. Buka browser.
5. Akses:

`http://localhost/product_information_system_vutri/`

6. Login menggunakan akun yang telah dibuat.