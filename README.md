📚 Library App – Sistem Manajemen Perpustakaan Digital

Library App adalah aplikasi Sistem Manajemen Perpustakaan Digital yang dikembangkan sebagai Tugas Besar Mata Kuliah Integrasi Aplikasi. Aplikasi ini mengintegrasikan frontend dan backend melalui REST API dengan format data JSON. Backend dibangun menggunakan Laravel dengan autentikasi berbasis token menggunakan Laravel Sanctum, sedangkan frontend dikembangkan menggunakan HTML, CSS, dan JavaScript. Sistem ini menyediakan fitur pengelolaan data buku, peminjaman dan pengembalian buku, serta informasi denda secara terintegrasi dan aman.

*Tujuan
- Mengimplementasikan REST API menggunakan Laravel
- Menerapkan integrasi aplikasi frontend dan backend
- Menggunakan JSON sebagai format pertukaran data
- Mengimplementasikan autentikasi berbasis token
- Menerapkan konsep integrasi aplikasi sesuai materi perkuliahan

*Arsitektur Sistem
- Backend: REST API (Laravel)
- Frontend: HTML, CSS, JavaScript (Vanilla)
- Database: MySQL
- Authentication: Bearer token

*Teknologi yang Digunakan
- Laravel
- PHP
- MySQL
- HTML, CSS, JavaScript
- Postman (API Testing)

Fitur Utama
*Backend
- Authentication & Authorization
- CRUD Buku
- Transaksi Peminjaman & Pengembalian
- Perhitungan denda
- REST API dengan format JSON

*Frontend
- Login & Register
- Dashboard
- Manajemen data buku
- Peminjaman dan pengembalian buku
- Validasi form
- Integrasi API

🔗 REST API Endpoint
No	Method	Endpoint	Modul	Deskripsi
1	POST	/api/login	Authentication	Login member atau librarian
2	POST	/api/logout	Authentication	Logout user
3	GET	/api/me	Authentication	Data user yang sedang login
4	GET	/api/books	Buku	Menampilkan semua buku
5	GET	/api/books/{id}	Buku	Menampilkan detail buku
6	GET	/api/books/search	Buku	Mencari buku berdasarkan judul/kategori
7	POST	/api/books	Buku	Menambah buku (librarian)
8	PUT	/api/books/{id}	Buku	Mengubah data buku (librarian)
9	DELETE	/api/books/{id}	Buku	Menghapus buku (librarian)
10	POST	/api/borrowings	Peminjaman	Mengajukan peminjaman buku (member)
11	GET	/api/borrowings	Peminjaman	Melihat riwayat peminjaman
12	GET	/api/borrowings/{id}	Peminjaman	Detail peminjaman
13	PUT	/api/borrowings/{id}/verify	Peminjaman	Verifikasi peminjaman (librarian)
14	POST	/api/returns	Pengembalian	Mengajukan pengembalian buku
15	GET	/api/returns	Pengembalian	Melihat data pengembalian
16	PUT	/api/returns/{id}/verify	Pengembalian	Verifikasi pengembalian (librarian)
17	GET	/api/fines	Denda	Melihat data denda member
18	GET	/api/fines/{id}	Denda	Detail denda
19	POST	/api/fines/{id}/pay	Denda	Pembayaran denda
20	POST	/api/fines/recalculate	Denda	Hitung ulang denda

- File .env.example
APP_NAME=Laravel
APP_ENV=local
APP_KEY=base64:nJ3uR9rgLmjq2mQqJdcXuJv5wx4xowh9pEwop4zpwjk=
APP_DEBUG=true
APP_URL=http://localhost
APP_TIMEZONE=Asia/Jakarta

LOG_CHANNEL=stack
LOG_LEVEL=debug

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=db_perpustakaan
DB_USERNAME=root
DB_PASSWORD=

SESSION_DRIVER=file
SESSION_LIFETIME=120

SANCTUM_STATEFUL_DOMAINS=localhost,127.0.0.1
SESSION_DOMAIN=localhost

CACHE_DRIVER=file
QUEUE_CONNECTION=sync

# Screenshoot Aplikasi

* Halaman Admin

Login Admin  
<img src="https://github.com/user-attachments/assets/1517a7e4-be93-4bd5-9299-c2fba3a25ec3" width="450"/>

Dashboard Admin  
<img src="https://github.com/user-attachments/assets/08e8a6bc-d4c7-475d-b9e4-62e011fc4e21" width="800"/>

