<?php
$koneksi = new mysqli("localhost", "root", "", "db_calon_mhs");

if ($koneksi->connect_error) {
    die("Koneksi database gagal.");
}

$pesan = "";

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["simpan"])) {
    $kode = trim($_POST["kode"] ?? "");
    $nama = trim($_POST["nama"] ?? "");
    $jk = $_POST["jk"] ?? "";
    $ttl = trim($_POST["ttl"] ?? "");
    $sekolah = trim($_POST["sekolah"] ?? "");
    $ortu = trim($_POST["ortu"] ?? "");
    $mat = $_POST["mat"] ?? "";
    $inggris = $_POST["inggris"] ?? "";
    $umum = $_POST["umum"] ?? "";

    if (
        $kode == "" || $nama == "" || $jk == "" ||
        $ttl == "" || $sekolah == "" || $ortu == "" ||
        $mat === "" || $inggris === "" || $umum === ""
    ) {
        $pesan = "Semua data wajib diisi!";
    } elseif (
        !in_array($jk, ["Laki-laki", "Perempuan"], true) ||
        !is_numeric($mat) || !is_numeric($inggris) ||
        !is_numeric($umum) ||
        $mat < 0 || $mat > 100 ||
        $inggris < 0 || $inggris > 100 ||
        $umum < 0 || $umum > 100
    ) {
        $pesan = "Data tidak valid! Nilai harus 0-100.";
    } else {
        $mat = (float)$mat;
        $inggris = (float)$inggris;
        $umum = (float)$umum;

        $rata = ($mat + $inggris + $umum) / 3;

        if ($rata >= 70) {
            $ket = "Lulus";
        } elseif ($rata >= 60) {
            $ket = "Cadangan";
        } else {
            $ket = "Tidak Lulus";
        }

        $stmt = $koneksi->prepare(
            "INSERT INTO calon_mhs
            (kode_pendaftaran, nama_pendaftar, jenis_kelamin,
            ttl, asal_sekolah, pekerjaan_ortu, matematika,
            bahasa_inggris, umum, rata_rata, keterangan)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)"
        );

        $stmt->bind_param(
            "ssssssdddds",
            $kode, $nama, $jk, $ttl, $sekolah, $ortu,
            $mat, $inggris, $umum, $rata, $ket
        );

        if ($stmt->execute()) {
            header("Location: index.php?status=berhasil");
            exit;
        } else {
            $pesan = "Data gagal disimpan!";
        }

        $stmt->close();
    }
}

if (isset($_GET["status"]) && $_GET["status"] == "berhasil") {
    $pesan = "Data berhasil disimpan!";
}

$hasil = $koneksi->query(
    "SELECT * FROM calon_mhs ORDER BY id ASC"
);

$data = [];
$total = 0;
$lulus = 0;
$tidak_lulus = 0;

while ($row = $hasil->fetch_assoc()) {
    $data[] = $row;
    $total++;

    if ($row["keterangan"] == "Lulus") {
        $lulus++;
    }

    if ($row["keterangan"] == "Tidak Lulus") {
        $tidak_lulus++;
    }
}

