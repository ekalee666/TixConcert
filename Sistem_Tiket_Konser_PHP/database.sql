CREATE DATABASE IF NOT EXISTS db_tiket_konser;
USE db_tiket_konser;

CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nama VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    role ENUM('admin', 'pembeli') DEFAULT 'pembeli',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE konser (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nama_konser VARCHAR(255) NOT NULL,
    deskripsi TEXT,
    tanggal DATETIME NOT NULL,
    lokasi VARCHAR(255) NOT NULL,
    slot_iklan BOOLEAN DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE tiket_stok (
    id INT AUTO_INCREMENT PRIMARY KEY,
    konser_id INT,
    kategori_tiket VARCHAR(50), -- Contoh: VIP, Festival
    harga DECIMAL(12,2) NOT NULL,
    kuota INT NOT NULL,
    sisa_kuota INT NOT NULL,
    FOREIGN KEY (konser_id) REFERENCES konser(id) ON DELETE CASCADE
);

CREATE TABLE transaksi (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    konser_id INT,
    total_bayar DECIMAL(12,2),
    metode_pembayaran ENUM('bank', 'e-wallet'),
    status_pembayaran ENUM('pending', 'success') DEFAULT 'success',
    tanggal_transaksi TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id),
    FOREIGN KEY (konser_id) REFERENCES konser(id)
);

CREATE TABLE tiket_terjual (
    id INT AUTO_INCREMENT PRIMARY KEY,
    transaksi_id INT,
    kategori_tiket VARCHAR(50),
    serial_number VARCHAR(20) UNIQUE,
    qr_code_path VARCHAR(255),
    status_penukaran ENUM('belum', 'sudah') DEFAULT 'belum',
    FOREIGN KEY (transaksi_id) REFERENCES transaksi(id) ON DELETE CASCADE
);

-- Indexing untuk kecepatan pencarian serial number
CREATE INDEX idx_serial ON tiket_terjual(serial_number);