Kelola Inventaris Buku  
<img src="https://github.com/user-attachments/assets/474b2c18-50ee-460a-8d22-ebde5a2974b3" width="800"/>

Pencarian Buku (Admin)  
<img src="https://github.com/user-attachments/assets/4c561c3e-838f-40e8-8483-db93126af276" width="800"/>

Konfirmasi Peminjaman Buku  
<img src="https://github.com/user-attachments/assets/eacefcef-1b09-4876-9090-f86b7f16275b" width="800"/>

Kelola Denda (Admin)  
<img src="https://github.com/user-attachments/assets/3de1f037-88be-4ae6-bd28-07339ab8c73f" width="800"/>

---

*Halaman Member

Registrasi Member  
<img src="https://github.com/user-attachments/assets/7ab3762c-35ce-4d94-9039-54ac628a41d6" width="450"/>

Login Member  
<img src="https://github.com/user-attachments/assets/7d64612e-aada-4b5e-a9a6-6c17b300bda3" width="450"/>

Dashboard Member  
<img src="https://github.com/user-attachments/assets/a80656eb-9335-4354-b244-6f83b09bc780" width="900"/>

Pencarian Buku (Member)  
<img src="https://github.com/user-attachments/assets/b94b4b89-d5c1-4f92-9192-4117507b6ae8" width="900"/>

Katalog Buku  
<img src="https://github.com/user-attachments/assets/cda5a916-3fd1-432c-8798-ef65f2fa1df6" width="700"/>

Form Peminjaman Buku  
<img src="https://github.com/user-attachments/assets/b6be2732-ee5d-47cd-bf66-b31ddfd4bf4e" width="350"/>

Riwayat Peminjaman Buku  
<img src="https://github.com/user-attachments/assets/b4590d45-a45a-44fa-b01c-d7e5a66b6d10" width="700"/>

Pengembalian Buku dan Informasi Denda  
<img src="https://github.com/user-attachments/assets/e66b62b8-9cb2-49f5-a71c-531aec8d5273" width="800"/>

Daftar Denda Member  
<img src="https://github.com/user-attachments/assets/c5426d18-60ee-4f0c-b97b-8a2608e91ac5" width="800"/>

*database schema


