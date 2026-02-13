<?php
include "../config.php";
session_start();

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit;
}

$id_dudi = $_GET['id_dudi'] ?? '';
if(!$id_dudi) die("DUDI tidak ditemukan");

/* ================= DETAIL DUDI ================= */
$dudi = mysqli_fetch_assoc(
    mysqli_query($conn,"SELECT * FROM dudi WHERE id_dudi='$id_dudi'")
);

/* ================= SISWA PKL ================= */
$siswa = mysqli_query($conn,"
    SELECT 
        s.nis,
        s.nama_siswa,
        s.no_hp,
        k.nama_kelas AS kelas,
        j.nama_jurusan AS jurusan
    FROM siswa s
    LEFT JOIN kelas k ON s.id_kelas = k.id_kelas
    LEFT JOIN jurusan j ON k.id_jurusan = j.id_jurusan
    WHERE s.id_dudi = '$id_dudi'
    ORDER BY s.nama_siswa
");

$total_siswa = mysqli_num_rows($siswa);
?>

<!DOCTYPE html>
<html>
<head>
<title>Detail DUDI</title>

<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

<style>
*{margin:0;padding:0;box-sizing:border-box;font-family:'Poppins', sans-serif;}
body{display:flex;background:#f3f4f6;}

/* SIDEBAR */
.sidebar{
    width:260px;background:white;min-height:100vh;
    padding:30px 20px;border-right:1px solid #e5e7eb;
}
.sidebar h2{
    color:#8b5cf6;margin-bottom:40px;
    display:flex;align-items:center;gap:10px;
}
.sidebar a{
    display:flex;align-items:center;gap:12px;
    padding:12px 15px;text-decoration:none;
    color:#374151;margin-bottom:8px;
    border-radius:8px;transition:0.2s;font-size:14px;
}
.sidebar a i{width:18px;}
.sidebar a:hover{background:#f3f4f6;color:#8b5cf6;}
.sidebar a.active{
    background:#ede9fe;color:#6d28d9;font-weight:600;position:relative;
}
.sidebar a.active::before{
    content:"";position:absolute;left:0;top:0;bottom:0;
    width:4px;background:#8b5cf6;border-radius:4px 0 0 4px;
}

/* MAIN */
.main{flex:1;}

/* TOPBAR */
.topbar{
    height:70px;background:white;
    display:flex;align-items:center;justify-content:flex-end;
    padding:0 30px;border-bottom:1px solid #e5e7eb;
}
.profile{
    font-weight:600;display:flex;align-items:center;gap:8px;
}

/* CONTENT */
.content{padding:30px;}

/* CARD */
.card{
    background:white;padding:25px;margin-bottom:30px;
    border-radius:14px;border:1px solid #e5e7eb;
}

.card h3{
    margin-bottom:15px;
}

/* HERO */
.hero h1{
    font-size:24px;margin-bottom:5px;
}

.hero p{
    color:#6b7280;margin-bottom:15px;
}

.stat-box{
    background:#f9fafb;
    padding:15px;
    border-radius:10px;
    border:1px solid #e5e7eb;
    width:220px;
}

.stat-box h2{
    margin-top:8px;
    color:#3b82f6;
}

/* TABLE */
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

.empty{
    padding:15px;
    color:#6b7280;
}

/* BUTTON */
.btn{
    display:inline-block;
    padding:8px 16px;
    background:#1877F2;
    color:white;
    text-decoration:none;
    font-size:14px;
    border-radius:6px;
    margin-bottom:20px;
}
.btn:hover{
    background:#0f5dc2;
}
</style>
</head>

<body>

<div class="sidebar">
    <h2><i class="fa-solid fa-graduation-cap"></i> Portal PKL</h2>

    <a href="home.php">
        <i class="fa-solid fa-house"></i> Dashboard
    </a>

    <a href="siswa.php">
        <i class="fa-solid fa-user-graduate"></i> Data Siswa
    </a>

    <a href="dudi.php" class="active">
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

<a href="dudi.php" class="btn">← Kembali</a>

<!-- HERO -->
<div class="card hero">
    <h1><?= $dudi['nama_dudi']; ?></h1>
    <p><?= $dudi['bidang_usaha']; ?> • <?= $dudi['email']; ?></p>

    <div class="stat-box">
        Total Siswa PKL
        <h2><?= $total_siswa; ?></h2>
    </div>
</div>

<!-- INFO PERUSAHAAN -->
<div class="card">
    <h3>Informasi Perusahaan</h3>
    <p><b>No HP:</b> <?= $dudi['no_hp_dudi']; ?></p>
    <p><b>Website:</b> <?= $dudi['website']; ?></p>
    <p><b>Deskripsi:</b><br><?= $dudi['deskripsi']; ?></p>
</div>

<!-- DAFTAR SISWA -->
<div class="card">
    <h3>Daftar Siswa PKL</h3>

    <?php if($total_siswa > 0){ ?>
    <table>
        <thead>
            <tr>
                <th>NIS</th>
                <th>Nama</th>
                <th>Kelas</th>
                <th>Jurusan</th>
                <th>No HP</th>
            </tr>
        </thead>
        <tbody>
            <?php mysqli_data_seek($siswa,0); while($s=mysqli_fetch_assoc($siswa)){ ?>
            <tr>
                <td><?= $s['nis']; ?></td>
                <td><?= $s['nama_siswa']; ?></td>
                <td><?= $s['kelas']; ?></td>
                <td><?= $s['jurusan']; ?></td>
                <td><?= $s['no_hp']; ?></td>
            </tr>
            <?php } ?>
        </tbody>
    </table>
    <?php } else { ?>
        <div class="empty">Belum ada siswa PKL</div>
    <?php } ?>

</div>

</div>
</div>

</body>
</html>
