<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
?>
<?php
include "../config.php";

// Cek apakah id_dudi dikirim
$id_dudi = $_GET['id_dudi'] ?? '';
if (!$id_dudi) {
    die("DUDI tidak ditemukan!");
}

// Ambil info DUDI
$dudi_query = mysqli_query($conn, "SELECT * FROM dudi WHERE id_dudi='$id_dudi'");
if (!$dudi_query || mysqli_num_rows($dudi_query)==0) {
    die("DUDI tidak ditemukan!");
}
$dudi = mysqli_fetch_assoc($dudi_query);

// Ambil semua siswa yang PKL di DUDI ini
$siswa_query = mysqli_query($conn, "SELECT * FROM siswa WHERE nama_dudi='{$dudi['nama_dudi']}' ORDER BY nama_siswa");
if (!$siswa_query) die("Query Siswa Error: ".mysqli_error($conn));
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>Siswa PKL di <?= $dudi['nama_dudi']; ?></title>
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
</head>
<body>

<div class="sidebar">
    <h4 class="text-center py-4 border-bottom">PKL Hub</h4>
    <a href="home.php"><i class="bi bi-speedometer2 me-2"></i> Dashboard</a>
    <a href="perusahaan.php"><i class="bi bi-building me-2"></i> Data Perusahaan</a>
    <a class="active" href="#"><i class="bi bi-people me-2"></i> Siswa PKL</a>
</div>

<div class="content">
<h3>Siswa PKL di <?= $dudi['nama_dudi']; ?></h3>
<div class="mb-3">
    <a href="home.php" class="btn btn-secondary"><i class="bi bi-arrow-left"></i> Kembali</a>
</div>

<?php if(mysqli_num_rows($siswa_query)>0){ ?>
<table class="table table-striped table-bordered">
<thead>
<tr>
    <th>NIS</th>
    <th>Nama Siswa</th>
    <th>Kelas</th>
    <th>Jurusan</th>
    <th>Alamat</th>
    <th>Jenis Kelamin</th>
    <th>Agama</th>
    <th>TTL</th>
    <th>No HP</th>
</tr>
</thead>
<tbody>
<?php while($s=mysqli_fetch_assoc($siswa_query)){ ?>
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
</tr>
<?php } ?>
</tbody>
</table>
<?php } else { ?>
<p>Tidak ada siswa PKL di DUDI ini.</p>
<?php } ?>

</div>
</body>
</html>