[Uploading db_perpust-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Waktu pembuatan: 12 Jan 2026 pada 04.43
-- Versi server: 10.4.32-MariaDB
-- Versi PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `db_perpustakaan`
--

-- --------------------------------------------------------

--
-- Struktur dari tabel `books`
--

CREATE TABLE `books` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `api_book_id` varchar(255) DEFAULT NULL,
  `title` varchar(255) NOT NULL,
  `author` varchar(255) DEFAULT NULL,
  `publisher` varchar(255) DEFAULT NULL,
  `year` int(11) DEFAULT NULL,
  `stock` int(11) NOT NULL DEFAULT 0,
  `location` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `books`
--

INSERT INTO `books` (`id`, `api_book_id`, `title`, `author`, `publisher`, `year`, `stock`, `location`, `created_at`, `updated_at`) VALUES
(1, 'OL15437W', 'Lilith', 'George MacDonald', NULL, NULL, 7, NULL, '2026-01-02 04:18:20', '2026-01-12 02:36:08'),
(2, 'OL2779754W', 'On the Beach', 'Nevil Shute', NULL, NULL, 2, NULL, '2026-01-02 04:25:34', '2026-01-02 04:30:05'),
(3, 'OL505944W', 'The Razor\'s Edge', 'William Somerset Maugham', NULL, NULL, 2, NULL, '2026-01-02 06:59:09', '2026-01-03 07:50:12'),
(4, 'OL1230715W', 'La Peste', 'Albert Camus', NULL, NULL, 4, NULL, '2026-01-02 07:35:53', '2026-01-08 04:39:40'),
(5, 'OL1168083W', 'Nineteen Eighty-Four', 'George Orwell', NULL, NULL, 1, NULL, '2026-01-03 07:59:44', '2026-01-08 04:56:04'),
(6, 'OL505781W', 'Of Human Bondage', 'William Somerset Maugham', NULL, NULL, 1, NULL, '2026-01-03 08:13:25', '2026-01-03 08:14:15'),
(7, 'OL112003W', 'Lust for Life', 'Irving Stone', NULL, NULL, 4, NULL, '2026-01-03 08:21:58', '2026-01-12 02:36:48'),
(8, 'OL51145W', 'It Can\'t Happen Here', 'Sinclair Lewis', NULL, NULL, 3, NULL, '2026-01-03 08:30:58', '2026-01-08 04:55:22'),
(9, 'OL85891W', 'The Jewel of Seven Stars', 'Bram Stoker', NULL, NULL, 66, NULL, '2026-01-04 02:04:38', '2026-01-07 18:07:30'),
(10, 'OL85892W', 'Dracula', 'Bram Stoker', NULL, NULL, 5, NULL, '2026-01-04 02:10:08', '2026-01-07 17:53:42'),
(11, 'OL18412W', 'Dorothy and the Wizard in Oz', 'L. Frank Baum', NULL, NULL, 9, NULL, '2026-01-07 10:35:53', '2026-01-07 10:37:03'),
(12, 'OL29024382W', 'A Time For Everything', 'Brona Mills', 'Unknown', 2026, 19, NULL, '2026-01-07 15:10:48', '2026-01-07 16:34:11'),
(13, 'OL13273656W', 'Alejandro Magno', 'Antonio Guzmán Guerra', 'Unknown', 2026, 0, NULL, '2026-01-07 15:58:10', '2026-01-07 15:58:10'),
(15, 'OL36725451W', 'Pyramid Game', 'David Petrie', 'Unknown', 2026, 3, NULL, '2026-01-07 18:15:16', '2026-01-08 03:51:41'),
(16, 'OL66513W', 'Emma', 'Jane Austen', 'Unknown', 2026, 0, NULL, '2026-01-08 03:54:32', '2026-01-08 03:54:32'),
(17, 'OL16313W', 'Vanity Fair', 'William Makepeace Thackeray', 'Unknown', 2026, 0, NULL, '2026-01-08 03:55:33', '2026-01-08 03:55:33'),
(18, '1', 'Pemrograman Web', 'Unknown', 'Unknown', 2026, 0, NULL, '2026-01-08 08:12:09', '2026-01-08 08:12:09'),
(19, 'OL450063W', 'Frankenstein or The Modern Prometheus', 'Mary Shelley', NULL, NULL, 0, NULL, '2026-01-09 15:22:08', '2026-01-09 15:22:08');

-- --------------------------------------------------------

--
-- Struktur dari tabel `cache`
--

CREATE TABLE `cache` (
  `key` varchar(255) NOT NULL,
  `value` mediumtext NOT NULL,
  `expiration` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `cache_locks`
--

CREATE TABLE `cache_locks` (
  `key` varchar(255) NOT NULL,
  `owner` varchar(255) NOT NULL,
  `expiration` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `failed_jobs`
--

CREATE TABLE `failed_jobs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `uuid` varchar(255) NOT NULL,
  `connection` text NOT NULL,
  `queue` text NOT NULL,
  `payload` longtext NOT NULL,
  `exception` longtext NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `jobs`
--

CREATE TABLE `jobs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `queue` varchar(255) NOT NULL,
  `payload` longtext NOT NULL,
  `attempts` tinyint(3) UNSIGNED NOT NULL,
  `reserved_at` int(10) UNSIGNED DEFAULT NULL,
  `available_at` int(10) UNSIGNED NOT NULL,
  `created_at` int(10) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `job_batches`
--

CREATE TABLE `job_batches` (
  `id` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `total_jobs` int(11) NOT NULL,
  `pending_jobs` int(11) NOT NULL,
  `failed_jobs` int(11) NOT NULL,
  `failed_job_ids` longtext NOT NULL,
  `options` mediumtext DEFAULT NULL,
  `cancelled_at` int(11) DEFAULT NULL,
  `created_at` int(11) NOT NULL,
  `finished_at` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `migrations`
--

CREATE TABLE `migrations` (
  `id` int(10) UNSIGNED NOT NULL,
  `migration` varchar(255) NOT NULL,
  `batch` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `migrations`
--

INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
(1, '0001_01_01_000000_create_users_table', 1),
(2, '0001_01_01_000001_create_cache_table', 1),
(3, '0001_01_01_000002_create_jobs_table', 1),
(4, '2025_12_13_142623_add_role_to_users_table', 1),
(5, '2025_12_13_143543_create_books_table', 1),
(6, '2025_12_14_142409_create_personal_access_tokens_table', 1),
(7, '2025_12_28_000002_create_transactions_table', 1),
(8, '2026_01_07_000000_update_transactions_table', 2),
(9, '2026_01_08_001104_add_fine_payment_requested_at_to_transactions_table', 3);

-- --------------------------------------------------------

--
-- Struktur dari tabel `password_reset_tokens`
--

CREATE TABLE `password_reset_tokens` (
  `email` varchar(255) NOT NULL,
  `token` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `personal_access_tokens`
--

CREATE TABLE `personal_access_tokens` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `tokenable_type` varchar(255) NOT NULL,
  `tokenable_id` bigint(20) UNSIGNED NOT NULL,
  `name` text NOT NULL,
  `token` varchar(64) NOT NULL,
  `abilities` text DEFAULT NULL,
  `last_used_at` timestamp NULL DEFAULT NULL,
  `expires_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `personal_access_tokens`
--

INSERT INTO `personal_access_tokens` (`id`, `tokenable_type`, `tokenable_id`, `name`, `token`, `abilities`, `last_used_at`, `expires_at`, `created_at`, `updated_at`) VALUES
(7, 'App\\Models\\User', 4, 'frontend', '21243e84dced6fcd7699df88c2a9dd4d23762a61be200db88d89004bd501b297', '[\"*\"]', '2026-01-04 02:10:10', NULL, '2026-01-04 02:08:10', '2026-01-04 02:10:10'),
(59, 'App\\Models\\User', 2, 'frontend', '69974f0ece0b0ddc318f3bb200b7246f15b2eb56d593bcea2a9e3883e3b3916e', '[\"*\"]', '2026-01-12 03:23:21', NULL, '2026-01-12 03:23:17', '2026-01-12 03:23:21'),
(60, 'App\\Models\\User', 3, 'frontend', '5e186ac5855b78efc77fafc02ac4fbc8d9556645e0af839bc160560c86bbc22a', '[\"*\"]', '2026-01-12 03:35:26', NULL, '2026-01-12 03:27:25', '2026-01-12 03:35:26');

-- --------------------------------------------------------

--
-- Struktur dari tabel `sessions`
--

CREATE TABLE `sessions` (
  `id` varchar(255) NOT NULL,
  `user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `payload` longtext NOT NULL,
  `last_activity` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `transactions`
--

CREATE TABLE `transactions` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `book_id` bigint(20) UNSIGNED NOT NULL,
  `status` enum('pending','borrowed','return_pending','returned','rejected') NOT NULL DEFAULT 'pending',
  `rejected_reason` varchar(255) DEFAULT NULL,
  `borrow_date` datetime DEFAULT NULL,
  `due_date` datetime DEFAULT NULL,
  `return_date` datetime DEFAULT NULL,
  `approved_by` bigint(20) UNSIGNED DEFAULT NULL,
  `fine_amount` int(11) NOT NULL DEFAULT 0,
  `fine_paid_at` datetime DEFAULT NULL,
  `fine_payment_requested_at` datetime DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `transactions`
--

INSERT INTO `transactions` (`id`, `user_id`, `book_id`, `status`, `rejected_reason`, `borrow_date`, `due_date`, `return_date`, `approved_by`, `fine_amount`, `fine_paid_at`, `fine_payment_requested_at`, `created_at`, `updated_at`) VALUES
(1, 3, 1, 'return_pending', NULL, '2026-01-02 11:22:15', '2026-01-09 11:22:15', NULL, 2, 0, NULL, NULL, '2026-01-02 04:18:20', '2026-01-08 03:58:15'),
(2, 3, 2, 'returned', NULL, '2026-01-02 11:26:27', '2026-01-03 11:26:27', '2026-01-02 11:30:05', 2, 0, NULL, NULL, '2026-01-02 04:25:34', '2026-01-02 04:30:05'),
(3, 3, 3, 'return_pending', NULL, '2026-01-03 14:50:12', '2026-01-10 14:50:12', NULL, 2, 0, NULL, NULL, '2026-01-02 06:59:09', '2026-01-08 03:58:25'),
(4, 3, 4, 'returned', NULL, '2026-01-03 14:50:05', '2026-01-04 14:50:05', '2026-01-08 11:39:40', 2, 4000, '2026-01-08 11:36:58', '2026-01-08 11:13:40', '2026-01-02 07:35:53', '2026-01-08 04:39:40'),
(5, 3, 5, 'returned', NULL, '2026-01-03 15:00:26', '2026-01-04 15:00:26', '2026-01-08 11:56:04', 2, 4000, '2026-01-08 11:27:01', '2026-01-08 11:13:51', '2026-01-03 07:59:44', '2026-01-08 04:56:04'),
(6, 3, 6, 'borrowed', NULL, '2026-01-03 15:14:15', '2026-01-04 15:14:15', NULL, 2, 0, NULL, NULL, '2026-01-03 08:13:25', '2026-01-03 08:14:15'),
(7, 3, 7, 'returned', NULL, '2026-01-03 15:23:36', '2026-01-04 15:23:36', '2026-01-12 09:36:48', 2, 8000, '2026-01-12 09:36:48', '2026-01-12 09:36:39', '2026-01-03 08:21:58', '2026-01-12 02:36:48'),
(8, 3, 8, 'returned', NULL, '2026-01-01 00:00:00', '2026-01-02 00:00:00', '2026-01-08 11:55:22', 2, 3000, '2026-01-08 00:14:43', '2026-01-08 00:13:21', '2026-01-03 08:30:58', '2026-01-08 04:55:22'),
(9, 3, 9, 'rejected', 'Lainnya', '2026-01-04 00:00:00', '2026-01-05 00:00:00', NULL, 2, 0, NULL, NULL, '2026-01-04 02:04:38', '2026-01-07 18:17:22'),
(10, 4, 10, 'borrowed', NULL, '2026-01-04 00:00:00', '2026-01-05 00:00:00', NULL, 2, 0, NULL, NULL, '2026-01-04 02:10:08', '2026-01-04 02:10:41'),
(11, 3, 11, 'borrowed', NULL, '2026-01-07 00:00:00', '2026-01-08 00:00:00', NULL, 2, 0, NULL, NULL, '2026-01-07 10:36:15', '2026-01-07 10:37:03'),
(12, 3, 5, 'borrowed', NULL, '2026-01-07 00:00:00', '2026-01-08 00:00:00', NULL, 2, 0, NULL, NULL, '2026-01-07 11:17:47', '2026-01-07 11:18:33'),
(13, 3, 4, 'borrowed', NULL, '2026-01-07 00:00:00', '2026-01-08 00:00:00', NULL, 2, 0, NULL, NULL, '2026-01-07 13:12:27', '2026-01-07 13:17:37'),
(14, 3, 12, 'borrowed', NULL, '2026-01-07 00:00:00', '2026-01-08 00:00:00', NULL, 2, 0, NULL, NULL, '2026-01-07 15:10:48', '2026-01-07 16:34:11'),
(15, 3, 13, 'rejected', 'Stok Habis', '2026-01-07 00:00:00', '2026-01-08 00:00:00', NULL, 2, 0, NULL, NULL, '2026-01-07 15:58:10', '2026-01-07 16:34:03'),
(16, 3, 15, 'rejected', 'Buku Rusak', '2026-01-08 00:00:00', '2026-01-09 00:00:00', NULL, 2, 0, NULL, NULL, '2026-01-07 18:15:16', '2026-01-07 18:17:12'),
(17, 3, 16, 'rejected', 'Buku Rusak', '2026-01-08 00:00:00', '2026-01-09 00:00:00', NULL, 2, 0, NULL, NULL, '2026-01-08 03:54:32', '2026-01-08 04:43:45'),
(18, 3, 17, 'rejected', 'stok_habis', '2026-01-08 00:00:00', '2026-01-09 00:00:00', NULL, NULL, 0, NULL, NULL, '2026-01-08 03:55:33', '2026-01-08 04:43:32'),
(19, 3, 1, 'borrowed', NULL, '2026-01-08 00:00:00', '2026-01-09 00:00:00', NULL, 1, 0, NULL, NULL, '2026-01-08 03:55:49', '2026-01-08 03:55:49'),
(20, 3, 1, 'borrowed', NULL, '2026-01-08 00:00:00', '2026-01-09 00:00:00', NULL, 1, 0, NULL, NULL, '2026-01-08 03:56:31', '2026-01-08 03:56:31'),
(21, 3, 1, 'returned', NULL, '2026-01-08 00:00:00', '2026-01-10 00:00:00', '2026-01-12 09:36:08', 1, 3000, '2026-01-12 09:36:08', '2026-01-12 09:35:18', '2026-01-08 03:57:58', '2026-01-12 02:36:08'),
(22, 2, 18, 'pending', NULL, '2025-01-08 00:00:00', '2025-01-15 00:00:00', NULL, NULL, 0, NULL, NULL, '2026-01-08 08:12:09', '2026-01-08 08:12:09');

-- --------------------------------------------------------

--
-- Struktur dari tabel `users`
--

CREATE TABLE `users` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `full_name` varchar(255) NOT NULL,
  `username` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `role` enum('admin','member') NOT NULL DEFAULT 'member',
  `password` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `users`
--

INSERT INTO `users` (`id`, `full_name`, `username`, `email`, `role`, `password`, `created_at`, `updated_at`) VALUES
(1, 'Test User', 'testuser', 'test@example.com', 'member', '$2y$12$SEQ7sfGHLBZTPMWp2g3DGeCAG1DfDu3tUl0IGCO9jQotk5lq/RNfS', '2026-01-02 04:15:24', '2026-01-02 04:15:24'),
(2, 'Librarian Admin', 'librarian', 'admin@gmail.com', 'admin', '$2y$12$/CcZPiehC0t.JrzN8P/N3egmAMGvNVjOH1jJuLLJ/HUhreppRTKUq', '2026-01-02 04:15:24', '2026-01-02 04:15:24'),
(3, 'veve', 'veve', 'veve2012@gmail.com', 'member', '$2y$12$SCofzG1vduKVGWBYYPXx4uTCDwPNocm.PpI/pmwEEnIQIDHiOo.4e', '2026-01-02 04:17:37', '2026-01-02 04:17:37'),
(4, 'Anita Veronika', 'anitaveronika', 'aniita@gmail.com', 'member', '$2y$12$pCN7aLE4Y4KWXHDA.n/Sser8dOvUj0JEbskHiSzFD/J1Ar8URF4/O', '2026-01-04 02:07:56', '2026-01-04 02:07:56');

--
-- Indexes for dumped tables
--

--
-- Indeks untuk tabel `books`
--
ALTER TABLE `books`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `books_api_book_id_unique` (`api_book_id`);

--
-- Indeks untuk tabel `cache`
--
ALTER TABLE `cache`
  ADD PRIMARY KEY (`key`);

--
-- Indeks untuk tabel `cache_locks`
--
ALTER TABLE `cache_locks`
  ADD PRIMARY KEY (`key`);

--
-- Indeks untuk tabel `failed_jobs`
--
ALTER TABLE `failed_jobs`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`);

--
-- Indeks untuk tabel `jobs`
--
ALTER TABLE `jobs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `jobs_queue_index` (`queue`);

--
-- Indeks untuk tabel `job_batches`
--
ALTER TABLE `job_batches`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `password_reset_tokens`
--
ALTER TABLE `password_reset_tokens`
  ADD PRIMARY KEY (`email`);

--
-- Indeks untuk tabel `personal_access_tokens`
--
ALTER TABLE `personal_access_tokens`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `personal_access_tokens_token_unique` (`token`),
  ADD KEY `personal_access_tokens_tokenable_type_tokenable_id_index` (`tokenable_type`,`tokenable_id`),
  ADD KEY `personal_access_tokens_expires_at_index` (`expires_at`);

--
-- Indeks untuk tabel `sessions`
--
ALTER TABLE `sessions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `sessions_user_id_index` (`user_id`),
  ADD KEY `sessions_last_activity_index` (`last_activity`);

--
-- Indeks untuk tabel `transactions`
--
ALTER TABLE `transactions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `transactions_user_id_foreign` (`user_id`),
  ADD KEY `transactions_book_id_foreign` (`book_id`);

--
-- Indeks untuk tabel `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `users_username_unique` (`username`),
  ADD UNIQUE KEY `users_email_unique` (`email`);

--
-- AUTO_INCREMENT untuk tabel yang dibuang
--

--
-- AUTO_INCREMENT untuk tabel `books`
--
ALTER TABLE `books`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- AUTO_INCREMENT untuk tabel `failed_jobs`
--
ALTER TABLE `failed_jobs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `jobs`
--
ALTER TABLE `jobs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT untuk tabel `personal_access_tokens`
--
ALTER TABLE `personal_access_tokens`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=61;

--
-- AUTO_INCREMENT untuk tabel `transactions`
--
ALTER TABLE `transactions`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;

--
-- AUTO_INCREMENT untuk tabel `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- Ketidakleluasaan untuk tabel pelimpahan (Dumped Tables)
--

--
-- Ketidakleluasaan untuk tabel `transactions`
--
ALTER TABLE `transactions`
  ADD CONSTRAINT `transactions_book_id_foreign` FOREIGN KEY (`book_id`) REFERENCES `books` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `transactions_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
akaan (1).sql…]()


[db_perpustakaan (1).sql](https://github.com/user-attachments/files/24556984/db_perpustakaan.1.sql)







```bash
git clone https://github.com/anitavrn/library-app.git
