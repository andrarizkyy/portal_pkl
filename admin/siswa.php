<?php
include "../config.php";

/* ================== FILTER ================== */
$jurusan = $_GET['jurusan'] ?? '';
$kelas   = $_GET['kelas'] ?? '';

$where = [];
if ($jurusan != '') $where[] = "s.jurusan = '$jurusan'";
if ($kelas != '')   $where[] = "s.kelas = '$kelas'";

$where_sql = $where ? "WHERE ".implode(" AND ", $where) : "";

/* ================== QUERY DATA SISWA ================== */
$siswa_query = null;
if ($jurusan && $kelas) {
    $siswa_query = mysqli_query($conn, "
        SELECT s.*, d.nama_dudi AS nama_dudi_relasi
        FROM siswa s
        LEFT JOIN dudi d ON s.id_dudi = d.id_dudi
        $where_sql
        ORDER BY s.nama_siswa
    ");
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>Monitoring Siswa PKL</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">

<style>
body { background:#f6f7fb; }
.sidebar{
    width:260px; min-height:100vh;
    background:linear-gradient(180deg,#c62828,#b71c1c);
    position:fixed; color:#fff;
}
.sidebar a{
    color:#fff; text-decoration:none;
    padding:14px 18px; display:block;
    margin:5px 12px; border-radius:10px;
}
.sidebar a.active,.sidebar a:hover{
    background:rgba(255,255,255,.2);
}
.content{ margin-left:260px; padding:25px; }
</style>

<script>
const kelasMap = {
    "Rekayasa Perangkat Lunak": ["XII RPL 1","XII RPL 2"],
    "AKL": ["AKL 1","AKL 2","AKL 3"],
    "PM": ["PM 1","PM 2"],
    "TJKT": ["TJKT 1","TJKT 2"]
};

function updateKelas(){
    let jurusan = document.getElementById("jurusan").value;
    let kelas = document.getElementById("kelas");
    kelas.innerHTML = '<option value="">-- Pilih Kelas --</option>';

    if(kelasMap[jurusan]){
        kelasMap[jurusan].forEach(k=>{
            kelas.innerHTML += `<option value="${k}">${k}</option>`;
        });
    }

    <?php if($kelas): ?>
    kelas.value = "<?= $kelas ?>";
    <?php endif; ?>
}
window.onload = updateKelas;
</script>
</head>
<body>

<!-- SIDEBAR -->
<div class="sidebar">
    <h4 class="text-center py-4 border-bottom">PKL Hub</h4>
    <a href="home.php"><i class="bi bi-speedometer2 me-2"></i> Dashboard</a>
    <a href="perusahaan.php"><i class="bi bi-building me-2"></i> Data DUDI</a>
    <a class="active" href="#"><i class="bi bi-people me-2"></i> Monitoring Siswa PKL</a>
</div>

<div class="content">
<h3>Monitoring Siswa PKL</h3>

<!-- FILTER -->
<form class="row g-3 mb-4" method="get">
    <div class="col-md-4">
        <select name="jurusan" id="jurusan" class="form-select" onchange="updateKelas()">
            <option value="">-- Pilih Jurusan --</option>
            <option value="Rekayasa Perangkat Lunak" <?= $jurusan=="Rekayasa Perangkat Lunak"?'selected':''; ?>>RPL</option>
            <option value="AKL" <?= $jurusan=="AKL"?'selected':''; ?>>AKL</option>
            <option value="PM" <?= $jurusan=="PM"?'selected':''; ?>>PM</option>
            <option value="TJKT" <?= $jurusan=="TJKT"?'selected':''; ?>>TJKT</option>
        </select>
    </div>

    <div class="col-md-4">
        <select name="kelas" id="kelas" class="form-select">
            <option value="">-- Pilih Kelas --</option>
        </select>
    </div>

    <div class="col-md-4">
        <button class="btn btn-danger">Tampilkan</button>
        <a href="siswa.php" class="btn btn-secondary">Reset</a>
    </div>
</form>

<!-- DATA -->
<?php if($jurusan && $kelas): ?>
<div class="card shadow-sm border-0">
<div class="card-body table-responsive">

<?php if(mysqli_num_rows($siswa_query) > 0): ?>
<table class="table table-bordered table-striped align-middle">
<thead class="table-danger">
<tr>
    <th>NIS</th>
    <th>Nama</th>
    <th>Kelas</th>
    <th>Jurusan</th>
    <th>Alamat</th>
    <th>JK</th>
    <th>Agama</th>
    <th>TTL</th>
    <th>No HP</th>
    <th>DUDI</th>
</tr>
</thead>
<tbody>
<?php while($s = mysqli_fetch_assoc($siswa_query)): ?>
<tr>
    <td><?= $s['nis']; ?></td>
    <td><?= $s['nama_siswa']; ?></td>
    <td><?= $s['kelas']; ?></td>
    <td><?= $s['jurusan']; ?></td>
    <td><?= $s['alamat']; ?></td>
    <td><?= $s['jenis_kelamin']; ?></td>
    <td><?= $s['agama']; ?></td>
    <td><?= $s['TTL']; ?></td>
    <td><?= $s['no_hp']; ?></td>
    <td><?= $s['nama_dudi_relasi'] ?? $s['nama_dudi'] ?? '-'; ?></td>
</tr>
<?php endwhile; ?>
</tbody>
</table>
<?php else: ?>
<div class="alert alert-warning">Data tidak ditemukan.</div>
<?php endif; ?>

</div>
</div>
<?php else: ?>
<div class="alert alert-info">
Silakan pilih <b>Jurusan</b> dan <b>Kelas</b> terlebih dahulu.
</div>
<?php endif; ?>

</div>
</body>
</html>
