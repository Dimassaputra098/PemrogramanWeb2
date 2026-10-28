
CREATE DATABASE IF NOT EXISTS db_calon_mhs;
USE db_calon_mhs;

CREATE TABLE IF NOT EXISTS calon_mhs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    kode_pendaftaran VARCHAR(30) NOT NULL,
    nama_pendaftar VARCHAR(100) NOT NULL,
    jenis_kelamin VARCHAR(20) NOT NULL,
    ttl VARCHAR(100) NOT NULL,
    asal_sekolah VARCHAR(100) NOT NULL,
    pekerjaan_ortu VARCHAR(100) NOT NULL,
    matematika DECIMAL(5,2) NOT NULL,
    bahasa_inggris DECIMAL(5,2) NOT NULL,
    umum DECIMAL(5,2) NOT NULL,
    rata_rata DECIMAL(5,2) NOT NULL,
    keterangan VARCHAR(20) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
