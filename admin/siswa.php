<?php
include "../config.php";
session_start();

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit;
}

/* ================== AJAX KELAS ================== */
if (isset($_GET['ajax']) && $_GET['ajax'] == 'kelas' && isset($_GET['id_jurusan'])) {
    $id_jurusan = $_GET['id_jurusan'];
    $kelas = mysqli_query($conn, "
        SELECT * FROM kelas 
        WHERE id_jurusan='$id_jurusan' 
        ORDER BY nama_kelas
    ");
    echo '<option value="">-- Kelas --</option>';
    while ($k = mysqli_fetch_assoc($kelas)) {
        echo "<option value='{$k['id_kelas']}'>{$k['nama_kelas']}</option>";
    }
    exit;
}

/* ================== FILTER ================== */
$id_tahun   = $_GET['id_tahun'] ?? '';
$id_jurusan = $_GET['id_jurusan'] ?? '';
$id_kelas   = $_GET['id_kelas'] ?? '';
$id_dudi    = $_GET['id_dudi'] ?? '';

$where = [];
$show_data = false;

if ($id_tahun !== '') { $where[] = "s.id_tahun = '$id_tahun'"; $show_data = true; }
if ($id_jurusan !== '') { $where[] = "j.id_jurusan = '$id_jurusan'"; $show_data = true; }
if ($id_kelas !== '') { $where[] = "k.id_kelas = '$id_kelas'"; $show_data = true; }
if ($id_dudi !== '') { $where[] = "s.id_dudi = '$id_dudi'"; $show_data = true; }

$where_sql = $where ? "WHERE " . implode(" AND ", $where) : "";

$siswa_query = null;
if ($show_data) {
    $siswa_query = mysqli_query($conn, "
        SELECT
            s.nis,
            s.nama_siswa,
            k.nama_kelas,
            j.nama_jurusan,
            t.tahun,
            d.nama_dudi,
            s.no_hp
        FROM siswa s
        JOIN kelas k ON s.id_kelas = k.id_kelas
        JOIN jurusan j ON k.id_jurusan = j.id_jurusan
        JOIN tahun_pkl t ON s.id_tahun = t.id_tahun
        LEFT JOIN dudi d ON s.id_dudi = d.id_dudi
        $where_sql
        ORDER BY k.nama_kelas, s.nama_siswa
    ");
}

$tahun_list   = mysqli_query($conn, "SELECT * FROM tahun_pkl ORDER BY tahun DESC");
$jurusan_list = mysqli_query($conn, "SELECT * FROM jurusan ORDER BY nama_jurusan");
$kelas_list   = mysqli_query($conn, "SELECT * FROM kelas ORDER BY nama_kelas");
$dudi_list    = mysqli_query($conn, "SELECT * FROM dudi ORDER BY nama_dudi");
?>

<!DOCTYPE html>
<html>
<head>
<title>Data Siswa</title>

<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

<style>

*{
    margin:0;
    padding:0;
    box-sizing:border-box;
    font-family:'Poppins', sans-serif;
}

body{
    display:flex;
    background:#f3f4f6;
}

/* ================= SIDEBAR (SAMA) ================= */

.sidebar{
    width:260px;
    background:white;
    min-height:100vh;
    padding:30px 20px;
    border-right:1px solid #e5e7eb;
}

.sidebar h2{
    color:#8b5cf6;
    margin-bottom:40px;
    display:flex;
    align-items:center;
    gap:10px;
}

.sidebar a{
    display:flex;
    align-items:center;
    gap:12px;
    padding:12px 15px;
    text-decoration:none;
    color:#374151;
    margin-bottom:8px;
    border-radius:8px;
    transition:0.2s;
    font-size:14px;
}

.sidebar a i{
    width:18px;
}

.sidebar a:hover{
    background:#f3f4f6;
    color:#8b5cf6;
}

.sidebar a.active{
    background:#ede9fe;
    color:#6d28d9;
    font-weight:600;
    position:relative;
}

.sidebar a.active::before{
    content:"";
    position:absolute;
    left:0;
    top:0;
    bottom:0;
    width:4px;
    background:#8b5cf6;
    border-radius:4px 0 0 4px;
}

/* ================= MAIN ================= */

.main{ flex:1; }

/* ================= TOPBAR ================= */

.topbar{
    height:70px;
    background:white;
    display:flex;
    align-items:center;
    justify-content:flex-end;
    padding:0 30px;
    border-bottom:1px solid #e5e7eb;
}

.profile{
    font-weight:600;
    display:flex;
    align-items:center;
    gap:8px;
}

/* ================= CONTENT ================= */

.content{
    padding:30px;
}

/* ================= FILTER BOX ================= */

.filter-box{
    background:white;
    padding:25px;
    margin-bottom:30px;
    border-radius:14px;
    border:1px solid #e5e7eb;
}

.filter-box h3{
    margin-bottom:20px;
    display:flex;
    align-items:center;
    gap:8px;
}

.filter-grid{
    display:grid;
    grid-template-columns:repeat(auto-fit,minmax(200px,1fr));
    gap:15px;
}

select{
    padding:10px;
    border:1px solid #e5e7eb;
    border-radius:6px;
    background:white;
}

button{
    padding:10px;
    background:#1877F2;
    border:none;
    color:white;
    border-radius:6px;
    cursor:pointer;
}

button:hover{
    background:#0f5dc2;
}

/* ================= TABLE ================= */

.section{
    background:white;
    padding:25px;
    border-radius:14px;
    border:1px solid #e5e7eb;
}

.section h3{
    margin-bottom:20px;
    display:flex;
    align-items:center;
    gap:8px;
}

table{
    width:100%;
    border-collapse:collapse;
}

th, td{
    padding:12px;
    border-bottom:1px solid #e5e7eb;
    text-align:left;
    font-size:14px;
}

thead{
    background:#f9fafb;
}

tr:hover{
    background:#f3f4f6;
}

.alert{
    background:#fef3c7;
    padding:15px;
    border-radius:8px;
    border:1px solid #fde68a;
}

a.detail{
    color:#1877F2;
    text-decoration:none;
    font-weight:600;
}

</style>
</head>
<body>

<div class="sidebar">
    <h2><i class="fa-solid fa-graduation-cap"></i> Portal PKL</h2>

    <a href="index.php">
        <i class="fa-solid fa-house"></i> Dashboard
    </a>

    <a href="siswa.php" class="active">
        <i class="fa-solid fa-user-graduate"></i> Data Siswa
    </a>

    <a href="dudi.php">
        <i class="fa-solid fa-building"></i> Data DUDI
    </a>
</div>

<div class="main">

    <div class="topbar">
        <div class="profile">
            <i class="fa-solid fa-user-shield"></i> Admin
        </div>
    </div>

    <div class="content">

        <!-- FILTER -->
        <form method="get" class="filter-box">
            <h3><i class="fa-solid fa-filter"></i> Filter Data Siswa</h3>

            <div class="filter-grid">
                <select name="id_tahun">
                    <option value="">-- Tahun PKL --</option>
                    <?php while($t=mysqli_fetch_assoc($tahun_list)): ?>
                        <option value="<?= $t['id_tahun']; ?>" <?= $id_tahun==$t['id_tahun']?'selected':''; ?>>
                            <?= $t['tahun']; ?>
                        </option>
                    <?php endwhile; ?>
                </select>

                <select name="id_jurusan" id="jurusan">
                    <option value="">-- Jurusan --</option>
                    <?php while($j=mysqli_fetch_assoc($jurusan_list)): ?>
                        <option value="<?= $j['id_jurusan']; ?>" <?= $id_jurusan==$j['id_jurusan']?'selected':''; ?>>
                            <?= $j['nama_jurusan']; ?>
                        </option>
                    <?php endwhile; ?>
                </select>

                <select name="id_kelas" id="kelas">
                    <option value="">-- Kelas --</option>
                    <?php while($k=mysqli_fetch_assoc($kelas_list)): ?>
                        <option value="<?= $k['id_kelas']; ?>" <?= $id_kelas==$k['id_kelas']?'selected':''; ?>>
                            <?= $k['nama_kelas']; ?>
                        </option>
                    <?php endwhile; ?>
                </select>

                <select name="id_dudi">
                    <option value="">-- DUDI --</option>
                    <?php while($d=mysqli_fetch_assoc($dudi_list)): ?>
                        <option value="<?= $d['id_dudi']; ?>" <?= $id_dudi==$d['id_dudi']?'selected':''; ?>>
                            <?= $d['nama_dudi']; ?>
                        </option>
                    <?php endwhile; ?>
                </select>

                <button type="submit">
                    <i class="fa-solid fa-magnifying-glass"></i> Tampilkan
                </button>
            </div>
        </form>

        <?php if(!$show_data): ?>
            <div class="alert">Silakan pilih filter terlebih dahulu.</div>
        <?php endif; ?>

        <?php if($show_data): ?>
        <div class="section">
            <h3>
                <i class="fa-solid fa-users"></i>
                Total Siswa: <?= mysqli_num_rows($siswa_query); ?>
            </h3>

            <table>
                <thead>
                    <tr>
                        <th>No</th>
                        <th>NIS</th>
                        <th>Nama</th>
                        <th>Kelas</th>
                        <th>Jurusan</th>
                        <th>Tahun</th>
                        <th>DUDI</th>
                        <th>No HP</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $no=1; while($s=mysqli_fetch_assoc($siswa_query)): ?>
                    <tr>
                        <td><?= $no++; ?></td>
                        <td><?= $s['nis']; ?></td>
                        <td><?= $s['nama_siswa']; ?></td>
                        <td><?= $s['nama_kelas']; ?></td>
                        <td><?= $s['nama_jurusan']; ?></td>
                        <td><?= $s['tahun']; ?></td>
                        <td><?= $s['nama_dudi'] ?? '-'; ?></td>
                        <td><?= $s['no_hp']; ?></td>
                        <td>
                            <a href="detail_siswa.php?nis=<?= $s['nis']; ?>" class="detail">
                                <i class="fa-solid fa-eye"></i> Detail
                            </a>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
        <?php endif; ?>

    </div>
</div>

<script>
document.getElementById('jurusan').addEventListener('change', function(){
    let id = this.value;
    let kelas = document.getElementById('kelas');
    kelas.innerHTML = '<option>Loading...</option>';

    fetch('siswa.php?ajax=kelas&id_jurusan=' + id)
        .then(res => res.text())
        .then(data => kelas.innerHTML = data);
});
</script>

</body>
</html>