function h($nilai) {
    return htmlspecialchars((string)$nilai, ENT_QUOTES, "UTF-8");
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Pendaftaran Calon Mahasiswa</title>

<style>
* {
    box-sizing: border-box;
}

body {
    margin: 0;
    padding: 30px 20px;
    font-family: Arial, sans-serif;
    background: #f3f6fb;
    color: #1e293b;
    font-size: 14px;
}

.container {
    max-width: 1200px;
    margin: auto;
}

header {
    margin-bottom: 25px;
}

header h1 {
    margin: 0 0 8px;
    color: #1e40af;
    font-size: 28px;
}

header p {
    margin: 0;
    color: #64748b;
}

.stats {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 16px;
    margin-bottom: 24px;
}

.stat {
    padding: 20px;
    background: white;
    border: 1px solid #e2e8f0;
    border-radius: 10px;
    box-shadow: 0 3px 10px #00000008;
}

.stat span {
    display: block;
    margin-bottom: 10px;
    color: #64748b;
}

.stat strong {
    font-size: 27px;
    color: #1e40af;
}

.card {
    background: white;
    padding: 25px;
    margin-bottom: 24px;
    border: 1px solid #e2e8f0;
    border-radius: 12px;
    box-shadow: 0 3px 12px #00000008;
}

.card h2 {
    margin: 0 0 22px;
    color: #1e40af;
    font-size: 20px;
}

.form-grid {
    display: grid;
    grid-template-columns: repeat(2, minmax(0, 1fr));
    gap: 20px;
}

.form-group {
    display: flex;
    flex-direction: column;
    gap: 8px;
}

.form-group label {
    font-weight: 600;
    color: #334155;
}

input[type="text"],
input[type="number"] {
    width: 100%;
    padding: 11px 12px;
    border: 1px solid #cbd5e1;
    border-radius: 7px;
    font-size: 14px;
    outline: none;
}

input:focus {
    border-color: #2563eb;
    box-shadow: 0 0 0 3px #2563eb18;
}

.radio {
    display: flex;
    gap: 18px;
    align-items: center;
    min-height: 40px;
    flex-wrap: wrap;
}

.radio label {
    font-weight: normal;
}

.section-title {
    grid-column: 1 / -1;
    padding-bottom: 10px;
    margin-top: 5px;
    border-bottom: 1px solid #e2e8f0;
    color: #1e40af;
    font-weight: bold;
    font-size: 16px;
}

.actions {
    grid-column: 1 / -1;
    display: flex;
    gap: 10px;
    margin-top: 5px;
}

button {
    padding: 12px 20px;
    border: none;
    border-radius: 7px;
    font-size: 14px;
    font-weight: bold;
    cursor: pointer;
}

.btn-primary {
    background: #2563eb;
    color: white;
}

.btn-primary:hover {
    background: #1d4ed8;
}

.btn-secondary {
    background: #e2e8f0;
    color: #334155;
}

.pesan {
    padding: 13px;
    margin-bottom: 18px;
    background: #dcfce7;
    color: #166534;
    border-radius: 7px;
}

.table-wrapper {
    width: 100%;
    overflow-x: auto;
}

table {
    width: 100%;
    min-width: 1050px;
    border-collapse: collapse;
    font-size: 12px;
}

th, td {
    padding: 12px 10px;
    border-bottom: 1px solid #e2e8f0;
    text-align: center;
}

th {
    background: #1e40af;
    color: white;
}

tbody tr:nth-child(even) {
    background: #f8fafc;
}

tbody tr:hover {
    background: #eff6ff;
}

tfoot td {
    background: #eff6ff;
    color: #1e40af;
    font-weight: bold;
}

@media (max-width: 700px) {
    body {
        padding: 18px 12px;
    }

    header h1 {
        font-size: 23px;
    }

    .card {
        padding: 17px;
    }

    .form-grid {
        grid-template-columns: 1fr;
    }

    .section-title,
    .actions {
        grid-column: 1;
    }

    .stats {
        gap: 8px;
    }

    .stat {
        padding: 12px;
    }

    .stat strong {
        font-size: 21px;
    }
}
</style>
</head>

<body>
<div class="container">

<header>
    <h1>Pendaftaran Calon Mahasiswa</h1>
    <p>Form input dan hasil seleksi calon mahasiswa.</p>
</header>

<div class="stats">
    <div class="stat">
        <span>Jumlah Pendaftar</span>
        <strong><?= $total ?></strong>
    </div>

    <div class="stat">
        <span>Peserta Lulus</span>
        <strong><?= $lulus ?></strong>
    </div>

    <div class="stat">
        <span>Tidak Lulus</span>
        <strong><?= $tidak_lulus ?></strong>
    </div>
</div>

<?php if ($pesan != ""): ?>
    <div class="pesan"><?= h($pesan) ?></div>
<?php endif; ?>

<section class="card">
<h2>Input Pendaftaran</h2>

<form method="POST" action="index.php">
<div class="form-grid">

    <div class="form-group">
        <label for="kode">Kode Pendaftaran</label>
        <input type="text" id="kode" name="kode" required>
    </div>

    <div class="form-group">
        <label for="nama">Nama Pendaftar</label>
        <input type="text" id="nama" name="nama" required>
    </div>

    <div class="form-group">
        <label>Jenis Kelamin</label>
        <div class="radio">
            <label><input type="radio" name="jk" value="Laki-laki" required> Laki-laki</label>
            <label><input type="radio" name="jk" value="Perempuan"> Perempuan</label>
        </div>
    </div>

    <div class="form-group">
        <label for="ttl">Tempat, Tanggal Lahir</label>
        <input type="text" id="ttl" name="ttl" placeholder="Contoh: Tangerang, 10-01-2005" required>
    </div>

    <div class="form-group">
        <label for="sekolah">Asal Sekolah</label>
        <input type="text" id="sekolah" name="sekolah" required>
    </div>

    <div class="form-group">
        <label for="ortu">Pekerjaan Orang Tua</label>
        <input type="text" id="ortu" name="ortu" required>
    </div>

    <div class="section-title">Nilai Tes</div>

    <div class="form-group">
        <label for="mat">Matematika</label>
        <input type="number" id="mat" name="mat" min="0" max="100" step="0.01" required>
    </div>

    <div class="form-group">
        <label for="inggris">Bahasa Inggris</label>
        <input type="number" id="inggris" name="inggris" min="0" max="100" step="0.01" required>
    </div>

    <div class="form-group">
        <label for="umum">Umum</label>
        <input type="number" id="umum" name="umum" min="0" max="100" step="0.01" required>
    </div>

    <div class="actions">
        <button class="btn-primary" type="submit" name="simpan">Simpan Data</button>
        <button class="btn-secondary" type="reset">Batal</button>
    </div>

</div>
</form>
</section>

<section class="card">
<h2>Data Calon Mahasiswa</h2>

<div class="table-wrapper">
<table>
<thead>
<tr>
    <th>No.</th>
    <th>Kode</th>
    <th>Kode Pendaftaran</th>
    <th>Nama Pendaftar</th>
    <th>Jenis Kelamin</th>
    <th>TTL</th>
    <th>Asal Sekolah</th>
    <th>Pekerjaan Ortu</th>
    <th>Matematika</th>
    <th>B. Inggris</th>
    <th>Umum</th>
    <th>Rata-rata</th>
    <th>Keterangan</th>
</tr>
</thead>

<tbody>
<?php if (count($data) > 0): ?>
    <?php foreach ($data as $i => $mhs): ?>
    <tr>
        <td><?= $i + 1 ?></td>
        <td><?= h(substr($mhs["kode_pendaftaran"], 0, 2)) ?></td>
        <td><?= h($mhs["kode_pendaftaran"]) ?></td>
        <td><?= h($mhs["nama_pendaftar"]) ?></td>
        <td><?= h($mhs["jenis_kelamin"]) ?></td>
        <td><?= h($mhs["ttl"]) ?></td>
        <td><?= h($mhs["asal_sekolah"]) ?></td>
        <td><?= h($mhs["pekerjaan_ortu"]) ?></td>
        <td><?= h($mhs["matematika"]) ?></td>
        <td><?= h($mhs["bahasa_inggris"]) ?></td>
        <td><?= h($mhs["umum"]) ?></td>
        <td><?= number_format($mhs["rata_rata"], 2) ?></td>
        <td><?= h($mhs["keterangan"]) ?></td>
    </tr>
    <?php endforeach; ?>
<?php else: ?>
    <tr>
        <td colspan="13">Belum ada data calon mahasiswa.</td>
    </tr>
<?php endif; ?>
</tbody>

<tfoot>
<tr>
    <td colspan="12" style="text-align:right">Jumlah Pendaftar</td>
    <td><?= $total ?></td>
</tr>
<tr>
    <td colspan="12" style="text-align:right">Jumlah Peserta Lulus</td>
    <td><?= $lulus ?></td>
</tr>
<tr>
    <td colspan="12" style="text-align:right">Jumlah Tidak Lulus</td>
    <td><?= $tidak_lulus ?></td>
</tr>
</tfoot>
</table>
</div>
</section>

</div>
</body>
</html>