<?php
include "../config.php";

/* ================== FILTER SISWA ================== */
$jurusan = $_GET['jurusan'] ?? '';
$kelas   = $_GET['kelas'] ?? '';

$where = [];
if ($jurusan) $where[] = "jurusan='$jurusan'";
if ($kelas)   $where[] = "kelas='$kelas'";
$where_sql = $where ? "WHERE ".implode(" AND ", $where) : "";

/* ================== QUERY ================== */
$jml_perusahaan = mysqli_fetch_assoc(mysqli_query($conn,"SELECT COUNT(*) total FROM perusahaan"));
$jml_siswa = mysqli_fetch_assoc(mysqli_query($conn,"SELECT COUNT(*) total FROM siswa"));
$data_siswa = mysqli_query($conn,"SELECT * FROM siswa $where_sql ORDER BY nama");
$data_perusahaan = mysqli_query($conn,"SELECT * FROM perusahaan ORDER BY nama_perusahaan");
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>Dashboard Admin Sekolah</title>

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
.card-stat{ border:0; border-radius:16px; color:#fff; }
</style>

<script>
const kelasMap = {
    "AKL":["AKL 1","AKL 2","AKL 3","AKL 4"],
    "PM":["PM 1","PM 2","PM 3"],
    "MP":["MP 1","MP 2","MP 3"],
    "BP":["BP 1","BP 2"],
    "TJKT":["TJKT 1","TJKT 2","TJKT 3","TJKT 4"],
    "RPL":["RPL 1","RPL 2"]
};
function updateKelas(){
    let j = document.getElementById("jurusan").value;
    let k = document.getElementById("kelas");
    k.innerHTML = '<option value="">-- Pilih Kelas --</option>';
    if(kelasMap[j]){
        kelasMap[j].forEach(v=>{
            k.innerHTML += `<option value="${v}">${v}</option>`;
        });
    }
}
</script>
</head>

<body>

<!-- SIDEBAR -->
<div class="sidebar">
    <h4 class="text-center py-4 border-bottom">PKL Hub</h4>
    <a class="active"><i class="bi bi-speedometer2 me-2"></i> Dashboard</a>
    <a href="#perusahaan"><i class="bi bi-building me-2"></i> Data Perusahaan</a>
    <a href="#siswa"><i class="bi bi-people me-2"></i> Monitoring Siswa PKL</a>
</div>

<div class="content">

<!-- TOP -->
<div class="d-flex justify-content-between align-items-center mb-4">
    <h3>Dashboard Admin Sekolah</h3>
    <div>
        <i class="bi bi-bell fs-4 me-3"></i>
        <i class="bi bi-person-circle fs-4"></i>
    </div>
</div>

<!-- STAT -->
<div class="row g-4 mb-4">
    <div class="col-md-6">
        <div class="card card-stat bg-danger p-3">
            <small>Perusahaan</small>
            <h2><?= $jml_perusahaan['total']; ?></h2>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card card-stat bg-success p-3">
            <small>Siswa PKL</small>
            <h2><?= $jml_siswa['total']; ?></h2>
        </div>
    </div>
</div>

<!-- DATA PERUSAHAAN -->
<div class="card shadow-sm border-0 mb-4" id="perusahaan">
<div class="card-header bg-white">
    <h5>Data Perusahaan</h5>
</div>
<div class="card-body table-responsive">
<table class="table table-striped table-bordered">
<thead class="table-light">
<tr>
    <th>Nama</th>
    <th>Bidang</th>
    <th>Email</th>
    <th>No HP</th>
</tr>
</thead>
<tbody>
<?php while($p=mysqli_fetch_assoc($data_perusahaan)){ ?>
<tr>
    <td><?= $p['nama_perusahaan']; ?></td>
    <td><?= $p['bidang_usaha']; ?></td>
    <td><?= $p['email']; ?></td>
    <td><?= $p['no_hp']; ?></td>
</tr>
<?php } ?>
</tbody>
</table>
</div>
</div>

<!-- MONITORING SISWA -->
<div class="card shadow-sm border-0 mb-4" id="siswa">
<div class="card-header bg-white">
    <h5>Monitoring Siswa PKL</h5>
</div>
<div class="card-body">

<form class="row g-3 mb-3">
<div class="col-md-4">
    <select name="jurusan" id="jurusan" class="form-select" onchange="updateKelas()">
        <option value="">-- Pilih Jurusan --</option>
        <?php foreach(["AKL","PM","MP","BP","TJKT","RPL"] as $j){
            echo "<option ".($jurusan==$j?'selected':'').">$j</option>";
        } ?>
    </select>
</div>
<div class="col-md-4">
    <select name="kelas" id="kelas" class="form-select">
        <option value="">-- Pilih Kelas --</option>
    </select>
</div>
<div class="col-md-4">
    <button class="btn btn-danger">Tampilkan</button>
    <a href="home.php" class="btn btn-secondary">Reset</a>
</div>
</form>

<?php while($s=mysqli_fetch_assoc($data_siswa)){ ?>
<div class="d-flex align-items-center border-bottom py-3">
    <img src="https://ui-avatars.com/api/?name=<?= urlencode($s['nama']); ?>&background=c62828&color=fff"
         class="rounded-circle me-3" width="45">
    <div class="flex-grow-1">
        <strong><?= $s['nama']; ?></strong><br>
        <small><?= $s['jurusan']; ?> • <?= $s['kelas']; ?></small>
    </div>
</div>
<?php } ?>

</div>
</div>

</div>
</body>
</html>
